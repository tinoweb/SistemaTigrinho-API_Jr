import promisePool, { promisePoolPp } from "../database"
import { RowDataPacket, ResultSetHeader } from "mysql2"

export default {
   async getagentbyagentToken(token: string) {
      const res = await promisePool.query<RowDataPacket[]>(`SELECT * FROM agents WHERE agentToken= ?`, [token])
      return res[0]
   },
   async getagentbysecretkey(secretkey: string) {
      const res = await promisePool.query<RowDataPacket[]>(`SELECT * FROM agents WHERE secretKey= ?`, [secretkey])
      return res[0]
   },
   async getuserbyagent(usercode: number, agentid: number) {
      const res = await promisePool.query<RowDataPacket[]>(`SELECT * FROM users WHERE username= ? and agentid = ?`, [usercode, agentid])
      return res[0]
   },
   async setbalanceuserbyid(id: number, balance: number, is_influencer?: boolean) {
      if (is_influencer !== undefined) {
         const res = await promisePool.query<ResultSetHeader>(
            "UPDATE users SET saldo = ?, isinfluencer = ? WHERE id=?",
            [balance, is_influencer, id]
         );
         return res[0];
      } else {
         const res = await promisePool.query<ResultSetHeader>(
            "UPDATE users SET saldo = ? WHERE id=?",
            [balance, id]
         );
         return res[0];
      }
   },
   async createuser(
      user_code: string,
      tokenuser: string,
      atkuser: string,
      balance: number,
      agentid: number,
      is_influencer: boolean = false
   ) {
      const res = await promisePool.query<ResultSetHeader>(
         "INSERT INTO users (username, token, atk, saldo, agentid, isinfluencer) VALUES(?,?,?,?,?,?)",
         [user_code, tokenuser, atkuser, balance, agentid, is_influencer]
      );
      return res[0];
   },
   async attagent(id: number, probganho: string, probbonus: string, probganhortp: string, probganhoinfluencer: string, probbonusinfluencer: string, probganhoaposta: string, probganhosaldo: string) {
      const res = await promisePool.query<ResultSetHeader>("UPDATE agents SET probganho = ?,probbonus = ?,probganhortp = ?,probganhoinfluencer = ?,probbonusinfluencer = ?,probganhoaposta = ?,probganhosaldo = ? WHERE id=?", [probganho, probbonus, probganhortp, probganhoinfluencer, probbonusinfluencer, probganhoaposta, probganhosaldo, id])
      return res[0]
   },

   async createAgent(
      agentCode: string,
      saldo: number,
      agentToken: string,
      secretKey: string,
      probganho: string = "0",
      probbonus: string = "0",
      probganhortp: string = "0",
      probganhoinfluencer: string = "0",
      probbonusinfluencer: string = "0",
      probganhoaposta: string = "0",
      probganhosaldo: string = "0",
      callbackurl: string | null = null
   ) {
      const res = await promisePool.query<ResultSetHeader>(
         `INSERT INTO agents (
         agentCode, 
         saldo, 
         agentToken, 
         secretKey, 
         probganho, 
         probbonus, 
         probganhortp, 
         probganhoinfluencer, 
         probbonusinfluencer, 
         probganhoaposta, 
         probganhosaldo, 
         callbackurl
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
         [
            agentCode,
            saldo,
            agentToken,
            secretKey,
            probganho,
            probbonus,
            probganhortp,
            probganhoinfluencer,
            probbonusinfluencer,
            probganhoaposta,
            probganhosaldo,
            callbackurl
         ]
      );

      // Sincronizar com apipp (inserir na tabela agents do banco apipp para garantir que o siteEndPoint seja preenchido)
      try {
         const siteEndPoint = callbackurl || "";
         const email = `${agentCode}@stellgames.com`; // Email fictício para preencher o campo obrigatório
         
         // Verificar se o agente já existe na apipp para evitar erro de duplicidade
         const checkAgent = await promisePoolPp.query<RowDataPacket[]>(`SELECT id FROM agents WHERE agentCode = ?`, [agentCode]);
         
         if (checkAgent[0].length === 0) {
             await promisePoolPp.query<ResultSetHeader>(
                `INSERT INTO agents (
                   agentCode, agentName, password, apiType, agentType, 
                   token, secretKey, siteEndPoint, ipAddress, zeroSetting, 
                   createdAt, updatedAt, email, status, depth, role, parentPath, 
                   curIndex, jackpotCome, showCall, lang, curShow, betEdited, 
                   minBet, maxBet, betLimitSkin, blockOppositeBet, blockRedEnvelope, 
                   balance, rtp
                ) VALUES (?, ?, ?, 1, 1, ?, ?, ?, '127.0.0.1', '0', NOW(), NOW(), ?, 1, 0, 0, '.', 0, 100, 1, 'en', 1, 0, 0.20, 100.00, 'SKIN1', 0, 0, ?, 80)`,
                [
                   agentCode, agentCode, agentToken, 
                   agentToken, secretKey, siteEndPoint, 
                   email, saldo
                ]
             );
         } else {
             // Se já existe, atualiza o siteEndPoint
             await promisePoolPp.query<ResultSetHeader>(
                `UPDATE agents SET siteEndPoint = ?, secretKey = ?, token = ? WHERE agentCode = ?`,
                [siteEndPoint, secretKey, agentToken, agentCode]
             );
         }
      } catch (error) {
         console.error("Erro ao sincronizar agente na apipp:", error);
      }

      return res[0];
   },
   async updateAgentProbabilities(agentId: number, rtpagent: number, rtpinfluencer: number) {
      try {
         const formatValue = (value: number) => value.toString().padStart(3, "0");

         const probGanho = formatValue(Math.floor(rtpagent)) + formatValue(Math.floor((rtpagent % 1) * 100));
         const probGanhoInfluencer = formatValue(Math.floor(rtpinfluencer)) + formatValue(Math.floor((rtpinfluencer % 1) * 100));
         const probBonusInfluencer = formatValue(Math.floor(rtpinfluencer)) + formatValue(Math.floor((rtpinfluencer % 1) * 100));

         await promisePool.query(
            "UPDATE agents SET probganho = ?, probganhoinfluencer = ?, probbonusinfluencer = ?, probganhosaldo = ?, probganhortp = ? WHERE id = ?",
            [probGanho, probGanhoInfluencer, probBonusInfluencer, probGanho, probGanho, agentId]
         );

         console.log(`Probabilidades atualizadas para o agente ${agentId}`);
      } catch (error) {
         console.log(`Erro ao atualizar as probabilidades para o agente ${agentId}:`, error);
      }
   }



}
