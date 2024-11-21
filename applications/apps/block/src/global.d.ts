export interface WCBlocksRegistry {
  registerPaymentMethod: (config: PaymentMethodConfig) => void;
}

export interface WPHtmlEntities {
  decodeEntities: (input: string) => string;
}

export interface WCSettings {
  getSetting: (name: string, defaultValue?: any) => any;
}

export interface PaymentMethodConfig {
  name: string;
  label: JSX.Element;
  content: JSX.Element;
  edit: JSX.Element;
  canMakePayment: () => boolean;
  ariaLabel: string;
  supports: {
    features: any;
  };
}