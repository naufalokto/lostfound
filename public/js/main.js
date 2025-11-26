document.addEventListener("DOMContentLoaded", () => {
  const yearEl = document.getElementById("year");
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  const menuToggle = document.getElementById("menuToggle");
  const siteNav = document.getElementById("siteNav");
  if (menuToggle && siteNav) {
    menuToggle.addEventListener("click", () => {
      const isOpen = siteNav.classList.toggle("is-open");
      menuToggle.setAttribute("aria-expanded", String(isOpen));
    });
  }

  const openers = document.querySelectorAll("[data-modal-open]");
  const closers = document.querySelectorAll("[data-modal-close]");

  function getModal(id) {
    return document.querySelector(`[data-modal="${id}"]`);
  }

  function openModal(id) {
    const modal = getModal(id);
    if (!modal) return;
    modal.classList.add("is-visible");
    modal.setAttribute("aria-hidden", "false");
  }

  function closeModal(id) {
    const modal = getModal(id);
    if (!modal) return;
    modal.classList.remove("is-visible");
    modal.setAttribute("aria-hidden", "true");
  }

  openers.forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = btn.getAttribute("data-modal-open");
      if (id) openModal(id);
    });
  });

  closers.forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = btn.getAttribute("data-modal-close");
      if (id) closeModal(id);
    });
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      document.querySelectorAll(".modal.is-visible").forEach((m) => {
        const id = m.getAttribute("data-modal");
        if (id) closeModal(id);
      });
    }
  });

  const reportForm = document.querySelector("[data-report-form]");
  if (reportForm) {
    const params = new URLSearchParams(window.location.search);
    const idParam = params.get("id");
    const typeParam = params.get("type");
    const idInput = document.getElementById("report_id");
    const typeInput = document.getElementById("report_type");
    if (idInput && idParam) idInput.value = idParam;
    if (typeInput && typeParam) typeInput.value = typeParam;
    const tabs = Array.from(document.querySelectorAll("[data-report-type]"));
    const lostGroups = Array.from(
      document.querySelectorAll('[data-group="lost"]')
    );
    const foundGroups = Array.from(
      document.querySelectorAll('[data-group="found"]')
    );
    const locationLabel = document.getElementById("locationLabel");
    const submitBtn = reportForm.querySelector('[data-submit="report"]');
    const vq = document.getElementById("verification_question");
    const va = document.getElementById("verification_answer");

    function setMode(mode) {
      tabs.forEach((t) => {
        const active = t.getAttribute("data-report-type") === mode;
        t.classList.toggle("tab--active", active);
        t.setAttribute("aria-selected", String(active));
      });

      const showLost = mode === "lost";
      lostGroups.forEach((el) => {
        el.style.display = showLost ? "" : "none";
      });
      foundGroups.forEach((el) => {
        el.style.display = showLost ? "none" : "";
      });

      if (locationLabel)
        locationLabel.textContent = showLost
          ? "Location Lost"
          : "Location Found";

      if (submitBtn) {
        submitBtn.textContent = showLost
          ? "Submit Lost Report"
          : "Submit Found Report";
        submitBtn.classList.toggle("btn--lost", showLost);
        submitBtn.classList.toggle("btn--found", !showLost);
      }

      if (vq) vq.required = !showLost;
      if (va) va.required = !showLost;
    }

    const urlMode = typeParam;
    setMode(urlMode === "found" ? "found" : "lost");

    tabs.forEach((t) => {
      t.addEventListener("click", () => {
        const mode = t.getAttribute("data-report-type");
        if (mode) setMode(mode);
      });
    });
  }

  const dashTabs = document.querySelectorAll('.tabs a[role="tab"]');
  if (dashTabs.length) {
    const sectionsMap = {};
    dashTabs.forEach((tab) => {
      const targetId = tab.getAttribute("href")?.replace("#", "");
      if (targetId) {
        const el = document.getElementById(targetId);
        if (el) sectionsMap[targetId] = el;
      }
    });

    function showSection(targetId) {
      Object.entries(sectionsMap).forEach(([id, el]) => {
        el.classList.toggle("is-hidden", id !== targetId);
      });
      dashTabs.forEach((tab) => {
        const id = tab.getAttribute("href")?.replace("#", "");
        const active = id === targetId;
        tab.classList.toggle("tab--active", active);
        tab.setAttribute("aria-selected", String(active));
      });
    }

    const firstTabTarget = dashTabs[0].getAttribute("href")?.replace("#", "");
    if (firstTabTarget) showSection(firstTabTarget);

    dashTabs.forEach((tab) => {
      tab.addEventListener("click", (e) => {
        e.preventDefault();
        const target = tab.getAttribute("href")?.replace("#", "");
        if (target) showSection(target);
      });
    });
  }

  const detailLost = document.querySelectorAll('[data-type-group="lost"]');
  const detailFound = document.querySelectorAll('[data-type-group="found"]');
  if (detailLost.length || detailFound.length) {
    const typeParam = new URLSearchParams(window.location.search).get("type");
    const isFound = typeParam === "found";
    detailLost.forEach((el) => {
      el.style.display = isFound ? "none" : "";
    });
    detailFound.forEach((el) => {
      el.style.display = isFound ? "" : "none";
    });
  }
});
