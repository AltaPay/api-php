# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.5.3] - 2025-08-12
### Added
- Rename `AuthenticationResult` property to `Authentication` in `Transaction` class.

## [3.5.2] - 2025-08-08
### Added
- Correct `Transaction` `CreditCardExpiry` type.

## [3.5.1] - 2025-06-11
### Added
- Improve type hinting.
- Improve return type annotations for better type clarity.
- Improve error handling by validating error message types.
- Add null check before setting the User-Agent header.
- Restrict supported PHP versions to 7.0 and above.

## [3.5.0] - 2025-05-12
### Added
- Make billing and shipping address optional for Customer Info

## [3.4.9] - 2025-04-22
### Added
- Provide `setCustomerCreatedDate` method to set `extra_merchant_data` for the `createPaymentRequest` callback config.
- Include `AuthorisationExpiryDate` in `Transaction` class.

## [3.4.8] - 2025-03-13
### Added
- Provide `setClientJavaEnabled` and `setClientColorDepth` methods to set `customer_info[client_java_enabled]` and `customer_info[client_color_depth]` for the customer object.

## [3.4.7] - 2025-02-06
### Added
- Provide `setSelectedScheme` method to set `selected_scheme` for `initiatePayment`.
- Added the `SelectedSchemes` class to define allowed values for `setSelectedScheme` method.

## [3.4.6] - 2025-01-14
### Added
- Include `AuthenticationResult` in `Transaction` class.

## [3.4.5] - 2024-11-05
### Added
- Include `MerchantErrorMessage`, `CardHolderErrorMessage`, `CardHolderErrorMessage` and `CardHolderErrorMessage` in `CaptureReservationResponse` class.

## [3.4.4] - 2024-10-15
### Added
- Add support for Processor API's `initiatePayment` and `verify3dSecure` method.
- Add support for additional languages.

## [3.4.3] - 2024-10-01
### Added
- Validate gateway XML response for login API.
- Fix: `PHP Warning:  Invalid argument supplied for foreach()` in `ResponseSerializer.php`
- Provide `setLanguage` method to set `language` for `reservation` API.
- Fix: Redundant assigning of shopOrderId in Callback.

## [3.4.2] - 2024-08-07
### Added
- Add support for symfony 7.

## [3.4.1] - 2024-07-09
### Added
- Extend support to include all languages that the gateway supports.

## [3.4.0] - 2024-06-04
### Added
- Add support for `updateOrder/{id}/reconciliationIdentifier` API

## [3.3.9] - 2024-02-19
### Added
- Provide `setCallbackMobileAppRedirect` method to set `config[callback_mobile_app_redirect]` for the `createPaymentRequest` callback config.
- Added new property `AppUrl` for `PaymentRequestResponse` & `ReservationOfFixedAmountResponse` classes.
- Add new properties `MerchantId`, `ShopName`, `LogoUrl`, `Description`, `Products`, `PrimaryMethod`, `CanUseCredit`, `CanIssueNewCredit` for `Terminal` class. 

## [3.3.8] - 2023-12-04
### Added
- Provide `setCardHolderName` method to set `customer_info[cardholder_name]` for the customer object.

## [3.3.7] - 2023-12-04
### Added
- Add `@throws` annotations in the PHPDoc blocks throughout the codebase.

## [3.3.6] - 2023-11-07
### Fixes
- Remove the restrictions in the `shop_orderid`,

## [3.3.5] - 2023-05-23
### Fixes
- Add option to use `transaction_info` and `orderLines` for `ChargeSubscription`

## [3.3.4] - 2023-05-12
### Fixes
- Add missing setters for the Address class

## [3.3.3] - 2023-05-10
### Fixes
- RedirectResponse URL is not returned for ChargeSubscription response

## [3.3.2] - 2023-04-20
### Added
- Enforce the right HTTP methods on all API endpoints.

## [3.3.1] - 2023-04-06
### Added
- Add `digital`, `discount`, `gift_card`, `physical` and `sales_tax` as a new goods types for OrderLine class

## [3.3.0] - 2023-03-15
### Added
- Add config parameter in the reservation API
- Support redirect url in reservation API

## [3.2.9] - 2023-03-03
### Added
- Add support for updateOrder API

