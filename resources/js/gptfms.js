// ═══════════ NAVIGATION ═══════════
window.navigate = function(page, el) {
  // Note: This function was for SPA-style navigation in the original HTML.
  // In Laravel, we use actual routes, but we keep this for compatibility if needed.
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  const pageEl = document.getElementById('page-' + page);
  if (pageEl) {
    pageEl.classList.add('active');
  }
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  if (el) el.classList.add('active');
  else {
    const found = document.querySelector(`[onclick*="'${page}'"]`);
    if (found) found.classList.add('active');
  }
  const names = { dashboard:'Dashboard', groups:'Groups', projects:'Projects', tasks:'Tasks', messages:'Messages', reports:'Analytics', evaluation:'Peer Evaluation', supervisor:'Supervisor', admin:'Admin Panel', settings:'Settings' };
  const breadcrumb = document.getElementById('page-breadcrumb');
  if (breadcrumb) {
    breadcrumb.textContent = names[page] || page;
  }
  closeAllDropdowns();
}

// ═══════════ SIDEBAR ═══════════
window.toggleSidebar = function() {
  const sidebar = document.getElementById('sidebar');
  if (sidebar) {
    sidebar.classList.toggle('collapsed');
    localStorage.setItem('gptfms-sidebar-collapsed', sidebar.classList.contains('collapsed'));
  }
}

// Preserve Sidebar Scroll Position
document.addEventListener('DOMContentLoaded', () => {
    const sidebarNav = document.querySelector('.sidebar-nav');
    if (sidebarNav) {
        // Restore scroll position
        const savedScrollPos = localStorage.getItem('gptfms-sidebar-scroll');
        if (savedScrollPos) {
            sidebarNav.scrollTop = parseInt(savedScrollPos, 10);
        }

        // Save scroll position on scroll (debounced)
        let scrollTimeout;
        sidebarNav.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                localStorage.setItem('gptfms-sidebar-scroll', sidebarNav.scrollTop);
            }, 100);
        });

        // Save scroll position when a link is clicked
        sidebarNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                localStorage.setItem('gptfms-sidebar-scroll', sidebarNav.scrollTop);
            });
        });
    }
});

// ═══════════ TOAST ═══════════
window.toast = function(msg, icon='ℹ️') {
  const container = document.getElementById('toast-container');
  if (!container) return;
  const el = document.createElement('div');
  el.className = 'toast';
  el.innerHTML = `<span class="toast-icon">${icon}</span><span>${msg}</span>`;
  container.appendChild(el);
  setTimeout(() => {
    el.classList.add('hide');
    setTimeout(() => el.remove(), 350);
  }, 3000);
}

// ═══════════ MODAL ═══════════
window.openModal = function(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.add('open');
  }
}
window.closeModal = function(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.remove('open');
  }
}

document.addEventListener('click', e => {
  if (e.target.classList.contains('modal-overlay')) e.target.classList.remove('open');
});

// ═══════════ DROPDOWN ═══════════
window.toggleDropdown = function(id) {
  const dropdown = document.getElementById(id);
  if (dropdown) {
    // Check if it's already open
    const isOpen = dropdown.classList.contains('open');
    
    // Close other dropdowns first
    document.querySelectorAll('.dropdown').forEach(d => {
        d.classList.remove('open');
    });
    
    // Toggle the target dropdown
    if (!isOpen) {
        dropdown.classList.add('open');
    }
  }
}
window.closeAllDropdowns = function() {
  document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('open'));
}
document.addEventListener('click', e => {
  if (!e.target.closest('.navbar-btn') && !e.target.closest('.navbar-avatar') && !e.target.closest('.dropdown')) {
    closeAllDropdowns();
  }
});

// ═══════════ SETTINGS ═══════════
window.switchSettings = function(section, el) {
  document.querySelectorAll('.settings-section').forEach(s => {
    s.classList.remove('active');
    s.style.display = 'none';
  });
  const sectionEl = document.getElementById('settings-' + section);
  if (sectionEl) {
    sectionEl.classList.add('active');
    sectionEl.style.display = 'block';
  }
  document.querySelectorAll('.settings-nav-item').forEach(n => n.classList.remove('active'));
  if (el) el.classList.add('active');
}

// ═══════════ THEME ═══════════
window.setTheme = function(t, quiet = false) {
  document.documentElement.setAttribute('data-theme', t);
  localStorage.setItem('gptfms-theme', t);
  if (!quiet) toast('Theme set to ' + t + ' mode', t === 'dark' ? '<i class=\'uil uil-moon\'></i>' : '<i class=\'uil uil-sun\'></i>');
}

// ═══════════ SIDEBAR COLOR ═══════════
window.setSidebarColor = function(bg, text, el, quiet = false) {
  document.documentElement.style.setProperty('--sidebar-bg', bg);
  document.documentElement.style.setProperty('--sidebar-text', text);
  localStorage.setItem('gptfms-sidebar-bg', bg);
  localStorage.setItem('gptfms-sidebar-text', text);
  document.querySelectorAll('#sidebar-swatches .swatch').forEach(s => s.classList.remove('selected'));
  if (el) el.classList.add('selected');
  if (!quiet) toast('Sidebar color updated!', '<i class=\'uil uil-palette\'></i>');
}

