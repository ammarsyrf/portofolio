<?php
/**
 * Form Tambah & Edit Tautan (Links)
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$linkId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = ($linkId > 0);

$link = null;
if ($isEdit) {
    $link = get_link_by_id($pdo, $linkId);
    if (!$link) {
        set_flash('danger', 'Data tautan tidak ditemukan.');
        redirect(BASE_URL . '/admin/links');
    }
    $pageTitle = 'Edit Tautan: ' . $link['label'];
} else {
    $pageTitle = 'Tambah Tautan Baru';
    $link = [
        'id' => 0,
        'label' => '',
        'url' => '',
        'icon' => 'link',
        'sort_order' => 0,
        'is_active' => 1
    ];
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $errorMessage = 'Sesi form tidak valid. Silakan coba kembali.';
    } else {
        $label = trim($_POST['label'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $icon = trim($_POST['icon'] ?? 'link');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($label)) {
            $errorMessage = 'Label tautan wajib diisi.';
        } elseif (empty($url)) {
            $errorMessage = 'URL tujuan wajib diisi.';
        } else {
            if ($isEdit) {
                $stmt = $pdo->prepare("
                    UPDATE links SET 
                        label = :label,
                        url = :url,
                        icon = :icon,
                        sort_order = :sort_order,
                        is_active = :is_active
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':label'      => $label,
                    ':url'        => $url,
                    ':icon'       => $icon,
                    ':sort_order' => $sortOrder,
                    ':is_active'  => $isActive,
                    ':id'         => $linkId
                ]);
                set_flash('success', 'Tautan "' . e($label) . '" berhasil diperbarui.');
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO links (label, url, icon, sort_order, is_active)
                    VALUES (:label, :url, :icon, :sort_order, :is_active)
                ");
                $stmt->execute([
                    ':label'      => $label,
                    ':url'        => $url,
                    ':icon'       => $icon,
                    ':sort_order' => $sortOrder,
                    ':is_active'  => $isActive
                ]);
                set_flash('success', 'Tautan baru berhasil ditambahkan.');
            }
            redirect(BASE_URL . '/admin/links');
        }
    }
}

$icons = [
    'link'      => ['name' => 'Tautan Umum', 'emoji' => '🔗'],
    'cv'        => ['name' => 'Berkas CV / PDF', 'emoji' => '📄'],
    'email'     => ['name' => 'Surel / Email', 'emoji' => '✉️'],
    'linkedin'  => ['name' => 'LinkedIn', 'emoji' => '💼'],
    'github'    => ['name' => 'GitHub', 'emoji' => '🐙'],
    'instagram' => ['name' => 'Instagram', 'emoji' => '📸'],
    'tiktok'    => ['name' => 'TikTok', 'emoji' => '🎵'],
    'globe'     => ['name' => 'Website / Web App', 'emoji' => '🌐'],
    'code'      => ['name' => 'Koding / Source Code', 'emoji' => '💻']
];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-card" style="max-width: 650px; margin: 0 auto;">
  <div class="admin-card-header">
    <div>
      <div class="admin-card-title"><?= $isEdit ? 'Edit Tautan' : 'Tambah Tautan Baru' ?></div>
      <p style="font-size: var(--text-xs); color: var(--color-cream-muted); margin-top: 0.25rem;">
        Konfigurasikan teks tombol, ikon, dan tautan tujuan untuk halaman Bio Links.
      </p>
    </div>
    <div>
      <a href="<?= BASE_URL ?>/admin/links" class="btn btn-secondary btn-sm">&larr; Kembali</a>
    </div>
  </div>

  <?php if (!empty($errorMessage)): ?>
    <div class="flash-alert flash-danger" style="margin-bottom: 1.5rem;">
      <div><?= e($errorMessage) ?></div>
    </div>
  <?php endif; ?>

  <form method="POST" action="">
    <?= csrf_field() ?>

    <div class="form-group">
      <label class="form-label">Label Tautan *</label>
      <input type="text" name="label" class="form-control" required value="<?= e($_POST['label'] ?? $link['label']) ?>" placeholder="Mis. Portofolio Desain Figma / Blog Pribadi">
    </div>

    <div class="form-group">
      <label class="form-label">URL Tujuan *</label>
      <input type="text" name="url" class="form-control" required value="<?= e($_POST['url'] ?? $link['url']) ?>" placeholder="https://... atau mailto:... atau #cv">
      <span class="form-help">Bisa berupa tautan eksternal (https://...), email (mailto:...), atau jangkar halaman (#cv).</span>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Ikon Tombol</label>
        <?php $selectedIcon = $_POST['icon'] ?? $link['icon']; ?>
        <select name="icon" class="form-control">
          <?php foreach ($icons as $key => $meta): ?>
            <option value="<?= e($key) ?>" <?= ($selectedIcon === $key) ? 'selected' : '' ?>>
              <?= $meta['emoji'] ?> <?= e($meta['name']) ?> (<?= e($key) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Urutan Tampilan</label>
        <input type="number" name="sort_order" class="form-control" value="<?= (int)($_POST['sort_order'] ?? $link['sort_order']) ?>" style="max-width: 120px;">
        <span class="form-help">Angka kecil tampil di atas (0, 1, 2...).</span>
      </div>
    </div>

    <div class="form-group" style="margin-top: 0.5rem;">
      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
        <input type="checkbox" name="is_active" value="1" <?= (!isset($_POST['label']) ? ($link['is_active'] ? 'checked' : '') : (isset($_POST['is_active']) ? 'checked' : '')) ?>>
        <span style="font-size: var(--text-sm); color: var(--color-cream); font-weight: 500;">Aktifkan tombol tautan ini di halaman Bio Links</span>
      </label>
    </div>

    <div style="display: flex; gap: 1rem; margin-top: 2rem; border-top: 1px solid var(--color-line); padding-top: 1.5rem;">
      <button type="submit" class="btn btn-primary">
        <?= $isEdit ? 'Simpan Perubahan' : 'Tambahkan Tautan' ?>
      </button>
      <a href="<?= BASE_URL ?>/admin/links" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
