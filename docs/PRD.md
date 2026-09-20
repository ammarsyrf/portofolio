# PRD — Portfolio Website Ammar Syarif

## 1. Latar Belakang & Tujuan
Website portofolio pribadi untuk Ammar Syarif, lulusan S1 Sistem Informasi, digunakan untuk melamar kerja di posisi yang lebih tinggi dari level fresh graduate (mid-level web developer / data analyst). Website harus menampilkan seluruh proyek yang pernah dikerjakan secara profesional, meyakinkan, dan mudah diperbarui sendiri tanpa harus mengedit kode. Struktur mengambil inspirasi dari satriabahari.my.id (nav lengkap, Achievements, Links, Guestbook, Command Palette) dipadukan dengan gaya visual dark-glassmorphism (lihat `DESIGN.md`).

## 2. Target Pengguna
- **Primary**: HR / hiring manager / recruiter yang mengevaluasi kandidat.
- **Secondary**: Ammar sendiri, sebagai admin yang mengelola konten.
- **Tertiary**: pengunjung umum yang ingin meninggalkan pesan di Guestbook.

## 3. Peran Pengguna (User Roles)
| Role | Akses |
|---|---|
| Visitor (publik) | Melihat seluruh halaman, download CV, klik kontak/sosial media |
| Visitor Login (Google) | Bisa menulis pesan di Guestbook |
| Admin (Ammar) | Login ke `/admin`, CRUD penuh atas semua konten di bawah, termasuk moderasi Guestbook |

## 4. Peta Situs (Site Map)
- `index.php` — Home (single-page scroll: Hero, About, Skills, Projects, Contact)
- `achievements.php` — daftar sertifikat/penghargaan
- `creations.php` — konten TikTok/Instagram (kurasi manual lewat admin)
- `guestbook.php` — buku tamu (butuh login Google untuk menulis pesan)
- `links.php` — halaman semua link penting (gaya Linktree)
- Nav global tampil di semua halaman + tombol **"Rekrut Saya"** yang menonjol (mengarah ke `index.php#contact`) + **Command Palette (⌘K)** untuk lompat cepat ke halaman/section manapun

## 5. Fitur — Halaman Publik

### 5.1 Home (`index.php`)
1. **Hero** — nama, role title, tagline, foto profil, tombol Download CV, tombol kontak cepat
2. **About** — paragraf latar belakang, pendidikan, fokus keahlian
3. **Skills** — daftar tech stack & kompetensi, dikelompokkan per kategori (mis. Frontend, Backend, Database, Tools)
4. **Projects** — grid seluruh proyek (7 proyek awal, lihat `CONTENT.md`), klik → detail (deskripsi, tech stack, peran, hasil, link demo/repo)
5. **Contact** — email, LinkedIn, GitHub, Instagram, dan TikTok

### 5.2 Achievements (`achievements.php`)
- Grid sertifikat/penghargaan: judul, penerbit/penyelenggara, tanggal, gambar sertifikat, link verifikasi (kalau ada)
- Kosong dulu saat awal — diisi Ammar lewat admin (lihat `CONTENT.md` untuk TODO)

### 5.3 Creations (`creations.php`)
- Menampilkan konten TikTok/Instagram milik Ammar (kalau ada)
- **Model kurasi manual** (bukan auto-fetch API): admin menambahkan tiap post secara manual lewat admin panel (judul, platform, thumbnail, link post). Ini disengaja — API resmi TikTok/Instagram butuh approval developer account yang berat untuk kebutuhan portofolio pribadi. Kalau ke depan mau upgrade ke auto-fetch, tinggal ganti sumber data di halaman ini tanpa ubah struktur DB.
- Kalau Ammar belum aktif bikin konten TikTok/IG, section ini boleh kosong / disembunyikan sementara (lihat catatan admin: opsi "tampilkan section ini" on/off)

### 5.4 Guestbook (`guestbook.php`)
- Visitor harus login dengan **Google OAuth** dulu sebelum bisa menulis pesan
- Setelah login: form kirim pesan singkat (maks 500 karakter), tampil nama & foto profil Google
- List pesan terbaru ditampilkan ke semua visitor (tanpa perlu login untuk *membaca*)
- Admin bisa hapus/moderasi pesan yang tidak pantas dari admin panel