// ═══════════ HEADER COLOR ═══════════
window.setHeaderColor = function(bg, text, el, quiet = false) {
  document.documentElement.style.setProperty('--header-bg', bg);
  document.documentElement.style.setProperty('--header-text', text);
  localStorage.setItem('gptfms-header-bg', bg);
  localStorage.setItem('gptfms-header-text', text);
  const navbar = document.getElementById('navbar');
  if (navbar) {
    navbar.style.background = bg;
  }
  document.querySelectorAll('#header-swatches .swatch').forEach(s => s.classList.remove('selected'));
  if (el) el.classList.add('selected');
  if (!quiet) toast('Header color updated!', '<i class=\'uil uil-palette\'></i>');
}

// ═══════════ ACCENT COLOR ═══════════
window.setAccentColor = function(color, light, el, quiet = false) {
  document.documentElement.style.setProperty('--primary', color);
  document.documentElement.style.setProperty('--primary-light', light);
  document.documentElement.style.setProperty('--sidebar-active', color);
  localStorage.setItem('gptfms-accent-color', color);
  localStorage.setItem('gptfms-accent-light', light);
  document.querySelectorAll('#accent-swatches .swatch').forEach(s => s.classList.remove('selected'));
  if (el) el.classList.add('selected');
  if (!quiet) toast('Accent color updated!', '<i class=\'uil uil-palette\'></i>');
}

// ═══════════ TRANSITION SPEED ═══════════
window.setTransitionSpeed = function(ms, quiet = false) {
  document.documentElement.style.setProperty('--transition', `${ms}ms cubic-bezier(.4,0,.2,1)`);
  localStorage.setItem('gptfms-transition-speed', ms);
}

// ═══════════ CHAT ═══════════
window.sendMessage = function(e) {
  if (e.key === 'Enter') sendMsgBtn();
}
window.sendMsgBtn = function() {
  const input = document.getElementById('chat-input-field');
  if (!input) return;
  const text = input.value.trim();
  if (!text) return;
  const msgs = document.querySelector('.chat-messages');
  if (!msgs) return;
  const msg = document.createElement('div');
  msg.className = 'msg me';
  msg.innerHTML = `<div class="msg-bubble">${text}</div><div class="msg-time">You · just now</div>`;
  msgs.appendChild(msg);
  msgs.scrollTop = msgs.scrollHeight;
  input.value = '';
  // Simulate reply
  setTimeout(() => {
    const reply = document.createElement('div');
    reply.className = 'msg them';
    const replies = ['Got it! 👍', 'Thanks for the update!', 'On it!', 'Nice work! 🎉', 'Let\'s review this together.'];
    reply.innerHTML = `<div class="msg-bubble">${replies[Math.floor(Math.random()*replies.length)]}</div><div class="msg-time">Aisha · just now</div>`;
    msgs.appendChild(reply);
    msgs.scrollTop = msgs.scrollHeight;
  }, 1200);
}

// ═══════════ INITIALIZATION ═══════════
function initSettings() {
  const sidebarCollapsed = localStorage.getItem('gptfms-sidebar-collapsed') === 'true' || window.__SIDEBAR_COLLAPSED__;
  const sidebar = document.getElementById('sidebar');
  if (sidebar && sidebarCollapsed) sidebar.classList.add('collapsed');

  // Re-apply header background specifically to the navbar element if it exists
  const headerBg = localStorage.getItem('gptfms-header-bg');
  const navbar = document.getElementById('navbar');
  if (navbar && headerBg) {
    navbar.style.background = headerBg;
  }

  // Highlight selected swatches in settings page if they exist
  if (window.location.pathname.includes('/settings')) {
    const sidebarBg = localStorage.getItem('gptfms-sidebar-bg');
    const headerBg = localStorage.getItem('gptfms-header-bg');
    const accentColor = localStorage.getItem('gptfms-accent-color');

    if (sidebarBg) {
        const s = document.querySelector(`#sidebar-swatches .swatch[onclick*="'${sidebarBg}'"]`);
        if (s) s.classList.add('selected');
    }
    if (headerBg) {
        const s = document.querySelector(`#header-swatches .swatch[onclick*="'${headerBg}'"]`);
        if (s) s.classList.add('selected');
    }
    if (accentColor) {
        const s = document.querySelector(`#accent-swatches .swatch[onclick*="'${accentColor}'"]`);
        if (s) s.classList.add('selected');
    }
  }
}

// Welcome toast
document.addEventListener('DOMContentLoaded', () => {
    initSettings();
    setTimeout(() => {
        if (window.location.pathname === '/' || window.location.pathname.includes('/dashboard')) {
            toast('Welcome back! 3 tasks need attention.', '<i class="uil uil-wave-hand"></i>');
        }
    }, 800);
});
