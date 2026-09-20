# CONTENT.md — Konten Nyata untuk Seed Database

Gunakan data ini sebagai isi awal (bukan lorem ipsum). Semua field ini nantinya bisa diedit lagi lewat admin panel — jadi kalau ada yang belum akurat, tidak masalah, tinggal diedit setelah web jadi.

## Profil
- **Nama lengkap**: Ammar Syarif
- **Role/Jabatan**: Web Developer & Data Analyst
- **Pendidikan**: S1 Sistem Informasi
- **Tagline**: Membangun sistem yang rapi dan mengubah data jadi keputusan.
- **About**: Lulusan S1 Sistem Informasi dengan pengalaman membangun berbagai sistem bisnis dari nol — mulai dari platform booking, sistem akademik, hingga riset klasifikasi data. Terbiasa mengerjakan proyek end-to-end: dari desain database, backend, sampai tampilan yang siap dipakai klien.
- **Skills**: PHP Native, MySQL, JavaScript, HTML/CSS, Sistem Informasi
- **Email**: zentokun90@gmail.com *(asumsi — konfirmasi domain email yang benar)*
- **LinkedIn**: https://linkedin.com/in/zentokun90 *(asumsi — konfirmasi username persis)*
- **GitHub**: https://github.com/zentokun90 *(asumsi — konfirmasi username persis)*
- **Instagram**: *(isi URL profil aktif lewat admin)*
- **TikTok**: *(isi URL profil aktif lewat admin)*
- **Foto profil**: *(perlu diunggah manual lewat admin)*
- **CV (PDF)**: *(perlu diunggah manual lewat admin)*

> Catatan: username "zentokun90" diasumsikan sama persis untuk email/LinkedIn/GitHub. Tolong dicek ulang & dikoreksi kalau beda.

## Proyek

### 1. Villa Zein
- **Kategori**: Web Platform
- **Ringkasan**: Company profile & booking platform untuk 5 villa sekaligus
- **Deskripsi**: Platform company profile multi-villa dengan fitur booking dan pengecekan kalender ketersediaan real-time. Setiap villa (Villa Zein 1–5) punya halaman sendiri dengan price list, fasilitas, dan peraturan. Dilengkapi konsultasi via WhatsApp, invoice otomatis, testimoni foto/video, alur pembayaran DP + pelunasan dengan verifikasi bukti transfer manual, dan fitur reschedule booking. Semua konten (tema warna, sosial media, harga) bisa diatur dari admin panel.
- **Tech stack**: PHP Native, MySQL, JavaScript
- **Peran**: Full-stack Developer (solo project)

### 2. Sistem LMS Asafiyah 02
- **Kategori**: Web Application
- **Ringkasan**: Sistem manajemen pembelajaran untuk institusi pendidikan
- **Deskripsi**: *(lengkapi detail: fitur kelas, materi, penilaian, jumlah pengguna, dsb.)*
- **Tech stack**: PHP Native, MySQL
- **Peran**: Developer

### 3. Big Cargo
- **Kategori**: Web Application
- **Ringkasan**: Sistem manajemen pengiriman dan logistik kargo
- **Deskripsi**: *(lengkapi detail: alur tracking, jenis laporan, integrasi, dsb.)*
- **Tech stack**: PHP Native, MySQL
- **Peran**: Developer

### 4. Big Aviation
- **Kategori**: Web Application
- **Ringkasan**: Sistem manajemen operasional di bidang aviasi
- **Deskripsi**: *(lengkapi detail spesifik modul/fitur)*
- **Tech stack**: PHP Native, MySQL
- **Peran**: Developer

### 5. GoHaji Umroh
- **Kategori**: Web Platform
- **Ringkasan**: Platform pendaftaran dan manajemen jamaah umroh/haji
- **Deskripsi**: *(lengkapi detail: alur pendaftaran, paket, manifest jamaah, dsb.)*
- **Tech stack**: PHP Native, MySQL
- **Peran**: Full-stack Developer

### 6. Zenerie
- **Kategori**: Web Project
- **Ringkasan**: *(lengkapi — proyek personal/brand apa?)*
- **Deskripsi**: *(lengkapi)*
- **Tech stack**: PHP Native, MySQL
- **Peran**: Developer

### 7. Pustaka48
- **Kategori**: Web Application
- **Ringkasan**: Sistem manajemen perpustakaan digital
- **Deskripsi**: *(lengkapi detail: fitur peminjaman, katalog, denda, dsb.)*
- **Tech stack**: PHP Native, MySQL
- **Peran**: Developer

---

## Links (halaman `/links`)
Diisi otomatis dari data profil di atas, plus link tambahan berikut:
1. **Download CV** → file CV yang diunggah admin
2. **Email** → mailto:zentokun90@gmail.com *(asumsi, cek ulang)*
3. **LinkedIn** → https://linkedin.com/in/zentokun90 *(asumsi, cek ulang)*
4. **GitHub** → https://github.com/zentokun90 *(asumsi, cek ulang)*
5. **Instagram** → *(isi URL profil aktif lewat admin)*
6. **TikTok** → *(isi URL profil aktif lewat admin)*

Admin bisa menambah link lain lewat panel (misal: Instagram, TikTok, portofolio lama, dsb.) — tinggal tambah lewat CRUD Links.

## Achievements (sertifikat/penghargaan)
*(kosong — belum ada data)*. Sebelum go-live, siapkan daftar sertifikat/penghargaan yang mau ditampilkan: judul, penerbit, tanggal, gambar sertifikat, link verifikasi (kalau ada). Contoh yang biasa relevan untuk lulusan Sistem Informasi: sertifikat pelatihan/bootcamp, sertifikasi cloud/database, penghargaan lomba/organisasi kampus, publikasi jurnal (kalau mau ditampilkan sebagai achievement, bukan section riset terpisah).

## Creations (konten TikTok/Instagram)
*(kosong — belum ada data)*. Section ini opsional — kalau Ammar belum aktif bikin konten TikTok/Instagram yang relevan untuk portofolio profesional, section ini bisa disembunyikan dulu lewat toggle di admin. Kalau mau diisi, siapkan: judul singkat, platform, thumbnail, dan link ke post aslinya untuk tiap konten.

## Guestbook
Tidak perlu konten awal — halaman ini terisi otomatis dari pesan pengunjung yang login dengan Google. Pastikan kredensial Google OAuth (lihat `AGENTS.md`) sudah disiapkan sebelum go-live.

---

## TODO sebelum go-live
- [ ] Konfirmasi domain email & username LinkedIn/GitHub yang benar
- [ ] Isi URL Instagram dan TikTok aktif (jika ingin ditampilkan)
- [ ] Lengkapi deskripsi detail untuk: LMS Asafiyah 02, Big Cargo, Big Aviation, GoHaji Umroh, Zenerie, Pustaka48
- [ ] Siapkan foto profil (disarankan foto formal/semi-formal, latar polos)
- [ ] Siapkan file CV dalam format PDF
- [ ] Siapkan screenshot/gambar tiap proyek untuk kartu proyek
- [ ] Siapkan daftar sertifikat/penghargaan untuk Achievements (kalau ada)
- [ ] Putuskan apakah section Creations mau diisi atau disembunyikan dulu
- [ ] Buat Google OAuth Client ID & Secret untuk fitur Guestbook (lihat panduan di `AGENTS.md`)
