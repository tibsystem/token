# Admin Buyback Function Example

The `adminBuyback` function allows the contract owner to transfer tokens from multiple investors back to the owner address. Payments to investors are handled off‑chain via the Laravel backend.

## Solidity Function Signature
```solidity
function adminBuyback(address[] calldata investors, uint256[] calldata amounts) external onlyOwner
```

## Example JSON Payload from Laravel
```json
{
  "contract": "0xContractAddress",
  "method": "adminBuyback",
  "params": {
    "investors": [
      "0xInvestor1",
      "0xInvestor2"
    ],
    "amounts": [
      "1000000000000000000",
      "2000000000000000000"
    ]
  }
}
```

## Pseudocode Call in Laravel
```php
$contract->adminBuyback(
    [$investor1, $investor2],
    [$amount1, $amount2],
    ['from' => $ownerAddress]
);
```

## Backend API Example

Use the following request to execute a buyback from Laravel:

```json
{
  "valor_pago": 25.0
}
```

The `valor_pago` field represents the amount paid per token. The controller
automatically locates all investors who hold tokens for the given property,
credits their internal wallets with `qtd_tokens * valor_pago`, logs the
corresponding financial transactions and marks the property as `vendido`.

The controller credits the investor's internal wallet with the paid amount, logs a `rendimento` entry in `transacoes_financeiras`, and marks the property as `vendido`.

## Testing with Postman

You can manually trigger a buyback using the authenticated admin route:

```
POST /api/admin/imoveis/{id}/buyback
Authorization: Bearer <ADMIN_TOKEN>
Content-Type: application/json

{
  "valor_pago": 25.0
}
```

Replace `{id}` with the property identifier and provide a valid admin bearer token in the `Authorization` header. The response will be:

```json
{ "status": "success" }
```
