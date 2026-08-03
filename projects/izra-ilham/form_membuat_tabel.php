<style>
    .lat3-vertical-container { width: 100%; max-width: 700px; margin: 0 auto; margin-top: 10px; text-align: left; }
    
    /* Box Form Input (Berjejer ke Samping) */
    .lat3-box-form { border: 1px solid #cbd5e1; padding: 15px 20px; background-color: #fff; border-radius: 6px; box-sizing: border-box; margin-bottom: 20px; }
    .lat3-box-form h3 { margin-top: 0; margin-bottom: 15px; color: #1e293b; font-size: 15px; border-bottom: 2px solid #334155; padding-bottom: 5px; }
    
    /* Flexbox untuk membuat input berjejer ke samping */
    .lat3-form-row { display: flex; flex-wrap: wrap; align-items: center; gap: 20px; }
    .lat3-group { display: flex; align-items: center; gap: 8px; }
    .lat3-group label { font-size: 13px; font-weight: 600; color: #334155; white-space: nowrap; }
    .lat3-input { width: 100px; padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; }
    
    /* Grup Tombol */
    .lat3-btn-group { display: flex; gap: 8px; }
    .lat3-btn { padding: 6px 18px; cursor: pointer; font-weight: bold; background-color: #6366f1; color: white; border: none; border-radius: 4px; font-size: 13px; }
    .lat3-btn:hover { background-color: #4f46e5; }
    .lat3-btn-reset { padding: 6px 18px; cursor: pointer; font-weight: bold; background-color: #ef4444; color: white; border: none; border-radius: 4px; font-size: 13px; }
    .lat3-btn-reset:hover { background-color: #dc2626; }
    
    /* Box Hasil Tabel (Di Bagian Bawah) */
    .lat3-box-result { border: 1px solid #cbd5e1; padding: 20px; background-color: #fff; border-radius: 6px; box-sizing: border-box; display: none; }
    .lat3-box-result h3 { margin-top: 0; margin-bottom: 15px; color: #1e293b; font-size: 15px; }
    
    /* Style Tabel Output Dinamis */
    table.lat3-table-output { width: 100%; border-collapse: collapse; margin-top: 5px; background-color: #fff; }
    table.lat3-table-output td { border: 1px solid #64748b; padding: 8px 12px; text-align: left; font-size: 13px; color: #334155; }
</style>

<div class="lat3-vertical-container">
    <div class="lat3-box-form">
        <h3>Membuat Tabel</h3>
        <form id="tabelForm" onsubmit="generateTabel(event)">
            <div class="lat3-form-row">
                <div class="lat3-group">
                    <label for="jumlah_baris">Jumlah Baris :</label>
                    <input type="number" id="jumlah_baris" class="lat3-input" min="1" required placeholder="0">
                </div>
                
                <div class="lat3-group">
                    <label for="jumlah_kolom">Jumlah Kolom :</label>
                    <input type="number" id="jumlah_kolom" class="lat3-input" min="1" required placeholder="0">
                </div>
                
                <div class="lat3-btn-group">
                    <button type="submit" class="lat3-btn">Create</button>
                    <button type="button" class="lat3-btn-reset" onclick="resetFormTabel()">Reset</button>
                </div>
            </div>
        </form>
    </div>

    <div id="lat3ResultBox" class="lat3-box-result">
        <h3>Tabel Hasil :</h3>
        <div id="tabelOutputArea"></div>
    </div>
</div>

<script>
function generateTabel(event) {
    // Stop form reload biar ga kena error 404
    event.preventDefault();
    
    // Ambil input angka baris & kolom
    var baris = parseInt(document.getElementById('jumlah_baris').value);
    var kolom = parseInt(document.getElementById('jumlah_kolom').value);
    
    // Bikin struktur tabel lewat looping JavaScript
    var htmlTabel = "<table class='lat3-table-output'>";
    for (var i = 1; i <= baris; i++) {
        htmlTabel += "<tr>";
        for (var j = 1; j <= kolom; j++) {
            htmlTabel += "<td>baris " + i + ", kolom " + j + "</td>";
        }
        htmlTabel += "</tr>";
    }
    htmlTabel += "</table>";
    
    // Tampilkan tabel ke area output dan buka container hasil kotak bawah
    document.getElementById('tabelOutputArea').innerHTML = htmlTabel;
    document.getElementById('lat3ResultBox').style.display = 'block';
}

function resetFormTabel() {
    // Kosongkan kolom input form
    document.getElementById('jumlah_baris').value = '';
    document.getElementById('jumlah_kolom').value = '';
    
    // Bersihkan isi tabel dan sembunyikan kotak hasilnya kembali
    document.getElementById('tabelOutputArea').innerHTML = '';
    document.getElementById('lat3ResultBox').style.display = 'none';
}
</script>