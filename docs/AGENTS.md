# AGENTS.md — Instruksi untuk AI Coding Agent

## ⚡ Aturan Siklus Alur Kerja (Wajib Diikuti)
1. **Edit lokal first**: Kerjakan semua perubahan di lingkungan lokal.
2. **Testing**: Uji tampilan dan fungsionalitas di browser/lokal sebelum commit.
3. **Push git commit**: Commit dengan pesan rapi dan lakukan `git push origin <branch>`.
4. **Deploy**: Deploy ke server hosting (`zen.zenerie.my.id`). Jaga agar `config.php` produksi tidak tertimpa.

Baca file ini sebelum mulai coding. Baca juga `PRD.md` (requirement), `DESIGN.md` (arah visual), `CONTENT.md` (isi konten nyata), dan `database/schema.sql` (struktur DB) di folder yang sama.

## Tech Stack (wajib, jangan diganti)
- **Backend**: PHP native (tanpa framework — no Laravel/CodeIgniter/Composer package besar)
- **Database**: MySQL, akses via **PDO** dengan prepared statements
- **Frontend**: HTML + CSS murni + vanilla JavaScript (tanpa React/Vue/build tool, tanpa library animasi eksternal)
- **Auth pihak ketiga**: Google OAuth 2.0 (hanya untuk fitur Guestbook) — pakai HTTP request manual (`file_get_contents`/`curl`) ke endpoint Google, TIDAK perlu Google API PHP Client SDK yang berat
- Alasan: pemilik proyek mengelola sendiri sistemnya tanpa tim/dev lain, dan proyek lain miliknya (Villa Zein) pakai stack yang sama — konsistensi penting.

