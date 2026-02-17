import React from "react";
import ReactDOM from "react-dom";
import ReactToWebComponent from "react-to-webcomponent";

// webpack.config.js
const path = require("path");

module.exports = {
  resolve: {
    alias: {
      "@project_react": path.resolve(
        __dirname,
        "vuexy-react-admin-dashboard/src"
      ),
    },
  },
};
// کامپوننت‌های React
import AllSubscribers from "../../Project_5G_React/src/views/subscribers/all/index.jsx";
import NewSubscriber from "../../Project_5G_React/src/views/subscribers/new/index.jsx";

// تبدیل کامپوننت‌ها به Web Components
const FirstWebComponent = ReactToWebComponent(AllSubscribers, React, ReactDOM);
const SecondWebComponent = ReactToWebComponent(NewSubscriber, React, ReactDOM);

// تعریف Custom Elements
customElements.define("first-component", FirstWebComponent);
customElements.define("second-component", SecondWebComponent);
