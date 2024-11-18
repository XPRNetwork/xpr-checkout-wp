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

// Extract functions and settings from the global objects
const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
const { decodeEntities } = window.wp.htmlEntities;
const { getSetting } = window.wc.wcSettings;

// Retrieve settings and define the label
const settings = getSetting("xprcheckout_data", {}) as {
  description?: string;
  supports?: any;
};
const label = decodeEntities("XPRCheckout");

// Define Content component with settings description
const Content: React.FC = () => {
  return <>{decodeEntities(settings.description || "")}</>;
};

// Define Label component to display the payment method label
const Label: React.FC<{ components: { PaymentMethodLabel: React.FC<{ text: string }> } }> = (props) => {
  const { PaymentMethodLabel } = props.components;
  return <PaymentMethodLabel text={label} />;
};

// Register the payment method with TypeScript typing
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