## [3.2.8] - 2022-11-01
### Added
- Supports API changes from 20221026
- Add agreement setters for ChargeSubscription and ReserveSubscriptionCharge class
- Add additional parameters for ReservationOfFixedAmount class
- Add new property and setters for Terminal class
- Add `subscription_model` as a new goods type for order line 
### Fixes
- Avoid throwing exception for missing setters or properties in response

## [3.2.7] - 2022-10-04
### Added
- Add support for new 'Agreements Engine' parameters

## [3.2.6] - 2022-08-22
### Fixes
- Add Api authentication classes

## [3.2.5] - 2022-06-14
### Fixes
- Add missing setters for ApplePay
- Fix broken integration tests

## [3.2.4] - 2022-06-14
### Added
- Add PreauthAmount to ReserveSubscriptionResponse
- Add missing properties from forks

## [3.2.3] - 2022-06-14
### Added
- Added CardInformation parameters for Transaction class

## [3.2.2] - 2022-04-14
### Added
- Added optional parameters for CardWalletAuthorize class
- Added setter for Transaction data 

## [3.2.1] - 2022-04-04
### Added
- Added support for the new endpoints for Apple Pay

## [3.2.0] - 2022-03-01
### Added
- Added User-Agent in API call headers
- Added new properties for ReleaseReservationResponse class
- Added new property and setter for ChargeSubscriptionResponse class
- Added support for upto PHP 8.1

## [3.1.1] - 2020-11-04
### Added
- Added missing setters
### Fixes
- Correctly parse boolean parameters

## [3.1.0] - 2020-10-19
### Added
- Support for installation and autoload via composer
- Document all function arguments
- Added DynamicJavascriptUrl on PensioCreatePaymentRequest
- The previous exception is now forwarded

### Changed
- Split ResponseSerializer serialize() and serializeChildren()

### Fixes
- Better handle invalid responses
- Corrected remaining warning messages
- Corrected all types in code DocBlocks
- Correctly handle non-US-ASCII symbols
- Make sure all exceptions have a message

### Removed
- The definedFilters property

## [3.0.0] - 2020-09-30
**Changes**
- Rebranded back to AltaPay
- Corrected dependencies
- ResponseInfo::getRegisteredAddress() will now return null when no address was set
- Package content has been reduced in size

**Fixes**
- Errors will now be Altapay\Exceptions\ClientException instead of Guzzle exceptions
- Corrected some warning messages
- Corrected some types in code DocBlocks

## [2.1.0] - 2020-06-04
**Added**
- Klarna Payments parameters to the Create payment request, and the Capture and refund
- Parse more credit card details

**Changed**
- Removed the restriction where "taxPercent" and "taxAmount" could be used
- Set the POST method as default
- Update code style

## [2.0.0] - 2019-12-19
**Changed**
- Rebranding from AltaPay to Valitor

## [1.8.0] - 2019-08-30
**Added**
- Add merchant error message in the release reservation response

**Fixed**
- Norwegian language not always automatically converted

## [1.7.0] - 2019-07-26
**Change**
- Update the allowed languages

**Fixes**
 - Make payment requests as POST requests by default

## [1.6.3] - 2018-11-21
**Change**
- Update the allowed languages

## [1.6.2] - 2018-09-13
**Added**
- Setup subscription changes

## [1.6.1] - 2018-09-13
**Changes**
- Allow changing type on a setup subscription

## [1.6.0] - 2018-06-16
**Added**
- IsTokenized field to the Transaction class

**Fixes**
- Compatibility with PHP 7.2

## [1.5.0] - 2018-02-14
**Added**
- PaymentSource field

**Fixes**
- Handle the decline and error cases when Refund issued

## [1.4.0] - 2017-03-30
**Changes**
- Improve Exception class

## [1.3.0] - 2017-03-22
**Changes**
- Throws the error if capture fails

## [1.2.0] - 2016-12-27
**Added**
- Factory class

**Fixes**
- Handled warnings in Callback class

## [1.1.0] - 2016-07-06
**Changes**
- It is no longer necessary to provide "/merchant" to the URL constructor

## [1.0.0] - 2016-06-16
**Added**
- The first release, see the documentation for the full feature list
