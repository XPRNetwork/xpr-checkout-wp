=== XPRCheckout - WebAuth Gateway for e-commerce ===
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.0
Stable tag: ##VERSION_TAG##
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

# XPRCheckout - WebAuth Gateway for e-commerce
*A WebAuth-Enabled Gateway for e-commerce*

## The basic scope

This plugin provides a payment gateway for WooCommerce that enables users to pay for their purchases using any cryptocurrency available in the WebAuth wallet. With this feature, users can enjoy a seamless and secure shopping experience, thank Proton, as they can easily pay for their purchases using their preferred digital currency.

XPR Checkout, through a hassle-free configuration, provides additional features to help store owners manage payment reconciliation, withdrawal, and refund inside the WooCommerce regular flow also driven by Webauth authentication. 

Overall, this plugin helps to expand the use of cryptocurrencies through the proton chain in e-commerce, making it easier and more convenient for users to use their digital assets for online shopping.

## External Services

This plugin connects to several external services to provide its functionality:

1. **FreeCurrencyAPI**
   - Service: Used to obtain real-time currency exchange rates
   - Data sent: Base currency (USD) for rate conversion
   - Endpoint: https://api.freecurrencyapi.com/v1/latest
   - Terms of Service: https://freecurrencyapi.com/terms
   - Privacy Policy: https://freecurrencyapi.com/privacy

2. **Bloks.io API**
   - Service: Used to fetch Proton token information and prices
   - Data sent: None (GET request only)
   - Endpoint: https://www.api.bloks.io/proton/tokens
   - Terms of Service: https://bloks.io/terms
   - Privacy Policy: https://bloks.io/privacy

3. **RockerOne API**
   - Service: Used for Proton blockchain interaction
   - Data sent: Transaction data for payments and refunds
   - Endpoints: 
     - Mainnet: https://api.rockerone.io
     - Testnet: https://testnet.rockerone.io
   - Terms of Service: https://rockerone.io/terms
   - Privacy Policy: https://rockerone.io/privacy

4. **XPR Network Explorer**
   - Service: Used to view transaction details
   - Data sent: None (only used for viewing transaction data)
   - Endpoints:
     - Mainnet: https://explorer.xprnetwork.org
     - Testnet: https://testnet.explorer.xprnetwork.org
   - Terms of Service: https://xprnetwork.org/terms
   - Privacy Policy: https://xprnetwork.org/privacy

All data transmission to these services is necessary for the proper functioning of the payment gateway. The plugin requires these services to:
- Convert currencies and provide accurate pricing
- Verify transactions on the blockchain
- Enable secure payment processing
- Provide transaction tracking and verification

## Code structure

The gateway itself follows the rules of each WooCommerce gateway. It binds the WooCommerce and WordPress event flow to functions through the use of hooks and filters that trigger methods from the **XPRCheckout_Gateway** class.

The front-end checkout widget and back-office utility widgets run exclusively on JS, composed of several apps compiled independently but sharing the same code base. 

Additionally, some API endpoints provide services abstraction like 

- real-time tokens prices in USD
- checkout amount in store currency to USD
- payment validation endpoint that consumes RPC curl call on the smart contract tables

The apps are compiled through a Vite config, and the distribution package (WordPress ready-to-install .zip file) is made by a make file automation.

The smart contract is outside this code structure as it uses a totally different compiler. 

### Front-end

The front end is a simple Svelte application that wraps the proton-web-SDK and is synced with the WebAuth authentication flow. After the user authorizes the connection with the Store dApp, it provides the total price of the checkout declined on a list of authorized tokens whose amount is calculated in real-time through prices services API. Thus, the user can select which token with he wants to pay to checkout amount.

Once the user has chosen the token, two actions are pushed through his session, a payment registration on the XPR Checkout smart contract, and a transfer action to the token contract. Booth actions are tied together by a unique checksum SHA256 key, generated for the WooCommerce order object and stored in the WooCommerce order meta. 

After those two action return with no error,  the application moves to a new view and uses the payment validation API endpoint to verify two things:  

- if the order checksum SHA256 key match the payment registration key
- if the transfer transaction memo is equal to the same checksum SHA256 key.

If both checks are validated, the order is marked as complete in WooCommerce. 

### Smart contract

To ease the pain of a bulky installation and configuration from the store owner, XPR Checkout provides a smart contract that handles the process for all stores in one place :

The core of the smart contract is the following actions: 

1. **Payment Registration (pay.reg)** 
That registers the checksum SHA256 key (generated by the XPR Checkout gateway at order creation) amongst a token amount. When registered, the payment is flagged as "AWAIT". 
2. **Transfer tokens notification actions (transfer)**
The transfer memo must contain the same checksum SHA256 key provided to the **Payment Registration.** Thus the contract can find the payment, validate both the token and amount that have to be paid, and finally flag the payment as "PAID".

It also provides other actions to manage stores, withdrawals, and refunds:

- **Store registration (store.reg)**
Register a store to allow payment and multi-balance storage (for different tokens).
- **Store unregistration (store.unreg)**
Remove a store from the store list, but keep the balance stored.

- **Refund by the store owner (pay.refund)**
Allow the refund of payment by flagging flag the payment as "REFUNDED".
- **No more Withdraw of payments by the store owner (bal.claim)**
No need to withdraw payment are directly transferred to the store owner through the @xprckechout smart contract.