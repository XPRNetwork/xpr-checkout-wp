TARGET_NAME		= wookey_woocommerce_webauth_gateway
VERSION			= 0.9.13-beta
BUILD_DIR		=./output
ROOT_FOLDER = ./
FOLDERS			=  $(ROOT_FOLDER)/dist $(ROOT_FOLDER)/includes $(ROOT_FOLDER)/i18n $(ROOT_FOLDER)/wookey_woocommerce_webauth_gateway.php
PACKAGE_NAME	= $(TARGET_NAME)_$(VERSION).zip

compile: 
	make clean
	make prepare
	zip -r $(PACKAGE_NAME) $(ROOT_FOLDER)/$(FOLDERS)
	rm -rf $(BUILD_DIR)

build:
	cd ./assets; npm run build
	
prepare:
	mkdir $(BUILD_DIR)
	cp -r $(FOLDERS) $(BUILD_DIR)

clean:
	rm -rf $(BUILD_DIR) $(PACKAGE_NAME)