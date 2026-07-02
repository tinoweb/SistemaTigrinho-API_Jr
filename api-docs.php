<?php
// API Documentation Generator
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação da API Unificada Stell Games</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; line-height: 1.6; color: #333; max-width: 1200px; margin: 0 auto; padding: 20px; background-color: #f4f6f8; }
        .container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1, h2, h3 { color: #2c3e50; }
        h1 { border-bottom: 2px solid #eaeaea; padding-bottom: 10px; }
        .endpoint { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .method { display: inline-block; padding: 4px 8px; border-radius: 4px; font-weight: bold; color: white; font-size: 0.9em; margin-right: 10px; }
        .post { background-color: #49cc90; }
        .get { background-color: #61affe; }
        .url { font-family: monospace; font-size: 1.1em; color: #333; }
        pre { background: #282c34; color: #abb2bf; padding: 15px; border-radius: 5px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #e1e4e8; }
        th { background-color: #f8f9fa; font-weight: 600; }
        .required { color: #e74c3c; font-size: 0.8em; }
        .badge { background: #e1e4e8; padding: 2px 6px; border-radius: 4px; font-size: 0.85em; }
        .toc { background: #eef2f5; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .toc ul { list-style: none; padding-left: 0; }
        .toc a { text-decoration: none; color: #0366d6; }
        .toc a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Documentação da API Unificada Stell Games</h1>
        <p>Esta documentação descreve como integrar com a API unificada da Stell Games. Ela centraliza jogos PG Soft, Blaze Double e Pragmatic Play.</p>
        
        <div class="toc">
            <h3>Índice</h3>
            <ul>
                <li><a href="#config">1. Configuração Básica</a></li>
                <li><a href="#agents">2. Gestão de Agentes</a></li>
                <li><a href="#launch">3. Lançamento de Jogos (Unificado)</a></li>
                <li><a href="#pp">4. API Pragmatic Play (Proxy)</a></li>
                <li><a href="#callback">5. Callback (Webhook)</a></li>
                <li><a href="#games">6. Lista de Jogos Suportados</a></li>
            </ul>
        </div>

        <h2 id="config">1. Configuração (Base URL)</h2>
        <p>Configure em seu painel ou sistema apenas a URL abaixo.</p>
        <pre>https://api.stellgames.com</pre>
        <p><strong>Nota:</strong> Esta API substitui URLs antigas como <code>apipp.stellgames.com</code>.</p>

        <hr>

        <h2 id="agents">2. Gestão de Agentes</h2>
        
        <div class="endpoint">
            <div>
                <span class="method post">POST</span>
                <span class="url">.../api/v1/createagent</span>
            </div>
            <p>Cria um novo agente no sistema.</p>
            <h3>Exemplo de Requisição</h3>
            <pre>{
    "agentCode": "AGENTE_001",
    "saldo": 1000,
    "agentToken": "SEU_AGENT_TOKEN_NOVO",
    "secretKey": "SUA_SECRET_KEY_NOVA",
    "callbackurl": "https://seu-servidor.com/callback"
}</pre>
        </div>

        <div class="endpoint">
            <div>
                <span class="method post">POST</span>
                <span class="url">.../api/v1/getagent</span>
            </div>
            <p>Consulta dados de um agente existente.</p>
            <h3>Exemplo de Requisição</h3>
            <pre>{
    "agentToken": "SEU_AGENT_TOKEN",
    "secretKey": "SUA_SECRET_KEY"
}</pre>
        </div>

        <div class="endpoint">
            <div>
                <span class="method post">POST</span>
                <span class="url">.../api/v1/attagent</span>
            </div>
            <p>Atualiza as probabilidades e configurações RTP do agente.</p>
            <h3>Exemplo de Requisição</h3>
            <pre>{
    "agentToken": "SEU_AGENT_TOKEN",
    "secretKey": "SUA_SECRET_KEY",
    "probganho": "0",
    "probbonus": "0",
    "probganhortp": "96.5",
    "probganhoinfluencer": "98.0"
}</pre>
        </div>

        <hr>

        <h2 id="launch">3. Lançamento de Jogos (Unificado)</h2>
        
        <div class="endpoint">
            <div>
                <span class="method post">POST</span>
                <span class="url">.../api/v1/game_launch</span>
            </div>
            <p>Endpoint único para iniciar sessões de jogos PG Soft, Blaze Double e Pragmatic Play.</p>
            
            <h3>Headers</h3>
            <pre>Content-Type: application/json</pre>

            <h3>Body Parameters</h3>
            <table>
                <thead>
                    <tr>
                        <th>Campo</th>
                        <th>Tipo</th>
                        <th>Obrigatório</th>
                        <th>Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>agentToken</td>
                        <td>string</td>
                        <td><span class="required">SIM</span></td>
                        <td>Token do agente.</td>
                    </tr>
                    <tr>
                        <td>secretKey</td>
                        <td>string</td>
                        <td><span class="required">SIM</span></td>
                        <td>Chave secreta do agente.</td>
                    </tr>
                    <tr>
                        <td>user_code</td>
                        <td>string</td>
                        <td><span class="required">SIM</span></td>
                        <td>ID único do usuário.</td>
                    </tr>
                    <tr>
                        <td>game_code</td>
                        <td>string</td>
                        <td><span class="required">SIM</span></td>
                        <td>Código do jogo (ver lista abaixo).</td>
                    </tr>
                    <tr>
                        <td>provider_code</td>
                        <td>string</td>
                        <td><span class="required">SIM</span></td>
                        <td><code>PG</code>, <code>BLAZE_DOUBLE</code> ou <code>PRAGMATIC</code>.</td>
                    </tr>
                    <tr>
                        <td>user_balance</td>
                        <td>number</td>
                        <td><span class="required">SIM</span></td>
                        <td>Saldo atual do usuário.</td>
                    </tr>
                    <tr>
                        <td>is_influencer</td>
                        <td>boolean</td>
                        <td>NÃO</td>
                        <td>Define se o usuário é influenciador (default: false).</td>
                    </tr>
                </tbody>
            </table>

            <h3>Exemplo de Requisição</h3>
            <pre>{
    "agentToken": "seu_token",
    "secretKey": "sua_chave",
    "user_code": "user123",
    "game_code": "fortune-tiger",
    "provider_code": "PG",
    "user_balance": 100.00,
    "is_influencer": false
}</pre>

            <h3>Exemplo de Resposta (Sucesso)</h3>
            <pre>{
    "status": 1,
    "msg": "SUCCESS",
    "launch_url": "https://api.stellgames.com/126/index.html?...",
    "user_code": "user123",
    "user_balance": 100.00,
    "user_created": true,
    "currency": "BRL"
}</pre>
        </div>

        <hr>

        <h2 id="pp">4. API Pragmatic Play (Proxy)</h2>
        <p>Caminho da rota: <code>/pp/</code> (Adicione este caminho à Base URL)</p>

        <div class="endpoint">
            <div>
                <span class="method post">POST</span>
                <span class="url">.../pp/</span>
            </div>
            <p>Proxy reverso para operações diretas da API Pragmatic Play.</p>
            
            <h3>Exemplo: Listar Jogos</h3>
            <pre>{
    "method": "game_list",
    "agent_code": "seu_codigo",
    "agent_token": "seu_token",
    "provider_code": "PRAGMATIC"
}</pre>
        </div>

        <hr>

        <h2 id="callback">5. Callback (Webhook)</h2>
        <div class="endpoint">
            <p>O sistema envia requisições POST para a <code>callbackurl</code> do agente a cada transação.</p>
            <h3>Payload</h3>
            <pre>{
    "agent_code": "seu_agent_code",
    "agent_secret": "sua_secret_key",
    "user_code": "user123",
    "game_code": "fortune-tiger",
    "type": "bet", 
    "amount": 2.00,
    "user_balance": 98.00,
    "transaction_id": "tx_123456"
}</pre>
        </div>

        <hr>

        <h2 id="games">6. Lista de Jogos Suportados</h2>
        <table>
            <thead>
                <tr>
                    <th>Nome do Jogo</th>
                    <th>Game Code</th>
                    <th>Provedor</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Fortune Tiger</td><td><code>fortune-tiger</code></td><td>PG</td></tr>
                <tr><td>Fortune Ox</td><td><code>fortune-ox</code></td><td>PG</td></tr>
                <tr><td>Fortune Mouse</td><td><code>fortune-mouse</code></td><td>PG</td></tr>
                <tr><td>Fortune Dragon</td><td><code>fortune-dragon</code></td><td>PG</td></tr>
                <tr><td>Fortune Rabbit</td><td><code>fortune-rabbit</code></td><td>PG</td></tr>
                <tr><td>Bikini Paradise</td><td><code>bikini-paradise</code></td><td>PG</td></tr>
                <tr><td>Jungle Delight</td><td><code>jungle-delight</code></td><td>PG</td></tr>
                <tr><td>Ganesha Gold</td><td><code>ganesha-gold</code></td><td>PG</td></tr>
                <tr><td>Double Fortune</td><td><code>double-fortune</code></td><td>PG</td></tr>
                <tr><td>Dragon Tiger Luck</td><td><code>dragon-tiger-luck</code></td><td>PG</td></tr>
                <tr><td>Ninja Raccoon</td><td><code>ninja-raccoon</code></td><td>PG</td></tr>
                <tr><td>Lucky Clover</td><td><code>lucky-clover</code></td><td>PG</td></tr>
                <tr><td>Ultimate Striker</td><td><code>ultimate-striker</code></td><td>PG</td></tr>
                <tr><td>Prosperity Fortune Tree</td><td><code>prosper-ftree</code></td><td>PG</td></tr>
                <tr><td>Chicky Run</td><td><code>chicky-run</code></td><td>PG</td></tr>
                <tr><td>Butterfly Blossom</td><td><code>butterfly-blossom</code></td><td>PG</td></tr>
                <tr><td>Cash Mania</td><td><code>cash-mania</code></td><td>PG</td></tr>
                <tr><td>Treasures of Aztec</td><td><code>treasures-aztec</code></td><td>PG</td></tr>
                <tr><td>Ice & Fire</td><td><code>gdn-ice-fire</code></td><td>PG</td></tr>
                <tr><td>Piggy Gold</td><td><code>piggy-gold</code></td><td>PG</td></tr>
                <tr><td>Wild Bandito</td><td><code>wild-bandito</code></td><td>PG</td></tr>
                <tr><td>Zombie Outbreak</td><td><code>zombie-outbreak</code></td><td>PG</td></tr>
                <tr><td>Majestic TS</td><td><code>majestic-ts</code></td><td>PG</td></tr>
                <tr><td>Thai River Wonders</td><td><code>thai-river</code></td><td>PG</td></tr>
                <tr><td>Rise of Apollo</td><td><code>rise-apollo</code></td><td>PG</td></tr>
                <tr><td>Wild Bounty Showdown</td><td><code>wild-bounty-sd</code></td><td>PG</td></tr>
                <tr><td>Three Crazy Pigs</td><td><code>three-cz-pigs</code></td><td>PG</td></tr>
                <tr><td>Fortune Snake</td><td><code>fortune-snake</code></td><td>PG</td></tr>
                <tr><td>Blaze Double</td><td><code>blaze-double</code></td><td>BLAZE</td></tr>
            </tbody>
        </table>
    </div>
</body>
</html>