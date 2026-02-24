import useApi, { ContentDisposition } from "./useApi.js";
import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";
import JSONEditor from "jsoneditor";
import "jsoneditor/dist/jsoneditor.min.css";
import fileDownload from "js-file-download";
import favIconBBDH from "../img/logo white.png";
import favIconMCI from "../img/logo-mci-02 (1).svg";
import specialKeys, { dataModules } from "./dataModule.js"; // وارد کردن داده‌ها از data.js// data.js
import { error } from "jquery";
import VersioningModule from "./VersioningModules.js";
let project = import.meta.env.VITE_API_PROJECT;

if (project == "BBDH") {
  document.getElementById("favIcon").href = favIconBBDH;
} else if (project == "MCI") {
  document.getElementById("favIcon").href = favIconMCI;
} else if (project == "RRU") {
  document.getElementById("favIcon").href = favIconBBDH;
}

if (window.innerWidth > 768) {
  document.getElementById("menuHamber").remove();
}

if (window.innerWidth < 768) {
  document.getElementById("idSidebar").remove();
}

if (window.innerWidth < 650) {
  document.getElementById("spanReturnToPreviousState").remove();
  document.getElementById("spanReturnToTheInitialState").remove();
  document.getElementById("spanExportModule").remove();
  document.getElementById("spanStartModule").remove();
  document.getElementById("spanStopModule").remove();
  document.getElementById("spanRestartModule").remove();
  document.getElementById("spanStatusModule").remove();
}

if (window.innerWidth < 768) {
  document.getElementById("EditorContainer").style.width = `${window.innerWidth - 27
    }px`;
  document.getElementById("TabEditorContainer").style.width = `${window.innerWidth - 27
    }px`;
}

// import * as jsondiffpatch from "jsondiffpatch";
// const jsondiffpatchInstance = jsondiffpatch.create(options);

let emailForm = document.getElementById("form-label");

function RemoveActiveTable(x) {
  for (let i = 1; i <= 5; i++) {
    document.getElementById("table" + i).classList.remove("active");
  }
  document.getElementById("table" + x).classList.add("active");
  emailForm.innerHTML = "Email address" + x;
}

function APIserver(x) {
  let breadrumb = document.getElementById("serverBreadrumb");
  breadrumb.innerHTML = localStorage.getItem("nameServer");
}

function table(x) {
  RemoveActiveTable(x);
}

function Select_A_Menu(x) {
  for (let i = 1; i <= 8; i++) {
    document.getElementById("a-menu" + i).classList.remove("selectMenu");
  }
  document.getElementById("a-menu" + x).classList.add("selectMenu");
}

function aMenu(x) {
  Select_A_Menu(x);
}

function getParameterByName(name) {
  name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
  var regexS = "[\\?&]" + name + "=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(window.location.href);
  if (results == null) return "";
  else return decodeURIComponent(results[1].replace(/\+/g, " "));
}

function addOrUpdateUrlParam(name, value, href = window.location.href) {
  var regex = new RegExp("[&\\?]" + name + "=");
  let url = undefined;
  if (regex.test(href)) {
    // regex = new RegExp("([&\\?])" + name + "=\\d+");
    regex = new RegExp("([&\\?])" + name + "=[^&]+");
    url = href.replace(regex, "$1" + name + "=" + value);
  } else {
    if (href.indexOf("?") > -1) {
      url = href + "&" + name + "=" + value;
    } else {
      url = href + "?" + name + "=" + value;
    }
  }
  if (!!url) window.history.replaceState({}, "", url);
}
// Module //

let firstTabModule;
let booleanFirstTabModule = true;

function generateFormFromJson(data) {
  return new Promise((resolve) => {
    const mainTabs = document.getElementById("mainTabs");
    booleanFirstTabModule = true;

    // const tabContent = document.getElementById("jsoneditor");

    if (data) {
      Object.keys(data).forEach((key, index) => {
        // if(UpdateJsons) {
        // }
        const tabId = `tab-${key}`;
        const tabButton = document.createElement("li");
        tabButton.classList.add("nav-item");
        tabButton.innerHTML = `
          <button class="nav-link ${index === 0 ? "active" : ""
          }" id="${tabId}-tab" data-bs-toggle="tab" data-bs-target="#${tabId}" type="button" role="tab" aria-controls="${tabId}" aria-selected="${index === 0
          }">
          ${key}
        </button>
        `;

        if (booleanFirstTabModule) {
          firstTabModule = key;
          booleanFirstTabModule = false;
        }

        mainTabs.appendChild(tabButton);

        const tabPane = document.createElement("div");
        tabPane.classList.add(
          "tab-pane",
          "fade",
          index === 0 ? "show" : "null"
        );
        if (index === 0) tabPane.classList.add("active");

        tabPane.id = tabId;
        tabPane.setAttribute("role", "tabpanel");
        tabPane.setAttribute("aria-labelledby", `${tabId}-tab`);

        // Add tabs to a separate list with edit functionality
        const listItem = document.createElement("li");
        listItem.classList.add(
          "list-group-item",
          "d-flex",
          "justify-content-between",
          "align-items-center"
        );
        listItem.textContent = key;

        const editButton = document.createElement("button");
        editButton.classList.add("btn", "btn-sm", "btn-primary");
        editButton.textContent = "Edit";
        editButton.addEventListener("click", () => {
          showEditModal(key, listItem);
        });

        listItem.appendChild(editButton);
      });
    }
    findProperty(Object.keys(data)[0]);
    addNewTab(data);
    resolve();
  });
}

function addNewTab(data) {
  const mainTabs = document.getElementById("mainTabs");
  const tabContent = document.getElementById("jsoneditor");

  if (data) {
    // ایجاد تب اصلی
    const tabId = "tab-all";
    const tabButton = document.createElement("li");
    tabButton.classList.add("nav-item");

    tabButton.innerHTML = `
      <button class="nav-link" id="${tabId}-tab" data-bs-toggle="tab" data-bs-target="#${tabId}" type="button" role="tab" aria-controls="${tabId}" aria-selected="false">
        modules
      </button>
    `;
    mainTabs.appendChild(tabButton);
    // ایجاد محتوای تب اصلی
    const tabPane = document.createElement("div");
    tabPane.classList.add("tab-pane", "fade");
    tabPane.id = tabId;
    tabPane.setAttribute("role", "tabpanel");
    tabPane.setAttribute("aria-labelledby", `${tabId}-tab`);

    const form = document.createElement("form");

    // ساخت فرم برای تمام داده‌ها
    // Object.keys(data).forEach((key) => {
    //   createInputs(key, data[key], form, key);
    // });

    tabPane.appendChild(form);
    tabContent.appendChild(tabPane);
    // let tabs = document.querySelectorAll(".tab-pane");
    // // tabs.style.display = "flex";
    // tabs.forEach((tab) => {
    // });
  }
}

