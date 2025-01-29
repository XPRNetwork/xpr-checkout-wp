(function(factory) {
  typeof define === "function" && define.amd ? define(factory) : factory();
})(function() {
  "use strict";
  var _jsxRuntime = require("react/jsx-runtime");
  var _react = _interopRequireDefault(require("react"));
  var _client = _interopRequireDefault(require("react-dom/client"));
  require("./index.css");
  var _App = _interopRequireDefault(require("./App"));
  var _reportWebVitals = _interopRequireDefault(require("./reportWebVitals"));
  function _interopRequireDefault(e) {
    return e && e.__esModule ? e : { "default": e };
  }
  var root = _client["default"].createRoot(document.getElementById("xpr-checkout"));
  root.render(/* @__PURE__ */ (0, _jsxRuntime.jsx)(_react["default"].StrictMode, {
    children: /* @__PURE__ */ (0, _jsxRuntime.jsx)(_App["default"], {})
  }));
  (0, _reportWebVitals["default"])();
});
//# sourceMappingURL=index.js.map
