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
    // Remove preload class to enable transitions after initial state is applied
    setTimeout(() => {
        document.body.classList.remove('preload');
    }, 100);

    const sidebarNav = document.querySelector('.sidebar-nav');
    if (sidebarNav) {
        // Note: Initial restoration is now handled inline in app.blade.php 
        // to prevent the "blink" during page load.

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

// ═══════════ NOTIFICATIONS ═══════════
window.loadNotifications = function() {
  const syncBtn = document.getElementById('sync-btn');
  const syncIcon = syncBtn ? syncBtn.querySelector('i') : null;
  
  if (syncIcon) syncIcon.classList.add('spin');

  fetch('/notifications')
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('notif-items-container');
      const dot = document.getElementById('notif-dot');
      
      if (!container) return;

      if (data.unreadCount > 0) {
        if (dot) dot.style.display = 'block';
      } else {
        if (dot) dot.style.display = 'none';
      }

      if (data.notifications.length === 0) {
        container.innerHTML = '<div class="notif-item" style="text-align: center; padding: 20px;"><div class="notif-text">No notifications found.</div></div>';
        return;
      }

      container.innerHTML = data.notifications.map(n => {
        const title = n.data.title || 'Notification';
        const message = n.data.message || '';
        const icon = n.data.icon || 'uil-bell';
        const time = new Date(n.created_at).toLocaleString();
        const isRead = n.read_at !== null;

        return `
          <div class="notif-item ${isRead ? '' : 'unread'}" onclick="markAsRead('${n.id}', this)" style="${isRead ? '' : 'background: rgba(37,99,235,0.05); border-left: 3px solid var(--primary);'}">
            <div class="notif-text">
              <i class="uil ${icon} me-2" style="color: var(--primary)"></i>
              <strong>${title}:</strong> ${message}
            </div>
            <div class="notif-time">${time}</div>
          </div>
        `;
      }).join('');
    })
    .finally(() => {
      if (syncIcon) {
        setTimeout(() => syncIcon.classList.remove('spin'), 500);
      }
    });
}

window.markAsRead = function(id, el) {
  fetch(`/notifications/${id}/read`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Content-Type': 'application/json'
    }
  }).then(() => {
    if (el) {
      el.classList.remove('unread');
      el.style.background = 'transparent';
      el.style.borderLeft = 'none';
    }
    loadNotifications(); // Refresh count
  });
}

window.markAllAsRead = function() {
  fetch('/notifications/read-all', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Content-Type': 'application/json'
    }
  }).then(() => {
    loadNotifications();
    toast('All notifications marked as read', '<i class="uil uil-check"></i>');
  });
}

// ═══════════ SERVER SYNC ═══════════
window.syncWithServer = function() {
  if (!navigator.onLine) {
    toast('You are offline. Cannot sync.', '⚠️');
    return;
  }
  
  toast('Syncing with server...', '<i class="uil uil-sync spin"></i>');
  loadNotifications();
  // Add other refresh calls here if needed (e.g., refresh current page data)
}

window.updateOnlineStatus = function() {
  const syncBtn = document.getElementById('sync-btn');
  if (!syncBtn) return;
  
  if (navigator.onLine) {
    syncBtn.classList.remove('sync-offline');
    syncBtn.classList.add('sync-online');
    syncBtn.title = "Online - Click to sync";
  } else {
    syncBtn.classList.remove('sync-online');
    syncBtn.classList.add('sync-offline');
    syncBtn.title = "Offline - Connection lost";
  }
}

window.addEventListener('online', updateOnlineStatus);
window.addEventListener('offline', updateOnlineStatus);

