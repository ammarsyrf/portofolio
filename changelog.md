# Changelog

Semua perubahan dicatat berdasarkan commit Git. Format tanggal mengikuti waktu commit lokal.

## Unreleased

### Kontak dan media sosial

- Menghapus seluruh kanal kontak WhatsApp dari halaman publik, halaman tautan, pilihan link admin, dan data kontak yang tersimpan.
- Menambahkan URL Instagram dan TikTok yang dapat diatur melalui **Admin → Kelola Profil & Berkas**; tautannya tampil otomatis pada kartu kontak, halaman Tautan, dan structured data SEO.

### URL admin

- Menetapkan URL bersih untuk seluruh panel admin, termasuk `/admin/login`, serta redirect permanen dari URL lama yang memakai ekstensi `.php`.
- Menonaktifkan directory listing dan menambahkan header anti-cache untuk halaman utama/login agar cache lama tidak menampilkan halaman indeks server.

### Responsivitas tablet

- Menambahkan layout khusus tablet sentuh landscape: hero lebih ringkas, kartu konteks memakai grid adaptif, dan navigasi berubah menjadi menu ringkas agar tidak terasa seperti desktop yang dipersempit.

## 2026-09-20

### SEO, URL, dan navigasi

- Menambahkan fondasi SEO dan GEO: canonical URL, Open Graph, Twitter card, JSON-LD `Person`/`WebSite`/`WebPage`, H1 semantik, `robots.txt`, dan `sitemap.xml`.
- Menetapkan URL publik bersih (`/achievements`, `/creations`, `/guestbook`, `/links`) serta redirect dari URL lama yang memakai folder `pages` atau ekstensi `.php`.
- Menghapus hash section dari address bar setelah smooth scroll, sehingga `/\#hero` kembali menjadi `/`.

### Admin dan konten

- Menambahkan **Konten Halaman Utama** di admin untuk mengubah hero, statistik, teks tentang, keahlian, kontak, proyek, widget dashboard, dan kartu brand tanpa mengedit kode.
- Menambahkan pengelolaan kartu **Zenerie** dari admin: judul, badge, deskripsi, URL, serta label tombol.

### Desain dan responsivitas

- Mengganti kartu proyek unggulan menjadi kartu **Our Brand — Zenerie**, memakai logo, CTA menuju website brand, dan copy pengenalan brand.
- Menyetabilkan tinggi kartu media desktop agar tidak memanjang ketika Tech Stack berubah setelah refresh.
- Membuat carousel keahlian mobile menampilkan satu kartu penuh per layar.
- Merapikan hero desktop agar dashboard bento mengisi satu viewport dan tidak menyisakan ruang atas berlebih.
- Memperbarui floating sidebar: lebih ringkas, terpusat, ekspansi hover lebih halus, dan monogram **A//S** yang berubah menjadi **Ammar Syarif**.
- Menghapus teks pemberitahuan privasi email dari kartu kontak tanpa mengekspos alamat email.

### Privasi

- Menyembunyikan alamat email dan nomor WhatsApp dari tampilan publik, sementara tombol kontak tetap berfungsi.

## Referensi Commit

- `b39039c` — fix: preserve clean section URLs
- `878f401` — feat: add SEO and GEO foundations
- `5ef6544` — feat: use clean public URLs
- `a0251bb` — feat: manage dashboard widgets from admin
- `c9dcae8` — fix: clarify Zenerie brand introduction
- `de72a44` — feat: animate expandable sidebar brand
- `bfa1631` — fix: fit desktop hero into viewport
- `6692624` — feat: showcase Zenerie brand card
- `d219d09` — fix: show full mobile skill cards
- `4ff8827` — fix: center and compact floating sidebar
- `7676e9e` — fix: remove contact privacy notice
- `818d563` — fix: stabilize desktop media card height
- `0e85699` — feat: add editable homepage content
- `fd85f20` — feat: add expandable sidebar brand label
- `b382c6f` — fix: hide email address in bento contact card
- `0d169e0` — fix: move expanded sidebar clear of content
- `eb1fe17` — fix: refine contact card privacy and layout
- `e8d70a0` — fix: improve responsive bento media previews
- `12a9f01` — fix: enlarge mobile media carousel
- `4c09d76` — fix: smooth desktop sidebar expansion
