// Client checks guide the student; the server repeats all trust-sensitive checks.
export function checkPosition(position, settings, currentTime = Date.now()) {
    const { latitude, longitude, accuracy } = position.coords;
    if (![latitude, longitude, accuracy, position.timestamp].every(Number.isFinite)
        || Math.abs(latitude) > 90 || Math.abs(longitude) > 180 || accuracy <= 0) {
        return { error: 'Pembacaan lokasi tidak valid. Tunggu pembacaan GPS berikutnya.' };
    }
    const age = currentTime - position.timestamp;
    if (age > 15000 || age < -10000) {
        return { error: 'Menunggu lokasi terbaru. Pastikan tanggal dan jam HP diatur otomatis.' };
    }
    if (accuracy > settings.accuracy) {
        return { error: `Galat GPS masih ±${Math.ceil(accuracy)} m; maksimal ±${settings.accuracy} m. Dekati area terbuka.` };
    }
    const radians = value => value * Math.PI / 180;
    const a = Math.sin(radians(latitude - settings.latitude) / 2) ** 2
        + Math.cos(radians(settings.latitude)) * Math.cos(radians(latitude))
        * Math.sin(radians(longitude - settings.longitude) / 2) ** 2;
    const distance = Math.round(6371000 * 2 * Math.asin(Math.sqrt(Math.min(1, Math.max(0, a)))));
    if (distance > settings.radius) {
        return { error: `Jarak Anda ${distance} m dari sekolah, di luar radius ${settings.radius} m.` };
    }
    if (distance + accuracy > settings.radius) {
        return { error: `Jarak ${distance} m dengan galat ±${Math.ceil(accuracy)} m masih melewati batas radius. Dekati titik absensi sekolah.` };
    }
    return { distance };
}

export function mountAttendance(form, browser = window, nav = navigator, doc = document) {
    const button = doc.getElementById('geo-button');
    const cancel = doc.getElementById('geo-cancel');
    const status = doc.getElementById('geo-status');
    const settings = Object.fromEntries(['latitude', 'longitude', 'radius', 'accuracy']
        .map(key => [key, Number(form.dataset[key])]));
    let watchId = null;
    let timer = null;
    let running = false;
    let submitting = false;
    let attempt = 0;

    const stop = () => {
        running = false;
        if (watchId !== null) nav.geolocation.clearWatch(watchId);
        if (timer !== null) browser.clearTimeout(timer);
        watchId = timer = null;
        cancel.hidden = true;
        form.setAttribute('aria-busy', 'false');
    };
    const fail = message => {
        stop();
        status.textContent = message;
        button.disabled = false;
        button.textContent = 'Coba periksa GPS lagi';
    };
    form.addEventListener('submit', event => {
        event.preventDefault();
        if (running || submitting || button.disabled) return;
        if (!browser.isSecureContext || !nav.geolocation) return;
        if (nav.onLine === false) return fail('Koneksi internet terputus. Sambungkan internet lalu coba lagi.');
        if (doc.hidden) return fail('Buka kembali halaman ini untuk memeriksa GPS.');

        running = true;
        const thisAttempt = ++attempt;
        button.disabled = true;
        button.textContent = 'Memeriksa GPS…';
        cancel.hidden = false;
        form.setAttribute('aria-busy', 'true');
        let lastMessage = 'Lokasi belum tersedia. Aktifkan GPS/lokasi presisi dan coba di area terbuka.';
        status.textContent = 'Izinkan akses lokasi. Menunggu GPS yang akurat (maksimal 30 detik)…';
        timer = browser.setTimeout(() => {
            if (running && thisAttempt === attempt) fail(lastMessage);
        }, 30000);

        try {
            watchId = nav.geolocation.watchPosition(position => {
                if (!running || thisAttempt !== attempt || doc.hidden) return;
                const result = checkPosition(position, settings);
                if (result.error) {
                    lastMessage = result.error;
                    status.textContent = lastMessage;
                    return;
                }
                if (nav.onLine === false) return fail('Koneksi internet terputus. Sambungkan internet lalu coba lagi.');
                stop();
                submitting = true;
                for (const name of ['latitude', 'longitude', 'accuracy']) form.elements.namedItem(name).value = position.coords[name];
                form.elements.namedItem('location_timestamp').value = position.timestamp;
                status.textContent = `GPS terbaca: jarak ${result.distance} m, galat ±${Math.ceil(position.coords.accuracy)} m. Mengirim absensi untuk diverifikasi…`;
                button.textContent = 'Menyimpan absensi…';
                browser.HTMLFormElement.prototype.submit.call(form);
            }, error => {
                if (!running || thisAttempt !== attempt) return;
                const messages = {
                    1: 'Izin lokasi ditolak. Aktifkan izin lokasi presisi untuk browser ini melalui pengaturan HP/browser.',
                    2: 'Lokasi tidak tersedia. Aktifkan GPS/lokasi HP, lalu coba di area terbuka.',
                    3: 'GPS belum merespons. Pastikan lokasi presisi aktif lalu coba lagi.',
                };
                fail(messages[error.code] ?? 'Gagal membaca lokasi. Coba kembali.');
            }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
        } catch {
            fail('Browser tidak mengizinkan pembacaan lokasi. Periksa izin lokasi lalu coba kembali.');
        }
    });
    cancel.addEventListener('click', () => fail('Pemeriksaan dibatalkan. Absensi belum dikirim.'));
    doc.addEventListener('visibilitychange', () => {
        if (doc.hidden && running) fail('Pemeriksaan dihentikan karena halaman ditinggalkan. Periksa GPS lagi.');
    });
    browser.addEventListener('pagehide', stop);
    browser.addEventListener('pageshow', event => {
        if (event.persisted) {
            submitting = false;
            fail('Halaman dibuka kembali. Periksa GPS lagi untuk mengambil lokasi terbaru.');
        }
    });

    if (!browser.isSecureContext) {
        status.textContent = 'Absensi membutuhkan HTTPS. Akses lewat alamat IP jaringan dengan HTTP tidak mendukung lokasi; hubungi admin.';
    } else if (!nav.geolocation) {
        status.textContent = 'Browser ini tidak mendukung lokasi. Gunakan browser HP yang mendukung lokasi.';
    } else {
        button.disabled = false;
        status.textContent = 'Siap memeriksa lokasi. Tekan tombol untuk meminta izin GPS dan melakukan absensi.';
    }
}

if (typeof document !== 'undefined') {
    const form = document.getElementById('attendance-form');
    if (form) mountAttendance(form);
}
