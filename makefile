GITHUB_REPO := https://github.com/XPRNetwork/xpr-checkout-wp
FILES := README.md info.json xprcheckout_gateway.php  # List your files here

LATEST_TAG := $(shell git ls-remote --tags $(GITHUB_REPO) | awk -F/ '{print $$NF}' | grep -v '{}' | sort -V | tail -n1)

define MANIFEST_BODY
{
	"name" : "XPRCheckout: WooCommerce WebAuth gateway",
	"slug" : "xprcheckout_woocommerce_webauth_gateway",
	"author" : "<a href='https://rockerone.io'>RockerOne</a>",
	"author_profile" : "http://profiles.wordpress.org/rocker0ne",
	"version" : "$(LATEST_TAG)",
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



.PHONY: update_version
update_version:
	@if [ -z "$(LATEST_TAG)" ]; then \
		echo "No tag found in the repository"; \
		exit 1; \
	fi
	@echo "Replacing ##VERSION_TAG## with the latest tag: $(LATEST_TAG)"
	@for file in $(FILES); do \
		sed -i '' -e "s/##VERSION_TAG##/$(LATEST_TAG)/g" $$file; \
		echo "Updated $$file"; \
	done

compile_apps:
	rm -rf ./dist
	mkdir dist
	cd ./applications/apps/block && rm -rf ./build && bun run build && cd ./../../../dist && mkdir block && cd ../ && cp -r ./applications/apps/block/build ./dist/block
	cd ./applications/apps/checkout && rm -rf ./build && bun run build && cd ./../../../dist && mkdir checkout && cd ../ && cp -r ./applications/apps/checkout/build ./dist/checkout
	cd ./applications/apps/regstore && rm -rf ./build && bun run build && cd ./../../../dist && mkdir regstore && cd ../ && cp -r ./applications/apps/regstore/build ./dist/regstore
	cd ./applications/apps/refund && rm -rf ./build && bun run build && cd ./../../../dist && mkdir refund && cd ../ && cp -r ./applications/apps/refund/build ./dist/refund
	