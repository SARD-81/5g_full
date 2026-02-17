import useApi, { setCookie } from "./useApi.js";
import { setAuthName } from "./auth.js";
import IconBBDH from "../img/logo white with logo type.png";
import favIconBBDH from "../img/logo white.png";
import IconMCI from "../img/logo-mci-02 (1).svg";
import favIconMCI from "../img/logo-mci-02 (1).svg";
import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";
let project = import.meta.env.VITE_API_PROJECT;

if (project == "BBDH") {
  document.getElementById("logo").src = IconBBDH;
  document.getElementById("logo").classList.remove("w-75");
  document.getElementById("logo").classList.remove("my-3");
  document.getElementById("logo").style.width = "100%";
  document.getElementById("logo").style.margin = "0";
  document.getElementById("title").innerHTML = "BBDH";
  document.getElementById("favIcon").href = favIconBBDH;
  document.getElementById("backLogoOTP").classList.add("backLogo");
} else if (project == "MCI") {
  document.getElementById("logo").src = IconMCI;
  document.getElementById("title").innerHTML = "MCI";
  document.getElementById("favIcon").href = favIconMCI;
} else if (project == "RRU") {
  document.getElementById("logo").src = IconMCI;
  document.getElementById("title").innerHTML = "RRU";
  document.getElementById("favIcon").href = favIconMCI;
}

(function () {
  const inputs = document.querySelectorAll("#otp-input input");

  for (let i = 0; i < inputs.length; i++) {
    const input = inputs[i];

    input.addEventListener("input", function () {
      // handling normal input
      if (input.value.length == 1 && i + 1 < inputs.length) {
        inputs[i + 1].focus();
      }

      // if a value is pasted, put each character to each of the next input
      if (input.value.length > 1) {
        // sanitise input
        if (isNaN(input.value)) {
          input.value = "";
          updateInput();
          return;
        }

        // split characters to array
        const chars = input.value.split("");

        for (let pos = 0; pos < chars.length; pos++) {
          // if length exceeded the number of inputs, stop
          if (pos + i >= inputs.length) break;

          // paste value
          let targetInput = inputs[pos + i];
          targetInput.value = chars[pos];
        }

        // focus the input next to the last pasted character
        let focus_index = Math.min(inputs.length - 1, i + chars.length);
        inputs[focus_index].focus();
      }
      updateInput();
    });

    input.addEventListener("keydown", function (e) {
      // backspace button
      if (e.keyCode == 8 && input.value == "" && i != 0) {
        // shift next values towards the left
        for (let pos = i; pos < inputs.length - 1; pos++) {
          inputs[pos].value = inputs[pos + 1].value;
        }

        // clear previous box and focus on it
        inputs[i - 1].value = "";
        inputs[i - 1].focus();
        updateInput();
        return;
      }

      // delete button
      if (e.keyCode == 46 && i != inputs.length - 1) {
        // shift next values towards the left
        for (let pos = i; pos < inputs.length - 1; pos++) {
          inputs[pos].value = inputs[pos + 1].value;
        }

        // clear the last box
        inputs[inputs.length - 1].value = "";
        input.select();
        e.preventDefault();
        updateInput();
        return;
      }

      // left button
      if (e.keyCode == 37) {
        if (i > 0) {
          e.preventDefault();
          inputs[i - 1].focus();
          inputs[i - 1].select();
        }
        return;
      }

      // right button
      if (e.keyCode == 39) {
        if (i + 1 < inputs.length) {
          e.preventDefault();
          inputs[i + 1].focus();
          inputs[i + 1].select();
        }
        return;
      }
    });
  }

  function updateInput() {
    let inputValue = Array.from(inputs).reduce(function (otp, input) {
      otp += input.value.length ? input.value : " ";
      return otp;
    }, "");
    document.querySelector("input[name=otp]").value = inputValue;
  }
})();

let timer = document.getElementById("time");
document
  .getElementById("loginPhoneBtn")
  .addEventListener("click", async function (event) {
    event.preventDefault();

    if (reCAPTCHA == 1) {
      await handleSubmit();
      if (!statusReCAPTCHA) {
        grecaptcha.reset(widgetId);
        return;
      }
      phone();
    } else {
      // if (
      //   document.getElementById("captchaLogin").value.toLowerCase() !=
      //   captchaTextLower
      // ) {
      //   if (document.getElementById("captchaLogin").value) {
      //     Toastify({
      //       text: "The captcha is incorrect.",
      //       style: {
      //         background:
      //           "linear-gradient(to right,rgb(255, 4, 4),rgb(226, 0, 0))",
      //       },
      //     }).showToast();
      //   } else {
      //     Toastify({
      //       text: "Please fill in the captcha.",
      //       style: {
      //         background:
      //           "linear-gradient(to right,rgb(255, 4, 4),rgb(226, 0, 0))",
      //       },
      //     }).showToast();
      //   }
      //   // generateCAPTCHA();
      //   disableReloadCaptcha();
      //   getCaptcha();
      //   document.getElementById("captchaLogin").value = "";
      //   return;
      // }
      phone();
    }
  });

