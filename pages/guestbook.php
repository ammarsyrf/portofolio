<?php
/**
 * Halaman Buku Tamu (Guestbook)
 * Visitor dapat membaca secara publik, dan login dengan Google OAuth untuk menulis pesan
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

require_once __DIR__ . '/../includes/seo.php';

$profile = get_profile($pdo);

// Handle logout guest visitor
if (isset($_GET['logout_guest'])) {
    logout_guest();
    set_flash('info', 'Anda telah keluar dari sesi pengunjung Google.');
    redirect(BASE_URL . '/guestbook');
}

$guestUser = get_guest_user();
$isLoggedIn = is_guest_logged_in();
$errorMessage = '';

// Handle Kirim Pesan Guestbook (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$isLoggedIn) {
        set_flash('danger', 'Anda harus masuk dengan Google terlebih dahulu untuk mengirimkan pesan.');
        redirect(BASE_URL . '/guestbook');
    }

    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $errorMessage = 'Sesi form telah kedaluwarsa. Silakan muat ulang halaman.';
    } else {
        $message = trim($_POST['message'] ?? '');

        if (empty($message)) {
            $errorMessage = 'Pesan tidak boleh kosong.';
        } elseif (mb_strlen($message) < 3) {
            $errorMessage = 'Pesan terlalu pendek (minimal 3 karakter).';
        } elseif (mb_strlen($message) > 500) {
            $errorMessage = 'Pesan maksimal 500 karakter.';
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO guestbook (google_id, user_name, user_avatar, user_email, message)
                    VALUES (:google_id, :user_name, :user_avatar, :user_email, :message)
                ");
                $stmt->execute([
                    ':google_id'   => $guestUser['google_id'],
                    ':user_name'   => $guestUser['name'],
                    ':user_avatar' => $guestUser['avatar'],
                    ':user_email'  => $guestUser['email'],
                    ':message'     => $message
                ]);

                set_flash('success', 'Terima kasih, ' . e($guestUser['name']) . '! Pesan Anda berhasil ditambahkan ke Buku Tamu.');
                redirect(BASE_URL . '/guestbook');
            } catch (PDOException $e) {
                error_log("Guestbook Insert Error: " . $e->getMessage());
                $errorMessage = 'Terjadi kesalahan sistem saat menyimpan pesan Anda.';
            }
        }
    }
}

// Ambil daftar pesan buku tamu terbaru
$messages = get_guestbook_messages($pdo);
$hasOauthConfig = (GOOGLE_CLIENT_ID !== 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com' && !empty(GOOGLE_CLIENT_ID));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buku Tamu Interaktif — <?= e($profile['full_name'] ?? 'Ammar Syarif') ?> | Zenerie</title>
  <meta name="description" content="Tinggalkan pesan, salam profesional, atau ulasan untuk <?= e($profile['full_name'] ?? 'Ammar Syarif') ?> di Zenerie melalui autentikasi Google.">
  <?php render_seo(
      $profile,
      'Buku Tamu Interaktif — ' . ($profile['full_name'] ?? 'Ammar Syarif') . ' | Zenerie',
      'Tinggalkan pesan, salam profesional, atau ulasan untuk ' . ($profile['full_name'] ?? 'Ammar Syarif') . ' di Zenerie melalui autentikasi Google.',
      '/guestbook',
      'website',
      ['Buku Tamu' => '/guestbook']
  ); ?>

  <!-- Google Fonts: Space Grotesk & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

  <!-- Global Nav -->
  <?php require_once __DIR__ . '/../includes/nav.php'; ?>

  <main class="site-container" style="padding-top: 4.5rem; padding-bottom: 6rem;">
    <div class="guestbook-wrapper">
      
      <div class="section-header" style="text-align: center;">
        <div class="section-caption">Ruang Silaturahmi & Umpan Balik</div>
        <h1 class="section-title">Buku Tamu Pengunjung</h1>
        <p style="color: var(--color-text-dim); max-width: 58ch; margin: 0.75rem auto 0; font-size: var(--text-base);">
          Tinggalkan pesan, salam profesional, atau masukan untuk Ammar. Masuk dengan akun Google Anda untuk menjamin keaslian identitas.
        </p>
      </div>

      <?php
      $flash = get_flash();
      if ($flash): ?>
        <div style="padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 2rem; font-size: var(--text-sm); background-color: <?= ($flash['type'] === 'success') ? 'rgba(34, 197, 94, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; border: 1px solid <?= ($flash['type'] === 'success') ? 'rgba(34, 197, 94, 0.4)' : 'rgba(239, 68, 68, 0.4)' ?>; color: <?= ($flash['type'] === 'success') ? '#86EFAC' : '#FCA5A5' ?>;">
          <?= e($flash['message']) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($errorMessage)): ?>
        <div style="padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 2rem; font-size: var(--text-sm); background-color: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #FCA5A5;">
          <?= e($errorMessage) ?>
        </div>
      <?php endif; ?>

      <!-- ------------------------------------------------------------------
           Area Form Kirim Pesan / Tombol Login Google
           ------------------------------------------------------------------ -->
      <?php if ($isLoggedIn): ?>
        <!-- Form Kirim Pesan untuk Pengunjung Login -->
        <div class="guestbook-form-box glass-panel-glow">
          <div class="guestbook-user-header">
            <?php if (!empty($guestUser['avatar'])): ?>
              <img src="<?= e($guestUser['avatar']) ?>" alt="<?= e($guestUser['name']) ?>" class="guest-avatar">
            <?php else: ?>
              <div class="guest-avatar" style="background: var(--color-accent); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                <?= strtoupper(substr($guestUser['name'], 0, 1)) ?>
              </div>
            <?php endif; ?>

            <div>
              <div style="font-weight: 600; color: var(--color-text);"><?= e($guestUser['name']) ?></div>
              <div style="font-size: 0.75rem; color: var(--color-text-dim);">
                Terhubung sebagai <?= e($guestUser['email']) ?> &bull; 
                <a href="?logout_guest=1" style="color: #FCA5A5; text-decoration: underline;">Keluar</a>
              </div>
            </div>
          </div>

          <form method="POST" action="">
            <?= csrf_field() ?>

            <div style="margin-bottom: 1.25rem;">
              <label for="message" style="display: block; font-size: var(--text-xs); color: var(--color-accent-bright); font-weight: 600; margin-bottom: 0.5rem;">
                Tuliskan Pesan Anda (Maksimal 500 Karakter)
              </label>
              <textarea id="message" name="message" rows="4" maxlength="500" required placeholder="Tuliskan ucapan, pertanyaan, atau kesan Anda di sini..." style="width: 100%; background-color: var(--color-bg-surface); border: 1px solid var(--color-line); color: var(--color-text); padding: 0.85rem 1rem; border-radius: 8px; font-family: var(--font-body); font-size: var(--text-sm); outline: none;"></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
              Kirimkan ke Buku Tamu
            </button>
          </form>
        </div>

      <?php else: ?>
        <!-- Banner Otorisasi Google -->
        <div class="guestbook-auth-banner glass-panel">
          <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">✍️</div>
          <h2 style="font-family: var(--font-display); font-size: var(--text-lg); font-weight: 700; color: var(--color-text); margin-bottom: 0.5rem;">
            Ingin Menuliskan Catatan?
          </h2>
          <p style="color: var(--color-text-dim); font-size: var(--text-sm); max-width: 46ch; margin: 0 auto 1.5rem; line-height: 1.6;">
            Untuk menjaga kenyamanan dan mencegah spam, Anda perlu masuk dengan akun Google sebelum mengirimkan pesan.
          </p>

          <a href="<?= BASE_URL ?>/auth/google_login.php" class="btn-google-login">
            <svg width="18" height="18" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Masuk dengan Akun Google</span>
          </a>

          <?php if (!$hasOauthConfig && function_exists('is_admin_logged_in') && is_admin_logged_in()): ?>
            <div style="font-size: 0.72rem; color: var(--color-text-faint); margin-top: 1.25rem; background: rgba(76,141,255,0.08); padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px dashed rgba(76,141,255,0.3);">
              ⚙️ Admin Note: Isi kredensial Google OAuth di <code>config.php</code> untuk mengaktifkan login publik.
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <!-- ------------------------------------------------------------------
           Daftar Pesan Publik (Terbuka untuk Semua Pengunjung)
           ------------------------------------------------------------------ -->
      <div style="margin-top: 3.5rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--color-line);">
          <h2 style="font-family: var(--font-display); font-size: var(--text-lg); font-weight: 700; color: var(--color-text);">
            Catatan Pengunjung (<?= count($messages) ?>)
          </h2>
          <span style="font-size: var(--text-xs); color: var(--color-accent-bright);">Publik</span>
        </div>

        <?php if (empty($messages)): ?>
          <div class="glass-panel" style="padding: 3rem 1.5rem; text-align: center; border-radius: 12px;">
            <p style="color: var(--color-text-dim); font-size: var(--text-sm);">
              Belum ada pesan yang tercatat. Jadilah yang pertama meninggalkan catatan di buku tamu ini!
            </p>
          </div>
        <?php else: ?>
          <div class="guestbook-feed">
            <?php foreach ($messages as $msg): 
              $initial = strtoupper(substr($msg['user_name'], 0, 1));
              $timeAgo = date('d M Y, H:i', strtotime($msg['created_at']));
            ?>
              <div class="guest-bubble">
                <div class="guest-bubble-header">
                  <div class="guest-info-block">
                    <?php if (!empty($msg['user_avatar'])): ?>
                      <img src="<?= e($msg['user_avatar']) ?>" alt="<?= e($msg['user_name']) ?>" class="guest-avatar" style="width: 38px; height: 38px;">
                    <?php else: ?>
                      <div class="guest-avatar" style="width: 38px; height: 38px; background: rgba(76, 141, 255, 0.2); color: var(--color-accent-bright); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.85rem;">
                        <?= e($initial) ?>
                      </div>
                    <?php endif; ?>

                    <div>
                      <div class="guest-name"><?= e($msg['user_name']) ?></div>
                      <div class="guest-date"><?= e($timeAgo) ?> WIB</div>
                    </div>
                  </div>
                </div>

                <div class="guest-msg-text">
                  <?= nl2br(e($msg['message'])) ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <!-- Site Footer -->
  <footer class="site-footer">
    <div class="site-container footer-inner">
      <div>&copy; <?= date('Y') ?> <?= e($profile['full_name'] ?? 'Ammar Syarif') ?> — Zenerie. Buku Tamu Digital.</div>
      <div style="display: flex; gap: 1.5rem;">
        <a href="<?= BASE_URL ?>/" class="footer-admin-link">Beranda</a>
        <a href="<?= BASE_URL ?>/links" class="footer-admin-link">Tautan</a>
        <a href="<?= BASE_URL ?>/admin/login" class="footer-admin-link">Portal Admin</a>
      </div>
    </div>
  </footer>

  <!-- Command Palette -->
  <?php require_once __DIR__ . '/../includes/command_palette.php'; ?>

  <!-- Scripts -->
  <script src="<?= asset('js/main.js') ?>"></script>
  <script src="<?= asset('js/command_palette.js') ?>"></script>
</body>
</html>
