# DESIGN.md — Arah Visual Portfolio

## Konsep
**"Dark Dashboard / Glass Interface"** — struktur halaman rapi & profesional ala portofolio developer modern (referensi: satriabahari.my.id — section Home/About/Skills/Projects/Contact, scroll animation halus), dibalut nuansa gelap dan glassmorphism dengan aksen biru (referensi visual: kartu-kartu kaca melayang, tipografi bold, seperti tampilan OS/control center).

Referensi:
- **Struktur & interaksi**: https://www.satriabahari.my.id/ — navigasi bersih, badge skill, animasi scroll yang halus dan tidak berlebihan
- **Visual/mood**: gelap, glassy, layered widget cards, aksen biru — TANPA elemen dekoratif phone-UI/status-bar/control-center literal (itu cuma referensi mood, bukan komponen yang harus ditiru persis)

## Warna (base palette)
| Token | Hex / Value | Peran |
|---|---|---|
| `--color-bg` | `#0B0E14` | Latar utama — navy nyaris hitam |
| `--color-bg-alt` | `#12161F` | Latar panel/section alternatif |
| `--color-glass-bg` | `rgba(255,255,255,0.04)` | Isi kartu kaca (dipakai dengan `backdrop-filter: blur(16px)`) |
| `--color-glass-border` | `rgba(255,255,255,0.08)` | Border tipis kartu kaca |
| `--color-text` | `#E7EAF0` | Teks utama (putih kebiruan lembut) |
| `--color-text-dim` | `#8B93A7` | Teks sekunder/caption |
| `--color-accent` | `#4C8DFF` | Aksen biru utama — link, tombol, glow, highlight |
| `--color-accent-bright` | `#7AAFFF` | Hover/active state dari aksen |
| `--color-line` | `rgba(255,255,255,0.08)` | Garis pembatas antar section |

Glow effect: gunakan `box-shadow: 0 0 40px rgba(76,141,255,0.15)` secukupnya di belakang elemen kunci (foto hero, tombol CTA utama) — jangan ditaruh di semua elemen.

## Tipografi
- **Display/heading**: *Space Grotesk* — sans-serif geometris dengan karakter techy, dipakai untuk nama, judul section, judul proyek
- **Body/UI**: *Inter* — sans-serif netral & sangat legible, dipakai untuk paragraf, label, navigasi, badge
- Skala tipe jelas (rasio ~1.25–1.333), heading besar & tegas tapi tidak menaikkan satu kata saja jadi highlight warna berbeda
- Label section pakai sentence case (bukan ALL CAPS bertumpuk)

## Layout & Struktur Halaman
Single-page scroll, section-section berikut (urutan sesuai `PRD.md`):
1. **Nav** — sticky, minimal, logo/nama + link ke tiap section/halaman, highlight otomatis sesuai section aktif saat di-scroll, tombol **"Rekrut Saya"** dibuat menonjol (warna solid aksen biru, bukan outline seperti link lain) + ikon Command Palette di ujung kanan
2. **Hero** — nama besar (Space Grotesk), role/tagline, foto profil dalam frame kaca dengan glow biru tipis di belakang, tombol Download CV + kontak cepat
3. **About** — kartu kaca berisi paragraf about, layout dua kolom di desktop (teks + elemen visual kecil), satu kolom di mobile
4. **Skills** — badge/chip flat (bukan kartu kaca berat) dikelompokkan per kategori, mirip gaya badge di satriabahari
5. **Projects** — grid kartu kaca, MUNCUL BERTAHAP saat di-scroll (lihat bagian Animasi), tiap kartu: judul, kategori (tag warna aksen), ringkasan, tech stack chip kecil
6. **Contact** — kartu kaca dengan tombol-tombol kontak dan sosial (email, LinkedIn, GitHub, Instagram, TikTok)

