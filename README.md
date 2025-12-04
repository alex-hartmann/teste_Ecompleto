# 🔗 Integração de Pagamentos (Laravel + API Externa)

Este projeto tem como objetivo **integrar pedidos existentes** com um intermediador de pagamentos via API.  
Ele não realiza cadastros ou registros de clientes/pedidos, apenas consome dados já existentes no banco e envia para a API de pagamento.

---

## 🚀 Funcionalidades

- Busca pedidos no banco de dados que estejam pendentes de pagamento
- Monta a requisição com dados de pedido, cliente e cartão
- Envia a transação para a API externa
- Atualiza a situação do pedido conforme o retorno da API
- Armazena o retorno do intermediador para auditoria

---

## 📡 Fluxo de Integração

1. O sistema identifica pedidos com situação `id_situacao = 1` e forma de pagamento `id_formapagto = 3`.
2. Monta o corpo da requisição com os dados necessários:
   - `amount` (double)
   - `card_number` (string, apenas dígitos)
   - `card_cvv` (string)
   - `card_expiration_date` (string no formato `MMYY`)
   - `card_holder_name` (string)
   - Dados básicos do cliente
3. Envia para a API externa via `Http::post`.
4. Recebe o retorno (`Transaction_code`) e atualiza o status do pedido:
   - `00` → Pago
   - `01` → Em análise
   - `02`, `03`, `04` → Cancelado
5. Salva o retorno no campo `retorno_intermediador`.

---

## 🛠️ Tecnologias Utilizadas

- [Laravel](https://laravel.com/) 10.x
- [MySQL](https://www.mysql.com/)
- [Composer](https://getcomposer.org/)
