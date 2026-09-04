[<](../index.md) Altapay - PHP Api - Card wallet authorize
================================================================

This step finalizes the Card Wallet payment by authorizing the previously registered payment.

The payment is registered beforehand with [card wallet session](card_wallet_session.md).

- [Request](#request)
    + [Required](#required)
    + [Optional](#optional)
    + [Example](#example)
- [Response](#response)

# Request

```php
$request = new \Altapay\Api\Payments\CardWalletAuthorize($auth);
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
| setProviderData(string) | The payment token of the wallet, passed on as the wallet provided it. | string
| setTerminal(string) | The title of your terminal<br/>It is also possible to pass in a terminal name with the currency as wildcard. Ex. 'My {currency} Terminal' would use the 'My EUR Terminal' if you also pass EUR along as the currency. | string
| setShopOrderId(string) | The id of the order in your webshop. This is what we will post back to you so you know which order a given payment is associated with. | [a-zA-Z0-9]{1,100} |
| setAmount(float) | The amount of the payment in english notation (ex. 89.95)<br />For a subscription the amount is the default amount for each capture.<br />Amount is limited to 2 decimals, an error will be returned if more decimals is supplied. | int, float
| setCurrency(string) | The currency of the payment in ISO-4217 format. Either the 3-digit numeric code, or the 3-letter character code. | string, int - [See currencies](../types/currencies.md)

### Optional

| Method  | Description | Type |
|---|---|---|
| setPaymentId(string) | The `payment_id` of the payment the wallet session registered. | string
| setSessionId(string) | The `session_id` the payment was registered with, required by terminals that expect an external session id. | string

The optional parameters of [payment request](../ecommerce/payment_request.md) apply here as well.

### Example

```php
$request = new \Altapay\Api\Payments\CardWalletAuthorize($auth);
$request->setTerminal('my terminal');
$request->setShopOrderId('123456');
$request->setAmount(200.45);
$request->setCurrency('SEK');
$request->setProviderData($walletToken);
$request->setSessionId('session id');
$request->setPaymentId('payment id');
```

# Response

Object of `\Altapay\Response\PaymentRequestResponse`

| Method  | Description | Type |
|---|---|---|
| `$response->Result` | | string
| `$response->Transactions` | | array
| `$response->RedirectResponse` | | `\Altapay\Response\Embeds\InitiatePaymentRedirectResponse`

A result of `Redirect` means the payment has to be continued somewhere else before it is authorized, for instance to authenticate the cardholder with 3D Secure. This happens for the cards a wallet provides without a cryptogram.

### `\Altapay\Response\Embeds\InitiatePaymentRedirectResponse`

| Method  | Description | Type |
|---|---|---|
| `$object->Url` | | string
| `$object->Method` | | string
| `$object->Data` | | array
| `$object->FlowType` | | string
