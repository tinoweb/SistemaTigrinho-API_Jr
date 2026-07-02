const axios = require('axios');
const https = require('https');

const agentCode = 'huck';
const agentToken = '8981643f09890293339fc251b52c5423';

const pragmaticRequestData = {
    method: "game_launch",
    agent_code: agentCode,
    agent_token: agentToken,
    user_code: "user_test_internal",
    game_code: "vs20olympgate",
    lang: "pt",
    provider_code: "PRAGMATIC",
    user_balance: 100,
    callback_url: "https://huckpremia.fun/api/pp_callback.php"
};

const httpsAgent = new https.Agent({
    rejectUnauthorized: false
});

async function test() {
    try {
        console.log("Sending request to https://127.0.0.1/api with Host: apipp.stellgames.com");
        const response = await axios.post("https://127.0.0.1/api", pragmaticRequestData, {
            httpsAgent: httpsAgent,
            headers: {
                'Host': 'apipp.stellgames.com',
                'Content-Type': 'application/json'
            }
        });
        console.log("Response status:", response.status);
        console.log("Response data:", response.data);
    } catch (error) {
        console.error("Error:", error.message);
        if (error.response) {
            console.error("Response data:", error.response.data);
            console.error("Response status:", error.response.status);
        }
    }
}

test();
