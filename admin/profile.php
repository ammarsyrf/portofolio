<?php
/**
 * Kelola Profil, Dokumen CV, Foto, & Ganti Password Admin
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Kelola Profil & Berkas';
$profile = get_profile($pdo);

if (!$profile) {
    // Buat record profil default jika belum ada
    $pdo->exec("INSERT INTO profile (id, full_name, role_title) VALUES (1, 'Ammar Syarif', 'Web Developer & Data Analyst')");
    $profile = get_profile($pdo);
}

$errorMessage = '';
$activeTab = $_GET['tab'] ?? 'profile';

// Handle Form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $errorMessage = 'Sesi form tidak valid. Silakan coba kembali.';
    } else {
        $formAction = $_POST['action'] ?? 'update_profile';

        // ------------------------------------------------------------------
        // 1. UPDATE DATA PROFIL & BERKAS
        // ------------------------------------------------------------------
        if ($formAction === 'update_profile') {
            $fullName = trim($_POST['full_name'] ?? '');
            $roleTitle = trim($_POST['role_title'] ?? '');
            $tagline = trim($_POST['tagline'] ?? '');
            $about = trim($_POST['about'] ?? '');
            $skills = trim($_POST['skills'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $linkedin = trim($_POST['linkedin'] ?? '');
            $github = trim($_POST['github'] ?? '');
            $whatsapp = trim($_POST['whatsapp'] ?? '');

            $deletePhoto = isset($_POST['delete_photo']) && $_POST['delete_photo'] === '1';
            $deleteCv = isset($_POST['delete_cv']) && $_POST['delete_cv'] === '1';

            if (empty($fullName)) {
                $errorMessage = 'Nama lengkap tidak boleh kosong.';
            } else {
                $photoFileName = $profile['photo'];
                $cvFileName = $profile['cv_file'];

                // Hapus foto jika dicentang
                if ($deletePhoto && !empty($photoFileName)) {
                    delete_uploaded_file($photoFileName, UPLOAD_DIR_PHOTOS);
                    $photoFileName = '';
                }

                // Hapus CV jika dicentang
                if ($deleteCv && !empty($cvFileName)) {
                    delete_uploaded_file($cvFileName, UPLOAD_DIR_CV);
                    $cvFileName = '';
                }

                // Upload Foto Baru
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $uploadPhotoRes = upload_file($_FILES['photo'], 'photo', UPLOAD_DIR_PHOTOS);
                    if ($uploadPhotoRes['success']) {
                        if (!empty($photoFileName)) {
                            delete_uploaded_file($photoFileName, UPLOAD_DIR_PHOTOS);
                        }
                        $photoFileName = $uploadPhotoRes['filename'];
                    } else {
                        $errorMessage = $uploadPhotoRes['error'];
                    }
                }

                // Upload File CV (PDF) Baru
                if (empty($errorMessage) && isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $uploadCvRes = upload_file($_FILES['cv_file'], 'cv', UPLOAD_DIR_CV);
                    if ($uploadCvRes['success']) {
                        if (!empty($cvFileName)) {
                            delete_uploaded_file($cvFileName, UPLOAD_DIR_CV);
                        }
                        $cvFileName = $uploadCvRes['filename'];
                    } else {
                        $errorMessage = $uploadCvRes['error'];
                    }
                }

                // Simpan ke database jika tidak ada error
                if (empty($errorMessage)) {
                    $updateStmt = $pdo->prepare("
                        UPDATE profile SET 
                            full_name = :full_name,
                            role_title = :role_title,
                            tagline = :tagline,
                            about = :about,
                            skills = :skills,
                            email = :email,
                            linkedin = :linkedin,
                            github = :github,
                            whatsapp = :whatsapp,
                            photo = :photo,
                            cv_file = :cv_file
                        WHERE id = 1
                    ");

                    $updateStmt->execute([
                        ':full_name' => $fullName,
                        ':role_title' => $roleTitle,
                        ':tagline' => $tagline,
                        ':about' => $about,
                        ':skills' => $skills,
                        ':email' => $email,
                        ':linkedin' => $linkedin,
                        ':github' => $github,
                        ':whatsapp' => $whatsapp,
                        ':photo' => $photoFileName,
                        ':cv_file' => $cvFileName
                    ]);

                    set_flash('success', 'Data profil dan berkas berhasil diperbarui.');
                    redirect(BASE_URL . '/admin/profile.php');
                }
            }
        }

        // ------------------------------------------------------------------
        // 2. GANTI PASSWORD ADMIN
        // ------------------------------------------------------------------
        elseif ($formAction === 'change_password') {
            $activeTab = 'password';
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword)) {
                $errorMessage = 'Semua bidang password wajib diisi.';
            } elseif (strlen($newPassword) < 6) {
                $errorMessage = 'Password baru minimal harus 6 karakter.';
            } elseif ($newPassword !== $confirmPassword) {
                $errorMessage = 'Konfirmasi password baru tidak cocok.';
            } else {
                // Ambil password saat ini dari DB
                $userStmt = $pdo->prepare("SELECT password FROM admin_users WHERE id = :id LIMIT 1");
                $userStmt->execute([':id' => $_SESSION['admin_user_id']]);
                $currentUser = $userStmt->fetch();

                if ($currentUser && password_verify($currentPassword, $currentUser['password'])) {
                    $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
                    $updPassStmt = $pdo->prepare("UPDATE admin_users SET password = :password WHERE id = :id");
                    $updPassStmt->execute([
                        ':password' => $newHash,
                        ':id' => $_SESSION['admin_user_id']
                    ]);

                    set_flash('success', 'Password administrator berhasil diubah! Gunakan password baru untuk login berikutnya.');
                    redirect(BASE_URL . '/admin/profile.php?tab=password');
                } else {
                    $errorMessage = 'Password saat ini yang Anda masukkan salah.';
                }
            }
        }
    }
}

// Refresh profile data
$profile = get_profile($pdo);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div style="margin-bottom: 1.5rem; display: flex; gap: 0.75rem;">
  <a href="?tab=profile" class="btn <?= ($activeTab !== 'password') ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
    Kelola Profil & Berkas
  </a>
  <a href="?tab=password" class="btn <?= ($activeTab === 'password') ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
    Ganti Password Admin
  </a>
</div>

<?php if (!empty($errorMessage)): ?>
  <div class="flash-alert flash-danger">
    <div><?= e($errorMessage) ?></div>
  </div>
<?php endif; ?>

<?php if ($activeTab === 'password'): ?>
  <!-- Form Ganti Password -->
  <div class="admin-card" style="max-width: 540px;">
    <div class="admin-card-header">
      <div>
        <div class="admin-card-title">Ganti Password Administrator</div>
        <p style="font-size: var(--text-xs); color: var(--color-cream-muted); margin-top: 0.25rem;">
          Pastikan menggunakan kombinasi password yang kuat dan aman.
        </p>
      </div>
    </div>

    <form method="POST" action="">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="change_password">

      <div class="form-group">
        <label class="form-label" for="current_password">Password Saat Ini</label>
        <input type="password" id="current_password" name="current_password" class="form-input" required autocomplete="current-password">
      </div>

      <div class="form-group">
        <label class="form-label" for="new_password">Password Baru</label>
        <input type="password" id="new_password" name="new_password" class="form-input" required placeholder="Minimal 6 karakter" autocomplete="new-password">
      </div>

      <div class="form-group">
        <label class="form-label" for="confirm_password">Ulangi Password Baru</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-input" required placeholder="Ketik ulang password baru" autocomplete="new-password">
      </div>

      <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--color-line);">
        <button type="submit" class="btn btn-primary">
          Perbarui Password
        </button>
      </div>
    </form>
  </div>

<?php else: ?>

  <!-- Form Kelola Profil & Berkas -->
  <div class="admin-card">
    <div class="admin-card-header">
      <div>
        <div class="admin-card-title">Informasi Profil & Berkas</div>
        <p style="font-size: var(--text-xs); color: var(--color-cream-muted); margin-top: 0.25rem;">
          Semua perubahan di halaman ini akan langsung diperbarui di halaman pengunjung.
        </p>
      </div>
      <div>
        <a href="<?= BASE_URL ?>/index.php" target="_blank" class="btn btn-secondary btn-sm">
          Lihat Halaman Publik
        </a>
      </div>
    </div>

    <form method="POST" action="" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="update_profile">

      <div class="form-row form-row-2">
        <div class="form-group">
          <label class="form-label" for="full_name">Nama Lengkap *</label>
          <input type="text" id="full_name" name="full_name" class="form-input" required value="<?= e($profile['full_name']) ?>" placeholder="Ammar Syarif">
        </div>

        <div class="form-group">
          <label class="form-label" for="role_title">Role / Jabatan Pekerjaan</label>
          <input type="text" id="role_title" name="role_title" class="form-input" value="<?= e($profile['role_title']) ?>" placeholder="Web Developer & Data Analyst">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="tagline">Tagline Ringkas (Tampil di Bawah Judul Hero)</label>
        <input type="text" id="tagline" name="tagline" class="form-input" value="<?= e($profile['tagline']) ?>" placeholder="Membangun sistem yang rapi dan mengubah data jadi keputusan.">
      </div>

      <div class="form-group">
        <label class="form-label" for="about">Tentang Saya (Paragraf Narasi Latar Belakang & Filosofi Rekayasa)</label>
        <textarea id="about" name="about" rows="4" class="form-textarea" placeholder="Jelaskan latar belakang pendidikan, spesialisasi, dan pengalaman kerja..."><?= e($profile['about']) ?></textarea>
      </div>

      <div class="form-group">
        <label class="form-label" for="skills">Keahlian & Toolkit (Pisahkan dengan koma)</label>
        <input type="text" id="skills" name="skills" class="form-input" value="<?= e($profile['skills']) ?>" placeholder="PHP Native, MySQL, JavaScript, HTML/CSS, Data Mining, C4.5 Algorithm, RapidMiner, Sistem Informasi">
        <div class="form-help">Setiap keahlian yang dipisahkan koma akan dirender otomatis sebagai tag ledger di halaman depan.</div>
      </div>

      <!-- Upload Foto & Upload CV -->
      <div class="form-row form-row-2" style="border-top: 1px solid var(--color-line); padding-top: 1.5rem; margin-top: 1.5rem;">
        
        <!-- Foto Profil -->
        <div class="form-group">
          <label class="form-label" for="photo">Foto Profil</label>
          <input type="file" id="photo" name="photo" class="form-input" accept=".jpg,.jpeg,.png,.webp">
          <div class="form-help">Format: JPG, PNG, WebP. Maksimal ukuran 2 MB. Disarankan foto formal berlatar polos.</div>

          <?php if (!empty($profile['photo']) && file_exists(UPLOAD_DIR_PHOTOS . '/' . basename($profile['photo']))): ?>
            <div class="file-preview-box">
              <img src="<?= upload_url('photos', $profile['photo']) ?>" alt="Foto Profil" class="file-preview-img">
              <div>
                <div style="font-size: var(--text-xs); color: var(--color-cream); font-weight: 500;">
                  <?= e($profile['photo']) ?>
                </div>
                <label style="font-size: 0.75rem; color: #FCA5A5; display: inline-flex; align-items: center; gap: 0.35rem; margin-top: 0.35rem; cursor: pointer;">
                  <input type="checkbox" name="delete_photo" value="1">
                  Hapus foto saat ini
                </label>
              </div>
            </div>
          <?php else: ?>
            <div style="font-size: 0.75rem; color: var(--color-cream-faint); margin-top: 0.5rem;">
              Belum ada foto profil terunggah (menggunakan inisial nama AS pada folio hero).
            </div>
          <?php endif; ?>
        </div>

        <!-- Berkas CV (PDF) -->
        <div class="form-group">
          <label class="form-label" for="cv_file">Berkas CV (Format PDF)</label>
          <input type="file" id="cv_file" name="cv_file" class="form-input" accept=".pdf,application/pdf">
          <div class="form-help">Khusus file PDF. Maksimal ukuran 5 MB. Dokumen ini yang akan diunduh oleh recruiter pada tombol "Unduh CV".</div>

          <?php if (!empty($profile['cv_file']) && file_exists(UPLOAD_DIR_CV . '/' . basename($profile['cv_file']))): ?>
            <div class="file-preview-box">
              <div style="font-size: 1.5rem; color: var(--color-brass);">📄</div>
              <div>
                <div style="font-size: var(--text-xs); color: var(--color-cream); font-weight: 500;">
                  <?= e($profile['cv_file']) ?>
                </div>
                <div style="display: flex; gap: 1rem; align-items: center; margin-top: 0.35rem;">
                  <a href="<?= upload_url('cv', $profile['cv_file']) ?>" target="_blank" style="font-size: 0.75rem; color: var(--color-brass-bright); text-decoration: underline;">
                    Lihat Dokumen
                  </a>
                  <label style="font-size: 0.75rem; color: #FCA5A5; display: inline-flex; align-items: center; gap: 0.35rem; cursor: pointer;">
                    <input type="checkbox" name="delete_cv" value="1">
                    Hapus file CV saat ini
                  </label>
                </div>
              </div>
            </div>
          <?php else: ?>
            <div style="font-size: 0.75rem; color: var(--color-cream-faint); margin-top: 0.5rem;">
              Belum ada file CV terunggah. Tombol di web publik akan mengarahkan recruiter ke kontak.
            </div>
          <?php endif; ?>
        </div>

      </div>

      <!-- Tautan Kontak & Media Sosial -->
      <div style="border-top: 1px solid var(--color-line); padding-top: 1.5rem; margin-top: 1.5rem;">
        <div style="font-family: var(--font-display); font-size: var(--text-base); color: var(--color-cream); margin-bottom: 1.25rem;">
          Tautan Kontak & Media Sosial
        </div>

        <div class="form-row form-row-2">
          <div class="form-group">
            <label class="form-label" for="email">Alamat Email Resmi</label>
            <input type="email" id="email" name="email" class="form-input" value="<?= e($profile['email']) ?>" placeholder="zentokun90@gmail.com">
          </div>

          <div class="form-group">
            <label class="form-label" for="whatsapp">Nomor WhatsApp Aktif</label>
            <input type="text" id="whatsapp" name="whatsapp" class="form-input" value="<?= e($profile['whatsapp']) ?>" placeholder="08xxxxxxxxxx atau 628xxxxxxxxxx">
            <div class="form-help">Format nomor lokal atau internasional dengan kode negara (misal 628123456789).</div>
          </div>
        </div>

        <div class="form-row form-row-2">
          <div class="form-group">
            <label class="form-label" for="linkedin">Tautan Akun LinkedIn (URL)</label>
            <input type="url" id="linkedin" name="linkedin" class="form-input" value="<?= e($profile['linkedin']) ?>" placeholder="https://linkedin.com/in/zentokun90">
          </div>

          <div class="form-group">
            <label class="form-label" for="github">Tautan Akun GitHub (URL)</label>
            <input type="url" id="github" name="github" class="form-input" value="<?= e($profile['github']) ?>" placeholder="https://github.com/zentokun90">
          </div>
        </div>
      </div>

      <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--color-line); display: flex; gap: 1rem;">
        <button type="submit" class="btn btn-primary">
          Simpan Seluruh Perubahan Profil
        </button>
      </div>
    </form>
  </div>

<?php endif; ?>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
