
import promisePool, { promisePoolPp } from "../database";
import { RowDataPacket } from "mysql2";

async function checkSpins() {
    try {
        console.log("Checking spins_inicial in api90 (default pool)...");
        const [rows90] = await promisePool.query<RowDataPacket[]>("SELECT COUNT(*) as count FROM spins_inicial");
        console.log(`api90 spins_inicial count: ${rows90[0].count}`);

        console.log("Checking spins_inicial in apipp (promisePoolPp)...");
        const [rowsPp] = await promisePoolPp.query<RowDataPacket[]>("SELECT COUNT(*) as count FROM spins_inicial");
        console.log(`apipp spins_inicial count: ${rowsPp[0].count}`);

    } catch (error) {
        console.error("Error checking spins:", error);
    } finally {
        process.exit(0);
    }
}

checkSpins();
