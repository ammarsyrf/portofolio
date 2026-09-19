<?php
/**
 * Setup Akun Admin Pertama Kali
 * File ini HANYA dapat diakses jika tabel admin_users masih kosong.
 * Wajib dihapus setelah akun admin berhasil dibuat.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Periksa apakah sudah ada admin terdaftar
$stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
$adminCount = (int)$stmt->fetchColumn();

$isSetupLocked = ($adminCount > 0);
$successMessage = '';
$errorMessage = '';

if (!$isSetupLocked && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $errorMessage = 'Sesi tidak valid (CSRF token mismatch). Silakan refresh halaman dan coba lagi.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($username) || empty($password)) {
            $errorMessage = 'Username dan password wajib diisi.';
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $errorMessage = 'Username harus terdiri dari 3 hingga 50 karakter.';
        } elseif (!preg_match('/^[a-zA-Z0-9_.-]+$/', $username)) {
            $errorMessage = 'Username hanya boleh mengandung huruf, angka, garis bawah (_), titik (.), atau tanda hubung (-).';
        } elseif (strlen($password) < 6) {
            $errorMessage = 'Password minimal harus 6 karakter demi keamanan.';
        } elseif ($password !== $confirmPassword) {
            $errorMessage = 'Konfirmasi password tidak cocok.';
        } else {
            // Hash password dengan BCRYPT
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            try {
                $insertStmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (:username, :password)");
                $insertStmt->execute([
                    ':username' => $username,
                    ':password' => $passwordHash
                ]);

                $successMessage = "Akun administrator <strong>" . e($username) . "</strong> berhasil dibuat!";
                $isSetupLocked = true; // Kunci form setelah sukses
            } catch (PDOException $e) {
                error_log("Setup admin error: " . $e->getMessage());
                $errorMessage = 'Gagal membuat akun admin. Kemungkinan username sudah dipakai.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Setup Administrator Awal — Portfolio Ammar Syarif</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Manrope:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
  <style>
    .setup-wrap {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1.5rem;
    }
    .setup-card {
      background-color: var(--color-bg-alt);
      border: 1px solid var(--color-line);
      border-radius: 4px;
      width: 100%;
      max-width: 480px;
      padding: 2.25rem;
      position: relative;
    }
    .setup-header {
      margin-bottom: 1.75rem;
      text-align: center;
    }
    .setup-header h1 {
      font-family: var(--font-display);
      font-size: var(--text-2xl);
      color: var(--color-cream);
      margin-bottom: 0.5rem;
    }
    .setup-header p {
      font-size: var(--text-sm);
      color: var(--color-cream-muted);
    }
    .form-group {
      margin-bottom: 1.25rem;
    }
    .form-label {
      display: block;
      font-size: var(--text-xs);
      color: var(--color-brass);
      margin-bottom: 0.4rem;
      letter-spacing: 0.03em;
    }
    .form-control {
      width: 100%;
      background-color: var(--color-bg-surface);
      border: 1px solid var(--color-line);
      color: var(--color-cream);
      padding: 0.75rem 0.9rem;
      border-radius: 2px;
      font-size: var(--text-sm);
      transition: border-color var(--transition-fast);
    }
    .form-control:focus {
      border-color: var(--color-brass);
      outline: none;
    }
    .alert-box {
      padding: 1rem;
      border-radius: 2px;
      margin-bottom: 1.5rem;
      font-size: var(--text-sm);
      line-height: 1.5;
    }
    .alert-danger {
      background-color: rgba(185, 28, 28, 0.15);
      border: 1px solid rgba(239, 68, 68, 0.35);
      color: #FCA5A5;
    }
    .alert-success {
      background-color: rgba(85, 104, 79, 0.2);
      border: 1px solid var(--color-moss-light);
      color: #D1E7DD;
    }
    .alert-warning {
      background-color: rgba(176, 138, 90, 0.18);
      border: 1px solid var(--color-brass);
      color: var(--color-cream);
    }
    .warning-notice {
      background-color: rgba(176, 138, 90, 0.1);
      border-left: 3px solid var(--color-brass);
      padding: 0.75rem 1rem;
      margin-top: 1.5rem;
      font-size: var(--text-xs);
      color: var(--color-cream-muted);
    }
  </style>
</head>
<body>

  <div class="setup-wrap">
    <div class="setup-card">
      <div class="setup-header">
        <span class="brand-folio" style="margin-bottom: 0.5rem; display: inline-block;">INICIALISASI SISTEM</span>
        <h1>Setup Administrator</h1>
        <p>Inisialisasi kredensial login admin pertama untuk mengelola portofolio.</p>
      </div>

      <?php if (!empty($errorMessage)): ?>
        <div class="alert-box alert-danger">
          <?= e($errorMessage) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($successMessage)): ?>
        <div class="alert-box alert-success">
          <?= $successMessage ?>
        </div>

        <div class="alert-box alert-warning">
          <strong>PENTING:</strong> Akun admin telah aktif. Demi alasan keamanan sistem, silakan segera hapus file <code>admin/setup.php</code> dari server Anda!
        </div>

        <div style="margin-top: 1.5rem; text-align: center;">
          <a href="<?= BASE_URL ?>/admin/login.php" class="btn btn-primary" style="width: 100%;">
            Lanjut ke Halaman Login Admin
          </a>
        </div>
      <?php elseif ($isSetupLocked): ?>
        <div class="alert-box alert-warning">
          <strong>Setup Terkunci:</strong> Sudah terdapat akun administrator di database. Halaman pendaftaran pertama ini telah dinonaktifkan demi keamanan.
        </div>

        <div class="warning-notice">
          <strong>Catatan Keamanan:</strong> Harap hapus file <code>admin/setup.php</code> jika tidak lagi digunakan.
        </div>

        <div style="margin-top: 1.5rem; text-align: center;">
          <a href="<?= BASE_URL ?>/admin/login.php" class="btn btn-primary" style="width: 100%;">
            Buka Halaman Login
          </a>
        </div>
      <?php else: ?>
        <form method="POST" action="">
          <?= csrf_field() ?>

          <div class="form-group">
            <label class="form-label" for="username">Username Admin</label>
            <input type="text" id="username" name="username" class="form-control" required placeholder="Contoh: admin atau ammar" autocomplete="username">
          </div>

          <div class="form-group">
            <label class="form-label" for="password">Password Baru</label>
            <input type="password" id="password" name="password" class="form-control" required placeholder="Minimal 6 karakter" autocomplete="new-password">
          </div>

          <div class="form-group">
            <label class="form-label" for="confirm_password">Konfirmasi Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="Ketik ulang password" autocomplete="new-password">
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">
            Daftarkan Akun Admin
          </button>
        </form>

        <div class="warning-notice">
          File ini hanya bisa digunakan saat tabel <code>admin_users</code> masih kosong. Password akan di-hash secara aman menggunakan BCRYPT.
        </div>
      <?php endif; ?>

      <div style="margin-top: 1.5rem; text-align: center;">
        <a href="<?= BASE_URL ?>" class="btn-detail-trigger" style="font-size: var(--text-xs); color: var(--color-cream-faint);">
          Kembali ke Halaman Utama
        </a>
      </div>
    </div>
  </div>

</body>
</html>
