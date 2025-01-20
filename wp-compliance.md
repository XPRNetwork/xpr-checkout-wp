## Not needed parameters in your readme and/or main PHP file.

We have noticed that you have used some unnecessary or wrong parameters in either your readme and/or main PHP files.

We know it's easy to mix up the names of the parameters, and also to mix them up between files,that happens to us as well!.

Please check the documentation for the correct parameters for each file:


For your readme file: Plugin Readmes – Plugin Handbook | Developer.WordPress.org 

For your main PHP file: Header Requirements – Plugin Handbook | Developer.WordPress.org 

Analysis result:

"Requires at least: 6.0" is not needed in your "readme.md" file - Redundant as of WordPress 5.8. Use only the plugin's main PHP file "xprcheckout.php" for this declaration.
"Requires PHP: 7.0" is not needed in your "readme.md" file - Redundant as of WordPress 5.8. Use only the plugin's main PHP file "xprcheckout.php" for this declaration.

## Requires Plugins: found possible dependencies.

The "Requires Plugins" header is a comma-separated list of WordPress.org-formatted slugs for its dependencies.

Does your plugin extend another plugin available in the Blog Tool, Publishing Platform, and CMS - WordPress.org  directory? If so, you can use the "Requires Plugins" header to have WordPress check if that plugin is installed and activated before your plugin is activated.

You can check out the documentation at:Introducing Plugin Dependencies in WordPress 6.5 

We have found that your plugin probably extends another plugin, so using this header may be useful for you, please check it out.

Analysis result:

By checking your plugin slug "xprcheckout-webauth-gateway-for-woocommerce" we found coincidences with "woocommerce"
You can easily solve this by adding or updating the "Requires Plugins" header at "xprcheckout.php", like so:
Requires Plugins: woocommerce

## Use wp_enqueue commands

Your plugin is not correctly including JS and/or CSS. You should be using the built in functions for this:

When includingJavaScript codeyou can use:


wp_register_script() and wp_enqueue_script() to add JavaScript code from a file.

wp_add_inline_script() to add inline JavaScript code to previous declared scripts.

 

When includingCSSyou can use:


wp_register_style() and wp_enqueue_style() to add CSS from a file.

wp_add_inline_style() to add inline CSS to previously declared CSS.

 

Note that as of WordPress 6.3, you can easily pass attributes like defer or async:Registering scripts with `async` and `defer` attributes in WordPress 6.3 

Also, as of WordPress 5.7, you can pass other attributes by using this functions and filters:Introducing script attributes related functions in WordPress 5.7 

If you're trying to enqueue on the admin pages you'll want to use the admin enqueues.

admin_enqueue_scripts – Hook | Developer.WordPress.org 

admin_print_scripts – Hook | Developer.WordPress.org 

admin_print_styles – Hook | Developer.WordPress.org 

 

Example(s) from your plugin:




includes/templates/template-payments.php:17 <script type='text/javascript'>
includes/woocommerce/gateway/xprcheckout-gateway.php:259 <script>
includes/controllers/Refund.php:102 <script>
 

## Undocumented use of a 3rd Party / external service

Plugins are permitted to require theuse of third party/external servicesas long as they areclearly documented.

When your plugin reach out to external services, you must disclose it. This is true even if you are the one providing that service.

You are required to document it in a clear and plain language, so users are aware of:what data is sent, why, where and under which conditions.

To do this, you must update your readme file toclearly explain that your plugin relies on third party/external services, and includeat least the following informationfor each third party/external service that this plugin uses:


What the service is and what it is used for.

What data is sent and when.

Provide links to the service's terms of service and privacy policy.

Remember, this is for your own legal protection. Use of services must be upfront and well documented. This allows users to ensure that any legal issues with data transmissions are covered.

Example:




== External services ==

This plugin connects to an API to obtain weather information, it's needed to show the weather information and forecasts in the included widget.

It sends the user's location every time the widget is loaded (If the location isn't available and/or the user hasn't given their consent, it displays a configurable default location).
This service is provided by "PRT Weather INC": terms of use, privacy policy.
 

 

Example(s) from your plugin:




# Domain(s) not mentioned in the readme file.
includes/rpc/PriceRateRPC.php:25 $url = XPRCHECKOUT_PRICE_RATE_API_ENDPOINT;
# ↳ Detected: https://api.freecurrencyapi.com/v1/latest
includes/rpc/TokensPricesRPC.php:17 $url = "https://www.api.bloks.io/proton/tokens";
includes/rpc/PriceRateRPC.php:30 wp_remote_get($url, array('headers' => $headers, 'body' => array('base_currency' => 'USD')));
# ↳ Found: 'https://api.freecurrencyapi.com/v1/latest'
includes/rpc/TokensPricesRPC.php:19 wp_remote_get($url, array('headers' => array('Accept' => 'application/json', 'Content-Type' => 'application/json'), 'timeout' => 45, 'sslverify' => false));
# ↳ Found: "https://www.api.bloks.io/proton/tokens"
 

 

## Review: Missingpermission_callbackinregister_rest_route().

When usingregister_rest_route()to define custom REST API endpoints, it is crucial to include a properpermission_callback.

Open 72.png
🔒
This callback function ensures that only authorized users can access or modify data through your endpoint.

Code example, checking that the user can change options:




register_rest_route( 'xprcheckout-webauth-gateway-for-woocommerce/v1', '/my-endpoint', array(
    'methods' => 'GET',
    'callback' => 'xprcheckout-webauth-gateway-for-woocommerce_callback_function',
    'permission_callback' => function() {
        return current_user_can( 'manage_options' );
    }
) );
 

