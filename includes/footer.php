        </div><!-- /.container-fluid -->

        
    </div><!-- /#content -->
</div><!-- /.d-flex -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="/bms/assets/js/app.js"></script>
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
