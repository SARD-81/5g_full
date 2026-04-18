export const auth_name = {
  value: null,
};

export function setAuthName(value) {
  let timeout;
  const timeoutMillis = 30 * 60 * 1000;
  const warningMillis = 10 * 60 * 1000;

  function logout() {
    fetch("{{ route('logout') }}", {
      method: "POST",
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    }).then(() => window.location.href = "/login");
  }

  function showWarning() {
    const warning = document.createElement("div");
    warning.innerText = "You are leaving panell about 10 min";
    warning.style.position = "fixed";
    warning.style.bottom = "20px";
    warning.style.right = "20px";
    warning.style.background = "#0a3468";
    warning.style.color = "white";
    warning.style.padding = "10px 20px";
    warning.style.borderRadius = "5px";
    warning.style.zIndex = "9999";

    document.body.appendChild(warning);

    setTimeout(() => {
      warning.remove();
    }, 5000);
  }

  function resetTimer() {
    clearTimeout(timeout);
    timeout = setTimeout(logout, timeoutMillis);

    clearTimeout(window.warningTimer);
    if (timeoutMillis > warningMillis) {
      window.warningTimer = setTimeout(
        showWarning,
        timeoutMillis - warningMillis
      );
    }
  }

  document.addEventListener("mousemove", resetTimer);
  document.addEventListener("keypress", resetTimer);
  document.addEventListener("click", resetTimer);
  document.addEventListener("scroll", resetTimer);

  resetTimer();

  auth_name.value = value;
}
