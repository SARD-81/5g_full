import "./style.css";
import javascriptLogo from "./javascript.svg";
import viteLogo from "/vite.svg";
import { setupCounter } from "./counter.js";
import "bootstrap/dist/js/bootstrap.js";
import "bootstrap/dist/css/bootstrap.css";

const changePage = (name) => {
  document.querySelector("#my-iframe").src = name;
};

// document.querySelector("#app").innerHTML = `
//   <div>
//    <a href="/src/views/login.html">click</a>
//    <button onclick="changePage('src/views/login.html')" >click</button>
//   </div>
// `;
// setupCounter(document.querySelector("#counter"));
