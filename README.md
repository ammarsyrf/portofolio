# Portfolio Ammar Syarif — Web Developer & Data Analyst

Website portofolio profesional dan sistem manajemen konten (CMS) berbasis **PHP Native**, **MySQL PDO**, **Vanilla CSS & JS (Bento Dashboard Grid)**.

## 📂 Struktur Direktori Proyek
```
/
├── config.php                 # Konfigurasi database, konstanta path, kredensial Google OAuth
├── index.php                  # Halaman Beranda (Bento Grid Dashboard Hero, About, Skills, Projects, Contact)
├── README.md                  # Petunjuk utama proyek
├── admin/                     # Panel Admin terproteksi (Dashboard, Profile, Projects, Achievements, Creations, Links, Guestbook)
├── assets/                    # File statis (css/style.css, js/main.js, js/command_palette.js, uploads/)
├── auth/                      # Alur Google OAuth 2.0 (google_login.php, google_callback.php)
├── database/                  # Skema basis data SQL (schema.sql)
├── docs/                      # Dokumentasi & Spesifikasi Proyek:
│   ├── AGENTS.md              # Instruksi teknis, arsitektur, dan standar keamanan
│   ├── PRD.md                 # Product Requirement Document lengkap
│   ├── DESIGN.md              # Pedoman visual (Dark Glassmorphism & Token Desain)
│   └── CONTENT.md             # Konten asli seed data (profil, proyek, keahlian)
├── includes/                  # Komponen global (db.php, functions.php, nav.php, command_palette.php)
└── pages/                     # Halaman Publik Sekunder:
    ├── achievements.php       # Portofolio sertifikat & penghargaan
    ├── creations.php          # Galeri kurasi konten edukasi TikTok & Instagram
    ├── guestbook.php          # Buku tamu interaktif (Login Google terverifikasi)
    └── links.php              # Halaman bio links (gaya Linktree)
```

## 🚀 Menjalankan Secara Lokal
Jalankan server bawaan PHP dari root direktori:
```bash
php -S 127.0.0.1:8080
```
- **Halaman Utama**: `http://127.0.0.1:8080/index.php`
- **Panel Admin**: `http://127.0.0.1:8080/admin/login.php` (User: `ammar` / Pass: `AmmarSyarif2026!`)
- **Buku Tamu**: `http://127.0.0.1:8080/pages/guestbook.php`

## ⚙️ Google OAuth (Untuk Fitur Buku Tamu)
Kredensial dapat dimasukkan pada `config.php` (`GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET`). Panduan setup detail dapat dibaca di [`docs/AGENTS.md`](file:///c:/laravel10/porto/docs/AGENTS.md).
