<?php
/**
 * Form Tambah & Edit Proyek
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$projectId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = ($projectId > 0);

$project = null;
if ($isEdit) {
    $project = get_project_by_id($pdo, $projectId);
    if (!$project) {
        set_flash('danger', 'Proyek tidak ditemukan.');
        redirect(BASE_URL . '/admin/projects.php');
    }
    $pageTitle = 'Edit Proyek: ' . $project['title'];
} else {
    $pageTitle = 'Tambah Proyek Baru';
    $project = [
        'id' => 0,
        'title' => '',
        'category' => 'Web Application',
        'summary' => '',
        'description' => '',
        'tech_stack' => '',
        'my_role' => 'Developer',
        'result_impact' => '',
        'image' => '',
        'demo_link' => '',
        'repo_link' => '',
        'sort_order' => 0,
        'is_published' => 1
    ];
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $errorMessage = 'Sesi form tidak valid. Silakan coba kembali.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $summary = trim($_POST['summary'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $techStack = trim($_POST['tech_stack'] ?? '');
        $myRole = trim($_POST['my_role'] ?? '');
        $resultImpact = trim($_POST['result_impact'] ?? '');
        $demoLink = trim($_POST['demo_link'] ?? '');
        $repoLink = trim($_POST['repo_link'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isPublished = isset($_POST['is_published']) ? 1 : 0;
        $deleteExistingImage = isset($_POST['delete_image']) && $_POST['delete_image'] === '1';

        if (empty($title)) {
            $errorMessage = 'Judul proyek wajib diisi.';
        } else {
            $imageFileName = $project['image'];

            // Jika user memilih untuk menghapus gambar yang ada
            if ($deleteExistingImage && !empty($imageFileName)) {
                delete_uploaded_file($imageFileName, UPLOAD_DIR_PROJECTS);
                $imageFileName = '';
            }

            // Jika ada file gambar baru yang diunggah
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadRes = upload_file($_FILES['image'], 'project', UPLOAD_DIR_PROJECTS);
                if ($uploadRes['success']) {
                    // Hapus gambar lama jika ada
                    if (!empty($imageFileName)) {
                        delete_uploaded_file($imageFileName, UPLOAD_DIR_PROJECTS);
                    }
                    $imageFileName = $uploadRes['filename'];
                } else {
                    $errorMessage = $uploadRes['error'];
                }
            }

            if (empty($errorMessage)) {
                if ($isEdit) {
                    $stmt = $pdo->prepare("
                        UPDATE projects SET 
                            title = :title,
                            category = :category,
                            summary = :summary,
                            description = :description,
                            tech_stack = :tech_stack,
                            my_role = :my_role,
                            result_impact = :result_impact,
                            image = :image,
                            demo_link = :demo_link,
                            repo_link = :repo_link,
                            sort_order = :sort_order,
                            is_published = :is_published
                        WHERE id = :id
                    ");
                    $stmt->execute([
                        ':title' => $title,
                        ':category' => $category,
                        ':summary' => $summary,
                        ':description' => $description,
                        ':tech_stack' => $techStack,
                        ':my_role' => $myRole,
                        ':result_impact' => $resultImpact,
                        ':image' => $imageFileName,
                        ':demo_link' => $demoLink,
                        ':repo_link' => $repoLink,
                        ':sort_order' => $sortOrder,
                        ':is_published' => $isPublished,
                        ':id' => $projectId
                    ]);

                    set_flash('success', 'Data proyek "' . e($title) . '" berhasil diperbarui.');
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO projects 
                            (title, category, summary, description, tech_stack, my_role, result_impact, image, demo_link, repo_link, sort_order, is_published)
                        VALUES 
                            (:title, :category, :summary, :description, :tech_stack, :my_role, :result_impact, :image, :demo_link, :repo_link, :sort_order, :is_published)
                    ");
                    $stmt->execute([
                        ':title' => $title,
                        ':category' => $category,
                        ':summary' => $summary,
                        ':description' => $description,
                        ':tech_stack' => $techStack,
                        ':my_role' => $myRole,
                        ':result_impact' => $resultImpact,
                        ':image' => $imageFileName,
                        ':demo_link' => $demoLink,
                        ':repo_link' => $repoLink,
                        ':sort_order' => $sortOrder,
                        ':is_published' => $isPublished
                    ]);

                    set_flash('success', 'Proyek baru "' . e($title) . '" berhasil ditambahkan.');
                }

                redirect(BASE_URL . '/admin/projects.php');
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-card">
  <div class="admin-card-header">
    <div>
      <div class="admin-card-title"><?= $isEdit ? 'Sunting Data Proyek' : 'Tambah Proyek Baru' ?></div>
      <p style="font-size: var(--text-xs); color: var(--color-cream-muted); margin-top: 0.25rem;">
        Isi detail teknis, peran, dan pratinjau proyek portofolio.
      </p>
    </div>
    <div>
      <a href="<?= BASE_URL ?>/admin/projects.php" class="btn btn-secondary btn-sm">
        Kembali ke Daftar
      </a>
    </div>
  </div>

  <?php if (!empty($errorMessage)): ?>
    <div class="flash-alert flash-danger">
      <div><?= e($errorMessage) ?></div>
    </div>
  <?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="form-row form-row-2">
      <div class="form-group">
        <label class="form-label" for="title">Judul Proyek *</label>
        <input type="text" id="title" name="title" class="form-input" required value="<?= e($project['title']) ?>" placeholder="Contoh: Villa Zein">
      </div>

      <div class="form-group">
        <label class="form-label" for="category">Kategori Proyek</label>
        <input type="text" id="category" name="category" class="form-input" value="<?= e($project['category']) ?>" placeholder="Contoh: Web Platform, Web Application, Web Project">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="summary">Ringkasan Singkat (Muncul pada Kartu Proyek) *</label>
      <input type="text" id="summary" name="summary" class="form-input" required value="<?= e($project['summary']) ?>" placeholder="Ringkasan 1-2 kalimat untuk tampilan card">
      <div class="form-help">Maksimal disarankan 120-180 karakter agar serasi di grid.</div>
    </div>

    <div class="form-group">
      <label class="form-label" for="description">Deskripsi Lengkap (Muncul pada Jendela Rincian / Modal)</label>
      <textarea id="description" name="description" rows="5" class="form-textarea" placeholder="Jelaskan latar belakang, fitur utama, dan arsitektur proyek..."><?= e($project['description']) ?></textarea>
    </div>

    <div class="form-row form-row-2">
      <div class="form-group">
        <label class="form-label" for="tech_stack">Teknologi & Stack (Pisahkan dengan koma)</label>
        <input type="text" id="tech_stack" name="tech_stack" class="form-input" value="<?= e($project['tech_stack']) ?>" placeholder="Contoh: PHP Native, MySQL, JavaScript, HTML/CSS">
      </div>

      <div class="form-group">
        <label class="form-label" for="my_role">Peran Ammar dalam Proyek</label>
        <input type="text" id="my_role" name="my_role" class="form-input" value="<?= e($project['my_role']) ?>" placeholder="Contoh: Full-stack Developer (solo project) atau Developer">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="result_impact">Hasil / Dampak Sistem (Opsional)</label>
      <textarea id="result_impact" name="result_impact" rows="3" class="form-textarea" placeholder="Contoh: Mempercepat proses reservasi hingga 70%, mendukung pemesanan 5 villa secara real-time..."><?= e($project['result_impact']) ?></textarea>
    </div>

    <!-- Upload Gambar -->
    <div class="form-group">
      <label class="form-label" for="image">Tangkapan Layar / Gambar Proyek</label>
      <input type="file" id="image" name="image" class="form-input" accept=".jpg,.jpeg,.png,.webp">
      <div class="form-help">Format: JPG, PNG, atau WebP. Maksimal ukuran 2 MB.</div>

      <?php if (!empty($project['image']) && file_exists(UPLOAD_DIR_PROJECTS . '/' . basename($project['image']))): ?>
        <div class="file-preview-box">
          <img src="<?= upload_url('projects', $project['image']) ?>" alt="Preview" class="file-preview-img">
          <div>
            <div style="font-size: var(--text-xs); color: var(--color-cream); font-weight: 500;">
              File saat ini: <?= e($project['image']) ?>
            </div>
            <label style="font-size: 0.75rem; color: #FCA5A5; display: inline-flex; align-items: center; gap: 0.35rem; margin-top: 0.35rem; cursor: pointer;">
              <input type="checkbox" name="delete_image" value="1">
              Hapus gambar saat ini (kembalikan ke placeholder ledger)
            </label>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="form-row form-row-2">
      <div class="form-group">
        <label class="form-label" for="demo_link">Tautan Demo / Live Website (URL)</label>
        <input type="url" id="demo_link" name="demo_link" class="form-input" value="<?= e($project['demo_link']) ?>" placeholder="https://contoh-demo.com">
      </div>

      <div class="form-group">
        <label class="form-label" for="repo_link">Tautan Repositori GitHub (URL)</label>
        <input type="url" id="repo_link" name="repo_link" class="form-input" value="<?= e($project['repo_link']) ?>" placeholder="https://github.com/zentokun90/repo">
      </div>
    </div>

    <div class="form-row form-row-2">
      <div class="form-group">
        <label class="form-label" for="sort_order">Nomor Urutan Tampil (Sort Order)</label>
        <input type="number" id="sort_order" name="sort_order" class="form-input" value="<?= (int)$project['sort_order'] ?>">
        <div class="form-help">Angka lebih kecil tampil lebih dulu (misal 1, 2, 3...).</div>
      </div>

      <div class="form-group" style="display: flex; flex-direction: column; justify-content: center;">
        <label class="form-label">Status Publikasi</label>
        <label style="display: inline-flex; align-items: center; gap: 0.6rem; cursor: pointer; margin-top: 0.5rem; font-size: var(--text-sm);">
          <input type="checkbox" name="is_published" value="1" <?= $project['is_published'] ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--color-moss-light);">
          <span>Publikasikan ke Halaman Pengunjung</span>
        </label>
      </div>
    </div>

    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--color-line); display: flex; gap: 1rem;">
      <button type="submit" class="btn btn-primary">
        <?= $isEdit ? 'Simpan Perubahan Proyek' : 'Tambahkan Proyek Sekarang' ?>
      </button>
      <a href="<?= BASE_URL ?>/admin/projects.php" class="btn btn-secondary">
        Batal
      </a>
    </div>
  </form>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
