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
    const tabs = Array.from(document.querySelectorAll("[data-report-type]"));
    const lostGroups = Array.from(
      document.querySelectorAll('[data-group="lost"]')
    );
    const foundGroups = Array.from(
      document.querySelectorAll('[data-group="found"]')
    );
    const locationLabel = document.getElementById("locationLabel");
    const submitBtn = reportForm.querySelector('[data-submit="report"]');

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
    }

    const urlMode = new URLSearchParams(window.location.search).get("type");
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
    const sections = {
      reportsSection: document.getElementById("reportsSection"),
      claimsSection: document.getElementById("claimsSection"),
      notificationsSection: document.getElementById("notificationsSection"),
      profileSection: document.getElementById("profileSection"),
    };

    function showSection(id) {
      Object.entries(sections).forEach(([key, el]) => {
        if (!el) return;
        const active = key === id;
        el.classList.toggle("is-hidden", !active);
      });

      dashTabs.forEach((tab) => {
        const target = tab.getAttribute("href")?.replace("#", "");
        const active = target === id;
        tab.classList.toggle("tab--active", active);
        tab.setAttribute("aria-selected", String(active));
      });
    }

    showSection("reportsSection");

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
