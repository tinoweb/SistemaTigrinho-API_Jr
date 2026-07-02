# Guia de Integração - StellGames API

Este documento descreve o processo de integração com as APIs da StellGames, cobrindo o lançamento de jogos e o recebimento de callbacks (webhooks) para atualizações de saldo.

## 1. Visão Geral da Arquitetura

O sistema é composto por dois módulos principais que operam em paralelo:
1.  **API Geral (PG Soft, e outros)**: Gerenciada em Node.js. Utiliza endpoints específicos (`/gold_api/...`) para callbacks.
2.  **API Pragmatic Play (PP)**: Gerenciada em PHP. Utiliza um endpoint único configurável para callbacks.

Ambos compartilham o conceito de "Lançamento Unificado", mas possuem formatos de webhook ligeiramente diferentes.

## 2. Configuração do Agente

Para integrar sua plataforma, você deve configurar o registro na tabela `agents`.

### Campos Principais
| Campo | Descrição |
| :--- | :--- |
| `agentCode` | Código único identificador do agente. |
| `secretKey` | Chave secreta para autenticação. |
| `agentToken` | Token utilizado nas requisições de lançamento. |
| `callbackurl` / `siteEndPoint` | **Base URL** do seu webhook. Ex: `https://suaplataforma.com/api/`. |
| `apiType` | `0` para integração via Webhook (Seamless). |

---

## 3. Lançamento de Jogos (Unified Launch)

O lançamento de jogos é centralizado. O sistema redirecionará automaticamente para a infraestrutura correta.

**Endpoint**: `POST https://api.stellgames.com/api/v1/game_launch`

**Payload (JSON)**:
```json
{
    "agentToken": "seu_agent_token",
    "secretKey": "sua_secret_key",
    "user_code": "id_do_usuario",
    "user_balance": 100.00,
    "game_type": "slot",
    "provider_code": "PGSOFT", 
    "game_code": "fortune-tiger",
    "currency": "BRL",
    "lang": "pt"
}
```

*   **`provider_code`**: 
    *   Use `"PP"` ou `"PRAGMATIC"` para Pragmatic Play.
    *   Use `"PGSOFT"` para PG Soft (Fortune Tiger, etc.).

**Resposta de Sucesso**:
```json
{
    "status": 1,
    "msg": "SUCCESS",
    "launch_url": "https://...", 
    "user_code": "...",
    "user_balance": 100
}
```

---

## 4. Webhooks e Callbacks (Seamless)

Se configurado com `apiType = 0`, você deve preparar sua API para receber requisições de dois sistemas diferentes.

### 4.1. Callbacks - API Geral (PG Soft, etc.)
A API Node.js adiciona automaticamente sufixos à sua `callbackurl`.
**Exemplo**: Se sua url base for `https://site.com/api/`, o sistema chamará `https://site.com/api/gold_api/user_balance`.

#### A. Verificar Saldo (`POST /gold_api/user_balance`)
Chamado antes de iniciar uma rodada para garantir fundos.

**Request**:
```json
{
    "user_code": "id_do_usuario"
}
```

**Response Esperada (JSON)**:
*   Se sucesso: Retorne o saldo (ex: `100.00` ou `{ "balance": 100.00 }` - *o sistema verifica status 200*).
*   Se erro/saldo insuficiente: Retorne `msg: "INSUFFICIENT_USER_FUNDS"` ou `msg: "INVALID_USER"`.

#### B. Resultado do Jogo (`POST /gold_api/game_callback`)
Chamado após o término da rodada (spin) para atualizar o saldo.

**Request**:
```json
{
    "agent_code": "seu_agent_code",
    "agent_secret": "sua_secret_key",
    "user_code": "id_do_usuario",
    "user_balance": 100.00,       // Saldo ANTES da atualização atual (informativo)
    "game_type": "slot",
    "slot": {
        "provider_code": "PGSOFT",
        "game_code": "fortune-tiger",
        "round_id": "123456",
        "type": "BASE",
        "bet": 2.00,
        "win": 5.00,
        "txn_id": "unique_txn_id",
        "txn_type": "debit_credit",
        "user_before_balance": 100.00,
        "user_after_balance": 103.00  // Saldo FINAL calculado pelo sistema
    }
}
```
*   **Ação**: Você deve atualizar o saldo do usuário para `user_after_balance` (ou processar `win - bet`).

---

### 4.2. Callbacks - API Pragmatic Play (PP)
A API PHP envia requisições diretamente para a `siteEndPoint` configurada (sem sufixos automáticos).
**Dica**: Você pode identificar a ação pelo campo `method` no JSON.

#### A. Consultar Saldo
**Request**:
```json
{
    "method": "user_balance",
    "agent_code": "seu_agent_code",
    "agent_secret": "sua_secret_key",
    "user_code": "id_do_usuario"
}
```
**Response Esperada**:
```json
{
    "user_balance": 150.50
}
```

#### B. Transação (Spin)
**Request**:
```json
{
    "method": "transaction",
    "agent_code": "seu_agent_code",
    "agent_secret": "sua_secret_key",
    "user_code": "id_do_usuario",
    "slot": {
        "gameCode": "vs20olympgate",
        "bet": 2.00,
        "win": 5.00,
        "txnId": "unique_txn_id"
    }
}
```
*   **Ação**: Debitar `bet` e creditar `win`.

---

## 5. Resumo de Implementação para o Cliente

Para suportar **todos** os jogos, sua API deve ter 3 rotas principais (assumindo base `https://seusite.com/api/`):

1.  `POST /gold_api/user_balance` (PG - Validação)
2.  `POST /gold_api/game_callback` (PG - Transação)
3.  `POST /` (PP - Saldo e Transação, diferenciados por `method`)

> **Nota**: Você pode configurar URLs bases diferentes para PG e PP se desejar separar a lógica completamente, solicitando ao suporte a configuração distinta nos bancos de dados `api90` e `apipp`.