### 5.5 Links (`links.php`)
- Halaman sederhana berisi daftar tombol link penting (gaya Linktree): CV, LinkedIn, GitHub, Email, Instagram, TikTok, dan link custom lain yang bisa ditambah admin
- Cocok untuk ditaruh di bio Instagram/LinkedIn

### 5.6 Navigasi Global & Command Palette
- **Nav bar**: Beranda, Tentang, Keahlian, Proyek (anchor ke section di Home) + Pencapaian, Kreasi, Buku Tamu, Tautan (halaman terpisah) + tombol **Rekrut Saya**
- **Command Palette (⌘K / Ctrl+K)**: overlay pencarian cepat, ketik untuk filter tujuan (Home, About, Skills, Achievements, Projects, Creations, Guestbook, Links, Contact), Enter untuk lompat ke sana. Client-side saja, tanpa backend.

## 6. Fitur — Admin Panel (`/admin`)
1. **Login** — autentikasi username + password (hashed, `password_hash`/`password_verify`)
2. **Dashboard** — ringkasan (jumlah proyek, achievements, creations, pesan guestbook menunggu moderasi)
3. **Kelola Profil** — nama, role title, tagline, about, skills, foto, CV, link kontak
4. **Kelola Proyek (CRUD)** — sama seperti sebelumnya (judul, kategori, ringkasan, deskripsi, tech stack, peran, hasil, gambar, demo/repo link, urutan, status publish)
5. **Kelola Achievements (CRUD)** — judul, penerbit, tanggal, deskripsi, gambar sertifikat, link verifikasi, urutan, status publish
6. **Kelola Creations (CRUD)** — platform (TikTok/Instagram), judul, thumbnail, link post, tanggal, urutan, status publish; toggle tampilkan/sembunyikan section Creations di publik
7. **Kelola Links (CRUD)** — label, url, ikon, urutan, status aktif
8. **Moderasi Guestbook** — lihat & hapus pesan
9. **Ganti Password Admin**

## 7. Non-Functional Requirements
- **Stack**: PHP native (no framework) + MySQL (PDO) + vanilla JS — konsisten dengan proyek lain milik Ammar
- **Dependensi eksternal**: Google OAuth 2.0 Client ID & Secret (untuk fitur Guestbook) — wajib dibuat sendiri di Google Cloud Console, lihat panduan setup di `AGENTS.md`
- **Security**:
  - Semua query pakai prepared statements (PDO)
  - Password admin di-hash dengan `password_hash()` (bcrypt)
  - Validasi & sanitasi setiap upload file (tipe, ukuran)
  - CSRF token di setiap form admin & form guestbook
  - Session admin dan session visitor Google terpisah namespace-nya
  - OAuth flow pakai parameter `state` untuk cegah CSRF
  - Semua pesan guestbook di-escape (`htmlspecialchars`) sebelum ditampilkan — cegah XSS
- **Responsive**: mobile-first
- **Performance**: gambar dioptimasi/di-resize saat upload
- **Aksesibilitas**: kontras cukup, fokus keyboard terlihat, alt text, animasi menghormati `prefers-reduced-motion`
- **Animasi**: scroll-reveal di section Projects (Home) dan grid Achievements/Creations, pakai IntersectionObserver vanilla JS — detail di `DESIGN.md`

## 8. Kriteria Sukses
- Semua konten (profil, proyek, achievements, creations, links) bisa diedit tanpa menyentuh kode
- Guestbook berfungsi end-to-end: login Google → kirim pesan → tampil di publik → bisa dihapus admin
- Command Palette bisa membawa user ke halaman manapun dalam 1-2 ketikan
- Desain terasa unik/berkarakter (lihat `DESIGN.md`), bukan template generik
- Tidak ada data sensitif (password, client secret) tersimpan dalam bentuk plain text di kode publik

## 9. Di Luar Cakupan (Out of Scope) — untuk versi pertama
- Multi-bahasa (ID/EN)
- Auto-fetch API TikTok/Instagram resmi (pakai kurasi manual dulu — lihat 5.3)
- Blog/artikel
- Multi-admin/role permission
- Login provider selain Google (Facebook/GitHub login, dsb.)