let inputCaptcha = document.getElementById("captchaLogin");
inputCaptcha.addEventListener("keydown", async function (event) {
  if (event.key === "Enter") {
    event.preventDefault();
    if (reCAPTCHA == 1) {
      if (enterLogin) {
        await handleSubmit();
        if (!statusReCAPTCHA) {
          grecaptcha.reset(widgetId);
          return;
        }
        phone();
      } else {
        login();
      }
    } else {
      if (enterLogin) {
        // if (
        //   document.getElementById("captchaLogin").value.toLowerCase() !=
        //   captchaTextLower
        // ) {
        //   if (document.getElementById("captchaLogin").value) {
        //     Toastify({
        //       text: "The captcha is incorrect.",
        //       style: {
        //         background:
        //           "linear-gradient(to right,rgb(255, 4, 4),rgb(226, 0, 0))",
        //       },
        //     }).showToast();
        //   } else {
        //     Toastify({
        //       text: "Please fill in the captcha.",
        //       style: {
        //         background:
        //           "linear-gradient(to right,rgb(255, 4, 4),rgb(226, 0, 0))",
        //       },
        //     }).showToast();
        //   }
        //   // generateCAPTCHA();
        //   disableReloadCaptcha();
        //   getCaptcha();
        //   document.getElementById("captchaLogin").value = "";
        //   return;
        // }
        phone();
      } else {
        login();
      }
    }
  }
});

let inputPhoneNamberCaptcha = document.getElementById("phoneNamber");
inputPhoneNamberCaptcha.addEventListener("keydown", async function (event) {
  if (event.key === "Enter") {
    event.preventDefault();
    if (reCAPTCHA == 1) {
      if (enterLogin) {
        await handleSubmit();
        if (!statusReCAPTCHA) {
          grecaptcha.reset(widgetId);
          return;
        }
        phone();
      } else {
        login();
      }
    } else {
      if (enterLogin) {
        // if (
        //   document.getElementById("captchaLogin").value.toLowerCase() !=
        //   captchaTextLower
        // ) {
        //   if (document.getElementById("captchaLogin").value) {
        //     Toastify({
        //       text: "The captcha is incorrect.",
        //       style: {
        //         background:
        //           "linear-gradient(to right,rgb(255, 4, 4),rgb(226, 0, 0))",
        //       },
        //     }).showToast();
        //   } else {
        //     Toastify({
        //       text: "Please fill in the captcha.",
        //       style: {
        //         background:
        //           "linear-gradient(to right,rgb(255, 4, 4),rgb(226, 0, 0))",
        //       },
        //     }).showToast();
        //   }
        //   // generateCAPTCHA();
        //   disableReloadCaptcha();
        //   getCaptcha();
        //   document.getElementById("captchaLogin").value = "";
        //   return;
        // }
        phone();
      } else {
        login();
      }
    }
  }
});

let inputEnd = document.getElementById("6");
inputEnd.addEventListener("keydown", function (event) {
  if (event.key === "Enter") {
    event.preventDefault();
    login();
  }
});

inputEnd.addEventListener("input", function () {
  if (inputEnd.value.length == 1) {
    login();
  }
});

let inputNumber;
let enterLogin = true;

let showPhone = false;

async function phone() {
  if (showPhone) return;
  showPhone = true;
  let inputCaptch = document.getElementById("captchaLogin").value;
  let phoneNumber = document.getElementById("phoneNamber").value;
  document.getElementById("loaderPing").style.display = "block";
  document.getElementById("loginPhoneBtn").style.opacity = "0.5";
  await useApi({
    method: "post",
    url: "send-login-by-phone",
    data: {
      phone: phoneNumber,
      captcha_key: keyCaptcha,
      captcha: inputCaptch,
    },
    callback: function (data) {
      enterLogin = false;
      document.getElementById("otp-input").classList.remove("OTP");
      document.getElementById("idDivReload").classList.remove("OTP");
      document.getElementById("idDivReload").classList.add("divFlex");
      document.getElementById("loginPhoneBtn").style.display = "none";
      document.getElementById("loginPhoneBtn").classList.remove("d-flex");
      document.getElementById("login").style.display = "flex";
      document.getElementById("idButtonReload").disabled = true;
      document.getElementById("idButtonReload").style.opacity = "0.5";
      document.querySelector(".divContainer").style.height = "520px";
      document.getElementById("reloadCaptcha").style.pointerEvents = "none";
      document.getElementById("reloadCaptcha").style.opacity = "0.7";

      Toastify({
        text: data.msg,
        style: {
          background:
            "linear-gradient(to right,rgb(0, 172, 14),rgb(0, 167, 14))",
        },
      }).showToast();
      // document.getElementById("otp-input").classList.add("OTPblock")
      startTimer(120, timer);
      document.getElementById("phoneNamber").disabled = true;
      document.getElementById("captchaLogin").disabled = true;
    },
    errorCallback: function () {
      // generateCAPTCHA();
      disableReloadCaptcha();
      if(!reCAPTCHA) {
        getCaptcha();
      }
      document.getElementById("captchaLogin").value = "";
    },
  });
  document.getElementById("loginPhoneBtn").style.opacity = "1";
  document.getElementById("loaderPing").style.display = "none";
  showPhone = false;
}

