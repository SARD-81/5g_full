import { auth_name, setAuthName } from "./auth.js";
import useApi from "./useApi.js";
import Toastify from "toastify-js";
import IconBBDH from "../img/logo white with logo type.png";
import favIconBBDH from "../img/logo white.png";
import IconMCI from "../img/logo-mci-02 (1).svg";
import IconBBU from "../img/image.png";
import favIconMCI from "../img/logo-mci-02 (1).svg";
import imageZabbix from "../img/Monitor-cuate.svg";
import imageElk from "../img/Monitor-cuate.svg";
import imageSubscribers from "../img/Monitor-cuate.svg";
import "toastify-js/src/toastify.css";
import { data, error } from "jquery";
import Stepper from "bs-stepper";
import "bs-stepper/dist/css/bs-stepper.min.css";

let project = import.meta.env.VITE_API_PROJECT;

if (project == "BBDH") {
  document.getElementById("favIcon").href = favIconBBDH;
  document.getElementById("divInputServerId")?.remove();
  document.getElementById("divInputEditServerId")?.remove();
  document.getElementById("facepalte")?.remove();
  document.getElementById("v-facePlate")?.remove();
  document.getElementById("tabRRU")?.remove();
  document.getElementById("v-logManagement")?.remove();
  document.getElementById("v-performanceManagement")?.remove();
} else if (project == "MCI") {
  document.getElementById("logo").src = IconMCI;
  document.getElementById("favIcon").href = favIconMCI;
  document.getElementById("divInputServerId")?.remove();
  document.getElementById("divInputEditServerId")?.remove();
  document.getElementById("facepalte")?.remove();
  document.getElementById("v-facePlate")?.remove();
} else if (project == "RRU") {
  document.getElementById("v-pills-tab")?.remove();
  document.getElementById("offcanvasWithBothOptions").style.display = "none";
  document.querySelector(".iconMenuHamber").style.display = "none";
  document.getElementById("favIcon").href = favIconBBDH;
  document.getElementById("tabPing")?.remove();
  document.getElementById("v-ping")?.remove();
  document.getElementById("divPhoneNumber")?.remove();
  document.getElementById("divPhoneNumberEdit")?.remove();
  document.getElementById("menuSubscribers")?.remove();
  document.getElementById("v-subscribers")?.remove();
  document.getElementById("tabTrace")?.remove();
  document.getElementById("v-trace")?.remove();
  document.getElementById("tabRoute")?.remove();
  document.getElementById("v-route")?.remove();
  document.getElementById("v-monitoring")?.remove();
  document.querySelector(".bgSpecifications").style.display = "none";
  const elements = document.querySelectorAll(".replace-server");

  elements.forEach((el) => {
    el.innerHTML = el.innerHTML.replace(/\bservers?\b/gi, "BBU");
  });
}

if (window.innerWidth < 992) {
  // document.getElementById("idSidebar")?.remove();
  document.querySelector(".divPing")?.classList.remove("w-50");
}

if (window.innerWidth < 768) {
  document.getElementById("idSidebar")?.remove();
  document.querySelector(".divPing")?.classList.remove("w-50");
  document.querySelector(".divInputMonitoring")?.classList.remove("d-flex");
  document.querySelector(".divInputMonitoringELK")?.classList.remove("d-flex");
  document.querySelector(".divButtonsMonitoring")?.classList.add("d-flex");
  document.querySelector(".divButtonsMonitoringELK")?.classList.add("d-flex");
  document.getElementById("url_input_a")?.classList.remove("w-50");
  document.getElementById("url_input_b")?.classList.remove("w-50");
  document.getElementById("buttonOpenIframe1")?.classList.remove("ms-3");
  document.getElementById("buttonOpenIframe2")?.classList.remove("ms-3");
  document.querySelector(".divServerIp")?.classList.remove("d-flex");
  document.querySelector(".divInputServerIp")?.classList.remove("w-75");
  document.getElementById("inputVmIp")?.classList.remove("w-50");
  document.getElementById("inputVmIp")?.classList.add("ms-0");
}

if (project == "BBDH") {
  if (window.innerWidth <= 992 && window.innerWidth >= 768) {
    let circle = document.getElementById("circleOne");
    if (circle) {
      circle.setAttribute("cx", "5");
      circle.setAttribute("cy", "5");
      circle.setAttribute("r", "5");
    }
    let circleTwo = document.getElementById("circleTwo");
    if (circleTwo) {
      circleTwo.setAttribute("cx", "5");
      circleTwo.setAttribute("cy", "5");
      circleTwo.setAttribute("r", "5");
    }
  }

  if (window.innerWidth <= 1200 && window.innerWidth >= 992) {
    let circle = document.getElementById("circleOne");
    circle.setAttribute("cx", "7");
    circle.setAttribute("cy", "7");
    circle.setAttribute("r", "7");
    let circleTwo = document.getElementById("circleTwo");
    circleTwo.setAttribute("cx", "7");
    circleTwo.setAttribute("cy", "7");
    circleTwo.setAttribute("r", "7");
  }
}

if (window.innerWidth > 768 || window.innerWidth == 768) {
  document.querySelector(".divMenuHumber")?.remove();
  document.querySelector(".iconMenuHamber")?.remove();
}

if (window.innerWidth > 470 && window.innerWidth < 576) {
  document.getElementById("tableResponsive").style.width = `${window.innerWidth - 30
    }px`;
  document.getElementById("tableResponsiveUser").style.width = `${window.innerWidth - 30
    }px`;
  document.getElementById("tableResponsiveLog").style.width = `${window.innerWidth - 30
    }px`;
}

if (window.innerWidth <= 470) {
  document.getElementById("buttonOpenIframe1").style.fontSize = "13px";
  document.getElementById("buttonOpenIframe2").style.fontSize = "13px";
  // document.getElementById("buttonIframe1").style.fontSize = "13px";
  // document.getElementById("buttonIframe2").style.fontSize = "13px";
  // انتخاب تمام divهای داخل divPing که کلاس‌های مورد نظر را دارند
  const targetDivs = document.querySelectorAll(
    ".divPing div.d-flex.justify-content-between.align-items-center"
  );
  // حذف تمام کلاس‌ها از هر کدام از این divها
  targetDivs.forEach((div) => {
    div.className = ""; // این خط تمام کلاس‌ها را پاک می‌کند
    // یا اگر می‌خواهید فقط کلاس‌های خاصی را حذف کنید:
    // div?.classList.remove('d-flex', 'justify-content-between', 'align-items-center');
  });
}

if (project == "BBDH") {
  document.getElementById("backLogo")?.classList.remove("backLogo");
  document.getElementById("logo").src = IconBBDH;
  document.getElementById("logo")?.classList.remove("w-75");
  document.getElementById("logo")?.classList.remove("my-3");
  document.getElementById("logo").style.width = "100%";
  document.getElementById("logo").style.margin = "0";
} else if (project == "MCI") {
  document.getElementById("logo").src = IconMCI;
} else if (project == "RRU") {
  document.getElementById("backLogo")?.classList.remove("backLogo");
  document.getElementById("logo").src = IconBBU;
  document.getElementById("logo")?.classList.remove("w-75");
  document.getElementById("logo")?.classList.remove("my-3");
  document.getElementById("logo").style.width = "90%";
  document.getElementById("logo").style.margin = "0";
}

let arrForActiveSubMenu = [1];

function forSubMneu() {
  for (let i = 1; i <= arrForActiveSubMenu.length; i++) {
    document.querySelector(".subMenu" + i)?.classList.remove("clickSubMenu");
  }
}

let arrForActiveMenu;

arrForActiveMenu = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];

if (project == "RRU") {
  arrForActiveMenu = arrForActiveMenu.filter((item) => item != 3);
  arrForActiveMenu = arrForActiveMenu.filter((item) => item != 6);
  arrForActiveMenu = arrForActiveMenu.filter((item) => item != 7);
  arrForActiveMenu = arrForActiveMenu.filter((item) => item != 8);
  arrForActiveMenu = arrForActiveMenu.filter((item) => item != 10);
  arrForActiveMenu = arrForActiveMenu.filter((item) => item != 11);
} else {
  arrForActiveMenu = arrForActiveMenu.filter((item) => item != 9);
  arrForActiveMenu = arrForActiveMenu.filter((item) => item != 12);
  arrForActiveMenu = arrForActiveMenu.filter((item) => item != 13);
}

function forActiveMenu() {
  if (project == "BBDH") {
    for (let i = 1; i <= arrForActiveMenu.length; i++) {
      document
        .querySelector(".menus" + arrForActiveMenu[i - 1])
        ?.classList.remove("activeMenu");
    }
  } else {
    for (let i = 1; i <= arrForActiveMenu.length; i++) {
      document
        .querySelector(".menus" + arrForActiveMenu[i - 1])
        ?.classList.remove("activeMenuRRU");
    }
  }
}

document
  .querySelector("#v-servers-tab")
  .addEventListener("shown.bs.tab", function () {
    if (project == "RRU") {
      document.getElementById("containerLTE").classList.add("d-none");
      document.getElementById("containerGSM").classList.add("d-none");
      document.getElementById("moduleENB").classList.add("d-none");
      document.getElementById("moduleRR").classList.add("d-none");
      document.getElementById("moduleSIB").classList.add("d-none");
      document.getElementById("moduleBSC").classList.add("d-none");
      document.getElementById("containerBBU").classList.add("d-none");
      document.querySelector(".dashboard-content").classList.remove("d-none");
      document
        .getElementById("menuSettingRRU")
        ?.classList.remove("activeMenuRRU");
      document
        .getElementById("menuFaultsRRU")
        ?.classList.remove("activeMenuRRU");
    }
    if (document.getElementById("closeOffcanvas")) {
      document.getElementById("closeOffcanvas").click();
    }
    whichTab = "servers";
    const id = this.dataset.id; // گرفتن id از data-id
    ActiveMenu(id); // فراخوانی تابع با id مربوطه
    if (requestShowAllServerOne) {
      showAllServer();
      permissionShow();
    }
  });

if (document.getElementById("v-trace")) {
  document
    .querySelector("#v-trace-tab")
    .addEventListener("shown.bs.tab", async function () {
      if (document.getElementById("closeOffcanvas")) {
        document.getElementById("closeOffcanvas").click();
      }
      whichTab = "trace";
      const id = this.dataset.id; // گرفتن id از data-id
      ActiveMenu(id); // فراخوانی تابع با id مربوطه
      if (serverCard == undefined) {
        await showAllServer();
      }
      if (modulesInfo.length == 0) {
        await showModuls();
      }
      showNameServerTrace();
    });
}

let kpiStatus = true;

if (document.getElementById("v-kpi")) {
  document
    .querySelector("#v-kpi-tab")
    .addEventListener("shown.bs.tab", async function () {
      if (document.getElementById("closeOffcanvas")) {
        document.getElementById("closeOffcanvas").click();
      }
      whichTab = "kpi";
      const id = this.dataset.id; // گرفتن id از data-id
      ActiveMenu(id); // فراخوانی تابع با id مربوطه
      if (kpiStatus) {
        indexKpi();
      }
    });
}

if (document.getElementById("v-route")) {
  document
    .querySelector("#v-route-tab")
    .addEventListener("shown.bs.tab", async function () {
      if (document.getElementById("closeOffcanvas")) {
        document.getElementById("closeOffcanvas").click();
      }
      whichTab = "route";
      const id = this.dataset.id; // گرفتن id از data-id
      ActiveMenu(id); // فراخوانی تابع با id مربوطه
      if (serverCard == undefined) {
        await showAllServer();
        if (serverCard !== undefined) {
          showNameServerRoute();
        }
      } else {
        showNameServerRoute();
      }
    });
}

if (document.getElementById("v-ping")) {
  document
    .querySelector("#v-ping-tab")
    .addEventListener("shown.bs.tab", async function () {
      if (document.getElementById("closeOffcanvas")) {
        document.getElementById("closeOffcanvas").click();
      }
      whichTab = "ping";
      const id = this.dataset.id; // گرفتن id از data-id
      ActiveMenu(id); // فراخوانی تابع با id مربوطه
      document.getElementById("ipServerPing").value = "";
      document.getElementById("interfaceServerPing").value = "";
      // document.getElementById("usernamePing").value = "";
      // document.getElementById("passwordPing").value = "";
      if (serverCard == undefined) {
        await showAllServer();
        if (serverCard !== undefined) {
          showServerTabPing();
        }
      } else {
        showServerTabPing();
      }
      document.getElementById("idLoading").style.display = "none";
      // document.getElementById("portPing").value = localStorage.getItem("port");
    });
}

function showServerTabPing() {
  document.getElementById("serverIdPing").innerHTML = "";
  for (let i = 0; i < serverCard.length; i++) {
    let option = document.createElement("option");
    option.innerHTML = serverCard[i].name;
    option.value = serverCard[i].id;
    document.getElementById("serverIdPing").appendChild(option);
  }
}

if (project == "BBDH") {
  document
    .querySelector("#V-monitoring-tab")
    .addEventListener("shown.bs.tab", function () {
      if (document.getElementById("closeOffcanvas")) {
        document.getElementById("closeOffcanvas").click();
      }
      document.getElementById("Elk")?.classList.remove("active");
      document.getElementById("zabbix")?.classList.add("active");
      whichTab = "monitoring";
      const id = this.dataset.id; // گرفتن id از data-id
      ActiveMenu(id); // فراخوانی تابع با id مربوطه
      if (requestShowAddress) {
        allMonitoring();
        showAddress();
      }
      requestAnimationFrame(() => {
        // غیر فعال کردن تمام تب‌ها
        document
          .querySelectorAll("#monitoringTabs .nav-link")
          .forEach((btn) => {
            btn.classList.remove("active");
          });

        // غیر فعال کردن تمام tab-pane ها
        document
          .querySelectorAll("#monitoringTabContent .tab-pane")
          .forEach((pane) => {
            pane.classList.remove("show", "active");
          });

        // گرفتن اولین تب و اولین محتوا
        const firstTabButton = document.querySelector(
          "#monitoringTabs .nav-link"
        );
        const firstTabPane = document.querySelector(
          "#monitoringTabContent .tab-pane"
        );

        // فعال کردن اولین تب
        if (firstTabButton) firstTabButton.classList.add("active");
        if (firstTabPane) firstTabPane.classList.add("show", "active");
      });
    });

  // document
  //   .querySelector("#v-access-levels-tab")
  //   .addEventListener("shown.bs.tab", function () {
  //     if (document.getElementById("closeOffcanvas")) {
  //       document.getElementById("closeOffcanvas").click();
  //     }
  //     whichTab = "accessLevel";
  //     const id = this.dataset.id; // گرفتن id از data-id
  //     subMenu(id); // فراخوانی تابع با id مربوطه
  //   });    --------------------------------------------------deleting accessUser submenu Part----------------------
}

document.getElementById("buttonLogout").addEventListener("click", function () {
  logout();
});

document
  .querySelector("#v-module-tab")
  .addEventListener("shown.bs.tab", async function () {
    if (project == "RRU") {
      document
        .getElementById("menuFaultsRRU")
        ?.classList.remove("activeMenuRRU");
      document
        .getElementById("menuSettingRRU")
        ?.classList.remove("activeMenuRRU");
      document.getElementById("menuSettingRRU")?.classList.add("activeMenuRRU");
    }
    const id = this.dataset.id; // گرفتن id از data-id
    ActiveMenu(id); // فراخوانی تابع با id مربوطه
    if (serverCard == undefined) {
      await showAllServer();
    }
    if (document.getElementById("closeOffcanvas")) {
      document.getElementById("closeOffcanvas").click();
    }
    whichTab = "module";
    if (requestShowModule) {
      if (urlModule == 0) {
        urlModule = 1;
      }
      showModuls(urlModule);
    }
    if (requestSchuleing && !scheduleModule) {
      getScheduleingModules();
    }
    let lengthTbody3 = document.getElementById("tBody3").rows.length;
    if (!lengthTbody3) {
      document.getElementById("divImgModule").style.display = "block";
    } else {
      document.getElementById("divImgModule").style.display = "none";
    }
    ChangingTheDisplayOfModuleServers();
  });

if (document.getElementById("v-subscribers-tab")) {
  document
    .querySelector("#v-subscribers-tab")
    .addEventListener("shown.bs.tab", function () {
      if (document.getElementById("closeOffcanvas")) {
        document.getElementById("closeOffcanvas").click();
      }
      whichTab = "subscribers";
      const id = this.dataset.id; // گرفتن id از data-id
      ActiveMenu(id); // فراخوانی تابع با id مربوطه
      if (getSub) {
        getSubscribers();
      }
    });
}

let getSub = true;

