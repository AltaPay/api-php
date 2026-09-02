[<](../index.md) Altapay - PHP Api - Card wallet session
================================================================

This endpoint initiates a new payment using the Card Wallet flow and retrieves merchant session data.

The payment is authorized afterwards with [card wallet authorize](card_wallet_authorize.md).

- [Request](#request)
    + [Required](#required)
    + [Optional](#optional)
    + [Example](#example)
- [Response](#response)

# Request

```php
$request = new \Altapay\Api\Payments\CardWalletSession($auth);
// Do the call
try {
    $response = $request->call();
    // See Response below
} catch (\Altapay\Exceptions\ClientException $e) {
    // Could not connect
} catch (\Altapay\Exceptions\ResponseHeaderException $e) {
    // Response error in header
    $e->getHeader()->ErrorMessage
} catch (\Altapay\Exceptions\ResponseMessageException $e) {
    // Error message
    $e->getMessage();
}
```

### Required

| Method  | Description | Type |
|---|---|---|
| setTerminal(string) | The title of your terminal<br/>It is also possible to pass in a terminal name with the currency as wildcard. Ex. 'My {currency} Terminal' would use the 'My EUR Terminal' if you also pass EUR along as the currency. | string
| setShopOrderId(string) | The id of the order in your webshop. This is what we will post back to you so you know which order a given payment is associated with. | [a-zA-Z0-9]{1,100} |
| setAmount(float) | The amount of the payment in english notation (ex. 89.95)<br />For a subscription the amount is the default amount for each capture.<br />Amount is limited to 2 decimals, an error will be returned if more decimals is supplied. | int, float
| setCurrency(string) | The currency of the payment in ISO-4217 format. Either the 3-digit numeric code, or the 3-letter character code. | string, int - [See currencies](../types/currencies.md)

### Optional

| Method  | Description | Type |
|---|---|---|
| setSessionId(string) | The `session_id` the payment is registered with, required by terminals that expect an external session id. | string
| setApplePayRequestData(array) | The `validationUrl` and `domain` of the merchant validation. Apple Pay only. | array
| setValidationUrl(string) | The validation url of the event. Apple Pay only, of the legacy flow. | string
| setDomain(string) | The domain initiating the request. Apple Pay only, of the legacy flow. | string

The optional parameters of [payment request](../ecommerce/payment_request.md) apply here as well.

Google Pay requires no provider specific parameters here, as it is configured on the frontend.

### Example

```php
$request = new \Altapay\Api\Payments\CardWalletSession($auth);
$request->setTerminal('my terminal');
$request->setShopOrderId('123456');
$request->setAmount(200.45);
$request->setCurrency('SEK');
$request->setSessionId('session id');
```

# Response

Object of `\Altapay\Response\CardWalletSessionResponse`

| Method | Type |
|---|---|
| `$response->Result` | string
| `$response->Transactions` | array
| `$response->WalletData` | `\Altapay\Response\Embeds\WalletData`
| `$response->ApplePaySession` | string

### `\Altapay\Response\Embeds\WalletData`

| Method | Type |
|---|---|
| `$object->Session` | string
