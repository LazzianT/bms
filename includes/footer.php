        </div><!-- /.container-fluid -->

        <footer class="app-footer">
            <div class="footer-inner">
                <div class="footer-left">
                    <span class="footer-brand"><i class="bi bi-tools"></i> BMS</span>
                    <span class="footer-sep">|</span>
                    <span>&copy; <?php echo date('Y'); ?> Bengkel Management System</span>
                </div>
                <div class="footer-right">v2026.1</div>
            </div>
        </footer>
    </div><!-- /#content -->
</div><!-- /.d-flex -->

<!-- Toast container -->
<div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer"></div>

<!-- Confirm modal -->
<div class="modal fade" id="bmsConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered confirm-modal">
        <div class="modal-content">
            <div class="modal-body">
                <div class="confirm-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <div class="confirm-title">Konfirmasi</div>
                    <p class="confirm-text" id="bmsConfirmText">Yakin melanjutkan aksi ini?</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="bmsConfirmOk"><i class="bi bi-check-lg me-1"></i> Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<script>
// Currency input formatter
document.querySelectorAll('input[type="number"]').forEach(function(el) {
    if (el.id && (el.id.match(/harga|bayar|total/i) || el.name && el.name.match(/harga|bayar/i))) {
        el.setAttribute('type', 'text');
        el.setAttribute('inputmode', 'numeric');
        el.classList.add('currency-input');
        // Format existing value
        if (el.value && el.value > 0) {
            el.dataset.raw = el.value;
            el.value = formatCurrency(el.value);
        }
        el.addEventListener('focus', function() {
            this.value = this.dataset.raw || this.value.replace(/[^\d]/g, '');
        });
        el.addEventListener('blur', function() {
            var raw = this.value.replace(/[^\d]/g, '');
            this.dataset.raw = raw;
            this.value = raw ? formatCurrency(raw) : '';
        });
        el.addEventListener('input', function() {
            this.dataset.raw = this.value.replace(/[^\d]/g, '');
        });
        // Override form submit to send raw value
        var form = el.closest('form');
        if (form && !form.dataset.currencyBound) {
            form.dataset.currencyBound = '1';
            form.addEventListener('submit', function() {
                this.querySelectorAll('.currency-input').forEach(function(inp) {
                    inp.value = inp.dataset.raw || inp.value.replace(/[^\d]/g, '');
                });
            });
        }
    }
});
function formatCurrency(val) {
    var n = parseInt(val) || 0;
    return 'Rp ' + n.toLocaleString('id-ID');
}
</script>
</body>
</html>