document.getElementById("login").addEventListener("click", function () {
  login();
});

let dataLogin;
let showLogin = false;

async function login() {
  if (showLogin) return;
  showLogin = true;
  let phoneNumber = document.getElementById("phoneNamber").value;
  let code = "";
  for (let i = 0; i < 6; i++) {
    code += document.getElementById(i + 1).value;
  }
  let inputCaptch = document.getElementById("captchaLogin").value;
  document.getElementById("loaderPingLogin").style.display = "block";
  document.getElementById("login").style.opacity = "0.5";
  await useApi({
    method: "post",
    url: "login-by-phone",
    data: {
      phone: phoneNumber,
      code,
      captcha_key: keyCaptcha,
      captcha: inputCaptch,
    },
    callback: function (data) {
      setCookie("userToken", data.data.token);
      localStorage.setItem("dataUser_name", data.data.user.auth_name);
      localStorage.setItem("dataUser_role", data.data.user.roles[0]);
      window.location.href = "../views/dashboard.html";
    },
  });
  document.getElementById("login").style.opacity = "1";
  document.getElementById("loaderPingLogin").style.display = "none";
  showLogin = false;
}

document
  .getElementById("idButtonReload")
  .addEventListener("click", function (event) {
    event.preventDefault();
    phone();
  });

let valueTimer;
function startTimer(duration, display) {
  let timer = duration;
  valueTimer = setInterval(function () {
    let minutes = parseInt(timer / 60);
    let seconds = parseInt(timer % 60);
    minutes = minutes < 10 ? "0" + minutes : minutes;
    seconds = seconds < 10 ? "0" + seconds : seconds;
    display.textContent = minutes + ":" + seconds;
    if (--timer < 0) {
      clearInterval(valueTimer);
      document.getElementById("idButtonReload").disabled = false;
      document.getElementById("idButtonReload").style.opacity = "1";
    }
  }, 1000);
}

document
  .getElementById("reloadCaptcha")
  .addEventListener("click", function (event) {
    event.preventDefault();
    // generateCAPTCHA();
    disableReloadCaptcha();
    if(!reCAPTCHA) {
      getCaptcha();
    }  });

function disableReloadCaptcha() {
  const el = document.getElementById("reloadCaptcha");
  el.style.opacity = "0.5";
  el.style.filter = "blur(2px)";
}

let input = document.getElementById("input"),
  submit = document.getElementById("submit"),
  CAPTCHA = document.getElementById("CAPTCHA");

input.onfocus = function () {
  "use strict";

  if (this.placeholder === "Type CAPTCHA Here") {
    this.placeholder = "";
  }
};

input.onblur = function () {
  "use strict";

  if (this.placeholder === "") {
    this.placeholder = "Type CAPTCHA Here";
  }
};

let captchaTextLower;

// function generateCAPTCHA() {
//   const captcha = document.getElementById("CAPTCHA");
//   captcha.innerHTML = "";

//   // متن تصادفی برای CAPTCHA (با حروف بزرگ و کوچک)
//   const chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789";
//   let captchaText = "";
//   for (let i = 0; i < 6; i++) {
//     captchaText += chars.charAt(Math.floor(Math.random() * chars.length));
//   }

//   // ذخیره نسخه lowercase برای مقایسه
//   captchaTextLower = captchaText.toLowerCase();

//   // تنظیمات تصادفی برای پس‌زمینه
//   captcha.style.setProperty(
//     "--gradient-angle",
//     `${Math.floor(Math.random() * 50)}deg`
//   );
//   captcha.style.setProperty(
//     "--gradient-size",
//     `${2 + Math.random() * 10}px ${2 + Math.random() * 10}px`
//   );

//   // ایجاد هر کاراکتر با سبک تصادفی
//   for (let char of captchaText) {
//     const charElement = document.createElement("span");
//     charElement.className = "char";
//     charElement.textContent = char;

//     const fontSize = 20 + Math.floor(Math.random() * 5);
//     charElement.style.fontSize = `${fontSize}px`;

//     charElement.style.marginRight = `5px`;
//     charElement.style.opacity = `0.7`;

