define MANIFEST_BODY
{
	"name" : "XPRCheckout: WooCommerce WebAuth gateway",
	"slug" : "xprcheckout_woocommerce_webauth_gateway",
	"author" : "<a href='https://rockerone.io'>RockerOne</a>",
	"author_profile" : "http://profiles.wordpress.org/rocker0ne",
	"version" : "$(VERSION)",
	"download_url" : "https://github.com/XPRNetwork/xpr-checkout-wp/releases/tag/$(VERSION)",
	"requires" : "5.0",
	"tested" : "5.0",
	"requires_php" : "7.0",
	"last_updated" : "$(shell date +"%Y-%m-%-d %H:%M:%S")",
	"sections" : {
		"description" : "A WebAuth wallet enabled Gateway for WooCommerce",
		"installation" : "See the github repo home page",
		"changelog" : "See commit on main branch"
	},
	"banners" : {
		"low" : "",
		"high" : ""
	}
}
endef
export MANIFEST_BODY

generate_manifest:
	@echo "$$MANIFEST_BODY" > info.json