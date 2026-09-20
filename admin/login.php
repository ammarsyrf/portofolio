<?php
/**
 * Halaman Login Administrator
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

// Jika admin sudah login, langsung alihkan ke dashboard
if (!empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_user_id'])) {
    redirect(BASE_URL . '/admin/dashboard');
}

// Cek apakah tabel admin_users masih kosong (jika kosong, sarankan ke setup.php)
$checkUsers = $pdo->query("SELECT COUNT(*) FROM admin_users");
$hasUsers = ((int)$checkUsers->fetchColumn() > 0);

$errorMessage = '';
$flash = get_flash();

// Cek status rate limiting untuk IP klien
$clientIp = get_client_ip();
$throttle = is_login_locked($clientIp);
$isLocked = $throttle['locked'];

if ($isLocked) {
    $remainingMinutes = max(1, (int)ceil($throttle['remaining_seconds'] / 60));
    $errorMessage = "⚠️ Terlalu banyak percobaan login gagal. Demi keamanan, akses login dari IP Anda dikunci sementara selama {$remainingMinutes} menit.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($isLocked) {
        $remainingMinutes = max(1, (int)ceil($throttle['remaining_seconds'] / 60));
        $errorMessage = "⚠️ Akses login masih dikunci. Silakan coba kembali dalam {$remainingMinutes} menit.";
    } else {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            $errorMessage = 'Sesi form telah kedaluwarsa. Silakan muat ulang halaman.';
        } else {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $errorMessage = 'Silakan masukkan username dan password Anda.';
            } else {
                // Prepared statement untuk mencari admin
                $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = :username LIMIT 1");
                $stmt->execute([':username' => $username]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password'])) {
                    // Reset counter percobaan gagal saat login berhasil
                    reset_login_attempts($clientIp);

                    // Regenerasi session ID untuk mencegah session fixation
                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_user_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_login_time'] = time();
                    $_SESSION['admin_last_activity'] = time();
                    $_SESSION['admin_user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

                    set_flash('success', 'Selamat datang kembali, ' . e($admin['username']) . '!');
                    redirect(BASE_URL . '/admin/dashboard');
                } else {
                    // Catat kegagalan login dan evaluasi rate limiting
                    $record = record_failed_login($clientIp);
                    if ($record['locked']) {
                        $remainingMinutes = max(1, (int)ceil($record['remaining_seconds'] / 60));
                        $isLocked = true;
                        $errorMessage = "⚠️ Terlalu banyak percobaan gagal (5x). Akses login dikunci selama {$remainingMinutes} menit demi keamanan.";
                    } else {
                        $remainingAttempts = MAX_LOGIN_ATTEMPTS - $record['attempts'];
                        $errorMessage = "Kombinasi username atau password tidak tepat. (Sisa percobaan aman: {$remainingAttempts})";
                    }
                }
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
  <title>Login Administrator — Portfolio Ammar Syarif</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Manrope:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
  <style>
    .auth-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1.5rem;
    }
    .auth-card {
      background-color: var(--color-bg-alt);
      border: 1px solid var(--color-line);
      border-radius: 4px;
      width: 100%;
      max-width: 440px;
      padding: 2.25rem;
      position: relative;
    }
    .auth-header {
      margin-bottom: 2rem;
      text-align: center;
    }
    .auth-header h1 {
      font-family: var(--font-display);
      font-size: var(--text-2xl);
      color: var(--color-cream);
      margin-top: 0.35rem;
      margin-bottom: 0.35rem;
    }
    .auth-header p {
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
      padding: 0.85rem 1rem;
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
    .alert-info {
      background-color: rgba(176, 138, 90, 0.15);
      border: 1px solid var(--color-brass);
      color: var(--color-cream);
    }
  </style>
</head>
<body>

  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="auth-header">
        <span class="brand-folio">ADMINISTRATION</span>
        <h1>Portal Pengelola</h1>
        <p>Akses autentikasi untuk pembaruan data portofolio.</p>
      </div>

      <?php if (!empty($flash)): ?>
        <div class="alert-box alert-<?= e($flash['type'] === 'danger' ? 'danger' : ($flash['type'] === 'success' ? 'success' : 'info')) ?>">
          <?= e($flash['message']) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($errorMessage)): ?>
        <div class="alert-box alert-danger">
          <?= e($errorMessage) ?>
        </div>
      <?php endif; ?>

      <?php if (!$hasUsers): ?>
        <div class="alert-box alert-info">
          Belum ada akun admin terdaftar di sistem. Silakan lakukan inisialisasi akun melalui halaman <a href="setup.php" style="color: var(--color-brass-bright); text-decoration: underline;">Setup Admin Pertama Kali</a>.
        </div>
      <?php endif; ?>

      <form method="POST" action="">
        <?= csrf_field() ?>

        <div class="form-group">
          <label class="form-label" for="username">Username</label>
          <input type="text" id="username" name="username" class="form-control" required autofocus autocomplete="username" <?= $isLocked ? 'disabled' : '' ?>>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password" <?= $isLocked ? 'disabled' : '' ?>>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.75rem;" <?= $isLocked ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?>>
          <?= $isLocked ? '🔒 Login Dikunci Sementara' : 'Masuk ke Dashboard' ?>
        </button>
      </form>

      <div style="margin-top: 2rem; text-align: center;">
        <a href="<?= BASE_URL ?>" class="btn-detail-trigger" style="font-size: var(--text-xs); color: var(--color-cream-faint);">
          Lihat Halaman Publik Portofolio
        </a>
      </div>
    </div>
  </div>

</body>
</html>
