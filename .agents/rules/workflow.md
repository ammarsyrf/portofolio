# Antigravity Workflow Rules

Setiap kali melakukan perubahan kode untuk proyek ini, WAJIB mengikuti siklus alur kerja berikut secara ketat:

1. **Edit Lokal First**: Seluruh perubahan kode harus dilakukan dan diselesaikan di lingkungan lokal terlebih dahulu. Jangan pernah langsung mengedit file di server produksi.
2. **Testing**: Lakukan pengujian di lokal secara menyeluruh (visual, fungsional, dan responsivitas) sebelum melakukan commit atau push.
3. **Push Git Commit**: Lakukan commit dengan pesan deskriptif dan push ke remote repository (`git push origin <branch>`).
4. **Deploy**: Deploy kode yang sudah teruji dan ter-push ke hosting server (`zen.zenerie.my.id`). Pastikan file konfigurasi produksi seperti `config.php` tidak tertimpa dan tetap aman.