document.addEventListener('DOMContentLoaded', () => {
  updateOnlineStatus();
  loadNotifications();
  // Poll for notifications every 30 seconds
  setInterval(loadNotifications, 30000);

  const markAllBtn = document.getElementById('mark-all-read');
  if (markAllBtn) {
    markAllBtn.addEventListener('click', markAllAsRead);
  }

  const logoutForm = document.getElementById('logout-form');
  if (logoutForm) {
    const idleMs = 5 * 60 * 1000;
    const warnMs = 60 * 1000;
    const storageKey = 'gptfms-last-activity';
    let lastActivity = Date.now();
    let isLoggingOut = false;
    let lastWrite = 0;
    const warningBar = document.getElementById('idle-warning');
    const warningCountdown = document.getElementById('idle-warning-countdown');
    const warningStayBtn = document.getElementById('idle-warning-stay');

    const writeActivity = () => {
      const now = Date.now();
      lastActivity = now;
      if (warningBar) {
        warningBar.style.display = 'none';
        warningBar.setAttribute('aria-hidden', 'true');
      }
      if (now - lastWrite > 1000) {
        lastWrite = now;
        try { localStorage.setItem(storageKey, String(now)); } catch (_) {}
      }
    };

    const doLogout = () => {
      if (isLoggingOut) return;
      isLoggingOut = true;
      try { logoutForm.submit(); } catch (_) { window.location.href = '/login'; }
    };

    const formatMmSs = (ms) => {
      const totalSeconds = Math.max(0, Math.floor(ms / 1000));
      const m = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
      const s = String(totalSeconds % 60).padStart(2, '0');
      return `${m}:${s}`;
    };

    const checkIdle = () => {
      if (isLoggingOut) return;
      let ts = lastActivity;
      try {
        const fromStorage = parseInt(localStorage.getItem(storageKey) || '', 10);
        if (!Number.isNaN(fromStorage)) ts = Math.max(ts, fromStorage);
      } catch (_) {}
      const elapsed = Date.now() - ts;
      const remaining = idleMs - elapsed;

      if (remaining <= 0) {
        doLogout();
        return;
      }

      if (warningBar) {
        if (remaining <= warnMs) {
          if (warningCountdown) warningCountdown.textContent = formatMmSs(remaining);
          if (warningBar.style.display !== 'block') {
            warningBar.style.display = 'block';
            warningBar.setAttribute('aria-hidden', 'false');
          }
        } else {
          if (warningBar.style.display !== 'none') {
            warningBar.style.display = 'none';
            warningBar.setAttribute('aria-hidden', 'true');
          }
        }
      }
    };

    ['mousemove','mousedown','keydown','scroll','touchstart','pointerdown'].forEach(evt => {
      window.addEventListener(evt, writeActivity, { passive: true });
    });
    document.addEventListener('visibilitychange', () => {
      if (!document.hidden) writeActivity();
    });
    window.addEventListener('storage', (e) => {
      if (e.key === storageKey && e.newValue) {
        const v = parseInt(e.newValue, 10);
        if (!Number.isNaN(v)) lastActivity = Math.max(lastActivity, v);
      }
    });

    if (warningStayBtn) {
      warningStayBtn.addEventListener('click', () => {
        writeActivity();
        toast('Session continued', '<i class="uil uil-check-circle"></i>');
      });
    }

    writeActivity();
    setInterval(checkIdle, 1000);
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

window.updateUserAvatar = function(avatarUrl, initials) {
  const cacheBustedUrl = avatarUrl ? `${avatarUrl}${avatarUrl.includes('?') ? '&' : '?'}v=${Date.now()}` : null;
  const targets = [
    document.getElementById('navbar-user-avatar'),
    document.getElementById('sidebar-footer-avatar'),
    document.getElementById('dropdown-user-avatar'),
    document.getElementById('profile-avatar-container'),
  ].filter(Boolean);

  targets.forEach(el => {
    if (cacheBustedUrl) {
      el.style.background = `url(${cacheBustedUrl}) center/cover`;
      el.innerHTML = '';
    } else {
      el.style.background = 'var(--primary)';
      el.textContent = (initials || '').toString().slice(0, 2).toUpperCase();
    }
  });
}

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