let selectedServers = [];

async function numberOfServers() {
  await useApi({
    url: "show-all-servers",
    callback: async function (data) {
      let updateBtn = document.getElementById("updateBtn");
      if (updateBtn) {
        updateBtn.addEventListener("click", () => {
          selectedServers = [];
          // محل قرار دادن چک‌باکس‌ها
          const container = document.getElementById("divServers");
          container.innerHTML = "";

          for (let i = 0; i < specifyingTheModuleServer.length; i++) {
            for (let x = 0; x < data.data.length; x++) {
              if (data.data[x].id == specifyingTheModuleServer[i]) {
                const div = document.createElement("div");
                div.setAttribute(
                  "class",
                  "d-flex juctify-content-center align-items-center mb-2"
                );
                div.style.display = "flex";
                // ایجاد یک لیبل
                const label = document.createElement("label");
                label.textContent = data.data[x].name; // متن لیبل
                label.style.marginRight = "10px"; // فاصله لیبل با چک‌باکس

                // ایجاد یک چک‌باکس
                const checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.setAttribute("class", "checkboxServer");
                checkbox.id = data.data[x].id; // مشخصه منحصربه‌فرد
                checkbox.name = data.data[x].name;

                // اضافه کردن چک‌باکس و لیبل به container
                div.appendChild(label);
                div.appendChild(checkbox);

                container.appendChild(div);
              }
            }
          }

          // document.getElementById("e").style.borderColor = ""

          document.querySelectorAll(".checkboxServer").forEach((e) => {
            for (let i = 0; i < data.data.length; i++) {
              if (e.id == localStorage.getItem("server")) {
                e.disabled = true;
                e.checked = true;
                e.style.backgroundColor = "gray";
                e.style.borderColor = "gray";
              }
            }
          });
        });
      }
    },
  });
}

let subUpdateModule = document.getElementById("subUpdateModule");
subUpdateModule.addEventListener("click", (event) => {
  saveDataToServer();
});

// function handleInputChange() {
// saveDataToServer();
// }

let returnToPreviousState = document.getElementById("subReturnToPrevious");
returnToPreviousState.addEventListener("click", () => {
  // document.getElementById("updateBtn").style.bottom = "-100px";
  backToBefore();
});

async function backToBefore() {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  let userName = localStorage.getItem("userNameServer");
  let password = localStorage.getItem("passwordServer");
  let port = localStorage.getItem("port");

  await useApi({
    method: "post",
    url: "undo-module-config",
    data: {
      server_id: TheDesiredServer,
      module_id: currentModuleId,
      username: userName,
      password: password,
      ...(!!port ? { port } : {}),
    },
    callback: async function (data) {
      UpdateJsons = false;
      // objectsOne = true;
      document.getElementById("mainTabs").innerHTML = "";
      // document.querySelector(".jsoneditor-mode-tree").innerHTML = "";
      if (data.config) {
        jsonData = data.config;

        // removeLastGeneratedForm();
        await generateFormFromJson(jsonData);
        setEventListeners();
        let status = 1;
        CallingTabs(status);
        showAllModules();
        // findProperty(jsonData);
        // window.location.reload();
      }

      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
    },
  });
  document.getElementById("idLoading").style.display = "none";
}

let returnToTheInitialState = document.getElementById("subReturnToTheInitial");
returnToTheInitialState.addEventListener("click", () => {
  document.getElementById("updateBtn").style.bottom = "-100px";
  returnToTheFirstState();
});

async function returnToTheFirstState() {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  let userName = localStorage.getItem("userNameServer");
  let password = localStorage.getItem("passwordServer");
  let port = localStorage.getItem("port");

  await useApi({
    method: "post",
    url: "undo-to-initial-config-modules",
    data: {
      server_id: TheDesiredServer,
      module_id: currentModuleId,
      username: userName,
      password: password,
      ...(!!port ? { port } : {}),
    },
    callback: async function (data) {
      UpdateJsons = false;
      // objectsOne = true;
      document.getElementById("mainTabs").innerHTML = "";
      document.querySelector(".jsoneditor-mode-tree").innerHTML = "";
      // const firstKey = Object.keys(data.config)[0];
      jsonData = data.config;
      await generateFormFromJson(jsonData);
      setEventListeners();
      let status = 1;
      CallingTabs(status);
      showAllModules();
      // findProperty(firstKey);
      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
    },
  });
  document.getElementById("idLoading").style.display = "none";
}

let dataServer = JSON.parse(localStorage.getItem("dataServer"));

