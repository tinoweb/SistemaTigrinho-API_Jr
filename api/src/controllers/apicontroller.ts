import { Request, Response } from "express"
import axios from "axios"
import https from "https"
import logger from "../logger"
import "dotenv/config"
import apifunctions from "../functions/apifunctions"
import { v4 } from "uuid"
import { parseTwoDigitYear } from "moment-timezone"
import "dotenv/config"

export default {
   async launchgame(req: Request, res: Response) {
      const agentToken = req.body.agentToken
      const secretKey = req.body.secretKey
      const user_code = req.body.user_code
      const game_type = req.body.game_type
      const provider_code = req.body.provider_code
      const game_code = req.body.game_code
      const user_balance: number = req.body.user_balance
      const is_influencer: boolean = req.body.is_influencer
      const rtpagent: number = req.body.rtpagent
      const rtpinfluencer: number = req.body.rtpinfluencer


      try {
         if (!user_code) {
            res.send({
               status: "error",
               message: "Voce precisa passar o user_code.",
            })
            return false
         }
         if (isNaN(user_balance) === true) {
            res.send({
               status: "error",
               message: "User Balance deve ser um numero.",
            })
            return false
         }

         if ((await apifunctions.getagentbyagentToken(agentToken)).length === 0) {
            res.send({
               status: "error",
               message: "Agent Token não cadastrado.",
            })
            return false
         }
         if ((await apifunctions.getagentbysecretkey(secretKey)).length === 0) {
            res.send({
               status: "error",
               message: "Secret Key não cadastrado.",
            })
            return false
         }

         const agent = await apifunctions.getagentbyagentToken(agentToken)
         const user = await apifunctions.getuserbyagent(user_code, agent[0].id) //PUXA O USUARIO ATRAVES DO USER E AGENTID

         if (!isNaN(rtpagent)) {
            await apifunctions.updateAgentProbabilities(agent[0].id, rtpagent, rtpinfluencer);
         }

         if (provider_code === "BLAZE_DOUBLE" || game_code === "blaze-double") {
            let currentUser = user

            if (currentUser.length === 0) {
               const tokenuser = v4()
               const atkuser = v4()
               const isInfluencerValue = is_influencer !== undefined ? is_influencer : false

               const createnewuser = await apifunctions.createuser(
                  user_code,
                  tokenuser,
                  atkuser,
                  user_balance,
                  agent[0].id,
                  isInfluencerValue
               )

               if (createnewuser.affectedRows >= 1) {
                  currentUser = await apifunctions.getuserbyagent(
                     user_code,
                     agent[0].id
                  )
               } else {
                  res.send({
                     status: 0,
                     msg: "ERRO",
                     message: "Erro ao criar o usuário para o jogo Blaze Double.",
                  })
                  return
               }
            } else {
               if (is_influencer !== undefined) {
                  await apifunctions.setbalanceuserbyid(
                     currentUser[0].id,
                     user_balance,
                     is_influencer
                  )
               } else {
                  await apifunctions.setbalanceuserbyid(
                     currentUser[0].id,
                     user_balance
                  )
               }
            }

            const baseUrl = `http://${process.env.DOMINIO_API}:3020`
            const launchUrl = `${baseUrl}/?t=${currentUser[0].token}`

            res.send({
               status: 1,
               msg: "SUCCESS",
               launch_url: launchUrl,
               user_code: currentUser[0].username,
               user_balance: user_balance,
               user_created: user.length === 0,
               currency: "BRL",
            })
            return
         }

         if (provider_code === "PP" || provider_code === "PRAGMATIC") {
            try {
               let user = await apifunctions.getuserbyagent(user_code, agent[0].id)

               if (user.length === 0) {
                  const tokenuser = v4()
                  const atkuser = v4()
                  const isInfluencerValue = is_influencer !== undefined ? is_influencer : false;

                  const createnewuser = await apifunctions.createuser(
                     user_code,
                     tokenuser,
                     atkuser,
                     user_balance,
                     agent[0].id,
                     isInfluencerValue
                  );
                  if (createnewuser.affectedRows >= 1) {
                     // Obtendo o novo usuário após criação
                     user = await apifunctions.getuserbyagent(user_code, agent[0].id)
                  } else {
                     res.send({
                        status: 0,
                        msg: "ERRO",
                        message: "Erro ao criar o usuário.",
                     })
                     return
                  }
               }

               const pragmaticRequestData = {
                  method: "game_launch",
                  agent_code: agent[0].agentCode,
                  agent_token: agent[0].agentToken,
                  user_code: user_code,
                  game_code: game_code,
                  lang: "pt",
                  provider_code: provider_code,
                  user_balance: user_balance,
                  callback_url: req.body.callback_url,
               }

               // Chamando internamente o endpoint PHP via HTTPS (porta 443) para evitar redirect 301 e perder o POST
               // Host header garante que o Nginx roteie para o apipp.stellgames.com
               const httpsAgent = new https.Agent({
                  rejectUnauthorized: false
               });

               const ppResponse = await axios.post("https://127.0.0.1/api", pragmaticRequestData, {
                  headers: {
                     'Host': 'apipp.stellgames.com',
                     'Content-Type': 'application/json'
                  },
                  httpsAgent: httpsAgent
               })

               logger.info("PP Internal Response Status:", ppResponse.status);
               logger.info("PP Internal Response Data:", JSON.stringify(ppResponse.data));

               if (ppResponse.data.status === 1 || ppResponse.data.launch_url) {
                  // Corrigir URL de lançamento para usar o domínio correto da API antiga (apipp)
                  let finalLaunchUrl = ppResponse.data.launch_url;
                  if (finalLaunchUrl && finalLaunchUrl.includes("api.stellgames.com/pp/")) {
                     finalLaunchUrl = finalLaunchUrl.replace("api.stellgames.com/pp/", "apipp.stellgames.com/");
                  }

                  res.send({
                     status: 1,
                     msg: "SUCCESS",
                     launch_url: finalLaunchUrl,
                     user_code: user[0].username,
                     user_balance: user[0].saldo,
                     user_created: user.length === 0,
                     currency: "BRL",
                  })
               } else {
                  logger.error("PP Endpoint Error Data:", ppResponse.data)
                  res.send({
                     status: 0,
                     msg: "ERRO",
                     message: ppResponse.data.msg || "Erro na resposta interna do provedor (apipp)."
                  })
               }
            } catch (err) {
               const error = err as any;
               logger.error("Error launching PP game:", error.message)
               if (error.response) {
                   logger.error("Error Response Data:", error.response.data);
                   logger.error("Error Response Status:", error.response.status);
               }
               res.send({
                  status: 0,
                  msg: "ERRO",
                  message: "Erro ao lançar o jogo da Pragmatic Play: " + error.message
               })
            }
            return
         }

         let codegame = 0;

         if (game_code === "fortune-tiger") {
            codegame = 126;
         } else if (game_code === "fortune-ox") {
            codegame = 98;
         } else if (game_code === "fortune-dragon") {
            codegame = 1695365;
         } else if (game_code === "fortune-rabbit") {
            codegame = 1543462;
         } else if (game_code === "fortune-mouse") {
            codegame = 68;
         } else if (game_code === "bikini-paradise") {
            codegame = 69;
         } else if (game_code === "jungle-delight") {
            codegame = 40;
         } else if (game_code === "ganesha-gold") {
            codegame = 42;
         } else if (game_code === "double-fortune") {
            codegame = 48;
         } else if (game_code === "dragon-tiger-luck") {
            codegame = 63;
         } else if (game_code === "ninja-raccoon") {
            codegame = 1529867;
         } else if (game_code === "lucky-clover") {
            codegame = 1601012;
         } else if (game_code === "ultimate-striker") {
            codegame = 1489936;
         } else if (game_code === "prosper-ftree") {
            codegame = 1312883;
         } else if (game_code === "chicky-run") {
            codegame = 1738001;
         } else if (game_code === "butterfly-blossom") {
            codegame = 125;
         } else if (game_code === "cash-mania") {
            codegame = 1682240;
         } else if (game_code === "treasures-aztec") {
            codegame = 87;
         } else if (game_code === "gdn-ice-fire") {
            codegame = 91;
         } else if (game_code === "piggy-gold") {
            codegame = 39;
         } else if (game_code === "wild-bandito") {
            codegame = 104;
         } else if (game_code === "zombie-outbreak") {
            codegame = 1635221;
         } else if (game_code === "majestic-ts") {
            codegame = 95;
         } else if (game_code === "thai-river") {
            codegame = 92;
         } else if (game_code === "rise-apollo") {
            codegame = 101;
         } else if (game_code === "wild-bounty-sd") {
            codegame = 135;
         } else if (game_code === "three-cz-pigs") {
            codegame = 1727711;
         } else if (game_code === "fortune-snake") {
            codegame = 1879752
         } else {
            res.send({
               status: "error",
               message: "Esse game não existe.",
            });
            return false;
         }

         const useApiDomainAsOr = [91, 92, 101, 1489936, 1529867, 1601012];
                if (user.length === 0) {
            const tokenuser = v4()
            const atkuser = v4()
            const createnewuser = await apifunctions.createuser(user_code, tokenuser, atkuser, user_balance, agent[0].id)

            if (createnewuser.affectedRows >= 1) {
               const getnewuser = await apifunctions.getuserbyagent(user_code, agent[0].id)

               res.send({
                  status: 1,
                  msg: "SUCCESS",
                  launch_url: `https://${process.env.DOMINIO_API}/${codegame}/index.html?operator_token=Zm9saWFiZXQ=&btt=1&t=${getnewuser[0].token}&or=${useApiDomainAsOr.includes(codegame) ? 'static.pgf-nmu507th.com' : 'static.pga-nmga5.com'}&api=${process.env.DOMINIO_API}`,
                  user_code: getnewuser[0].username,
                  user_balance: getnewuser[0].saldo,
                  user_created: true,
                  currency: "BRL",
               })
            } else {
               res.send({
                  status: "error",
                  message: "Erro ao cadastrar o usuario.",
               })
               return false
            }
         } else {
            if (is_influencer !== undefined) {
               await apifunctions.setbalanceuserbyid(user[0].id, user_balance, is_influencer);
            } else {
               await apifunctions.setbalanceuserbyid(user[0].id, user_balance);
            }
            res.send({
               status: 1,
               msg: "SUCCESS",
               launch_url: `https://${process.env.DOMINIO_API}/${codegame}/index.html?operator_token=Zm9saWFiZXQ=&btt=1&t=${user[0].token}&or=${useApiDomainAsOr.includes(codegame) ? 'static.pgf-nmu507th.com' : 'static.pga-nmga5.com'}&api=${process.env.DOMINIO_API}`,
               user_code: user[0].username,
               user_balance: user_balance,
               user_created: false,
               currency: "BRL",
            })
         }
      } catch (error) {
         logger.error(error)
      }
   },
   async createAgent(req: Request, res: Response) {
      try {
         const {
            agentCode,
            saldo = 1000,
            agentToken,
            secretKey,
            probganho = "0",
            probbonus = "0",
            probganhortp = "0",
            probganhoinfluencer = "0",
            probbonusinfluencer = "0",
            probganhoaposta = "0",
            probganhosaldo = "0",
            callbackurl = process.env.DOMINIO_AGREGADOR
         } = req.body;

         if (!agentCode || !agentToken || !secretKey) {
            return res.status(400).json({ message: "Campos obrigatórios estão faltando." });
         }

         const result = await apifunctions.createAgent(agentCode, saldo, agentToken, secretKey, probganho, probbonus, probganhortp, probganhoinfluencer, probbonusinfluencer, probganhoaposta, probganhosaldo, callbackurl);

         return res.status(201).json({ message: "Agente criado com sucesso.", agentId: result.insertId });
      } catch (error) {
         console.error("Erro ao criar agente:", error);
         return res.status(500).json({ message: "Erro interno do servidor." });
      }
   },
   async callbackgame(json: any) {
      const agent = await apifunctions.getagentbysecretkey(json.agent_secret)

      try {
         await axios({
            maxBodyLength: Infinity,
            method: "POST",
            url: `${agent[0].callbackurl}gold_api/game_callback`,
            headers: {
               "Content-Type": "application/json",
            },
            data: json,
         })
            .then((data) => {
               //console.log("NEW BALANCE" + data.data.user_balance)
            })
            .catch((error: any) => {
               // console.log(error)
            })
      } catch (error) {
         // console.log(error)
      }
   },
   async getagent(req: Request, res: Response) {
      const agentToken = req.body.agentToken
      const secretKey = req.body.secretKey

      if ((await apifunctions.getagentbyagentToken(agentToken)).length === 0) {
         res.send({
            status: "error",
            message: "Agent Token não cadastrado.",
         })
         return false
      }
      if ((await apifunctions.getagentbysecretkey(secretKey)).length === 0) {
         res.send({
            status: "error",
            message: "Secret Key não cadastrado.",
         })
         return false
      }
      const agent = await apifunctions.getagentbyagentToken(agentToken)
      agent[0].saldo = undefined
      agent[0].agentToken = undefined
      agent[0].saldo = undefined

      res.send(agent[0])
   },
   async attagent(req: Request, res: Response) {
      const agentToken = req.body.agentToken
      const secretKey = req.body.secretKey
      const probganho = req.body.probganho
      const probbonus = req.body.probbonus
      const probganhortp = req.body.probganhortp
      const probganhoinfluencer = req.body.probganhoinfluencer
      const probbonusinfluencer = req.body.probbonusinfluencer
      const probganhoaposta = req.body.probganhoaposta
      const probganhosaldo = req.body.probganhosaldo

      if ((await apifunctions.getagentbyagentToken(agentToken)).length === 0) {
         res.send({
            status: "error",
            message: "Agent Token não cadastrado.",
         })
         return false
      }
      if ((await apifunctions.getagentbysecretkey(secretKey)).length === 0) {
         res.send({
            status: "error",
            message: "Secret Key não cadastrado.",
         })
         return false
      }
      const agent = await apifunctions.getagentbyagentToken(agentToken)

      const att = await apifunctions.attagent(agent[0].id, probganho, probbonus, probganhortp, probganhoinfluencer, probbonusinfluencer, probganhoaposta, probganhosaldo)

      if (att.affectedRows > 0) {
         res.send({
            status: "success",
            message: "Probabiliades alteradas com sucesso.",
         })
      } else {
         res.send({
            status: "error",
            message: "Erro desconhecido por favor contate o adm.",
         })
      }
   },
}
