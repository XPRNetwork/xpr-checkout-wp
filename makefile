TARGET_NAME		= woow_woocommerce_webauth_gateway
VERSION			= 1_0
BUILD_DIR		=./output
ROOT_FOLDER = ./
FOLDERS			=  $(ROOT_FOLDER)/dist $(ROOT_FOLDER)/includes $(ROOT_FOLDER)/proton-wc-gateway.php
PACKAGE_NAME	= $(TARGET_NAME).zip

compile: 
	make clean
	make prepare
	zip -r $(PACKAGE_NAME) $(ROOT_FOLDER)/$(FOLDERS)
	rm -rf $(BUILD_DIR)

prepare:
	mkdir $(BUILD_DIR)
	cp -r $(FOLDERS) $(BUILD_DIR)

clean:
	rm -rf $(BUILD_DIR) $(PACKAGE_NAME)