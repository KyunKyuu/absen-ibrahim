(() => {
    const button = document.getElementById('school-location-button');
    const status = document.getElementById('school-location-status');
    if (!button) return;
    button.addEventListener('click', () => {
        if (!window.isSecureContext || !navigator.geolocation) {
            status.textContent = 'Gunakan HTTPS dan browser yang mendukung lokasi, atau isi koordinat sekolah secara manual.';
            return;
        }
        button.disabled = true;
        status.textContent = 'Meminta lokasi. Izinkan lokasi presisi dan pastikan Anda berada di sekolah…';
        const failed = message => {
            button.disabled = false;
            status.textContent = message;
        };
        try {
            navigator.geolocation.getCurrentPosition(position => {
                const { latitude, longitude, accuracy } = position.coords;
                const limit = Number(document.getElementById('school-gps-accuracy').value);
                if (![latitude, longitude, accuracy, limit].every(Number.isFinite) || accuracy <= 0 || limit <= 0 || accuracy > limit) {
                    failed('Lokasi belum cukup akurat. Coba kembali di area terbuka atau isi koordinat sekolah secara manual.');
                    return;
                }
                if (document.hidden || Date.now() - position.timestamp > 15000) {
                    failed('Pembacaan sudah tidak baru. Tetap buka halaman dan ambil koordinat lagi.');
                    return;
                }
                document.getElementById('school-latitude').value = latitude.toFixed(7);
                document.getElementById('school-longitude').value = longitude.toFixed(7);
                button.disabled = false;
                status.textContent = `Koordinat terisi dengan galat ±${Math.ceil(accuracy)} m, belum disimpan. Pastikan ini titik sekolah lalu tekan Simpan pengaturan.`;
            }, () => failed('Lokasi gagal dibaca. Aktifkan GPS/lokasi presisi dan izin browser, lalu coba lagi.'),
            { enableHighAccuracy: true, maximumAge: 0, timeout: 20000 });
        } catch {
            failed('Browser menolak pembacaan lokasi. Periksa izin lokasi atau isi koordinat secara manual.');
        }
    });
})();
