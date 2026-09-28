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

                    // Catat ke log login sukses
                    log_login_attempt($pdo, $username, 'SUCCESS');

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
                    // Catat ke log login gagal
                    log_login_attempt($pdo, $username, 'FAILED');

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
  <title>Login Administrator — Studio Portfolio Ammar Syarif</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
  <style>
    :root {
      --font-display: 'Plus Jakarta Sans', system-ui, sans-serif;
      --font-body: 'Inter', system-ui, sans-serif;
    }
    body {
      background: #07090e;
      font-family: var(--font-body);
      color: #f8fafc;
      min-height: 100vh;
      margin: 0;
      overflow-x: hidden;
      background-image: 
        radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.15) 0px, transparent 50%),
        radial-gradient(at 100% 100%, rgba(139, 92, 246, 0.12) 0px, transparent 50%),
        radial-gradient(at 50% 50%, rgba(15, 23, 42, 0.8) 0px, transparent 100%);
      background-attachment: fixed;
    }
    .auth-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2.5rem 1.5rem;
      position: relative;
    }
    .auth-card {
      background: rgba(13, 18, 28, 0.85);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 18px;
      width: 100%;
      max-width: 440px;
      padding: 2.5rem;
      position: relative;
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), 0 0 35px rgba(59, 130, 246, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.1);
      animation: authCardFade 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes authCardFade {
      from { opacity: 0; transform: translateY(12px) scale(0.98); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .auth-header {
      margin-bottom: 2rem;
      text-align: center;
    }
    .auth-logo {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 1.25rem;
      color: #fff;
      margin-bottom: 1.25rem;
      box-shadow: 0 0 20px rgba(59, 130, 246, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .auth-header h1 {
      font-family: var(--font-display);
      font-size: 1.45rem;
      font-weight: 800;
      color: #ffffff;
      margin: 0 0 0.35rem;
      letter-spacing: -0.02em;
    }
    .auth-header p {
      font-size: 0.84rem;
      color: #94a3b8;
      margin: 0;
      line-height: 1.45;
    }
    .form-group {
      margin-bottom: 1.35rem;
    }
    .form-label {
      display: block;
      font-size: 0.8rem;
      font-weight: 600;
      color: #cbd5e1;
      margin-bottom: 0.45rem;
    }
    .form-control {
      width: 100%;
      background: rgba(17, 24, 39, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.12);
      color: #ffffff;
      padding: 0.8rem 1rem;
      border-radius: 9px;
      font-size: 0.88rem;
      font-family: inherit;
      transition: all 0.2s ease;
      box-sizing: border-box;
    }
    .form-control:focus {
      border-color: #3b82f6;
      background: rgba(22, 30, 48, 0.95);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25), 0 0 20px rgba(59, 130, 246, 0.15);
      outline: none;
    }
    .btn-login {
      width: 100%;
      padding: 0.8rem 1.25rem;
      font-size: 0.9rem;
      font-weight: 700;
      color: #ffffff;
      background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 9px;
      cursor: pointer;
      box-shadow: 0 4px 16px rgba(59, 130, 246, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.2);
      transition: all 0.2s ease;
      margin-top: 0.75rem;
    }
    .btn-login:hover:not(:disabled) {
      background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
      transform: translateY(-1.5px);
      box-shadow: 0 8px 24px rgba(59, 130, 246, 0.5);
    }
    .btn-login:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }
    .alert-box {
      padding: 0.9rem 1.15rem;
      border-radius: 9px;
      margin-bottom: 1.5rem;
      font-size: 0.84rem;
      line-height: 1.45;
    }
    .alert-danger {
      background: rgba(244, 63, 94, 0.15);
      border: 1px solid rgba(244, 63, 94, 0.35);
      color: #fda4af;
    }
    .alert-success {
      background: rgba(16, 185, 129, 0.15);
      border: 1px solid rgba(16, 185, 129, 0.35);
      color: #6ee7b7;
    }
    .alert-info {
      background: rgba(59, 130, 246, 0.15);
      border: 1px solid rgba(59, 130, 246, 0.35);
      color: #93c5fd;
    }
  </style>
</head>
<body>

  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="auth-header">
        <div class="auth-logo">AS</div>
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
          Belum ada akun admin terdaftar di sistem. Silakan lakukan inisialisasi akun melalui halaman <a href="setup.php" style="color: #60a5fa; text-decoration: underline;">Setup Admin Pertama Kali</a>.
        </div>
      <?php endif; ?>

      <form method="POST" action="">
        <?= csrf_field() ?>

        <div class="form-group">
          <label class="form-label" for="username">Username</label>
          <input type="text" id="username" name="username" class="form-control" required autofocus autocomplete="username" placeholder="Masukkan username" <?= $isLocked ? 'disabled' : '' ?>>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password" placeholder="Masukkan password" <?= $isLocked ? 'disabled' : '' ?>>
        </div>

        <button type="submit" class="btn-login" <?= $isLocked ? 'disabled' : '' ?>>
          <?= $isLocked ? '🔒 Login Dikunci Sementara' : 'Masuk ke Dashboard' ?>
        </button>
      </form>

      <div style="margin-top: 2rem; text-align: center;">
        <a href="<?= BASE_URL ?>/" style="font-size: 0.8rem; color: #64748b; text-decoration: none; transition: color 0.15s ease;" onmouseover="this.style.color='#94a3b8'" onmouseout="this.style.color='#64748b'">
          &larr; Lihat Website Publik Portofolio
        </a>
      </div>
    </div>
  </div>

</body>
</html>
