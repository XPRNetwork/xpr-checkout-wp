"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = exports.Label = void 0;
var _react = _interopRequireDefault(require("react"));
var _htmlEntities = require("@wordpress/html-entities");
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
var registerPaymentMethod = window.wc.wcBlocksRegistry.registerPaymentMethod;
var getSetting = window.wc.wcSettings.getSetting;
var settings = getSetting("misha_data", {});
var label = (0, _htmlEntities.decodeEntities)(settings.title);
var _default = exports.default = Content = function Content() {
  return (0, _htmlEntities.decodeEntities)(settings.description || "");
};
var Label = exports.Label = function Label(props) {
  var PaymentMethodLabel = props.components.PaymentMethodLabel;
  return /*#__PURE__*/_react.default.createElement(PaymentMethodLabel, {
    text: label
  });
};
registerPaymentMethod({
  name: "xprcheckout",
  label: /*#__PURE__*/_react.default.createElement(Label, null),
  content: /*#__PURE__*/_react.default.createElement(Content, null),
  edit: /*#__PURE__*/_react.default.createElement(Content, null),
  canMakePayment: function canMakePayment() {
    return true;
  },
  ariaLabel: label,
  supports: {
    features: settings.supports
  }
});
