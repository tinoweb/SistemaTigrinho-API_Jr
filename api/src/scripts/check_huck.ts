
import { promisePoolPp } from "../database";
import { RowDataPacket } from "mysql2";

async function checkHuck() {
    try {
        const [rows] = await promisePoolPp.query<RowDataPacket[]>("SELECT * FROM agents WHERE agentCode = ?", ['huck']);
        if (rows.length > 0) {
            console.log("Agent 'huck' found in apipp:");
            console.log("siteEndPoint:", rows[0].siteEndPoint);
            console.log("secretKey:", rows[0].secretKey);
        } else {
            console.log("Agent 'huck' NOT found in apipp.");
        }
    } catch (error) {
        console.error("Error checking huck:", error);
    } finally {
        process.exit(0);
    }
}

checkHuck();
