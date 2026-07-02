import { Request, Response } from "express"
import axios from "axios"
import logger from "../../logger"
import * as crypto from "crypto"
import { v4 } from "uuid"
import { Server, Socket } from "socket.io"
import moment from "moment"
import fortunefunctions from "../../functions/fortune-snake/fortunesnakefunctions"
import allfunctions from "../../functions/allfunctions"
import apicontroller from "../apicontroller"
import { emitirEventoInterno, adicionarListener } from "../../serverEvents"
import linhaganhosnake from "../../jsons/fortune-snake/linhaganhosnake"
import linhaperdasnake from "../../jsons/fortune-snake/linhaperdasnake"
import linhabonustiger from "../../jsons/fortune-snake/linhabonustiger"
import notcashsnake from "../../jsons/fortune-snake/notcashsnake"

import "dotenv/config"

export default {
   async getsnake(req: Request, res: Response) {
      try {
         const token = req.body.atk;
         const gamename = "fortune-snake";
         const user = await allfunctions.getuserbyatk(token);
         logger.info('[+] Usuario logado: ' + user[0].username)
         const jsonprimay = await allfunctions.getSpinByPlayerId(user[0].id);
         const jsoninicial = await allfunctions.getjsonprimary(gamename);
         if (jsonprimay.length === 0) {
            await allfunctions.createOrUpdateSpin(user[0].id, gamename, jsoninicial[0].json);
         }
         if (jsonprimay.length > 0 && jsonprimay.length > 0 && jsonprimay[0].game_code === gamename) {
            logger.info('[+] Json Recuperado Do Ultimo Spin.')
         } else {
            await allfunctions.createOrUpdateSpin(user[0].id, gamename, jsoninicial[0].json);
         }
         const json = await allfunctions.getSpinByPlayerId(user[0].id);
         const jsonformatado = JSON.parse(json[0].json);
         res.send({
            dt: {
               "fb": null,

               "wt": {
                  "mw": 5,
                  "bw": 20,
                  "mgw": 35,
                  "smgw": 50
               },
               "gcs": {
                  "ab": {
                     "is": true,
                     "bm": 0,
                     "bms": [
                        1.5
                     ],
                     "t": 0
                  }
               },
               "abm": [
                  1.5
               ],
               maxwm: 5000,
               cs: [0.05, 0.5, 4.0],
               ml: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
               mxl: 10,
               bl: user[0].saldo,
               inwe: false,
               iuwe: false,
               ls: jsonformatado.dt,
               cc: "BRL",
            },
            err: null,
         })
      } catch (error) {
         logger.error(error)
      }
   },
   async spin(req: Request, res: Response) {
      let cs: number = req.body.cs
      let ml: number = req.body.ml
      let ab: number = req.body.ab
      const token = req.body.atk

      if (cs <= 0 || ml <= 0) {
         res.send({
            dt: null,
            err: {
               cd: "1302",
               msg: "OERR: Valores de aposta inválidos",
               tid: "YNGTHB25"
            }
         })
         return false
      }

      async function lwchange(
         json1: { [key: string]: any },
         json2: { [key: string]: any },
         cs: number,
         ml: number,
      ) {
         for (let chave in json1) {
            if (json1.hasOwnProperty(chave)) {
               const valor = json1[chave]
               const ganho = cs * ml * parseFloat(valor)
               for (let chave2 in json2) {
                  if (json2.hasOwnProperty(chave2)) {
                     json2[chave] = ganho
                  }
               }
            }
         }
      }

      async function countrwsp(json: { [key: string]: any }) {
         let multplicador: number = 0
         for (let i = 1; i <= 9; i++) {
            const chave = i.toString()
            if (json.hasOwnProperty(chave)) {
               multplicador = multplicador + parseFloat(json[chave])
            }
         }
         return multplicador
      }
      async function gerarNumeroUnico() {
         return crypto.randomBytes(8).toString("hex")
      }
      async function gerarSid(): Promise<string> {
         return Math.floor(1000000000000000000 + Math.random() * 9000000000000000000).toString();
      }

      async function gerarPsid(): Promise<string> {
         return Math.floor(1000000000000000000 + Math.random() * 9000000000000000000).toString();
      }

      async function gerarHashr(): Promise<string> {
         const linhas = [];
         for (let i = 0; i < 4; i++) {
            const linha = [];
            for (let j = 0; j < 5; j++) {
               linha.push(Math.floor(Math.random() * 13));
            }
            linhas.push(linha.join(';'));
         }

         const mv = "15.0";
         const mt = Math.floor(Math.random() * 3) + 1;
         const mg = (Math.random() * 10).toFixed(1);

         return `0:${linhas.join('#')}#MV#${mv}#MT#${mt}#MG#${mg}#`;
      }
      try {
         const user = await fortunefunctions.getuserbyatk(token)
         let bet: number = ab && Number(ab) === 1 ? cs * ml * 15 : cs * ml * 10;
         console.log(bet)
         let saldoatual: number = user[0].saldo
         const gamename = "fortune-snake"

         emitirEventoInterno("att", {
            token: token,
            username: user[0].username,
            bet: bet,
            saldo: saldoatual,
            rtp: user[0].rtp,
            agentid: user[0].agentid,
            gamecode: gamename,
         })

         const agent = await allfunctions.getagentbyid(user[0].agentid)

         const checkuserbalance = await axios({
            maxBodyLength: Infinity,
            method: "POST",
            url: `${agent[0].callbackurl}gold_api/user_balance`,
            headers: {
               "Content-Type": "application/json",
            },
            data: {
               user_code: user[0].username,
            },
         })

         if (checkuserbalance.data.msg === "INVALID_USER") {
            res.send(await notcashsnake.notcash(saldoatual, cs, ml))
            return false
         } else if (checkuserbalance.data.msg === "INSUFFICIENT_USER_FUNDS") {
            res.send(await notcashsnake.notcash(saldoatual, cs, ml))
            return false
         }

         const retornado = user[0].valorganho
         const valorapostado = user[0].valorapostado

         const rtp = (retornado / valorapostado) * 100

         console.log("RTP ATUAL " + rtp)

         console.log("BET ATUAL " + bet)

         if (saldoatual < bet) {
            const semsaldo = await notcashsnake.notcash(saldoatual, cs, ml)
            res.send(semsaldo)
            return false
         }

         const resultadospin = await allfunctions.calcularganho(bet, saldoatual, token, gamename)

         if (resultadospin.result === "perda") {
            let newbalance = saldoatual - bet
            await fortunefunctions.attsaldobyatk(token, newbalance)
            await fortunefunctions.atualizardebitado(token, bet)
            await fortunefunctions.atualizarapostado(token, bet)
            const perdajson = await linhaperdasnake.linhaperda()

            let json: any = {
               "dt": {
                  "si": {
                     "wp": null,
                     "lw": null,
                     "irfs": false,
                     "itr": false,
                     "itff": false,
                     "it": perdajson.it,
                     "ifsw": false,
                     "gm": 0,
                     "crtw": 0.0,
                     "imw": false,
                     "orl": perdajson.orl,
                     "rcs": 0,
                     "gwt": -1,
                     "ctw": 0.0,
                     "pmt": null,
                     "cwc": 0,
                     "fstc": null,
                     "pcwc": 0,
                     "rwsp": null,
                     "hashr": await gerarHashr(),
                     "fb": null,
                     "ab": null,
                     "ml": ml,
                     "cs": cs,
                     "rl": perdajson.rl,
                     "sid": await gerarSid(),
                     "psid": await gerarPsid(),
                     "st": 1,
                     "nst": 1,
                     "pf": 1,
                     "aw": 0.00,
                     "wid": 0,
                     "wt": "C",
                     "wk": "0_C",
                     "wbn": null,
                     "wfg": null,
                     "blb": saldoatual,
                     "blab": newbalance,
                     "bl": newbalance,
                     "tb": bet,
                     "tbb": bet,
                     "tw": 0.0,
                     "np": -bet,
                     "ocr": null,
                     "mr": null,
                     "ge": [
                        1,
                        11
                     ]
                  }
               },
               "err": null
            }

            await allfunctions.savejsonspin(user[0].id, JSON.stringify(json), gamename);
            const txnid = v4()
            const dataFormatada: string = moment().toISOString()
            await apicontroller.callbackgame({
               agent_code: agent[0].agentCode,
               agent_secret: agent[0].secretKey,
               user_code: user[0].username,
               user_balance: user[0].saldo,
               user_total_credit: user[0].valorganho,
               user_total_debit: user[0].valorapostado,
               game_type: "slot",
               slot: {
                  provider_code: "PGSOFT",
                  game_code: gamename,
                  round_id: await gerarNumeroUnico(),
                  type: "BASE",
                  bet: bet,
                  win: 0,
                  txn_id: `${txnid}`,
                  txn_type: "debit_credit",
                  is_buy: false,
                  is_call: false,
                  user_before_balance: user[0].saldo,
                  user_after_balance: newbalance,
                  agent_before_balance: 100,
                  agent_after_balance: 100,
                  created_at: dataFormatada,
               },
            })
            res.send(json)
         }
         if (resultadospin.result === "ganho") {
            const ganhojson = await linhaganhosnake.linhaganho(bet)
            const multplicador = await countrwsp(ganhojson.rwsp)
            await lwchange(ganhojson.rwsp, ganhojson.lw, cs, ml)
            let valorganho = cs * ml * multplicador
            let ifsw = false

            // Verifica se existem 4 zeros consecutivos no orl
            const temQuatroZerosConsecutivos = ganhojson.orl.some((valor: number, index: number, array: number[]) => {
               return valor === 0 &&
                  array[index + 1] === 0 &&
                  array[index + 2] === 0 &&
                  array[index + 3] === 0;
            });

            // Se tiver 4 zeros consecutivos, multiplica o ganho por 10
            if (temQuatroZerosConsecutivos) {
               valorganho = valorganho * 10;
               ifsw = true
            }

            const newbalance = saldoatual + valorganho - bet
            await fortunefunctions.attsaldobyatk(token, newbalance)
            await fortunefunctions.atualizardebitado(token, bet)
            await fortunefunctions.atualizarapostado(token, bet)
            await fortunefunctions.atualizarganho(token, valorganho)

            let json: any = {
               dt: {
                  "si": {
                     "wp": ganhojson.wp,
                     "lw": ganhojson.lw,
                     "irfs": false,
                     "itr": false,
                     "itff": false,
                     "it": ganhojson.it,
                     "ifsw": ifsw,
                     "gm": 0,
                     "crtw": 0.0,
                     "imw": false,
                     "orl": ganhojson.orl,
                     "rcs": 0,
                     "gwt": bet,
                     "ctw": valorganho,
                     "pmt": null,
                     "cwc": bet,
                     "fstc": null,
                     "pcwc": bet,
                     "rwsp": ganhojson.rwsp,
                     "hashr": await gerarHashr(),
                     "fb": null,
                     "ab": null,
                     "ml": ml,
                     "cs": cs,
                     "rl": ganhojson.orl,
                     "sid": await gerarSid(),
                     "psid": await gerarPsid(),
                     "st": 1,
                     "nst": 1,
                     "pf": 1,
                     "aw": valorganho,
                     "wid": 0,
                     "wt": "C",
                     "wk": "0_C",
                     "wbn": null,
                     "wfg": null,
                     "blb": saldoatual,
                     "blab": newbalance,
                     "bl": newbalance,
                     "tb": bet,
                     "tbb": bet,
                     "tw": valorganho,
                     "np": -bet,
                     "ocr": null,
                     "mr": null,
                     "ge": [
                        1,
                        11
                     ]
                  }
               },
               "err": null
            }

            await allfunctions.savejsonspin(user[0].id, JSON.stringify(json), gamename);

            const txnid = v4()
            const dataFormatada: string = moment().toISOString()

            await apicontroller.callbackgame({
               agent_code: agent[0].agentCode,
               agent_secret: agent[0].secretKey,
               user_code: user[0].username,
               user_balance: user[0].saldo,
               user_total_credit: user[0].valorganho,
               user_total_debit: user[0].valorapostado,
               game_type: "slot",
               slot: {
                  provider_code: "PGSOFT",
                  game_code: gamename,
                  round_id: await gerarNumeroUnico(),
                  type: "BASE",
                  bet: bet,
                  win: Number(valorganho),
                  txn_id: `${txnid}`,
                  txn_type: "debit_credit",
                  is_buy: false,
                  is_call: false,
                  user_before_balance: user[0].saldo,
                  user_after_balance: newbalance,
                  agent_before_balance: 100,
                  agent_after_balance: 100,
                  created_at: dataFormatada,
               },
            })
            res.send(json)
         }
         if (resultadospin.result === "bonus" && resultadospin.gamecode === "fortune-tiger") {
            const cartajson = await linhabonustiger.linhacarta(resultadospin.json)
            let call = await allfunctions.getcallbyid(resultadospin.idcall)

            if (call[0].steps === null && call[0].status === "pending") {
               const steps = Object.keys(cartajson).length - 1
               await allfunctions.updatestepscall(resultadospin.idcall, steps)
            }

            let calltwo = await allfunctions.getcallbyid(resultadospin.idcall)

            if (calltwo[0].steps === 0) {
               const multplicador = await countrwsp(cartajson[calltwo[0].steps].rwsp)
               await lwchange(
                  cartajson[calltwo[0].steps].rwsp,
                  cartajson[calltwo[0].steps].lw,
                  cs,
                  ml,
               )
               let valorganho = cs * ml * multplicador

               if (cartajson[calltwo[0].steps].completed === true) {
                  valorganho = cs * ml * multplicador * 10
               }

               const newbalance = saldoatual + valorganho - bet
               await fortunefunctions.attsaldobyatk(token, newbalance)
               await fortunefunctions.atualizardebitado(token, bet)
               await fortunefunctions.atualizarapostado(token, bet)
               await fortunefunctions.atualizarganho(token, valorganho)

               let json: any = {
                  dt: {
                     si: {
                        wc: 0,
                        ist: cartajson[calltwo[0].steps].ist,
                        itw: cartajson[calltwo[0].steps].itw,
                        fws: cartajson[calltwo[0].steps].fws,
                        wp: cartajson[calltwo[0].steps].wp,
                        orl: cartajson[calltwo[0].steps].orl,
                        lw: cartajson[calltwo[0].steps].lw,
                        irs: cartajson[calltwo[0].steps].irs,
                        gwt: 3,
                        fb: null,
                        ctw: valorganho,
                        pmt: null,
                        cwc: 1,
                        fstc: { "4": 2 },
                        pcwc: 0,
                        rwsp: cartajson[calltwo[0].steps].rwsp,
                        hashr: await gerarHashr(),
                        ml: cs,
                        cs: ml,
                        rl: cartajson[calltwo[0].steps].rl,
                        sid: await gerarSid(),
                        psid: await gerarPsid(),
                        st: 4,
                        nst: 1,
                        pf: 1,
                        aw: valorganho,
                        wid: 0,
                        wt: "C",
                        wk: "0_C",
                        wbn: null,
                        wfg: null,
                        blb: saldoatual,
                        blab: newbalance,
                        bl: newbalance,
                        tb: 0.0,
                        tbb: bet,
                        tw: valorganho,
                        np: valorganho,
                        ocr: null,
                        mr: null,
                        ge: [1, 11],
                     },
                  },
                  err: null,
               }
               await allfunctions.savejsonspin(user[0].id, JSON.stringify(json), gamename);
               await allfunctions.completecall(calltwo[0].id)

               const txnid = v4()
               const dataFormatada: string = moment().toISOString()
               await apicontroller.callbackgame({
                  agent_code: agent[0].agentCode,
                  agent_secret: agent[0].secretKey,
                  user_code: user[0].username,
                  user_balance: user[0].saldo,
                  user_total_credit: user[0].valorganho,
                  user_total_debit: user[0].valorapostado,
                  game_type: "slot",
                  slot: {
                     provider_code: "PGSOFT",
                     game_code: gamename,
                     round_id: await gerarNumeroUnico(),
                     type: "BASE",
                     bet: bet,
                     win: valorganho,
                     txn_id: `${txnid}`,
                     txn_type: "debit_credit",
                     is_buy: false,
                     is_call: true,
                     user_before_balance: user[0].saldo,
                     user_after_balance: newbalance,
                     agent_before_balance: 100,
                     agent_after_balance: 100,
                     created_at: dataFormatada,
                  },
               })
               res.send(json)
               return false
            }

            await allfunctions.subtrairstepscall(resultadospin.idcall)
            let json: any = {
               dt: {
                  si: {
                     wc: 103,
                     ist: cartajson[calltwo[0].steps].ist,
                     itw: cartajson[calltwo[0].steps].itw,
                     fws: cartajson[calltwo[0].steps].fws,
                     wp: cartajson[calltwo[0].steps].wp,
                     orl: cartajson[calltwo[0].steps].orl,
                     lw: cartajson[calltwo[0].steps].lw,
                     irs: cartajson[calltwo[0].steps].irs,
                     gwt: -1,
                     fb: null,
                     ctw: 0.0,
                     pmt: null,
                     cwc: 0,
                     fstc: null,
                     pcwc: 0,
                     rwsp: cartajson[calltwo[0].steps].rwsp,
                     hashr: await gerarHashr(),
                     ml: cs,
                     cs: ml,
                     rl: cartajson[calltwo[0].steps].rl,
                     sid: await gerarSid(),
                     psid: await gerarPsid(),
                     st: 1,
                     nst: 4,
                     pf: 1,
                     aw: 0.0,
                     wid: 0,
                     wt: "C",
                     wk: "0_C",
                     wbn: null,
                     wfg: null,
                     blb: saldoatual,
                     blab: saldoatual,
                     bl: saldoatual,
                     tb: bet,
                     tbb: bet,
                     tw: 0.0,
                     np: -bet,
                     ocr: null,
                     mr: null,
                     ge: [4, 11],
                  },
               },
               err: null,
            }
            res.send(json)
         }
      } catch (error) {
         logger.error(error)
      }
   },
}