//     const rotate = -20 + Math.floor(Math.random() * 50);
//     charElement.style.setProperty("--char-rotate", `${rotate}deg`);

//     // const verticalPos = 1 + Math.floor(Math.random() * 1);
//     // charElement.style.verticalAlign = `${verticalPos}px`;

//     captcha.appendChild(charElement);
//   }

//   submit.onclick = function () {
//     if (input.value === "") {
//       document.querySelector(".box").style.height = "280px";
//       document.getElementById("wrong").style.display = "block";
//       document.getElementById("wrong").innerHTML = "This Field Can't Be Empty";
//       input.focus();
//       generateCAPTCHA();
//     } else if (input.value.toLowerCase() !== captchaTextLower) {
//       // مقایسه غیرحساس
//       document.querySelector(".box").style.height = "280px";
//       document.getElementById("wrong").style.display = "block";
//       document.getElementById("wrong").innerHTML = "Try Again";
//       input.value = "";
//       generateCAPTCHA();
//     } else {
//       document.getElementById("boxCaptcha").style.display = "none";
//     }
//   };
// }

window.onload = async function () {
  // generateCAPTCHA();
  disableReloadCaptcha();  
  document.getElementById("idLoadingLogin").style.display = "flex";
  await statusReCaptcha();
  loadRecaptcha();  
  if(!reCAPTCHA) {
    getCaptcha();
  }
};

let reCAPTCHA;

async function statusReCaptcha() {
  await useApi({
    url: "get-status-reCapcha",
    callback: function (data) {
      if (data.data == 1) {
        document.getElementById("reCaptchaOnline").classList.remove("d-none");
        document.getElementById("reCaptchaOnline").classList.add("d-flex");
        document.getElementById("idDivContainer").style.height = "430px";
        reCAPTCHA = +data.data;
      } else {
        document.getElementById("chptchaOffline").classList.remove("d-none");
        document.getElementById("chptchaOffline").classList.add("d-flex");
        reCAPTCHA = +data.data;
      }
    },
  });
  document.getElementById("idLoadingLogin").style.display = "none";
}

let widgetId;

function loadRecaptcha() {
  const script = document.createElement("script");
  script.src =
    "https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit";
  script.async = true;
  script.defer = true;
  document.head.appendChild(script);
}

window.onloadCallback = function () {
  widgetId = grecaptcha.render("recaptcha-widget", {
    sitekey: "6LcSsSwrAAAAACr48sIuVQwBuBNjDiP8ksVqgkwa",
    theme: "light",
  });
};

let statusReCAPTCHA = false;

async function handleSubmit(endpoint) {
  const token = grecaptcha.getResponse(widgetId);

  if (!token) {
    Toastify({
      text: "The captcha is incorrect.",
      style: {
        background: "linear-gradient(to right,rgb(255, 4, 4),rgb(226, 0, 0))",
      },
    }).showToast();
    return;
  }

  document.getElementById("loaderPing").style.display = "block";
  if (document.getElementById("loginPhoneBtn")) {
    document.getElementById("loginPhoneBtn").style.opacity = "0.5";
  }
  if (document.getElementById("login")) {
    document.getElementById("login").style.opacity = "0.5";
  }

  await useApi({
    method: "post",
    url: "validation-reCaptcha",
    headers: { "Content-Type": "application/json" },
    data: JSON.stringify({ response: token }),
    callback: function (data) {
      statusReCAPTCHA = data.data.success;
    },
  });
  if (document.getElementById("loginPhoneBtn")) {
    document.getElementById("loginPhoneBtn").style.opacity = "1";
  }
  if (document.getElementById("login")) {
    document.getElementById("login").style.opacity = "1";
  }
  if (document.getElementById("loaderPing")) {
    document.getElementById("loaderPing").style.display = "none";
  }
  // grecaptcha.reset(widgetId);
}

const otpInput = document.getElementById("otp-input");
const input1 = document.getElementById("1");

const observer = new MutationObserver(() => {
  if (getComputedStyle(otpInput).display === "flex") {
    input1.focus();
    input1.select();
    observer.disconnect(); // فقط یک‌بار اجرا بشه
  }
});

observer.observe(otpInput, {
  attributes: true,
  attributeFilter: ["style", "class"],
});

window.history.pushState(null, "", window.location.href);
window.onpopstate = function () {
  history.pushState(null, "", window.location.href);
};

let keyCaptcha;
async function getCaptcha() {
  await useApi({
    url: "get-captcha",
    callback: function (data) {
      const imgSrc = data.image;
      keyCaptcha = data.key;
      document.getElementById("CAPTCHA").src = imgSrc;
      const el = document.getElementById("reloadCaptcha");
      el.style.opacity = "1";
      el.style.filter = "none";
    },
  });
  document.getElementById("idLoadingLogin").style.display = "none";
}
