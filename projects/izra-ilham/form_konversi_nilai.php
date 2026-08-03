<style>
    .lat2-box { border: 1px solid #cbd5e1; padding: 20px; width: 100%; max-width: 400px; background-color: #fff; border-radius: 6px; box-sizing: border-box; text-align: left; margin: 0 auto; margin-top: 10px; }
    .lat2-box h3 { margin-top: 0; margin-bottom: 15px; color: #1e293b; border-bottom: 2px solid #334155; padding-bottom: 5px; font-size: 15px; }
    .lat2-group { margin-bottom: 15px; }
    .lat2-group label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 13px; color: #334155; }
    .lat2-input { width: 100%; padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; }
    .lat2-btn-group { display: flex; gap: 10px; }
    .lat2-btn { padding: 6px 15px; font-weight: bold; cursor: pointer; background-color: #10b981; color: white; border: none; border-radius: 4px; font-size: 13px; }
    .lat2-btn:hover { background-color: #059669; }
    .lat2-btn-reset { padding: 6px 15px; font-weight: bold; cursor: pointer; background-color: #ef4444; color: white; border: none; border-radius: 4px; font-size: 13px; }
    .lat2-btn-reset:hover { background-color: #dc2626; }
    .lat2-result { margin-top: 15px; padding: 12px; border-left: 4px solid #10b981; background-color: #f0fdf4; border-radius: 0 4px 4px 0; font-size: 13px; color: #1e293b; display: none; }
</style>

<div class="lat2-box">
    <h3>Konversi Nilai</h3>
    <form id="nilaiForm" onsubmit="hitungKonversi(event)">
        <div class="lat2-group">
            <label for="nilai_angka">Masukkan Nilai Angka (0 - 100):</label>
            <input type="number" id="nilai_angka" class="lat2-input" min="0" max="100" required placeholder="Contoh: 85">
        </div>
        <div class="lat2-btn-group">
            <button type="submit" class="lat2-btn">Konversi</button>
            <button type="button" class="lat2-btn-reset" onclick="resetFormNilai()">Reset</button>
        </div>
    </form>

    <div id="lat2ResultBox" class="lat2-result">
        Nilai Angka: <strong id="resAngka">0</strong><br>
        Hasil Konversi Huruf: <strong id="resHuruf" style="font-size: 16px; color: #dc2626;">-</strong>
    </div>
</div>

<script>
function hitungKonversi(event) {
    // Mencegah form melakukan reload/submit bawaan HTML agar tidak Not Found
    event.preventDefault();
    
    // Ambil nilai input
    var nilai = parseInt(document.getElementById('nilai_angka').value);
    var huruf = "";

    // Logika penentuan nilai huruf sesuai instruksi tugas
    if (nilai >= 85 && nilai <= 100) {
        huruf = "A";
    } else if (nilai >= 70 && nilai <= 84) {
        huruf = "B";
    } else if (nilai >= 60 && nilai < 70) {
        huruf = "C";
    } else if (nilai >= 50 && nilai < 60) {
        huruf = "D";
    } else {
        huruf = "E";
    }

    // Tampilkan data ke dalam kotak output hasil
    document.getElementById('resAngka').innerText = nilai;
    document.getElementById('resHuruf').innerText = huruf;
    document.getElementById('lat2ResultBox').style.display = 'block';
}

function resetFormNilai() {
    // Kosongkan kolom input angka
    document.getElementById('nilai_angka').value = '';
    
    // Sembunyikan dan reset teks kotak hasil output
    document.getElementById('lat2ResultBox').style.display = 'none';
    document.getElementById('resAngka').innerText = '0';
    document.getElementById('resHuruf').innerText = '-';
}
</script>