async function saveDataToServer() {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  // let userName = localStorage.getItem("userNameServer");
  // let password = localStorage.getItem("passwordServer");
  // let port = localStorage.getItem("port");

  let serverIds = [];
  let ServerSpecifications;

  document.querySelectorAll(".checkboxServer").forEach((e) => {
    if (e.checked) {
      dataServer.forEach((server) => {
        if (server.id == e.id) {
          ServerSpecifications = {
            id: server.id,
            username: server.username,
            password: server.password,
            port: server.port,
          };
          serverIds.push(ServerSpecifications);
        }
      });
    }
  });

  await useApi({
    method: "post",
    url: "update-config-module",
    data: {
      data: updateJsonKey(oldDataContainer, newDataContainer),
      module_id: currentModuleId,
      // server_id: TheDesiredServer,
      servers: serverIds,
      // username: userName,
      // password: password,
      // servers: selectedServers,
      // ...(!!port ? { port } : {}),
    },
    callback: async function (data) {
      document.getElementById("mainTabs").innerHTML = "";
      // document.querySelectorId("jsoneditor").innerHTML = "";
      jsonData = data.config;
      await generateFormFromJson(jsonData);
      setEventListeners();
      let status = 0;
      CallingTabs(status);
      showAllModules();
      document.querySelectorAll(".nav-link").forEach((tab) => {
        tab.classList.remove("active");
      });
      findProperty(tabName);

      resetChanges();
      if (document.getElementById(`tab-${tabName}-tab`))
        document.getElementById(`tab-${tabName}-tab`).classList.add("active");
      Toastify({
        text: "The configurations have been successfully updated.",
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
      document.getElementById("updateBtn").style.bottom = "-100px";
    },
    // errorCallback: function (data) {
    //   Toastify({
    //     text: "VM Error:" + data.data.message,
    //   }).showToast();
    // },
  });
  document.getElementById("idLoading").style.display = "none";
}

// Example JSON data with boolean values

let jsonData = {};

async function onLoadFunction1() {
  backToLogin();
  if (!!getParameterByName("server")) {
    localStorage.setItem("server", getParameterByName("server"));
  } else {
    APIserver(Number(localStorage.getItem("server")));
  }
  APIserver(Number(getParameterByName("server")));
  numberOfServers();
}

let roleUser;
let roleUserGetMe;
let motherboard = false;

async function backToLogin() {
  document.getElementById("idLoading").style.display = "flex";
  await useApi({
    url: "get-me",
    callback: function (data) {
      // let serverId = data.data.user.server_id;
      // if (serverId != null) {
      //   if (document.getElementById("idSidebar")) {
      //     document.getElementById("idSidebar").style.backgroundColor =
      //       "#05009b";
      //   }
      //   if (document.getElementById("offcanvasWithBothOptions")) {
      //     document
      //       .getElementById("offcanvasWithBothOptions")
      //       .classList.remove("backOffcanvas");
      //     document
      //       .getElementById("offcanvasWithBothOptions")
      //       .classList.add("backOffcanvasMotherboard");
      //   }
      //   document.querySelector(".sidebar").style.backgroundColor = "#05009b";
      //   motherboard = true;
      // }

      roleUser = data.data.user.permissions.find(checkAge);
      roleUserGetMe = data.data.user.roles[0];

      function checkAge(per) {
        return per == "module/update";
      }
      // roleUser = data.data.user.roles[0];
    },
    errorCallback: function () {
      window.location.href = "../views/login.html";
    },
  });
  if (roleUser != "module/update") {
    document.getElementById("returnToPreviousState").remove();
    document.getElementById("returnToTheInitialState").remove();
    document.getElementById("exportModule").remove();
    document.getElementById("startModule").remove();
    document.getElementById("stopModule").remove();
    document.getElementById("restartModule").remove();
    document.getElementById("statusModule").remove();
    // document.getElementById("reloadServer").remove();
    document.getElementById("updateBtn").remove();
    document.getElementById("returnToPrevious").remove();
    document.getElementById("returnToTheInitial").remove();
    document.getElementById("moduleUpdateBtn").remove();
    document.getElementById("idpageConfigModule").classList.add("mt-4");
  }
  // document.getElementById("idLoading").style.display = "none";
}

// شیء برای ذخیره تغییرات
let changes = {};

// شمارشگر تغییرات
let changeCount = 0;

function resetChanges() {
  changes = {};
  changeCount = 0;
  updateChangeCount();
}

// عنصر HTML برای نمایش تعداد تغییرات
const changeCounterDisplay = document.getElementById("changeCounter"); // فرض کنید این عنصر در HTML وجود دارد

// تابع کمکی برای دسترسی به مقدار اولیه از روی مسیر
function getInitialValue(path, data) {
  if (!path || !data) return;

  try {
    // مسیر را به اجزای قابل فهم تبدیل کنید
    const keys = path
      .replace(/\[(\d+)\]/g, ".$1") // تبدیل اندیس‌های آرایه به فرمت استاندارد
      .split(".");

    // تجزیه مسیر و بازیابی مقدار
    return keys.reduce((obj, key) => {
      if (obj && typeof obj === "object" && key in obj) {
        return obj[key];
      }
      throw new Error(`Key "${key}" not found in the object.`, data);
    }, data);
  } catch (error) {
    console.error(error.message, data);
    return null;
  }
}

// به‌روزرسانی نمایش تعداد تغییرات
function updateChangeCount() {
  // if (Object.keys(changes).length > 0) {
  //   document.getElementById("boxChange").style.bottom = "0px";
  // } else {
  //   document.getElementById("boxChange").style.bottom = "-100px";
  // }
  changeCount = Object.keys(changes).length;
  // changeCounterDisplay.textContent = `Number of Changes: ${changeCount}`;
}

// بررسی تغییرات و ثبت در شیء تغییرات
function trackChange(path, newValue) {
  setTimeout(() => {
    const initialValue = getInitialValue(path, jsonData) || "";

    // اگر مقدار جدید با مقدار اولیه متفاوت بود
    if (newValue != initialValue.toString()) {
      changes[path] = newValue;
    } else if (changes.hasOwnProperty(path)) {
      delete changes[path]; // حذف تغییر در صورت بازگشت مقدار اولیه
    }

    updateChangeCount();
  }, 200);
}

function setEventListeners() {
  // گوش دادن به تغییرات در اینپوت‌ها

  document.querySelectorAll("input").forEach((input) => {
    const path = input.getAttribute("data-path"); // فرض بر اینکه هر اینپوت یک data-path دارد که مسیر را ذخیره می‌کند

    if (path) {
      setTimeout(() => {
        input.value = getInitialValue(path, jsonData) || ""; // مقدار اولیه را از داده‌ها می‌گیرد

        input.addEventListener("input", (event) => {
          const newValue = event.target.value;
          trackChange(path, newValue);
        });
      }, 100);
    }
  });

  // // گوش دادن به تغییرات در اینپوت‌ها
  document.querySelectorAll("select").forEach((select) => {
    const path = select.getAttribute("data-path"); // فرض بر اینکه هر اینپوت یک data-path دارد که مسیر را ذخیره می‌کند
    if (path) {
      select.value = getInitialValue(path, jsonData) || ""; // مقدار اولیه را از داده‌ها می‌گیرد

      select.addEventListener("change", (event) => {
        const newValue = event.target.value;
        trackChange(path, newValue);
      });
    }
  });
}

let jsonDataNew;

// تابعی برای نمایش آرایه نهایی تغییرات
function getChanges() {
  jsonDataNew = jsonData;

  const arrChanges = Object.keys(changes).map((key) => {
    let value = changes[key];

    // Convert 'true' and 'false' string values to boolean true and false
    if (value === "true") {
      value = true;
    } else if (value === "false") {
      value = false;
    }

    return { [key]: value };
  });

  return arrChanges.reduce((acc, obj) => {
    return { ...acc, ...obj };
  }, {});
}

async function onLoadFunction2() {
  let serverId;
  if (!localStorage.getItem("server")) {
    addOrUpdateUrlParam("server", 1);
    serverId = 1;
  } else {
    addOrUpdateUrlParam("server", localStorage.getItem("server"));
    serverId = localStorage.getItem("server");
  }
  await getModules(serverId);
}

window.onload = async function () {
  document.getElementById("body").style.display = "block";
  await onLoadFunction1(); // ابتدا اجرای تابع اول
  await onLoadFunction2(); // سپس اجرای تابع دوم
  let url = new URL(window.location).searchParams;
  let moduleIdUrl = url.get("module_id");
  desiredRootModuleId(moduleIdUrl);
};

let Modules;
let moduleId;

async function getModules(serverId) {
  await useApi({
    url: `show-all-servies-and-modules/${serverId}`,
    callback: function (data) {
      Modules = data.data;
      let lengthEpc = data.data.Epc.length;
      let length5gc = data.data["5gc"].length;
      for (let i = 0; i < lengthEpc; i++) {
        let tempModuleId = data.data["Epc"][i]["id"];
        let aEpc = document.createElement("a");
        aEpc.setAttribute("class", "selectMenu module-button");
        aEpc.setAttribute("id", `Type-${data.data["Epc"][i]["id"]}`);
        aEpc.setAttribute("data-route", `${data.data["Epc"][i]["name"]}`);
        aEpc.innerHTML = data.data.Epc[i].name;
        aEpc.addEventListener("click", (x) => {
          if (document.getElementById("closeOffcanvas")) {
            document.getElementById("closeOffcanvas").click();
          }
          UpdateJsons = false;
          tabModuleSelect = undefined;
          if (moduleId != tempModuleId) {
            if (document.getElementById("updateBtn") != null) {
              document.getElementById("updateBtn").style.bottom = "-100px";
            }
            displayNone = false;
            handleButtonClickModulId(tempModuleId);
            resetChanges();
            document.getElementById("idMassgeModule").style.display = "none";
            getModuleConfig(tempModuleId);
            moduleId = tempModuleId;
            let element = x.target;
            for (let x = 0; x < length5gc; x++) {
              document
                .getElementById(`Type-${data.data["5gc"][x]["id"]}`)
                .classList.remove("selectMenus");
              if (motherboard) {
                document
                  .getElementById(`Type-${data.data["5gc"][x]["id"]}`)
                  .classList.remove("backSelectMenus");
              }
            }
            for (let x = 0; x < lengthEpc; x++) {
              document
                .getElementById(`Type-${data.data["Epc"][x]["id"]}`)
                .classList.remove("selectMenus");
              if (motherboard) {
                document
                  .getElementById(`Type-${data.data["Epc"][x]["id"]}`)
                  .classList.remove("backSelectMenus");
              }
            }

            element.classList.add("selectMenus");
            if (motherboard) {
              element.classList.add("backSelectMenus");
            }
            document.getElementById("idpageConfigModule").style.display =
              "block";
            document.getElementById("jsoneditor").innerHTML = "";
          }
        });
        document.querySelector(".accordion-body-epc").appendChild(aEpc);
      }

      for (let i = 0; i < length5gc; i++) {
        let tempModuleId = data.data["5gc"][i]["id"];
        let a5gc = document.createElement("a");
        a5gc.setAttribute("class", "selectMenu module-button");
        a5gc.setAttribute("id", `Type-${data.data["5gc"][i]["id"]}`);
        a5gc.setAttribute("data-route", data.data["5gc"][i]["name"]);
        a5gc.innerHTML = data.data["5gc"][i].name;
        a5gc.addEventListener("click", (x) => {
          if (document.getElementById("closeOffcanvas")) {
            document.getElementById("closeOffcanvas").click();
          }
          UpdateJsons = false;
          tabModuleSelect = undefined;
          if (moduleId != tempModuleId) {
            if (document.getElementById("updateBtn") != null) {
              document.getElementById("updateBtn").style.bottom = "-100px";
            }
            displayNone = false;
            handleButtonClickModulId(tempModuleId);
            resetChanges();
            document.getElementById("idMassgeModule").style.display = "none";
            document.getElementById("idLoading").style.display = "flex";
            getModuleConfig(tempModuleId);
            moduleId = tempModuleId;
            let element = x.target;
            for (let x = 0; x < lengthEpc; x++) {
              document
                .getElementById(`Type-${data.data["Epc"][x]["id"]}`)
                .classList.remove("selectMenus");
              if (motherboard) {
                document
                  .getElementById(`Type-${data.data["Epc"][x]["id"]}`)
                  .classList.remove("backSelectMenus");
              }
            }
            for (let x = 0; x < length5gc; x++) {
              document
                .getElementById(`Type-${data.data["5gc"][x]["id"]}`)
                .classList.remove("selectMenus");
              if (motherboard) {
                document
                  .getElementById(`Type-${data.data["5gc"][x]["id"]}`)
                  .classList.remove("backSelectMenus");
              }
            }

            element.classList.add("selectMenus");
            if (motherboard) {
              element.classList.add("backSelectMenus");
            }
            document.getElementById("idpageConfigModule").style.display =
              "block";
            document.getElementById("jsoneditor").innerHTML = "";
          }
        });
        document.querySelector(".accordion-body-5gc").appendChild(a5gc);
      }
    },
  });

  if (document.getElementById("idSidebar")) {
    document.getElementById("idLoadingModule").style.display = "none";
  }
  document.getElementById("idLoading").style.display = "none";
}

let displayNone = false;
let currentModuleId;
let showConfigModuleLoading = false;
let TheDesiredServer = localStorage.getItem("server");
let specifyingTheModuleServer;
let moduleDetails;

async function getModuleConfig(moduleId) {
  if (showConfigModuleLoading) return;
  showConfigModuleLoading = true;
  currentModuleId = moduleId;
  document.getElementById("idLoading").style.display = "flex";
  await useApi({
    url: `show-config-module/${TheDesiredServer}/${moduleId}`,
    callback: async function (data) {
      moduleDetails = data.moduleDetails.name;
      document.getElementById("mainTabs").innerHTML = "";
      specifyingTheModuleServer = data.serversIdInModuleName;
      if (data) jsonData = data.config;
      // removeLastGeneratedForm();
      await generateFormFromJson(jsonData);
      setEventListeners();
      // setObserver();
      let status = 0;
      CallingTabs(status);
      showAllModules();
    },
  });
  showConfigModuleLoading = false;
  // document.getElementById("idMassgeModule").style.display = "none";
  document.getElementById("idLoading").style.display = "none";
  if (document.getElementById("idSidebar")) {
    document.getElementById("idLoadingModule").style.display = "none";
  }
}

let UpdateJsons = false;

function showAllModules() {
  document.getElementById("tab-all-tab").addEventListener("click", function () {
    UpdateJsons = true;
    document.getElementById("jsoneditor").innerHTML = "";
    setJsonEditor(jsonData);
  });
}

let tabModuleSelect;

function CallingTabs(status) {
  document
    .querySelectorAll("#mainTabs .nav-link")
    .forEach(function (e, number) {
      if (status == 1) {
        tabModuleSelect = e.innerHTML.replace(/\s+/g, " ").trim();

        if (document.getElementById("updateBtn") != null) {
          document.getElementById("updateBtn").style.bottom = "-100px";
        }

        if (e.innerHTML.trim() != "modules") {
          UpdateJsons = false;
        } else {
          UpdateJsons = true;
        }
        document.getElementById("jsoneditor").innerHTML = ""; // clear page after each click to prevent multi jsoneditor in page
        if (document.visibilityState === "visible") {
          findProperty(e.innerHTML.trim());
        }
        status = 0;
        if (number != 0) return;
      } else {
        e.addEventListener("click", function () {
          // خروجی: "این یک متن با فاصله‌های اضافی است"
          tabModuleSelect = e.innerHTML.replace(/\s+/g, " ").trim();

          if (document.getElementById("updateBtn") != null) {
            document.getElementById("updateBtn").style.bottom = "-100px";
          }

          if (e.innerHTML.trim() != "modules") {
            UpdateJsons = false;
          } else {
            UpdateJsons = true;
          }

          document.getElementById("jsoneditor").innerHTML = ""; // clear page after each click to prevent multi jsoneditor in page
          if (document.visibilityState === "visible") {
            findProperty(e.innerHTML.trim());
          }
        });
      }
    });
}

let CompleteJsonData;

function findProperty(targetKey) {
  if (UpdateJsons) {
    setJsonEditor(jsonData);
  } else {
    // generateFormFromJson(jsonData);
    let objectKeys = Object.keys(jsonData);

    CompleteJsonData = jsonData;
    // firstTabModule = objectKeys[0]
    // const key = objectKeys.find(jsonDataKey => jsonDataKey === targetKey);
    // const result = key ? { [key]: jsonData[key] } : null;

    setJsonEditor(
      jsonData[objectKeys.find((jsonDataKey) => jsonDataKey == targetKey)]
    );
  }
}

let exportModule = document.getElementById("exportModule");
exportModule.addEventListener("click", () => {
  let url = new URL(window.location).searchParams;
  let moduleIdUrl = url.get("module_id");
  ExportModules(moduleIdUrl);
});

async function ExportModules(id) {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  let userName = localStorage.getItem("userNameServer");
  let password = localStorage.getItem("passwordServer");
  let port = localStorage.getItem("port");

  await useApi({
    method: "post",
    url: `export-module-file`,
    responseType: "blob",
    headers: {
      "Content-Type": "multipart/form-data",
    },
    data: {
      server_id: localStorage.getItem("server"),
      module_id: id,
      username: userName,
      password: password,
      ...(!!port ? { port } : {}),
    },
    callback: async function (data) {
      const text = await data.text(); // blob → text
      console.log("YAML received from server:\n", text);
      downloadFileProcess(data);
    },
  });
  document.getElementById("idLoading").style.display = "none";
}

const downloadFileProcess = (data, name = null) => {
  if (data) {
    if (name) return fileDownload(data, name);

    // set name from response headers
    let filename;
    if (ContentDisposition) {
      const regex = /filename=([^;]+)/;
      const match = ContentDisposition.match(regex);
      filename = match[1];
    } else {
      filename = "name.yaml";
      Toastify({
        text: "The file was not downloaded correctly!",
      }).showToast();
    }

    filename = filename?.replace(/"/g, ""); // Removes all double quotes
    fileDownload(data, filename);
  }
};

document.getElementById("startModule").addEventListener("click", () => {
  let url = new URL(window.location).searchParams;
  let moduleIdUrl = url.get("module_id");
  StartModules(moduleIdUrl);
});

async function StartModules(id) {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  let userName = localStorage.getItem("userNameServer");
  let password = localStorage.getItem("passwordServer");
  let port = localStorage.getItem("port");

  await useApi({
    method: "post",
    url: `start-service-config`,
    data: {
      server_id: localStorage.getItem("server"),
      module_id: id,
      username: userName,
      password: password,
      ...(!!port ? { port } : {}),
    },
    callback: async function (data) {
      Toastify({
        text: "The module started successfully.",
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
    },
  });
  document.getElementById("idLoading").style.display = "none";
}

document.getElementById("stopModule").addEventListener("click", () => {
  let url = new URL(window.location).searchParams;
  let moduleIdUrl = url.get("module_id");
  StopModules(moduleIdUrl);
});

async function StopModules(id) {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  let userName = localStorage.getItem("userNameServer");
  let password = localStorage.getItem("passwordServer");
  let port = localStorage.getItem("port");

  await useApi({
    method: "post",
    url: `stop-service-config`,
    data: {
      server_id: localStorage.getItem("server"),
      module_id: id,
      username: userName,
      password: password,
      ...(!!port ? { port } : {}),
    },
    callback: async function (data) {
      Toastify({
        text: "The module stopped successfully.",
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
    },
  });
  document.getElementById("idLoading").style.display = "none";
}

document.getElementById("restartModule").addEventListener("click", () => {
  let url = new URL(window.location).searchParams;
  let moduleIdUrl = url.get("module_id");
  RestartModules(moduleIdUrl);
});

async function RestartModules(id) {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  let userName = localStorage.getItem("userNameServer");
  let password = localStorage.getItem("passwordServer");
  let port = localStorage.getItem("port");

  await useApi({
    method: "post",
    url: `restart-service-config`,
    data: {
      server_id: localStorage.getItem("server"),
      module_id: id,
      username: userName,
      password: password,
      ...(!!port ? { port } : {}),
    },
    callback: async function () {
      Toastify({
        text: `${moduleDetails} module was successfully restarted.`,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
    },
    // errorCallback: function (data) {
    //   let message = data.data.error.message;

    //   if (typeof message === "object" && message !== null) {
    //     let keyObject = Object.keys(message);
    //     let length = keyObject.length;

    //     for (let i = 0; i < length; i++) {
    //       let errorKey = keyObject[i]; // دریافت کلید خطا
    //       let errorMessage = message[errorKey]; // دریافت پیام خطا با استفاده از کلید

    //       Toastify({
    //         text: `${errorMessage}`, // نمایش پیام خطا
    //         style: {
    //           background:
    //             "linear-gradient(to right, rgb(255, 0, 0), rgb(231, 0, 0))",
    //         },
    //       }).showToast();
    //     }
    //   } else if (typeof message === "string") {
    //     // حالت دوم: message یک رشته است
    //     Toastify({
    //       text: message, // نمایش پیام خطا
    //       style: {
    //         background:
    //           "linear-gradient(to right, rgb(255, 0, 0), rgb(231, 0, 0))",
    //       },
    //     }).showToast();
    //   }
    // },
  });
  document.getElementById("idLoading").style.display = "none";
}

document.getElementById("statusModule").addEventListener("click", () => {
  document.getElementById("textStatus").textContent =
    "Waiting for the response to the request...";
  let url = new URL(window.location).searchParams;
  let moduleIdUrl = url.get("module_id");
  StatusModules(moduleIdUrl);
});

async function StatusModules(id) {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  let userName = localStorage.getItem("userNameServer");
  let password = localStorage.getItem("passwordServer");
  let port = localStorage.getItem("port");

  await useApi({
    method: "post",
    url: `status-service-config`,
    data: {
      server_id: localStorage.getItem("server"),
      module_id: id,
      username: userName,
      password: password,
      ...(!!port ? { port } : {}),
    },
    callback: async function (data) {
      if (data.message) {
        let msg = data?.message?.replace(/\x1b\[[0-9;?]*[hlmKG]/g, "");

        // رنگی کردن کلمات
        msg = msg
          .replace(/\bfailed\b/gi, `<span style="color:red;">failed</span>`)
          .replace(
            /\b(running|exited)\b/gi,
            `<span style="color:green;">$1</span>`
          )
          .replace(
            /\b(activating|reloading)\b/gi,
            `<span style="color:gold;">$1</span>`
          )
          .replace(
            /\b(inactive|dead)\b/gi,
            `<span style="color:red;">$1</span>`
          )
          .replace(
            /\b(not-found|bad)\b/gi,
            `<span style="color:red;">$1</span>`
          );

        document.getElementById("textStatus").innerHTML = msg;
      } else {
        let msg = data?.replace(/\x1b\[[0-9;?]*[hlmKG]/g, "");

        msg = msg
          .replace(/\bfailed\b/gi, `<span style="color:red;">failed</span>`)
          .replace(
            /\b(running|exited)\b/gi,
            `<span style="color:darkgreen;">$1</span>`
          )
          .replace(
            /\b(activating|reloading)\b/gi,
            `<span style="color:gold;">$1</span>`
          )
          .replace(
            /\b(inactive|dead)\b/gi,
            `<span style="color:gray;">$1</span>`
          )
          .replace(
            /\b(not-found|bad)\b/gi,
            `<span style="color:red;">$1</span>`
          );

        document.getElementById("textStatus").innerHTML = msg;
      }

      Toastify({
        text: "SSH was successful.",
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
    },
    errorCallback: function () {
      document.getElementById("textStatus").textContent =
        "SSH was not successful.";
    },
    // errorCallback: function (data) {
    //   let message = data.data.error.message;

    //   if (typeof message === "object" && message !== null) {
    //     let keyObject = Object.keys(message);
    //     let length = keyObject.length;

    //     for (let i = 0; i < length; i++) {
    //       let errorKey = keyObject[i]; // دریافت کلید خطا
    //       let errorMessage = message[errorKey]; // دریافت پیام خطا با استفاده از کلید

    //       Toastify({
    //         text: `${errorMessage}`, // نمایش پیام خطا
    //         style: {
    //           background:
    //             "linear-gradient(to right, rgb(255, 0, 0), rgb(231, 0, 0))",
    //         },
    //       }).showToast();
    //     }
    //   } else if (typeof message === "string") {
    //     // حالت دوم: message یک رشته است
    //     Toastify({
    //       text: message, // نمایش پیام خطا
    //       style: {
    //         background:
    //           "linear-gradient(to right, rgb(255, 0, 0), rgb(231, 0, 0))",
    //       },
    //     }).showToast();
    //   }
    // },
  });
  document.getElementById("idLoading").style.display = "none";
}

/*************** */
/*************** */
/*************** */
/*************** */
let idRoute;

function handleButtonClickModulId(route) {
  idRoute = route;

  // Update the URL without reloading the page
  const currentUrl = new URL(window.location);
  currentUrl.searchParams.set("module_id", route); // Set the module in URL
  window.history.pushState({}, "", currentUrl); // Update the URL without reloading
}

let idModuleType;

function desiredRootModuleId(route) {
  if (document.querySelector(`#Type-${route}`) != null) {
    document.getElementById("idpageConfigModule").style.display = "block";
    let dataRoute = document.querySelector(`#Type-${route}`).dataset.route;
    idModuleType = route;
    desiredRoot(dataRoute);
  }
  // Get the value of 'module' from the URL
  const currentUrl = new URL(window.location);
  let module = currentUrl.searchParams.get("module_id");
  // Use the value of 'module' to get the module config
  if (route != null) {
    moduleId = Number(module);
    getModuleConfig(module);
    if (document.getElementById("idSidebar")) {
      document.getElementById("idLoadingModule").style.display = "flex";
    }
  }
}

function desiredRoot(route) {
  if (route != null) {
    document.getElementById("idMassgeModule").style.display = "none";
    document.getElementById("idpageConfigModule").style.display = "block";
    let lengthEpc = Modules.Epc.length;
    for (let i = 0; i < lengthEpc; i++) {
      if (route == Modules.Epc[i].name && idModuleType == Modules.Epc[i].id) {
        document.getElementById("collapseEPC").classList.add("show");
        document.getElementById("collapse5GC").classList.remove("show");
      }
    }

    let length5gc = Modules["5gc"].length;
    for (let i = 0; i < length5gc; i++) {
      if (
        route == Modules["5gc"][i].name &&
        idModuleType == Modules["5gc"][i].id
      ) {
        document.getElementById("collapse5GC").classList.add("show");
        document.getElementById("collapseEPC").classList.remove("show");
      }
    }

    document
      .querySelector(
        `.module-button[data-route='${route}']#Type-${idModuleType}`
      )
      .classList.add("selectMenus");
    if (motherboard) {
      document
        .querySelector(
          `.module-button[data-route='${route}']#Type-${idModuleType}`
        )
        .classList.add("backSelectMenus");
    }
  }
}

///////////////////////
/////////JsonEditor//////////
///////////////////////
// عنصر HTML ویرایشگر
// const container = document.getElementById("jsoneditor");

// container.innerHTML = "";
let keyNew = "";
let tabName = "";

function findChanges(oldData, newData) {
  for (const key in newData) {
    if (!(key in oldData)) {
      keyNew = key; // ذخیره کلید سطح اول
      break; // بعد از پیدا کردن اولین تغییر، از حلقه خارج شو
    } else if (
      typeof newData[key] === "object" &&
      !Array.isArray(newData[key])
    ) {
      continue; // بررسی فرزندان را انجام نده
    }
  }
}

let oldDataContainer, newDataContainer;
let descriptionsData;
let finalObject;

function setJsonEditor(data) {
  document.getElementById("jsoneditor").innerHTML = "";
  const container = document.getElementById("jsoneditor");

  if (roleUser != "module/update") {
    const options = {
      mode: "view",
      modes: ["view"],
      onError: function (err) {
        alert(err.toString());
      },
    };
    const editor = new JSONEditor(container, options);
    editor.set(data);
  } else {
    const options = {
      mode: "tree",
      onChange: function () {
        const updatedJson = editor.get();
        const activeNavLink = document.querySelector(".nav-link.active");
        const activeNavLinkId = activeNavLink ? activeNavLink.id : null;

        if (activeNavLinkId) {
          const parts = activeNavLinkId.split("-");
          tabName = parts[1];
        }

        if (document.getElementById("updateBtn") != null) {
          if (JSON.stringify(data) == JSON.stringify(updatedJson)) {
            document.getElementById("updateBtn").style.bottom = "-100px";
          } else {
            document.getElementById("updateBtn").style.bottom = "0";
          }
        }

        findChanges(jsonData, updatedJson);

        oldDataContainer = CompleteJsonData;
        newDataContainer = updatedJson;
      },
    };

    const editor = new JSONEditor(container, options);
    editor.set(data);

    container.addEventListener("click", function () {
      // findDataKeys(finalObject);
      addIconsForSpecialKeys(finalObject);
    });

    let subModuleName; // از tabModuleSelect گرفته میشود
    let UpperCaseModuleDetails = moduleDetails.toUpperCase();

    if (tabModuleSelect == undefined) {
      if (dataModules[`${UpperCaseModuleDetails}`]) {
        descriptionsData =
          dataModules[`${UpperCaseModuleDetails}`][`${firstTabModule}`];
        subModuleName = firstTabModule;
      }
    } else if (tabModuleSelect == "modules") {
      if (dataModules[`${UpperCaseModuleDetails}`]) {
        descriptionsData = dataModules[`${UpperCaseModuleDetails}`];
      }
    } else {
      if (dataModules[`${UpperCaseModuleDetails}`]) {
        descriptionsData =
          dataModules[`${UpperCaseModuleDetails}`][`${tabModuleSelect}`];
        subModuleName = tabModuleSelect;
      }
    }

    function createModuleObject(moduleName, subModuleName, jsonData) {
      const result = {};

      if (subModuleName === undefined) {
        result[moduleName] = jsonData;
      } else {
        result[moduleName] = {};
        result[moduleName][subModuleName] = jsonData;
      }

      return result;
    }

    // مثال استفاده:
    const moduleName = UpperCaseModuleDetails; // از UpperCaseModuleDetails گرفته میشود
    const jsonData = data;

    finalObject = createModuleObject(moduleName, subModuleName, jsonData);

    addIconsForSpecialKeys(finalObject);

    // findDataKeys(finalObject, dataModules);
  }
}

function addIconsForSpecialKeys(jsonData) {
  const allPaths = extractAllPaths(jsonData);
  document.querySelectorAll(".jsoneditor-treepath-element").forEach((e) => {
    if (e.innerHTML == "object") {
      if (tabModuleSelect == undefined) {
        e.innerHTML = firstTabModule;
      } else {
        e.innerHTML = tabModuleSelect;
      }
    }
    return;
  });
  document.querySelectorAll(".jsoneditor-readonly").forEach((e) => {
    if (e.innerHTML == "object") {
      if (tabModuleSelect == undefined) {
        e.innerHTML = firstTabModule;
      } else {
        e.innerHTML = tabModuleSelect;
      }
    }
    return;
  });

  // مرحله 1: تبدیل specialKeys به map برای lookup سریع
  const specialKeyMap = new Map();
  for (const group of Object.values(specialKeys)) {
    for (const [fullPath, description] of Object.entries(group)) {
      specialKeyMap.set(fullPath, description);
    }
  }

  // مرحله 2: map از fullPath به DOM عنصر مربوطه
  const pathToDOMMap = new Map();

  // اسکن DOM و ساختن map سریع
  const keyElements = document.querySelectorAll(".jsoneditor-field");
  keyElements.forEach((el) => {
    const keyName = el.textContent.trim();
    const tr = el.closest("tr");
    if (!tr) return;

    const valueCell = tr.querySelector(".jsoneditor-value");
    if (!valueCell) return;

    // توی path ها بگرد دنبال keyName مطابق
    const match = allPaths.find(
      (p) => p.key === keyName && !pathToDOMMap.has(p.path)
    );
    if (match) {
      pathToDOMMap.set(match.path, { element: valueCell, keyElement: el });
    }
  });

  // مرحله 3: آیکون اضافه کن فقط به مسیرهای خاص
  for (const [path, description] of specialKeyMap.entries()) {
    const domInfo = pathToDOMMap.get(path);
    if (!domInfo) continue;

    const { element: valueCell } = domInfo;

    // اگر قبلاً آیکون گذاشتیم، رد شو
    if (valueCell.querySelector(".custom-icon-container")) continue;

    const icon = document.createElement("span");
    icon.classList.add("custom-icon-container");
    icon.classList.add("custom-tooltip");
    icon.setAttribute("data-bs-toggle", "tooltip");
    // icon.setAttribute("data-bs-target", "#dataJsonEditor");

    icon.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle-fill custom-icon" viewBox="0 0 16 16">
      <path d="M16 8A8 8 0 1 1 0 8a 8 8 0 0 1 16 0M5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247m2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
      </svg>
      `;

    valueCell.insertAdjacentElement("beforebegin", icon);
    icon.setAttribute("data-tooltip", description);

    // icon.addEventListener("mouseover", (event) => {
    //   const clickedElement = event.target;
    // let currentElement = clickedElement;
    // let count = 0;
    // let lastParent = null;
    // let startElementFound = false; // پرچم برای شروع جستجو

    // // پیدا کردن والدین تا 6 سطح، اما فقط بعد از رسیدن به تگ مورد نظر
    // while (currentElement.parentElement && count < 3) {
    //     // مثال: شروع جستجو از اولین والد با کلاس "start-here"
    //     if (currentElement.parentElement.classList.contains("custom-icon-container")) {
    //         startElementFound = true;
    //     }

    //     if (startElementFound) {
    //       lastParent = currentElement.parentElement;
    //       count++;
    //     }

    //     currentElement = currentElement.parentElement;
    //   }

    // // اگر حداقل یک والد پیدا شد، تگ <p> با متن را اضافه کن
    // if (lastParent) {
    //   const newParagraph = document.createElement('td'); // ایجاد تگ <p>
    //   newParagraph.style.paddingTop = "4px"; // متن داخل <p>
    //     newParagraph.style.paddingLeft = "7px"; // متن داخل <p>
    //     newParagraph.textContent = description; // متن داخل <p>
    //     lastParent.appendChild(newParagraph); // اضافه کردن به انتهای والد
    //   }

    // icon.setAttribute("data-tooltip", description);
    // document.getElementById("titleDataJsonEditor").innerText = path;
    // document.getElementById("valueDataJsonEditor").innerText = description;
    // });
  }

  const tooltipElements = document.querySelectorAll(".custom-tooltip");

  tooltipElements.forEach((element) => {
    const tooltipText = element.getAttribute("data-tooltip");

    if (tooltipText) {
      const tooltipBox = document.createElement("div");
      tooltipBox.className = "tooltip-box";
      tooltipBox.textContent = tooltipText;

      element.appendChild(tooltipBox);
    }
  });

  document.querySelectorAll("td.jsoneditor-tree").forEach((tree) => {
    const customIcons = tree.querySelectorAll("span.custom-icon-container");
    for (let i = 1; i < customIcons.length; i++) {
      customIcons[i].remove();
    }
  });
}

function extractAllPaths(
  obj,
  currentPath = "",
  result = [],
  idCounter = { value: 0 }
) {
  if (typeof obj !== "object" || obj === null) {
    result.push({
      path: currentPath,
      value: obj,
      key: currentPath.split(".").pop(),
      id: idCounter.value++,
    });
    return;
  }

  if (currentPath) {
    result.push({
      path: currentPath,
      value: obj,
      key: currentPath.split(".").pop(),
      id: idCounter.value++,
    });
  }

  if (Array.isArray(obj)) {
    obj.forEach((item, index) => {
      const newPath = `${currentPath}.${index}`;
      extractAllPaths(item, newPath, result, idCounter);
    });
  } else {
    Object.keys(obj).forEach((key) => {
      const newPath = currentPath ? `${currentPath}.${key}` : key;
      extractAllPaths(obj[key], newPath, result, idCounter);
    });
  }

  return result;
}

// function findDataKeys(entryData, parentData = null) {
//   // بررسی اینکه آیا در parentData (والد اصلی) این کلید وجود دارد یا نه

//   for (const [key, value] of Object.entries(entryData)) {
//     if (parentData && !keyInParents(key, parentData)) {
//       // اگر کلید در هیچ یک از والد‌ها (والد اصلی و دیگر والد‌ها) نباشد، ادامه نده و تابع را متوقف کن
//       return;
//     }

//     if (typeof value === "object" && value !== null) {
//       // اگر value یک شیء است، باز هم به طور بازگشتی جستجو کن
//       findDataKeys(value, entryData); // به طور بازگشتی به جستجو ادامه بده
//     } else {
//       // اگر مقدار یک مقدار ساده باشد (مثل string یا number یا boolean)، آیکون رو اضافه کن
//       findAndAddIcon(entryData, key);
//     }
//   }
// }

// function findAndAddIcon(json, keyToFind) {
//   // جستجو در JSON برای پیدا کردن مقدار مربوط به keyToFind
//   for (const [key, value] of Object.entries(json)) {
//     if (typeof value === "object" && value !== null) {
//       // اگر value یک شیء است، ادامه بده به جستجو
//       findAndAddIcon(value, keyToFind);
//     } else if (key === keyToFind) {
//       // اگر کلید پیدا شد، آیکون رو اضافه کن
//       addIconToInput(key, descriptionsData);
//     }
//   }
// }

// function keyInParents(key, parentData) {
//   // پیمایش در والد‌ها و چک کردن وجود کلید
//   for (const [parentKey, parentValue] of Object.entries(parentData)) {
//     if (parentKey === key) {
//       return true; // کلید پیدا شد
//     }
//     if (typeof parentValue === "object" && parentValue !== null) {
//       if (keyInParents(key, parentValue)) {
//         return true; // اگر در سطح بعدی والد‌ها پیدا شد
//       }
//     }
//   }
//   return false; // کلید در هیچ یک از والد‌ها پیدا نشد
// }

// function addIconToInput(key, data) {
//   // جستجو برای key در داخل jsonEditor
//   const keyElements = document.querySelectorAll(".jsoneditor-field"); // پیدا کردن تمام فیلدها
//   // جستجو در آبجکت برای پیدا کردن مقدار مربوط به این key
//   let value = findValueInNestedObject(data, key);

//   if (value !== undefined) {
//     keyElements.forEach(function (element) {
//       // بررسی اینکه آیا این عنصر، دقیقا key مورد نظر رو شامل میشه
//       if (element.textContent.trim() === key) {
//         // ایجاد آیکون
//         const icon = document.createElement("span");
//         icon.classList.add("custom-icon-container");
//         icon.setAttribute("data-bs-toggle", "modal");
//         icon.setAttribute("data-bs-target", "#dataJsonEditor");

//         icon.innerHTML = `
//           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle-fill custom-icon" viewBox="0 0 16 16">
//             <path d="M16 8A8 8 0 1 1 0 8a 8 8 0 0 1 16 0M5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247m2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
//           </svg>
//         `;

//         // پیدا کردن عنصر `jsoneditor-value` و قرار دادن آیکون در کنار آن
//         let parentElement = element.closest("tr"); // نزدیکترین عنصر `tr` را پیدا می‌کنیم
//         if (parentElement) {
//           let valueCell = parentElement.querySelector(".jsoneditor-value"); // سلول حاوی مقدار
//           if (valueCell) {
//             valueCell.insertAdjacentElement("beforebegin", icon); // آیکون رو قبل از مقدار اضافه می‌کنیم
//           }
//         }

//         // تنظیم رفتار کلیک آیکون
//         icon.addEventListener("click", function () {
//           document.getElementById("titleDataJsonEditor").innerHTML = key;
//           document.getElementById("valueDataJsonEditor").innerHTML = value;
//         });
//       }
//     });
//   }

//   const jsonEditorTrees = document.querySelectorAll("td.jsoneditor-tree");

//   jsonEditorTrees.forEach((tree) => {
//     // پیدا کردن تمام spanهای با کلاس custom-icon-container داخل این td
//     const customIcons = tree.querySelectorAll("span.custom-icon-container");

//     // اگر بیش از یک custom-icon-container وجود داشت
//     if (customIcons.length > 1) {
//       // نگه داشتن اولین مورد و حذف بقیه
//       for (let i = 1; i < customIcons.length; i++) {
//         customIcons[i].remove();
//       }
//     }
//   });
// }

// // تابع بازگشتی برای جستجوی مقدار key در آبجکت تو در تو
// function findValueInNestedObject(obj, key) {
//   if (typeof obj !== "object" || obj === null) return undefined;

//   if (key in obj) {
//     return obj[key];
//   }

//   // جستجوی در شیء‌های تو در تو
//   for (const k in obj) {
//     let result = findValueInNestedObject(obj[k], key);
//     if (result !== undefined) {
//       return result;
//     }
//   }

//   return undefined;
// }

function updateJsonKey(oldData, newData) {
  // اگر کلید وجود نداشته باشد یا مقدار جدید با مقدار قبلی یکسان باشد، مقدار قدیمی را برگردان
  if (UpdateJsons) {
    oldData = newData;
  } else {
    console.log(oldData, tabName);

    oldData[tabName] = {}; // reset old data values to set new values.
    Object.keys(newData).forEach((key) => {
      // for each of newData keys, set a key for oldData and set its value to newData's value
      oldData[tabName][key] = newData[key];
    });
  }
  return oldData;
}
// ---------------------------------VersioningModuleHere----------------------------------------
let VersionSaveBtn = document.getElementById("SaveVersion")
VersionSaveBtn.addEventListener("click", () => {
  let VersionInput = document.getElementById("Version").value
  let CommentInput = document.getElementById("comments").value
  VersioningModule(VersionInput, CommentInput)
})