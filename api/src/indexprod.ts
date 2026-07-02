import express, { Request, Response } from "express"
import helmet from "helmet"
import cors from "cors"
import fs from "fs"
import https from "https"
import http from "http"
import logger from "./logger/index"
import routes from "./routes"
import * as figlet from "figlet"
import path from "path"
import compression from "compression"
import { Server, Socket } from "socket.io"
import allfunctions from "./functions/allfunctions"
import { emitirEventoInterno, adicionarListener } from "./serverEvents"
import { createProxyMiddleware } from "http-proxy-middleware"

import "dotenv/config"

// Handlers to avoid silent process exits and surface errors
process.on("unhandledRejection", (reason: any) => {
   logger.error(`UNHANDLED_REJECTION: ${String(reason)}`)
})
process.on("uncaughtException", (err: any) => {
   logger.error(`UNCAUGHT_EXCEPTION: ${err?.stack || err?.message}`)
})

// const privateKey = fs.readFileSync("server.key", "utf8")
// const certificate = fs.readFileSync("server.crt", "utf8")
// const credentials = {
//   key: privateKey,
//   cert: certificate,
// }
const app = express()
const httpServer = http.createServer(app)

// Enable CORS for all routes (must be before proxies)
app.use(cors({
    origin: '*',
    methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With', 'Accept', 'Origin'],
    credentials: true
}));

const io = new Server(httpServer, {
   transports: ['websocket'],
   cors: {
      origin: "*", // Permitir qualquer origem, ajuste conforme necessário
      methods: ["GET", "POST"]
   }
});

console.log(figlet.textSync("API PHILLYPS"), "\n")
logger.info('DOMINIO CONECTADO: ' + process.env.DOMINIO_API)

// httpServer.listen(process.env.PORT, () => {
//   logger.info("SERVIDOR INICIADO JOHN " + process.env.PORT)

// })
declare module "express-serve-static-core" {
   interface Request {
      io: Server
   }
}
const users = new Map<string, any>()

io.on("connection", async (socket: Socket) => {
   console.log("Usuário Conectado", socket.id);

   socket.on("join", async (socket1) => {
      const token: any = socket1.token
      const gameid: any = socket1.gameId

      setInterval(async function () {
         const user = await allfunctions.getuserbytoken(token)

         if (!user[0]) {
            socket.disconnect(true)
            return false
         }

         const retornado = user[0].valorganho
         const valorapostado = user[0].valorapostado

         const rtp = Math.round((retornado / valorapostado) * 100)

         if (isNaN(rtp) === false) {
            await allfunctions.updatertp(token, rtp)
         }
      }, 10000)
   })

   adicionarListener("attganho", async (dados) => {
      const current = users.get(socket.id) || { aw: 0 }
      const newvalue = parseFloat(current.aw) + dados.aw
      users.set(socket.id, { ...current, aw: newvalue })
      emitirEventoInterno("awreceive", {
         aw: users.get(socket.id).aw,
      })
   })

   adicionarListener("att", (dados) => {
      users.forEach((valor, chave) => {
         if (valor.token === dados.token) {
            return false
         } else {
            users.set(socket.id, {
               token: dados.token,
               username: dados.username,
               bet: dados.bet,
               saldo: dados.saldo,
               rtp: dados.rtp,
               agentid: dados.agentid,
               socketid: socket.id,
               gamecode: dados.gamecode,
               aw: 0,
            })
         }
      })

      if (Object.keys(users).length === 0) {
         users.set(socket.id, {
            token: dados.token,
            username: dados.username,
            bet: dados.bet,
            saldo: dados.saldo,
            rtp: dados.rtp,
            agentid: dados.agentid,
            socketid: socket.id,
            gamecode: dados.gamecode,
            aw: 0,
         })
      }
   })

   socket.on("disconnect", (reason) => {
      users.delete(socket.id)

      console.log("Cliente desconectado:", reason)
   })
})

// Middleware para adicionar compressão
app.use(compression());

// Proxy Configuration for PP Integration
const proxyOptions = {
   target: 'https://127.0.0.1:443',
   secure: false,
   changeOrigin: true,
   headers: {
      Host: 'apipp.stellgames.com'
   }
};

app.use('/pp', createProxyMiddleware({
   ...proxyOptions,
   pathRewrite: { '^/pp': '' },
   onProxyRes: (proxyRes, req, res) => {
       proxyRes.headers['Access-Control-Allow-Origin'] = '*';
   }
}));

app.use('/gs2c', createProxyMiddleware(proxyOptions));
app.use('/EXgames', createProxyMiddleware(proxyOptions));
app.use('/public', createProxyMiddleware(proxyOptions));
app.use('/game', createProxyMiddleware(proxyOptions));

// Middleware para adicionar o socket.io em cada requisição
app.use((req: Request, res: Response, next) => {
   req.io = io // Adiciona o socket.io ao objeto req
   next()
})

app.use(cors())
app.use(express.json())
app.use(express.urlencoded({ extended: true }))
app.use("/", express.static(path.join(__dirname, "public")))

// Disable strict CSP to allow game scripts and connections
app.use(
   helmet({
      contentSecurityPolicy: false,
      crossOriginEmbedderPolicy: false,
   })
)
/*
app.use(
   helmet.contentSecurityPolicy({
      directives: {
         "default-src": ["'none'"],
         "base-uri": ["'self'"],
         "font-src": ["'self'", "https:", "data:"],
         "frame-ancestors": ["'self'"],
         "img-src": ["'self'", "data:"],
         "object-src": ["'none'"],
         "script-src": ["'self'", "https://cdnjs.cloudflare.com"],
         "style-src": ["'self'", "https://cdnjs.cloudflare.com"],
      },
   }),
)
*/

app.use("/status", (req, res) => {
   res.json({ status: "operational" })
})

app.use(routes)
const PORT = Number(process.env.PORT) || 3016
httpServer.on("error", (err) => {
   logger.error(`HTTP SERVER ERROR: ${err.message}`)
})
httpServer.listen(PORT, () => {
   logger.info("API RODANDO NA PORTA: " + PORT)
})
