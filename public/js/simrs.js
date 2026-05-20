/**
 * SIMRS - Main JavaScript
 * Hospital Management System Interactivity
 */

document.addEventListener('DOMContentLoaded', function () {

  // ── Sidebar Toggle ─────────────────────────────────────
  const sidebar     = document.getElementById('sidebar');
  const topbar      = document.getElementById('topbar');
  const mainContent = document.getElementById('mainContent');
  const toggleBtn   = document.getElementById('sidebarToggle');

  let collapsed = localStorage.getItem('sidebarCollapsed') === 'true';

  function applySidebar() {
    if (collapsed) {
      sidebar.classList.add('collapsed');
      topbar.classList.add('expanded');
      mainContent.classList.add('expanded');
    } else {
      sidebar.classList.remove('collapsed');
      topbar.classList.remove('expanded');
      mainContent.classList.remove('expanded');
    }
  }

  applySidebar();

  if (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      collapsed = !collapsed;
      localStorage.setItem('sidebarCollapsed', collapsed);
      applySidebar();
    });
  }

  // Mobile overlay toggle
  const mobileToggle = document.getElementById('mobileToggle');
  if (mobileToggle) {
    mobileToggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });
  }

  // ── Submenu Toggle ─────────────────────────────────────
  const navParents = document.querySelectorAll('.nav-link[data-submenu]');
  navParents.forEach(function (link) {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const targetId  = link.getAttribute('data-submenu');
      const submenu   = document.getElementById(targetId);
      const isOpen    = submenu.classList.contains('open');

      // Close all
      document.querySelectorAll('.nav-submenu.open').forEach(function (sm) {
        sm.classList.remove('open');
        sm.previousElementSibling && sm.previousElementSibling.classList.remove('open');
      });

      if (!isOpen) {
        submenu.classList.add('open');
        link.classList.add('open');
      }
    });
  });

  // Keep active submenu open on load
  const activeLink = document.querySelector('.nav-submenu .nav-link.active');
  if (activeLink) {
    const submenu = activeLink.closest('.nav-submenu');
    if (submenu) {
      submenu.classList.add('open');
      const parent = submenu.previousElementSibling;
      if (parent) parent.classList.add('open');
    }
  }

  // ── Live Clock ─────────────────────────────────────────
  const clockEl = document.getElementById('liveClock');
  const dateEl  = document.getElementById('liveDate');

  function updateClock() {
    const now = new Date();
    const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    if (clockEl) {
      clockEl.textContent = now.toLocaleTimeString('id-ID', {
        hour: '2-digit', minute: '2-digit', second: '2-digit'
      });
    }
    if (dateEl) {
      dateEl.textContent = days[now.getDay()] + ', ' +
        now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
    }
  }

  updateClock();
  setInterval(updateClock, 1000);

  // ── Tabs ───────────────────────────────────────────────
  const tabBtns = document.querySelectorAll('.tab-btn');
  tabBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const target = btn.getAttribute('data-tab');
      const parent = btn.closest('[data-tabs]');

      parent.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      parent.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

      btn.classList.add('active');
      const content = parent.querySelector('#tab-' + target);
      if (content) content.classList.add('active');
    });
  });

  // ── Modals ─────────────────────────────────────────────
  document.querySelectorAll('[data-modal-open]').forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      const id = trigger.getAttribute('data-modal-open');
      const modal = document.getElementById(id);
      if (modal) modal.classList.add('open');
    });
  });

  document.querySelectorAll('[data-modal-close], .modal-overlay').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (e.target === el) {
        document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
      }
    });
  });

  document.querySelectorAll('.modal-close').forEach(function (btn) {
    btn.addEventListener('click', function () {
      btn.closest('.modal-overlay').classList.remove('open');
    });
  });

  // ── Alert Dismiss ─────────────────────────────────────
  document.querySelectorAll('.alert-close').forEach(function (btn) {
    btn.addEventListener('click', function () {
      btn.closest('.alert').style.display = 'none';
    });
  });

  // ── Search Filter (Table) ─────────────────────────────
  const tableSearch = document.getElementById('tableSearch');
  if (tableSearch) {
    tableSearch.addEventListener('input', function () {
      const val = tableSearch.value.toLowerCase();
      const rows = document.querySelectorAll('[data-search-row]');
      rows.forEach(function (row) {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(val) ? '' : 'none';
      });
    });
  }

  // ── Tooltip on Collapsed Sidebar ──────────────────────
  // (simple title-based fallback - works natively)

  // ── Animate Cards on Load ─────────────────────────────
  const animatedCards = document.querySelectorAll('.stat-card, .card');
  animatedCards.forEach(function (card, i) {
    card.style.animationDelay = `${i * 0.04}s`;
    card.classList.add('fade-in');
  });

  // ── Bed Map Toggle ────────────────────────────────────
  document.querySelectorAll('.bed-card[data-toggle-info]').forEach(function (card) {
    card.addEventListener('click', function () {
      const info = document.getElementById(card.getAttribute('data-toggle-info'));
      if (info) info.classList.toggle('hidden');
    });
  });

  // ── Select All Checkbox ───────────────────────────────
  const selectAll = document.getElementById('selectAll');
  if (selectAll) {
    selectAll.addEventListener('change', function () {
      document.querySelectorAll('.row-check').forEach(cb => cb.checked = selectAll.checked);
    });
  }

});
