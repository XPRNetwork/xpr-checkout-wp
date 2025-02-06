/**
 * @fileoverview WooCommerce payment block integration for XPRCheckout
 */

import React from "react";
import type { WCBlocksRegistry, WCSettings, WPHtmlEntities } from "./global";

// Ensure TypeScript understands window properties
declare global {
  interface Window {
    wc: {
      wcBlocksRegistry: WCBlocksRegistry;
      wcSettings: WCSettings;
    };
    wp: {
      htmlEntities: WPHtmlEntities;
    };
  }
}

/**
 * @description Extract core functions from WooCommerce and WordPress globals
 */
const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
const { decodeEntities } = window.wp.htmlEntities;
const { getSetting } = window.wc.wcSettings;

// Retrieve settings and define the label
const settings = getSetting("xprcheckout_data", {}) as {
  description?: string;
  supports?: any;
};
const label = decodeEntities("XPRCheckout");

/**
 * @component Content
 * @description Renders the payment method description from XPRCheckout settings
 * @returns {JSX.Element} React component displaying the payment description
 */
const Content: React.FC = () => {
  return <>{decodeEntities(settings.description || "")}</>;
};

/**
 * @component Label
 * @description Renders the payment method label using WooCommerce components
 * @param {Object} props - Component properties
 * @param {Object} props.components - WooCommerce provided components
 * @param {React.FC<{text: string}>} props.components.PaymentMethodLabel - Label component from WooCommerce
 * @returns {JSX.Element} React component displaying the payment method label
 */
const Label: React.FC<{ components: { PaymentMethodLabel: React.FC<{ text: string }> } }> = (props) => {
  const { PaymentMethodLabel } = props.components;
  return <PaymentMethodLabel text={label} />;
};

/**
 * @description Register XPRCheckout as a payment method in WooCommerce Blocks
 * @param {Object} config - Payment method configuration object
 */
registerPaymentMethod({
  name: "xprcheckout",
  label: <Label components={{ PaymentMethodLabel: ({ text }) => <span>{text}</span> }} />,
  content: <Content />,
  edit: <Content />,
  canMakePayment: () => true,
  ariaLabel: label.toString(),
  supports: {
    features: settings.supports,
  },
});
