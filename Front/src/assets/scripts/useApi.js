import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";
import axios from "axios";

export const setCookie = (cname, cvalue, exdays = 7) => {
  const d = new Date();
  d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
  let expires = "expires=" + d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
};

export const getCookie = (cname) => {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(";");
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == " ") {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
};

if (!window.hasShownOfflineToast) {
  window.addEventListener("offline", function updateOnlineStatus() {
    if (!window.hasShownOfflineToast) {
      window.hasShownOfflineToast = true; // جلوگیری از اجراهای بعدی
      Toastify({
        text: "You are not connected to the network.",
        style: {
          background: "linear-gradient(to right, rgb(255, 0, 0), rgb(231, 0, 0))",
        },
      }).showToast();
    }
  });
}

// export let userRole = ''

// export const setUserRole = function (role){
//   userRole = role
// }

export const removeCookie = (cname) => {
  document.cookie = `${cname}=;expires=1970;path=/`;
};

// axios.defaults.baseURL = "/back-end/api/";
axios.defaults.baseURL = "/api/";
axios.defaults.headers.common["Accept"] = "application/json";
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
// axios.defaults.baseURL = "/back-end/public/api/";
// axios.defaults.baseURL = "http://192.168.0.69/back-end/api/";
// axios.defaults.baseURL = "http://5g.anatoa.ir/api/";
// axios.defaults.baseURL = "http://192.168.43.104:8000/api/";

export let loadingItem = 0;
export let totalValue = 0;
export let loadedValue = 0;
export let estimatedValue = 0;
export let rateValue = 0;
export let onProgress = false;
export let ContentDisposition = "";

let controller;
export const abortRequest = () => controller.abort();

let progressInterval;
const startProgress = (progressEvent) => {
  // getEstimatedTime

  if (progressEvent.estimated) {
    if (progressEvent.estimated > 100) {
      estimatedValue = `${Math.round(progressEvent.estimated / 60)} دقیقه`;
    } else {
      estimatedValue = `${Math.round(progressEvent.estimated)} ثانیه`;
    }
  }

  rateValue = progressEvent.rate || 0;
  loadedValue = progressEvent.loaded;
  totalValue = progressEvent.total;
  loadingItem = Math.round((progressEvent.loaded * 100) / progressEvent.total);
};

const resetProgressVars = () => {
  clearInterval(progressInterval);
  loadingItem = 0;
  totalValue = 0;
  loadedValue = 0;
  estimatedValue = 0;
  rateValue = 0;
  controller = null;
  onProgress = false;
};

let timeSpace = 0;

export default async function (props) {
  const {
    method,
    url,
    data,
    callback,
    errorCallback,
    finallyCallback,
    upload,
    responseType,
    headers,
    accept,
    setToken,
  } = {
    method: props.method || "get",
    url: props.url || "",
    data: props.data || "",
    callback: props.callback || null,
    errorCallback: props.errorCallback || null,
    finallyCallback: props.finallyCallback || null,
    upload: props.upload || false,
    responseType: props.responseType || null,
    headers: props.headers || {},
    accept: props.accept || "application/json",
    setToken: props.setToken == false ? props.setToken : "true",
  };

  // const { getCookie } = useAuth;
  // Merge custom headers with the default headers

  const mergedHeaders = {
    Accept: accept,
    ...(setToken && getCookie("userToken")
      ? { Authorization: `Bearer ${getCookie("userToken")}` }
      : {}),
    ...headers,
  };

  // files have different content-type
  if (!mergedHeaders["Content-Type"]) {
    mergedHeaders["Content-Type"] = "application/json";
  }

  try {
    // reset names
    ContentDisposition = "";
    if (responseType || upload) onProgress = true;

    controller = new AbortController();
    const response = await axios.request({
      method,
      url,
      data,
      signal: controller.signal,
      ...(responseType ? { responseType } : {}),
      headers: mergedHeaders,

      onDownloadProgress: (progressEvent) => {
        if (responseType) startProgress(progressEvent);


      },
      onUploadProgress: (progressEvent) => {
        if (upload) startProgress(progressEvent);

      },
    });

    // Only execute the callback for successful responses (2xx status codes)
    if (response.status >= 200 && response.status < 300) {
      // Call the callback function with the response data
      if (responseType) {
        ContentDisposition = response.headers["content-disposition"];
      }
      if (callback && typeof callback === "function") callback(response.data);
    }

    return response.data;
  }
  catch (error) {
    console.error("AXIOS FULL ERROR:", error);
    console.log("STATUS:", error.response?.status);
    console.log("DATA:", error.response?.data);
    console.log("HEADERS:", error.response?.headers);

    if (errorCallback && typeof errorCallback === "function")
      errorCallback(error.response);

    handleError(error);
  }

  finally {
    if (responseType || upload) resetProgressVars();

    if (finallyCallback && typeof finallyCallback === "function")
      finallyCallback();
  }
}

const handleError = (error) => {
  // const router = useRouter();
  if (!error.response) return console.error("response is undefined");
  const data = error.response.data;
  const MSG =
    error.response.data.msg ||
    error.response.data.message ||
    error.msg ||
    error.message;

  if (data.errors !== undefined && typeof data.errors === "object") {
    // Case 1: Object of errors
    const errorValues = Object.values(data.errors);
    if (errorValues.length > 0) {
      const firstError = errorValues[0];
      if (Array.isArray(firstError) && firstError.length > 0) {
        const errorMessage = firstError.join(", "); // Join error messages into a single string
        const cleanMessage = errorMessage
          .replace(/\x1B\[[0-?]*[ -/]*[@-~]/g, "")
          .trim();
        Toastify({
          text: cleanMessage,
          style: {
            background:
              "linear-gradient(to right,rgb(255, 0, 0),rgb(231, 0, 0))",
          },
        }).showToast();
      }
    }
  } else if (data.errors !== undefined && Array.isArray(data.errors)) {
    // Case 2: Array of errors
    if (data.errors.length > 0) {
      const errorMessage = data.errors.join(", "); // Join error messages into a single string
      Toastify({
        text: errorMessage,
        style: {
          background: "linear-gradient(to right,rgb(255, 0, 0),rgb(231, 0, 0))",
        },
      }).showToast();
    } else
      Toastify({
        text: data.errors[0],
        style: {
          background: "linear-gradient(to right,rgb(255, 0, 0),rgb(231, 0, 0))",
        },
      }).showToast();
  } else if (MSG) {
    // Case 3: Single error message
    if (error.status == 401) {
      // if (router.currentRoute.meta.needAuth) {
      Toastify({
        text: "Token expired. Need to log in again.",
        style: {
          background: "linear-gradient(to right,rgb(255, 0, 0),rgb(231, 0, 0))",
        },
      }).showToast();
      // setTimeout(() => {
      window.location.href = "../views/login.html";
      // }, 2000);
      // return router.replace({ name: "Login" });
      // }
      return;
    }
    Toastify({
      text: MSG,
      style: {
        background: "linear-gradient(to right,rgb(255, 0, 0),rgb(231, 0, 0))",
      },
    }).showToast();
  } else {
    // Case 4: Unknown error format
    Toastify({
      text: "Error ! " + error.response,
      style: {
        background: "linear-gradient(to right,rgb(255, 0, 0),rgb(231, 0, 0))",
      },
    }).showToast();
  }
};
