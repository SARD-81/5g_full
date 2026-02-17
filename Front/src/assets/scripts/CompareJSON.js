import { jsonData } from "./settingServer";

// شیء برای ذخیره تغییرات
let changes = {};

// شمارشگر تغییرات
let changeCount = 0;

// عنصر HTML برای نمایش تعداد تغییرات
const changeCounterDisplay = document.getElementById("changeCounter"); // فرض کنید این عنصر در HTML وجود دارد

// تابع کمکی برای دسترسی به مقدار اولیه از روی مسیر
function getInitialValue(path, jsonData) {
  return path.split(".").reduce((obj, key) => obj && obj[key], jsonData);
}

// به‌روزرسانی نمایش تعداد تغییرات
function updateChangeCount() {
  changeCount = Object.keys(changes).length;
  changeCounterDisplay.textContent = `تعداد تغییرات: ${changeCount}`;
}

// بررسی تغییرات و ثبت در شیء تغییرات
function trackChange(path, newValue) {
  const initialValue = getInitialValue(path, jsonData);

  if (newValue !== initialValue) {
    // اگر مقدار جدید با مقدار اولیه متفاوت بود و قبلا ثبت نشده بود
    if (!changes.hasOwnProperty(path)) {
      changes[path] = newValue;
      updateChangeCount();
    } else {
      changes[path] = newValue; // مقدار جدید را به‌روزرسانی می‌کنیم
    }
  } else {
    // اگر مقدار دوباره به مقدار اولیه برگردد
    if (changes.hasOwnProperty(path)) {
      delete changes[path];
      updateChangeCount();
    }
  }
}

// گوش دادن به تغییرات در اینپوت‌ها
document.querySelectorAll("input").forEach((input) => {
  const path = input.getAttribute("jsonData-path"); // فرض بر اینکه هر اینپوت یک jsonData-path دارد که مسیر را ذخیره می‌کند
  input.value = getInitialValue(path, jsonData); // مقدار اولیه را از داده‌ها می‌گیرد

  input.addEventListener("input", (event) => {
    const newValue = event.target.value;
    trackChange(path, newValue);
  });
});

// تابعی برای نمایش آرایه نهایی تغییرات
function getChanges() {
  return Object.keys(changes).map((key) => ({ [key]: changes[key] }));
}

// برای مثال نمایش تغییرات
// console.log(getChanges());
