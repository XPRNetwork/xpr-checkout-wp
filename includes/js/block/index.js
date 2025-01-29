(function(factory) {
  typeof define === "function" && define.amd ? define(factory) : factory();
})(function() {
  "use strict";
  var _jsxRuntime = require("react/jsx-runtime");
  var registerPaymentMethod = window.wc.wcBlocksRegistry.registerPaymentMethod;
  var decodeEntities = window.wp.htmlEntities.decodeEntities;
  var getSetting = window.wc.wcSettings.getSetting;
  var settings = getSetting("xprcheckout_data", {});
  var label = decodeEntities("XPRCheckout");
  var Content = function Content2() {
    return /* @__PURE__ */ (0, _jsxRuntime.jsx)(_jsxRuntime.Fragment, {
      children: decodeEntities(settings.description || "")
    });
  };
  var Label = function Label2(props) {
    var PaymentMethodLabel = props.components.PaymentMethodLabel;
    return /* @__PURE__ */ (0, _jsxRuntime.jsx)(PaymentMethodLabel, {
      text: label
    });
  };
  registerPaymentMethod({
    name: "xprcheckout",
    label: /* @__PURE__ */ (0, _jsxRuntime.jsx)(Label, {
      components: {
        PaymentMethodLabel: function PaymentMethodLabel(_ref) {
          var text = _ref.text;
          return /* @__PURE__ */ (0, _jsxRuntime.jsx)("span", {
            children: text
          });
        }
      }
    }),
    content: /* @__PURE__ */ (0, _jsxRuntime.jsx)(Content, {}),
    edit: /* @__PURE__ */ (0, _jsxRuntime.jsx)(Content, {}),
    canMakePayment: function canMakePayment() {
      return true;
    },
    ariaLabel: label.toString(),
    supports: {
      features: settings.supports
    }
  });
});
//# sourceMappingURL=index.js.map
