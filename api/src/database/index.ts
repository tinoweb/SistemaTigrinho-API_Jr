import mysql, { PoolOptions } from 'mysql2';
import logger from '../logger';
import 'dotenv/config';

const access: PoolOptions = {
    host: process.env.DB_HOST,
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME,
};

const accessPp: PoolOptions = {
    host: process.env.DB_HOST,
    user: process.env.DB_USERNAME_PP,
    password: process.env.DB_PASSWORD_PP,
    database: process.env.DB_NAME_PP,
};

const pool = mysql.createPool(access);
const promisePool = pool.promise();

const poolPp = mysql.createPool(accessPp);
export const promisePoolPp = poolPp.promise();

// Testa a conexão ao iniciar
async function testConnection() {
    try {
        const connection = await pool.promise().getConnection();
        connection.release();
        logger.info('CONEXÃO REALIZADA COM SUCESSO! (API90)');
    } catch (err) {
        logger.error(`MySQL error (API90): ${err}`);
    }

    try {
        const connectionPp = await poolPp.promise().getConnection();
        connectionPp.release();
        logger.info('CONEXÃO REALIZADA COM SUCESSO! (APIPP)');
    } catch (err) {
        logger.error(`MySQL error (APIPP): ${err}`);
    }
}

testConnection();

export default promisePool;