## ⚠️ Dependensi Eksternal yang Harus Disiapkan User Dulu
Sebelum fitur Guestbook bisa jalan, user (Ammar) harus:
1. Buka [Google Cloud Console](https://console.cloud.google.com/) → buat project baru
2. Aktifkan **Google Identity / OAuth consent screen**, isi info aplikasi dasar
3. Buat **OAuth Client ID** tipe "Web application"
4. Set **Authorized redirect URI** ke `https://domain-kamu.com/auth/google_callback.php` (sesuaikan domain)
5. Salin **Client ID** dan **Client Secret**, masukkan ke `config.php` sebagai konstanta (`GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`)

Kalau kredensial ini belum ada, agent tetap boleh membangun seluruh fitur lain — hanya tombol "Login with Google" di Guestbook yang menunggu kredensial ini diisi. Jangan hardcode client secret di kode; hanya di `config.php` yang tidak boleh diexpose publik (idealnya di luar webroot atau di-`.gitignore`).

## Struktur Folder yang Diharapkan
```
/
├── config.php                    # koneksi DB, konstanta path, kredensial Google OAuth
├── index.php                     # Home: Hero, About, Skills, Projects, Contact (single-page scroll)
├── achievements.php              # daftar sertifikat/penghargaan
├── creations.php                 # konten TikTok/Instagram (kurasi manual)
├── guestbook.php                 # buku tamu (perlu login Google untuk menulis)
├── links.php                     # halaman semua link penting
├── includes/
│   ├── db.php                     # PDO connection
│   ├── functions.php              # helper (upload file, sanitize, dsb)
│   ├── auth.php                   # cek session admin
│   ├── nav.php                    # navbar + tombol "Rekrut Saya" + trigger Command Palette, dipakai di semua halaman publik
│   └── command_palette.php        # markup overlay command palette (di-include di footer tiap halaman)
├── auth/
│   ├── google_login.php           # redirect ke Google OAuth consent screen
│   └── google_callback.php        # terima code dari Google, tukar token, simpan session visitor
├── assets/
│   ├── css/style.css
│   ├── js/
│   │   ├── main.js                 # scroll reveal, nav active state
│   │   └── command_palette.js      # logic ⌘K: buka/tutup overlay, filter, navigasi
│   └── uploads/
│       ├── photos/
│       ├── cv/
│       ├── projects/
│       ├── achievements/
│       └── creations/
├── admin/
│   ├── login.php
│   ├── logout.php
│   ├── setup.php                  # setup akun admin pertama kali, HAPUS setelah dipakai
│   ├── dashboard.php
│   ├── profile.php
│   ├── projects.php               # list + hapus
│   ├── project_form.php           # tambah/edit
│   ├── achievements.php           # list + hapus
│   ├── achievement_form.php       # tambah/edit
│   ├── creations.php              # list + hapus + toggle tampil/sembunyi section
│   ├── creation_form.php          # tambah/edit
│   ├── links.php                  # list + hapus
│   ├── link_form.php              # tambah/edit
│   ├── guestbook.php              # list pesan + hapus (moderasi)
│   └── includes/
│       ├── header.php
│       ├── sidebar.php
│       └── footer.php
└── database/
    └── schema.sql
```

## Aturan Koding
1. **Tidak ada query mentah dengan concatenation.** Selalu pakai PDO prepared statement (`->prepare()` + `->execute([...])`).
2. **Semua output ke HTML di-escape** dengan `htmlspecialchars()`, TERMASUK pesan guestbook (rawan XSS karena diisi publik).
3. **Password admin**: simpan dengan `password_hash($pass, PASSWORD_BCRYPT)`, verifikasi dengan `password_verify()`. Jangan pernah simpan plain text.
4. **Upload file**: validasi ekstensi + MIME type per jenis (CV: PDF; foto/gambar: jpg/png/webp), batasi ukuran, rename file saat disimpan (cegah path traversal).
5. **CSRF protection**: token per session, validasi di setiap form POST — termasuk form kirim pesan guestbook, bukan cuma form admin.
6. **Auth guard admin**: setiap file di `/admin/` (kecuali `login.php`, `setup.php`) wajib cek session admin di baris paling atas.
7. **Auth guard guestbook**: hanya endpoint "kirim pesan" yang butuh session visitor Google aktif; endpoint "lihat pesan" tetap publik.
8. **Google OAuth flow**:
   - `auth/google_login.php` generate `state` random, simpan di session, redirect ke `accounts.google.com/o/oauth2/v2/auth` dengan scope `openid email profile`
   - `auth/google_callback.php` validasi `state` cocok dengan session (cegah CSRF), tukar `code` jadi access token via POST ke `oauth2.googleapis.com/token`, ambil profil user via `openidconnect.googleapis.com/v1/userinfo`, simpan `google_id`, nama, avatar ke session visitor (`$_SESSION['guest_user']`, terpisah dari session admin)
   - Jangan simpan access token Google secara permanen di DB — cukup untuk sesi berjalan
9. **Session terpisah**: admin (`$_SESSION['admin_id']`) dan visitor Google (`$_SESSION['guest_user']`) tidak boleh saling menimpa.
10. **Setup admin pertama kali**: `admin/setup.php` hanya jalan kalau tabel `admin_users` kosong. **Instruksikan user menghapus file ini setelah dipakai.**
11. **Konten seed**: gunakan data asli dari `CONTENT.md`, bukan lorem ipsum.
12. **Desain**: ikuti `DESIGN.md` secara ketat — warna, tipografi, treatment kartu per jenis konten (project card ≠ achievement card ≠ link pill, lihat DESIGN.md).
13. **Responsive**: mobile-first, breakpoint minimal 375px, 768px, 1280px.
14. **Animasi scroll**: `IntersectionObserver` vanilla JS untuk reveal kartu (Projects di Home, grid di Achievements/Creations) dan highlight nav aktif. Tanpa library eksternal. Wajib cek `prefers-reduced-motion`.
15. **Command Palette**:
    - Trigger: `Cmd+K` (Mac) / `Ctrl+K` (Windows/Linux), dan juga tombol ikon di navbar untuk mobile (karena keyboard shortcut tidak natural di HP)
    - Daftar tujuan statis di JS (Home, About, Skills, Achievements, Projects, Creations, Guestbook, Links, Contact) — tidak perlu backend/search API
    - Filter live saat mengetik, navigasi dengan arrow key + Enter, `Esc` untuk tutup
    - Modal glassy sesuai `DESIGN.md`, dengan backdrop blur gelap di belakangnya

## Alur Kerja yang Disarankan untuk Agent
1. Import `database/schema.sql`, buat `config.php` (termasuk placeholder kredensial Google OAuth).
2. Bangun `includes/db.php`, `functions.php`, `auth.php` (fondasi).
3. Bangun `index.php` + `assets/css/style.css` sesuai `DESIGN.md` (Hero/About/Skills/Projects/Contact).
4. Bangun `includes/nav.php` (dipakai di semua halaman) + tombol Rekrut Saya.
5. Bangun `assets/js/main.js` (scroll reveal + nav active state).
6. Bangun `assets/js/command_palette.js` + `includes/command_palette.php`, sisipkan ke semua halaman.
7. Bangun `achievements.php`, `links.php` (lebih sederhana, tanpa auth).
8. Bangun `creations.php` (tampilkan data dari tabel `creations`, atau pesan "belum ada konten" kalau kosong/di-nonaktifkan admin).
9. Bangun alur Google OAuth (`auth/google_login.php`, `auth/google_callback.php`) + `guestbook.php`.
10. Bangun `admin/setup.php` → akun admin pertama.
11. Bangun `admin/login.php`, `logout.php`, `dashboard.php`.
12. Bangun seluruh CRUD admin: projects, achievements, creations, links, moderasi guestbook, edit profil.
13. Uji end-to-end: semua CRUD, upload file, Command Palette dari tiap halaman, alur login Google → kirim pesan guestbook → moderasi dari admin, responsive di mobile, animasi menghormati reduced-motion.

Kalau kredensial Google OAuth belum tersedia saat development, tetap bangun seluruh alurnya (jangan dilewati) — beri instruksi jelas di README bagian mana yang perlu diisi user sebelum fitur ini live.

## Jangan Lakukan
- Jangan tambah dependency berat (Composer packages besar, framework JS, Google API PHP Client SDK) tanpa diminta eksplisit — cukup HTTP request manual untuk OAuth.
- Jangan hardcode kredensial (DB, Google Client Secret) di banyak file — cukup satu `config.php`.
- Jangan buat auto-fetch API TikTok/Instagram — Creations pakai kurasi manual (lihat PRD.md §5.3).
- Jangan buat desain generik (lihat daftar "hindari" di `DESIGN.md`).
- Jangan expose pesan error PHP mentah ke publik (matikan `display_errors` di production, log ke file).
