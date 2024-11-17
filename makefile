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

compile_apps:
	rm -rf ./dist
	mkdir dist
	cd ./applications/apps/block && rm -rf ./build && bun run build && cd ./../../../dist && mkdir block && cd ../ && cp -r ./applications/apps/block/build/ ./dist/block
	cd ./applications/apps/checkout && rm -rf ./build && bun run build && cd ./../../../dist && mkdir checkout && cd ../ && cp -r ./applications/apps/checkout/build/ ./dist/checkout
	cd ./applications/apps/regstore && rm -rf ./build && bun run build && cd ./../../../dist && mkdir regstore && cd ../ && cp -r ./applications/apps/regstore/build/ ./dist/regstore
	cd ./applications/apps/refund && rm -rf ./build && bun run build && cd ./../../../dist && mkdir refund && cd ../ && cp -r ./applications/apps/refund/build/ ./dist/refund
	