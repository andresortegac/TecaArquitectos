document.addEventListener("DOMContentLoaded", () => {
  const body = document.body;
  const sidebar = document.getElementById("main-sidebar");
  const menuButton = document.querySelector(".mobile-menu-toggle");
  const overlay = document.querySelector(".sidebar-overlay");
  const moneyNamePattern = /(costo|valor|monto|pago|tarifa|precio|saldo|total|salario|sueldo|abono|importe|anticipo|deuda|recaudo|ingreso|egreso|gasto|utilidad)/i;

  const parseMoneyValue = (value) => {
    let raw = String(value ?? "")
      .replace(/\s/g, "")
      .replace(/[$]/g, "")
      .replace(/[^0-9,.-]/g, "");

    if (!raw || raw === "-" || raw === "," || raw === ".") return 0;

    const isNegative = raw.startsWith("-");
    raw = raw.replace(/-/g, "");

    const lastComma = raw.lastIndexOf(",");
    const lastDot = raw.lastIndexOf(".");
    let normalized = raw;

    if (lastComma > -1 && lastDot > -1) {
      const decimalSep = lastComma > lastDot ? "," : ".";
      const thousandsSep = decimalSep === "," ? "." : ",";
      normalized = raw.split(thousandsSep).join("").replace(decimalSep, ".");
    } else if (lastComma > -1) {
      const decimals = raw.length - lastComma - 1;
      normalized = decimals > 0 && decimals <= 2
        ? raw.replace(/\./g, "").replace(",", ".")
        : raw.replace(/,/g, "");
    } else if (lastDot > -1) {
      const decimals = raw.length - lastDot - 1;
      normalized = decimals > 0 && decimals <= 2
        ? raw.replace(/,/g, "")
        : raw.replace(/\./g, "");
    }

    const number = Number(normalized);
    return Number.isFinite(number) ? (isNegative ? -number : number) : 0;
  };

  const formatMoneyNumber = (number) => {
    const normalized = Number(number || 0);
    return normalized.toLocaleString("es-CO", {
      minimumFractionDigits: 0,
      maximumFractionDigits: 2,
    });
  };

  const formatMoneyInput = (input) => {
    if (!input) return;
    const value = parseMoneyValue(input.value);
    input.value = input.value === "" ? "" : formatMoneyNumber(value);
    input.dataset.moneyRawValue = String(value);
  };

  const formatMoneyInputLive = (input) => {
    if (!input) return;

    const previousValue = input.value;
    const previousCursor = input.selectionStart || previousValue.length;
    const digitsBeforeCursor = previousValue
      .slice(0, previousCursor)
      .replace(/\D/g, "")
      .length;

    let clean = previousValue.replace(/[^0-9,]/g, "");
    if (!clean) {
      input.value = "";
      input.dataset.moneyRawValue = "0";
      return;
    }

    const hasDecimalComma = clean.includes(",");
    const parts = clean.split(",");
    const integerPart = parts.shift().replace(/^0+(?=\d)/, "") || "0";
    const decimalPart = parts.join("").slice(0, 2);
    const formattedInteger = Number(integerPart).toLocaleString("es-CO");
    const formattedValue = hasDecimalComma
      ? `${formattedInteger},${decimalPart}`
      : formattedInteger;

    input.value = formattedValue;
    input.dataset.moneyRawValue = String(parseMoneyValue(formattedValue));

    let nextCursor = formattedValue.length;
    if (digitsBeforeCursor < clean.replace(/\D/g, "").length) {
      let seenDigits = 0;
      for (let i = 0; i < formattedValue.length; i += 1) {
        if (/\d/.test(formattedValue[i])) seenDigits += 1;
        if (seenDigits >= digitsBeforeCursor) {
          nextCursor = i + 1;
          break;
        }
      }
    }

    input.setSelectionRange(nextCursor, nextCursor);
  };

  const cleanMoneyFields = (root = document) => {
    root.querySelectorAll("input[data-money-input='true']").forEach((input) => {
      const value = parseMoneyValue(input.value);
      input.value = value === 0 && input.value.trim() === "" ? "" : String(value);
    });
  };

  const shouldFormatMoneyInput = (input) => {
    if (!input || input.tagName !== "INPUT") return false;
    if (!input.name && input.dataset.moneyInput !== "true") return false;
    if (input.type === "hidden" || input.dataset.moneyInput === "false") return false;
    return input.dataset.moneyInput === "true" || moneyNamePattern.test(input.name);
  };

  const initMoneyInput = (input) => {
    if (!shouldFormatMoneyInput(input) || input.dataset.moneyReady === "true") return;

    input.dataset.moneyInput = "true";
    input.dataset.moneyReady = "true";
    input.inputMode = "decimal";
    input.autocomplete = "off";

    if (input.type === "number") {
      input.type = "text";
    }

    input.classList.add("money-input");

    if (!input.closest(".money-input-wrap")) {
      const wrapper = document.createElement("span");
      wrapper.className = "money-input-wrap";
      const prefix = document.createElement("span");
      prefix.className = "money-input-prefix";
      prefix.textContent = "$";

      input.parentNode.insertBefore(wrapper, input);
      wrapper.appendChild(prefix);
      wrapper.appendChild(input);
    }

    formatMoneyInput(input);

    input.addEventListener("focus", () => {
      input.select();
    });

    input.addEventListener("input", () => {
      formatMoneyInputLive(input);
    });

    input.addEventListener("blur", () => {
      formatMoneyInput(input);
    });
  };

  const initMoneyInputs = (root = document) => {
    if (root.matches?.("input")) {
      initMoneyInput(root);
    }

    root.querySelectorAll?.("input[name], input[data-money-input='true']").forEach(initMoneyInput);
  };

  window.parseMoneyValue = parseMoneyValue;
  window.formatMoneyInput = formatMoneyInput;
  window.cleanMoneyFields = cleanMoneyFields;
  window.initMoneyInputs = initMoneyInputs;

  // Menu lateral movil: abre/cierra con transicion y bloquea toques del fondo.
  const closeSidebar = () => {
    body.classList.remove("sidebar-open");
    menuButton?.setAttribute("aria-expanded", "false");
  };

  const openSidebar = () => {
    body.classList.add("sidebar-open");
    menuButton?.setAttribute("aria-expanded", "true");
  };

  menuButton?.addEventListener("click", () => {
    body.classList.contains("sidebar-open") ? closeSidebar() : openSidebar();
  });

  overlay?.addEventListener("click", closeSidebar);

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") closeSidebar();
  });

  sidebar?.querySelectorAll("a[href]:not([href='javascript:void(0)'])").forEach((link) => {
    link.addEventListener("click", () => {
      if (window.matchMedia("(max-width: 767px)").matches) closeSidebar();
    });
  });

  window.addEventListener("resize", () => {
    if (window.matchMedia("(min-width: 768px)").matches) closeSidebar();
  });

  const flashIcons = {
    success: "success",
    error: "error",
    warning: "warning",
    info: "info",
    status: "info",
  };

  const flashTitles = {
    success: "Operacion realizada",
    error: "No se pudo completar",
    warning: "Atencion",
    info: "Informacion",
    status: "Informacion",
  };

  if (window.Swal && window.AppFlash) {
    const flash = Object.entries(window.AppFlash).find(([, message]) => message);

    if (flash) {
      const [type, message] = flash;
      window.Swal.fire({
        icon: flashIcons[type] || "info",
        title: flashTitles[type] || "Informacion",
        text: message,
        confirmButtonText: "Aceptar",
        confirmButtonColor: "#0f172a",
      });
    }
  }

  document.addEventListener("click", async (event) => {
    const trigger = event.target.closest("[data-confirm-action]");
    if (!trigger || trigger.dataset.confirmed === "true") return;

    event.preventDefault();
    event.stopPropagation();

    const title = trigger.dataset.confirmTitle || "Confirmar accion";
    const text = trigger.dataset.confirmText || "Esta accion no se puede deshacer.";
    const confirmButtonText = trigger.dataset.confirmButton || "Si, confirmar";
    const cancelButtonText = trigger.dataset.cancelButton || "Cancelar";
    const icon = trigger.dataset.confirmIcon || "warning";

    let confirmed = false;

    if (window.Swal) {
      const result = await window.Swal.fire({
        icon,
        title,
        text,
        showCancelButton: true,
        confirmButtonText,
        cancelButtonText,
        confirmButtonColor: trigger.dataset.confirmColor || "#dc2626",
        cancelButtonColor: "#64748b",
        reverseButtons: true,
      });

      confirmed = result.isConfirmed;
    } else {
      confirmed = window.confirm(text);
    }

    if (!confirmed) return;

    trigger.dataset.confirmed = "true";

    if (trigger.tagName === "A" && trigger.href) {
      window.location.href = trigger.href;
      return;
    }

    const form = trigger.closest("form");
    if (form) {
      cleanMoneyFields(form);
      form.submit();
      return;
    }

    trigger.click();
  });

  initMoneyInputs();

  const moneyObserver = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (node.nodeType === Node.ELEMENT_NODE) {
          initMoneyInputs(node);
        }
      });
    });
  });

  moneyObserver.observe(document.body, {
    childList: true,
    subtree: true,
  });

  document.addEventListener("submit", (event) => {
    cleanMoneyFields(event.target);
  }, true);
});
