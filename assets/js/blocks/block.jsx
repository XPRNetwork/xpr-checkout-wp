
const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
const { decodeEntities } = window.wp.htmlEntities;
const { getSetting } = window.wc.wcSettings;
const settings = getSetting("misha_data", {});
const label = decodeEntities(settings.title);

export default Content = () => {
  return decodeEntities(settings.description || "");
};

export const Label = props => {
  const { PaymentMethodLabel } = props.components;
  return <PaymentMethodLabel text={label} />;
};

registerPaymentMethod({
  name: "xprcheckout",
  label: <Label />,
  content: <Content />,
  edit: <Content />,
  canMakePayment: () => true,
  ariaLabel: label,
  supports: {
    features: settings.supports,
  },
});
