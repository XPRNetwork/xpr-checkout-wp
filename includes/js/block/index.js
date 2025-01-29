(function (factory) {
  typeof define === 'function' && define.amd ? define(factory) :
  factory();
})((function () { 'use strict';

  var _react = _interopRequireDefault(require("react"));
  function _interopRequireDefault(e) { return e && e.__esModule ? e : { "default": e }; }
  // Ensure TypeScript understands window properties

  // Extract functions and settings from the global objects
  var registerPaymentMethod = window.wc.wcBlocksRegistry.registerPaymentMethod;
  var decodeEntities = window.wp.htmlEntities.decodeEntities;
  var getSetting = window.wc.wcSettings.getSetting;

  // Retrieve settings and define the label
  var settings = getSetting("xprcheckout_data", {});
  var label = decodeEntities("XPRCheckout");

  // Define Content component with settings description
  var Content = function Content() {
    return /*#__PURE__*/_react["default"].createElement(_react["default"].Fragment, null, decodeEntities(settings.description || ""));
  };

  // Define Label component to display the payment method label
  var Label = function Label(props) {
    var PaymentMethodLabel = props.components.PaymentMethodLabel;
    return /*#__PURE__*/_react["default"].createElement(PaymentMethodLabel, {
      text: label
    });
  };

  // Register the payment method with TypeScript typing
  registerPaymentMethod({
    name: "xprcheckout",
    label: /*#__PURE__*/_react["default"].createElement(Label, {
      components: {
        PaymentMethodLabel: function PaymentMethodLabel(_ref) {
          var text = _ref.text;
          return /*#__PURE__*/_react["default"].createElement("span", null, text);
        }
      }
    }),
    content: /*#__PURE__*/_react["default"].createElement(Content, null),
    edit: /*#__PURE__*/_react["default"].createElement(Content, null),
    canMakePayment: function canMakePayment() {
      return true;
    },
    ariaLabel: label.toString(),
    supports: {
      features: settings.supports
    }
  });

}));
//# sourceMappingURL=index.js.map