async function getSubscribers() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  getSub = false;
  await useApi({
    url: "get-subscriber-address",
    callback: function (data) {
      document.getElementById("urlSubscribers").value = data.subscriber_address;
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 1)";
}

if (document.getElementById("v-logManagement-tab")) {
  document
    .querySelector("#v-logManagement-tab")
    .addEventListener("shown.bs.tab", function () {
      if (project == "RRU") {
        document
          .getElementById("menuSettingRRU")
          ?.classList.remove("activeMenuRRU");
        document
          .getElementById("menuFaultsRRU")
          ?.classList.remove("activeMenuRRU");
        document
          .getElementById("menuFaultsRRU")
          ?.classList.add("activeMenuRRU");
      }
      if (document.getElementById("closeOffcanvas")) {
        document.getElementById("closeOffcanvas").click();
      }
      whichTab = "logManagement";
      const id = this.dataset.id; // گرفتن id از data-id
      ActiveMenu(id); // فراخوانی تابع با id مربوطه
    });
}

if (document.getElementById("v-performanceManagement-tab")) {
  document
    .querySelector("#v-performanceManagement-tab")
    .addEventListener("shown.bs.tab", function () {
      if (project == "RRU") {
        document
          .getElementById("menuSettingRRU")
          ?.classList.remove("activeMenuRRU");
        document
          .getElementById("menuFaultsRRU")
          ?.classList.remove("activeMenuRRU");
        document
          .getElementById("menuFaultsRRU")
          ?.classList.add("activeMenuRRU");
      }
      if (document.getElementById("closeOffcanvas")) {
        document.getElementById("closeOffcanvas").click();
      }
      whichTab = "performanceManagement";
      const id = this.dataset.id; // گرفتن id از data-id
      ActiveMenu(id); // فراخوانی تابع با id مربوطه
    });
}

let tabSetting = true;
if (document.getElementById("v-setting-tab")) {
  document
    .querySelector("#v-setting-tab")
    .addEventListener("shown.bs.tab", function () {
      whichTab = "setting";
      const id = this.dataset.id; // گرفتن id از data-id
      ActiveMenu(id); // فراخوانی تابع با id مربوطه

      if (tabSetting) {
        if (document.getElementById("closeOffcanvas")) {
          document.getElementById("closeOffcanvas").click();
        }

        if (requestShowSetting) {
          loginSMS();
          configBackup();
          // historyBackup();
          reCaptcha();
          if (roleUserGetMe == "admin") {
            configSMS();
          }
          // serverIP();
          getConnectionReCaptcha();
          twoFAstatus();
        }
        tabSetting = false;
      }
      document.getElementById("backupTab")?.classList.remove("active");
      document.getElementById("serverTab")?.classList.remove("active");
      document.getElementById("smsTab")?.classList.add("active");
      requestAnimationFrame(() => {
        document.getElementById("tabSms")?.classList.add("show");
        document.getElementById("tabSms")?.classList.add("active");
      });
    });
}

if (document.getElementById("v-facePlate-tab")) {
  document
    .querySelector("#v-facePlate-tab")
    .addEventListener("shown.bs.tab", function () {
      if (project == "RRU") {
        document
          .getElementById("menuSettingRRU")
          ?.classList.remove("activeMenuRRU");
        document
          .getElementById("menuFaultsRRU")
          ?.classList.remove("activeMenuRRU");
      }
      if (document.getElementById("closeOffcanvas")) {
        document.getElementById("closeOffcanvas").click();
      }
      whichTab = "facePlate";
      const id = this.dataset.id; // گرفتن id از data-id
      ActiveMenu(id); // فراخوانی تابع با id مربوطه
    });
}

let changeShowServerModule = "";

let nameMapping = [];

function ChangingTheDisplayOfModuleServers() {
  let lengthTbody3 = document.getElementById("tBody3").rows.length;

  let numbersToKeep = [];
  let idToKeep = [];

  // ابتدا آرایه numbersToKeep را با نام‌های serverCard پر کنید
  if (serverCard) {
    for (let x = 0; x < serverCard.length; x++) {
      numbersToKeep.push(serverCard[x].name);
    }

    for (let x = 0; x < serverCard.length; x++) {
      idToKeep.push(serverCard[x].id);
    }
  }

  modulesInfo.forEach((item) => {
    if (item.serverIDs && item.serverIDs.length > 0) {
      item.serverIDs = item.serverIDs.filter((id) => idToKeep.includes(id));
    }
  });

  if (changeShowServerModule == "removeServer") {
    // حالا برای هر سطر در tBody3
    for (let i = 1; i <= lengthTbody3; i++) {
      // انتخاب تمام تگ‌های <span> در سطر فعلی
      let spans = document.querySelector(`#tr${i} .td3 span`);
      // اگر تگ <span> وجود داشت
      if (spans) {
        // دریافت محتوای داخل تگ <span>
        let content = spans.textContent.trim();
        // اگر محتوا خالی نبود
        if (content) {
          // جدا کردن مقادیر با استفاده از کاما و تبدیل به آرایه
          let values = content.split(",").map(function (val) {
            return val.trim(); // حذف فاصله‌های اضافی
          });
          // فیلتر کردن مقادیر: فقط مقادیری که در numbersToKeep هستند نگه دار
          let filteredValues = values.filter(function (val) {
            return numbersToKeep.includes(val); // فقط مقادیری که در لیست numbersToKeep هستند نگه دار
          });
          // تبدیل آرایه به رشته‌ی جدید با کاما
          let newContent = filteredValues.join(", ");
          if (!newContent) {
            // به‌روزرسانی محتوای تگ <span>
            spans.textContent = "_";
          } else {
            spans.textContent = newContent;
          }
        }
      }
    }
  } else if (changeShowServerModule == "editServer") {
    for (let i = 1; i <= lengthTbody3; i++) {
      // انتخاب تمام تگ‌های <span> در سطر فعلی
      let spans = document.querySelector(`#tr${i} .td3 span`);
      // اگر تگ <span> وجود داشت
      if (spans) {
        // دریافت محتوای داخل تگ <span>
        let content = spans.textContent.trim();
        // اگر محتوا خالی نبود
        if (content) {
          // جدا کردن مقادیر با استفاده از کاما و تبدیل به آرایه
          let values = content.split(",").map(function (val) {
            return val.trim(); // حذف فاصله‌های اضافی
          });

          // بررسی و به‌روزرسانی نام‌ها با استفاده از nameMapping
          let updatedValues = values.map(function (val) {
            // جستجو در nameMapping برای یافتن نام جدید
            let mapping = nameMapping.find((item) => item.oldName === val);
            // اگر نام قدیمی پیدا شد، نام جدید را برگردان
            if (mapping) {
              return mapping.newName;
            }
            // در غیر این صورت، نام قدیمی را برگردان
            return val;
          });

          // فیلتر کردن مقادیر: فقط مقادیری که در numbersToKeep هستند نگه دار
          let filteredValues = updatedValues.filter(function (val) {
            return numbersToKeep.includes(val); // فقط مقادیری که در لیست numbersToKeep هستند نگه دار
          });

          // تبدیل آرایه به رشته‌ی جدید با کاما
          let newContent = filteredValues.join(", ");

          if (!newContent) {
            // به‌روزرسانی محتوای تگ <span>
            spans.textContent = "_";
          } else {
            spans.textContent = newContent;
          }
          // به‌روزرسانی محتوای تگ <span>
          // spans.textContent = newContent;
        }
      }
    }
  }
}

function ActiveMenu(x) {
  if (whichTab != "log") {
    removeUrlLogParams();
  }
  if (whichTab != "module") {
    removeUrlModuleParams();
  }
  if (whichTab != "accessLevel") {
    removeUrlUserParams();
  }
  forActiveMenu();
  if (project == "BBDH") {
    forSubMneu();
    iconDisplay();
  }
  if (project == "BBDH") {
    document.querySelector(".menus" + x)?.classList.add("activeMenu");
  } else {
    document.querySelector(".menus" + x)?.classList.add("activeMenuRRU");
  }
}

function iconDisplay() {
  for (let i = 1; i <= arrForActiveSubMenu.length; i++) {
    document.querySelector(".iconSubMenu" + i).style.display = "none";
  }
}

function subMenu(x) {
  removeUrlLogParams();
  removeUrlModuleParams();
  forActiveMenu();
  forSubMneu();
  iconDisplay();
  if (x == 1 || x == 2) {
    document.querySelector(".menus2")?.classList.add("activeMenu");
  } else {
    document.querySelector(".menus6")?.classList.add("activeMenu");
  }
  document.querySelector(".subMenu1")?.classList.add("clickSubMenu");
  document.querySelector(".iconSubMenu1").style.display = "flex";
  return;
}
/// users ///
let passwordTF = false;

let showPasswordAddUser = document.getElementById("showPasswordAddUser");
showPasswordAddUser.addEventListener("click", function () {
  showPasswords();
});

function showPasswords() {
  if (!passwordTF) {
    document.getElementById("passwordAddUser").type = "text";
    document.getElementById("repeatPasswordAddUser").type = "text";
    passwordTF = true;
  } else {
    document.getElementById("passwordAddUser").type = "password";
    document.getElementById("repeatPasswordAddUser").type = "password";
    passwordTF = false;
  }
}

let passwordTF2 = false;

let showPasswordEditUser = document.getElementById("showPasswordEditUser");
showPasswordEditUser.addEventListener("click", function () {
  showPasswordsEditUser();
});

function showPasswordsEditUser() {
  if (!passwordTF2) {
    document.getElementById("passwordEditUser").type = "text";
    document.getElementById("repeatPasswordEditUser").type = "text";
    passwordTF2 = true;
  } else {
    document.getElementById("passwordEditUser").type = "password";
    document.getElementById("repeatPasswordEditUser").type = "password";
    passwordTF2 = false;
  }
}

// let passwordTF3 = false;

// let showPasswordShowConfig = document.getElementById("showPasswordShowConfig");
// showPasswordShowConfig.addEventListener("click", function () {
//   showPasswordsShowConfig();
// });

// function showPasswordsShowConfig() {
//   if (!passwordTF3) {
//     document.getElementById("passwordShowConfig").type = "text";
//     passwordTF3 = true;
//   } else {
//     document.getElementById("passwordShowConfig").type = "password";
//     passwordTF3 = false;
//   }
// }

let passwordTF4 = false;

let showPasswordDeletServer = document.getElementById(
  "showPasswordDeletServer"
);
showPasswordDeletServer.addEventListener("click", function () {
  showPasswordsDeletServer();
});

function showPasswordsDeletServer() {
  if (!passwordTF4) {
    document.getElementById("passwordDeletServer").type = "text";
    passwordTF4 = true;
  } else {
    document.getElementById("passwordDeletServer").type = "password";
    passwordTF4 = false;
  }
}

let passwordTF5 = false;

let showPasswordServer = document.getElementById("showPasswordServer");

showPasswordServer.addEventListener("click", function () {
  showPasswordsServer();
});

function showPasswordsServer() {
  if (!passwordTF5) {
    document.getElementById("inputPasswordServer").type = "text";
    passwordTF5 = true;
  } else {
    document.getElementById("inputPasswordServer").type = "password";
    passwordTF5 = false;
  }
}

let permissionAll;

async function permissionShow() {
  await useApi({
    url: "show-all-permission",
    callback: function (data) {
      permissionAll = data.data;
      createChecckBox(permissionAll);
      requestShowPermission = false;
    },
  });
  permissionRoleShowAdd();
}

function createChecckBox(data) {
  if (document.getElementById("showAccessLevel")) {
    document.getElementById("showAccessLevel").innerHTML = "";
  }
  if (document.getElementById("divAccessLevelEdit")) {
    document.getElementById("divAccessLevelEdit").innerHTML = "";
  }

  for (let i = 1; i < data.length; i++) {
    let div = document.createElement("div");
    div.setAttribute("class", "form-check");
    let input = document.createElement("input");
    input.setAttribute("class", "form-check-input accessLevelCheckbox");
    input.setAttribute("type", "checkbox");
    input.setAttribute("id", `selectAccessLevel${i + 1}`);
    input.setAttribute("data-id", data[i]);
    input.setAttribute("required", `required`);
    let lable = document.createElement("label");
    lable.setAttribute("class", "form-check-label");
    lable.setAttribute("for", `selectAccessLevel${i + 1}`);
    lable.innerHTML = `${data[i]}`;
    div.appendChild(lable);
    div.appendChild(input);
    if (document.getElementById("showAccessLevel")) {
      document.getElementById("showAccessLevel").appendChild(div);
    }
  }
  for (let i = 1; i < data.length; i++) {
    let div = document.createElement("div");
    div.setAttribute("class", "form-check");
    let input = document.createElement("input");
    input.setAttribute("class", "form-check-input accessLevelCheckboxEdit");
    input.setAttribute("type", "checkbox");
    input.setAttribute("id", `selectAccessLevelEdit${i + 1}`);
    input.setAttribute("data-id", data[i]);
    input.setAttribute("required", `required`);
    let lable = document.createElement("label");
    lable.setAttribute("class", "form-check-label");
    lable.setAttribute("for", `selectAccessLevelEdit${i + 1}`);
    lable.innerHTML = `${data[i]}`;
    div.appendChild(lable);
    div.appendChild(input);
    if (document.getElementById("divAccessLevelEdit")) {
      document.getElementById("divAccessLevelEdit").appendChild(div);
    }
  }
}

// let changePermission = "";

let visitor;
let expert;
let dataValueID = [];
let NumberOfPermissions = [];
let checkBoxs;
let dataShowAllRole;
let requestLoading = false;
async function permissionRoleShowAdd() {
  await useApi({
    url: "show-all-roles",
    callback: function (data) {
      dataShowAllRole = data.data;
      showAllRole(dataShowAllRole);
    },
  });

  ManagingAccessLevels();
  requestLoading = true;
  document.getElementById("idLoading").style.display = "none";
}

function showAllRole(data) {
  if (data != undefined) {
    visitor = data[1];
    expert = data[2];
    checkBoxs = document.querySelectorAll('input[type="checkbox"][data-id]');
    dataValueID = [];
    checkBoxs.forEach((item) => {
      dataValueID.push(item.getAttribute("data-id"));
    });
    checkBoxs.forEach((el) => {
      let isExpert = expert.permissions.some(
        (expItem) => expItem === el.getAttribute("data-id")
      );
      if (isExpert) {
        el.checked = true;
        el.disabled = false;
      } else {
        el.disabled = false;
      }
    });
  }
}

let checkBoxServer;
let checkBoxServerEdit;

function ManagingAccessLevels() {
  checkBoxServer = [];
  NumberOfPermissions = [];
  document.querySelectorAll(".accessLevelCheckbox").forEach((e) => {
    checkBoxServer.push(e.id);
    NumberOfPermissions.push(e.id);
  });

  checkBoxs.forEach((buttons) => {
    buttons.addEventListener("change", () => {
      let checkBoxModuleRead = document.getElementById("selectAccessLevel5");
      let checkBoxModuleCreate = document.getElementById("selectAccessLevel6");
      let checkBoxModuleUpdate = document.getElementById("selectAccessLevel7");
      let checkBoxModuleDelete = document.getElementById("selectAccessLevel8");
      if (
        checkBoxModuleCreate.checked ||
        checkBoxModuleUpdate.checked ||
        checkBoxModuleDelete.checked
      ) {
        checkBoxModuleRead.checked = true;
      }
      for (let i = 13; i <= checkBoxServer.length + 1; i++) {
        if (document.getElementById("selectAccessLevel" + i).checked)
          checkBoxModuleRead.checked = true;
      }
    });
  });

  checkBoxServerEdit = [];
  document.querySelectorAll(".accessLevelCheckboxEdit").forEach((e) => {
    checkBoxServerEdit.push(e.id);
  });
  checkBoxs.forEach((buttons) => {
    buttons.addEventListener("change", () => {
      let checkBoxModuleRead = document.getElementById(
        "selectAccessLevelEdit5"
      );
      let checkBoxModuleCreate = document.getElementById(
        "selectAccessLevelEdit6"
      );
      let checkBoxModuleUpdate = document.getElementById(
        "selectAccessLevelEdit7"
      );
      let checkBoxModuleDelete = document.getElementById(
        "selectAccessLevelEdit8"
      );
      if (
        checkBoxModuleCreate.checked ||
        checkBoxModuleUpdate.checked ||
        checkBoxModuleDelete.checked
      ) {
        checkBoxModuleRead.checked = true;
      }
      for (let i = 13; i <= checkBoxServer.length + 1; i++) {
        if (document.getElementById("selectAccessLevelEdit" + i).checked)
          checkBoxModuleRead.checked = true;
      }
    });
  });
}

// async function permissionRoleShowEdit() {
//   await useApi({
//     url: "show-all-roles",
//     callback: function (data) {
//       admin = data.data[0];
//       visitor = data.data[1];
//       expert = data.data[2];
//       checkBoxs = document.querySelectorAll('input[type="checkbox"][data-id]');
//       dataValueID = [];
//       checkBoxs.forEach((item) => {
//         dataValueID.push(item.getAttribute("data-id"));
//       });
//       checkBoxs.forEach((el) => {
//         let isExpert = expert.permissions.some(
//           (expItem) => expItem === el.getAttribute("data-id")
//         );
//         if (isExpert) {
//           el.checked = true;
//           el.disabled = false;
//         } else {
//           el.disabled = false;
//         }
//       });
//     },
//   });
// }

function changeSelectAddUser() {
  // ابتدا تمام چک‌باکس‌ها را غیرفعال و لغو انتخاب می‌کنیم
  checkBoxs.forEach((el) => {
    el.checked = false;
    el.disabled = false;
  });

  if (this.value === "visitor") {
    visitor.permissions.forEach((item) => {
      if (dataValueID.includes(item)) {
        checkBoxs.forEach((el) => {
          if (el.getAttribute("data-id") === item) {
            el.checked = true;
          }
        });
      }
    });

    // بررسی و غیرفعال کردن چک‌باکس‌هایی که تیک نخورده‌اند
    checkBoxs.forEach((el) => {
      if (!el.checked) {
        el.disabled = true; // چک‌باکس‌های بدون تیک غیرفعال شوند
      }
    });

    document.getElementById("selectAccessLevel11").disabled = false;
  } else if (this.value === "expert") {
    expert.permissions.forEach((item) => {
      if (dataValueID.includes(item)) {
        checkBoxs.forEach((el) => {
          if (el.getAttribute("data-id") === item) {
            el.checked = true;
            el.disabled = false; // مطمئن شویم چک‌باکس‌های expert غیرفعال نیستند
          }
        });
      }
    });
  }
  // if (document.getElementById("serverIdInputAddUser")) {
  // if (inputServerIdAddUser.value.trim() === "") {
  let numberSelectVm = 2;
  if (document.getElementById("selectAddUser").value != "visitor") {
    for (let i = 1; i <= 3; i++) {
      document.getElementById(
        "selectAccessLevel" + numberSelectVm
      ).checked = true;
      document.getElementById(
        "selectAccessLevel" + numberSelectVm
      ).disabled = false;
      numberSelectVm++;
    }
  } else {
    document.getElementById("selectAccessLevel2").checked = false;
    document.getElementById("selectAccessLevel2").disabled = true;
  }
  document.getElementById("selectAccessLevel11").disabled = false;
  if (document.getElementById("selectAddUser").value != "visitor") {
    document.getElementById("selectAccessLevel9").disabled = false;
    document.getElementById("selectAccessLevel9").checked = true;
    if (project == "BBDH") {
      document.getElementById("selectAccessLevel13").disabled = false;
    } else {
      document.getElementById("selectAccessLevel12").disabled = false;
      document.getElementById("selectAccessLevel10").checked = false;
      document.getElementById("selectAccessLevel10").disabled = false;
    }
  }
  if (project == "BBDH") {
    let numberSelect = 14;
    for (let i = 13; i <= checkBoxServer.length; i++) {
      if (document.getElementById("selectAccessLevel" + numberSelect)) {
        document.getElementById(
          "selectAccessLevel" + numberSelect
        ).checked = true;
        document.getElementById(
          "selectAccessLevel" + numberSelect
        ).disabled = false;
        numberSelect++;
      }
    }
  } else {
    let numberSelect = 13;
    for (let i = 12; i <= checkBoxServer.length; i++) {
      document.getElementById(
        "selectAccessLevel" + numberSelect
      ).checked = true;
      document.getElementById(
        "selectAccessLevel" + numberSelect
      ).disabled = false;
      numberSelect++;
    }
  }

  // --- کنترل 4 چک‌باکس آخر selectAccessLevel ---
  // فقط چک‌باکس‌هایی که id آن‌ها با selectAccessLevel شروع می‌شود
  const allAccess = Array.from(
    document.querySelectorAll("[id^='selectAccessLevel']")
  )
    // حذف آیتم‌هایی که مربوط به selectAccessLevelEdit هستند
    .filter((el) => !el.id.includes("Edit"))
    // تبدیل id به شماره برای مرتب‌سازی صحیح
    .sort((a, b) => {
      const numA = parseInt(a.id.replace("selectAccessLevel", ""));
      const numB = parseInt(b.id.replace("selectAccessLevel", ""));
      return numA - numB;
    });

  // 4 آیتم آخر real
  const last4 = allAccess.slice(-4);

  if (document.getElementById("selectAddUser").value === "visitor") {
    last4.forEach((el) => {
      el.checked = false;
      el.disabled = true;
    });
  } else {
    last4.forEach((el) => {
      el.disabled = false;
    });
  }
}

function changeSelectEditUser() {
  // ابتدا تمام چک‌باکس‌ها را غیرفعال و لغو انتخاب می‌کنیم
  checkBoxs.forEach((el) => {
    el.checked = false;
    el.disabled = false;
  });

  if (this.value === "visitor") {
    visitor.permissions.forEach((item) => {
      if (dataValueID.includes(item)) {
        checkBoxs.forEach((el) => {
          if (el.getAttribute("data-id") === item) {
            el.checked = true;
          }
        });
      }
    });

    // بررسی و غیرفعال کردن چک‌باکس‌هایی که تیک نخورده‌اند
    checkBoxs.forEach((el) => {
      if (!el.checked) {
        el.disabled = true; // چک‌باکس‌های بدون تیک غیرفعال شوند
      }
    });

    document.getElementById("selectAccessLevelEdit11").disabled = false;
  } else if (this.value === "expert") {
    expert.permissions.forEach((item) => {
      if (dataValueID.includes(item)) {
        checkBoxs.forEach((el) => {
          if (el.getAttribute("data-id") === item) {
            el.checked = true;
            el.disabled = false; // مطمئن شویم چک‌باکس‌های expert غیرفعال نیستند
          }
        });
      }
    });
  }
  // if (serverIdInputEditUser.value.trim() === "") {
  let numberSelectVm = 2;
  if (document.getElementById("selectEditUser").value != "visitor") {
    for (let i = 1; i <= 3; i++) {
      document.getElementById(
        "selectAccessLevelEdit" + numberSelectVm
      ).checked = true;
      document.getElementById(
        "selectAccessLevelEdit" + numberSelectVm
      ).disabled = false;
      numberSelectVm++;
    }
  } else {
    document.getElementById("selectAccessLevelEdit2").checked = false;
    document.getElementById("selectAccessLevelEdit2").disabled = true;
  }
  document.getElementById("selectAccessLevelEdit11").disabled = false;
  if (document.getElementById("selectEditUser").value != "visitor") {
    document.getElementById("selectAccessLevelEdit9").disabled = false;
    document.getElementById("selectAccessLevelEdit9").checked = true;
    if (project == "BBDH") {
      document.getElementById("selectAccessLevelEdit13").disabled = false;
    } else {
      document.getElementById("selectAccessLevelEdit10").disabled = false;
      document.getElementById("selectAccessLevelEdit10").checked = true;
      document.getElementById("selectAccessLevelEdit12").disabled = false;
    }
  }
  if (project == "BBDH") {
    let numberSelect = 14;
    for (let i = 13; i <= checkBoxServer.length; i++) {
      if (document.getElementById("selectAccessLevelEdit" + numberSelect)) {
        document.getElementById(
          "selectAccessLevelEdit" + numberSelect
        ).checked = true;
        document.getElementById(
          "selectAccessLevelEdit" + numberSelect
        ).disabled = false;
        numberSelect++;
      }
    }
  } else {
    let numberSelect = 13;
    for (let i = 12; i <= checkBoxServer.length; i++) {
      document.getElementById(
        "selectAccessLevelEdit" + numberSelect
      ).checked = true;
      document.getElementById(
        "selectAccessLevelEdit" + numberSelect
      ).disabled = false;
      numberSelect++;
    }
  }

  // --- کنترل 4 چک‌باکس آخر selectAccessLevelEdit ---
  const allAccessEdit = Array.from(
    document.querySelectorAll("[id^='selectAccessLevelEdit']")
  ).sort((a, b) => {
    const numA = parseInt(a.id.replace("selectAccessLevelEdit", ""));
    const numB = parseInt(b.id.replace("selectAccessLevelEdit", ""));
    return numA - numB;
  });

  const last4Edit = allAccessEdit.slice(-4);

  if (document.getElementById("selectEditUser").value === "visitor") {
    last4Edit.forEach((el) => {
      el.checked = false;
      el.disabled = true;
    });
  } else {
    last4Edit.forEach((el) => {
      el.disabled = false;
    });
  }
}

function changeSelectEditUserVisitor(role) {
  // ابتدا تمام چک‌باکس‌ها را غیرفعال و لغو انتخاب می‌کنیم
  checkBoxs.forEach((el) => {
    el.disabled = true;
  });

  if (role === "visitor") {
    visitor.permissions.forEach((item) => {
      if (dataValueID.includes(item)) {
        checkBoxs.forEach((el) => {
          if (el.getAttribute("data-id") === item) {
            el.disabled = false;
          }
        });
      }
    });

    document.getElementById("selectAccessLevelEdit11").disabled = false;
  } else {
    checkBoxs.forEach((el) => {
      el.disabled = false;
    });
  }
}

document
  .getElementById("selectAddUser")
  .addEventListener("change", changeSelectAddUser);

document
  .getElementById("selectEditUser")
  .addEventListener("change", changeSelectEditUser);

// permissionRoleShowAdd();

let addUserModal = document.getElementById("addUserModal");

addUserModal.addEventListener("click", function () {
  btnAddUser = "";
  document.getElementById("showPasswordAddUser").checked = false;
  passwordTF = true;
  showPasswords();
  document.getElementById("addTableUsers").removeAttribute("data-bs-dismiss");
  document.getElementById("selectAddUser").value = "expert";
  permissionUser = [];
  numberPermission = 0;
  document
    .querySelectorAll(".accessLevelCheckbox")
    .forEach(function (checkBox) {
      checkBox.checked = false;
    });
  if (document.getElementById("serverIdInputAddUser")) {
    selectServerIdAddUser();
  }
  removeInputAddUser();
  showAllRole(dataShowAllRole);
});

function selectServerIdAddUser() {
  document.getElementById("serverIdInputAddUser").innerHTML = "";
  for (let i = 0; i < serverCard.length; i++) {
    let option = document.createElement("option");
    option.innerHTML = serverCard[i].name;
    option.value = serverCard[i].id;
    document.getElementById("serverIdInputAddUser").appendChild(option);
  }
  let option = document.createElement("option");
  option.innerHTML = "Main Server";
  option.value = "";
  document.getElementById("serverIdInputAddUser").appendChild(option);
}

let permissionUser = [];

function permission() {
  document
    .querySelectorAll(".accessLevelCheckbox")
    .forEach(function (checkBox) {
      if (checkBox.checked) {
        let label = document.querySelector(`label[for='${checkBox.id}']`);
        permissionUser.push(label.innerHTML);
      }
    });
}

function permissionEdit() {
  document
    .querySelectorAll(".accessLevelCheckboxEdit")
    .forEach(function (checkBox) {
      if (checkBox.checked) {
        let label = document.querySelector(`label[for='${checkBox.id}']`);
        permissionUser.push(label.innerHTML);
      }
    });
}

// افزودن رویداد 'change' به عنصر

let tableUsers = document.getElementById("addTableUsers");
tableUsers.addEventListener("click", function () {
  if (btnAddUser == "") {
    let lengthNameAddUser = document.getElementById("nameInputAddUser").value;
    let lengthFamilyAddUser =
      document.getElementById("familyInputAddUser").value;
    let lengthAuthNameAddUser = document.getElementById(
      "authNameInputAddUser"
    ).value;
    let checkBox = document.querySelectorAll(".accessLevelCheckbox");
    let lengthPhoneNumber;
    if (document.getElementById("phoneNumber")) {
      lengthPhoneNumber = document.getElementById("phoneNumber").value;
    }

    let lengthPassword = document.getElementById("passwordAddUser").value;
    let lengthRepeatPassword = document.getElementById(
      "repeatPasswordAddUser"
    ).value;

    const isCheckedBox = [...checkBox].some((checkBox) => checkBox.checked);
    let typePhoneNumber = /^\d+$/.test(lengthPhoneNumber);

    if (lengthNameAddUser.length < 3) {
      Toastify({
        text: "The name is less than 3 characters.",
      }).showToast();
    } else if (lengthFamilyAddUser.length < 3) {
      Toastify({
        text: "The last name is less than 3 characters.",
      }).showToast();
    } else if (lengthAuthNameAddUser.length < 3) {
      Toastify({
        text: "The username is less than 3 characters.",
      }).showToast();
    } else if (isCheckedBox == false) {
      Toastify({
        text: "You must select one of the following permissions.",
      }).showToast();
    } else if (
      lengthPhoneNumber &&
      lengthPhoneNumber.length < 11 &&
      project != "RRU"
    ) {
      Toastify({
        text: "The phone number is less than 11 characters.",
      }).showToast();
    } else if (typePhoneNumber == false && project != "RRU") {
      Toastify({
        text: "The entered phone number is not numeric.",
      }).showToast();
    } else if (lengthPassword.length < 8) {
      Toastify({
        text: "The password is less than 8 characters.",
      }).showToast();
    } else if (lengthPassword != lengthRepeatPassword) {
      Toastify({
        text: "The password does not match the repeat field.",
      }).showToast();
    }

    if (project == "RRU") {
      if (
        lengthNameAddUser.length >= 3 &&
        lengthFamilyAddUser.length >= 3 &&
        lengthAuthNameAddUser.length >= 3 &&
        isCheckedBox != false &&
        lengthPassword.length >= 8 &&
        lengthRepeatPassword.length >= 8
      ) {
        if (lengthPassword === lengthRepeatPassword) {
          if (document.getElementById("selectAddUser").value == "visitor") {
            const checkbox5 = document.getElementById("selectAccessLevel5");
            if (checkbox5 && checkbox5.checked) {
              let isValid = true;
              for (let x = 2; x <= NumberOfPermissions.length + 1; x++) {
                if (x === 5 || x >= 13 || x === 11) {
                  continue;
                }
                const checkbox = document.getElementById(
                  "selectAccessLevel" + x
                );
                if (checkbox && checkbox.checked) {
                  isValid = false; // اگر چک‌باکس دیگری انتخاب شده باشد، شرایط نامعتبر است
                  break;
                }
              }
              if (isValid) {
                SubAddUser();
              } else {
                Toastify({
                  text: "This permissions is not related to this role.",
                }).showToast();
              }
            }
          } else {
            SubAddUser();
          }
        }
      }
    } else {
      if (
        lengthNameAddUser.length >= 3 &&
        lengthFamilyAddUser.length >= 3 &&
        lengthAuthNameAddUser.length >= 3 &&
        isCheckedBox != false &&
        lengthPhoneNumber.length == 11 &&
        typePhoneNumber == true &&
        lengthPassword.length >= 8 &&
        lengthRepeatPassword.length >= 8
      ) {
        if (lengthPassword === lengthRepeatPassword) {
          if (document.getElementById("selectAddUser").value == "visitor") {
            const checkbox5 = document.getElementById("selectAccessLevel5");
            if (checkbox5 && checkbox5.checked) {
              let isValid = true;
              for (let x = 2; x <= NumberOfPermissions.length + 1; x++) {
                if (x === 5 || x >= 13 || x === 11) {
                  continue;
                }
                const checkbox = document.getElementById(
                  "selectAccessLevel" + x
                );
                if (checkbox && checkbox.checked) {
                  isValid = false; // اگر چک‌باکس دیگری انتخاب شده باشد، شرایط نامعتبر است
                  break;
                }
              }
              if (isValid) {
                SubAddUser();
              } else {
                Toastify({
                  text: "This permissions is not related to this role.",
                }).showToast();
              }
            }
          } else {
            SubAddUser();
          }
        }
      }
    }
  }
});

let canselAddTableUser = document.getElementById("canselAddTableUser");

canselAddTableUser.addEventListener("click", function () {
  removeInputAddUser();
});

function removeInputAddUser() {
  document.getElementById("nameInputAddUser").value = "";
  document.getElementById("familyInputAddUser").value = "";
  document.getElementById("authNameInputAddUser").value = "";
  if (document.getElementById("serverIdInputAddUser")) {
    document.getElementById("serverIdInputAddUser").value = "";
  }
  if (document.getElementById("phoneNumber")) {
    document.getElementById("phoneNumber").value = "";
  }
  document.getElementById("passwordAddUser").value = "";
  document.getElementById("repeatPasswordAddUser").value = "";
}

const checkbox = document.querySelectorAll(".accessLevelAdd");

let accessLevelUser = [];

// اضافه کردن رویداد کلیک
checkbox.forEach(function (e) {
  e.addEventListener("click", function () {
    const label = document.querySelector(`label[for="${this.dataset.id}"]`);
    const labelValue = label ? label.textContent : null;
    accessLevelUser.push([labelValue]);
  });
});

let btnAddUser = "";
let inputServerIdAddUser;

if (document.getElementById("serverIdInputAddUser")) {
  inputServerIdAddUser = document.getElementById("serverIdInputAddUser");
  inputServerIdAddUser.addEventListener("input", () => {
    if (inputServerIdAddUser.value.trim() === "") {
      let numberSelectVm = 2;
      if (document.getElementById("selectAddUser").value != "visitor") {
        for (let i = 1; i <= 3; i++) {
          document.getElementById(
            "selectAccessLevel" + numberSelectVm
          ).checked = true;
          document.getElementById(
            "selectAccessLevel" + numberSelectVm
          ).disabled = false;
          numberSelectVm++;
        }
      } else {
        document.getElementById("selectAccessLevel2").checked = false;
        document.getElementById("selectAccessLevel2").disabled = true;
      }
      document.getElementById("selectAccessLevel11").disabled = false;
      if (document.getElementById("selectAddUser").value != "visitor") {
        document.getElementById("selectAccessLevel9").disabled = false;
        document.getElementById("selectAccessLevel9").checked = true;
        if (project == "BBDH") {
          document.getElementById("selectAccessLevel13").disabled = false;
        } else {
          document.getElementById("selectAccessLevel10").disabled = false;
          document.getElementById("selectAccessLevel10").checked = false;
          document.getElementById("selectAccessLevel12").disabled = false;
        }
      }
      if (project == "BBDH") {
        let numberSelect = 14;
        for (let i = 13; i <= checkBoxServer.length; i++) {
          document.getElementById(
            "selectAccessLevel" + numberSelect
          ).checked = true;
          document.getElementById(
            "selectAccessLevel" + numberSelect
          ).disabled = false;
          numberSelect++;
        }
      } else {
        let numberSelect = 13;
        for (let i = 12; i <= checkBoxServer.length; i++) {
          document.getElementById(
            "selectAccessLevel" + numberSelect
          ).checked = true;
          document.getElementById(
            "selectAccessLevel" + numberSelect
          ).disabled = false;
          numberSelect++;
        }
      }
    } else {
      let numberSelectVm = 2;
      for (let i = 1; i <= 3; i++) {
        document.getElementById(
          "selectAccessLevel" + numberSelectVm
        ).checked = false;
        document.getElementById(
          "selectAccessLevel" + numberSelectVm
        ).disabled = true;
        numberSelectVm++;
      }
      document.getElementById("selectAccessLevel9").disabled = true;
      document.getElementById("selectAccessLevel9").checked = false;
      document.getElementById("selectAccessLevel11").checked = false;
      document.getElementById("selectAccessLevel11").disabled = true;
      if (project == "BBDH") {
        document.getElementById("selectAccessLevel13").disabled = true;
        let numberSelect = 14;
        for (let i = 13; i <= checkBoxServer.length; i++) {
          document.getElementById(
            "selectAccessLevel" + numberSelect
          ).checked = false;
          document.getElementById(
            "selectAccessLevel" + numberSelect
          ).disabled = true;
          numberSelect++;
        }
      } else {
        document.getElementById("selectAccessLevel10").disabled = true;
        document.getElementById("selectAccessLevel10").checked = false;
        document.getElementById("selectAccessLevel12").disabled = true;
        let numberSelect = 13;
        for (let i = 12; i <= checkBoxServer.length; i++) {
          document.getElementById(
            "selectAccessLevel" + numberSelect
          ).checked = false;
          document.getElementById(
            "selectAccessLevel" + numberSelect
          ).disabled = true;
          numberSelect++;
        }
      }
    }
  });
}

let numberPermission = 0;
async function SubAddUser() {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  let nameInputAddUser = document.getElementById("nameInputAddUser").value;
  let familyInputAddUser = document.getElementById("familyInputAddUser").value;
  let authNameInputAddUser = document.getElementById(
    "authNameInputAddUser"
  ).value;
  let serverIdInputAddUser;
  if (document.getElementById("serverIdInputAddUser")) {
    serverIdInputAddUser = document.getElementById(
      "serverIdInputAddUser"
    ).value;
  }
  let roleInput = document.getElementById("selectAddUser").value;
  let phoneNumberAddUser;
  if (document.getElementById("phoneNumber")) {
    phoneNumberAddUser = document.getElementById("phoneNumber").value;
  }
  let passwordAddUser = document.getElementById("passwordAddUser").value;
  let repeatPasswordAddUser = document.getElementById(
    "repeatPasswordAddUser"
  ).value;

  let formData = new FormData();

  // اضافه کردن مقادیر به FormData
  formData.append("first_name", nameInputAddUser);
  formData.append("last_name", familyInputAddUser);
  formData.append("auth_name", authNameInputAddUser);
  if (document.getElementById("serverIdInputAddUser")) {
    if (serverIdInputAddUser != "") {
      formData.append("server_id", serverIdInputAddUser);
    }
  }
  formData.append("role", roleInput);
  if (project != "RRU") {
    formData.append("phone", phoneNumberAddUser);
  }
  formData.append("password", passwordAddUser);
  formData.append("password_confirmation", repeatPasswordAddUser);

  permission();

  // اضافه کردن آرایه server_ids به‌صورت مقادیر جداگانه
  permissionUser.forEach((id) => {
    formData.append(`permission_name[${numberPermission++}]`, id); // هر مقدار جداگانه اضافه می‌شود
  });

  await useApi({
    method: "post",
    url: "add-member",
    data: formData,
    headers: {
      "Content-Type": "multipart/form-data", // این هدر مهم است
    },
    callback: function (data) {
      let endPageShowUser = Number(arrRowShowAllUser.length) + pageShowUser - 1;
      arrRowShowAllUser.push(data.data.user);

      let tr = document.createElement("tr");
      tr.setAttribute("id", `tr${data.data.user.id}`);
      tr.setAttribute("data-id", `${data.data.user.id}`);

      dataUser.push({
        id: data.data.user.id,
        name: data.data.user.first_name,
        family: data.data.user.last_name,
        authName: data.data.user.auth_name,
        permission: data.data.permission_name,
        role: data.data.role,
        phone: data.data.user.phone,
      });
      for (let x = 1; x <= 8; x++) {
        let td = document.createElement("td");
        td.setAttribute("class", `td${x}`);
        if (x == 1) {
          td.innerHTML = endPageShowUser;
        } else if (x == 2) {
          td.innerHTML = document.getElementById("nameInputAddUser").value;
        } else if (x == 3) {
          td.innerHTML = document.getElementById("familyInputAddUser").value;
        } else if (x == 4) {
          td.innerHTML = document.getElementById("authNameInputAddUser").value;
        } else if (x == 5) {
          let span = document.createElement("span");
          span.innerHTML = document.getElementById("selectAddUser").value;
          td.appendChild(span);
          if (data.data.role == "expert") {
            span.setAttribute("class", "badge text-bg-primary fs-6");
          } else if (data.data.role == "visitor") {
            span.setAttribute("class", "badge text-bg-success fs-6");
          }
        } else if (x == 6) {
          let svgElemMore = `
            <button type="button" class="btn btn-secondary iconAccessLevel"  
             data-bs-toggle="modal"
             data-bs-target="#modalAccessLevel"
             id="${data.data.user.id}"
             data-id="permission_${data.data.user.id}"
            > 
             View Details
             </button>
            `;
          td.insertAdjacentHTML("afterbegin", svgElemMore);
        } else if (x == 7) {
          let svgElemEdit = `<svg
          xmlns="http://www.w3.org/2000/svg"
          width="20"
          height="20"
          fill="currentColor"
          id="${data.data.user.id}"
          data-bs-toggle="modal"
          data-bs-target="#editUserModal"
          class="bi bi-pencil-square iconEditUser cursorPointer"
          viewBox="0 0 16 16"
        >
          <path
            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"
          />
          <path
            fill-rule="evenodd"
            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"
          />
        </svg>`;

          td.insertAdjacentHTML("afterbegin", svgElemEdit);
        } else if (x == 8) {
          let svgElemRemove = `<svg
          xmlns="http://www.w3.org/2000/svg"
          width="20"
          height="20"
          fill="currentColor"
          id="${data.data.user.id}"
          class="bi bi-trash3 removeUser cursorPointer" 
          data-bs-toggle="modal"
          data-bs-target="#removeUserModal"
          viewBox="0 0 16 16"
          >
           <path
             d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"
             />
        </svg>`;

          td.insertAdjacentHTML("afterbegin", svgElemRemove);
        }
        tr.appendChild(td);
      }

      document.getElementById("tBody").appendChild(tr);
      removeInputAddUser();
      showEditUser();
      removeUsers();
      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
      let button = document.getElementById("addTableUsers");
      button.setAttribute("data-bs-dismiss", "modal");
      btnAddUser = "modalAddUser";
      button.click(); // کلیک برنامه‌نویسی
    },
    errorCallback: function () {
      permissionUser = [];
      numberPermission = 0;
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

window.onload = function () {
  document.getElementById("body").style.display = "block";
  load();
};

let clickTF = false;

let requestShowPermission = true;

document
  .querySelector("#v-users-tab")
  .addEventListener("shown.bs.tab", async function () {
    if (project == "RRU") {
      document
        .getElementById("menuSettingRRU")
        ?.classList.remove("activeMenuRRU");
      document
        .getElementById("menuFaultsRRU")
        ?.classList.remove("activeMenuRRU");
    }
    if (document.getElementById("closeOffcanvas")) {
      document.getElementById("closeOffcanvas").click();
    }
    const id = this.dataset.id; // گرفتن id از data-id
    if (project == "BBDH") {
      subMenu(id); // فراخوانی تابع با id مربوطه
    } else {
      ActiveMenu(id);
    }
    if (serverCard == undefined) {
      await showAllServer();
    }
    if (requestShowUser) {
      if (urlUser == 0) {
        urlUser = 1;
      }
      showUsers(urlUser);
    }
    if (requestShowPermission) {
      permissionShow();
    }
  });

let dataUser = [];
let idRemove;
let showUserLoading = false;
let requestShowUser = true;
let rowShowAllUser = 1;
let arrRowShowAllUser = [];
let pageShowUser = 1;
let urlUser;
let paginationUser;
let desiredPageTFuser = true;
let desiredPageUser = 1;
let currentPageUser;

async function showUsers(page = 1) {
  if (roleUserGetMe == "visitor") return;
  if (showUserLoading) return;
  showUserLoading = true;
  requestShowUser = false;
  document.getElementById("idLoading").style.display = "flex";
  let totalPages;
  await useApi({
    url: `show-all-users?paginate=20&page=${page}`,
    callback: function (data) {
      desiredPageTFuser = true;
      arrRowShowAllUser = [];
      rowShowAllUser = 1;
      dataUser = [];
      if (clickTF) {
        document.getElementById("tBody").innerHTML = "";
      }
      clickTF = true;

      let endPage = Math.ceil(data.data.pagination.total / 20);

      let paginationNamber;
      paginationNamber = (page - 1) * 20;
      paginationNamber++;
      pageShowUser = paginationNamber + 1;

      let lengthDataUser = data.data.users.length;

      for (let i = 0; i < lengthDataUser; i++) {
        arrRowShowAllUser.push(data.data.users[i]);
        let tr = document.createElement("tr");
        tr.setAttribute("id", `tr${data.data.users[i].id}`);
        tr.setAttribute("data-id", `${data.data.users[i].id}`);

        dataUser.push({
          id: data.data.users[i].id,
          name: data.data.users[i].first_name,
          family: data.data.users[i].last_name,
          authName: data.data.users[i].auth_name,
          permission: data.data.users[i].permissions,
          role: data.data.users[i].roles[0],
          phone: data.data.users[i].phone,
          server_id: data.data.users[i].server_id,
        });

        for (let x = 1; x <= 8; x++) {
          let td = document.createElement("td");
          td.setAttribute("class", `td${x}`);
          if (x == 1) {
            td.innerHTML = paginationNamber++;
          } else if (x == 2) {
            td.innerHTML = data.data.users[i].first_name;
          } else if (x == 3) {
            td.innerHTML = data.data.users[i].last_name;
          } else if (x == 4) {
            td.innerHTML = data.data.users[i].auth_name;
          } else if (x == 5) {
            let span = document.createElement("span");
            span.innerHTML = data.data.users[i].roles[0];
            td.appendChild(span);
            if (data.data.users[i].roles[0] == "admin") {
              span.setAttribute("class", "badge text-bg-danger fs-6");
            } else if (data.data.users[i].roles[0] == "expert") {
              span.setAttribute("class", "badge text-bg-primary fs-6");
            } else if (data.data.users[i].roles[0] == "visitor") {
              span.setAttribute("class", "badge text-bg-success fs-6");
            }
          } else if (x == 6) {
            let svgElemMore = `
            <button type="button" 
            id="${data.data.users[i].id}" 
            data-id="permission_${data.data.users[i].id}"
            class="btn btn-secondary iconAccessLevel"
             data-bs-toggle="modal"
             data-bs-target="#modalAccessLevel"
             > 
              View Details
             </button>
            `;
            td.insertAdjacentHTML("afterbegin", svgElemMore);
          } else if (x == 7) {
            if (roleUserGetMe != "admin" && data.data.users[i].id == 1) {
              let p = document.createElement("p");
              p.innerHTML = "_";
              td.appendChild(p);
            } else {
              let svgElemEdit = `<svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                fill="currentColor"
                id="${data.data.users[i].id}"
                data-bs-toggle="modal"
                data-bs-target="#editUserModal"
                class="bi bi-pencil-square iconEditUser cursorPointer"
                viewBox="0 0 16 16"
              >
                <path
                  d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"
                />
                <path
                  fill-rule="evenodd"
                  d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"
                />
              </svg>`;

              td.insertAdjacentHTML("afterbegin", svgElemEdit);
            }
          } else if (x == 8) {
            if (data.data.users[i].id != 1) {
              let svgElemRemove = `<svg
          xmlns="http://www.w3.org/2000/svg"
          width="20"
          height="20"
          id="${data.data.users[i].id}"
          fill="currentColor"
          class="bi bi-trash3 removeUser cursorPointer"
          data-bs-toggle="modal"
          data-bs-target="#removeUserModal"
          viewBox="0 0 16 16"
        >
          <path
             d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"
          />
        </svg>`;

              td.insertAdjacentHTML("afterbegin", svgElemRemove);
            } else {
              let p = document.createElement("p");
              p.innerHTML = "_";
              td.appendChild(p);
            }
          }
          tr.appendChild(td);
        }
        document.getElementById("tBody").appendChild(tr);
      }
      if (requestLoading) {
        document.getElementById("idLoading").style.display = "none";
      }
      user(data);

      totalPages = endPage; // Set the total number of pages
      renderPagination(totalPages, currentPageUser);

      function renderPagination(
        totalPages,
        currentPage = !urlUser ? 1 : urlUser
      ) {
        paginationUser = document.getElementById("paginationUser");
        paginationUser.innerHTML = "";

        if (totalPages != 1) {
          // First button
          const firstItem = document.createElement("li");
          firstItem.className = `page-item ${currentPage === 1 ? "disabled" : ""
            }`;
          firstItem.innerHTML = `<a class="page-link" href="#" data-page="1">First Page</a>`;
          paginationUser.appendChild(firstItem);

          // Previous button
          const prevItem = document.createElement("li");
          prevItem.className = `page-item ${currentPage === 1 ? "disabled" : ""
            }`;
          prevItem.innerHTML = `<a class="page-link" href="#" data-page="${currentPage - 1
            }">Previous Page</a>`;
          paginationUser.appendChild(prevItem);

          // Page numbers logic
          const startPage = currentPage === 1 ? currentPage : currentPage - 1;
          const endPage =
            currentPage === totalPages
              ? currentPage
              : Math.min(currentPage + 1, totalPages);

          for (
            let i = Math.max(1, startPage - 1);
            i <= Math.min(totalPages, endPage + 1);
            i++
          ) {
            const pageItem = document.createElement("li");
            pageItem.className = `page-item ${i === currentPage ? "active" : ""
              }`;
            pageItem.innerHTML = `<a class="page-link ${i === currentPage ? "active-page" : ""
              }" href="#" data-page="${i}">${i}</a>`;
            paginationUser.appendChild(pageItem);
          }

          // Next button
          const nextItem = document.createElement("li");
          nextItem.className = `page-item ${currentPage === totalPages ? "disabled" : ""
            }`;
          nextItem.innerHTML = `<a class="page-link" href="#" data-page="${currentPage + 1
            }">Next Page</a>`;
          paginationUser.appendChild(nextItem);

          // Last button
          const lastItem = document.createElement("li");
          lastItem.className = `page-item ${currentPage === totalPages ? "disabled" : ""
            }`;
          lastItem.innerHTML = `<a class="page-link" href="#" data-page="${totalPages}">Last Page</a>`;
          paginationUser.appendChild(lastItem);

          const currentUrl = new URL(window.location);
          currentUrl.searchParams.set("user", !currentPage ? 1 : currentPage); // Set the module in URL
          window.history.pushState({}, "", currentUrl);
        }
      }
    },
  });
  showUserLoading = false;

  function user(data) {
    let lengthUser = data.data.users.length;
    let users = data.data.users;

    let id = [];
    for (let i = 0; i < lengthUser; i++) {
      id[i] = users[i].id;
    }
  }
  showEditUser();
  removeUsers();

  paginationUser.querySelectorAll(".page-link").forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();

      const newPage = parseInt(this.getAttribute("data-page"));
      if (!isNaN(newPage) && newPage > 0 && newPage <= totalPages) {
        if (desiredPageUser == newPage) {
          return;
        }
        if (desiredPageTFuser) {
          desiredPageUser = newPage;
          desiredPageTFuser = false;
        }
        currentPageUser = newPage;
        showUsers(newPage);
        document.querySelector(".paginationUser").innerHTML = "";
        document.getElementById("tBody").innerHTML = "";
      }
    });
  });
}

function removeUsers() {
  let removeUser = document.querySelectorAll(".removeUser");
  removeUser.forEach((element) => {
    element.addEventListener("click", function () {
      let arrTableTr = document.querySelector(
        `#usersTable tbody tr#tr${element.id} .td4`
      );
      document.getElementById("spanRemoveUser").innerHTML =
        arrTableTr.innerHTML;

      idRemove = element.id;
    });
  });
}

let modalRemoveUser = document.getElementById("removeUserModalClick");
modalRemoveUser.addEventListener("click", function () {
  deletUser(idRemove);
});

function showPermissions(level) {
  // همه آیتم‌های دسترسی را انتخاب می‌کنیم
  const permissions = document.querySelectorAll(".permissionsP");

  // پنهان کردن همه آیتم‌ها
  permissions.forEach((permission) => {
    permission.style.display = "none";
  });

  // نمایش موارد بر اساس سطح دسترسی
  permissions.forEach((permission) => {
    const groups = permission.dataset.group.split("|"); // گروه‌ها را جدا می‌کنیم
    if (groups.includes(String(level))) {
      permission.style.display = "block";
    }
  });
}

// function selectServerIdEditUser() {
//   document.getElementById("serverIdInputEditUser").innerHTML = "";
//   for (let i = 0; i < serverCard.length; i++) {
//     let option = document.createElement("option");
//     option.innerHTML = serverCard[i].name;
//     option.value = serverCard[i].id;
//     document.getElementById("serverIdInputEditUser").appendChild(option);
//   }
//   let option = document.createElement("option");
//   option.innerHTML = "Main Server";
//   option.value = "";
//   document.getElementById("serverIdInputEditUser").appendChild(option);
// }

let elementEdit;

function showEditUser() {
  let editUser = document.querySelectorAll(".iconEditUser");
  document.getElementById("passwordEditUser").value = "";

  document.querySelectorAll(".iconAccessLevel").forEach((icon) => {
    icon.addEventListener("click", function () {
      let arrTableTr = document.querySelector(
        `#usersTable tbody tr#tr${icon.id} .td4`
      );

      document.getElementById("accessLevelUser").innerHTML =
        "Access Level " + arrTableTr.innerHTML;

      permissionUser = [];

      document.getElementById("divShowPermission").innerHTML = "";
      const foundUser = dataUser.find((user) => user.id == this.id);
      for (let i = 0; i < foundUser.permission.length; i++) {
        let thisId = foundUser.permission[i];
        let p = document.createElement("p");
        p.innerHTML = thisId;
        document.getElementById("divShowPermission").appendChild(p);
      }
    });
  });

  editUser.forEach((element) => {
    element.addEventListener("click", function () {
      document.getElementById("showPasswordEditUser").checked = false;
      passwordTF2 = true;
      showPasswordsEditUser();

      btnEditUser = "";
      document.getElementById("addEditUser").removeAttribute("data-bs-dismiss");
      if (element.id == 1) {
        document.getElementById("roleEditUser").style.display = "none";
        document.getElementById("divEditUsers").style.display = "none";
      } else {
        document.getElementById("roleEditUser").style.display = "block";
        document.getElementById("divEditUsers").style.display = "block";
      }

      document.getElementById("divShowPermission").innerHTML = "";
      let foundUser = [];
      foundUser = dataUser.find((user) => user.id == this.id);

      for (let x = 0; x < foundUser.permission.length; x++) {
        document.querySelectorAll("#divEditUsers input").forEach((checkbox) => {
          checkbox.checked = false;
        });
      }

      for (let i = 0; i < foundUser.permission.length; i++) {
        let thisId = foundUser.permission[i];

        document
          .querySelectorAll(
            "#divAccessLevelEdit .form-check .accessLevelCheckbox"
          )
          .forEach((checkbox) => {
            let label = document.querySelector(`label[for='${checkbox.id}']`);
            if (thisId === label.innerHTML) {
              let p = document.createElement("p");
              p.innerHTML = thisId;
              document.getElementById("divShowPermission").appendChild(p);
              checkbox.checked = true; // اگر پیدا شد، انتخاب شود
            }
          });

        document
          .querySelectorAll(
            "#divAccessLevelEdit .form-check .accessLevelCheckboxEdit"
          )
          .forEach((checkbox) => {
            let label = document.querySelector(`label[for='${checkbox.id}']`);
            if (thisId === label.innerHTML) {
              let p = document.createElement("p");
              p.innerHTML = thisId;
              document.getElementById("divShowPermission").appendChild(p);
              checkbox.checked = true; // اگر پیدا شد، انتخاب شود
            }
          });
      }
      elementEdit = element;
      // if (document.getElementById("serverIdInputEditUser")) {
      //   selectServerIdEditUser();
      // }

      let findEditUser = dataUser.find((item) => {
        return item.id == element.id;
      });

      if (findEditUser != -1) {
        document.querySelector('input[name="name_nameInputEditUser"]').value =
          findEditUser.name;
        document.querySelector('input[name="nameFamilyInputEditUser"]').value =
          findEditUser.family;
        if (project != "RRU") {
          document.querySelector(
            'input[name="namePhoneNumberEditUser"]'
          ).value = findEditUser.phone;
        }
        document.querySelector('input[name="namePasswordEditUser"]').value = "";

        if (document.getElementById("serverIdInputEditUser")) {
          let matched = false;
          const selectElement = document.getElementById(
            "serverIdInputEditUser"
          );
          for (let option of selectElement.options) {
            if (
              option.value == findEditUser.server_id ||
              (option.value == "" && findEditUser.server_id == null)
            ) {
              option.selected = true;
              matched = true;
              break;
            }
          }

          if (!matched) {
            for (let option of selectElement.options) {
              if (option.value === "") {
                option.selected = true;
                break;
              }
            }
          }
        }
        document.querySelector('input[name="myInputRepeatPassword"]').value =
          "";
        document.querySelector(
          'input[name="nameAuthNameInputEditUser"]'
        ).value = findEditUser.authName;
        document.getElementById("selectEditUser").value = findEditUser.role;
        changeSelectEditUserVisitor(findEditUser.role);

        // if (document.getElementById("serverIdInputEditUser")) {
        // if (serverIdInputEditUser.value.trim() === "") {
        // let numberSelectVm = 2;
        // if (document.getElementById("selectEditUser").value != "visitor") {
        //   for (let i = 1; i <= 3; i++) {
        //     document.getElementById(
        //       "selectAccessLevelEdit" + numberSelectVm
        //     ).checked = true;
        //     document.getElementById(
        //       "selectAccessLevelEdit" + numberSelectVm
        //     ).disabled = false;
        //     numberSelectVm++;
        //   }
        // } else {
        //   document.getElementById("selectAccessLevelEdit2").checked = false;
        //   document.getElementById("selectAccessLevelEdit2").disabled = true;
        // }
        // document.getElementById("selectAccessLevelEdit11").disabled = false;
        // if (document.getElementById("selectEditUser").value != "visitor") {
        //   document.getElementById("selectAccessLevelEdit9").disabled = false;
        //   document.getElementById("selectAccessLevelEdit9").checked = true;
        //   if (project == "BBDH") {
        //     document.getElementById("selectAccessLevelEdit13").disabled = false;
        //   } else {
        //     document.getElementById("selectAccessLevelEdit10").disabled = false;
        //     document.getElementById("selectAccessLevelEdit10").checked = true;
        //     document.getElementById("selectAccessLevelEdit12").disabled = false;
        //   }
        // }
        // if (project == "BBDH") {
        //   let numberSelect = 14;
        //   for (let i = 13; i <= checkBoxServer.length; i++) {
        //     document.getElementById(
        //       "selectAccessLevelEdit" + numberSelect
        //     ).checked = true;
        //     document.getElementById(
        //       "selectAccessLevelEdit" + numberSelect
        //     ).disabled = false;
        //     numberSelect++;
        //   }
        // } else {
        //   let numberSelect = 13;
        //   for (let i = 12; i <= checkBoxServer.length; i++) {
        //     document.getElementById(
        //       "selectAccessLevelEdit" + numberSelect
        //     ).checked = true;
        //     document.getElementById(
        //       "selectAccessLevelEdit" + numberSelect
        //     ).disabled = false;
        //     numberSelect++;
        //   }
        // }
        // } else {
        //   let numberSelectVm = 2;
        //   for (let i = 1; i <= 3; i++) {
        //     document.getElementById(
        //       "selectAccessLevelEdit" + numberSelectVm
        //     ).checked = false;
        //     document.getElementById(
        //       "selectAccessLevelEdit" + numberSelectVm
        //     ).disabled = true;
        //     numberSelectVm++;
        //   }
        //   document.getElementById("selectAccessLevelEdit9").disabled = true;
        //   document.getElementById("selectAccessLevelEdit9").checked = false;
        //   document.getElementById("selectAccessLevelEdit11").checked = false;
        //   document.getElementById("selectAccessLevelEdit11").disabled = true;
        //   if (project == "BBDH") {
        //     document.getElementById(
        //       "selectAccessLevelEdit13"
        //     ).disabled = true;
        //     let numberSelect = 14;
        //     for (let i = 13; i <= checkBoxServer.length; i++) {
        //       document.getElementById(
        //         "selectAccessLevelEdit" + numberSelect
        //       ).checked = false;
        //       document.getElementById(
        //         "selectAccessLevelEdit" + numberSelect
        //       ).disabled = true;
        //       numberSelect++;
        //     }
        //   } else {
        //     document.getElementById(
        //       "selectAccessLevelEdit10"
        //     ).disabled = true;
        //     document.getElementById(
        //       "selectAccessLevelEdit10"
        //     ).checked = false;
        //     document.getElementById(
        //       "selectAccessLevelEdit12"
        //     ).disabled = true;
        //     let numberSelect = 13;
        //     for (let i = 12; i <= checkBoxServer.length; i++) {
        //       document.getElementById(
        //         "selectAccessLevelEdit" + numberSelect
        //       ).checked = false;
        //       document.getElementById(
        //         "selectAccessLevelEdit" + numberSelect
        //       ).disabled = true;
        //       numberSelect++;
        //     }
        //   }
        // }
        // }
      }
    });
  });
}

let subEditUser = document.getElementById("addEditUser");
subEditUser.addEventListener("click", function () {
  if (btnEditUser == "") {
    numberPermission = 0;
    let lengthNameAddUser = document.getElementById("nameInputEditUser").value;
    let lengthFamilyAddUser = document.getElementById(
      "familyInputEditUser"
    ).value;
    let lengthAuthNameAddUser = document.getElementById(
      "authNameInputEditUser"
    ).value;
    let checkBox = document.querySelectorAll(".accessLevelCheckboxEdit");
    let lengthPhoneNumber;
    if (document.getElementById("phoneNumberEditUser")) {
      lengthPhoneNumber = document.getElementById("phoneNumberEditUser").value;
    }
    let lengthPassword = document.getElementById("passwordEditUser").value;
    let lengthRepeatPassword = document.getElementById(
      "repeatPasswordEditUser"
    ).value;

    const isCheckedBox = [...checkBox].some((checkBox) => checkBox.checked);
    let typePhoneNumber = /^\d+$/.test(lengthPhoneNumber);

    if (lengthNameAddUser.length < 3) {
      Toastify({
        text: "The name is less than 3 characters.",
        // avatar: "icons8-information.gif", // مسیر آیکون شما
      }).showToast();
    } else if (lengthFamilyAddUser.length < 3) {
      Toastify({
        text: "The last name is less than 3 characters.",
      }).showToast();
    } else if (lengthAuthNameAddUser.length < 3) {
      Toastify({
        text: "The username is less than 3 characters.",
      }).showToast();
    } else if (isCheckedBox == false) {
      Toastify({
        text: "You must select one of the following permissions.",
      }).showToast();
    } else if (
      lengthPhoneNumber &&
      lengthPhoneNumber.length < 11 &&
      project != "RRU"
    ) {
      Toastify({
        text: "The phone number is less than 11 characters.",
      }).showToast();
    } else if (typePhoneNumber == false && project != "RRU") {
      Toastify({
        text: "The entered phone number is not numeric.",
      }).showToast();
    } else if (lengthPassword.length < 8 && lengthPassword.length != 0) {
      Toastify({
        text: "The password is less than 8 characters.",
      }).showToast();
    } else if (lengthPassword != lengthRepeatPassword) {
      Toastify({
        text: "The password does not match the repeat field.",
      }).showToast();
    }

    if (project == "RRU") {
      if (
        lengthNameAddUser.length >= 3 &&
        lengthFamilyAddUser.length >= 3 &&
        lengthAuthNameAddUser.length >= 3 &&
        isCheckedBox != false &&
        (lengthPassword.length >= 8 || lengthPassword.length == 0)
      ) {
        if (lengthPassword === lengthRepeatPassword) {
          if (document.getElementById("selectEditUser").value == "visitor") {
            const checkbox5 = document.getElementById("selectAccessLevelEdit5");
            if (checkbox5 && checkbox5.checked) {
              let isValid = true;
              for (let x = 2; x <= NumberOfPermissions.length + 1; x++) {
                if (x === 5 || x >= 13 || x === 11) {
                  continue;
                }
                const checkbox = document.getElementById(
                  "selectAccessLevelEdit" + x
                );
                if (checkbox && checkbox.checked) {
                  isValid = false; // اگر چک‌باکس دیگری انتخاب شده باشد، شرایط نامعتبر است
                  break;
                }
              }
              if (isValid) {
                editUsers(elementEdit.id);
              } else {
                Toastify({
                  text: "This permissions is not related to this role.",
                }).showToast();
              }
            }
          } else {
            editUsers(elementEdit.id);
          }
        }
      }
    } else {
      if (
        lengthNameAddUser.length >= 3 &&
        lengthFamilyAddUser.length >= 3 &&
        lengthAuthNameAddUser.length >= 3 &&
        isCheckedBox != false &&
        lengthPhoneNumber.length == 11 &&
        typePhoneNumber == true &&
        (lengthPassword.length >= 8 || lengthPassword.length == 0)
      ) {
        if (lengthPassword === lengthRepeatPassword) {
          if (document.getElementById("selectEditUser").value == "visitor") {
            const checkbox5 = document.getElementById("selectAccessLevelEdit5");
            if (checkbox5 && checkbox5.checked) {
              let isValid = true;
              for (let x = 2; x <= NumberOfPermissions.length + 1; x++) {
                if (x === 5 || x >= 13 || x === 11) {
                  continue;
                }
                const checkbox = document.getElementById(
                  "selectAccessLevelEdit" + x
                );
                if (checkbox && checkbox.checked) {
                  isValid = false; // اگر چک‌باکس دیگری انتخاب شده باشد، شرایط نامعتبر است
                  break;
                }
              }
              if (isValid) {
                editUsers(elementEdit.id);
              } else {
                Toastify({
                  text: "This permissions is not related to this role.",
                }).showToast();
              }
            }
          } else {
            editUsers(elementEdit.id);
          }
        }
      }
    }
  }
});

let pageUser;

async function deletUser(id) {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  await useApi({
    method: "delete",
    url: `delete-member-Account/${id}`,
    callback: function (data) {
      let arrTableTr = document.querySelector(`#usersTable tbody tr#tr${id}`);
      arrTableTr?.remove();
      let items = arrRowShowAllUser.filter((item) => item.id != data.data.id);
      pageUser = items;
      arrRowShowAllUser = items;
      let endPageShowUser = Number(arrRowShowAllUser.length) + pageShowUser;

      let counter = 0;
      for (let x = pageShowUser - 1; x < endPageShowUser - 1; x++) {
        let targetId = arrRowShowAllUser[counter].id; // مقدار data-id که می‌خواهید انتخاب کنید
        counter++;
        document.querySelector(`[data-id="${targetId}"] .td1`).innerHTML = x;
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
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
  if (pageUser.length == 0) {
    document.getElementById("idLoading").style.display = "flex";
    removeUserParam();
    showUsers(1);
  }
}

function removeUserParam() {
  const url = new URL(window.location.href);
  url.searchParams.delete("user"); // حذف پارامتر "module"

  history.replaceState(null, "", url.pathname + url.search + url.hash);
}

let btnEditUser = "";

if (document.getElementById("serverIdInputEditUser")) {
  let serverIdInputEditUser = document.getElementById("serverIdInputEditUser");
  serverIdInputEditUser.addEventListener("input", () => {
    if (serverIdInputEditUser.value.trim() === "") {
      let numberSelectVm = 2;
      if (document.getElementById("selectEditUser").value != "visitor") {
        for (let i = 1; i <= 3; i++) {
          document.getElementById(
            "selectAccessLevelEdit" + numberSelectVm
          ).checked = true;
          document.getElementById(
            "selectAccessLevelEdit" + numberSelectVm
          ).disabled = false;
          numberSelectVm++;
        }
      } else {
        document.getElementById("selectAccessLevelEdit2").checked = false;
        document.getElementById("selectAccessLevelEdit2").disabled = true;
      }
      document.getElementById("selectAccessLevelEdit11").disabled = false;
      if (document.getElementById("selectEditUser").value != "visitor") {
        document.getElementById("selectAccessLevelEdit9").disabled = false;
        document.getElementById("selectAccessLevelEdit9").checked = true;
        if (project == "BBDH") {
          document.getElementById("selectAccessLevelEdit13").disabled = false;
        } else {
          document.getElementById("selectAccessLevelEdit10").disabled = false;
          document.getElementById("selectAccessLevelEdit10").checked = true;
          document.getElementById("selectAccessLevelEdit12").disabled = false;
        }
      }
      if (project == "BBDH") {
        let numberSelect = 14;
        for (let i = 13; i <= checkBoxServer.length; i++) {
          document.getElementById(
            "selectAccessLevelEdit" + numberSelect
          ).checked = true;
          document.getElementById(
            "selectAccessLevelEdit" + numberSelect
          ).disabled = false;
          numberSelect++;
        }
      } else {
        let numberSelect = 13;
        for (let i = 12; i <= checkBoxServer.length; i++) {
          document.getElementById(
            "selectAccessLevelEdit" + numberSelect
          ).checked = true;
          document.getElementById(
            "selectAccessLevelEdit" + numberSelect
          ).disabled = false;
          numberSelect++;
        }
      }
    } else {
      let numberSelectVm = 2;
      for (let i = 1; i <= 3; i++) {
        document.getElementById(
          "selectAccessLevelEdit" + numberSelectVm
        ).checked = false;
        document.getElementById(
          "selectAccessLevelEdit" + numberSelectVm
        ).disabled = true;
        numberSelectVm++;
      }
      document.getElementById("selectAccessLevelEdit9").disabled = true;
      document.getElementById("selectAccessLevelEdit9").checked = false;
      document.getElementById("selectAccessLevelEdit11").checked = false;
      document.getElementById("selectAccessLevelEdit11").disabled = true;
      if (project == "BBDH") {
        document.getElementById("selectAccessLevelEdit13").disabled = true;
        let numberSelect = 14;
        for (let i = 13; i <= checkBoxServer.length; i++) {
          document.getElementById(
            "selectAccessLevelEdit" + numberSelect
          ).checked = false;
          document.getElementById(
            "selectAccessLevelEdit" + numberSelect
          ).disabled = true;
          numberSelect++;
        }
      } else {
        document.getElementById("selectAccessLevelEdit10").disabled = true;
        document.getElementById("selectAccessLevelEdit10").checked = false;
        document.getElementById("selectAccessLevelEdit12").disabled = true;
        let numberSelect = 13;
        for (let i = 12; i <= checkBoxServer.length; i++) {
          document.getElementById(
            "selectAccessLevelEdit" + numberSelect
          ).checked = false;
          document.getElementById(
            "selectAccessLevelEdit" + numberSelect
          ).disabled = true;
          numberSelect++;
        }
      }
    }
  });
}

async function editUsers(id) {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  permissionUser = [];
  let numberId = Number(id);
  let nameInputEditUser = document.getElementById("nameInputEditUser").value;
  let familyInputEditUser = document.getElementById(
    "familyInputEditUser"
  ).value;
  let authNameInputEditUser = document.getElementById(
    "authNameInputEditUser"
  ).value;
  let serverIdInputEditUser;
  if (document.getElementById("serverIdInputEditUser")) {
    serverIdInputEditUser = document.getElementById(
      "serverIdInputEditUser"
    ).value;
  }
  let roleEditUser = document.getElementById("selectEditUser").value;
  let phoneNumberEditUser;
  if (document.getElementById("phoneNumberEditUser")) {
    phoneNumberEditUser = document.getElementById("phoneNumberEditUser").value;
  }
  let passwordEditUser = document.getElementById("passwordEditUser").value;
  let repeatPasswordEditUser = document.getElementById(
    "repeatPasswordEditUser"
  ).value;

  let formData = new FormData();

  // اضافه کردن مقادیر به FormData
  formData.append("user_id", numberId);
  formData.append("first_name", nameInputEditUser);
  formData.append("last_name", familyInputEditUser);
  formData.append("auth_name", authNameInputEditUser);
  if (document.getElementById("serverIdInputEditUser")) {
    if (serverIdInputEditUser != "") {
      formData.append("server_id", serverIdInputEditUser);
    }
  }
  formData.append("role", roleEditUser);
  if (project != "RRU") {
    formData.append("phone", phoneNumberEditUser);
  }
  formData.append("password", passwordEditUser);
  formData.append("password_confirmation", repeatPasswordEditUser);

  permissionEdit();

  // اضافه کردن آرایه server_ids به‌صورت مقادیر جداگانه
  permissionUser.forEach((id) => {
    formData.append(`permission_name[${numberPermission++}]`, id); // هر مقدار جداگانه اضافه می‌شود
  });

  await useApi({
    method: "post",
    url: "edit_member",
    data: formData,
    headers: {
      "Content-Type": "multipart/form-data", // این هدر مهم است
    },
    callback: function (data) {
      dataUser.forEach((e) => {
        if (e.id == data.data.user.id) {
          e.name = data.data.user.name;
          e.family = data.data.user.family;
          e.authName = data.data.user.authName;
          e.role = data.data.user.role;
          e.permission = data.data.user.permissions;
        }
      });

      let findEditUserIndex = dataUser.findIndex((item) => {
        return item.id == elementEdit.id;
      });

      if (findEditUserIndex != -1) {
        dataUser[findEditUserIndex]["id"] = data.data.user.id;
        dataUser[findEditUserIndex]["name"] = data.data.user.first_name;
        dataUser[findEditUserIndex]["family"] = data.data.user.last_name;
        dataUser[findEditUserIndex]["authName"] = data.data.user.auth_name;
        dataUser[findEditUserIndex]["role"] = data.data.user.roles;
      }

      document.querySelector(`#tr${id} .td2`).innerHTML =
        data.data.user.first_name;
      document.querySelector(`#tr${id} .td3`).innerHTML =
        data.data.user.last_name;
      document.querySelector(`#tr${id} .td4`).innerHTML =
        data.data.user.auth_name;
      document.querySelector(`#tr${id} .td5 span`).innerHTML =
        data.data.user.roles;


      let spanTd = document.querySelector(`#tr${data.data.user.id} .td5 span`);

      if (spanTd.innerHTML == "expert") {
        spanTd?.classList.remove("text-bg-success");
        spanTd?.classList.add("text-bg-primary");
      } else if (spanTd.innerHTML == "visitor") {
        spanTd?.classList.remove("text-bg-primary");
        spanTd?.classList.add("text-bg-success");
      }

      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
      let button = document.getElementById("addEditUser");
      button.setAttribute("data-bs-dismiss", "modal");
      btnEditUser = "modalEditUser";
      button.click(); // کلیک برنامه‌نویسی
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

let serverCard;
let idIconEditServer;

function setItemWithExpiry(key, value, days) {
  const now = new Date();
  const expiryTime = now.getTime() + days * 24 * 60 * 60 * 1000; // تبدیل روز به میلی‌ثانیه
  const item = {
    value: value,
    expiry: expiryTime,
  };
  localStorage.setItem(key, JSON.stringify(item));
}

// بازیابی مقدار و بررسی تاریخ انقضا
function getItemWithExpiry(key) {
  const itemStr = localStorage.getItem(key);
  if (!itemStr) {
    return null; // داده‌ای موجود نیست
  }

  const item = JSON.parse(itemStr);
  const now = new Date();

  if (now.getTime() > item.expiry) {
    // تاریخ انقضا گذشته، داده را حذف کنید
    localStorage.removeItem(key);
    return null;
  }

  return item.value;
}

let numberAndNameServers = [];

let permissionUsers;

let editModule = false;
let scheduleModule = false;
let RemoveModule = false;

// at least ONE of the paramters should be in user's roles.
// const userHasPermissionToViewPageMultiple = (
//   permissions,
//   checkPermissions = []
// ) => {
//   // if any of page's roles, wasn't in user's roles, return false
//   for (const per of checkPermissions) {
//     if (permissions.includes(per)) {
//       return true;
//     }
//   }
//   return false;
// };

function getPageByPermission(per) {
  if (per.startsWith("VM")) {
    return "#v-servers-home";
  } else if (per.startsWith("module")) {
    return "#v-module";
  } else if (per.startsWith("log")) {
    return "#v-log";
  } else if (per.startsWith("monitoring")) {
    return "#v-monitoring";
  } else if (per.startsWith("user")) {
    return "#v-users";
  }
}

let defaultPage = "";
let roleUserGetMe;
let serverIdPermission;
let idServers = [];
let serverPermissions;
let serverPermissionsIds;
let requestShowAllServer = false;
let requestShowAllServerOne = true;

// let userRoles = [];
async function showAllServer() {
  if (requestShowAllServer) return;
  requestShowAllServer = true;
  requestShowAllServerOne = false;
  document.getElementById("idLoading").style.display = "flex";
  await useApi({
    url: "show-all-servers",
    callback: function (data) {
      const serverIds = data.data.map((s) => Number(s.id));
      const accountIds = dataServer.map((a) => Number(a.id));

      // 3️⃣ اگر در servers هست ولی در accounts نیست → اضافه کن
      serverIds.forEach((id) => {
        if (!accountIds.includes(id)) {
          dataServer.push({ id: id.toString() }); // فقط id اضافه میشه
        }
      });

      // 4️⃣ اگر در accounts هست ولی در servers نیست → حذف کن
      dataServer = dataServer.filter((acc) =>
        serverIds.includes(Number(acc.id))
      );

      // 5️⃣ ذخیره در localStorage
      localStorage.setItem("dataServer", JSON.stringify(dataServer));

      dataServer.forEach((server) => {
        if (server.id == dataIdPassword) {
          server.username = document
            .getElementById("usernameServer")
            .value.trim();
          server.password = document
            .getElementById("inputPasswordServer")
            .value.trim();
          server.port = document.getElementById("portServer").value.trim();
          found = true; // یعنی این آیتم وجود داشت
        }
      });
      serverCard = data.data;
      for (let x = 0; x < data.data.length; x++) {
        idServers.push(data.data[x].id);
        numberAndNameServers.push({
          numberOfServers: data.data.length,
          idServer: data.data[x].id,
          nameServer: data.data[x].name,
          serverStatus: data.data[x].is_down,
        });
      }
      cardServer(serverCard);
      if (numberAndNameServers.length == 0) {
        document.getElementById("divImageServer").style.display = "block";
      } else {
        document.getElementById("divImageServer").style.display = "none";
      }
    },
  });
  // document.getElementById("idLoading").style.display = "none";
  requestShowAllServer = false;
  const buttonServer = document.querySelectorAll(".Server");
  iconAddServer(buttonServer);

  const buttoneditServer = document.querySelectorAll(".editServer");
  iconEditServer(buttoneditServer);

  if (!permissionUsers.includes("VM/update")) {
    document.querySelectorAll(".editServer").forEach((icon) => {
      icon?.remove();
    });
    document.getElementById("staticBackdrop")?.remove();
  }

  if (!permissionUsers.includes("VM/delete")) {
    document.querySelectorAll(".divIconRemoveServer").forEach((icon) => {
      icon?.remove();
    });
    document.getElementById("deletServer")?.remove();
  }

  if (!permissionUsers.includes("VM/status")) {
    document.querySelectorAll(".divIconPause").forEach((icon) => {
      icon?.remove();
    });
    document.querySelectorAll(".divIconPlay").forEach((icon) => {
      icon?.remove();
    });
  }
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
}

async function load() {
  // document.getElementById("idLoading").style.display = "flex";
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
      //       ?.classList.remove("backOffcanvas");
      //     document
      //       .getElementById("offcanvasWithBothOptions")
      //       ?.classList.add("backOffcanvasMotherboard");
      //   }
      //   document.querySelector(".logOut").style.backgroundColor = "#05009b";
      //   document.querySelector(".textDashbord").innerHTML =
      //     "Dashboard > Motherboard";
      // } else {
      //   document.querySelectorAll(".iconMotherboard").forEach((icon) => {
      //     icon.style.display = "none";
      //   });
      // }

      serverIdPermission = data.data.user.permissionServerIds;
      roleUserGetMe = data.data.user.roles[0];
      permissionUsers = data.data.user.permissions;

      if (roleUserGetMe != "admin") {
        serverPermissionsIds = data.data.user.permissionServerIds;
        serverPermissions = data.data.user.permissions.filter((item) =>
          item.startsWith("server/")
        );
      }

      if (roleUserGetMe == "visitor" || roleUserGetMe == "expert") {
        document.getElementById("v-setting")?.remove();
        document.getElementById("v-setting-tab")?.remove();
        document.getElementById("v-facePlate")?.remove();
        document.getElementById("v-facePlate-tab")?.remove();
        arrForActiveMenu = arrForActiveMenu.filter((item) => item != 8);
        arrForActiveMenu = arrForActiveMenu.filter((item) => item != 9);
      }

      // let username = localStorage.getItem("dataUser_name");
      document.getElementById("userName").innerHTML = data.data.user.auth_name;
      // let role = localStorage.getItem("dataUser_role");
      document.getElementById("role").innerHTML = data.data.user.roles[0];

      setAuthName(data.data.user.auth_name);
    },
    errorCallback: function () {
      window.location.href = "../views/login.html";
    },
  });
  if (document.getElementById("idLoadingDashbord")) {
    document.getElementById("idLoadingDashbord").style.display = "none";
  }
  if (document.getElementById("idSidebar")) {
    document.querySelector(".divMenu").style.display = "block";
  }

  activateTabFromUrlPath();
  defaultPage = getPageByPermission(permissionUsers[0]);

  if (!permissionUsers.includes("user")) {
    document.getElementById("v-userManagement")?.remove();
    arrForActiveMenu = arrForActiveMenu.filter((item) => item != 2);
    document.getElementById("modalAddUser")?.remove();
    document.getElementById("editUserModal")?.remove();
    document.getElementById("removeUserModal")?.remove();
    document.querySelector(".tabAdmin")?.remove();
  }

  if (!permissionUsers.includes("VM/read")) {
    document.getElementById("v-servers-tab")?.remove();
    document.getElementById("v-servers-home")?.remove();
    arrForActiveMenu = arrForActiveMenu.filter((item) => item != 1);
  }

  if (!permissionUsers.includes("VM/create")) {
    document.getElementById("iconAddServer")?.remove();
    document.getElementById("addServer")?.remove();
  }

  if (!permissionUsers.includes("module/read")) {
    document.getElementById("v-module-tab")?.remove();
    document.getElementById("v-module")?.remove();
    arrForActiveMenu = arrForActiveMenu.filter((item) => item != 5);
  }

  if (!permissionUsers.includes("module/create")) {
    scheduleModule = true;
    document.getElementById("addModal")?.remove();
    document.getElementById("addModule")?.remove();
    document.getElementById("thSchedulingModule")?.remove();
    document.getElementById("schedulingModule")?.remove();
  }

  if (!permissionUsers.includes("module/update")) {
    editModule = true;
    document.getElementById("thEditModule")?.remove();
    document.getElementById("editModule")?.remove();
  }

  if (!permissionUsers.includes("module/delete")) {
    RemoveModule = true;
    document.getElementById("thRemoveModule")?.remove();
    document.getElementById("removeModule")?.remove();
  }

  if (!permissionUsers.includes("kpi")) {
    document.getElementById("v-kpi-tab")?.remove();
    document.getElementById("v-kpi")?.remove();
    arrForActiveMenu = arrForActiveMenu.filter((item) => item != 14);
  }

  if (!permissionUsers.includes("route")) {
    document.getElementById("v-route-tab")?.remove();
    document.getElementById("v-route")?.remove();
    arrForActiveMenu = arrForActiveMenu.filter((item) => item != 11);
  }

  if (!permissionUsers.includes("ping")) {
    document.getElementById("tabPing")?.remove();
    document.getElementById("v-ping")?.remove();
    arrForActiveMenu = arrForActiveMenu.filter((item) => item != 7);
  }

  if (!permissionUsers.includes("trace")) {
    document.getElementById("v-trace-tab")?.remove();
    document.getElementById("v-trace")?.remove();
    arrForActiveMenu = arrForActiveMenu.filter((item) => item != 10);
  }

  if (!permissionUsers.includes("log")) {
    document.getElementById("v-log-tab")?.remove();
    document.getElementById("v-log")?.remove();
    arrForActiveMenu = arrForActiveMenu.filter((item) => item != 4);
    document.querySelector(".tabHistory")?.remove();
  }

  if (!permissionUsers.includes("monitoring")) {
    document.getElementById("V-monitoring-tab")?.remove();
    document.getElementById("v-monitoring")?.remove();
    arrForActiveMenu = arrForActiveMenu.filter((item) => item != 3);
    document.querySelector(".tabFaults")?.remove();
    document.getElementById("v-logManagement")?.remove();
    document.getElementById("v-performanceManagement")?.remove();
    arrForActiveMenu = arrForActiveMenu.filter((item) => item != 12);
    arrForActiveMenu = arrForActiveMenu.filter((item) => item != 13);
  }

  if (project == "BBDH") {
    if (!permissionUsers.includes("subscriber")) {
      document.getElementById("v-subscribers-tab")?.remove();
      document.getElementById("v-subscribers")?.remove();
      arrForActiveMenu = arrForActiveMenu.filter((item) => item != 6);
    }
  }

  if (roleUserGetMe != "admin") {
    const extraValues = idServers.filter(
      (num) => !serverIdPermission.includes(num)
    );

    extraValues.forEach((serverID) => {
      document
        .querySelector(`.info-box[data-server-id='${serverID}']`)
        ?.remove();
    });
  }

  // if (roleUserGetMe != "admin") {
  //   let allowedNames = serverPermissions.map((item) =>
  //     item.replace("server/", "")
  //   );

  //   // فیلتر کردن اشیایی که name آن‌ها در allowedNames وجود دارد
  //   serverCard = serverCard.filter((server) =>
  //     allowedNames.includes(server.name)
  //   );

  //   numberAndNameServers = numberAndNameServers.filter((server) =>
  //     serverPermissionsIds.includes(server.idServer)
  //   );

  //   for (let i = idServers.length - 1; i >= 0; i--) {
  //     if (!serverPermissionsIds.includes(idServers[i])) {
  //       idServers.splice(i, 1);
  //     }
  //   }
  // }
}

// const hasRole = (role)=>{
//   for(const r in role){
//     if
//   }
// }

function scheduleModulePermission() {
  if (scheduleModule) {
    document.querySelectorAll(".schedulingModule").forEach((icon) => {
      icon?.remove();
    });
    document.querySelectorAll(".tdSchedulingModule").forEach((td) => {
      td?.remove();
    });
  }
}

function editModulePermission() {
  if (editModule) {
    document.querySelectorAll(".editModuleClick").forEach((icon) => {
      icon?.remove();
    });
    document.querySelectorAll(".tdEditModule").forEach((td) => {
      td?.remove();
    });
  }
}

function removeModulePermission() {
  if (RemoveModule) {
    document.querySelectorAll(".deleteModuleClick").forEach((icon) => {
      icon?.remove();
    });
    document.querySelectorAll(".tdRemoveModule").forEach((td) => {
      td?.remove();
    });
  }
}

function iconAddServer(index) {
  // const subShowConfig = index
  // console.log(index);

  // let idServer;

  // افزودن Event Listener به همه دکمه‌ها
  index.forEach((button) => {
    button.addEventListener("click", function () {
      // idServer = this.dataset.id; // گرفتن id از data-id
      server(this.dataset.id);
    });
  });

  // subShowConfig.addEventListener("click", function () {
  //   // let port = document.getElementById("portTestConnetion").value;
  //   // localStorage.setItem("port", port);
  //   server(idServer); // فراخوانی تابع با id مربوطه
  // });
}

let urlLog;

function iconEditServer(index) {
  // افزودن Event Listener به همه دکمه‌ها
  index.forEach((button) => {
    button.addEventListener("click", function () {
      btnEditServer = "";
      document.getElementById("subServer").removeAttribute("data-bs-dismiss");
      let theDesiredServer = serverCard.find((item) => {
        return item.id == this.dataset.id;
      });
      idIconEditServer = this.dataset.id;

      let valueNameServer = theDesiredServer.name;
      let valueIpServer = theDesiredServer.ip;
      let valueInputPathConfig = theDesiredServer.path_config;
      let valueInputPathRunConfig = theDesiredServer.path_run_config;

      document.querySelector('input[name="nameEditNameServer"]').value =
        valueNameServer;
      document.querySelector('input[name="nameEditIpServer"]').value =
        valueIpServer;
      document.querySelector('input[name="nameEditPathConfigServer"]').value =
        valueInputPathConfig;
      document.querySelector(
        'input[name="nameEditPathRunConfigServer"]'
      ).value = valueInputPathRunConfig;
    });
  });
}

async function server(x) {
  let username, password, port;

  dataServer.forEach((server) => {
    if (server.id == x) {
      username = server.username;
      password = server.password;
      Number(port = server.port);
    }
  });

  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  // document.getElementById("subShowConfigModule").disabled = true;
  localStorage.setItem("server", x);

  await useApi({
    method: "post",
    url: `test-connection`,
    data: {
      server_id: x,
      username,
      password,
      ...(!!port ? { port } : {}),
    },
    callback: function () {
      localStorage.setItem("userNameServer", username);
      localStorage.setItem("passwordServer", password);
      if (project == "BBDH") {
        location.href = "../views/settingServer.html";
      } else {
        document.getElementById("containerBBU").classList.remove("d-none");
        document.querySelector(".dashboard-content").classList.add("d-none");
      }
      // let button = document.getElementById("closeModal");
      // button.click(); // کلیک برنامه‌نویسی
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
  // document.getElementById("subShowConfigModule").disabled = false;
}

let counterServer;
let btnEditServer = "";

async function editServer(x) {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  document.getElementById("subServer").disabled = true;
  counterServer = x;
  let nameServer = document.getElementById("editNameServer").value;
  let ipServer = document.getElementById("editIpServer").value;
  let pathConfig = document.getElementById("editPathConfigServer").value;
  let pathRunConfig = document.getElementById("editPathRunConfigServer").value;
  await useApi({
    method: "put",
    url: "edit-server",
    data: {
      server_id: x,
      name: nameServer,
      ip: ipServer,
      path_config: pathConfig,
      path_run_config: pathRunConfig,
    },
    callback: function (data) {
      changeShowServerModule = "editServer";
      nameMapping.push({
        id: data.server.id,
        oldName: data.oldNameServer,
        newName: data.server.name,
      });

      nameMapping.forEach((item) => {
        if (item.id == data.server.id) {
          nameMapping = nameMapping.filter(
            (item) => item.id !== data.server.id
          );
          nameMapping.push({
            id: data.server.id,
            oldName: data.oldNameServer,
            newName: data.server.name,
          });
        }
      });

      dataUser.forEach((item) => {
        item.permission.forEach((item2, num) => {
          if (item2 == `server/${data.oldNameServer}`) {
            item.permission.splice(num, 1, `server/${data.server.name}`);
          }
        });
      });

      for (let i = 0; i < numberAndNameServers.length; i++) {
        if (numberAndNameServers[i].idServer == data.server.id) {
          numberAndNameServers[i].nameServer = data.server.name;
        }
      }

      let suffix = "server/"; // پسوند دلخواه شما
      let name = data.server.name; // مقدار name از data
      let nameOld = data.oldNameServer;

      permissionAll.forEach((nameServer, num) => {
        if (`${suffix}${nameOld}` == nameServer) {
          permissionAll.splice(num, 1, `${suffix}${name}`); // 1 به معنای حذف یک عنصر است
        }
      });
      createChecckBox(permissionAll);

      dataShowAllRole[1].permissions.forEach((nameServer, num) => {
        if (`${suffix}${nameOld}` == nameServer) {
          dataShowAllRole[1].permissions.splice(num, 1, `${suffix}${name}`); // 1 به معنای حذف یک عنصر است
        }
      });
      dataShowAllRole[2].permissions.forEach((nameServer, num) => {
        if (`${suffix}${nameOld}` == nameServer) {
          dataShowAllRole[2].permissions.splice(num, 1, `${suffix}${name}`); // 1 به معنای حذف یک عنصر است
        }
      });
      showAllRole(dataShowAllRole);

      let counterCardServer = 0;
      let valueInputNameServer = data.server.name;
      let valueInputIpServer = data.server.ip;

      document.getElementById("nameServer" + x).innerHTML =
        valueInputNameServer;
      document.getElementById("ipServer" + x).innerHTML = valueInputIpServer;

      const obj = serverCard.find((obj) => {
        counterCardServer++;
        return obj.id == x;
      });
      if (obj) {
        serverCard[counterCardServer - 1].name = valueInputNameServer;
        serverCard[counterCardServer - 1].ip = valueInputIpServer;
        serverCard[counterCardServer - 1].path_config = data.server.path_config;
        serverCard[counterCardServer - 1].path_run_config =
          data.server.path_run_config;
      }
      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
      document.getElementById("subServer").disabled = false;
      let button = document.getElementById("subServer");
      button.setAttribute("data-bs-dismiss", "modal");
      btnEditServer = "modalEditServer";
      button.click(); // کلیک برنامه‌نویسی
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
  document.getElementById("subServer").disabled = false;
}

let subServer = document.getElementById("subServer");
subServer.addEventListener("click", function () {
  if (btnEditServer == "") {
    let valueInputEditNameServer = document.querySelector(
      'input[name="nameEditNameServer"]'
    ).value;
    let valueInputEditIpServer = document.querySelector(
      'input[name="nameEditIpServer"]'
    ).value;

    if (valueInputEditNameServer.length < 3) {
      Toastify({
        text: "The name is less than 3 characters.",
      }).showToast();
    } else if (valueInputEditIpServer.length < 7) {
      Toastify({
        text: "The IP is less than 7 characters.",
      }).showToast();
    }

    if (
      valueInputEditNameServer.length >= 3 &&
      valueInputEditIpServer.length >= 7
    ) {
      editServer(idIconEditServer); // فراخوانی تابع با id مربوطه
    }
  }
});

let idIconPause;
let idIconPlay;
let idIconTrash;
let idPause;
let idPlay;
let card;
let iconsPause;
function cardServer(Servers) {
  let num = 0;
  // محل اضافه کردن کارت‌ها
  const container = document.querySelector("#cardContainer"); // عنصر مقصد برای اضافه کردن کارت‌ها
  Servers.forEach((server) => {
    // ساخت کارت به صورت داینامیک
    card = document.createElement("div");
    if (Servers[num].is_down == 0) {
      card.className =
        "info-box host col-lg-3 col-md-4 col-11 ms-md-5 ms-sm-3 ms-3 text-dark";
    } else {
      card.className =
        "info-box off col-lg-3 col-md-4 col-11 ms-md-5 ms-sm-3 ms-3 text-dark";
    }
    card.setAttribute("data-server-id", `${Servers[num].id}`);
    num++;

    card.innerHTML = `
  <h5 style="font-weight: bold;" id="nameServer${server.id}">${server.name}</h5>
  <p id="ipServer${server.id}">${server.ip}</p>
  <div
  class="div-pencil d-flex justify-content-center align-items-center editServer"
  data-id="${server.id}"
  type="button"
  data-bs-toggle="modal"
  data-bs-target="#staticBackdrop"
  >
  <svg
    xmlns="http://www.w3.org/2000/svg"
    width="20"
    height="20"
    fill="currentColor"
    class="bi bi-pencil-square"
    viewBox="0 0 16 16"
     
  >
    <path
      d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"
    />
    <path
      fill-rule="evenodd"
      d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"
    />
  </svg>
  </div>
  <div class="d-flex justify-content-between">
  <div class="d-flex">
    <div
      class="divFlex divIconPause justify-content-center align-items-center"
      style="${Servers[num - 1].is_down == 1 ? "display:none" : "display:flex"}"
      data-server-pause-id="${server.id}"
      id="iconPause${server.id}"
      data-bs-toggle="modal"
      data-bs-target="#stopServer"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="25"
        height="25"
        fill="currentColor"
        class="bi bi-pause-fill"
        viewBox="0 0 16 16"
      >
        <path
          d="M5.5 3.5A1.5 1.5 0 0 1 7 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5m5 0A1.5 1.5 0 0 1 12 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5"
        />
      </svg>
    </div>
    <div
      class="divFlex divIconPlay justify-content-center align-items-center"
      style="${Servers[num - 1].is_down == 0 ? "display:none" : "display:flex"}"
      data-server-play-id="${server.id}"
      id="iconPlay${server.id}"
      data-bs-toggle="modal"
      data-bs-target="#playServer"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="25"
        height="25"
        fill="currentColor"
        class="bi bi-play-fill"
        viewBox="0 0 16 16"
      >
        <path
          d="m11.596 8.697-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393"
        />
      </svg>
    </div>
    <div
      class="divFlex divIconRemoveServer d-flex justify-content-center align-items-center ms-2"
      data-server-trash-id="${server.id}"
      id="iconRemoveServer${server.id}"
      data-bs-toggle="modal"
      data-bs-target="#deletServer"
    >
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
        <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
      </svg>
    </div>
    <div
      class="divFlex divIconPasswordServer d-flex justify-content-center align-items-center ms-3"
      data-id="${server.id}"
      id="passwordServer${server.id}"
      data-bs-toggle="modal"
      data-bs-target="#passwordServer"
    >
      <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-key-fill" viewBox="0 0 16 16">
        <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2M2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
      </svg>
    </div>
  </div>
  <div>
    <div
      class="divFlex d-flex justify-content-center align-items-center Server me-2 gearConfig"
      data-id="${server.id}"

    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="20"
        height="20"
        fill="currentColor"
        class="bi bi-gear-fill"
        viewBox="0 0 16 16"
      >
        <path
          d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"
        />
      </svg>
    </div>
  </div>
  </div>
  `;
    // افزودن کارت به کانتینر
    container.appendChild(card);
  });

  iconPause();

  document.getElementById("playClick").addEventListener("click", function () {
    StartServers(idIconPlay);
  });

  iconPlay();

  document.getElementById("pauseClick").addEventListener("click", function () {
    StopServers(idIconPause);
  });

  iconRemoveServer();
  iconPasswordServer();

  if (document.getElementById("subDeletServer")) {
    document
      .getElementById("subDeletServer")
      .addEventListener("click", function () {
        if (btnRemoveServer == "") {
          let valueUserNameDeletServer = document.querySelector(
            'input[name="nameUserNameDeletServer"]'
          ).value;
          let valuePasswordDeletServer = document.querySelector(
            'input[name="namePasswordDeletServer"]'
          ).value;

          if (valueUserNameDeletServer.length < 3) {
            Toastify({
              text: "The name is less than 3 characters.",
            }).showToast();
          } else if (valuePasswordDeletServer.length < 8) {
            Toastify({
              text: "The password is less than 8 characters.",
            }).showToast();
          }

          if (
            valueUserNameDeletServer.length >= 3 &&
            valuePasswordDeletServer.length >= 8
          ) {
            removeServer(idIconTrash);
          }
        }
      });
  }

  removeInputGearConfig();
  removeInputAddServer();

  if (document.getElementById("subAddServer")) {
    document
      .getElementById("subAddServer")
      .addEventListener("click", function () {
        if (btnAddServer == "") {
          let valueInputAddNameServer = document.querySelector(
            'input[name="nameAddNameServer"]'
          ).value;
          let valueInputAddIpServer = document.querySelector(
            'input[name="nameAddIpServer"]'
          ).value;

          if (valueInputAddNameServer.length < 3) {
            Toastify({
              text: "The name is less than 3 characters.",
            }).showToast();
          } else if (valueInputAddIpServer.length < 7) {
            Toastify({
              text: "The IP is less than 7 characters.",
            }).showToast();
          }

          if (
            valueInputAddNameServer.length >= 3 &&
            valueInputAddIpServer.length >= 7
          ) {
            addServer();
          }
        }
      });
  }
}

function iconPause() {
  iconsPause = document.querySelectorAll(".divIconPause");

  iconsPause.forEach((element) => {
    element.addEventListener("click", function () {
      idPause = element.id;
      idIconPause = this.dataset.serverPauseId;
      let nameServer = document.getElementById(
        "nameServer" + this.dataset.serverPauseId
      ).innerHTML;
      document.getElementById("spanPauseServer").innerHTML = nameServer;
    });
  });
}

function iconPlay() {
  let iconsPlay = document.querySelectorAll(".divIconPlay");

  iconsPlay.forEach((element) => {
    element.addEventListener("click", function () {
      idPlay = element.id;
      idIconPlay = this.dataset.serverPlayId;
      let nameServer = document.getElementById(
        "nameServer" + this.dataset.serverPlayId
      ).innerHTML;
      document.getElementById("spanPlayServer").innerHTML = nameServer;
    });
  });
}

let dataServer = [];
if (JSON.parse(localStorage.getItem("dataServer", dataServer))) {
  dataServer = JSON.parse(localStorage.getItem("dataServer", dataServer));

}
let dataIdPassword;

function iconPasswordServer() {
  let iconsPassword = document.querySelectorAll(".divIconPasswordServer");

  iconsPassword.forEach((element) => {
    element.addEventListener("click", function () {
      document.getElementById("showPasswordServer").checked = false;
      passwordTF5 = true;
      showPasswordsServer();
      dataIdPassword = this.dataset.id;
      document.getElementById("usernameServer").value = "";
      document.getElementById("inputPasswordServer").value = "";
      document.getElementById("portServer").value = 22;

      if (JSON.parse(localStorage.getItem("dataServer", dataServer))) {
        dataServer = JSON.parse(localStorage.getItem("dataServer", dataServer));
      } else {
        dataServer = [];
      }

      if (dataServer.length != 0) {
        dataServer.forEach((server) => {
          if (server.id == dataIdPassword && server.username != undefined) {
            document.getElementById("portServer").value = server.port;
            document.getElementById("usernameServer").value = server.username;
            document.getElementById("inputPasswordServer").value =
              server.password;
          }
        });
      }
    });
  });
}

document.getElementById("savePasswordServer").addEventListener("click", () => {
  savePasswordServer(dataIdPassword);
});

async function savePasswordServer(id) {
  // let username, password;

  const port = Number(document.getElementById("portServer").value.trim());
  const username = document.getElementById("usernameServer").value.trim();
  const password = document.getElementById("inputPasswordServer").value.trim();

  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  await useApi({
    method: "post",
    url: `test-connection`,
    data: {
      server_id: id,
      username,
      password,
      ...(!!port ? { port } : {}),
    },
    callback: function (data) {
      const newServer = {
        id: id,
        username: username,
        password: password,
        ...(!!port ? { port } : {}),
      };

      let found = false;

      dataServer.forEach((server) => {
        if (server.id == dataIdPassword) {
          debugger
          server.username = document
            .getElementById("usernameServer")
            .value.trim();
          server.password = document
            .getElementById("inputPasswordServer")
            .value.trim();
          server.port = Number(document.getElementById("portServer").value.trim());

          found = true; // یعنی این آیتم وجود داشت
        }
        else (console.log("salam"))
      });

      if (!found) {
        dataServer.push(newServer);
      }
      localStorage.setItem("dataServer", JSON.stringify(dataServer));
      Toastify({
        text: "The username and password have been successfully registered.",
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
      document.getElementById("cancelSavePasswordServer").click();
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

function iconRemoveServer() {
  let iconsTrash = document.querySelectorAll(".divIconRemoveServer");

  iconsTrash.forEach((element) => {
    element.addEventListener("click", function () {
      document.getElementById("showPasswordDeletServer").checked = false;
      passwordTF4 = true;
      showPasswordsDeletServer();

      btnRemoveServer = "";
      document
        .getElementById("subDeletServer")
        .removeAttribute("data-bs-dismiss");
      idIconTrash = this.dataset.serverTrashId;
      let nameServer = document.getElementById(
        "nameServer" + this.dataset.serverTrashId
      ).innerHTML;
      document.getElementById("userNameDeletServer").value = "";
      document.getElementById("passwordDeletServer").value = "";
      document.getElementById(
        "spanTrashServer"
      ).innerHTML = `\`${nameServer}\``;
    });
  });
}

async function StartServers(id) {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  await useApi({
    method: "post",
    url: "server-start",
    data: {
      server_id: id,
    },
    callback: function (data) {
      let index = numberAndNameServers.findIndex(
        (item) => item.nameServer === data.data.name
      );

      if (index !== -1) {
        // انجام تغییرات
        numberAndNameServers[index].serverStatus = 0;
      }

      let massge = data.msg;
      document.getElementById(`${idPlay}`)?.classList.remove("d-flex");
      document.getElementById(`${idPlay}`)?.classList.add("d-none");
      document
        .getElementById(`iconPause${idIconPlay}`)
        ?.classList.remove("d-none");
      document
        .getElementById(`iconPause${idIconPlay}`)
        ?.classList.add("d-flex");
      document
        .querySelector(`.info-box[data-server-id='${data.data.id}']`)
        ?.classList.remove("off");
      document
        .querySelector(`.info-box[data-server-id='${data.data.id}']`)
        ?.classList.add("host");

      Toastify({
        text: massge,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

async function StopServers(id) {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  await useApi({
    method: "post",
    url: "server-stop",
    data: {
      server_id: id,
    },
    callback: function (data) {
      let massge = data.msg;

      let index = numberAndNameServers.findIndex(
        (item) => item.nameServer === data.data.name
      );

      if (index !== -1) {
        // انجام تغییرات
        numberAndNameServers[index].serverStatus = 1;
      }

      document.getElementById(`${idPause}`)?.classList.remove("d-flex");
      document.getElementById(`${idPause}`)?.classList.add("d-none");
      document
        .getElementById(`iconPlay${idIconPause}`)
        ?.classList.remove("d-none");
      document
        .getElementById(`iconPlay${idIconPause}`)
        ?.classList.add("d-flex");
      document
        .querySelector(`.info-box[data-server-id='${data.data.id}']`)
        ?.classList.remove("host");
      document
        .querySelector(`.info-box[data-server-id='${data.data.id}']`)
        ?.classList.add("off");

      Toastify({
        text: massge,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

let btnRemoveServer = "";

async function removeServer(id) {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  document.getElementById("subDeletServer").disabled = true;
  let nameUserNameDeletServer = document.querySelector(
    'input[name="nameUserNameDeletServer"]'
  ).value;
  let namePasswordDeletServer = document.querySelector(
    'input[name="namePasswordDeletServer"]'
  ).value;
  await useApi({
    method: "delete",
    url: "server-delete",
    data: {
      server_id: id,
      auth_name: nameUserNameDeletServer,
      password: namePasswordDeletServer,
    },
    callback: function (data) {
      dataServer = dataServer.filter((server) => server.id != data.data.id);
      localStorage.setItem("dataServer", JSON.stringify(dataServer));
      changeShowServerModule = "removeServer";
      dataUser.forEach((item) => {
        item.permission = item.permission.filter(
          (item2) => !item2.includes(`server/${data.data.name}`)
        );
      });

      let suffix = "server/"; // پسوند دلخواه شما
      let name = data.data.name; // مقدار name از data

      permissionAll.forEach((nameServer, num) => {
        if (`${suffix}${name}` == nameServer) {
          permissionAll.splice(num, 1); // 1 به معنای حذف یک عنصر است
        }
      });
      createChecckBox(permissionAll);

      dataShowAllRole[1].permissions.forEach((nameServer, num) => {
        if (`${suffix}${name}` == nameServer) {
          dataShowAllRole[1].permissions.splice(num, 1); // 1 به معنای حذف یک عنصر است
        }
      });
      dataShowAllRole[2].permissions.forEach((nameServer, num) => {
        if (`${suffix}${name}` == nameServer) {
          dataShowAllRole[2].permissions.splice(num, 1); // 1 به معنای حذف یک عنصر است
        }
      });
      showAllRole(dataShowAllRole);

      serverCard.forEach((server, num) => {
        if (data.data.id == server.id) {
          serverCard.splice(num, 1); // 1 به معنای حذف یک عنصر است
        }
      });

      const newArray = numberAndNameServers.filter(
        (obj) => obj.nameServer !== data.data.name
      );

      numberAndNameServers = newArray;
      let massge = data.msg;
      document
        .querySelector(`.info-box[data-server-id='${data.data.id}']`)
        ?.remove();

      document.getElementById("subDeletServer").disabled = false;
      let button = document.getElementById("subDeletServer");
      button.setAttribute("data-bs-dismiss", "modal");
      btnRemoveServer = "modalRemoveServer";
      button.click(); // کلیک برنامه‌نویسی
      Toastify({
        text: massge,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
    },
  });

  if (numberAndNameServers.length == 0) {
    document.getElementById("divImageServer").style.display = "block";
  } else {
    document.getElementById("divImageServer").style.display = "none";
  }

  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
  document.getElementById("subDeletServer").disabled = false;
}

function removeInputGearConfig() {
  document.querySelectorAll(".gearConfig").forEach((element) => {
    element.addEventListener("click", function () {
      // document.getElementById("showPasswordShowConfig").checked = false;
      // passwordTF3 = true;
      // showPasswordsShowConfig();
      let dataId = element.getAttribute("data-id");
      let nameServer = document.getElementById("nameServer" + dataId).innerHTML;
      localStorage.setItem("nameServer", nameServer);
      // document.getElementById("userNameShowConfig").value =
      //   localStorage.getItem("userNameServer");
      // document.getElementById("passwordShowConfig").value =
      //   localStorage.getItem("passwordServer");
      // document.getElementById("portTestConnetion").value =
      //   localStorage.getItem("port");
    });
  });
}

function removeInputAddServer() {
  if (document.getElementById("iconAddServer")) {
    document
      .getElementById("iconAddServer")
      .addEventListener("click", function () {
        btnAddServer = "";
        document
          .getElementById("subAddServer")
          .removeAttribute("data-bs-dismiss");
        document.getElementById("addNameServer").value = "";
        document.getElementById("addIpServer").value = "";
        document.getElementById("addPathConfigServer").value = "/etc/bbdh/";
        document.getElementById("addPathRunConfigServer").value = "/usr/bin/";
      });
  }
}

let btnAddServer = "";

async function addServer() {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  document.getElementById("subAddServer").disabled = true;
  const container = document.querySelector("#cardContainer"); // عنصر مقصد برای اضافه کردن کارت‌ها

  let valueInputAddNameServer = document.querySelector(
    'input[name="nameAddNameServer"]'
  ).value;
  let valueInputAddIpServer = document.querySelector(
    'input[name="nameAddIpServer"]'
  ).value;
  let valueInputAddPathConfigServer = document.querySelector(
    'input[name="nameAddPathConfigServer"]'
  ).value;
  let valueInputAddPathRunConfigServer = document.querySelector(
    'input[name="nameAddPathRunConfigServer"]'
  ).value;

  await useApi({
    method: "post",
    url: "create-server",
    data: {
      name: valueInputAddNameServer,
      ip: valueInputAddIpServer,
      path_config: valueInputAddPathConfigServer,
      path_run_config: valueInputAddPathRunConfigServer,
    },
    callback: function (data) {
      const newServer = {
        id: data.data.id,
      };
      dataServer.push(newServer);
      localStorage.setItem("dataServer", JSON.stringify(dataServer));
      let suffix = "server/"; // پسوند دلخواه شما
      let name = data.data.name; // مقدار name از data
      permissionAll.push(`${suffix}${name}`);
      createChecckBox(permissionAll);
      dataShowAllRole[1].permissions.push(`${suffix}${name}`);
      dataShowAllRole[2].permissions.push(`${suffix}${name}`);
      showAllRole(dataShowAllRole);

      idServers.push(data.data.id);
      serverIdPermission.push(data.data.id);
      numberAndNameServers.push({
        numberOfServers: numberAndNameServers.length,
        nameServer: data.data.name,
        idServer: data.data.id,
      });

      serverCard.push(data.data);
      const addCard = document.createElement("div");
      addCard.className =
        "info-box host col-lg-3 col-md-4 col-11 ms-md-5 ms-sm-3 ms-3 text-dark";
      addCard.setAttribute("data-server-id", `${data.data.id}`);

      addCard.innerHTML = `
  <h5 style="font-weight: bold;" id="nameServer${data.data.id}">${data.data.name}</h5>
  <p id="ipServer${data.data.id}">${data.data.ip}</p>
  <div
  class="div-pencil d-flex justify-content-center align-items-center editServer"
  data-id="${data.data.id}"
  type="button"
  data-bs-toggle="modal"
  data-bs-target="#staticBackdrop"
  >
  <svg
    xmlns="http://www.w3.org/2000/svg"
    width="20"
    height="20"
    fill="currentColor"
    class="bi bi-pencil-square"
    viewBox="0 0 16 16"
  >
    <path
      d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"
    />
    <path
      fill-rule="evenodd"
      d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"
    />
  </svg>
  </div>
  <div class="d-flex justify-content-between">
  <div class="d-flex">
    <div
      class="divFlex divIconPause d-flex justify-content-center align-items-center"
      data-server-pause-id="${data.data.id}"
      id="iconPause${data.data.id}"
      data-bs-toggle="modal"
      data-bs-target="#stopServer"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="25"
        height="25"
        fill="currentColor"
        class="bi bi-pause-fill"
        viewBox="0 0 16 16"
      >
        <path
          d="M5.5 3.5A1.5 1.5 0 0 1 7 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5m5 0A1.5 1.5 0 0 1 12 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5"
        />
      </svg>
    </div>
    <div
      class="divFlex divIconPlay justify-content-center align-items-center"
      style="display:none"
      data-server-play-id="${data.data.id}"
      id="iconPlay${data.data.id}"
      data-bs-toggle="modal"
      data-bs-target="#playServer"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="25"
        height="25"
        fill="currentColor"
        class="bi bi-play-fill"
        viewBox="0 0 16 16"
      >
        <path
          d="m11.596 8.697-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393"
        />
      </svg>
    </div>
    <div
      class="divFlex divIconRemoveServer d-flex justify-content-center align-items-center ms-2"
      data-server-trash-id="${data.data.id}"
      id="iconRemoveServer${data.data.id}"
      data-bs-toggle="modal"
      data-bs-target="#deletServer"
    >
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
        <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
      </svg>
    </div>
    <div
      class="divFlex divIconPasswordServer d-flex justify-content-center align-items-center ms-3"
      data-id="${data.data.id}"
      id="passwordServer${data.data.id}"
      data-bs-toggle="modal"
      data-bs-target="#passwordServer"
    >
      <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-key-fill" viewBox="0 0 16 16">
        <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2M2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
      </svg>
    </div>
  </div>
  <div>
    <div
      class="divFlex d-flex justify-content-center align-items-center Server me-2 gearConfig"
      data-id="${data.data.id}"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="20"
        height="20"
        fill="currentColor"
        class="bi bi-gear-fill"
        viewBox="0 0 16 16"
      >
        <path
          d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"
        />
      </svg>
    </div>
  </div>
  </div>
  `;
      // افزودن کارت به کانتینر
      container.appendChild(addCard);
      removeInputAddServer();
      removeInputGearConfig();
      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();

      document.getElementById("subAddServer").disabled = false;
      let button = document.getElementById("subAddServer");
      button.setAttribute("data-bs-dismiss", "modal");
      btnAddServer = "modalAddServer";
      button.click(); // کلیک برنامه‌نویسی

      if (!permissionUsers.includes("VM/update")) {
        document.querySelectorAll(".editServer").forEach((icon) => {
          icon?.remove();
        });
      }
      if (!permissionUsers.includes("VM/delete")) {
        document.querySelectorAll(".divIconRemoveServer").forEach((icon) => {
          icon?.remove();
        });
      }

      if (!permissionUsers.includes("VM/status")) {
        document.querySelectorAll(".divIconPause").forEach((icon) => {
          icon?.remove();
        });
        document.querySelectorAll(".divIconPlay").forEach((icon) => {
          icon?.remove();
        });
      }
    },
  });

  iconPause();

  iconPlay();

  iconRemoveServer();
  iconPasswordServer();

  const buttonServer = document.querySelectorAll(".Server");
  iconAddServer(buttonServer);

  const buttoneditServer = document.querySelectorAll(".editServer");
  iconEditServer(buttoneditServer);

  if (numberAndNameServers.length == 0) {
    document.getElementById("divImageServer").style.display = "block";
  } else {
    document.getElementById("divImageServer").style.display = "none";
  }
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
  document.getElementById("subAddServer").disabled = false;
}

let logClickTF = false;
let whichTab = "";

document
  .querySelector("#v-log-tab")
  .addEventListener("shown.bs.tab", function () {
    if (project == "RRU") {
      document
        .getElementById("menuFaultsRRU")
        ?.classList.remove("activeMenuRRU");
      document
        .getElementById("menuSettingRRU")
        ?.classList.remove("activeMenuRRU");
      document.getElementById("menuSettingRRU")?.classList.add("activeMenuRRU");
    }
    if (document.getElementById("closeOffcanvas")) {
      document.getElementById("closeOffcanvas").click();
    }
    whichTab = "log";
    if (requestShowLog) {
      if (urlLog == 0) {
        urlLog = 1;
      }
      pageLog(urlLog);
    }
    const id = this.dataset.id; // گرفتن id از data-id
    ActiveMenu(id); // فراخوانی تابع با id مربوطه
  });

window.addEventListener("hashchange", () => {
  const isVLog = window.location.hash === "#v-log";
  if (!isVLog) {
    requestShowLog = true;
    currentPageLog = 1;
  }
});

// let newPage;
let currentPageLog;
let showLogLoading = false;
let requestShowLog = true;
let desiredPageTFlog = true;
let desiredPageLog = 1;

async function pageLog(page = 1) {
  if (roleUserGetMe == "visitor") return;
  if (showLogLoading) return;
  showLogLoading = true;
  let dataLog = [];
  let totalPages;
  requestShowLog = false;
  document.getElementById("idLoading").style.display = "flex";
  await useApi({
    method: "post",
    url: `show-all-logs?sort=-id&paginate=20&page=${page}`,
    callback: function (data) {
      desiredPageTFlog = true;
      dataLog.push(data.data.data);
      if (logClickTF) {
        document.getElementById("tBody2").innerHTML = "";
      }
      logClickTF = true;

      let endPage = Math.ceil(data.data.total / 20);

      let paginationNamber;
      // if (page == 1) {
      paginationNamber = (page - 1) * 20;
      paginationNamber++;
      // } else {
      //   paginationNamber = (page - 1) * 20;
      //   paginationNamber++;
      // }

      let lengthDataUser = data.data.data.length;
      for (let i = 0; i < lengthDataUser; i++) {
        let tr = document.createElement("tr");
        for (let x = 1; x <= 5; x++) {
          let td = document.createElement("td");
          if (x == 1) {
            td.innerHTML = paginationNamber++;
          } else if (x == 2) {
            // let span1 = document.createElement("span");
            // let span2 = document.createElement("span");
            // span1.setAttribute("class", "mx-2");
            // span2.setAttribute("class", "mx-2");
            let div = document.createElement("div");
            div.setAttribute("class", "mt-2");
            // span1.innerHTML =
            //   data.data.data[i].properties?.user?.first_name || "-";
            // span2.innerHTML =
            //   data.data.data[i].properties?.user?.last_name || "-";
            div.innerHTML =
              data.data.data[i].properties?.user?.auth_name || "-";
            // td.appendChild(span1);
            // td.appendChild(span2);
            td.appendChild(div);
            div?.classList.add("fontSize");
          } else if (x == 3) {
            td.innerHTML = data.data.data[i].event;
          } else if (x == 4) {
            td.innerHTML = data.data.data[i].description;
            const divIcon = document.createElement("div");
            divIcon.innerHTML = `
            <div class="btn btn-secondary btnLog py-0 px-2 pb-1 mt-2" data-id-log=${i} data-bs-toggle="modal"  data-bs-target="#modalMoreDetailsLog">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-three-dots" viewBox="0 0 16 16" 
               >
                <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3"/>
              </svg>
            </div>
               `;
            td.appendChild(divIcon);
          } else if (x == 5) {
            let dataFa = new Date(
              data.data.data[i].created_at
            ).toLocaleString();
            td.innerHTML = dataFa;
          }
          tr.appendChild(td);
        }
        document.getElementById("tBody2").appendChild(tr);
      }

      totalPages = endPage; // Set the total number of pages

      renderPagination(totalPages, currentPageLog);

      function renderPagination(
        totalPages,
        currentPage = !urlLog ? 1 : urlLog
      ) {
        const pagination = document.getElementById("pagination");
        pagination.innerHTML = "";

        if (totalPages != 1) {
          // First button
          const firstItem = document.createElement("li");
          firstItem.className = `page-item ${currentPage === 1 ? "disabled" : ""
            }`;
          firstItem.innerHTML = `<a class="page-link" href="#" data-page="1">First Page</a>`;
          pagination.appendChild(firstItem);

          // Previous button
          const prevItem = document.createElement("li");
          prevItem.className = `page-item ${currentPage === 1 ? "disabled" : ""
            }`;
          prevItem.innerHTML = `<a class="page-link" href="#" data-page="${currentPage - 1
            }">Previous Page</a>`;
          pagination.appendChild(prevItem);

          // Page numbers logic
          const startPage = currentPage === 1 ? currentPage : currentPage - 1;
          const endPage =
            currentPage === totalPages
              ? currentPage
              : Math.min(currentPage + 1, totalPages);

          for (
            let i = Math.max(1, startPage - 1);
            i <= Math.min(totalPages, endPage + 1);
            i++
          ) {
            const pageItem = document.createElement("li");
            pageItem.className = `page-item ${i === currentPage ? "active" : ""
              }`;
            pageItem.innerHTML = `<a class="page-link ${i === currentPage ? "active-page" : ""
              }" href="#" data-page="${i}">${i}</a>`;
            pagination.appendChild(pageItem);
          }

          // Next button
          const nextItem = document.createElement("li");
          nextItem.className = `page-item ${currentPage === totalPages ? "disabled" : ""
            }`;
          nextItem.innerHTML = `<a class="page-link" href="#" data-page="${currentPage + 1
            }">Next Page</a>`;
          pagination.appendChild(nextItem);

          // Last button
          const lastItem = document.createElement("li");
          lastItem.className = `page-item ${currentPage === totalPages ? "disabled" : ""
            }`;
          lastItem.innerHTML = `<a class="page-link" href="#" data-page="${totalPages}">Last Page</a>`;
          pagination.appendChild(lastItem);

          const currentUrl = new URL(window.location);
          currentUrl.searchParams.set("log", !currentPage ? 1 : currentPage); // Set the module in URL
          window.history.pushState({}, "", currentUrl);
        }
      }
    },
  });
  showLogLoading = false;
  document.getElementById("idLoading").style.display = "none";

  // Add click event listeners to all links
  pagination.querySelectorAll(".page-link").forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();

      const newPage = parseInt(this.getAttribute("data-page"));

      if (!isNaN(newPage) && newPage > 0 && newPage <= totalPages) {
        if (desiredPageLog == newPage) {
          return;
        }
        if (desiredPageTFlog) {
          desiredPageLog = newPage;
          desiredPageTFlog = false;
        }
        currentPageLog = newPage;
        pageLog(newPage);
        document.querySelector(".paginationLog").innerHTML = "";
        document.getElementById("tBody2").innerHTML = "";
      }
    });
  });

  document.querySelectorAll(".btnLog").forEach((element) => {
    element.addEventListener("click", function () {
      // const jsonData = dataLog[0];
      // const id = this.dataset.idLog;
      // const resultAcctive = jsonData[id];

      // const displayDiv = document.getElementById("formLog");
      // if (result) {
      //   displayDiv.innerHTML = `<pre>${JSON.stringify(result, null, 2)}</pre>`;
      // } else {
      //   displayDiv.innerHTML = "<p>No data found for the given ID.</p>";
      // }

      // تابع اصلی برای نمایش داده‌ها بر اساس ID
      const id = this.dataset.idLog;
      const jsonData = dataLog[0]; // بارگذاری داده‌ها
      const result = jsonData[id];

      const displayDiv = document.getElementById("formLog");
      displayDiv.innerHTML = ""; // پاک کردن محتوای قبلی

      if (result) {
        displayDynamicData(result, displayDiv);
      } else {
        displayDiv.innerHTML =
          '<p class="highlight">No data found for the given ID.</p>';
      }

      // async function loadData() {
      //   try {
      //     const response = await fetch(`${dataLog[0]}`); // مسیر فایل JSON یا API
      //     if (!response.ok) {
      //       throw new Error("Network response was not ok");
      //     }
      //     const data = await response.json();
      //     return data;
      //   } catch (error) {
      //     console.error("Error loading data:", error);
      //     return [];
      //   }
      // }

      // تابع برای نمایش داده‌ها به صورت دینامیک
      function displayDynamicData(data, parentElement, level = 0) {
        const list = document.createElement("ul"); // ایجاد لیست
        list.style.marginLeft = `${level * 5}px`; // فاصله برای سطوح تو در تو
        let dataFa;

        for (const key in data) {
          if (
            key != "id" &&
            key != "subject_type" &&
            key != "subject_id" &&
            key != "causer_type" &&
            key != "causer_id" &&
            key != "batch_uuid"
          ) {
            if (data.hasOwnProperty(key)) {
              const value = data[key];
              const listItem = document.createElement("li"); // ایجاد آیتم لیست

              // اگر key برابر با "changes" بود، یک کلاس CSS اضافه کنید
              if (key === "changes") {
                listItem?.classList.add("changes-highlight");
              }

              if (typeof value === "object" && value !== null) {
                listItem.innerHTML = `<strong>${key}:</strong>`;
                list.appendChild(listItem);
                displayDynamicData(value, listItem, level + 1); // بازگشت برای سطوح تو در تو
              } else if (typeof value === "string" && isJsonString(value)) {
                listItem.innerHTML = `<strong>${key}:</strong>`;
                list.appendChild(listItem);
                const parsedValue = JSON.parse(value);
                displayDynamicData(parsedValue, listItem, level + 1);
              } else {
                if (key == "updated_at" || key == "created_at") {
                  dataFa = new Date(value).toLocaleString();
                } else {
                  dataFa = value;
                }
                listItem.innerHTML = `<strong>${key}:</strong> <span class="highlight me-2">${dataFa}</span>`;
                list.appendChild(listItem);
              }
            }
          }
        }

        parentElement.appendChild(list); // اضافه کردن لیست به والد
      }

      function isJsonString(str) {
        try {
          JSON.parse(str);
        } catch (e) {
          return false;
        }
        return true;
      }

      // let idLog = this.dataset.idLog;
      // document.getElementById("id").innerHTML = dataLog[0][idLog].id;
      // document.getElementById("logName").innerHTML = dataLog[0][idLog].log_name;

      // if (dataLog[0][idLog].properties.member === undefined) {
      //   document.getElementById("divMember").style.display = "none";
      // } else {
      //   document.getElementById("divMember").style.display = "block";
      //   document.getElementById("idUser").innerHTML =
      //     dataLog[0][idLog].properties.member?.id || "---";
      //   document.getElementById("authNameUser").innerHTML =
      //     dataLog[0][idLog].properties.member?.auth_name || "---";
      //   document.getElementById("firstNameUser").innerHTML =
      //     dataLog[0][idLog].properties.member?.first_name || "---";
      //   document.getElementById("lastNameUser").innerHTML =
      //     dataLog[0][idLog].properties.member?.last_name || "---";
      // }

      // if (dataLog[0][idLog].properties.password === undefined) {
      //   document.getElementById("divPassword").style.display = "none";
      // } else {
      //   document.getElementById("divPassword").style.display = "block";
      //   document.getElementById("password").innerHTML =
      //     dataLog[0][idLog].properties?.password || "---";
      // }

      // if (dataLog[0][idLog].properties.server === undefined) {
      //   document.getElementById("divServer").style.display = "none";
      // } else {
      //   document.getElementById("divServer").style.display = "block";
      //   document.getElementById("ipServer").innerHTML =
      //     dataLog[0][idLog].properties?.server?.ip || "---";
      //   document.getElementById("nameServer").innerHTML =
      //     dataLog[0][idLog].properties?.server?.name || "---";
      // }

      // if (dataLog[0][idLog].properties.module_name === undefined) {
      //   document.getElementById("divConfigServer").style.display = "none";
      // } else {
      //   document.getElementById("divConfigServer").style.display = "block";
      //   document.getElementById("nameModule").innerHTML =
      //     dataLog[0][idLog].properties?.module_name || "---";
      //   document.getElementById("typeModule").innerHTML =
      //     dataLog[0][idLog].properties?.module_type || "---";
      // }
    });
  });
}

/// درست کردن تمامی فانکشن ها todo

let modulesInfo = [];
let showModulesLoading = false;
let requestShowModule = true;
let idTrModule;
let numberTrModule;
let rowShowModule = 1;
let urlModule;
let arrRowShowModule = [];
let paginationModule;
let pageShowModule = 1;
let desiredPageTFmodule = true;
let desiredPageModule = 1;
let currentPageModule;

async function showModuls(page = 1) {
  document.getElementById("idLoading").style.display = "flex";
  if (showModulesLoading) return;
  showModulesLoading = true;
  requestShowModule = false;
  let totalPages;
  await useApi({
    url: `show-all-modules?paginate=20&page=${page}`,
    callback: function (data) {
      desiredPageTFmodule = true;
      arrRowShowModule = [];
      rowShowModule = 1;

      let endPage = Math.ceil(data.module.pagination.total / 20);

      let paginationNamber;
      paginationNamber = (page - 1) * 20;
      paginationNamber++;
      pageShowModule = paginationNamber + 1;

      document.getElementById("tBody3").innerHTML = "";
      let numberTr = data.module.module.length;

      for (let y = 0; y < numberTr; y++) {
        arrRowShowModule.push(data.module.module[y].module_id);
        numberTrModule = y + 1;
        modulesInfo.push({
          serverIDs: data.module.module[y].server_ids,
          moduleID: data.module.module[y].module_id,
          moduleName: data.module.module[y].module_name,
          moduleType: [data.module.module[y].module_type],
        });
        let tr = document.createElement("tr");
        tr.setAttribute("id", `tr${y + 1}`);
        tr.setAttribute("data-id", `${data.module.module[y].module_id}`);
        for (let x = 1; x <= 7; x++) {
          let td = document.createElement("td");
          td.setAttribute("class", `td${x}`);
          if (x == 1) {
            td.innerHTML = paginationNamber++;
          } else if (x == 2) {
            td.innerHTML = data.module.module[y].module_name;
          } else if (x == 3) {
            let valueSpan;

            valueSpan = data.module.module[y].server_name;
            let span = document.createElement("span");
            let result = JSON.stringify(valueSpan)
              .replace(/,\s*\]$/, "")
              .replace(/^\[/, "")
              .replace(/\]$/, "")
              .replace(/,/g, ", ")
              .replace(/"/g, "");
            if (!result) {
              span.innerHTML = "_";
            } else {
              span.innerHTML = result;
            }
            td.appendChild(span);
          } else if (x == 4) {
            let fullSpecifications =
              data.module.module[y].module_type.toUpperCase();
            let correctSpecifications = JSON.stringify(
              fullSpecifications
            ).replace(/[\[\]"\s\\]+/g, "");

            td.innerHTML = correctSpecifications;
          } else if (x == 5) {
            td.setAttribute("class", `td5 tdSchedulingModule`);
            let svgElemEdit = `<svg 
            xmlns="http://www.w3.org/2000/svg" 
            width="20" 
            height="20" 
            id="${y + 1}"
            data-id-modules = "${y + 1}"
            data-module-id = "${data.module.module[y].module_id}"
            fill="currentColor" 
            data-bs-toggle="modal"
            data-bs-target="#schedulingModule"
            class="bi bi-calendar4-week schedulingModule cursorPointer" 
            viewBox="0 0 16 16">
              <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v1h14V3a1 1 0 0 0-1-1zm13 3H1v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/>
              <path d="M11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-2 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
              </svg>`;
            td.insertAdjacentHTML("afterbegin", svgElemEdit);
          } else if (x == 6) {
            td.setAttribute("class", `td6 tdEditModule`);
            let svgElemEdit = `<svg
          xmlns="http://www.w3.org/2000/svg"
          width="20"
          height="20"
          fill="currentColor"
          id="${y + 1}"
          data-id-modules = "${y + 1}"
            data-module-id = "${data.module.module[y].module_id}"
          data-bs-toggle="modal"
          data-bs-target="#editModule"
          class="bi bi-pencil-square editModuleClick cursorPointer"
          aria-label="Edit module"
          data-test="module-edit-btn"
          viewBox="0 0 16 16"
        >
          <path
            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"
          />
          <path
            fill-rule="evenodd"
            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"
          />
        </svg>`;

            td.insertAdjacentHTML("afterbegin", svgElemEdit);
          } else if (x == 7) {
            td.setAttribute("class", `td7 tdRemoveModule`);
            let svgElemEdit = `<svg 
            xmlns="http://www.w3.org/2000/svg"
           width="20" 
           height="20" 
           id="${y + 1}"
           data-id-modules = "${y + 1}"
            data-module-id = "${data.module.module[y].module_id}"
           data-bs-toggle="modal"
           data-bs-target="#removeModule"
           fill="currentColor" 
           class="bi bi-trash3 deleteModuleClick cursorPointer"
           aria-label="Delete module"
           data-test="module-delete-btn"
           viewBox="0 0 16 16">
           <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
           </svg>`;

            td.insertAdjacentHTML("afterbegin", svgElemEdit);
          }
          tr.appendChild(td);
        }
        document.getElementById("tBody3").appendChild(tr);
      }

      totalPages = endPage; // Set the total number of pages
      renderPagination(totalPages, currentPageModule);

      function renderPagination(
        totalPages,
        currentPage = !urlModule ? 1 : urlModule
      ) {
        paginationModule = document.getElementById("paginationModule");
        paginationModule.innerHTML = "";
        if (totalPages != 1) {
          // First button
          const firstItem = document.createElement("li");
          firstItem.className = `page-item ${currentPage === 1 ? "disabled" : ""
            }`;
          firstItem.innerHTML = `<a class="page-link" href="#" data-page="1">First Page</a>`;
          paginationModule.appendChild(firstItem);

          // Previous button
          const prevItem = document.createElement("li");
          prevItem.className = `page-item ${currentPage === 1 ? "disabled" : ""
            }`;
          prevItem.innerHTML = `<a class="page-link" href="#" data-page="${currentPage - 1
            }">Previous Page</a>`;
          paginationModule.appendChild(prevItem);

          // Page numbers logic
          const startPage = currentPage === 1 ? currentPage : currentPage - 1;
          const endPage =
            currentPage === totalPages
              ? currentPage
              : Math.min(currentPage + 1, totalPages);

          for (
            let i = Math.max(1, startPage - 1);
            i <= Math.min(totalPages, endPage + 1);
            i++
          ) {
            const pageItem = document.createElement("li");
            pageItem.className = `page-item ${i === currentPage ? "active" : ""
              }`;
            pageItem.innerHTML = `<a class="page-link ${i === currentPage ? "active-page" : ""
              }" href="#" data-page="${i}">${i}</a>`;
            paginationModule.appendChild(pageItem);
          }

          // Next button
          const nextItem = document.createElement("li");
          nextItem.className = `page-item ${currentPage === totalPages ? "disabled" : ""
            }`;
          nextItem.innerHTML = `<a class="page-link" href="#" data-page="${currentPage + 1
            }">Next Page</a>`;
          paginationModule.appendChild(nextItem);

          // Last button
          const lastItem = document.createElement("li");
          lastItem.className = `page-item ${currentPage === totalPages ? "disabled" : ""
            }`;
          lastItem.innerHTML = `<a class="page-link" href="#" data-page="${totalPages}">Last Page</a>`;
          paginationModule.appendChild(lastItem);

          const currentUrl = new URL(window.location);
          currentUrl.searchParams.set("module", !currentPage ? 1 : currentPage); // Set the module in URL
          window.history.pushState({}, "", currentUrl);
        }
      }
    },
  });

  modalEditModule();
  modalSchedulingModule();
  iconDeleteModule();

  showModulesLoading = false;
  document.getElementById("idLoading").style.display = "none";

  removeModulePermission();
  editModulePermission();
  scheduleModulePermission();

  paginationModule?.querySelectorAll(".page-link")?.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();

      const newPage = parseInt(this.getAttribute("data-page"));
      if (!isNaN(newPage) && newPage > 0 && newPage <= totalPages) {
        if (desiredPageModule == newPage) {
          return;
        }
        if (desiredPageTFmodule) {
          desiredPageModule = newPage;
          desiredPageTFmodule = false;
        }
        modulesInfo = [];
        currentPageModule = newPage;
        showModuls(newPage);
        document.querySelector(".paginationModule").innerHTML = "";
        document.getElementById("tBody3").innerHTML = "";
      }
    });
  });

  let lengthTbody3 = document.getElementById("tBody3").rows.length;
  if (!lengthTbody3) {
    document.getElementById("paginationModule").style.display = "none";
    document.getElementById("divImgModule").style.display = "block";
  } else {
    document.getElementById("paginationModule").style.display = "flex";
    document.getElementById("divImgModule").style.display = "none";
  }
}

// document.getElementById("buttonIframe1").addEventListener("click", function () {
//   updateIframe("iframe_a", "url_input_a");
//   sendAddress();
// });

/////////////////////////////////////////////////
// const input = document.getElementById("url_input_a");
// const openBtn = document.getElementById("buttonOpenIframe1");
// const linkList = document.getElementById("linkList");

// let tabs = [];

// function normalizeURL(url) {
//   if (!url.startsWith("http://") && !url.startsWith("https://")) {
//     return "https://" + url;
//   }
//   return url;
// }

// openBtn.addEventListener("click", () => {
//   let lengthInput = document.getElementById("url_input_a").value.length;

//   if (lengthInput >= 5) {
//     sendAddress();
//     const raw = input.value.trim();
//     const url = normalizeURL(raw);

//     const newTab = window.open(url, "_blank");
//     if (newTab) {
//       const id = Date.now(); // unique id
//       tabs.push({ id, url, win: newTab });

//       renderLinks();
//     }
//   } else {
//     Toastify({
//       text: "Enter the desired URL.",
//     }).showToast();
//   }
// });

// function renderLinks() {
//   linkList.innerHTML = tabs
//     .map(
//       (t) => `
//         <li>
//           <a href="${t.url}" target="_blank">${t.url}</a>
//           <span style="font-size: 12px; color: gray;">(Open)</span>
//         </li>
//       `
//     )
//     .join("");
//   if (linkList.children.length === 0) {
//     document.getElementById("textOpenLink").style.display = "none";
//   } else {
//     document.getElementById("textOpenLink").style.display = "block";
//   }
// }
/////////////////////////////////////////////////
// const input2 = document.getElementById("url_input_b");
// const openBtn2 = document.getElementById("buttonOpenIframe2");
// const linkList2 = document.getElementById("linkList2");

// let tabs2 = [];

// function normalizeURL2(url) {
//   if (!url.startsWith("http://") && !url.startsWith("https://")) {
//     return "https://" + url;
//   }
//   return url;
// }

// openBtn2.addEventListener("click", () => {
//   let lengthInput = document.getElementById("url_input_b").value.length;

//   if (lengthInput >= 5) {
//     sendAddress();
//     const raw = input2.value.trim();
//     const url = normalizeURL2(raw);

//     const newTab = window.open(url, "_blank");
//     if (newTab) {
//       const id = Date.now(); // unique id
//       tabs2.push({ id, url, win: newTab });

//       renderLinks2();
//     }
//   }
// });

// function renderLinks2() {
//   linkList2.innerHTML = tabs2
//     .map(
//       (t) => `
//         <li>
//           <a href="${t.url}" target="_blank">${t.url}</a>
//           <span style="font-size: 12px; color: gray;">(Open)</span>
//         </li>
//       `
//     )
//     .join("");
//   if (linkList2.children.length === 0) {
//     document.getElementById("textOpenLink2").style.display = "none";
//   } else {
//     document.getElementById("textOpenLink2").style.display = "block";
//   }
// }
/////////////////////////////////////////////////

// document.getElementById("buttonIframe2").addEventListener("click", function () {
//   updateIframe("iframe_b", "url_input_b");
//   sendAddress();
// });

function modalEditModule() {
  document.querySelectorAll(".editModuleClick").forEach((element) => {
    element.addEventListener("click", function () {
      btnEditModule = "";
      document
        .getElementById("buttonUpdateModule")
        .removeAttribute("data-bs-dismiss");
      idTrModule = this.dataset.idModules;
      const moduleIdFromDataset = Number(this.dataset.moduleId);
      const moduleRow = this.closest("tr");
      idModule = moduleIdFromDataset || Number(moduleRow?.dataset.id || 0);

      document.querySelector("#moduleFile").value = "";
      const dataSet = this.dataset.idModules;
      document.getElementById("ServerEditModule").innerHTML = "";
      crateServerEditModule(dataSet, idModule);
      typeModuleEdit(idModule);

      for (let i = 0; i < numberAndNameServers.length; i++) {
        if (numberAndNameServers[i].serverStatus == 1) {
          for (let x = 0; x < numberAndNameServers.length; x++) {
            if (serverCard[i].name == numberAndNameServers[x].nameServer) {
              let id = serverCard[i].id;
              document.getElementById(`selectServerEdit${id}`).disabled = true;
              document.getElementById(`selectServerEdit${id}`).checked = false;
            }
          }
        }
      }
    });
  });
}

let dataSet;

function modalSchedulingModule() {
  document.querySelectorAll(".schedulingModule").forEach((element) => {
    element.addEventListener("click", function () {
      dataSet = this.dataset.idModules;
      document.getElementById("runScheduling").value = "";
      document.getElementById("moduleFileScheduling").value = "";
      document.getElementById("currentSystemPassword").value = "";
      document.getElementById("ServerScheduling").innerHTML = "";
      crateServerSchedulingModule(dataSet);
    });
  });
}

let idModuleScheduling;
let idScheduling;
// let serverModule = [];
function crateServerSchedulingModule(x) {
  // پاک کردن کانتینر و ریست کردن فیلدها
  const serverContainer = document.getElementById("ServerScheduling");
  serverContainer.innerHTML = "";
  document.getElementById("runScheduling").value = "";
  document.getElementById("moduleFileScheduling").value = "";
  document.getElementById("currentSystemPassword").value = "";

  // تعیین id ماژول بر اساس پارامتر x (که قبلاً از dataset میاد)
  idModuleScheduling = modulesInfo[x - 1].moduleID;

  // ساخت چک‌باکس‌ها برای همه‌ی سرورها
  let numberLength = numberAndNameServers.length;
  for (let i = 1; i <= numberLength; i++) {
    let idInputServer = serverCard[i - 1].id;
    let div = document.createElement("div");
    div.setAttribute("class", "form-check");
    let input = document.createElement("input");
    input.setAttribute("class", "form-check-input schedulingModalCheckbox");
    input.setAttribute("data-server-id", `${idInputServer}`);
    input.setAttribute("value", "");
    input.setAttribute("type", "checkbox");
    input.setAttribute("id", `selectServerScheduling${idInputServer}`);
    let label = document.createElement("label");
    label.setAttribute("class", "form-check-label");
    label.setAttribute("for", `selectServerScheduling${idInputServer}`);
    label.innerHTML = `${numberAndNameServers[i - 1].nameServer}`;
    div.appendChild(input);
    div.appendChild(label);
    serverContainer.appendChild(div);
  }

  // --- پیدا کردن اسکجول مرتبط با این ماژول ---
  // توجه: schedulingData ممکن است دو فرم داشته باشد:
  // 1) فرم خامی که از بک میاد (چند آیتم با همان module_id و server_id های جدا)
  // 2) فرم نرمالایز شده که قبلاً تبدیل کرده‌ایم (یک آیتم با servers: [...] و module_id)
  // این کد با هر دو فرمت کار می‌کند.

  const moduleId = Number(modulesInfo[x - 1].moduleID);
  // اول سعی می‌کنیم آیتم نرمالایز شده پیدا کنیم (has servers array)
  let schedule = schedulingData.find(
    (item) => Number(item.module_id) === moduleId && Array.isArray(item.servers)
  );

  if (!schedule) {
    document
      .getElementById("currentPasswordScheduling")
      .classList.add("d-block");
    document.getElementById("divModuleFileScheduling").classList.add("d-block");
    document
      .getElementById("currentPasswordScheduling")
      .classList.remove("d-none");
    document
      .getElementById("divModuleFileScheduling")
      .classList.remove("d-none");
    document
      .getElementById("buttonSchedulingModule")
      .classList.remove("d-none");
    // document
    //   .getElementById("buttonEditSchedulingModule")
    //   .classList.remove("d-inline-flex");
    document
      .getElementById("buttonDeleteSchedulingModule")
      .classList.remove("d-inline-flex");
    document
      .getElementById("buttonSchedulingModule")
      .classList.add("d-inline-flex");
    // document
    //   .getElementById("buttonEditSchedulingModule")
    //   .classList.add("d-none");
    document
      .getElementById("buttonDeleteSchedulingModule")
      .classList.add("d-none");
    // اگر نرمالایز نشده بود، ممکنه data خام باشه: چند رکورد با همان module_id و server_id جدا
    const rawItems = schedulingData.filter(
      (item) => Number(item.module_id) === moduleId
    );

    if (rawItems.length > 0) {
      idScheduling = rawItems[0].id;
      // تبدیل rawItems به یک structure مشابه نرمال
      const servers = rawItems.map((ri) => ({
        id: Number(ri.server_id),
        username: ri.username_ssh || ri.username || "",
        password: ri.password_ssh || ri.password || "",
        port: ri.port_ssh || ri.port || "",
      }));

      schedule = {
        module_id: moduleId,
        run_scheduled_at: rawItems[0].run_scheduled_at || "",
        servers,
        file_name: rawItems[0].config ? "from_server" : null,
        password: rawItems[0].password_ssh || "",
        status: rawItems[0].status || "",
      };
    }
  }

  // اگر اسکجول پیدا شد، فرم را مقداردهی کن
  if (schedule) {
    document
      .getElementById("currentPasswordScheduling")
      .classList.add("d-none");
    document.getElementById("divModuleFileScheduling").classList.add("d-none");
    document
      .getElementById("currentPasswordScheduling")
      .classList.remove("d-block");
    document
      .getElementById("divModuleFileScheduling")
      .classList.remove("d-block");
    serverContainer.innerHTML = "";

    // ساخت چک‌باکس‌ها برای همه‌ی سرورها
    let numberLength = numberAndNameServers.length;

    // اگر اسکجول داریم، فقط سرورهای داخل schedule را نمایش بدهیم
    let allowedServers = null;
    if (schedule && Array.isArray(schedule.servers)) {
      allowedServers = schedule.servers.map((s) => Number(s.id));
    }

    for (let i = 1; i <= numberLength; i++) {
      let idInputServer = serverCard[i - 1].id;

      // اگر اسکجول داریم و این سرور داخلش نیست => نمایش داده نشود
      if (allowedServers && !allowedServers.includes(Number(idInputServer))) {
        continue; // اصلا skip کن
      }

      let div = document.createElement("div");
      div.setAttribute("class", "form-check ps-3");

      // let input = document.createElement("input");
      // input.setAttribute("class", "form-check-input schedulingModalCheckbox");
      // input.setAttribute("data-server-id", `${idInputServer}`);
      // input.setAttribute("value", "");
      // input.setAttribute("type", "checkbox");
      // input.setAttribute("id", `selectServerScheduling${idInputServer}`);

      let label = document.createElement("label");
      label.setAttribute("class", "form-check-label");
      label.setAttribute("for", `selectServerScheduling${idInputServer}`);
      label.innerHTML = `${numberAndNameServers[i - 1].nameServer}`;

      // div.appendChild(input);
      div.appendChild(label);
      serverContainer.appendChild(div);

      // اگر اسکجول موجود باشد → چک‌باکس‌ها قفل شوند
      // if (allowedServers) {
      //   input.disabled = true; // قفل شود
      // }
    }

    document
      .getElementById("buttonSchedulingModule")
      .classList.remove("d-inline-flex");
    // document
    //   .getElementById("buttonEditSchedulingModule")
    //   .classList.remove("d-none");
    document
      .getElementById("buttonDeleteSchedulingModule")
      .classList.remove("d-none");
    document.getElementById("buttonSchedulingModule").classList.add("d-none");
    // document
    //   .getElementById("buttonEditSchedulingModule")
    //   .classList.add("d-inline-flex");
    document
      .getElementById("buttonDeleteSchedulingModule")
      .classList.add("d-inline-flex");
    if (schedule.run_scheduled_at) {
      const raw = schedule.run_scheduled_at; // مقداری که از بک می‌گیری
      const dt = new Date(raw); // JS خودش Z یا GMT را تشخیص می‌دهد

      const options = {
        timeZone: "Asia/Tehran",
        hour12: false,
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
      };

      const parts = new Intl.DateTimeFormat("en-CA", options).formatToParts(dt);

      const year = parts.find((p) => p.type === "year").value;
      const month = parts.find((p) => p.type === "month").value;
      const day = parts.find((p) => p.type === "day").value;
      const hour = parts.find((p) => p.type === "hour").value;
      const minute = parts.find((p) => p.type === "minute").value;

      const finalValue = `${year}-${month}-${day}T${hour}:${minute}`;

      document.getElementById("runScheduling").value = finalValue;
    }

    // تیک زدن چک‌باکس‌های سرور
    (schedule.servers || []).forEach((s) => {
      const checkbox = document.querySelector(`#selectServerScheduling${s.id}`);
      if (checkbox) checkbox.checked = true;
    });

    // اگر اسم فایل قبلی داری، می‌گذاریم به عنوان متادیتا
    if (schedule.file_name) {
      document
        .getElementById("moduleFileScheduling")
        .setAttribute("data-old-file", schedule.file_name);
    }
  }
}

const inputRunScheduling = document.getElementById("runScheduling");
const now = new Date();
// به فرمت YYYY-MM-DDThh:mm تبدیل کن
const localDatetime = now.toISOString().slice(0, 16);
inputRunScheduling.min = localDatetime;

document
  .getElementById("buttonSchedulingModule")
  .addEventListener("click", () => {
    let runScheduling = document.getElementById("runScheduling").value;
    let fileInput = document.getElementById("moduleFileScheduling");
    let fileModule = fileInput.files[0];
    let checkBox = document.querySelectorAll(".schedulingModalCheckbox");
    let password = document.getElementById("currentSystemPassword").value;

    const isCheckedBox = [...checkBox].some((c) => c.checked);

    if (runScheduling == "") {
      Toastify({ text: "Please enter the desired date carefully" }).showToast();
    } else if (!fileModule) {
      Toastify({
        text: "Please upload the desired file correctly",
      }).showToast();
    } else if (!isCheckedBox) {
      Toastify({
        text: "Please select the desired server correctly",
      }).showToast();
    } else if (password == "") {
      Toastify({ text: "Please enter the desired password" }).showToast();
    }

    if (runScheduling && isCheckedBox && fileModule && password) {
      setScheduleingModules(idModuleScheduling);
    }
  });

// document
//   .getElementById("buttonEditSchedulingModule")
//   .addEventListener("click", () => {
//     let runScheduling = document.getElementById("runScheduling").value;
//     let fileScheduling = document.getElementById("moduleFileScheduling").value;
//     let checkBox = document.querySelectorAll(".schedulingModalCheckbox");
//     let password = document.getElementById("currentSystemPassword").value;

//     const isCheckedBox = [...checkBox].some((checkBox) => checkBox.checked);

//     if (runScheduling == "") {
//       Toastify({
//         text: "Please enter the desired date carefully",
//       }).showToast();
//     } else if (fileScheduling == "") {
//       Toastify({
//         text: "Please upload the desired file correctly",
//       }).showToast();
//     } else if (isCheckedBox == false) {
//       Toastify({
//         text: "Please select the desired server correctly",
//       }).showToast();
//     } else if (password == "") {
//       Toastify({
//         text: "Please enter the desired password",
//       }).showToast();
//     }

//     if (
//       runScheduling != "" &&
//       isCheckedBox != false &&
//       fileScheduling != "" &&
//       password != ""
//     ) {
//       // editModules(idModule);
//       editScheduleingModules(idScheduling, idModuleScheduling);
//     }
//   });

document
  .getElementById("buttonDeleteSchedulingModule")
  .addEventListener("click", () => {
    deleteScheduleingModules(idModuleScheduling);
  });

async function setScheduleingModules(idModule) {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";

  let dateScheduling = document.getElementById("runScheduling").value;
  let password = document.getElementById("currentSystemPassword").value;

  let serverIds = [];
  let serverSpecifications;
  let formData = new FormData();

  document.querySelectorAll(".schedulingModalCheckbox").forEach((checkBox) => {
    if (checkBox.checked)
      dataServer.forEach((server) => {
        if (server.id == checkBox.getAttribute("data-server-id")) {
          serverSpecifications = {
            id: server.id,
            username: server.username,
            password: server.password,
            port: server.port,
          };
          serverIds.push(serverSpecifications);
        }
      });
  });

  serverIds.forEach((server, index) => {
    formData.append(`servers[${index}][id]`, server?.id || "");
    formData.append(`servers[${index}][username]`, server?.username || "");
    formData.append(`servers[${index}][password]`, server?.password || "");
    formData.append(`servers[${index}][port]`, server?.port || "");
  });

  // اضافه کردن مقادیر به FormData
  formData.append("module_id", idModule);
  formData.append("password", password);
  formData.append("run_scheduled_at", dateScheduling.replace("T", " "));

  let fileInput = document.getElementById("moduleFileScheduling");
  let fileModule = fileInput.files[0];
  if (fileModule) {
    formData.append("config_file", fileModule); // فایل را اضافه می‌کنیم
  }

  await useApi({
    url: `module/schedule`,
    method: "post",
    data: formData,
    headers: {
      "Content-Type": "multipart/form-data", // این هدر مهم است
    },
    callback: function (data) {
      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
      for (let i = 0; i < data.data.length; i++) {
        schedulingData.push(data.data[i]); // اضافه‌کردن جدید
      }
      document.getElementById("schedulingCancel").click();
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

// async function editScheduleingModules(idModule, idModuleMain) {
//   document.getElementById("idLoading").style.display = "flex";
//   document.getElementById("idLoading").style.background =
//     "hsla(0, 0%, 100%, 0.5)";

//   let dateScheduling = document.getElementById("runScheduling").value;
//   let password = document.getElementById("currentSystemPassword").value;

//   let serverIds = [];
//   let serverSpecifications;
//   let formData = new FormData();

//   document.querySelectorAll(".schedulingModalCheckbox").forEach((checkBox) => {
//     if (checkBox.checked)
//       dataServer.forEach((server) => {
//         if (server.id == checkBox.getAttribute("data-server-id")) {
//           serverSpecifications = {
//             id: server.id,
//             username: server.username,
//             password: server.password,
//             port: server.port,
//           };
//           serverIds.push(serverSpecifications);
//         }
//       });
//   });

//   serverIds.forEach((server, index) => {
//     formData.append(`servers[${index}][id]`, server.id);
//     formData.append(`servers[${index}][username]`, server.username);
//     formData.append(`servers[${index}][password]`, server.password);
//     formData.append(`servers[${index}][port]`, server.port);
//   });

//   // اضافه کردن مقادیر به FormData
//   formData.append("module_id", idModuleMain);
//   formData.append("password", password);
//   formData.append("run_scheduled_at", dateScheduling.replace("T", " "));

//   let fileInput = document.getElementById("moduleFileScheduling");
//   let fileModule = fileInput.files[0];
//   if (fileModule) {
//     formData.append("config_file", fileModule); // فایل را اضافه می‌کنیم
//   }

//   await useApi({
//     url: `module/schedule/${idModule}`,
//     method: "patch",
//     data: formData,
//     headers: {
//       "Content-Type": "multipart/form-data", // این هدر مهم است
//     },
//     callback: function (data) {
//       // بررسی اینکه آیا ماژول از قبل اسکجول داشته یا نه
//       const existingIndex = schedulingData.findIndex(
//         (item) => item.id === idModule
//       );

//       Toastify({
//         text: data.msg,
//         style: {
//           background:
//             "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
//         },
//       }).showToast();

//       if (existingIndex !== -1) {
//         schedulingData[existingIndex] = data.data; // بروزرسانی
//       } else {
//         schedulingData.push(data.data); // اضافه‌کردن جدید
//       }
//       document.getElementById("schedulingCancel").click();
//     },
//   });
//   document.getElementById("idLoading").style.display = "none";
//   document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
// }

async function deleteScheduleingModules(idModule) {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  await useApi({
    url: `module/schedules-by-module/${idModule}`,
    method: "delete",
    callback: function (data) {
      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();

      document
        .getElementById("buttonSchedulingModule")
        .classList.remove("d-none");
      // document
      //   .getElementById("buttonEditSchedulingModule")
      //   .classList.remove("d-inline-flex");
      document
        .getElementById("buttonDeleteSchedulingModule")
        .classList.remove("d-inline-flex");
      document
        .getElementById("buttonSchedulingModule")
        .classList.add("d-inline-flex");
      // document
      //   .getElementById("buttonEditSchedulingModule")
      //   .classList.add("d-none");
      document
        .getElementById("buttonDeleteSchedulingModule")
        .classList.add("d-none");

      document.getElementById("runScheduling").value = "";
      document
        .querySelectorAll(".schedulingModalCheckbox")
        .forEach((element) => {
          element.checked = false;
        });
      schedulingData = schedulingData.filter(
        (item) => Number(item.module_id) !== Number(idModule)
      );
      document.getElementById("schedulingCancel").click();
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

let schedulingData = [];
let requestSchuleing = true;

async function getScheduleingModules() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  requestSchuleing = false;

  await useApi({
    url: `module/schedule`,
    callback: function (data) {
      schedulingData = data.data || [];
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

// function updateIframe(iframeName, inputId) {
//   const input = document.getElementById(inputId);
//   const iframe = document.getElementsByName(iframeName)[0];

//   if (input && iframe) {
//     iframe.src = input.value;
//   }
// }

if (window.top !== window.self) {
  // داخل iframe هستیم
  window.top.location.href = "/"; // مسیر صفحه اصلی parent
}

function typeModuleEdit(moduleId) {
  let type = [];
  for (let y = 0; y < modulesInfo.length; y++) {
    if (Number(modulesInfo[y].moduleID) === Number(moduleId)) {
      type = modulesInfo[y].moduleType || [];
      break;
    }
  }

  // نرمال‌سازی داده‌ها (تبدیل به حروف کوچک)
  const normalizedData = type.map((item) => String(item).toLowerCase());

  // انتخاب چک‌باکس‌ها
  const epcCheckbox = document.getElementById("moduleTypeEPC");
  const g5cCheckbox = document.getElementById("moduleType5GC");

  // بررسی و فعال کردن چک‌باکس‌ها
  if (normalizedData.includes("epc")) {
    epcCheckbox.checked = true;
  } else {
    epcCheckbox.checked = false;
  }

  if (normalizedData.includes("5gc")) {
    g5cCheckbox.checked = true;
  } else {
    g5cCheckbox.checked = false;
  }

  if (
    normalizedData.includes("5gc") == false &&
    normalizedData.includes("epc") == false
  ) {
    g5cCheckbox.checked = true;
    epcCheckbox.checked = true;
  }
}

let idModule;
let serverModule = [];
function crateServerEditModule(x, moduleId) {
  const selectedModule = modulesInfo.find(
    (moduleItem) => Number(moduleItem.moduleID) === Number(moduleId)
  );
  const servers = selectedModule?.serverIDs || [];
  const serverLength = servers.length;
  idModule = Number(moduleId);
  document.querySelector('input[name="name_nameInputEditModule"]').value =
    document.querySelector(`#tBody3 #tr${x} .td2`).textContent;
  let numberLength = numberAndNameServers.length;
  for (let i = 1; i <= numberLength; i++) {
    let idInputServer = serverCard[i - 1].id;
    let div = document.createElement("div");
    div.setAttribute("class", "form-check");
    let input = document.createElement("input");
    input.setAttribute("class", "form-check-input editModalCheckbox");
    input.setAttribute("data-server-id", `${idInputServer}`);
    input.setAttribute("value", "");
    input.setAttribute("type", "checkbox");
    input.setAttribute("id", `selectServerEdit${idInputServer}`);
    let lable = document.createElement("label");
    lable.setAttribute("class", "form-check-label");
    lable.setAttribute("for", `selectServerEdit${idInputServer}`);
    lable.innerHTML = `${numberAndNameServers[i - 1].nameServer}`;
    div.appendChild(input);
    div.appendChild(lable);
    document.getElementById("ServerEditModule").appendChild(div);
    // document.getElementById("ServerScheduling").appendChild(div);
  }
  serverModule = [];
  for (let x = 0; x < serverLength; x++) {
    serverModule.push(servers[x]);
    document.getElementById(`selectServerEdit${servers[x]}`).checked = true;
  }
}

document
  .getElementById("buttonUpdateModule")
  .addEventListener("click", function () {
    if (btnEditModule == "") {
      // let userName = document.querySelector(
      //   'input[name="name_InputUserNameModule"]'
      // ).value;

      // let password = document.querySelector(
      //   'input[name="name_InputPasswordModule"]'
      // ).value;

      // let saveValue = document.getElementById("saveValue");

      // if (saveValue.checked) {
      //   localStorage.setItem("userNameServer", userName);
      //   localStorage.setItem("passwordServer", password);
      // }

      let moduleName = document.getElementById("moduleName").value;
      let checkBox = document.querySelectorAll(".editModalCheckbox");
      // let userNameUser = document.getElementById("userNameModule").value;
      // let passwordUser = document.getElementById("passwordModule").value;

      const isCheckedBox = [...checkBox].some((checkBox) => checkBox.checked);

      if (moduleName.length < 3) {
        Toastify({
          text: "The module name is less than 3 characters.",
        }).showToast();
      } else if (isCheckedBox == false) {
        Toastify({
          text: "Select the desired server for the module.",
        }).showToast();
      }
      // else if (userNameUser.length < 3) {
      //   Toastify({
      //     text: "The username is less than 3 characters.",
      //   }).showToast();
      // } else if (passwordUser.length < 1) {
      //   Toastify({
      //     text: "The password does not match the repeat field.",
      //   }).showToast();
      // }

      if (
        moduleName.length >= 3 &&
        isCheckedBox != false
        // userNameUser.length >= 3 &&
        // passwordUser.length >= 1
      ) {
        editModules(idModule);
      }
    }
  });

let btnEditModule = "";

async function editModules(idModule) {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  let nameModul = document.querySelector(
    'input[name="name_nameInputEditModule"]'
  ).value;

  let fileInput = document.getElementById("moduleFile");
  let fileModule = fileInput.files[0];

  let typeModule;

  let fivegcType = document.getElementById("moduleType5GC");
  let epcType = document.getElementById("moduleTypeEPC");

  if (fivegcType.checked) {
    typeModule = "5gc";
  } else if (epcType.checked) {
    typeModule = "Epc";
  }

  // let userName = document.querySelector(
  //   'input[name="name_InputUserNameModule"]'
  // ).value;

  // let password = document.querySelector(
  //   'input[name="name_InputPasswordModule"]'
  // ).value;

  // let port = document.querySelector('input[name="name_InputPortModule"]').value;

  let formData = new FormData();

  const selectedServerIds = Array.from(
    document.querySelectorAll(".editModalCheckbox")
  )
    .filter((checkBox) => checkBox.checked)
    .map((checkBox) => Number(checkBox.getAttribute("data-server-id")));

  const selectedModule = modulesInfo.find(
    (moduleItem) => Number(moduleItem.moduleID) === Number(idModule)
  );
  const previousServerIds = (selectedModule?.serverIDs || []).map((id) => Number(id));

  const sortedSelectedServerIds = [...selectedServerIds].sort((a, b) => a - b);
  const sortedPreviousServerIds = [...previousServerIds].sort((a, b) => a - b);

  const isServersChanged =
    sortedSelectedServerIds.length !== sortedPreviousServerIds.length ||
    sortedSelectedServerIds.some((id, index) => id !== sortedPreviousServerIds[index]);

  const shouldSendServers = Boolean(fileModule) || isServersChanged;

  if (shouldSendServers) {
    const selectedServerDetails = getSelectedServersWithCredentials(
      ".editModalCheckbox",
      selectedServerIds
    );

    if (!selectedServerDetails.isValid) {
      Toastify({
        text: selectedServerDetails.error,
        style: {
          background: "linear-gradient(to right,rgb(255, 0, 0),rgb(231, 0, 0))",
        },
      }).showToast();
      document.getElementById("idLoading").style.display = "none";
      document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
      return;
    }

    selectedServerDetails.servers.forEach((server, index) => {
      formData.append(`servers[${index}][id]`, server.id);
      formData.append(`servers[${index}][username]`, server.username);
      formData.append(`servers[${index}][password]`, server.password);
      formData.append(`servers[${index}][port]`, server.port);
    });
  }

  // اضافه کردن مقادیر به FormData
  formData.append("module_id", idModule);
  formData.append("name", nameModul);
  if (fileModule) {
    formData.append("config_file", fileModule); // فایل را اضافه می‌کنیم
  }
  formData.append("type", typeModule);

  // اضافه کردن آرایه server_ids به‌صورت مقادیر جداگانه
  // serverIds.forEach((id) => {
  //   formData.append("server_ids[]", id); // هر مقدار جداگانه اضافه می‌شود
  // });

  // اضافه کردن سایر مقادیر
  // if (port) {
  //   formData.append("port", port);
  // }

  // formData.append("username", userName);
  // formData.append("password", password);

  // let saveValue = document.getElementById("saveValue");

  let requestSuccess = false;
  await useApi({
    url: `edit-module`,
    method: "post",
    data: formData,
    headers: {
      "Content-Type": "multipart/form-data",
    },
    callback: async function (data) {
      requestSuccess = true;
      Toastify({
        text: data.message,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();

      const pageToRefresh = Number(currentPageModule || urlModule || 1);
      modulesInfo = [];
      await showModuls(pageToRefresh);

      let button = document.getElementById("buttonUpdateModule");
      button.setAttribute("data-bs-dismiss", "modal");
      btnEditModule = "modalEditModule";
      button.click();
    },
    errorCallback: async function () {
      requestSuccess = false;
      const pageToRefresh = Number(currentPageModule || urlModule || 1);
      modulesInfo = [];
      await showModuls(pageToRefresh);
    },
  });

  if (!requestSuccess) {
    Toastify({
      text: "Update failed. Changes were not saved.",
      style: {
        background: "linear-gradient(to right,rgb(255, 0, 0),rgb(231, 0, 0))",
      },
    }).showToast();
  }

  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

let moduleIdRemove;
let trModuleRemove;
function iconDeleteModule() {
  document.querySelectorAll(".deleteModuleClick").forEach(function (e) {
    e.addEventListener("click", function () {
      const trElement = e.closest("tr");
      const dataId = trElement.getAttribute("data-id");

      let valueNameModule = document.querySelector(`#tr${e.id} .td2`).innerHTML;
      document.getElementById("spanRemoveModule").innerHTML = valueNameModule;
      trModuleRemove = `#tr${e.id}`;
      moduleIdRemove = dataId;
    });
  });
}

document
  .getElementById("subDeletModule")
  .addEventListener("click", function () {
    DeleteModules();
  });

let pageModule;

async function DeleteModules() {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  await useApi({
    url: `delete-module`,
    method: "delete",
    data: { module_id: moduleIdRemove },
    callback: function (data) {
      modulesInfo = modulesInfo.filter(
        (item) => item.moduleID !== data.module.id
      );
      document.querySelector(`${trModuleRemove}`)?.remove();
      let items = arrRowShowModule.filter((item) => item != data.module.id);
      pageModule = items;
      arrRowShowModule = items;
      let endPageShowUser = Number(arrRowShowModule.length) + pageShowModule;
      let counter = 0;
      for (let x = pageShowModule - 1; x < endPageShowUser - 1; x++) {
        let targetId = arrRowShowModule[counter]; // مقدار data-id که می‌خواهید انتخاب کنید
        counter++;
        document.querySelector(`[data-id="${targetId}"] .td1`).innerHTML = x;
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
  let lengthTbody3 = document.getElementById("tBody3").rows.length;
  if (!lengthTbody3) {
    document.getElementById("divImgModule").style.display = "block";
  } else {
    document.getElementById("divImgModule").style.display = "none";
  }
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";

  if (pageModule.length == 0) {
    document.getElementById("idLoading").style.display = "flex";
    removeModuleParam();
    showModuls(1);
  }
}

function removeModuleParam() {
  const url = new URL(window.location.href);
  url.searchParams.delete("module"); // حذف پارامتر "module"

  history.replaceState(null, "", url.pathname + url.search + url.hash);
}

document.getElementById("addModal").addEventListener("click", function () {
  btnAddModule = "";
  document.getElementById("buttonAddModule").removeAttribute("data-bs-dismiss");
  // let userNameServer = localStorage.getItem("userNameServer");
  // let passwordServer = localStorage.getItem("passwordServer");
  // let port = localStorage.getItem("port");

  document.querySelector('input[name="name_nameInputAddModule"]').value = "";
  document.querySelector("#moduleAddFile").value = "";
  document.querySelector("#moduleAddTypeEPC").checked = false;
  document.querySelector("#moduleAddType5GC").checked = false;
  // document.querySelector("#saveValueAdd").checked = false;

  // if (userNameServer && passwordServer) {
  //   document.querySelector('input[name="name_InputUserNameAddModule"]').value =
  //     userNameServer;
  //   document.querySelector('input[name="name_InputPasswordAddModule"]').value =
  //     passwordServer;
  // } else {
  //   document.querySelector('input[name="name_InputUserNameAddModule"]').value =
  //     "";
  //   document.querySelector('input[name="name_InputPasswordAddModule"]').value =
  //     "";
  // }

  // document.querySelector('input[name="name_InputPortAddModule"]').value = port;

  createModuleServerCredentials = {};
  document.getElementById("ServerAddModule").innerHTML = "";
  crateServerAddModule();
  for (let i = 0; i < numberAndNameServers.length; i++) {
    if (numberAndNameServers[i].serverStatus == 1) {
      for (let x = 0; x < numberAndNameServers.length; x++) {
        if (serverCard[i].name == numberAndNameServers[x].nameServer) {
          let id = serverCard[i].id;
          document.querySelector(
            `.addModalCheckbox#selectServer${id}`
          ).disabled = true;
        }
        // let idInputServer = serverCard[i - 1].name;
      }
    }
  }
});

function crateServerAddModule() {
  let numberLength = numberAndNameServers.length;
  for (let i = 1; i <= numberLength; i++) {
    let idInputServer = serverCard[i - 1].id;
    let serverName = numberAndNameServers[i - 1].nameServer;
    let savedServerCredentials =
      createModuleServerCredentials[idInputServer] ||
      dataServer.find((server) => Number(server.id) === Number(idInputServer)) ||
      {};

    let wrapper = document.createElement("div");
    wrapper.setAttribute("class", "border rounded p-2 mb-2");

    let div = document.createElement("div");
    div.setAttribute("class", "form-check mb-2");
    let input = document.createElement("input");
    input.setAttribute("class", "form-check-input addModalCheckbox");
    input.setAttribute("data-server-id", `${idInputServer}`);
    input.setAttribute("value", "");
    input.setAttribute("type", "checkbox");
    input.setAttribute("id", `selectServer${idInputServer}`);

    let lable = document.createElement("label");
    lable.setAttribute("class", "form-check-label");
    lable.setAttribute("for", `selectServer${idInputServer}`);
    lable.innerHTML = `${serverName}`;

    div.appendChild(input);
    div.appendChild(lable);

    let credentialsDiv = document.createElement("div");
    credentialsDiv.setAttribute("id", `serverCredentialsRow${idInputServer}`);
    credentialsDiv.setAttribute("class", "server-credential-block d-none");
    credentialsDiv.innerHTML = `
      <div class="row g-2">
        <div class="col-12 col-md-4">
          <input type="text" class="form-control form-control-sm addModuleServerUsername" data-server-id="${idInputServer}" placeholder="Username" value="${savedServerCredentials.username || ""}" />
        </div>
        <div class="col-12 col-md-4">
          <input type="password" class="form-control form-control-sm addModuleServerPassword" data-server-id="${idInputServer}" placeholder="Password" value="${savedServerCredentials.password || ""}" />
        </div>
        <div class="col-12 col-md-4">
          <input type="number" min="1" class="form-control form-control-sm addModuleServerPort" data-server-id="${idInputServer}" placeholder="Port (default 22)" value="${savedServerCredentials.port || ""}" />
        </div>
      </div>
    `;

    input.addEventListener("change", function () {
      if (this.checked) {
        credentialsDiv.classList.remove("d-none");
      } else {
        credentialsDiv.classList.add("d-none");
      }
    });

    wrapper.appendChild(div);
    wrapper.appendChild(credentialsDiv);
    document.getElementById("ServerAddModule").appendChild(wrapper);
  }

  document
    .querySelectorAll(
      ".addModuleServerUsername, .addModuleServerPassword, .addModuleServerPort"
    )
    .forEach((inputElement) => {
      inputElement.addEventListener("input", function () {
        const serverId = Number(this.getAttribute("data-server-id"));
        const username = document
          .querySelector(`.addModuleServerUsername[data-server-id="${serverId}"]`)
          ?.value.trim();
        const password = document
          .querySelector(`.addModuleServerPassword[data-server-id="${serverId}"]`)
          ?.value.trim();
        const portRaw = document
          .querySelector(`.addModuleServerPort[data-server-id="${serverId}"]`)
          ?.value.trim();

        createModuleServerCredentials[serverId] = {
          id: serverId,
          username,
          password,
          port: portRaw || 22,
        };
      });
    });
}

document
  .getElementById("buttonAddModule")
  .addEventListener("click", function () {
    if (btnAddModule == "") {
      // let userName = document.querySelector(
      //   'input[name="name_InputUserNameAddModule"]'
      // ).value;

      // let password = document.querySelector(
      //   'input[name="name_InputPasswordAddModule"]'
      // ).value;

      // let saveValue = document.getElementById("saveValueAdd");

      // if (saveValue.checked) {
      //   localStorage.setItem("userNameServer", userName);
      //   localStorage.setItem("passwordServer", password);
      // }
      let moduleFile;

      moduleFile = document.getElementById("moduleAddFile").value;
      console.log(moduleFile)

      let moduleName = document.getElementById("moduleAddName").value;
      let radioButtons = document.querySelectorAll(
        'input[name="flexRadioDefault"]'
      );
      let checkBox = document.querySelectorAll(".addModalCheckbox");
      // let userNameUser = document.getElementById("userNameAddModule").value;
      // let passwordUser = document.getElementById("passwordAddModule").value;

      const isChecked = [...radioButtons].some((radio) => radio.checked);
      const isCheckedBox = [...checkBox].some((checkBox) => checkBox.checked);
      if (moduleName.length < 3) {
        Toastify({
          text: "The module name is less than 3 characters.",
        }).showToast();
      } else if (moduleFile == "") {
        Toastify({
          text: "No file has been selected.",
        }).showToast();
      } else if (isChecked == false) {
        Toastify({
          text: "Select the desired module type.",
        }).showToast();
      } else if (isCheckedBox == false) {
        Toastify({
          text: "Select the desired server for the module.",
        }).showToast();
      }
      // else if (userNameUser.length < 3) {
      //   Toastify({
      //     text: "The username is less than 3 characters.",
      //   }).showToast();
      // } else if (passwordUser.length < 1) {
      //   Toastify({
      //     text: "The password does not match the repeat field.",
      //   }).showToast();
      // }

      if (
        moduleName.length >= 3 &&
        moduleFile != "" &&
        isChecked != false &&
        isCheckedBox != false
        // userNameUser.length >= 3 &&
        // passwordUser.length >= 1
      ) {
        addModules();
      }
    }
  });

let btnAddModule = "";
let createModuleServerCredentials = {};

function getServerNameById(serverId) {
  return (
    numberAndNameServers.find((server) => Number(server.idServer) === Number(serverId))
      ?.nameServer || `ID ${serverId}`
  );
}

function getSelectedServersWithCredentials(checkboxSelector, selectedServerIds) {
  const selectedIds = selectedServerIds
    ? selectedServerIds.map((id) => Number(id))
    : Array.from(document.querySelectorAll(checkboxSelector))
      .filter((checkBox) => checkBox.checked)
      .map((checkBox) => Number(checkBox.getAttribute("data-server-id")));

  const selectedServers = [];

  for (const serverId of selectedIds) {
    const inlineUsername = document
      .querySelector(`.addModuleServerUsername[data-server-id="${serverId}"]`)
      ?.value?.trim?.();
    const inlinePassword = document
      .querySelector(`.addModuleServerPassword[data-server-id="${serverId}"]`)
      ?.value?.trim?.();
    const inlinePort = document
      .querySelector(`.addModuleServerPort[data-server-id="${serverId}"]`)
      ?.value?.trim?.();

    const cachedCredentials = createModuleServerCredentials[serverId] || {};
    const storedServer = dataServer.find(
      (item) => Number(item.id) === Number(serverId)
    );

    const username =
      inlineUsername || cachedCredentials.username || storedServer?.username?.trim?.() || "";
    const password =
      inlinePassword || cachedCredentials.password || storedServer?.password?.trim?.() || "";

    const port = Number(
      inlinePort || cachedCredentials.port || storedServer?.port || 22
    );

    const missingFields = [];
    if (!username) missingFields.push("username");
    if (!password) missingFields.push("password");

    if (missingFields.length > 0) {
      return {
        isValid: false,
        error: `Server ${getServerNameById(serverId)} (ID ${serverId}) is missing ${missingFields.join(
          " and "
        )}.`,
      };
    }

    selectedServers.push({
      id: Number(serverId),
      username,
      password,
      port: Number.isInteger(port) && port > 0 ? port : 22,
    });
  }

  return {
    isValid: true,
    servers: selectedServers,
    selectedIds,
  };
}

async function addModules() {
  if (roleUserGetMe == "visitor") return;
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  let nameModul = document.querySelector(
    'input[name="name_nameInputAddModule"]'
  ).value;
  let fileInput = document.getElementById("moduleAddFile");
  let fileModule = fileInput.files[0];
  let typeModule;

  let fivegcType = document.getElementById("moduleAddType5GC");
  let epcType = document.getElementById("moduleAddTypeEPC");

  if (fivegcType.checked && epcType.checked) {
    typeModule = "Epc, 5gc";
  } else if (fivegcType.checked) {
    typeModule = "5gc";
  } else if (epcType.checked) {
    typeModule = "Epc";
  }

  // let path_config = document.querySelector(
  //   'input[name="name_InputPathConfigAddModule"]'
  // ).value;

  // let path_run_config = document.querySelector(
  //   'input[name="name_InputPathRunConfigAddModule"]'
  // ).value;

  // let userName = document.querySelector(
  //   'input[name="name_InputUserNameAddModule"]'
  // ).value;

  // let password = document.querySelector(
  //   'input[name="name_InputPasswordAddModule"]'
  // ).value;

  // let port = document.querySelector(
  //   'input[name="name_InputPortAddModule"]'
  // ).value;

  let formData = new FormData();
  const selectedServerDetails = getSelectedServersWithCredentials(
    ".addModalCheckbox"
  );

  if (!selectedServerDetails.isValid) {
    Toastify({
      text: selectedServerDetails.error,
      style: {
        background: "linear-gradient(to right,rgb(255, 0, 0),rgb(231, 0, 0))",
      },
    }).showToast();
    document.getElementById("idLoading").style.display = "none";
    document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
    return;
  }

  selectedServerDetails.servers.forEach((server, index) => {
    formData.append(`servers[${index}][id]`, server.id);
    formData.append(`servers[${index}][username]`, server.username);
    formData.append(`servers[${index}][password]`, server.password);
    formData.append(`servers[${index}][port]`, server.port);
  });

  // اضافه کردن مقادیر به FormData
  //  fileModule.name.replace(/\.\w*/gm, "")
  formData.append("name", nameModul)
  if (fileModule) {
    formData.append("config_file", fileModule); // فایل را اضافه می‌کنیم
    formData.append("configType", fileModule.name.replace(/\.\w*/gm, ""))
  }
  formData.append("type", typeModule);
  console.log(formData.get("configType"))
  // serverIds.forEach((serverId) => {
  //   formData.append("servers[]", serverId);
  // });

  // اضافه کردن آرایه server_ids به‌صورت مقادیر جداگانه
  // let number = 0;
  // serverIds.forEach((id) => {
  //   formData.append(`server_id[${number}]`, id); // هر مقدار جداگانه اضافه می‌شود
  //   number++;
  // });

  // formData.append("path_config", path_config);
  // formData.append("path_run_config", path_run_config);

  // اضافه کردن سایر مقادیر
  // if (port) {
  //   formData.append("port", port);
  // }

  // formData.append("username", userName);
  // formData.append("password", password);

  await useApi({
    url: `create-module`,
    method: "post",
    data: formData,
    headers: {
      "Content-Type": "multipart/form-data", // این هدر مهم است
    },
    callback: function (data) {
      console.log(data)
      // localStorage.setItem("port", port);
      let serverId = [];
      let endPageShowUser =
        Number(arrRowShowModule.length) + pageShowModule - 1;
      arrRowShowModule.push(data.data.created_modules[0].module.module_id);

      for (let i = 0; i < data.data.created_modules.length; i++) {
        serverId.push(data.data.created_modules[i].server.server_id);
      }

      if (isNaN(numberTrModule)) {
        numberTrModule = 0;
      }

      let tr = document.createElement("tr");
      tr.setAttribute("id", `tr${++numberTrModule}`);
      tr.setAttribute(
        "data-id",
        `${data.data.created_modules[0].module.module_id}`
      );
      modulesInfo.push({
        serverIDs: serverId,
        moduleID: data.data.created_modules[0].module.module_id,
        moduleName: data.data.created_modules[0].module.module_name,
        moduleType: [data.data.created_modules[0].module.module_type],
        configType: data.data.created_modules[0].module.config_type,
      });

      for (let x = 1; x <= 8; x++) {
        let td = document.createElement("td");
        td.setAttribute("class", `td${x}`);

        if (x == 1) {
          // Row
          td.innerHTML = endPageShowUser;

        } else if (x == 2) {
          // nameModule
          td.innerHTML =
            data.data.created_modules[0].module.module_name;

        } else if (x == 3) {
          // Servers
          let arrServers = [];
          let span = document.createElement("span");

          for (let y = 0; y < data.data.created_modules.length; y++) {
            arrServers.push(data.data.created_modules[y].server.server_name);
          }

          span.innerHTML = arrServers.join(", ");
          td.appendChild(span);

        } else if (x == 4) {
          // Type (EPC / 5GC)
          let arrType = [];
          for (let y = 0; y < 1; y++) {
            arrType.push(
              data.data.created_modules[y].module.module_type.toUpperCase()
            );
          }
          td.innerHTML = arrType.join(", ");

        } else if (x == 5) {
          // Scheduling (SVG — بدون تغییر)
          td.setAttribute("class", `td5`);
          let svgElemEdit = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" id="${numberTrModule}" data-id-modules = "${numberTrModule}" fill="currentColor" data-bs-toggle="modal" data-bs-target="#schedulingModule" class="bi bi-calendar4-week schedulingModule cursorPointer" viewBox="0 0 16 16"> <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v1h14V3a1 1 0 0 0-1-1zm13 3H1v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/> <path d="M11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-2 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/> </svg>`;
          td.insertAdjacentHTML("afterbegin", svgElemEdit);

        } else if (x == 6) {
          // Edit (SVG — بدون تغییر)
          td.setAttribute("class", `td6 tdEditModule`);
          let svgElemEdit = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" id="${numberTrModule}" data-id-modules = "${numberTrModule}" data-bs-toggle="modal" data-bs-target="#editModule" class="bi bi-pencil-square editModuleClick cursorPointer"
          aria-label="Edit module"
          data-test="module-edit-btn" viewBox="0 0 16 16" > <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" /> <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" /> </svg>`;
          td.insertAdjacentHTML("afterbegin", svgElemEdit);

        } else if (x == 7) {
          // Delete (SVG — بدون تغییر)
          td.setAttribute("class", `td7 tdRemoveModule`);
          let svgElemEdit = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" id="${numberTrModule}" data-id-modules = "${numberTrModule}" data-bs-toggle="modal" data-bs-target="#removeModule" fill="currentColor" class="bi bi-trash3 deleteModuleClick cursorPointer"
           aria-label="Delete module"
           data-test="module-delete-btn" viewBox="0 0 16 16"> <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/> </svg>`;
          td.insertAdjacentHTML("afterbegin", svgElemEdit);
        }

        tr.appendChild(td);

      }
      document.getElementById("tBody3").appendChild(tr);
      modalEditModule();
      modalSchedulingModule();
      iconDeleteModule();

      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
      let button = document.getElementById("buttonAddModule");
      button.setAttribute("data-bs-dismiss", "modal");
      btnAddModule = "modalAddModule";
      button.click(); // کلیک برنامه‌نویسی
      let lengthTbody3 = document.getElementById("tBody3").rows.length;
      if (!lengthTbody3) {
        document.getElementById("divImgModule").style.display = "block";
      } else {
        document.getElementById("divImgModule").style.display = "none";
      }
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";

  removeModulePermission();
  editModulePermission();
  scheduleModulePermission();
}

let requestShowAddress = true;

async function showAddress() {
  requestShowAddress = false;
  document.getElementById("idLoading").style.display = "flex";

  await useApi({
    url: `show-address`,
    callback: function (data) {
      if (data.length > 0) {
        document.getElementById(
          "url_input_a"
        ).value = `${data[0].zabbix_address}`;
        document.getElementById("url_input_b").value =
          data[0].elk_address || "";
      }
    },
  });
  document.getElementById("idLoading").style.display = "none";
}

async function sendAddress() {
  document.getElementById("idLoading").style.display = "flex";
  let urlZabbix = document.getElementById("url_input_a").value;
  let urlElk = document.getElementById("url_input_b").value;

  await useApi({
    method: "post",
    url: `add-address`,
    data: {
      zabbix_address: urlZabbix,
      elk_address: urlElk,
    },
  });
  document.getElementById("idLoading").style.display = "none";
}
//mahdi's js..............................................................

let questionMark = document.querySelector(".userQuestionInit");
let InnerModal =
  questionMark.addEventListener("mouseover", () => {
    document.querySelector(".userQuestionModal").style.display = "flex"
  })
document.querySelector(".userQuestionModal").addEventListener("mouseleave", () => {
  document.querySelector(".userQuestionModal").style.display = "none"
})
document.querySelector(".userQuestionModal").addEventListener("mouseover", () => {
  document.querySelectorAll(".UsersLevels").forEach(item => {
    item.addEventListener("mouseover", () => {

      switch (item.innerText) {
        case "admin":
          document.querySelector(".userQuestionModal_innerModal").style.display = "block"
          document.querySelector(".adminPermissions").style.display = "block"
          break;
        case "expert":
          document.querySelector(".userQuestionModal_innerModal").style.display = "block"
          document.querySelector(".expertpermissions").style.display = "block"
          break;
        case "user":
          document.querySelector(".userQuestionModal_innerModal").style.display = "block"
          document.querySelector(".userPermissions").style.display = "block"
          break;
        default:
          break;
      }
      item.addEventListener("mouseleave", () => {
        switch (item.innerText) {
          case "admin":
            document.querySelector(".userQuestionModal_innerModal").style.display = "none"
            document.querySelector(".adminPermissions").style.display = "none"
            break;
          case "expert":
            document.querySelector(".userQuestionModal_innerModal").style.display = "none"
            document.querySelector(".expertpermissions").style.display = "none"
            break;
          case "user":
            document.querySelector(".userQuestionModal_innerModal").style.display = "none"
            document.querySelector(".userPermissions").style.display = "none"
            break;
          default:
            break;
        }
      })
    })
  })
})
//........................................................................
function removeUrlLogParams() {
  // Get the current URL
  const url = new URL(window.location.href);
  url.searchParams.delete("log");
  // Use history.replaceState to update the URL without refreshing the page
  window.history.replaceState({}, "", url);
}

function removeUrlModuleParams() {
  // Get the current URL
  const url = new URL(window.location.href);
  url.searchParams.delete("module");
  // Use history.replaceState to update the URL without refreshing the page
  window.history.replaceState({}, "", url);
}

function removeUrlUserParams() {
  // Get the current URL
  const url = new URL(window.location.href);
  url.searchParams.delete("user");
  // Use history.replaceState to update the URL without refreshing the page
  window.history.replaceState({}, "", url);
}

document.querySelectorAll(".ActiveMenuScroll").forEach(function (e) {
  e.addEventListener("click", function () {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });
});

document.querySelectorAll(".subMenuClick").forEach(function (e) {
  e.addEventListener("click", function () {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });
});

// const iframeSubscribers = document.getElementById("iframeSubscribers");
// const iframeDocumentSubscribers =
//   iframeSubscribers.contentDocument || iframeSubscribers.contentWindow.document;

// // ایجاد محتوای HTML برای iframe
// iframeDocumentSubscribers.open();
// iframeDocumentSubscribers.write(`
//     <html>
//         <body>
//          <img class="imgSubscribers" src="${imageSubscribers}" alt="عکس نمونه">
//         </body>
//     </html>
// `);
// iframeDocumentSubscribers.close();

// const imgSubscribers =
//   iframeDocumentSubscribers.querySelector(".imgSubscribers");
// imgSubscribers.style.width = "100%";
// imgSubscribers.style.height = "580px";

document
  .getElementById("buttonSubscribers")
  .addEventListener("click", function () {
    setSubscribers();
  });

async function setSubscribers() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  let subscriber_address = document.getElementById("urlSubscribers").value;

  await useApi({
    method: "post",
    url: "set-subscriber-address",
    data: {
      subscriber_address,
    },
    callback: function (data) {
      const url = subscriber_address.trim();
      const finalUrl = url.startsWith("http") ? url : `https://${url}`;

      // باز کردن لینک در تب جدید
      window.open(finalUrl, "_blank");
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 1)";
}

// const iframeZabbix = document.getElementById("IframeZabbix");
// const iframeDocumentZabbix =
//   iframeZabbix.contentDocument || iframeZabbix.contentWindow.document;

// // ایجاد محتوای HTML برای iframe
// iframeDocumentZabbix.open();
// iframeDocumentZabbix.write(`
//     <html>
//         <body>
//          <img class="imgMonitoring" src="${imageZabbix}" alt="عکس نمونه">
//         </body>
//     </html>
// `);
// iframeDocumentZabbix.close();

// const imgZabbix = iframeDocumentZabbix.querySelector(".imgMonitoring");
// imgZabbix.style.width = "100%";
// imgZabbix.style.height = "480px";

// const iframeELK = document.getElementById("IframeELK");
// const iframeDocumentELK =
//   iframeELK.contentDocument || iframeELK.contentWindow.document;

// // ایجاد محتوای HTML برای iframe
// iframeDocumentELK.open();
// iframeDocumentELK.write(`
//     <html>
//         <body>
//          <img class="imgMonitoringELK" src="${imageElk}" alt="عکس نمونه">
//         </body>
//     </html>
// `);
// iframeDocumentELK.close();

// const imgELK = iframeDocumentELK.querySelector(".imgMonitoringELK");
// imgELK.style.width = "100%";
// imgELK.style.height = "480px";

/*************** */
/*************** */
/*************** bootstrap tab in url */
/*************** */
/*************** */

const updateUrlPath = (tabId) => {
  const baseUrl = window.location.href;
  let newPath = "";

  if (baseUrl.endsWith(".html")) {
    newPath = `${baseUrl}/${tabId}`;
  } else {
    newPath = baseUrl.split("/").slice(0, -1).join("/") + "/" + tabId;
  }
  window.location.hash = tabId;
};

function getHash() {
  return (
    window.location.hash ||
    defaultPage ||
    getPageByPermission(permissionUsers[0])
  );
}

const activateTabFromUrlPath = () => {
  let hash = getHash();
  const targetTab = document.querySelector(hash);
  if (targetTab) {
    showTabContent(hash);
    updateUrlPath(hash); // Update URL to default tab if no tab is specified
  }
};

// Event listener for tab change
document.querySelectorAll("button.styleButton").forEach((button) => {
  button.addEventListener("shown.bs.tab", (event) => {
    updateUrlPath(event.target.dataset.bsTarget);
  });
});

document.querySelectorAll("button.styleTabRRU").forEach((button) => {
  button.addEventListener("shown.bs.tab", (event) => {
    updateUrlPath(event.target.dataset.bsTarget);
  });
});

document.querySelectorAll("button.styleTabRRUsubMenu").forEach((button) => {
  button.addEventListener("shown.bs.tab", (event) => {
    updateUrlPath(event.target.dataset.bsTarget);
  });
});

document.querySelectorAll(".clickTab").forEach((method) => {
  method.addEventListener("click", function () {
    updateUrlPath(getHash());
    showTabContent(getHash());
  });
});

let offClick = true;

async function showTabContent(hash) {
  let url = new URL(window.location).searchParams;
  let moduleIdUrl = url.get("log");
  urlLog = Number(moduleIdUrl);

  let urlModules = new URL(window.location).searchParams;
  let moduleIdUrlModule = urlModules.get("module");
  urlModule = Number(moduleIdUrlModule);

  let urlUsers = new URL(window.location).searchParams;
  let moduleIdUrlUsers = urlUsers.get("user");
  urlUser = Number(moduleIdUrlUsers);

  document.querySelectorAll(".tab-pane").forEach((method) => {
    method?.classList.remove("active", "show");
  });
  document.querySelectorAll(".clickTab").forEach((method) => {
    method?.classList.remove("active");
  });

  document.querySelector(`${hash}`)?.classList.add("active", "show");
  if (offClick) {
    switch (hash) {
      case "#v-servers-home":
        ActiveMenu(1);
        showAllServer();
        permissionShow();
        break;
      case "#v-access-levels":
        subMenu(1);
        document
          .getElementById("v-userManagement")
          ?.classList.remove("collapsed");
        document.getElementById("collapseAppManagement")?.classList.add("show");
        document.getElementById("idLoading").style.display = "none";
        break;
      case "#v-users":
        if (project == "BBDH") {
          subMenu(2);
          document
            .getElementById("v-userManagement")
            ?.classList.remove("collapsed");
          document
            .getElementById("collapseAppManagement")
            ?.classList.add("show");
        } else {
          ActiveMenu(2);
        }
        await showAllServer();
        arrRowShowAllUser = [];
        if (urlUser == 0) {
          urlUser = 1;
        }
        showUsers(urlUser);
        desiredPageUser = urlUser;
        permissionShow();
        break;
      case "#v-monitoring":
        // if ([window.top](blocked) !== window.self) {
        //   window.top.location = window.self.location;
        // }
        allMonitoring();
        showAddress();
        ActiveMenu(3);
        document.getElementById("Elk")?.classList.remove("active");
        document.getElementById("zabbix")?.classList.add("active");
        document.getElementById("tab1")?.classList.add("show");
        document.getElementById("tab1")?.classList.add("active");
        break;
      case "#v-log":
        if (project == "RRU") {
          document
            .getElementById("menuFaultsRRU")
            ?.classList.remove("activeMenuRRU");
          document
            .getElementById("menuSettingRRU")
            ?.classList.remove("activeMenuRRU");
          document
            .getElementById("menuSettingRRU")
            ?.classList.add("activeMenuRRU");
        }
        ActiveMenu(4);
        if (urlLog == 0) {
          urlLog = 1;
        }
        pageLog(urlLog);
        desiredPageLog = urlLog;
        break;
      case "#v-subscribers":
        document.getElementById("idLoading").style.display = "none";
        ActiveMenu(6);
        getSubscribers();
        break;
      case "#v-logManagement":
        if (project == "RRU") {
          document
            .getElementById("menuSettingRRU")
            ?.classList.remove("activeMenuRRU");
          document
            .getElementById("menuFaultsRRU")
            ?.classList.remove("activeMenuRRU");
          document
            .getElementById("menuFaultsRRU")
            ?.classList.add("activeMenuRRU");
        }
        document.getElementById("idLoading").style.display = "none";
        ActiveMenu(12);
        break;
      case "#v-performanceManagement":
        if (project == "RRU") {
          document
            .getElementById("menuSettingRRU")
            ?.classList.remove("activeMenuRRU");
          document
            .getElementById("menuFaultsRRU")
            ?.classList.remove("activeMenuRRU");
          document
            .getElementById("menuFaultsRRU")
            ?.classList.add("activeMenuRRU");
        }
        document.getElementById("idLoading").style.display = "none";
        ActiveMenu(13);
        break;
      case "#v-setting":
        ActiveMenu(8);
        loginSMS();
        configBackup();
        // historyBackup();
        reCaptcha();
        if (roleUserGetMe == "admin") {
          configSMS();
        }
        // serverIP();
        getConnectionReCaptcha();
        twoFAstatus();
        document.getElementById("tabSms")?.classList.add("show");
        document.getElementById("tabSms")?.classList.add("active");
        break;
      case "#v-facePlate":
        document.getElementById("idLoading").style.display = "none";
        ActiveMenu(9);
        break;
      case "#v-trace":
        ActiveMenu(10);
        await showAllServer();
        permissionShow();
        await showModuls();
        showNameServerTrace();
        document.getElementById("idLoading").style.display = "none";
        break;
      case "#v-kpi":
        ActiveMenu(14);
        indexKpi();
        break;
      case "#v-route":
        ActiveMenu(11);
        await showAllServer();
        permissionShow();
        showNameServerRoute();
        document.getElementById("idLoading").style.display = "none";
        break;
      case "#v-ping":
        ActiveMenu(7);
        await showAllServer();
        permissionShow();
        showServerTabPing();
        let username = localStorage.getItem("userNameServer");
        let password = localStorage.getItem("passwordServer");
        // document.getElementById("usernamePing").value = username;
        // document.getElementById("passwordPing").value = password;
        document.getElementById("idLoading").style.display = "none";
        // document.getElementById("portPing").value =
        //   localStorage.getItem("port");
        break;
      case "#v-module":
        if (project == "RRU") {
          document
            .getElementById("menuFaultsRRU")
            ?.classList.remove("activeMenuRRU");
          document
            .getElementById("menuSettingRRU")
            ?.classList.remove("activeMenuRRU");
          document
            .getElementById("menuSettingRRU")
            ?.classList.add("activeMenuRRU");
        }
        ActiveMenu(5);
        arrRowShowModule = [];
        if (urlModule == 0) {
          urlModule = 1;
        }
        await showAllServer();
        permissionShow();
        showModuls(urlModule);
        if (!scheduleModule) {
          getScheduleingModules();
        }
        desiredPageModule = urlModule;
        break;
    }
    offClick = false;
  }
}

let isStatusToggle;
let checkBox = document.getElementById("idInputToggle");

async function twoFAstatus() {
  document.getElementById("idLoading").style.display = "flex";
  if (document.getElementById("toggle2FA")) {
    await useApi({
      url: "get-2FA-status",
      callback: function (data) {
        isStatusToggle = data[0].is_login_2FA;
      },
    });
    // تنظیم وضعیت اولیه بر اساس متغیر
    if (isStatusToggle == 1) {
      checkBox.checked = true; // toggle به حالت آبی (سمت راست)
      document.querySelector(".h2fa").innerHTML = "ON";
      toggleLoginSMS.checked = false; // toggle به حالت آبی (سمت راست)
      toggleLoginSMS.disabled = true;
      document.querySelector(".loginSMS").innerHTML = "OFF";
    } else {
      checkBox.checked = false; // toggle به حالت سیاه (سمت چپ)
      document.querySelector(".h2fa").innerHTML = "OFF";
    }
  }
  document.getElementById("idLoading").style.display = "none";
}

if (document.getElementById("toggle2FA")) {
  // رویداد کلیک برای تغییر وضعیت toggle
  checkBox.addEventListener("click", () => {
    if (checkBox.checked) {
      isStatusToggle = 1;
      checkBox.checked = true;
      document.querySelector(".h2fa").innerHTML = "ON";
      toggleLoginSMS.checked = false; // toggle به حالت آبی (سمت راست)
      toggleLoginSMS.disabled = true;
      document.querySelector(".loginSMS").innerHTML = "OFF";
    } else {
      toggleLoginSMS.disabled = false;
      loginSMS();
      isStatusToggle = 0;
      checkBox.checked = false;
      document.querySelector(".h2fa").innerHTML = "OFF";
    }
    twoGetFAstatus();
  });
}

async function twoGetFAstatus() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  await useApi({
    method: "post",
    url: "set-2FA",
    data: {
      is_login_2FA: isStatusToggle,
    },
    callback: function (data) {
      isStatusToggle = data.data;
      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
    },
    errorCallback: function () {
      isStatusToggle = +!isStatusToggle;
    },
  });

  if (isStatusToggle == 1) {
    checkBox.checked = true; // toggle به حالت آبی (سمت راست)
    document.querySelector(".h2fa").innerHTML = "ON";
    toggleLoginSMS.checked = false; // toggle به حالت آبی (سمت راست)
    toggleLoginSMS.disabled = true;
    document.querySelector(".loginSMS").innerHTML = "OFF";
  } else {
    checkBox.checked = false; // toggle به حالت سیاه (سمت چپ)
    document.querySelector(".h2fa").innerHTML = "OFF";
  }

  if (isStatusLoginSMS == 1) {
    toggleLoginSMS.checked = true; // toggle به حالت آبی (سمت راست)
    document.querySelector(".loginSMS").innerHTML = "ON";
  } else {
    toggleLoginSMS.checked = false; // toggle به حالت سیاه (سمت چپ)
    document.querySelector(".loginSMS").innerHTML = "OFF";
  }

  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

let isStatusLoginSMS;
let toggleLoginSMS = document.getElementById("idInputToggleLoginSMS");

async function loginSMS() {
  await useApi({
    url: "get-login-sms-status",
    callback: function (data) {
      isStatusLoginSMS = data[0].is_login_sms;
    },
  });

  // تنظیم وضعیت اولیه بر اساس متغیر
  if (isStatusLoginSMS == 1) {
    toggleLoginSMS.checked = true; // toggle به حالت آبی (سمت راست)
    document.querySelector(".loginSMS").innerHTML = "ON";
  } else {
    toggleLoginSMS.checked = false; // toggle به حالت سیاه (سمت چپ)
    document.querySelector(".loginSMS").innerHTML = "OFF";
  }
}

toggleLoginSMS.addEventListener("click", () => {
  if (toggleLoginSMS.checked) {
    isStatusLoginSMS = 1;
    toggleLoginSMS.checked = true;
    document.querySelector(".loginSMS").innerHTML = "ON";
  } else {
    isStatusLoginSMS = 0;
    toggleLoginSMS.checked = false;
    document.querySelector(".loginSMS").innerHTML = "OFF";
  }
  setLoginSMS();
});

async function setLoginSMS() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  await useApi({
    method: "post",
    url: "set-login-sms-status",
    data: {
      is_login_sms: isStatusLoginSMS,
    },
    callback: function (data) {
      isStatusLoginSMS = data.data;
      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
    },
    errorCallback: function () {
      isStatusLoginSMS = +!isStatusLoginSMS;
    },
  });

  if (isStatusLoginSMS == 1) {
    toggleLoginSMS.checked = true; // toggle به حالت آبی (سمت راست)
    document.querySelector(".loginSMS").innerHTML = "ON";
  } else {
    toggleLoginSMS.checked = false; // toggle به حالت سیاه (سمت چپ)
    document.querySelector(".loginSMS").innerHTML = "OFF";
  }
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

let isStatusReCaptcha;
let toggleReCaptcha = document.getElementById("idInputToggleReCaptcha");

async function reCaptcha() {
  await useApi({
    url: "get-status-reCapcha",
    callback: function (data) {
      isStatusReCaptcha = data.data;
    },
  });
  // تنظیم وضعیت اولیه بر اساس متغیر
  if (isStatusReCaptcha == 1) {
    toggleReCaptcha.checked = true; // toggle به حالت آبی (سمت راست)
    document.querySelector(".reCaptcha").innerHTML = "ON";
    document.getElementById("buttonConnectionReCaptcha").style.display =
      "block";
  } else {
    toggleReCaptcha.checked = false; // toggle به حالت سیاه (سمت چپ)
    document.querySelector(".reCaptcha").innerHTML = "OFF";
    document.getElementById("buttonConnectionReCaptcha").style.display = "none";
  }
}

toggleReCaptcha.addEventListener("click", () => {
  if (toggleReCaptcha.checked) {
    isStatusReCaptcha = 1;
    toggleReCaptcha.checked = true;
    document.querySelector(".reCaptcha").innerHTML = "ON";
    document.getElementById("buttonConnectionReCaptcha").style.display =
      "block";
  } else {
    isStatusReCaptcha = 0;
    toggleReCaptcha.checked = false;
    document.querySelector(".reCaptcha").innerHTML = "OFF";
    document.getElementById("buttonConnectionReCaptcha").style.display = "none";
  }
  setReCaptch();
});

async function setReCaptch() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  await useApi({
    method: "post",
    url: "set-status-reCapcha",
    data: {
      active_online_capcha: isStatusReCaptcha,
    },
    callback: function (data) {
      isStatusLoginSMS = data.data;
      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
    },
    errorCallback: function () {
      isStatusReCaptcha = +!isStatusReCaptcha;
    },
  });
  if (isStatusReCaptcha == 1) {
    toggleReCaptcha.checked = true; // toggle به حالت آبی (سمت راست)
    document.querySelector(".reCaptcha").innerHTML = "ON";
    document.getElementById("buttonConnectionReCaptcha").style.display =
      "block";
  } else {
    toggleReCaptcha.checked = false; // toggle به حالت سیاه (سمت چپ)
    document.querySelector(".reCaptcha").innerHTML = "OFF";
    document.getElementById("buttonConnectionReCaptcha").style.display = "none";
  }
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

let dataConfigSMS;

document.getElementById("buttonConfigSMS").addEventListener("click", () => {
  btnConfigSMS = "";
  document.getElementById("SubmitConfigSMS").removeAttribute("data-bs-dismiss");
  document.getElementById("usernameConfigSMS").value =
    dataConfigSMS?.data?.username || "";
  document.getElementById("passwordConfigSMS").value =
    dataConfigSMS?.data?.password || "";
  document.getElementById("specialNumberConfigSMS").value =
    dataConfigSMS?.data?.special_number || "";
});

async function configSMS() {
  await useApi({
    url: "get-config-connection-sms",
    callback: function (data) {
      dataConfigSMS = data;
      document.getElementById("usernameConfigSMS").value = data?.data?.username;
      document.getElementById("passwordConfigSMS").value = data?.data?.password;
      document.getElementById("specialNumberConfigSMS").value =
        data?.data?.special_number;
    },
  });
  // document.getElementById("idLoading").style.display = "none";
}

document.getElementById("SubmitConfigSMS").addEventListener("click", () => {
  if (btnConfigSMS == "") {
    setConfigSMS();
  }
});

let btnConfigSMS = "";

async function setConfigSMS() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  let username = document.getElementById("usernameConfigSMS").value;
  let password = document.getElementById("passwordConfigSMS").value;
  let special_number = document.getElementById("specialNumberConfigSMS").value;

  const jsonData = {
    "connection-data": {
      username: username,
      password: password,
      special_number: special_number,
    },
  };

  await useApi({
    method: "post",
    url: "set-config-connection-sms",
    data: jsonData, // ارسال به صورت JSON
    headers: {
      "Content-Type": "application/json", // تغییر هدر به JSON
    },
    callback: function (data) {
      dataConfigSMS = data;
      let button = document.getElementById("SubmitConfigSMS");
      button.setAttribute("data-bs-dismiss", "modal");
      btnConfigSMS = "modalSetConfigSMS";
      button.click(); // کلیک برنامه‌نویسی
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
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

async function logout() {
  document.getElementById("idLoading").style.display = "flex";
  await useApi({
    method: "post",
    url: "logout",

    callback: function (data) {
      window.location.href = "../views/login.html";
    },
  });
  document.getElementById("idLoading").style.display = "none";
}

if (document.getElementById("v-ping")) {
  document.getElementById("submitPing").addEventListener("click", function () {
    // let port = document.getElementById("portPing").value;
    // localStorage.setItem("port", port);
    if (ping) {
      pingServer();
    } else {
      debugger
      interfacePing();
    }
  });
}

let ping = false;

async function pingServer() {
  document.getElementById("loaderPing").style.display = "inline-block";
  document.getElementById("submitPing").disabled = true;

  let server_id = document.getElementById("serverIdPing").value;
  let ipـdestination = document.getElementById("ipServerPing").value;
  let Interface = document.getElementById("interfaceServerPing").value;

  let username, password, port;

  dataServer.forEach((server) => {
    if (server.id == server_id) {
      (username = server.username),
        (password = server.password),
        (port = Number(server.port));
    }
  });

  await useApi({
    method: "post",
    url: "ping-ssh",
    data: {
      server_id,
      ipـdestination,
      Interface,
      username,
      password,
      ...(port ? { port } : {}),
    },
    callback: function (data) {
      document.getElementById("resultPing").style.display = "block";
      document.getElementById("hrPing").style.display = "block";
      let raw = data.message;
      // 1) تبدیل \r و \n به newline واقعی
      let withNewlines = raw.replace(/\\r/g, "\r").replace(/\\n/g, "\n");
      // 2) حذف کدهای ANSI (مثل \u001b[?2004l)
      let cleaned = withNewlines.replace(/\u001b\[[0-9;?]*[ -/]*[@-~]/g, "");
      document.getElementById("pingResponse").innerHTML = cleaned.replace(
        /\n/g,
        "<br>"
      );
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("loaderPing").style.display = "none";
  document.getElementById("submitPing").disabled = false;
}

async function interfacePing() {
  document.getElementById("loaderPing").style.display = "inline-block";
  document.getElementById("submitPing").disabled = true;

  let idServer = document.getElementById("serverIdPing").value;
  let inputServer = document.getElementById("serverIdPing");

  let servers = [];
  let serverSpecifications;

  dataServer.forEach((server) => {
    if (server.id == idServer) {
      serverSpecifications = {
        id: idServer,
        username: server.username,
        password: server.password,
        port: Number(server.port),
      };

      servers.push(serverSpecifications);
    }
  });

  await useApi({
    method: "post",
    url: "show-interface-vm",
    data: {
      servers,
    },
    callback: function (data) {
      let selectedName = inputServer.options[inputServer.selectedIndex].text;

      inputServer.addEventListener("change", function () {
        selectedName = inputServer.options[inputServer.selectedIndex].text;
      });

      let raw = data[selectedName].command_output;

      // 1) تبدیل \r\n به newline واقعی
      let cleaned = raw.replace(/\\r/g, "").replace(/\\n/g, "\n");

      // 2) حذف کدهای ANSI
      cleaned = cleaned.replace(/\u001b\[[0-9;?]*[ -/]*[@-~]/g, "");

      // 3) گرفتن خطوط
      let lines = cleaned.split("\n");

      // 4) پیدا کردن اینترفیس‌ها (regex)
      let regex = /^\s*(\d+):\s*([^:]+):/;
      let interfaces = [];

      lines.forEach((line) => {
        let match = line.match(regex);
        if (match) {
          interfaces.push(match[2]); // فقط اسم اینترفیس مثل lo, enp3s0
        }
      });

      // 5) ساختن option ها
      let select = document.getElementById("interfaceServerPing");
      select.innerHTML = ""; // پاک کردن قبلی

      interfaces.forEach((iface) => {
        let opt = document.createElement("option");
        opt.value = iface;
        opt.textContent = iface;
        select.appendChild(opt);
      });

      ping = true;
      document.getElementById("inputInterface").classList.remove("d-none");
      document.getElementById("inputIp").classList.remove("d-none");
      document.getElementById("inputInterface").classList.add("d-flex");
      document.getElementById("inputIp").classList.add("d-flex");
      let div = document.createElement("div");
      div.setAttribute("id", "loaderPing");
      div.setAttribute("class", "loaderPing me-2");
      let btn = document.getElementById("submitPing");
      btn.textContent = "Ping"; // یا btn.innerText

      // اضافه کردن div قبل از متن
      btn.insertBefore(div, btn.firstChild);
    },
  });

  document.getElementById("loaderPing").style.display = "none";
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("submitPing").disabled = false;
}

document.getElementById("v-ping-tab").addEventListener("click", function () {
  // let username = localStorage.getItem("userNameServer");
  // let password = localStorage.getItem("passwordServer");
  // document.getElementById("usernamePing").value = username;
  // document.getElementById("passwordPing").value = password;
  document.getElementById("inputInterface").classList.remove("d-flex");
  document.getElementById("inputIp").classList.remove("d-flex");
  document.getElementById("inputInterface").classList.add("d-none");
  document.getElementById("inputIp").classList.add("d-none");
  let div = document.createElement("div");
  div.setAttribute("id", "loaderPing");
  div.setAttribute("class", "loaderPing me-2");
  let btn = document.getElementById("submitPing");
  btn.textContent = "Show Interface"; // یا btn.innerText
  btn.insertBefore(div, btn.firstChild);
  ping = false;
  document.getElementById("resultPing").style.display = "none";
  document.getElementById("hrPing").style.display = "none";
  document.getElementById("pingResponse").innerHTML = "";
});

document.getElementById("serverIdPing").addEventListener("change", () => {
  document.getElementById("inputInterface").classList.remove("d-flex");
  document.getElementById("inputIp").classList.remove("d-flex");
  document.getElementById("inputInterface").classList.add("d-none");
  document.getElementById("inputIp").classList.add("d-none");
  document.getElementById("hrPing").style.display = "none";
  document.getElementById("resultPing").style.display = "none";
  document.getElementById("pingResponse").innerHTML = "";
  let div = document.createElement("div");
  div.setAttribute("id", "loaderPing");
  div.setAttribute("class", "loaderPing me-2");
  let btn = document.getElementById("submitPing");
  btn.textContent = "Show Interface"; // یا btn.innerText
  btn.insertBefore(div, btn.firstChild);
  ping = false;
});

// document.getElementById("submitServerIp").addEventListener("click", () => {
//   sendServerIP();
// });

// async function sendServerIP() {
//   document.getElementById("idLoading").style.display = "flex";
//   document.getElementById("idLoading").style.background =
//     "hsla(0, 0%, 100%, 0.5)";
//   let serverIp = document.getElementById("inputVmIp").value;
//   await useApi({
//     method: "post",
//     url: `set-orginal-VM-ip`,
//     data: {
//       orginal_vm_ip: serverIp,
//     },
//     callback: function (data) {
//       Toastify({
//         text: data.msg,
//         style: {
//           background:
//             "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
//         },
//       }).showToast();
//     },
//   });
//   document.getElementById("idLoading").style.display = "none";
//   document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
// }

// let requestShowSetting = true;

// async function serverIP() {
//   document.getElementById("idLoading").style.display = "flex";
//   await useApi({
//     url: `get-orginal-vm-ip`,
//     callback: function (data) {
//       document.getElementById("inputVmIp").value = data?.data || "";
//     },
//   });
//   requestShowSetting = false;
// }

let dataRecaptcha;
async function getConnectionReCaptcha() {
  await useApi({
    url: `get-recapcha-data`,
    callback: function (data) {
      dataRecaptcha = data.data;
    },
  });
}

document
  .getElementById("buttonConnectionReCaptcha")
  .addEventListener("click", () => {
    btnSetConnectionReCaptch = "";
    document
      .getElementById("SubmitConfigReCaptcha")
      .removeAttribute("data-bs-dismiss");
    document.getElementById("recaptchaSecretKey").value =
      dataRecaptcha.recaptcha_secret_key;
    document.getElementById("recaptchaSiteName").value =
      dataRecaptcha.recaptcha_site_name;
  });

document
  .getElementById("SubmitConfigReCaptcha")
  .addEventListener("click", () => {
    if (btnSetConnectionReCaptch == "") {
      setConnectionReCaptcha();
    }
  });

let btnSetConnectionReCaptch = "";

async function setConnectionReCaptcha() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  let recaptcha_secret_key =
    document.getElementById("recaptchaSecretKey").value;
  let recaptcha_site_name = document.getElementById("recaptchaSiteName").value;
  await useApi({
    method: "post",
    url: `set-recapcha-data`,
    data: {
      recaptcha_secret_key,
      recaptcha_site_name,
    },
    callback: function (data) {
      dataRecaptcha = data.data;
      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
      let button = document.getElementById("SubmitConfigReCaptcha");
      button.setAttribute("data-bs-dismiss", "modal");
      btnSetConnectionReCaptch = "modalSetConnectionReCaptcha";
      button.click(); // کلیک برنامه‌نویسی
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

let idBackup;
let requestShowSetting = true;

async function configBackup() {
  await useApi({
    url: `get-config-backup`,
    callback: function (data) {
      let parts, day, time;
      if (data.data.length != 0) {
        document.getElementById("setBackup").style.display = "none";
        document.getElementById("inputPasswordBackup").style.display = "none";
        parts = data?.data[0]?.run_backup_at.split(" "); // ['06', '10:10']
        day = parseInt(parts[0], 10);
        time = parts[1];
      } else {
        document.getElementById("editBackup").style.display = "none";
        document.getElementById("deleteBackup").style.display = "none";
        document.getElementById("divBackupChanger").style.display = "none";
      }
      document.getElementById("backupAddress").value =
        data?.data[0]?.destination_path || "";

      idBackup = data?.data[0]?.id;

      for (let i = 0; i < 3; i++) {
        document.getElementById(`textBackup${i + 1}`).innerHTML =
          data?.data[0]?.next_backups[i] || "";
      }

      document.getElementById(`backupChanger`).innerHTML =
        data?.data[0]?.user.auth_name || "";

      document.getElementById("dayBackup").value = day || "";
      document.getElementById("timeBackup").value = time || "";
    },
  });
  requestShowSetting = false;
}

// document.getElementById("buttonConfigBackup").addEventListener("click", () => {
//   document.getElementById("backupPassword").value =
//     localStorage.getItem("passwordServer");
//   btnBackup = "";
//   // document.getElementById("SubmitBackup").removeAttribute("data-bs-dismiss");
// });

document.getElementById("setBackup").addEventListener("click", () => {
  let password = document.getElementById("backupPassword").value;
  localStorage.setItem("passwordServer", password);

  let day_backup = document.getElementById("dayBackup").value;
  let time_backup = document.getElementById("timeBackup").value;
  let address_backup = document.getElementById("backupAddress").value;
  let password_backup = document.getElementById("backupPassword").value;

  if (
    day_backup != "" &&
    time_backup != "" &&
    address_backup != "" &&
    password_backup != ""
  ) {
    setConfigBackup();
  } else {
    Toastify({
      text: "Make sure all required fields are filled in correctly.",
    }).showToast();
  }
});

// let btnBackup = "";

async function setConfigBackup() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  let destination_path = document.getElementById("backupAddress").value;
  const day = document.getElementById("dayBackup").value;
  const time = document.getElementById("timeBackup").value;
  const formattedDay = day.toString().padStart(2, "0");

  let run_backup_at = `${formattedDay} ${time}`;
  let password = document.getElementById("backupPassword").value;
  await useApi({
    url: `set-config-backup`,
    method: "post",
    data: {
      destination_path,
      run_backup_at,
      password,
    },
    callback: function (data) {
      for (let i = 0; i < 3; i++) {
        document.getElementById(`textBackup${i + 1}`).innerHTML =
          data?.data?.next_backups[i];
      }

      document.getElementById(`backupChanger`).innerHTML =
        data?.data?.user.auth_name || "";

      // document.getElementById("backupAddress").value =
      //   data.data.destination_path;
      // document.getElementById("backupDay").value = data.data.run_backup_daily;
      document.getElementById("setBackup").style.display = "none";
      document.getElementById("inputPasswordBackup").style.display = "none";
      document.getElementById("editBackup").style.display = "inline-block";
      document.getElementById("divBackupChanger").style.display =
        "inline-block";
      document.getElementById("deleteBackup").style.display = "inline-block";
      document.getElementById("backupAddress").value =
        data?.data?.destination_path || "";

      idBackup = data?.data?.id;

      const parts = data?.data?.run_backup_at.split(" "); // ['06', '10:10']
      const day = parseInt(parts[0], 10);
      const time = parts[1];

      document.getElementById("dayBackup").value = day || "";
      document.getElementById("timeBackup").value = time || "";
      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
      // let button = document.getElementById("SubmitBackup");
      // button.setAttribute("data-bs-dismiss", "modal");
      // btnBackup = "modalBackup";
      // button.click();
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

document.getElementById("editBackup").addEventListener("click", () => {
  let day_backup = document.getElementById("dayBackup").value;
  let time_backup = document.getElementById("timeBackup").value;
  let address_backup = document.getElementById("backupAddress").value;

  if (day_backup != "" && time_backup != "" && address_backup != "") {
    editConfigBackup();
  } else {
    Toastify({
      text: "Make sure all required fields are filled in correctly.",
    }).showToast();
  }
});

async function editConfigBackup() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  let destination_path = document.getElementById("backupAddress").value;
  const day = document.getElementById("dayBackup").value;
  const time = document.getElementById("timeBackup").value;
  const formattedDay = day.toString().padStart(2, "0");

  let run_backup_at = `${formattedDay} ${time}`;
  await useApi({
    url: `edit-config-backup`,
    method: "patch",
    data: {
      id: idBackup,
      destination_path,
      run_backup_at,
    },
    callback: function (data) {
      document.getElementById(`backupChanger`).innerHTML =
        data?.data?.user.auth_name || "";
      for (let i = 0; i < 3; i++) {
        document.getElementById(`textBackup${i + 1}`).innerHTML =
          data?.data?.next_backups[i];
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
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

document.getElementById("deleteBackup").addEventListener("click", () => {
  deleteConfigBackup();
});

async function deleteConfigBackup() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  // let destination_path = document.getElementById("backupAddress").value;
  // const day = document.getElementById("dayBackup").value;
  // const time = document.getElementById("timeBackup").value;
  // const formattedDay = day.toString().padStart(2, "0");

  // let run_backup_at = `${formattedDay} ${time}`;
  await useApi({
    url: `delete-config-backup/${idBackup}`,
    method: "delete",
    // data: {
    //   id : idBackup,
    //   destination_path,
    //   run_backup_at,
    // },
    callback: function (data) {
      for (let i = 0; i < 3; i++) {
        document.getElementById(`textBackup${i + 1}`).innerHTML = "";
      }
      document.getElementById(`backupChanger`).innerHTML = "";
      document.getElementById("dayBackup").value = "";
      document.getElementById("timeBackup").value = "";
      document.getElementById("backupAddress").value = "";
      document.getElementById("backupPassword").value = "";
      document.getElementById("editBackup").style.display = "none";
      document.getElementById("divBackupChanger").style.display = "none";
      document.getElementById("deleteBackup").style.display = "none";
      document.getElementById("setBackup").style.display = "inline-block";
      document.getElementById("inputPasswordBackup").style.display = "block";
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
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
}

let backupClickTF = false;

// async function historyBackup() {
//   document.getElementById("idLoading").style.display = "flex";
//   document.getElementById("idLoading").style.background =
//     "hsla(0, 0%, 100%, 0.5)";
//   let totalPages;
//   await useApi({
//     url: `get-history-backup?sort=-id&paginate=5&page=${1}`,
//     callback: function (data) {
//       if (backupClickTF) {
//         document.getElementById("tBodyBackup").innerHTML = "";
//       }
//       backupClickTF = true;

//       let endPage = Math.ceil(data.data.total / 5);

//       let paginationNamber;
//       // if (page == 1) {
//       paginationNamber = 1 * 5;
//       paginationNamber++;
//       // } else {
//       //   paginationNamber = (page - 1) * 20;
//       //   paginationNamber++;
//       // }

//       let lengthDataUser = data.data.data.length;
//       for (let i = 0; i < lengthDataUser; i++) {
//         let tr = document.createElement("tr");
//         for (let x = 1; x <= 3; x++) {
//           let td = document.createElement("td");
//           if (x == 1) {
//             td.innerHTML = paginationNamber++;
//           } else if (x == 2) {
//             td.innerHTML = data.data.data[i].start_time;
//           } else if (x == 3) {
//             td.innerHTML = data.data.data[i].finish_time;
//           }
//           tr.appendChild(td);
//         }
//         document.getElementById("tBodyBackup").appendChild(tr);
//       }

//       console.log(endPage);
//       console.log(totalPages);

//       totalPages = endPage; // Set the total number of pages

//       renderPagination(totalPages, currentPageLog);

//       function renderPagination(
//         totalPages,
//         currentPage = !urlLog ? 1 : urlLog
//       ) {
//         const pagination = document.getElementById("pagination");
//         pagination.innerHTML = "";

//         if (totalPages != 1) {
//           // First button
//           const firstItem = document.createElement("li");
//           firstItem.className = `page-item ${
//             currentPage === 1 ? "disabled" : ""
//           }`;
//           firstItem.innerHTML = `<a class="page-link" href="#" data-page="1">First Page</a>`;
//           pagination.appendChild(firstItem);

//           // Previous button
//           const prevItem = document.createElement("li");
//           prevItem.className = `page-item ${
//             currentPage === 1 ? "disabled" : ""
//           }`;
//           prevItem.innerHTML = `<a class="page-link" href="#" data-page="${
//             currentPage - 1
//           }">Previous Page</a>`;
//           pagination.appendChild(prevItem);

//           // Page numbers logic
//           const startPage = currentPage === 1 ? currentPage : currentPage - 1;
//           const endPage =
//             currentPage === totalPages
//               ? currentPage
//               : Math.min(currentPage + 1, totalPages);

//           for (
//             let i = Math.max(1, startPage - 1);
//             i <= Math.min(totalPages, endPage + 1);
//             i++
//           ) {
//             const pageItem = document.createElement("li");
//             pageItem.className = `page-item ${
//               i === currentPage ? "active" : ""
//             }`;
//             pageItem.innerHTML = `<a class="page-link ${
//               i === currentPage ? "active-page" : ""
//             }" href="#" data-page="${i}">${i}</a>`;
//             pagination.appendChild(pageItem);
//           }

//           // Next button
//           const nextItem = document.createElement("li");
//           nextItem.className = `page-item ${
//             currentPage === totalPages ? "disabled" : ""
//           }`;
//           nextItem.innerHTML = `<a class="page-link" href="#" data-page="${
//             currentPage + 1
//           }">Next Page</a>`;
//           pagination.appendChild(nextItem);

//           // Last button
//           const lastItem = document.createElement("li");
//           lastItem.className = `page-item ${
//             currentPage === totalPages ? "disabled" : ""
//           }`;
//           lastItem.innerHTML = `<a class="page-link" href="#" data-page="${totalPages}">Last Page</a>`;
//           pagination.appendChild(lastItem);

//           const currentUrl = new URL(window.location);
//           currentUrl.searchParams.set("log", !currentPage ? 1 : currentPage); // Set the module in URL
//           window.history.pushState({}, "", currentUrl);
//         }
//       }
//     },
//   });
//   document.getElementById("idLoading").style.display = "none";
//   document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";
// }

let stepperEl;
let stepper;
if (project != "RRU") {
  stepperEl = document.querySelector("#stepper");
  stepper = new Stepper(stepperEl, {
    animation: true,
  });
}

let serversTrace = [];
const serverFetchedInterfaces = {};
let tracingActive = false;
let globalIdentifier = ""; // کلید IMSI/MSISDN جدا

// --- fetchInterfaces جدید ---
async function fetchInterfaces(serverData, serverName, container) {
  const key = String(serverName).trim();
  if (serverFetchedInterfaces[key]) {
    container.innerHTML = serverFetchedInterfaces[key];
    return;
  }

  // پاراگراف ثابت "Interfaces:"
  container.innerHTML = `
    <p class="m-0 fw-bold text-secondary ms-2">Interfaces:</p>
    <div class="placeholder-glow mt-2 ms-3 w-50">
      <span class="placeholder col-8"></span>
      <span class="placeholder col-8"></span>
      <span class="placeholder col-8"></span>
      <span class="placeholder col-8"></span>
    </div>
  `;

  const servers = [
    {
      id: serverData.id,
      username: serverData.username,
      password: serverData.password,
      port: serverData.port ? Number(serverData.port) : 22,
    },
  ];
  await useApi({
    method: "post",
    url: "show-interface-vm",
    data: { servers },
    callback: function (data) {
      const serverNameKey = key;
      if (data[serverNameKey] && data[serverNameKey].command_output) {
        let raw = data[serverNameKey].command_output;
        let cleaned = raw.replace(/\\r/g, "").replace(/\\n/g, "\n");
        cleaned = cleaned.replace(/\u001b\[[0-9;?]*[ -/]*[@-~]/g, "");
        const lines = cleaned.split("\n");
        const regex = /^\s*(\d+):\s*([^:]+):/;
        const interfaces = [];
        lines.forEach((line) => {
          const match = line.match(regex);
          if (match) interfaces.push(match[2]);
        });

        if (interfaces.length > 0) {
          let html = `<p class="m-0 fw-bold text-secondary ms-2">Interfaces:</p>`;
          interfaces.forEach((iface) => {
            html += `
                <div class="form-check ms-4 mt-1">
                  <input type="checkbox" class="form-check-input interfaceTrace interfaceTrace${serverData.id}" 
                         id="iface_${serverData.id}_${iface}" value="${iface}">
                  <label class="form-check-label" for="iface_${serverData.id}_${iface}">${iface}</label>
                </div>`;
          });
          container.innerHTML = html;
          serverFetchedInterfaces[key] = html;
        } else {
          container.innerHTML = `
            <p class="m-0 fw-bold text-secondary ms-2">Interfaces:</p>
            <div class="text-muted small ms-3">No interfaces found</div>`;
          serverFetchedInterfaces[key] = container.innerHTML;
        }
      } else {
        showInterfaceError(container, serverData, serverName);
      }
    },
    errorCallback: function () {
      showInterfaceError(container, serverData, serverName);
    },
  });
}

// --- خطا + Retry ---
function showInterfaceError(container, serverData, serverName) {
  container.innerHTML = `
    <p class="m-0 fw-bold text-secondary ms-2">Interfaces:</p>
    <div class="text-danger small ms-3 d-flex align-items-center mt-1">
      Failed to fetch interfaces
      <button class="btn btn-outline-primary btn-sm ms-2 retry-btn">Retry</button>
    </div>
  `;
  container.querySelector(".retry-btn").addEventListener("click", async () => {
    container.innerHTML = `
      <p class="m-0 fw-bold text-secondary ms-2">Interfaces:</p>
      <div class="placeholder-glow mt-2 ms-3 w-50">
        <span class="placeholder col-6"></span>
        <span class="placeholder col-5"></span>
        <span class="placeholder col-7"></span>
        <span class="placeholder col-4"></span>
      </div>
    `;
    await fetchInterfaces(serverData, serverName, container);
  });
}

// --- کنترل کلی Start / Stop ---
function runActionForAll(action) {
  if (action === "start" && typeof funStartTrace === "function")
    funStartTrace(globalIdentifier);
  else if (action === "stop" && typeof funStopTrace === "function")
    funStopTrace(globalIdentifier);
}

// --- نمایش سرورها ---
function showNameServerTrace() {
  const step1 = document.getElementById("step-1");
  step1.innerHTML = "";

  serverCard.forEach((server) => {
    const serverSection = document.createElement("div");
    serverSection.className =
      "server-section border rounded p-3 mt-3 bg-white shadow-sm";

    // Header سرور
    const header = document.createElement("div");
    header.className = "d-flex align-items-center justify-content-between";

    const left = document.createElement("div");
    left.className = "form-check";

    const chk = document.createElement("input");
    chk.type = "checkbox";
    chk.className = "form-check-input serverTrace me-2";
    chk.id = `server${server.id}`;

    const lbl = document.createElement("label");
    lbl.className = "form-check-label fw-bold";
    lbl.htmlFor = chk.id;
    lbl.textContent = server.name;

    left.appendChild(chk);
    left.appendChild(lbl);

    header.appendChild(left);
    serverSection.appendChild(header);

    // بخش افقی ماژول + اینترفیس
    const row = document.createElement("div");
    row.className = "d-flex flex-wrap gap-4 mt-2";

    const modulesContainer = document.createElement("div");
    modulesContainer.className = "flex-fill modules-container d-none";
    modulesContainer.style.minWidth = "300px";

    const interfacesContainer = document.createElement("div");
    interfacesContainer.className = "flex-fill interfaces-container d-none";
    interfacesContainer.style.minWidth = "300px";
    interfacesContainer.id = `interfacesContainer_${server.id}`;

    const mods = modulesInfo.filter((m) => m.serverIDs.includes(server.id));
    if (mods.length > 0) {
      const title = document.createElement("p");
      title.className = "m-0 fw-bold text-secondary ms-2";
      title.textContent = "Modules:";
      modulesContainer.appendChild(title);
      mods.forEach((m) => {
        const div = document.createElement("div");
        div.className = "form-check ms-3 mt-1";
        div.innerHTML = `
          <input type="checkbox" class="form-check-input moduleTrace moduleTrace${server.id}" id="module${m.moduleID}">
          <label class="form-check-label" for="module${m.moduleID}">${m.moduleName}</label>
        `;
        modulesContainer.appendChild(div);
      });
    }

    row.appendChild(modulesContainer);
    row.appendChild(interfacesContainer);
    serverSection.appendChild(row);

    step1.appendChild(serverSection);

    // رویداد تیک سرور
    chk.addEventListener("change", async () => {
      const checked = chk.checked;
      modulesContainer.classList.toggle("d-none", !checked);
      interfacesContainer.classList.toggle("d-none", !checked);
      // modulesContainer
      //   .querySelectorAll(".moduleTrace")
      //   .forEach((c) => (c.checked = checked));

      if (checked) {
        const sData = dataServer.find((d) => d.id == server.id);
        if (!sData?.username || !sData?.password) {
          Toastify({
            text: `Please enter username/password for ${server.name}`,
          }).showToast();
          chk.checked = false;
          modulesContainer.classList.add("d-none");
          interfacesContainer.classList.add("d-none");
          return;
        }
        const key = String(server.name).trim();
        if (!serverFetchedInterfaces[key])
          await fetchInterfaces(sData, server.name, interfacesContainer);
        else interfacesContainer.innerHTML = serverFetchedInterfaces[key];
      } else {
        interfacesContainer.classList.add("d-none");
        interfacesContainer
          .querySelectorAll(".interfaceTrace")
          ?.forEach((c) => (c.checked = false));
      }
    });
  });

  // ورودی و کنترل کلی (IMSI / MSISDN + Start / Stop + Password)
  const controlsDiv = document.createElement("div");
  controlsDiv.className = "mt-4";

  const leftControls = document.createElement("div");
  leftControls.className = "d-flex align-items-center mb-3 gap-2";
  leftControls.innerHTML = `
    <label for="globalIdentifier" class="form-label mb-0 me-2">IMSI / MSISDN</label>
    <input type="text" class="form-control form-control-sm w-25" id="globalIdentifier">
  `;

  const midControls = document.createElement("div");
  midControls.className = "d-flex align-items-center mb-3 gap-2";
  midControls.innerHTML = `
    <label for="passServer" class="form-label mb-0 me-2">Current System Password</label>
    <input type="password" class="form-control form-control-sm w-25" id="passServer">
  `;

  const rightControls = document.createElement("div");
  rightControls.className = "d-flex align-items-center mb-2 gap-2";
  rightControls.innerHTML = `
    <button class="btn btn-success" type="button" id="globalStart">Start Trace</button>
    <button class="btn btn-danger" type="button" id="globalStop" disabled>Stop Trace</button>
  `;

  controlsDiv.appendChild(leftControls);
  controlsDiv.appendChild(midControls);
  controlsDiv.appendChild(rightControls);
  step1.appendChild(controlsDiv);

  // --- هندلرهای Start / Stop ---
  document.getElementById("globalStart").addEventListener("click", async () => {
    globalIdentifier = document.getElementById("globalIdentifier").value.trim();

    // if (!globalIdentifier) {
    //   Toastify({ text: "Please enter IMSI / MSISDN" }).showToast();
    //   return;
    // }

    const checkedServers = Array.from(
      document.querySelectorAll(".serverTrace:checked")
    ).map((cb) => Number(cb.id.replace("server", "")));

    if (!checkedServers.length) {
      Toastify({
        text: "Please select at least one server to start trace.",
      }).showToast();
      return;
    }

    for (const sid of checkedServers) {
      const sData = dataServer.find((d) => d.id == sid);
      const label =
        document.querySelector(`label[for="server${sid}"]`)?.textContent ||
        String(sid);
      if (!sData?.username || !sData?.password) {
        Toastify({
          text: `You didn’t enter the username and password for ${label}`,
        }).showToast();
        return;
      }

      // const key = String(label).trim();
      // const container = document.getElementById(`interfacesContainer_${sid}`);
      // if (!serverFetchedInterfaces[key]) {
      //   container.classList.remove("d-none");
      //   await fetchInterfaces(sData, label, container);
      // }
    }

    let module_identifier = document.getElementById("globalIdentifier").value;

    // ساخت داده trace
    serversTrace = checkedServers.map((sid) => {
      const sInfo = serverCard.find((s) => s.id == sid);
      const dInfo = dataServer.find((d) => d.id == sid);
      return {
        id: sid,
        name: sInfo?.name,
        username: dInfo?.username,
        password: dInfo?.password,
        port: dInfo?.port || 22,
        module_identifier: module_identifier,
        module_ids: Array.from(
          document.querySelectorAll(`.moduleTrace${sid}:checked`)
        ).map((c) => Number(c.id.replace("module", ""))),
        interfaces: Array.from(
          document.querySelectorAll(`.interfaceTrace${sid}:checked`)
        ).map((c) => c.value),
      };
    });
    runActionForAll("start");
  });

  document.getElementById("globalStop").addEventListener("click", async () => {
    if (!tracingActive) {
      Toastify({
        text: "You must Start trace before stopping it.",
      }).showToast();
      return;
    }

    document.getElementById("tabBtn2")?.classList.remove("active");
    document.getElementById("tabBtn1")?.classList.add("active");
    requestAnimationFrame(() => {
      document.getElementById("tabMain")?.classList.add("show");
      document.getElementById("tabMain")?.classList.add("active");
    });

    const checkedServers = Array.from(
      document.querySelectorAll(".serverTrace:checked")
    ).map((cb) => Number(cb.id.replace("server", "")));

    for (const sid of checkedServers) {
      const sData = dataServer.find((d) => d.id == sid);
      const label =
        document.querySelector(`label[for="server${sid}"]`)?.textContent ||
        String(sid);
      if (!sData?.username || !sData?.password) {
        Toastify({
          text: `You didn’t enter the username and password for ${label}`,
        }).showToast();
        return;
      }

      // const key = String(label).trim();
      // const container = document.getElementById(`interfacesContainer_${sid}`);
      // if (!serverFetchedInterfaces[key]) {
      //   container.classList.remove("d-none");
      //   await fetchInterfaces(sData, label, container);
      // }
    }

    let module_identifier = document.getElementById("globalIdentifier").value;

    serversTrace = checkedServers.map((sid) => {
      const sInfo = serverCard.find((s) => s.id == sid);
      const dInfo = dataServer.find((d) => d.id == sid);
      return {
        id: sid,
        name: sInfo?.name,
        username: dInfo?.username,
        password: dInfo?.password,
        port: dInfo?.port || 22,
        module_identifier: module_identifier,
        module_ids: Array.from(
          document.querySelectorAll(`.moduleTrace${sid}:checked`)
        ).map((c) => Number(c.id.replace("module", ""))),
        interfaces: Array.from(
          document.querySelectorAll(`.interfaceTrace${sid}:checked`)
        ).map((c) => c.value),
      };
    });
    runActionForAll("stop");
  });
}

let statusTraceStart = true;
let statusTraceStop = true;

function showInputTrace() {
  const container = document.getElementById("step-3");
  container.innerHTML = "";
  // حلقه روی همه سرورها (module ها)
  for (let i = 0; i < allInterfacesTrace.length; i++) {
    const moduleName = Object.keys(allInterfacesTrace[i])[0];

    // نام سرور
    const p = document.createElement("p");
    p.setAttribute("class", "m-0");
    p.textContent = moduleName;
    container.appendChild(p);

    // ایجاد div برای input
    const div = document.createElement("div");
    div.setAttribute("class", "mb-3 ms-4");

    // label
    const label = document.createElement("label");
    label.setAttribute("for", `input_${moduleName}`);
    label.setAttribute("class", "form-label fontSizeTrace");
    label.textContent = `IMSI & MSYSDN`;

    // input
    const input = document.createElement("input");
    input.setAttribute("type", "text");
    input.setAttribute("class", "form-control serverInput w-75");
    input.setAttribute("id", `input_${moduleName}`);
    div.appendChild(label);
    div.appendChild(input);
    container.appendChild(div);
  }

  const labelPassServer = document.createElement("label");
  labelPassServer.setAttribute("for", `passServer`);
  labelPassServer.setAttribute("class", "form-label fontSizeTrace ms-4");
  labelPassServer.textContent = `Current Server Password`;

  const inputPassServer = document.createElement("input");
  inputPassServer.setAttribute("type", "password");
  inputPassServer.setAttribute("class", "form-control w-25 ms-4");
  inputPassServer.setAttribute("id", `passServer`);

  container.appendChild(labelPassServer);
  container.appendChild(inputPassServer);

  // دکمه‌ها
  const buttonPrev = document.createElement("button");
  buttonPrev.setAttribute("class", "btn btn-primary mt-5 me-2 buttonTrace");
  buttonPrev.setAttribute("type", "button");
  buttonPrev.setAttribute("id", "prev3");
  buttonPrev.innerHTML = "Prev";

  // const buttonNext = document.createElement("button");
  // buttonNext.setAttribute("class", "btn btn-primary mt-3 buttonTrace");
  // buttonNext.setAttribute("type", "button");
  // buttonNext.setAttribute("id", "next3");
  // buttonNext.innerHTML = "Next";

  const startTrace = document.createElement("button");
  startTrace.setAttribute("class", "btn btn-success mt-5 me-2 buttonTrace");
  startTrace.setAttribute("type", "button");
  startTrace.setAttribute("id", "startTrace");
  startTrace.innerHTML = "Start Trace";

  const stopTrace = document.createElement("button");
  stopTrace.setAttribute("class", "btn btn-success mt-5 buttonTrace");
  stopTrace.setAttribute("type", "button");
  stopTrace.setAttribute("id", "StopTrace");
  stopTrace.innerHTML = "Stop Trace";

  container.appendChild(buttonPrev);
  container.appendChild(startTrace);
  container.appendChild(stopTrace);

  // رویداد next
  // document.getElementById("next3").addEventListener("click", () => {
  //   stepper.next();
  // });

  document.getElementById("startTrace").addEventListener("click", () => {
    if (statusTraceStart) {
      // جمع‌آوری داده‌ها از input ها
      const inputs = Array.from(document.querySelectorAll(".serverInput")).map(
        (input) => ({
          moduleName: input.id.replace("input_", ""),
          value: input.value.trim(),
        })
      );

      // افزودن فقط ورودی‌هایی که مقدار دارند
      serversTrace.forEach((server) => {
        const matched = inputs.find(
          (inp) => inp.moduleName === (server.name || server.module_name)
        );
        if (matched && matched.value !== "") {
          // فقط اگر مقدار پر شده بود
          server.module_identifier = matched.value;
        }
      });

      funStartTrace();
    } else {
      // document.querySelectorAll(".serverTrace").forEach((element) => {
      //   element.checked = false;
      // });
      // document.querySelectorAll(".moduleTrace").forEach((element) => {
      //   element.checked = false;
      // });
      // showInterfaceTrace = true;
      // stepper.to(1);
      statusTraceStart = true;
    }
  });

  document.getElementById("StopTrace").addEventListener("click", () => {
    if (statusTraceStop) {
      // جمع‌آوری داده‌ها از input ها
      const inputs = Array.from(document.querySelectorAll(".serverInput")).map(
        (input) => ({
          moduleName: input.id.replace("input_", ""),
          value: input.value.trim(),
        })
      );

      // افزودن فقط ورودی‌هایی که مقدار دارند
      serversTrace.forEach((server) => {
        const matched = inputs.find(
          (inp) => inp.moduleName === (server.name || server.module_name)
        );
        if (matched && matched.value !== "") {
          server.module_identifier = matched.value;
        }
      });

      funStopTrace();
    } else {
      stepper.next();
      statusTraceStop = true;
    }
  });

  // رویداد prev
  document.getElementById("prev3").addEventListener("click", () => {
    stepper.previous();
  });
}

async function funStartTrace() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";

  let current_server_password = document.getElementById("passServer").value;
  await useApi({
    method: "post",
    url: "trace-server-start",
    data: {
      current_server_password,
      servers: serversTrace,
    },
    callback: function (data) {
      Toastify({
        text: "trace started successfully",
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
      document.getElementById("globalStart").disabled = true;
      document.getElementById("globalStop").disabled = false;
      tracingActive = true;
      statusTraceStart = false;
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 1)";
}

async function funStopTrace() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";

  let current_server_password = document.getElementById("passServer").value;

  await useApi({
    method: "post",
    url: "trace-server-stop",
    data: {
      current_server_password,
      servers: serversTrace,
    },
    callback: function (data) {
      downloadFile(data.final_pcap);
      // setTimeout(() => {
      //   loadPcapIntoWebshark(data.final_pcap);
      // }, 5000);
      const iframe = document.getElementById("traceFrame");
      if (iframe && data?.webshark) {
        iframe.src = data.webshark;
      }

      const iframe2 = document.getElementById("traceFrame2");
      if (iframe2 && data?.webshark_pcap) {
        iframe2.src = data.webshark_pcap;
      }
      stepper.to(2);
      Toastify({
        text: "trace stopped successfully",
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
      document.getElementById("globalStart").disabled = false;
      document.getElementById("globalStop").disabled = true;
      tracingActive = false;
      statusTraceStop = false;
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 1)";
}

function downloadFile(address) {
  const url = address;

  const a = document.createElement("a");
  a.href = url;
  a.download = "trace.pcapng"; // اسم دلخواه فایل
  document.body.appendChild(a);
  a.click();
  a.remove();
}

async function loadPcapIntoWebshark(userFile) {
  // const iframe = document.getElementById("traceFrame");
  // let testFile;
  // await useApi({
  //   url: userFile,
  //   setToken: false,
  //   headers: {
  //     "Content-Type": "multipart/form-data",
  //   },
  //   responseType: "blob",
  //   callback: function (data) {
  //     testFile = new File([data], "test.pcapng", {
  //       type: data.type || "application/octet-stream",
  //     });
  //   },
  // });

  iframe.onload = () => {
    try {
      const iframeDoc = iframe.contentWindow.document;

      // پیدا کردن input آپلود Webshark
      const fileInput = iframeDoc.querySelector('input[type="file"]');
      if (!fileInput) {
        console.error("Upload input not found in Webshark.");
        return;
      }

      // ساخت FileList
      const dt = new DataTransfer();
      dt.items.add(testFile);
      fileInput.files = dt.files;

      // اجرای event آپلود
      const changeEvent = new Event("change", { bubbles: true });
      fileInput.dispatchEvent(changeEvent);
    } catch (err) {
      console.error("Cannot access Webshark iframe:", err);
    }
  };
}

document.getElementById("endStep").addEventListener("click", () => {
  // برگشت به step سرورها
  stepper.to(1);

  // برداشتن تیک سرور‌ها
  document.querySelectorAll(".serverTrace").forEach((cb) => {
    cb.checked = false;
  });

  // مخفی کردن ماژول‌ها و اینترفیس‌ها
  document
    .querySelectorAll(".modules-container, .interfaces-container")
    .forEach((el) => {
      el.classList.add("d-none");
    });

  // برداشتن تیک ماژول‌ها و اینترفیس‌ها
  document.querySelectorAll(".moduleTrace, .interfaceTrace").forEach((cb) => {
    cb.checked = false;
  });

  // فعال‌سازی دوباره دکمه Start و غیرفعال‌سازی Stop
  const btnStart = document.getElementById("globalStart");
  const btnStop = document.getElementById("globalStop");
  if (btnStart) btnStart.disabled = false;
  if (btnStop) btnStop.disabled = true;

  // پاک کردن وضعیت ردیابی فعال (در صورت وجود)
  tracingActive = false;
});

// document.getElementById("endStep2").addEventListener("click", () => {
//   // برگشت به step سرورها
//   stepper.to(1);

//   // برداشتن تیک سرور‌ها
//   document.querySelectorAll(".serverTrace").forEach((cb) => {
//     cb.checked = false;
//   });

//   // مخفی کردن ماژول‌ها و اینترفیس‌ها
//   document
//     .querySelectorAll(".modules-container, .interfaces-container")
//     .forEach((el) => {
//       el.classList.add("d-none");
//     });

//   // برداشتن تیک ماژول‌ها و اینترفیس‌ها
//   document.querySelectorAll(".moduleTrace, .interfaceTrace").forEach((cb) => {
//     cb.checked = false;
//   });

//   // فعال‌سازی دوباره دکمه Start و غیرفعال‌سازی Stop
//   const btnStart = document.getElementById("globalStart");
//   const btnStop = document.getElementById("globalStop");
//   if (btnStart) btnStart.disabled = false;
//   if (btnStop) btnStop.disabled = true;

//   // پاک کردن وضعیت ردیابی فعال (در صورت وجود)
//   tracingActive = false;
// });

// مرحله اول

// مرحله دوم
// document
//   .getElementById("prev2")
//   .addEventListener("click", () => stepper.previous());
// document
//   .getElementById("next2")
//   .addEventListener("click", () => stepper.next());

// مرحله سوم
// document
//   .getElementById("prev3")
//   .addEventListener("click", () => stepper.previous());
// document
//   .getElementById("next3")
//   .addEventListener("click", () => stepper.next());

// مرحله چهارم
// document
//   .getElementById("prev4")
//   .addEventListener("click", () => stepper.previous());

// مدیریت ارسال فرم
// document.getElementById("myForm").addEventListener("submit", function (e) {
//   e.preventDefault();
//   alert("فرم با موفقیت ارسال شد!");
//   // اینجا می‌تونی فرم رو به سرور بفرستی یا هر کاری که لازم داری انجام بدی
// });

document.getElementById("buttonSendRoute").addEventListener("click", () => {
  // let port = document.getElementById("portRoute").value;
  // localStorage.setItem("port", port);
  addRouteServer();
});

document.getElementById("getRoute").addEventListener("click", () => {
  showRouteServer();
});

document.getElementById("getRouteDiv").addEventListener("click", () => {
  // let userName = document.getElementById("usernameGetRoute").value;
  // let password = document.getElementById("passwordGetRoute").value;
  // let port = document.getElementById("portGetRoute").value;

  // localStorage.setItem("userNameServer", userName);
  // localStorage.setItem("passwordServer", password);
  // localStorage.setItem("port", port);

  showRouteServer();
});

function showNameServerRoute() {
  document.getElementById("serverIdRouteShowToBox").innerHTML = "";
  for (let i = 1; i <= serverCard.length; i++) {
    let option = document.createElement("option");
    option.setAttribute("value", `${serverCard[i - 1].id}`);
    option.innerHTML = serverCard[i - 1].name;
    document.getElementById("serverIdRouteShowToBox").appendChild(option);
  }
  document.getElementById("serverIdRouteShow").innerHTML = "";
  for (let i = 1; i <= serverCard.length; i++) {
    let option = document.createElement("option");
    option.setAttribute("value", `${serverCard[i - 1].id}`);
    option.innerHTML = serverCard[i - 1].name;
    document.getElementById("serverIdRouteShow").appendChild(option);
  }
  document.getElementById("serverIdRoute").innerHTML = "";
  for (let i = 1; i <= serverCard.length; i++) {
    let option = document.createElement("option");
    option.setAttribute("value", `${serverCard[i - 1].id}`);
    option.innerHTML = serverCard[i - 1].name;
    document.getElementById("serverIdRoute").appendChild(option);
  }
  // document.getElementById("serverIdRouteRemove").innerHTML = "";
  // for (let i = 1; i <= serverCard.length; i++) {
  //   let option = document.createElement("option");
  //   option.setAttribute("value", `${serverCard[i - 1].id}`);
  //   option.innerHTML = serverCard[i - 1].name;
  //   document.getElementById("serverIdRouteRemove").appendChild(option);
  // }
  document.getElementById("idLoading").style.display = "none";
}

document.getElementById("addRouteModal").addEventListener("click", () => {
  // let username = localStorage.getItem("userNameServer");
  // let password = localStorage.getItem("passwordServer");
  // let port = localStorage.getItem("port");

  // if (username == null || password == null) {
  //   document.getElementById("usernameRoute").value = "";
  //   document.getElementById("passwordRoute").value = "";
  // } else {
  //   document.getElementById("usernameRoute").value = username;
  //   document.getElementById("passwordRoute").value = password;
  // }
  // document.getElementById("portRoute").value = port;
  document.getElementById("getwayRoute").value = "";
  document.getElementById("destinationIpRoute").value = "";
  document.getElementById("interfaceRoute").value = "";
});

async function addRouteServer() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  let server_id = document.getElementById("serverIdRoute").value;

  let username, password, port;

  dataServer.forEach((server) => {
    if (server.id == server_id) {
      (username = server.username),
        (password = server.password),
        (port = server.port);
    }
  });
  // let username = document.getElementById("usernameRoute").value;
  // let password = document.getElementById("passwordRoute").value;
  // let port = document.getElementById("portRoute").value;
  let geteway_ip = document.getElementById("getwayRoute").value;
  let destination_ip = document.getElementById("destinationIpRoute").value;
  let interface_route = document.getElementById("interfaceRoute").value;

  await useApi({
    url: `add-route-server`,
    method: "post",
    data: {
      server_id,
      username,
      password,
      ...(!!port ? { port } : {}),
      geteway_ip,
      destination_ip,
      ...(!!interface_route ? { interface_route } : {}),
    },
    callback: function (data) {
      getRoute = false;
      showRouteServer(server_id);
      getRoute = true;
      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
      document.querySelectorAll(".closeModal").forEach((button) => {
        button.click();
      });
    },
    errorCallback: function () {
      document.getElementById("idLoading").style.display = "none";
      document.getElementById("idLoading").style.background =
        "hsl(0, 0%, 100%)";
    },
  });
}

let serverIdForRemoveRoute;
let onlyNumber;
let getRoute = true;

async function showRouteServer(serverId) {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";

  let server_id;
  if (getRoute) {
    server_id = document.getElementById("serverIdRouteShow").value;
  } else {
    server_id = serverId;
  }

  let username, password, port;

  dataServer.forEach((server) => {
    if (server.id == server_id) {
      (username = server.username),
        (password = server.password),
        (port = Number(server.port));
    }
  });

  // let username = localStorage.getItem("userNameServer");
  // let password = localStorage.getItem("passwordServer");
  // let port = localStorage.getItem("port");
  serverIdForRemoveRoute = server_id;

  await useApi({
    url: `show-route-server`,
    method: "post",
    data: {
      server_id,
      username,
      password,
      ...(!!port ? { port } : {}),
    },
    callback: function (data) {
      const tableBody = document.getElementById("tBodyRoute");
      document.getElementById("tHeadRoute").style.display =
        "table-header-group";

      // پاکسازی جدول قبلی
      tableBody.innerHTML = "";

      // تمیز کردن اولیه رشته
      let cleanedOutput = data.output
        .replace(/\\n/g, "\n") // 🔴 تبدیل \n متنی به newline واقعی
        .replace(/\u001b\[\?2004[hl]/g, "")
        .replace(/\u001b/g, "")
        .trim();

      // تقسیم هر خط
      let rows = cleanedOutput.split("\n");
      const serverName =
        serverCard?.find((server) => server.id == server_id)?.name || "";


      // ساخت ردیف‌ها
      rows
        .filter((rowText) => {
          const line = rowText.trim();

          if (/^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+:.*[$#]$/.test(line)) {
            return false;
          }

          return line.length > 0;
        })
        .forEach((rowText, x) => {
          const cleanRow = rowText.trim();

          const tr = document.createElement("tr");
          tr.setAttribute("id", `trRoute${x}`);

          const tdText = document.createElement("td");
          tdText.textContent = cleanRow;

          tr.appendChild(tdText);
          tableBody.appendChild(tr);
        });

      // فرم‌ها رو پنهان/نمایش بده
      document.getElementById("idFormGetRoute")?.classList.remove("d-flex");
      document.getElementById("idFormGetRoute")?.classList.add("d-none");

      document.getElementById("idGetRoute")?.classList.remove("d-none");
      document.getElementById("idGetRoute")?.classList.add("d-flex");
    },
    // errorCallback: function () {
    //   getRoute = false;

    //   document.getElementById("tHeadRoute").style.display = "none";
    //   const tableBody = document.getElementById("tBodyRoute");
    //   tableBody.innerHTML = "";

    //   document.getElementById("idFormGetRoute")?.classList.remove("d-none");
    //   document.getElementById("idFormGetRoute")?.classList.add("d-flex");

    //   document.getElementById("idGetRoute")?.classList.remove("d-flex");
    //   document.getElementById("idGetRoute")?.classList.add("d-none");

    //   // document.getElementById("usernameGetRoute").value = username;
    //   // document.getElementById("passwordGetRoute").value = password;
    //   // document.getElementById("portGetRoute").value = port;
    // },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background = "hsl(0, 0%, 100%)";

  document.querySelectorAll(".iconRemoveRoute").forEach((icon) => {
    icon.addEventListener("click", () => {
      let str = icon.id;
      onlyNumber = str.replace(/\D/g, ""); // \D یعنی "غیر عدد"
      let username = localStorage.getItem("userNameServer");
      let password = localStorage.getItem("passwordServer");

      if (username == null || password == null) {
        document.getElementById("usernameRouteRemove").value = "";
        document.getElementById("passwordRouteRemove").value = "";
      } else {
        document.getElementById("usernameRouteRemove").value = username;
        document.getElementById("passwordRouteRemove").value = password;
      }
      document.getElementById("getwayRouteRemove").value = "";
      document.getElementById("destinationIpRouteRemove").value = "";
      document.getElementById("interfaceRouteRemove").value = "";
    });
  });
}

document.getElementById("buttonRemoveRoute").addEventListener("click", () => {
  removeRoute(onlyNumber);
});

async function removeRoute(x) {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";

  let server_id = document.getElementById("serverIdRoute").value;
  let username, password, port;

  dataServer.forEach((server) => {
    if (server.id == serverIdForRemoveRoute) {
      (username = server.username),
        (password = server.password),
        (port = server.port);
    }
  });

  // let username = document.getElementById("usernameRouteRemove").value;
  // let password = document.getElementById("passwordRouteRemove").value;
  // let port = document.getElementById("portRouteRemove").value;
  let geteway_ip = document.getElementById("getwayRouteRemove").value;
  let destination_ip = document.getElementById(
    "destinationIpRouteRemove"
  ).value;
  let interface_route = document.getElementById("interfaceRouteRemove").value;

  await useApi({
    url: `delete-route-server`,
    method: "post",
    data: {
      server_id: serverIdForRemoveRoute,
      username,
      password,
      ...(!!port ? { port } : {}),
      destination_ip,
      geteway_ip,
      ...(!!interface_route ? { interface_route } : {}),
    },
    callback: function (data) {
      showRouteServer(serverIdForRemoveRoute);

      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
      document.querySelectorAll(".closeModal").forEach((button) => {
        button.click();
      });
    },
    errorCallback: function () {
      document.getElementById("idLoading").style.display = "none";
      document.getElementById("idLoading").style.background =
        "hsl(0, 0%, 100%)";
    },
  });
}

document.getElementById("viewLTE").addEventListener("click", () => {
  document.getElementById("containerLTE").classList.remove("d-none");
  document.getElementById("containerBBU").classList.add("d-none");
});

document.getElementById("viewGSM").addEventListener("click", () => {
  document.getElementById("containerGSM").classList.remove("d-none");
  document.getElementById("containerBBU").classList.add("d-none");
});

document.getElementById("enbModule").addEventListener("click", () => {
  document.getElementById("moduleENB").classList.remove("d-none");
  document.getElementById("containerLTE").classList.add("d-none");
});

document.getElementById("rrModule").addEventListener("click", () => {
  document.getElementById("moduleRR").classList.remove("d-none");
  document.getElementById("containerLTE").classList.add("d-none");
});

document.getElementById("sibModule").addEventListener("click", () => {
  document.getElementById("moduleSIB").classList.remove("d-none");
  document.getElementById("containerLTE").classList.add("d-none");
});

document.getElementById("bscModule").addEventListener("click", () => {
  document.getElementById("moduleBSC").classList.remove("d-none");
  document.getElementById("containerGSM").classList.add("d-none");
});

const saveModal = document.getElementById("saveModal");
const successToast = document.getElementById("successToast");
const decInput = document.getElementById("enbIdDec");
const hexInput = document.getElementById("enbId");
const mccInput = document.getElementById("mcc");
const mncInput = document.getElementById("mnc");

// Convert decimal to hex when decimal input changes
decInput.addEventListener("input", function () {
  const decValue = parseInt(this.value) || 0;
  const hexValue = decValue.toString(16).toUpperCase();
  hexInput.value = hexValue;
});

// Convert hex to decimal when hex input changes
hexInput.addEventListener("input", function () {
  const hexValue = this.value.replace(/[^0-9A-Fa-f]/g, "");
  this.value = hexValue.toUpperCase();
  const decValue = parseInt(hexValue, 16) || 0;
  decInput.value = decValue;
});

// Ensure MCC is always 3 digits with leading zeros
mccInput.addEventListener("blur", function () {
  this.value = this.value.padStart(3, "0");
});

// Ensure MNC is 2 digits with leading zeros when saving
mncInput.addEventListener("blur", function () {
  const val = this.value;
  if (val.length === 3) {
    this.value = val; // Keep 3 digits as is
  } else {
    this.value = val.padStart(2, "0"); // Pad to 2 digits
  }
});

// Handle MIMO settings visibility and nof_ports
const transmissionSelect = document.getElementById("transmission");
const mimoSettings = document.getElementById("mimoSettings");
const nofPortsSelect = document.getElementById("nof_ports");

transmissionSelect.addEventListener("change", function () {
  mimoSettings.style.display = this.value === "MIMO" ? "block" : "none";
  if (this.value === "MIMO") {
    nofPortsSelect.value = "2";
    nofPortsSelect.disabled = true;
  }
});

saveModal.querySelector(".btn-success").addEventListener("click", function () {
  // Convert bandwidth to n_prb
  const bandwidthToNprb = {
    1.4: "6",
    3: "15",
    5: "25",
    10: "50",
    15: "75",
    20: "100",
  };
  const bandwidth = document.getElementById("bandwidth").value;
  const n_prb = bandwidthToNprb[bandwidth];

  // Collect values
  const data = {
    enb_id: document.getElementById("enbId").value,
    mcc: document.getElementById("mcc").value,
    mnc: document.getElementById("mnc").value,
    n_prb: n_prb, // Send n_prb instead of bandwidth
    transmission: document.getElementById("transmission").value,
    tm: document.getElementById("tm").value,
    nof_ports: document.getElementById("nof_ports").value,
    tx_gain: document.getElementById("txGain").value,
    rx_gain: document.getElementById("rxGain").value,
    mme_addr: [
      document.getElementById("mmeAddr1").value,
      document.getElementById("mmeAddr2").value,
      document.getElementById("mmeAddr3").value,
      document.getElementById("mmeAddr4").value,
    ].join("."),
    gtp_bind_addr: [
      document.getElementById("gtpBindAddr1").value,
      document.getElementById("gtpBindAddr2").value,
      document.getElementById("gtpBindAddr3").value,
      document.getElementById("gtpBindAddr4").value,
    ].join("."),
    s1c_bind_addr: [
      document.getElementById("s1cBindAddr1").value,
      document.getElementById("s1cBindAddr2").value,
      document.getElementById("s1cBindAddr3").value,
      document.getElementById("s1cBindAddr4").value,
    ].join("."),
  };

  // Send to backend
  // fetch('{% url "core:save_enb" %}', {
  //     method: 'POST',
  //     headers: {
  //         'Content-Type': 'application/json',
  //         'X-CSRFToken': '{{ csrf_token }}'
  //     },
  //     body: JSON.stringify(data)
  // })
  // .then(response => response.json())
  // .then(data => {
  //     if (data.status === 'success') {
  //         // Close the modal
  //         const modal = bootstrap.Modal.getInstance(saveModal);
  //         modal.hide();

  //         // Show success toast
  //         const toast = new bootstrap.Toast(successToast);
  //         toast.show();
  //     } else {
  //         alert('Error saving configuration: ' + data.message);
  //     }
  // })
  // .catch(error => {
  //     alert('Error saving configuration: ' + error);
  // });
});

// const saveModal = document.getElementById('saveModal');
// const successToast = document.getElementById('successToast');
const numCellsSelect = document.getElementById("numCells");
const cellCards = [
  document.querySelector('[data-cell="0"]'),
  document.querySelector('[data-cell="1"]'),
  document.querySelector('[data-cell="2"]'),
];

// Setup hex-decimal conversion for each cell
for (let i = 0; i < 3; i++) {
  // cell_id conversion
  const cellDecInput = document.getElementById(`cellIdDec${i}`);
  const cellHexInput = document.getElementById(`cellId${i}`);

  // Convert decimal to hex when decimal input changes
  cellDecInput.addEventListener("input", function () {
    let decValue = parseInt(this.value) || 0;
    if (decValue < 0) {
      this.value = 0;
      decValue = 0;
    }
    const hexValue = decValue.toString(16).padStart(2, "0").toUpperCase();
    cellHexInput.value = hexValue;
  });

  // Convert hex to decimal when hex input changes
  cellHexInput.addEventListener("input", function () {
    const hexValue = this.value.replace(/[^0-9A-Fa-f]/g, "");
    this.value = hexValue.toUpperCase();
    const decValue = parseInt(hexValue, 16) || 0;
    cellDecInput.value = decValue;
  });

  // tac conversion
  const tacDecInput = document.getElementById(`tacDec${i}`);
  const tacHexInput = document.getElementById(`tac${i}`);

  // Convert decimal to hex when decimal input changes
  tacDecInput.addEventListener("input", function () {
    let decValue = parseInt(this.value) || 0;
    if (decValue < 0) {
      this.value = 0;
      decValue = 0;
    }
    const hexValue = decValue.toString(16).padStart(4, "0").toUpperCase();
    tacHexInput.value = hexValue;
  });

  // Convert hex to decimal when hex input changes
  tacHexInput.addEventListener("input", function () {
    const hexValue = this.value.replace(/[^0-9A-Fa-f]/g, "");
    this.value = hexValue.toUpperCase();
    const decValue = parseInt(hexValue, 16) || 0;
    tacDecInput.value = decValue;
  });
}

// Setup hex-decimal conversion for measurement cell ECIs
for (let cellNum = 0; cellNum < 3; cellNum++) {
  for (let measCellNum = 0; measCellNum < 2; measCellNum++) {
    const decInput = document.getElementById(
      `measCell${cellNum}EciDec${measCellNum}`
    );
    const hexInput = document.getElementById(
      `measCell${cellNum}Eci${measCellNum}`
    );

    // Convert decimal to hex when decimal input changes
    decInput.addEventListener("input", function () {
      let decValue = parseInt(this.value) || 0;
      if (decValue < 0) {
        this.value = 0;
        decValue = 0;
      }
      const hexValue = decValue.toString(16).padStart(5, "0").toUpperCase();
      hexInput.value = hexValue;
    });

    // Convert hex to decimal when hex input changes
    hexInput.addEventListener("input", function () {
      const hexValue = this.value.replace(/[^0-9A-Fa-f]/g, "");
      this.value = hexValue.toUpperCase();
      const decValue = parseInt(hexValue, 16) || 0;
      decInput.value = decValue;
    });
  }
}

// Function to update cell visibility
function updateCellVisibility() {
  const selectedNum = parseInt(numCellsSelect.value);
  cellCards.forEach((card, index) => {
    if (card) {
      card.style.display = index < selectedNum ? "block" : "none";
    }
  });
}

// Initialize visibility
updateCellVisibility();

// Update visibility when selection changes
numCellsSelect.addEventListener("change", updateCellVisibility);

saveModal.querySelector(".btn-success").addEventListener("click", function () {
  // Collect values
  const data = {
    num_cells: document.getElementById("numCells").value,
    cell_id_0: document.getElementById("cellId0").value,
    cell_id_1: document.getElementById("cellId1").value,
    cell_id_2: document.getElementById("cellId2").value,
    tac_0: document.getElementById("tac0").value,
    tac_1: document.getElementById("tac1").value,
    tac_2: document.getElementById("tac2").value,
    pci_0: document.getElementById("pci0").value,
    pci_1: document.getElementById("pci1").value,
    pci_2: document.getElementById("pci2").value,
    root_seq_idx_0: document.getElementById("rootSeqIdx0").value,
    root_seq_idx_1: document.getElementById("rootSeqIdx1").value,
    root_seq_idx_2: document.getElementById("rootSeqIdx2").value,
    dl_earfcn_0: document.getElementById("dlEarfcn0").value,
    dl_earfcn_1: document.getElementById("dlEarfcn1").value,
    dl_earfcn_2: document.getElementById("dlEarfcn2").value,
    ho_active_0: document.getElementById("hoActive0").value,
    ho_active_1: document.getElementById("hoActive1").value,
    ho_active_2: document.getElementById("hoActive2").value,
    meas_cell_0_eci_0: document.getElementById("measCell0Eci0").value,
    meas_cell_0_eci_1: document.getElementById("measCell0Eci1").value,
    meas_cell_1_eci_0: document.getElementById("measCell1Eci0").value,
    meas_cell_1_eci_1: document.getElementById("measCell1Eci1").value,
    meas_cell_2_eci_0: document.getElementById("measCell2Eci0").value,
    meas_cell_2_eci_1: document.getElementById("measCell2Eci1").value,
    meas_cell_0_dl_earfcn_0:
      document.getElementById("measCell0DlEarfcn0").value,
    meas_cell_0_dl_earfcn_1:
      document.getElementById("measCell0DlEarfcn1").value,
    meas_cell_1_dl_earfcn_0:
      document.getElementById("measCell1DlEarfcn0").value,
    meas_cell_1_dl_earfcn_1:
      document.getElementById("measCell1DlEarfcn1").value,
    meas_cell_2_dl_earfcn_0:
      document.getElementById("measCell2DlEarfcn0").value,
    meas_cell_2_dl_earfcn_1:
      document.getElementById("measCell2DlEarfcn1").value,
    meas_cell_0_pci_0: document.getElementById("measCell0Pci0").value,
    meas_cell_0_pci_1: document.getElementById("measCell0Pci1").value,
    meas_cell_1_pci_0: document.getElementById("measCell1Pci0").value,
    meas_cell_1_pci_1: document.getElementById("measCell1Pci1").value,
    meas_cell_2_pci_0: document.getElementById("measCell2Pci0").value,
    meas_cell_2_pci_1: document.getElementById("measCell2Pci1").value,
  };

  // Send to backend
  // fetch('{% url "core:save_rr" %}', {
  //     method: 'POST',
  //     headers: {
  //         'Content-Type': 'application/json',
  //         'X-CSRFToken': '{{ csrf_token }}'
  //     },
  //     body: JSON.stringify(data)
  // })
  // .then(response => response.json())
  // .then(data => {
  //     if (data.status === 'success') {
  //         // Close the modal
  //         const modal = bootstrap.Modal.getInstance(saveModal);
  //         modal.hide();

  //         // Show success toast
  //         const toast = new bootstrap.Toast(successToast);
  //         toast.show();
  //     } else {
  //         alert('Error saving configuration: ' + data.message);
  //     }
  // })
  // .catch(error => {
  //     alert('Error saving configuration: ' + error);
  // });
});

// Function to toggle handover settings visibility
window.toggleHoSettings = function (cellNum) {
  const hoActive = document.getElementById(`hoActive${cellNum}`);
  const hoSettings = document.getElementById(`hoSettings${cellNum}`);
  hoSettings.style.display = hoActive.value === "true" ? "block" : "none";
};

// const saveModal = document.getElementById('saveModal');
// const successToast = document.getElementById('successToast');

saveModal.querySelector(".btn-success").addEventListener("click", function () {
  // Collect values
  const data = {
    prach_freq_offset: document.getElementById("prachFreqOffset").value,
  };
  // Send to backend
  // fetch('{% url "core:save_sib" %}', {
  //     method: 'POST',
  //     headers: {
  //         'Content-Type': 'application/json',
  //         'X-CSRFToken': '{{ csrf_token }}'
  //     },
  //     body: JSON.stringify(data)
  // })
  // .then(response => response.json())
  // .then(data => {
  //     if (data.status === 'success') {
  //         // Close the modal
  //         const modal = bootstrap.Modal.getInstance(saveModal);
  //         modal.hide();

  //         // Show success toast
  //         const toast = new bootstrap.Toast(successToast);
  //         toast.show();
  //     } else {
  //         alert('Error saving configuration: ' + data.message);
  //     }
  // })
  // .catch(error => {
  //     alert('Error saving configuration: ' + error);
  // });
});

// const saveModal = document.getElementById('saveModal');
//     const successToast = document.getElementById('successToast');
const numBtsSelect = document.getElementById("numBts");
const btsCards = [
  document.querySelector('[data-bts="0"]'),
  document.querySelector('[data-bts="1"]'),
  document.querySelector('[data-bts="2"]'),
];

// Setup hex-decimal conversion for each BTS
for (let i = 0; i < 3; i++) {
  const decInput = document.getElementById(`locationAreaCodeDec${i}`);
  const hexInput = document.getElementById(`locationAreaCode${i}`);

  // Convert decimal to hex when decimal input changes
  decInput.addEventListener("input", function () {
    let decValue = parseInt(this.value) || 0;
    if (decValue < 0) {
      this.value = 0;
      decValue = 0;
    }
    const hexValue = decValue.toString(16).padStart(4, "0").toUpperCase();
    hexInput.value = hexValue;
  });

  // Convert hex to decimal when hex input changes
  hexInput.addEventListener("input", function () {
    const hexValue = this.value.replace(/[^0-9A-Fa-f]/g, "");
    this.value = hexValue.toUpperCase();
    const decValue = parseInt(hexValue, 16) || 0;
    decInput.value = decValue;
  });
}

// Function to update BTS visibility
function updateBtsVisibility() {
  const selectedNum = parseInt(numBtsSelect.value);
  btsCards.forEach((card, index) => {
    if (card) {
      card.style.display = index < selectedNum ? "block" : "none";
    }
  });
}

// Initialize visibility
updateBtsVisibility();

// Update visibility when selection changes
numBtsSelect.addEventListener("change", updateBtsVisibility);

saveModal.querySelector(".btn-success").addEventListener("click", function () {
  // Collect all values
  const data = {
    num_bts: document.getElementById("numBts").value,
    network_country_code: document.getElementById("networkCountryCode").value,
    mobile_network_code: document.getElementById("mobileNetworkCode").value,
    handover: document.getElementById("handover").value,
    band_0: document.getElementById("band0").value,
    arfcn_0: document.getElementById("arfcn0").value,
    base_station_id_code_0: document.getElementById("baseStationIdCode0").value,
    cell_identity_0: document.getElementById("cellIdentity0").value,
    ipa_unit_id_0: document.getElementById("ipaUnitId0").value,
    location_area_code_0: document.getElementById("locationAreaCode0").value,
    ms_max_power_0: document.getElementById("msMaxPower0").value,
    rxlev_access_min_0: document.getElementById("rxlevAccessMin0").value,
    band_1: document.getElementById("band1").value,
    arfcn_1: document.getElementById("arfcn1").value,
    base_station_id_code_1: document.getElementById("baseStationIdCode1").value,
    cell_identity_1: document.getElementById("cellIdentity1").value,
    ipa_unit_id_1: document.getElementById("ipaUnitId1").value,
    location_area_code_1: document.getElementById("locationAreaCode1").value,
    ms_max_power_1: document.getElementById("msMaxPower1").value,
    rxlev_access_min_1: document.getElementById("rxlevAccessMin1").value,
    band_2: document.getElementById("band2").value,
    arfcn_2: document.getElementById("arfcn2").value,
    base_station_id_code_2: document.getElementById("baseStationIdCode2").value,
    cell_identity_2: document.getElementById("cellIdentity2").value,
    ipa_unit_id_2: document.getElementById("ipaUnitId2").value,
    location_area_code_2: document.getElementById("locationAreaCode2").value,
    ms_max_power_2: document.getElementById("msMaxPower2").value,
    rxlev_access_min_2: document.getElementById("rxlevAccessMin2").value,
  };

  // Send to backend
  // fetch('{% url "core:save_bsc" %}', {
  //     method: 'POST',
  //     headers: {
  //         'Content-Type': 'application/json',
  //         'X-CSRFToken': '{{ csrf_token }}'
  //     },
  //     body: JSON.stringify(data)
  // })
  // .then(response => response.json())
  // .then(data => {
  //     if (data.status === 'success') {
  //         // Close the modal
  //         const modal = bootstrap.Modal.getInstance(saveModal);
  //         modal.hide();

  //         // Show success toast
  //         const toast = new bootstrap.Toast(successToast);
  //         toast.show();
  //     } else {
  //         alert('Error saving configuration: ' + data.message);
  //     }
  // })
  // .catch(error => {
  //     alert('Error saving configuration: ' + error);
  // });
});

function showViewFromHash() {
  const hash = location.hash || "#v-servers-home";
  switch (hash) {
    case "#v-servers-home":
      document.querySelector("#v-servers-tab").click();
      break;
    case "#v-module":
      document.querySelector("#v-module-tab").click();
      break;
    case "#v-access-levels":
      document.querySelector("#v-access-levels-tab").click();
      break;
    case "#v-users":
      if (!requestShowPermission) {
        document.querySelector("#v-users-tab").click();
      }
      break;
    case "#v-subscribers":
      document.querySelector("#v-subscribers-tab").click();
      break;
    case "#v-trace":
      document.querySelector("#v-trace-tab").click();
      break;
    case "#v-kpi":
      document.querySelector("#v-kpi-tab").click();
      break;
    case "#v-ping":
      document.querySelector("#v-ping-tab").click();
      break;
    case "#v-route":
      document.querySelector("#v-route-tab").click();
      break;
    case "#v-monitoring":
      document.querySelector("#V-monitoring-tab").click();
      break;
    case "#v-facePlate":
      document.querySelector("#v-facePlate-tab").click();
      break;
    case "#v-log":
      document.querySelector("#v-log-tab").click();
      break;
    case "#v-setting":
      document.querySelector("#v-setting-tab").click();
      break;
    default:
      document.querySelector("#v-servers-tab").click();
      break;
  }
}
// وقتی کاربر دکمه back/forward رو زد یا hash تغییر کرد
window.addEventListener("hashchange", showViewFromHash);

document.getElementById("deletRoute").addEventListener("click", () => {
  // let username = localStorage.getItem("userNameServer");
  // let password = localStorage.getItem("passwordServer");
  // let port = localStorage.getItem("port");

  // document.getElementById("usernameRouteRemove").value = username;
  // document.getElementById("passwordRouteRemove").value = password;
  // document.getElementById("portRouteRemove").value = port;
  document.getElementById("getwayRouteRemove").value = "";
  document.getElementById("destinationIpRouteRemove").value = "";
  document.getElementById("interfaceRouteRemove").value = "";
});

async function allMonitoring() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  await useApi({
    url: "get-all-monitoring",
    callback: function (data) {
      for (let i = 0; i < data.monitoring.length; i++) {
        addMonitoringTab(
          data.monitoring[i].title,
          data.monitoring[i].address,
          data.monitoring[i].id
        );
      }
    },
  });
  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 1)";
}

document.getElementById("buttonAddMonitoring").addEventListener("click", () => {
  createMonitoring();
});

async function createMonitoring() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";

  let title = document.getElementById("moduleAddTitle").value;
  let address = document.getElementById("moduleAddAddress").value;

  await useApi({
    method: "post",
    url: "create-monitoring",
    data: { title, address },
    callback: function (data) {
      addMonitoringTab(
        data.monitoring.title,
        data.monitoring.address,
        data.monitoring.id
      );
    },
  });

  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 1)";

  document.getElementById("moduleAddTitle").value = "";
  document.getElementById("moduleAddAddress").value = "";
  document.getElementById("cancelMonitoring").click();
}

let currentEditingTabId = null;
let currentDeletingTabId = null;
let idMonitoring;

document
  .getElementById("buttonEditMonitoring")
  .addEventListener("click", () => {
    editMonitoring();
  });

async function editMonitoring() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";

  let title = document.getElementById("moduleEditTitle").value;
  let address = document.getElementById("moduleEditAddress").value;

  await useApi({
    method: "put",
    url: `edit-monitoring/${idMonitoring}`,
    data: {
      title,
      address,
    },
    callback: function () {
      if (!currentEditingTabId) return;

      const newTitle = document.getElementById("moduleEditTitle").value.trim();
      const newAddress = document
        .getElementById("moduleEditAddress")
        .value.trim();
      if (!newTitle || !newAddress) return;

      // تصحیح آدرس برای iframe
      let finalURL = newAddress;
      if (
        !newAddress.startsWith("http://") &&
        !newAddress.startsWith("https://")
      ) {
        finalURL = "http://" + newAddress;
      }

      // تغییر عنوان تب
      const tabButton = document.querySelector(
        `[data-bs-target="#${currentEditingTabId}"]`
      );
      if (tabButton) {
        tabButton.innerText = newTitle;
      }

      // تغییر آدرس iframe
      const iframe = document.getElementById(`iframe_${currentEditingTabId}`);
      if (iframe) {
        iframe.src = finalURL;
      }

      // تغییر مقدار input زیر iframe
      const inputAddress = document.getElementById(
        `url_input_${currentEditingTabId}`
      );
      if (inputAddress) {
        inputAddress.value = finalURL;
      }

      document.getElementById("cancelEditMonitoring").click();

      currentEditingTabId = null;
    },
  });

  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 1)";

  document.getElementById("moduleAddTitle").value = "";
  document.getElementById("moduleAddAddress").value = "";
  document.getElementById("cancelMonitoring").click();
}

document.getElementById("deleteTabMonitoring").addEventListener("click", () => {
  deleteMonitoring();
});

async function deleteMonitoring() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";
  await useApi({
    method: "delete",
    url: `delete-monitoring/${idMonitoring}`,
    callback: function () {
      if (!currentDeletingTabId) return;

      // حذف دکمه تب
      const tabLi = document.querySelector(
        `button[data-bs-target="#${currentDeletingTabId}"]`
      )?.parentElement;
      if (tabLi) tabLi.remove();

      // حذف محتوای تب
      const tabPane = document.getElementById(currentDeletingTabId);
      if (tabPane) tabPane.remove();

      // انتخاب یک تب دیگر برای فعال شدن (اگر موجود بود)
      const firstTabBtn = document.querySelector("#monitoringTabs .nav-link");
      if (firstTabBtn) firstTabBtn.click();

      currentDeletingTabId = null;
    },
  });

  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 1)";
}

function addMonitoringTab(title, address, idTab) {
  const tabs = document.getElementById("monitoringTabs");
  const tabContent = document.getElementById("monitoringTabContent");

  // اگر آدرس http نداشت اضافه کنیم
  if (!address.startsWith("http://") && !address.startsWith("https://")) {
    address = "http://" + address;
  }

  const id = "tab_" + Date.now();

  const li = document.createElement("li");
  li.setAttribute("id", idTab);
  li.className = "nav-item";
  li.innerHTML = `
    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#${id}">
      ${title}
    </button>
  `;
  tabs.insertBefore(li, document.getElementById("addTabBtn").parentElement);

  const div = document.createElement("div");
  div.className = "tab-pane fade";
  div.id = id;
  div.innerHTML = `
    <div class="d-flex w-100 align-items-center justify-content-center">
      <img src="../assets/img/Monitor-cuate.svg" class="imgMonitoring" />
    </div>

    <div class="d-flex divInputMonitoring mt-2">
      <input type="text" id="url_input_${id}" value="${address}" placeholder="Enter URL" class="form-control mb-1 w-50" disabled/>
      <button
        class="btn btn-primary mb-1 ms-3 buttonMonitoring">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            fill="currentColor"
            class="bi bi-eye-fill me-1"
            viewBox="0 0 16 16"
          >
          <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
          <path
            d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"
          />
        </svg>
          Go
      </button>
    </div>
  `;

  tabContent.appendChild(div);
  // فعال کردن تب جدید
  li.querySelector("button").click();

  const button = div.querySelector(".buttonMonitoring");

  button.addEventListener("click", () => {
    let addressUrl = document.getElementById(`url_input_${id}`).value;
    window.open(addressUrl, "_blank");
  });

  // رویداد دابل کلیک برای باز کردن مودال ویرایش
  li.querySelector("button").addEventListener("dblclick", () => {
    idMonitoring = idTab;
    currentEditingTabId = id;

    const currentTitle = li.querySelector("button").innerText.trim();
    document.getElementById("moduleEditTitle").value = currentTitle;

    // دریافت آدرس فعلی iframe
    const input = document.getElementById(`url_input_${id}`).value;
    const currentAddress = input;
    document.getElementById("moduleEditAddress").value = currentAddress;

    document.getElementById("EditMonitoring").click();
  });

  li.querySelector("button").addEventListener("contextmenu", (e) => {
    e.preventDefault(); // جلوگیری از نمایش منوی راست‌کلیک مرورگر
    idMonitoring = idTab;

    currentDeletingTabId = id;

    // گرفتن عنوان تب
    const tabTitle = li.querySelector("button").innerText.trim();

    // نمایش عنوان در داخل مودال
    document.getElementById("spanDeleteMonitoring").innerText = tabTitle;

    // نمایش مودال
    document.getElementById("DeleteMonitoring").click();
  });
}

let testKpi;

document.getElementById("initKpi").addEventListener("click", () => {
  document.getElementById("kpiAddFile").value = "";
  document.getElementById("kpiPath").value = "";
});

document.getElementById("buttonInitKpi").addEventListener("click", () => {
  initKpi();
});

async function indexKpi() {
  document.getElementById("idLoading").style.display = "flex";

  let formData = new FormData();

  let fileInput = document.getElementById("kpiAddFile");
  let fileModule = fileInput.files[0];

  let pathInput = document.getElementById("kpiPath");

  if (fileModule) {
    formData.append("file", fileModule);
  }
  formData.append("path", pathInput);

  await useApi({
    url: `kpi/index`,
    callback: function (data) {
      testKpi = data;
      kpiStatus = false;
      loadKpiTable();
    },
  });

  document.getElementById("idLoading").style.display = "none";
}

async function initKpi() {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";

  let formData = new FormData();

  let fileInput = document.getElementById("kpiAddFile");
  let fileModule = fileInput.files[0];

  let pathInput = document.getElementById("kpiPath").value;

  if (fileModule) {
    formData.append("file", fileModule);
  }
  formData.append("path", JSON.stringify(pathInput));

  await useApi({
    method: "post",
    url: `kpi/init`,
    data: formData,
    headers: {
      "Content-Type": "multipart/form-data",
    },
    callback: function (data) {
      testKpi = data.items;
      loadKpiTable();
      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
      document.getElementById("cancelKPI").click();
    },
  });

  document.getElementById("idLoading").style.display = "none";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 1)";
}

async function editKpi(id, status) {
  document.getElementById("idLoading").style.display = "flex";
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 0.5)";

  await useApi({
    method: "put",
    url: `kpi/update`,
    data: { id, status },
    callback: function (data) {
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
  document.getElementById("idLoading").style.background =
    "hsla(0, 0%, 100%, 1)";
}

// تابع برای بارگذاری جدول KPI
function loadKpiTable() {
  const tbody = document.getElementById("tBodyKpi");
  tbody.innerHTML = ""; // خالی کردن جدول

  Object.values(testKpi).forEach((item) => {
    const tr = document.createElement("tr");

    const tdId = document.createElement("td");
    tdId.textContent = item.id;

    const tdDesc = document.createElement("td");
    tdDesc.textContent = item.title;

    const tdStatus = document.createElement("td");
    tdStatus.innerHTML = `
      <div class="mt-2">
        <label class="switch">
          <input 
            type="checkbox" 
            class="inputToggle" 
            data-id="${item.id}" 
            ${item.status === "on" ? "checked" : ""}>
          <span class="slider"></span>
        </label>
      </div>
    `;

    tr.appendChild(tdId);
    tr.appendChild(tdDesc);
    tr.appendChild(tdStatus);
    tbody.appendChild(tr);
  });

  // افزودن رویداد کلیک برای toggle‌ها
  document.querySelectorAll(".inputToggle").forEach((toggle) => {
    toggle.addEventListener("change", async (e) => {
      const id = e.target.getAttribute("data-id");
      const status = e.target.checked ? "on" : "off";
      await editKpi(id, status);
    });
  });
}