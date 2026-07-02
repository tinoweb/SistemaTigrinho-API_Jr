
import "dotenv/config";
import promisePool from "../database";
import { RowDataPacket } from "mysql2";

async function checkSpinsInicial() {
    try {
        const [rows] = await promisePool.query<RowDataPacket[]>("SELECT game_code FROM spins_inicial");
        console.log("Games in spins_inicial:", rows.map(r => r.game_code));
    } catch (error) {
        console.error("Error checking spins_inicial:", error);
    } finally {
        process.exit(0);
    }
}

checkSpinsInicial();