Card treatment dibedakan sesuai fungsi (jangan semua kartu identik — hindari kesan "SaaS card kit"):
- Kartu hero/about: glass besar, border lebih terang, sedikit glow
- Skill badge: flat, tanpa glass, border tipis, radius kecil
- Kartu proyek: glass sedang, hover = border & glow biru menyala + sedikit terangkat (translateY kecil)
- Kartu achievement: glass sedang, ada elemen gambar sertifikat sebagai fokus utama (bukan teks dulu)
- Link pill (halaman Links): flat, lebar penuh, radius besar (pill-shape), ikon di kiri — beda treatment dari project card supaya halaman Links terasa ringan/cepat di-scan
- Bubble pesan Guestbook: glass tipis, avatar Google bulat kecil + nama + pesan, TANPA glow (biar tidak "berteriak" dibanding konten utama)
- Command Palette modal: glass paling pekat (blur tertinggi di seluruh site) karena mengambang di atas semua konten, border terang, muncul di tengah layar dengan sedikit scale-in saat dibuka

## Halaman Tambahan
- **Achievements**: grid 2-3 kolom (desktop) / 1 kolom (mobile), tiap kartu scroll-reveal sama seperti Projects
- **Creations**: grid kartu thumbnail video/post, badge kecil menandai platform (TikTok/Instagram) dengan warna berbeda tipis (bukan warna asli brand TikTok/IG yang mencolok — tetap dalam palet biru-netral situs)
- **Links**: layout vertikal terpusat (mirip Linktree), background tetap dark-glass, cocok dibuka dari HP
- **Guestbook**: form kirim pesan di atas (disabled/ganti jadi tombol "Login dengan Google" kalau belum login), list pesan di bawah, terbaru di atas
- **Command Palette**: dipicu ⌘K atau ikon di navbar, overlay penuh layar dengan modal di tengah berisi input pencarian + list hasil

## Animasi & Interaksi (inti requirement dari brief)
- **Scroll reveal Projects**: tiap kartu proyek fade-in + translateY(20px→0) saat masuk viewport, memakai `IntersectionObserver` (bukan library berat). Beri stagger delay kecil antar kartu (misal 80–120ms) supaya terasa "muncul satu-satu", bukan serentak.
- **Nav active state**: highlight menu sesuai section yang sedang terlihat (pakai `IntersectionObserver` juga di tiap section).
- **Hero**: satu momen animasi saat load — fade+slide halus untuk nama & tagline (jangan diulang di section lain, cukup di hero).
- **Hover kartu proyek**: transisi halus border-color & box-shadow (glow biru), bukan efek generik shadow abu-abu.
- **Wajib**: hormati `prefers-reduced-motion` — kalau user set reduce motion, animasi scroll reveal diganti langsung tampil tanpa transisi.
- Implementasi vanilla JS (`assets/js/main.js`), tanpa GSAP/Framer Motion/library animasi eksternal — cukup CSS transition/keyframes + IntersectionObserver.

## Yang Harus Dihindari
- Elemen dekoratif literal ala UI HP (status bar, control center, ikon baterai/wifi) — itu di luar konteks web portofolio developer, jangan ditiru persis dari gambar referensi
- Kartu seragam rounded-corner + shadow abu-abu generik di semua tempat (SaaS card kit)
- ALL CAPS label bertaburan, meta text dengan titik tengah ("A · B · C"), tanda panah "→" di akhir tombol
- Animasi fade-slide-up yang diulang di SETIAP section — cukup di Hero (load) dan Projects (scroll)

## Prinsip Kunci
1. Struktur & UX mengikuti kerapian referensi satriabahari.my.id — jangan korbankan clarity demi gaya.
2. Mood gelap-glassy-biru jadi identitas visual utama, dieksekusi lewat warna & material (glass, glow), bukan lewat elemen dekoratif phone-UI.
3. Animasi scroll adalah salah satu fitur yang paling terlihat — pastikan halus, ringan, dan dihormati `prefers-reduced-motion`.
4. Responsive mobile-first, kontras cukup untuk aksesibilitas, fokus keyboard terlihat jelas di atas latar gelap.
