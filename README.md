# wookey-woocommerce-webauth
 
This plugin provides a payment gateway for WooCommerce that enables users to pay for their purchases using any cryptocurrency available in the WebAuth wallet. With this feature, users can enjoy a seamless and secure shopping experience, thank Proton, as they can easily pay for their purchases using their preferred digital currency.

Wookey, through a hassle-free configuration, provides additional features to help store owners manage payment reconciliation, withdrawal, and refund inside the WooCommerce regular flow also driven by Webauth authentification. 

Overall, this plugin helps to expand the use of cryptocurrencies through the proton chain in e-commerce, making it easier and more convenient for users to use their digital assets for online shopping.

## Pre-installation
1. First be sure that WooCommerce is installed and activated on your WordPress web site. 
2. Be sure to use the "Post Name" structure in settings > Permalinks

## Intallation
This is a good'ol wordpress plugin. 

1. Download the latest stable version from the github [release](https://github.com/ProtonProtocol/wookey-woocommerce-webauth/releases) section
2. Login to your WordPress admin.
<img width="1435" alt="Capture d’écran 2023-08-17 à 09 42 49" src="https://github.com/ProtonProtocol/wookey-woocommerce-webauth/assets/1812457/925d76cb-d314-4c7a-872b-be3db5018ac9">

3. Go to plugin > add new.
<img width="1427" alt="Capture d’écran 2023-08-17 à 09 33 57" src="https://github.com/ProtonProtocol/wookey-woocommerce-webauth/assets/1812457/03f07cde-23cb-4ba8-9f7c-a8a9c5149103">

4. Click on the "Upload Plugin" for the top of the page.
<img width="1439" alt="Capture d’écran 2023-08-17 à 09 34 18" src="https://github.com/ProtonProtocol/wookey-woocommerce-webauth/assets/1812457/cadc2706-37a7-4252-9d55-a9a060dede03">

5. Then click on the "Select a file"'s button. From your file system dialog, locate the ZIP file downloaded from Step 1, select it, and click open.
<img width="1439" alt="Capture d’écran 2023-08-17 à 09 34 47" src="https://github.com/ProtonProtocol/wookey-woocommerce-webauth/assets/1812457/c546f9c7-a7c8-4f49-87ea-3921353b912f">

6. Once it's done click install now.
<img width="1438" alt="Capture d’écran 2023-08-17 à 09 45 02" src="https://github.com/ProtonProtocol/wookey-woocommerce-webauth/assets/1812457/8f0dc3f5-fa8d-449b-9aba-1a99d72ae70c">

7. Once WordPress finishes the installation process, click the "Activate now" button
<img width="1431" alt="Capture d’écran 2023-08-17 à 09 58 29" src="https://github.com/ProtonProtocol/wookey-woocommerce-webauth/assets/1812457/6e5fe4cd-1519-44e2-ad7e-9092b5e1639d">

8. Done, congratulation! Grab a coffee, you deserve it.

## Pre-Setup
The Wookey payment gateway use a escrow smart contract to ease the daily basis store management. 
In order to allow your store to work with the Wookey smart contract you have to register an account with the smart contract.  
The Wookey escrow contract is in charge for
- receive and reconciliate payment
- Keep track of the store balances per token
- Allow store to withdraw his balances 
- Refund non withdrawn payment

**We strongly recommend you to create a dedicated proton account for your store before register it.**

## Setup
1. Go to Woocommerce > settings
2. Choose the tab "Payments"
3. Click on the "Manage"'s button on the "WebAuth for WooCommerce" row.
4. Enable the gateway 
5. If you want to test the plugin, activate testnet. Unchecked mean mainnet.
6. Register your proton store account with the Wookey smart contract.
7. Gives the payment method a title, it will appear on the checkout page, where user choose his payment method.
8. Give a description to the payment method, it will appear on the checkout page, where user choose his payment method.
9. Provide the list of accepted tokens for payment
10. Leave the Free API key empty for now 
11. Your user is now able to pay with any crypto you've allowed on your store ! 
12. Done, congratulation! Grab another coffee, you deserve it. 

