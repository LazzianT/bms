/* BMS Global UI helpers */
var BMS = {
    icons: {
        success: 'bi-check-circle-fill',
        error:   'bi-x-circle-fill',
        info:    'bi-info-circle-fill',
        warning: 'bi-exclamation-triangle-fill'
    },
    titles: {
        success: 'Berhasil',
        error:   'Gagal',
        info:    'Info',
        warning: 'Perhatian'
    },
    toast: function(message, type) {
        type = type || 'info';
        var container = document.getElementById('toastContainer');
        if (!container) return;
        var el = document.createElement('div');
        el.className = 'bms-toast bms-' + type;
        el.innerHTML = '<div class="toast-icon"><i class="bi ' + (this.icons[type] || this.icons.info) + '"></i></div>' +
            '<div><div class="toast-title">' + (this.titles[type] || 'Info') + '</div>' +
            '<p class="toast-msg"></p></div>';
        el.querySelector('.toast-msg').textContent = message || '';
        el.addEventListener('click', function() { BMS._removeToast(el); });
        container.appendChild(el);
        setTimeout(function() { BMS._removeToast(el); }, 4200);
        return el;
    },
    _removeToast: function(el) {
        if (!el || el.classList.contains('toast-out')) return;
        el.classList.add('toast-out');
        setTimeout(function() { el && el.remove(); }, 320);
    },
    success: function(msg) { return this.toast(msg, 'success'); },
    error: function(msg)   { return this.toast(msg, 'error'); },
    info: function(msg)    { return this.toast(msg, 'info'); },
    warning: function(msg) { return this.toast(msg, 'warning'); },
    confirm: function(message) {
        var modalEl = document.getElementById('bmsConfirmModal');
        return new Promise(function(resolve) {
            if (!modalEl) { resolve(window.confirm(message || '')); return; }
            document.getElementById('bmsConfirmText').textContent = message || 'Yakin melanjutkan aksi ini?';
            var ok = document.getElementById('bmsConfirmOk');
            ok.onclick = null;
            var onHidden = function() {
                modalEl.removeEventListener('hidden.bs.modal', onHidden);
                resolve(false);
            };
            modalEl.addEventListener('hidden.bs.modal', onHidden);
            ok.onclick = function() {
                modalEl.removeEventListener('hidden.bs.modal', onHidden);
                resolve(true);
            };
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        });
    }
};

$(document).ready(function() {
    // Sidebar toggle
    $('#sidebarToggle').on('click', function() {
        $('#sidebar').toggleClass('show');
    });

    // Auto-hide sidebar on mobile after click
    if ($(window).width() < 768) {
        $('.nav-link').on('click', function() {
            if (!$(this).attr('data-bs-toggle')) {
                $('#sidebar').removeClass('show');
            }
        });
    }
});
