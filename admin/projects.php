<?php
/**
 * Kelola Proyek: List & Hapus
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Kelola Koleksi Proyek';

// Handle Hapus Proyek (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        set_flash('danger', 'Token CSRF tidak valid. Silakan coba kembali.');
        redirect(BASE_URL . '/admin/projects');
    }

    $projectId = (int)($_POST['project_id'] ?? 0);
    if ($projectId > 0) {
        $project = get_project_by_id($pdo, $projectId);
        if ($project) {
            // Hapus file gambar jika ada
            if (!empty($project['image'])) {
                delete_uploaded_file($project['image'], UPLOAD_DIR_PROJECTS);
            }

            $delStmt = $pdo->prepare("DELETE FROM projects WHERE id = :id");
            $delStmt->execute([':id' => $projectId]);

            set_flash('success', 'Proyek "' . e($project['title']) . '" berhasil dihapus.');
        } else {
            set_flash('danger', 'Proyek tidak ditemukan.');
        }
    }
    redirect(BASE_URL . '/admin/projects');
}

// Ambil semua proyek
$projects = get_all_projects($pdo);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-card">
  <div class="admin-card-header">
    <div>
      <div class="admin-card-title">Daftar Seluruh Proyek</div>
      <p style="font-size: var(--text-xs); color: var(--color-cream-muted); margin-top: 0.25rem;">
        Kelola proyek, urutan tampilan (sort order), dan status publikasi di website publik.
      </p>
    </div>
    <div>
      <a href="<?= BASE_URL ?>/admin/project-form" class="btn btn-primary btn-sm">
        + Tambah Proyek Baru
      </a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 50px;">Gambar</th>
          <th style="width: 70px;">Urutan</th>
          <th>Judul Proyek & Peran</th>
          <th>Kategori</th>
          <th>Teknologi</th>
          <th style="width: 100px;">Status</th>
          <th style="text-align: right; width: 140px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($projects)): ?>
          <tr>
            <td colspan="7" style="text-align: center; padding: 3rem 1rem;">
              <p style="color: var(--color-cream-muted); margin-bottom: 1rem;">Belum ada proyek yang tersimpan.</p>
              <a href="<?= BASE_URL ?>/admin/project-form" class="btn btn-primary btn-sm">Tambah Proyek Pertama</a>
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($projects as $p): 
            $hasImg = !empty($p['image']) && file_exists(UPLOAD_DIR_PROJECTS . '/' . basename($p['image']));
          ?>
            <tr>
              <td>
                <?php if ($hasImg): ?>
                  <img src="<?= upload_url('projects', $p['image']) ?>" alt="<?= e($p['title']) ?>" style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid var(--adm-border); box-shadow: 0 4px 12px rgba(0,0,0,0.35);">
                <?php else: ?>
                  <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.04); border: 1px solid var(--adm-border); display: flex; align-items: center; justify-content: center; font-size: 0.68rem; color: var(--adm-text-muted); border-radius: 8px;">
                    No Img
                  </div>
                <?php endif; ?>
              </td>

              <td style="color: var(--adm-accent-bright); font-family: var(--font-mono); font-weight: 700;">
                #<?= (int)$p['sort_order'] ?>
              </td>

              <td>
                <div style="font-weight: 600; color: #ffffff; font-size: 0.88rem;">
                  <?= e($p['title']) ?>
                </div>
                <div style="font-size: 0.76rem; color: var(--adm-text-muted);">
                  <?= e($p['my_role'] ?: 'Developer') ?>
                </div>
              </td>

              <td>
                <span style="font-size: 0.78rem; font-weight: 600; color: #38bdf8;">
                  <?= e($p['category'] ?: '-') ?>
                </span>
              </td>

              <td style="font-size: 0.8rem; color: var(--adm-text-secondary); max-width: 200px;">
                <?= e($p['tech_stack'] ?: '-') ?>
              </td>

              <td>
                <?php if ($p['is_published']): ?>
                  <span class="badge-status badge-published">Published</span>
                <?php else: ?>
                  <span class="badge-status badge-draft">Draft</span>
                <?php endif; ?>
              </td>

              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.45rem;">
                  <a href="<?= BASE_URL ?>/admin/project-form?id=<?= (int)$p['id'] ?>" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.65rem;">
                    Edit
                  </a>

                  <form method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin menghapus proyek <?= e(addslashes($p['title'])) ?>? Tindakan ini tidak dapat dibatalkan.');" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="project_id" value="<?= (int)$p['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.35rem 0.65rem;">
                      Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
