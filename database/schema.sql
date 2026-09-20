-- =====================================================
-- Portfolio Database Schema — Ammar Syarif
-- Updated: Multi-page (Achievements, Creations, Guestbook, Links, Settings)
-- =====================================================

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS profile (
    id INT PRIMARY KEY DEFAULT 1,
    full_name VARCHAR(150) NOT NULL,
    role_title VARCHAR(200) DEFAULT '',
    tagline VARCHAR(255) DEFAULT '',
    about TEXT,
    photo VARCHAR(255) DEFAULT '',
    cv_file VARCHAR(255) DEFAULT '',
    email VARCHAR(150) DEFAULT '',
    linkedin VARCHAR(255) DEFAULT '',
    github VARCHAR(255) DEFAULT '',
    instagram VARCHAR(255) DEFAULT '',
    tiktok VARCHAR(255) DEFAULT '',
    skills TEXT COMMENT 'comma-separated list',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(100) DEFAULT '',
    summary VARCHAR(255) DEFAULT '' COMMENT 'short one-liner for card view',
    description TEXT,
    tech_stack VARCHAR(255) DEFAULT '',
    my_role VARCHAR(200) DEFAULT '',
    result_impact TEXT,
    image VARCHAR(255) DEFAULT '',
    demo_link VARCHAR(255) DEFAULT '',
    repo_link VARCHAR(255) DEFAULT '',
    sort_order INT DEFAULT 0,
    is_published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    issuer VARCHAR(150) NOT NULL,
    issue_date VARCHAR(50) DEFAULT '',
    description TEXT,
    image VARCHAR(255) DEFAULT '',
    verify_link VARCHAR(255) DEFAULT '',
    sort_order INT DEFAULT 0,
    is_published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS creations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform ENUM('tiktok', 'instagram') NOT NULL DEFAULT 'tiktok',
    title VARCHAR(255) NOT NULL,
    thumbnail VARCHAR(255) DEFAULT '',
    post_link VARCHAR(255) NOT NULL,
    created_date VARCHAR(50) DEFAULT '',
    sort_order INT DEFAULT 0,
    is_published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS guestbook (
    id INT AUTO_INCREMENT PRIMARY KEY,
    google_id VARCHAR(100) NOT NULL,
    user_name VARCHAR(150) NOT NULL,
    user_avatar VARCHAR(255) DEFAULT '',
    user_email VARCHAR(150) DEFAULT '',
    message VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(50) DEFAULT 'link',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Seed: profile
-- =====================================================
INSERT INTO profile (id, full_name, role_title, tagline, about, email, linkedin, github, instagram, tiktok, skills)
VALUES (
    1,
    'Ammar Syarif',
    'Web Developer & Data Analyst',
    'Membangun sistem yang rapi dan mengubah data jadi keputusan.',
    'Lulusan S1 Sistem Informasi dengan pengalaman membangun berbagai sistem bisnis dari nol — mulai dari platform booking, sistem akademik, hingga riset klasifikasi data. Terbiasa mengerjakan proyek end-to-end: dari desain database, backend, sampai tampilan yang siap dipakai klien.',
    'zentokun90@gmail.com',
    'https://linkedin.com/in/zentokun90',
    'https://github.com/zentokun90',
    '',
    '',
    'PHP Native,MySQL,JavaScript,HTML/CSS,Data Mining,C4.5 Algorithm,RapidMiner,Sistem Informasi'
)
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

-- =====================================================
-- Seed: projects
-- =====================================================
INSERT INTO projects (title, category, summary, description, tech_stack, my_role, sort_order) VALUES
('Villa Zein', 'Web Platform', 'Company profile & booking platform untuk 5 villa sekaligus.',
 'Platform company profile multi-villa dengan fitur booking dan pengecekan kalender ketersediaan real-time. Setiap villa punya halaman sendiri (price list, fasilitas, peraturan, foto & video). Dilengkapi fitur konsultasi via WhatsApp, invoice otomatis, testimoni foto/video, alur pembayaran DP + pelunasan dengan verifikasi bukti transfer, serta fitur reschedule booking. Seluruh konten (warna tema, sosial media, harga) bisa diatur dari admin panel.',
 'PHP Native, MySQL, JavaScript', 'Full-stack Developer (solo project)', 1),

('Sistem LMS Asafiyah 02', 'Web Application', 'Sistem manajemen pembelajaran untuk institusi pendidikan.',
 'Learning Management System untuk mendukung proses belajar-mengajar secara digital, mencakup manajemen kelas, materi, dan penilaian.',
 'PHP Native, MySQL', 'Developer', 2),

('Big Cargo', 'Web Application', 'Sistem manajemen pengiriman dan logistik kargo.',
 'Sistem untuk mengelola alur pengiriman kargo, pelacakan status barang, dan administrasi logistik.',
 'PHP Native, MySQL', 'Developer', 3),

('Big Aviation', 'Web Application', 'Sistem manajemen operasional di bidang aviasi.',
 'Sistem pendukung operasional layanan aviasi, mencakup pengelolaan data dan administrasi terkait penerbangan.',
 'PHP Native, MySQL', 'Developer', 4),

('GoHaji Umroh', 'Web Platform', 'Platform pendaftaran dan manajemen jamaah umroh/haji.',
 'Platform digital untuk pendaftaran paket umroh/haji, manajemen data jamaah, dan administrasi keberangkatan.',
 'PHP Native, MySQL', 'Full-stack Developer', 5),

('Zenerie', 'Web Project', 'Proyek personal/brand digital.',
 'Deskripsi proyek — silakan lengkapi detail lebih lanjut lewat admin panel.',
 'PHP Native, MySQL', 'Developer', 6),

('Pustaka48', 'Web Application', 'Sistem manajemen perpustakaan digital.',
 'Sistem untuk pengelolaan koleksi buku, peminjaman, dan pengembalian secara digital.',
 'PHP Native, MySQL', 'Developer', 7)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- =====================================================
-- Seed: links (sesuai CONTENT.md §5)
-- =====================================================
INSERT INTO links (label, url, icon, sort_order, is_active) VALUES
('Unduh CV (PDF)', '#cv', 'cv', 1, 1),
('Kirim Email Resmi', 'mailto:zentokun90@gmail.com', 'email', 2, 1),
('Profil LinkedIn', 'https://linkedin.com/in/zentokun90', 'linkedin', 3, 1),
('Repositori GitHub', 'https://github.com/zentokun90', 'github', 4, 1)
ON DUPLICATE KEY UPDATE label = VALUES(label);

-- =====================================================
-- Seed: site_settings
-- =====================================================
INSERT INTO site_settings (setting_key, setting_value) VALUES
('show_creations_section', '1')
ON DUPLICATE KEY UPDATE setting_key = VALUES(setting_key);
