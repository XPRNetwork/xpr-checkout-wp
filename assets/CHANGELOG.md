# Wookey

## 1.0.7

### Patch Changes

- Update version, hide "place order" button

## 1.0.6

### Patch Changes

- Add support for external woocommerce checkout validation form

## 1.0.5

### Patch Changes

- Add token filter feature based on available user balance to a…
- Clean proton endpoint
- Add @proton/light-api to handle user balances request
- Fix modal cycle when user cancel proton sign from payment…

## 1.0.4

### Patch Changes

- Update plugin semver
- from XPRNetwork/fix-include-paths
- Fix import paths in wookey-gateway.core.php.
- Remove old php tag opening

## 1.0.3

### Patch Changes

- Explore a way for better naming
- Change basic information about version and Author URI
- Clean up makefile
- Add better types for api across ts apps
- remove unused return fields in includes/api/cart.php
- Include new gateway classes structure to the wookey-gateway.core.php.…
- Clean dead code. Split includes/woocommerce/gateway/wookey-gateway.ph…
- Include controller/Config.php in the wookey-gateway.core
- Reflect the new Config structure to common/type.d.ts
- Externalize Config used accros wook-gateway.php for code reuse and be…
- Reflect changes of the new payment flow to the public payment app
- Adding service for cart update
- Reflect payment flow change to ts types files. Better typing of the V…
- WIP Update payment flow by moving it to the checkout page
- Adding make file for wordpress plugin distribution
- CHORE: update build process and vite config
- Adding processing gif for payment verification dialog
- Adding dashboard app