Please check theregister_rest_route() documentationand thecurrent_user_can() documentation.

Open 72.png
✅
When apermission_callbackis NOT Required:

There are valid use cases for public endpoints, such as publicly available data (e.g., posts, public metadata) or endpoints designed for unauthenticated access (e.g., fetching public stats or information).

In these cases, you should use__return_trueas thepermission_callbackto indicate that the endpoint is intentionally public.

Open 72.png
🔒
When apermission_callbackIS Required:

For endpoints that involve sensitive data or actions (e.g., getting not public data, creating, updating, or deleting content).

In these cases, you should always implement proper permission checks.

Possible cases found on this plugin's code:




includes/api/verify-transaction.php:12 register_rest_route('xprcheckout/v1', '/verify-settlement', array('methods' => 'POST', 'callback' => 'handle_transaction_check', 'permission_callback' => '__return_true'));
includes/api/order-payment.php:12 register_rest_route('xprcheckout/v2', '/order-payment', array('methods' => 'POST', 'callback' => 'handle_convert_order', 'permission_callback' => '__return_true'));
 

 

## Internationalization: Text domain does not match plugin slug.

In order to make a string translatable in your plugin you are using a set of special functions. These functions collectively are known as "gettext".

These functions havea parameter called "text domain", which is a unique identifier for retrieving translated strings.

This "text domain" must be the same as your plugin slug so that the plugin can be translated by the community using the tools provided by the directory. As for example, if this plugin slug is "xprcheckout-webauth-gateway-for-woocommerce" the Internationalization functions should look like:
esc_html__('Hello', 'xprcheckout-webauth-gateway-for-woocommerce');

From your plugin, you have set your text domain as follows:




# This plugin is using the domain "xprcheckout_webauth_gateway" for 57 element(s).
 

However, the current plugin slug is this:




xprcheckout-webauth-gateway-for-woocommerce
 

 

## Generic function/class/define/namespace/option names

All plugins must have unique function names, namespaces, defines, class and option names. This prevents your plugin from conflicting with other plugins or themes. We need you to update your plugin to use more unique and distinct names.

A good way to do this is with a prefix. For example, if your plugin is called "Easy Custom Post Types" then you could use names like these:


function ecpt_save_post()

class ECPT_Admin{}

namespace ECPT;

update_option( 'ecpt_settings', $settings );

define( 'ECPT_LICENSE', true );

global $ecpt_options;

 

Don't try to use two (2) or three (3) letter prefixes anymore. We host nearly 100-thousand plugins on Blog Tool, Publishing Platform, and CMS - WordPress.org  alone. There are tens of thousands more outside our servers. Believe us, you’re going to run into conflicts.

You also need to avoid the use of __ (double underscores), wp_ , or _ (single underscore) as a prefix. Those are reserved for WordPress itself. You can use them inside your classes, but not as stand-alone function.

Please remember, if you're using _n() or __() for translation, that's fine. We'reonlytalking about functions you've created for your plugin, not the core functions from WordPress. In fact, those core features are why you need to not use those prefixes in your own plugin! You don't want to break WordPress for your users.

Related to this, using if (!function_exists('NAME')) { around all your functions and classes sounds like a great idea until you realize the fatal flaw. If something else has a function with the same name and their code loads first, your plugin will break. Using if-exists should be reserved for shared libraries only.

Remember: Good prefix names are unique and distinct to your plugin. This will help you and the next person in debugging, as well as prevent conflicts.

Analysis result:




# This plugin is using the prefix "xprcheckout" for 33 element(s).

# Cannot use "wc" as a prefix.
includes/supports/block-support.php:4 class WC_XPRCheckoutBlocksSupport
# Cannot use "get" as a prefix.
includes/utils/order-by-payment-key.php:2 function get_order_by_payment_key
# Cannot use "admin" as a prefix.
includes/api/admin-save-wallet.php:49 function admin_only_save_wallet_permission_check
includes/api/admin-refund.php:26 function admin_only_permission_check
# Cannot use "handle" as a prefix.
includes/api/admin-save-wallet.php:67 function handle_save_config_request
includes/api/verify-transaction.php:19 function handle_transaction_check
includes/api/order-payment.php:19 function handle_convert_order
includes/api/admin-refund.php:44 function handle_refund_request
# Cannot use "run" as a prefix.
xprcheckout.php:71 function run_proton_wc_gateway

# Looks like there are elements not using common prefixes.
includes/utils/to-precision.php:2 function toPrecision
includes/utils/symbol.php:2 function symbolFromU64
includes/api/order-payment.php:106 function convertedTokenFactory
includes/rpc/ProtonRPC.php:2 class ProtonRPC
includes/rpc/PriceRateRPC.php:3 class PriceRateRPC
includes/rpc/TokensPricesRPC.php:3 class TokenPrices
includes/xprcheckout-gateway.core.php:2 class ProtonWcGateway
xprcheckout.php:38 $jal_db_version;
xprcheckout.php:83 function sample_admin_notice_success
 

 

## Allowing Direct File Access to plugin files

Direct file access occurs when someone directly queries a PHP file. This can be done by entering the complete path to the file in the browser's URL bar or by sending a POST request directly to the file.

For files that only contain class or function definitions, the risk of something funky happening when accessed directly is minimal. However, for files that contain executable code (e.g., function calls, class instance creation, class method calls, or inclusion of other PHP files), the risk of security issues is hard to predict because it depends on the specific case, but it can exist and it can be high.

You can easily prevent this by adding the following code at the top of all PHP files that could potentially execute code if accessed directly:




    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 

Example(s) from your plugin:




xprcheckout.php:15 
includes/templates/template-payments.php:5 
 