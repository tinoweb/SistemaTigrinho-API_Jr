
import promisePool, { promisePoolPp } from "../database";
import { RowDataPacket, ResultSetHeader } from "mysql2";

async function syncHuck() {
    console.log("Starting sync for agent 'huck'...");

    try {
        // 1. Get agent from api90
        const [rows] = await promisePool.query<RowDataPacket[]>("SELECT * FROM agents WHERE agentCode = ?", ['huck']);
        
        if (rows.length === 0) {
            console.log("Agent 'huck' not found in api90.");
            process.exit(1);
        }

        const agent = rows[0];
        console.log("Found agent in api90:", agent.agentCode, "Callback:", agent.callbackurl);

        const siteEndPoint = agent.callbackurl || "";
        const email = `${agent.agentCode}@stellgames.com`;

        // 2. Check if exists in apipp
        const [checkRows] = await promisePoolPp.query<RowDataPacket[]>("SELECT id FROM agents WHERE agentCode = ?", ['huck']);

        if (checkRows.length === 0) {
            console.log("Agent 'huck' not found in apipp. Inserting...");
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
                   agent.agentCode, agent.agentCode, agent.agentToken, 
                   agent.agentToken, agent.secretKey, siteEndPoint, 
                   email, agent.saldo || 0
                ]
            );
            console.log("Inserted successfully.");
        } else {
            console.log("Agent 'huck' found in apipp. Updating siteEndPoint...");
            await promisePoolPp.query<ResultSetHeader>(
                `UPDATE agents SET siteEndPoint = ?, secretKey = ?, token = ? WHERE agentCode = ?`,
                [siteEndPoint, agent.secretKey, agent.agentToken, 'huck']
            );
            console.log("Updated successfully.");
        }

    } catch (error) {
        console.error("Error syncing huck:", error);
    } finally {
        process.exit(0);
    }
}

syncHuck();
