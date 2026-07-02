# Documentação da API Unificada Stell Games

Esta documentação descreve como integrar com a API unificada da Stell Games. Agora, tanto os jogos da PG quanto os jogos da PP podem ser acessados através de um único domínio base.

**Base URL:** `https://api.stellgames.com`

---

## 1. Jogos PG (PG Soft)

Os endpoints para jogos PG permanecem na raiz da API (Node.js).

### Endpoints Principais

- **Lançar Jogo (Game Launch)**
  - **URL:** `/api/v1/game_launch`
  - **Método:** POST
  - **Exemplo de Payload:**
    ```json
    {
      "agent_code": "seu_codigo",
      "agent_token": "seu_token",
      "user_code": "user123",
      "game_code": "fortune-tiger",
      "language": "pt"
    }
    ```

- **Criar Agente**
  - **URL:** `/api/v1/createagent`
  - **Método:** POST

- **Consultar Agente**
  - **URL:** `/api/v1/getagent`
  - **Método:** POST

---

## 2. Jogos PP (Pragmatic Play / Outros)

Os endpoints para jogos PP agora são acessados através do prefixo `/pp`. O sistema funciona como um proxy reverso que redireciona internamente para a aplicação correta.

**Atenção:** Todas as requisições para PP devem ser enviadas para o endpoint base `/pp/` com o parâmetro `method` especificando a ação desejada.

### Endpoint Único

- **URL:** `/pp/`
- **Método:** POST
- **Headers:** `Content-Type: application/json`

### Estrutura do Payload (Corpo da Requisição)

Todos os métodos exigem `agent_code`, `agent_token` e `method`.

#### Lançar Jogo (Game Launch)
```json
{
  "method": "game_launch",
  "agent_code": "seu_codigo",
  "agent_token": "seu_token",
  "user_code": "user123",
  "game_code": "vs20olympgate",
  "lang": "pt"
}
```

#### Criar Usuário (User Create)
```json
{
  "method": "user_create",
  "agent_code": "seu_codigo",
  "agent_token": "seu_token",
  "user_code": "user123"
}
```

#### Listar Jogos (Game List)
```json
{
  "method": "game_list",
  "agent_code": "seu_codigo",
  "agent_token": "seu_token",
  "provider_code": "PRAGMATIC"
}
```

### Outros Métodos Disponíveis (PP)
- `user_deposit`
- `user_withdraw`
- `money_info`
- `provider_list`
- `get_game_log`

---

## Resumo da Mudança

| Serviço | URL Antiga (Descontinuada) | **Nova URL Unificada** |
| :--- | :--- | :--- |
| **PG API** | `https://api.stellgames.com` | `https://api.stellgames.com` (Sem alteração) |
| **PP API** | `https://apipp.stellgames.com` | `https://api.stellgames.com/pp/` |

**Nota:** Certifique-se de que suas integrações para PP agora apontem para `https://api.stellgames.com/pp/`.
