
import promisePool from "../database";
import { RowDataPacket } from "mysql2";

async function getToken() {
    try {
        const [rows] = await promisePool.query<RowDataPacket[]>("SELECT username, token, atk FROM users LIMIT 1");
        if (rows.length > 0) {
            console.log("User found:");
            console.log("Username:", rows[0].username);
            console.log("ATK:", rows[0].atk);
        } else {
            console.log("No users found in database.");
        }
    } catch (error) {
        console.error("Error getting token:", error);
    } finally {
        process.exit(0);
    }
}

getToken();
