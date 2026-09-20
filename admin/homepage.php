<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/homepage_content.php';

$pageTitle = 'Konten Halaman Utama';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errorMessage = 'Sesi form tidak valid. Silakan coba kembali.';
    } else {
        try {
            save_homepage_content($pdo, $_POST);
            set_flash('success', 'Konten halaman utama berhasil diperbarui.');
            redirect(BASE_URL . '/admin/homepage.php');
        } catch (JsonException $exception) {
            $errorMessage = 'Konten tidak dapat disimpan. Silakan coba kembali.';
        }
    }
}

$content = get_homepage_content($pdo);
$groups = [
    'Hero & Statistik' => ['hero_education_label','hero_degree','hero_field','hero_specialty_label','hero_specialty_value','hero_availability_label','hero_availability_value','hero_readiness','hero_line_one','hero_line_two','hero_line_three','hero_badge','hero_vertical_badge'],
    'Kartu Brand Zenerie' => ['brand_title','brand_badge','brand_description','brand_url','brand_button'],
    'Widget Dashboard' => ['identity_button','media_title','media_button','tech_widget_title','tech_widget_status','tech_widget_footer','tech_widget_button'],
    'Kartu Tentang (Bento)' => ['about_widget_title','about_widget_status','about_widget_text_one','about_widget_text_two','about_metric_one_value','about_metric_one_label','about_metric_two_value','about_metric_two_label'],
    'Bagian Tentang Saya' => ['about_caption','about_title','about_kicker','about_heading','about_paragraph_one','about_paragraph_two','about_paragraph_three'],
    'Bagian Keahlian' => ['skills_caption','skills_title','skills_lead'],
    'Bagian Proyek' => ['projects_caption','projects_title'],
    'Bagian Kontak' => ['contact_caption','contact_title','contact_email_title','contact_email_description','contact_whatsapp_title','contact_whatsapp_description','contact_linkedin_title','contact_linkedin_description','contact_github_title','contact_github_description','contact_form_title','contact_form_button'],
];
$labels = [
    'hero_education_label'=>'Label pendidikan','hero_degree'=>'Gelar singkat','hero_field'=>'Bidang pendidikan','hero_specialty_label'=>'Label spesialisasi','hero_specialty_value'=>'Nilai spesialisasi','hero_availability_label'=>'Label ketersediaan','hero_availability_value'=>'Nilai ketersediaan','hero_readiness'=>'Kesiapan teknis (0–100)','hero_line_one'=>'Hero baris 1','hero_line_two'=>'Hero baris 2','hero_line_three'=>'Hero baris 3','hero_badge'=>'Badge hero','hero_vertical_badge'=>'Badge vertikal',
    'brand_title'=>'Judul brand','brand_badge'=>'Badge brand','brand_description'=>'Deskripsi Zenerie','brand_url'=>'URL website Zenerie','brand_button'=>'Teks tombol','identity_button'=>'Tombol profil','media_title'=>'Judul media','media_button'=>'Tombol media','tech_widget_title'=>'Judul tech stack','tech_widget_status'=>'Status tech stack','tech_widget_footer'=>'Footer tech stack','tech_widget_button'=>'Tombol tech stack','projects_caption'=>'Caption proyek','projects_title'=>'Judul proyek',
    'about_widget_title'=>'Judul kartu','about_widget_status'=>'Status','about_widget_text_one'=>'Paragraf 1','about_widget_text_two'=>'Paragraf 2','about_metric_one_value'=>'Metrik 1 nilai','about_metric_one_label'=>'Metrik 1 label','about_metric_two_value'=>'Metrik 2 nilai','about_metric_two_label'=>'Metrik 2 label',
    'about_caption'=>'Caption section','about_title'=>'Judul section','about_kicker'=>'Kicker','about_heading'=>'Judul panel','about_paragraph_one'=>'Paragraf 1','about_paragraph_two'=>'Paragraf 2','about_paragraph_three'=>'Paragraf 3',
    'skills_caption'=>'Caption section','skills_title'=>'Judul section','skills_lead'=>'Deskripsi section',
    'contact_caption'=>'Caption section','contact_title'=>'Judul section','contact_email_title'=>'Judul kartu email','contact_email_description'=>'Deskripsi kartu email','contact_whatsapp_title'=>'Judul kartu WhatsApp','contact_whatsapp_description'=>'Deskripsi kartu WhatsApp','contact_linkedin_title'=>'Judul kartu LinkedIn','contact_linkedin_description'=>'Deskripsi kartu LinkedIn','contact_github_title'=>'Judul kartu GitHub','contact_github_description'=>'Deskripsi kartu GitHub','contact_form_title'=>'Judul form pesan','contact_form_button'=>'Teks tombol form',
];
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>
<div class="admin-card" style="max-width: 980px;">
  <div class="admin-card-header"><div><div class="admin-card-title">Konten halaman index</div><p style="font-size:var(--text-xs);color:var(--color-cream-muted);margin-top:.25rem">Profil, foto, sosial, CV, proyek, pencapaian, kreasi, dan tautan tetap dikelola dari menu masing-masing. Form ini mengatur seluruh salinan statis utama di beranda.</p></div></div>
  <?php if ($errorMessage): ?><div class="flash-alert flash-danger"><div><?= e($errorMessage) ?></div></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <?php foreach ($groups as $title => $keys): ?>
      <fieldset style="border:1px solid var(--color-line);border-radius:12px;padding:1.25rem;margin:0 0 1.25rem"><legend style="padding:0 .4rem;font-weight:700"><?= e($title) ?></legend>
        <div class="form-grid">
        <?php foreach ($keys as $key): $long = str_contains($key, 'text_') || str_contains($key, 'paragraph') || str_contains($key, 'description') || str_contains($key, '_lead'); ?>
          <div class="form-group" style="<?= $long ? 'grid-column:1 / -1' : '' ?>"><label class="form-label" for="<?= e($key) ?>"><?= e($labels[$key]) ?></label>
          <?php if ($long): ?><textarea class="form-textarea" id="<?= e($key) ?>" name="<?= e($key) ?>" rows="<?= str_contains($key, 'paragraph') ? '4' : '3' ?>"><?= e($content[$key]) ?></textarea><?php else: ?><input class="form-input" id="<?= e($key) ?>" name="<?= e($key) ?>" value="<?= e($content[$key]) ?>" <?= $key === 'hero_readiness' ? 'type="number" min="0" max="100"' : 'type="text"' ?>><?php endif; ?></div>
        <?php endforeach; ?>
        </div>
      </fieldset>
    <?php endforeach; ?>
    <div style="display:flex;gap:.75rem;align-items:center"><button class="btn btn-primary" type="submit">Simpan Konten Halaman Utama</button><a class="btn btn-secondary" target="_blank" href="<?= BASE_URL ?>/">Pratinjau Web ↗</a></div>
  </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
