import { Request, Response } from "express"
import axios from "axios"
import logger from "../../logger"
import * as crypto from "crypto"
import { v4 } from "uuid"
import moment from "moment"
import allfunctions from "../../functions/allfunctions"
import apicontroller from "../apicontroller"
import { emitirEventoInterno, adicionarListener } from "../../serverEvents"
import "dotenv/config"
import cashmaniafunctions from "../../functions/cash-mania/cashmaniafunctions"
import linhabonuscash from "../../jsons/cash-mania/linhabonuscash"
import linhaganhocash from "../../jsons/cash-mania/linhaganhocash"
import linhaperdacash from "../../jsons/cash-mania/linhaperdacash"
import notcashcash from "../../jsons/cash-mania/notcashcash"

export default {
   async getcash(req: Request, res: Response) {
      try {
         const token = req.body.atk;
         const gamename = "cash-mania";
         const user = await allfunctions.getuserbyatk(token);
         logger.info('[+] Usuario logado: ' + user[0].username)
         const jsonprimay = await allfunctions.getSpinByPlayerId(user[0].id);
         const jsoninicial = await allfunctions.getjsonprimary(gamename);
         if (jsonprimay.length === 0) {
            await allfunctions.createOrUpdateSpin(user[0].id, gamename, jsoninicial[0].json);
         }
         if (jsonprimay.length > 0 && jsonprimay[0].game_code === gamename) {
            logger.info('[+] Json Recuperado Do Ultimo Spin.')
         } else {
            await allfunctions.createOrUpdateSpin(user[0].id, gamename, jsoninicial[0].json);
         }
         const json = await allfunctions.getSpinByPlayerId(user[0].id);
         const jsonformatado = JSON.parse(json[0].json);
         res.send({
            dt: {
               fb: {
                  is: true,
                  bm: 100,
                  t: 0.75
               },
               wt: {
                  mw: 5,
                  bw: 20,
                  mgw: 35,
                  smgw: 50
               },
               maxwm: null,
               cs: [0.02, 0.12, 0.8],
               ml: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
               mxl: 25,
               bl: user[0].saldo,
               inwe: false,
               iuwe: false,
               ls: jsonformatado.dt,
               cc: "BRL"
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
      const token = req.body.atk

      async function lwchange(json2: { [key: string]: any }, cs: number, ml: number, ganho: number) {
         for (let chave2 in json2) {
            if (json2.hasOwnProperty(chave2)) {
               json2[chave2] = ganho;
            }
         }
      }
      async function rvchange(bet: number, cs: number, ml: number, json: any) {
         const { rl, rv } = json;

         const keysToIgnore = [3, 4, 5];

         for (let i = 0; i < rl.length; i++) {
            if (!keysToIgnore.includes(i)) {
               if (rl[i] === 1) {
                  rv[i] = cs * ml * 25 * 0.1;
               }
               if (rl[i] === 2) {
                  rv[i] = cs * ml * 25 * 0.5;
               }
               if (rl[i] === 3) {
                  rv[i] = cs * ml * 25 * 1;
               }
               if (rl[i] === 4) {
                  rv[i] = cs * ml * 25 * 5;
               }
               if (rl[i] === 5) {
                  rv[i] = cs * ml * 25 * 10;
               }
            }
         }
      }


      async function countrwsp(json: { [key: string]: any }) {
         let multplicador: number = 0
         for (let i = 1; i <= 10; i++) {
            const chave = i.toString()
            if (json.hasOwnProperty(chave)) {
               multplicador = multplicador + parseFloat(json[chave])
               logger.info('Multiplicador Countrwsp: ' + multplicador)
            }
         }
         return multplicador
      }

      async function returnrwm(json: { [key: string]: any }) {
         let value: number = 0
         for (let chave in json) {
            if (json.hasOwnProperty(chave)) {
               value = value + parseFloat(json[chave])
            }
         }
         return value
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
         const user = await cashmaniafunctions.getuserbyatk(token)
         let bet: number = cs * ml * 25
         let saldoatual: number = user[0].saldo
         const gamename = "cash-mania"

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
            res.send(await notcashcash.notcash(saldoatual, cs, ml))
            return false
         } else if (checkuserbalance.data.msg === "INSUFFICIENT_USER_FUNDS") {
            res.send(await notcashcash.notcash(saldoatual, cs, ml))
            return false
         }

         const retornado = user[0].valorganho
         const valorapostado = user[0].valorapostado

         const rtp = (retornado / valorapostado) * 100

         console.log("RTP ATUAL " + rtp)

         console.log("BET ATUAL " + bet)

         if (saldoatual < bet) {
            const semsaldo = await notcashcash.notcash(saldoatual, cs, ml)
            res.send(semsaldo)
            return false
         }

         const resultadospin = await allfunctions.calcularganho(bet, saldoatual, token, gamename)

         if (resultadospin.result === "perda") {
            let newbalance = saldoatual - bet
            await cashmaniafunctions.attsaldobyatk(token, newbalance)
            await cashmaniafunctions.atualizardebitado(token, bet)
            await cashmaniafunctions.atualizarapostado(token, bet)
            const perdajson = await linhaperdacash.linhaperda()

            let json: any = {
               dt: {
                  si: {
                     wp: null,
                     lw: null,
                     twbm: 0.0,
                     fs: null,
                     imw: false,
                     rv: perdajson.rv,
                     orl: null,
                     orv: null,
                     rsrl: null,
                     rsrv: null,
                     nfp: null,
                     gwt: -1,
                     fb: null,
                     ctw: 0.0,
                     pmt: null,
                     cwc: 0,
                     fstc: null,
                     pcwc: 0,
                     rwsp: null,
                     hashr: await gerarHashr(),
                     ml: ml,
                     cs: cs,
                     rl: perdajson.rl,
                     sid: await gerarSid(),
                     psid: await gerarPsid(),
                     st: 1,
                     nst: 1,
                     pf: 1,
                     aw: 0.0,
                     wid: 0,
                     wt: 'C',
                     wk: '0_C',
                     wbn: null,
                     wfg: null,
                     blb: saldoatual,
                     blab: newbalance,
                     bl: newbalance,
                     tb: bet,
                     tbb: bet,
                     tw: 0.0,
                     np: -bet,
                     ocr: null,
                     mr: null,
                     ge: [1, 11],
                  },
               },
               err: null,
            };

            await allfunctions.savejsonspin(user[0].id, JSON.stringify(json), gamename);
            const txnid = v4()
            const dataFormatada: string = moment().toISOString()
            await apicontroller.callbackgame({
               agent_code: agent[0].agentcode,
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
            const ganhojson = await linhaganhocash.linhaganho(bet);
            await rvchange(bet, Number(cs), Number(ml), ganhojson);
            logger.info(`cs: ${cs}, ml: ${ml}`);
            logger.info(`ganhojson: ${JSON.stringify(ganhojson)}`);
            let valorganho = 0;

            if (ganhojson.rv[4] > 0) {
               valorganho = ganhojson.rv[1] * ganhojson.rv[4];
            }

            await lwchange(ganhojson.lw, Number(cs), Number(ml), valorganho);

            logger.info(`Valor do ganho calculado: ${valorganho}`);

            const newbalance = saldoatual + valorganho - bet;
            await cashmaniafunctions.attsaldobyatk(token, newbalance);
            await cashmaniafunctions.atualizardebitado(token, bet);
            await cashmaniafunctions.atualizarapostado(token, bet);
            await cashmaniafunctions.atualizarganho(token, valorganho);

            function gerarBooleanoAleatorio(): boolean {
               // Gera um número aleatório entre 0 e 1
               const boolean = Math.floor(Math.random() * 10) + 1
               console.log("NUMERO DO BOOLEANO " + boolean)

               // Se o número for maior ou igual a 0.5, retorna true, caso contrário, retorna false
               return boolean >= 5
            }

            let json: any = {
               dt: {
                  si: {
                     wp: ganhojson.wp,
                     lw: ganhojson.lw,
                     twbm: ganhojson.rv[1],
                     fs: null,
                     imw: false,
                     rv: ganhojson.rv,
                     orl: null,
                     orv: null,
                     rsrl: ganhojson.rsrl,
                     rsrv: ganhojson.rsrv,
                     nfp: null,
                     gwt: -1,
                     fb: null,
                     ctw: valorganho,
                     pmt: null,
                     cwc: 1,
                     fstc: null,
                     pcwc: 1,
                     rwsp: ganhojson.rwsp,
                     hashr: await gerarHashr(),
                     ml: ml,
                     cs: cs,
                     rl: ganhojson.rl,
                     sid: await gerarSid(),
                     psid: await gerarPsid(),
                     st: 1,
                     nst: 1,
                     pf: 1,
                     aw: valorganho,
                     wid: 0,
                     wt: 'C',
                     wk: '0_C',
                     wbn: null,
                     wfg: null,
                     blb: saldoatual,
                     blab: newbalance,
                     bl: newbalance,
                     tb: bet,
                     tbb: bet,
                     tw: valorganho,
                     np: bet,
                     ocr: null,
                     mr: null,
                     ge: [1, 11],
                  },
               },
               err: null,
            };

            await allfunctions.savejsonspin(user[0].id, JSON.stringify(json), gamename);

            const txnid = v4()
            const dataFormatada: string = moment().toISOString()

            await apicontroller.callbackgame({
               agent_code: agent[0].agentcode,
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
         if (resultadospin.result === "bonus" && resultadospin.gamecode === "cash-mania") {
            const bonusjson = await linhabonuscash.linhabonus(resultadospin.json);

            if ((await allfunctions.getcallbyid(resultadospin.idcall))[0].steps === null && (await allfunctions.getcallbyid(resultadospin.idcall))[0].status === 'pending') {
               if (saldoatual < bet) {
                  const semsaldo = await notcashcash.notcash(saldoatual, cs, ml);
                  res.send(semsaldo);
                  return false;
               }
               const steps = Object.keys(bonusjson).length - 1;
               await allfunctions.updatestepscall(resultadospin.idcall, steps);
            }

            const call = await allfunctions.getcallbyid(resultadospin.idcall);

            if (call[0].steps === 0) {
               await allfunctions.completecall(call[0].id);
            }

            let newbalance = 0;
            let valorganho = 0;

            await rvchange(bet, Number(cs), Number(ml), bonusjson[call[0].steps]);

            if (bonusjson[call[0].steps] && bonusjson[call[0].steps].rv[4] > 0 && bonusjson[call[0].steps].wp != null) {
               valorganho = bonusjson[call[0].steps].rv[1] * bonusjson[call[0].steps].rv[4];
            }

            await lwchange(bonusjson[call[0].steps].rwsp, bonusjson[call[0].steps].lw, cs, ml);


            if (call[0].steps === Object.keys(bonusjson).length - 1) {
               newbalance = saldoatual - bet + valorganho;
               await cashmaniafunctions.attsaldobyatk(token, newbalance);

               const txnid = v4();
               const dataFormatada = moment().toISOString();

               await apicontroller.callbackgame({
                  agent_code: agent[0].agentcode,
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
                     is_call: false,
                     user_before_balance: user[0].saldo,
                     user_after_balance: newbalance,
                     agent_before_balance: 100,
                     agent_after_balance: 100,
                     created_at: dataFormatada,
                  },
               });
            }

            newbalance = saldoatual + valorganho;
            if (call[0].steps === 0) {
               newbalance = saldoatual + valorganho - bet;
            }

            console.log("VALOR GANHO " + valorganho)

            if (valorganho > 0) {
               const currentCall = await allfunctions.getcallbyid(resultadospin.idcall);
               const currentAw = currentCall[0].aw || 0;
               const newAw = currentAw + valorganho;
               await cashmaniafunctions.attawcall(call[0].id, newAw);
            }

            await cashmaniafunctions.attsaldobyatk(token, newbalance);
            await cashmaniafunctions.atualizardebitado(token, bet);
            await cashmaniafunctions.atualizarapostado(token, bet);
            await cashmaniafunctions.atualizarganho(token, valorganho);

            if (call[0].steps > 0) {
               await allfunctions.subtrairstepscall(resultadospin.idcall);
            }

            const currentCall = await allfunctions.getcallbyid(resultadospin.idcall);
            if (bonusjson && call[0] && call[0].steps !== undefined &&
               bonusjson[call[0].steps] &&
               bonusjson[call[0].steps].fs &&
               bonusjson[call[0].steps].fs.hasOwnProperty("aw")) {
               bonusjson[call[0].steps].fs["aw"] = currentCall[0].aw;
               console.log("VALOR DO AW " + currentCall[0].aw)
            }

            let json: any = {
               dt: {
                  si: {
                     wp: bonusjson[call[0].steps] ? bonusjson[call[0].steps].wp : null,
                     lw: bonusjson[call[0].steps] ? bonusjson[call[0].steps].lw : null,
                     twbm: bonusjson[call[0].steps] && bonusjson[call[0].steps].rv ? bonusjson[call[0].steps].rv[1] : 0,
                     fs: bonusjson[call[0].steps] ? bonusjson[call[0].steps].fs : null,
                     imw: false,
                     rv: bonusjson[call[0].steps] ? bonusjson[call[0].steps].rv : null,
                     orl: null,
                     orv: null,
                     rsrl: null,
                     rsrv: null,
                     nfp: null,
                     gwt: -1,
                     fb: null,
                     ctw: valorganho,
                     pmt: null,
                     cwc: 0,
                     fstc: bonusjson[call[0].steps] ? bonusjson[call[0].steps].fstc : null,
                     pcwc: 0,
                     rwsp: null,
                     hashr: await gerarHashr(),
                     ml: ml,
                     cs: cs,
                     rl: bonusjson[call[0].steps] ? bonusjson[call[0].steps].rl : null,
                     sid: await gerarSid(),
                     psid: await gerarPsid(),
                     st: bonusjson[call[0].steps] ? bonusjson[call[0].steps].st : 1,
                     nst: bonusjson[call[0].steps] ? bonusjson[call[0].steps].nst : 1,
                     pf: 1,
                     aw: (await allfunctions.getcallbyid(resultadospin.idcall))[0].aw,
                     wid: 0,
                     wt: "C",
                     wk: "0_C",
                     wbn: null,
                     wfg: null,
                     blb: saldoatual,
                     blab: newbalance,
                     bl: newbalance,
                     tb: bet,
                     tbb: bet,
                     tw: valorganho,
                     np: bet,
                     ocr: null,
                     mr: null,
                     ge: [1, 11]
                  }
               },
               err: null,
            };

            const txnid = v4();
            const dataFormatada = moment().toISOString();

            await apicontroller.callbackgame({
               agent_code: agent[0].agentcode,
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
                  bet: 0,
                  win: valorganho,
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
            });

            await allfunctions.savejsonspin(user[0].id, JSON.stringify(json), gamename);

            res.send(json);
         }


      } catch (error) {
         logger.error(error)
      }
   },
}
