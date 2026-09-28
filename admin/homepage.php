<?php
/**
 * Pengelola Konten Halaman Utama (Homepage)
 * Desain: Tabbed Modular Editor dengan Sticky Save Bar
 */

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
            set_flash('success', 'Konten halaman utama berhasil diperbarui!');
            redirect(BASE_URL . '/admin/homepage');
        } catch (JsonException $exception) {
            $errorMessage = 'Konten tidak dapat disimpan. Silakan coba kembali.';
        }
    }
}

$content = get_homepage_content($pdo);

$groups = [
    'nav' => [
        'title' => 'Navigasi & Brand',
        'icon'  => '🧭',
        'desc'  => 'Nama brand navbar (lengkap & singkatan), serta teks tombol aksi/rekrutmen.',
        'keys'  => ['nav_brand_full', 'nav_brand_short', 'nav_hire_text']
    ],
    'hero' => [
        'title' => 'Hero & Statistik',
        'icon'  => '⚡',
        'desc'  => 'Teks headline, gelar, ketersediaan rekrutmen, dan badge utama pada hero section.',
        'keys'  => ['hero_education_label','hero_degree','hero_field','hero_specialty_label','hero_specialty_value','hero_availability_label','hero_availability_value','hero_readiness','hero_line_one','hero_line_two','hero_line_three','hero_badge','hero_vertical_badge']
    ],
    'terminal' => [
        'title' => 'Terminal Console',
        'icon'  => '💻',
        'desc'  => 'Teks status live terminal mockup, fokus stack, workflow, dan simulasi pengujian.',
        'keys'  => [
            'terminal_title', 'terminal_status_badge', 'terminal_user', 
            'terminal_status_val', 'terminal_role_val', 'terminal_focus_val', 
            'terminal_ai_val', 'terminal_workflow_val', 'terminal_location_val', 
            'terminal_cmd_test', 'terminal_cmd_pass', 'terminal_summary_line1', 'terminal_summary_line2'
        ]
    ],
    'brand' => [
        'title' => 'Brand Zenerie',
        'icon'  => '💎',
        'desc'  => 'Kartu profil brand Zenerie, link website, dan tombol aksi.',
        'keys'  => ['brand_title','brand_badge','brand_description','brand_url','brand_button']
    ],
    'tech_chips' => [
        'title' => 'Tech Stack (Chips)',
        'icon'  => '🧩',
        'desc'  => 'Daftar skill chips interaktif di homepage dalam format JSON terstruktur.',
        'keys'  => ['tech_widget_title', 'tech_widget_status', 'tech_widget_footer', 'tech_widget_button', 'tech_chips_json']
    ],
    'gear' => [
        'title' => 'Gear & Workspace',
        'icon'  => '⚙️',
        'desc'  => 'Daftar hardware, toolset software, dan suite AI di tab gear & setup.',
        'keys'  => [
            'gear_hw_laptop_name', 'gear_hw_laptop_desc',
            'gear_hw_display_name', 'gear_hw_display_desc',
            'gear_hw_keyboard_name', 'gear_hw_keyboard_desc',
            'gear_hw_audio_name', 'gear_hw_audio_desc',
            'gear_sw_os_tags', 'gear_sw_ide_tags', 'gear_sw_tools_tags',
            'gear_ai_models_desc', 'gear_ai_prompt_desc', 'gear_ai_design_desc', 'gear_ai_devops_desc'
        ]
    ],
    'about_bento' => [
        'title' => 'Tentang (Bento)',
        'icon'  => '🪪',
        'desc'  => 'Teks dan metrik statistik pada kartu bento profil hero.',
        'keys'  => ['about_widget_title','about_widget_status','about_widget_text_one','about_widget_text_two','about_metric_one_value','about_metric_one_label','about_metric_two_value','about_metric_two_label']
    ],
    'about_section' => [
        'title' => 'Tentang Saya',
        'icon'  => '📖',
        'desc'  => 'Seluruh paragraf narasi profil, rekayasa sistem, dan latar belakang profesional.',
        'keys'  => ['about_caption','about_title','about_kicker','about_heading','about_paragraph_one','about_paragraph_two','about_paragraph_three']
    ],
    'skills' => [
        'title' => 'Keahlian',
        'icon'  => '🛠️',
        'desc'  => 'Caption, judul, dan lead paragraf pengantar section keahlian & toolkit.',
        'keys'  => ['skills_caption','skills_title','skills_lead']
    ],
    'projects' => [
        'title' => 'Proyek',
        'icon'  => '💼',
        'desc'  => 'Caption dan judul pengantar section karya & proyek.',
        'keys'  => ['projects_caption','projects_title']
    ],
    'contact' => [
        'title' => 'Bagian Kontak',
        'icon'  => '📬',
        'desc'  => 'Judul kartu email, jejaring profesional, sosial media, dan teks form pesan singkat.',
        'keys'  => ['contact_caption','contact_title','contact_email_title','contact_email_description','contact_linkedin_title','contact_linkedin_description','contact_github_title','contact_github_description','contact_instagram_title','contact_instagram_description','contact_tiktok_title','contact_tiktok_description','contact_form_title','contact_form_button']
    ],
    'oauth' => [
        'title' => 'Integrasi & OAuth',
        'icon'  => '🔑',
        'desc'  => 'Kredensial Google OAuth 2.0 untuk autentikasi Buku Tamu publik.',
        'keys'  => ['google_client_id', 'google_client_secret']
    ],
];

$labels = [
    'nav_brand_full' => 'Nama Brand Lengkap (Navbar)',
    'nav_brand_short' => 'Inisial / Brand Singkat (Navbar)',
    'nav_hire_text' => 'Teks Tombol Rekrut (Navbar)',

    'hero_education_label'=>'Label pendidikan','hero_degree'=>'Gelar singkat','hero_field'=>'Bidang pendidikan','hero_specialty_label'=>'Label spesialisasi','hero_specialty_value'=>'Nilai spesialisasi','hero_availability_label'=>'Label ketersediaan','hero_availability_value'=>'Nilai ketersediaan','hero_readiness'=>'Kesiapan teknis (0–100)','hero_line_one'=>'Hero baris 1','hero_line_two'=>'Hero baris 2','hero_line_three'=>'Hero baris 3','hero_badge'=>'Badge hero','hero_vertical_badge'=>'Badge vertikal',

    'terminal_title' => 'Judul Shell Terminal (contoh: ammar@zen:~$)',
    'terminal_status_badge' => 'Badge Status Terminal (contoh: ACTIVE)',
    'terminal_user' => 'User Prompt (contoh: zen@dev)',
    'terminal_status_val' => 'Nilai Status (contoh: ● Available for Hire)',
    'terminal_role_val' => 'Nilai Role (contoh: Fullstack & AI-Augmented Dev)',
    'terminal_focus_val' => 'Nilai Focus (contoh: Laravel 13 • MySQL • Next.js)',
    'terminal_ai_val' => 'Nilai AI Stack (contoh: Claude • Gemini • OpenAI)',
    'terminal_workflow_val' => 'Nilai Workflow (contoh: Agentic Acceleration (3x Speed))',
    'terminal_location_val' => 'Nilai Location (contoh: Jakarta, ID (GMT+7))',
    'terminal_cmd_test' => 'Perintah Simulasi Tes (contoh: pest test && eval-prompt)',
    'terminal_cmd_pass' => 'Teks Hasil Tes Lulus (contoh: Pest v3 • 18 passed (0.04s))',
    'terminal_summary_line1' => 'Baris Ringkasan 1 (contoh: ✓ 100% Bebas SQLi & Siap Produksi)',
    'terminal_summary_line2' => 'Baris Ringkasan 2 (contoh: ✓ Prompt Token Efficiency: 98.4%)',

    'brand_title'=>'Judul brand','brand_badge'=>'Badge brand','brand_description'=>'Deskripsi Zenerie','brand_url'=>'URL website Zenerie','brand_button'=>'Teks tombol',
    'identity_button'=>'Tombol profil','media_title'=>'Judul media','media_button'=>'Tombol media',
    
    'tech_widget_title'=>'Judul widget tech stack','tech_widget_status'=>'Status widget tech stack','tech_widget_footer'=>'Footer widget tech stack','tech_widget_button'=>'Tombol widget tech stack',
    'tech_chips_json' => 'Data JSON Skill Chips (name, category, role, highlight)',

    'gear_hw_laptop_name' => 'Nama Hardware: Mesin Utama (Laptop/PC)',
    'gear_hw_laptop_desc' => 'Spesifikasi Mesin Utama',
    'gear_hw_display_name' => 'Nama Hardware: Monitor / Layar',
    'gear_hw_display_desc' => 'Spesifikasi Monitor / Layar',
    'gear_hw_keyboard_name' => 'Nama Hardware: Keyboard / Input',
    'gear_hw_keyboard_desc' => 'Spesifikasi Keyboard / Mouse',
    'gear_hw_audio_name' => 'Nama Hardware: Audio / Monitor',
    'gear_hw_audio_desc' => 'Spesifikasi Audio / IEM',
    'gear_sw_os_tags' => 'Daftar Tag OS & Shell (pisahkan dengan koma)',
    'gear_sw_ide_tags' => 'Daftar Tag IDE & Editor (pisahkan dengan koma)',
    'gear_sw_tools_tags' => 'Daftar Tag Runtime, DB & API (pisahkan dengan koma)',
    'gear_ai_models_desc' => 'Deskripsi: Frontier AI Models',
    'gear_ai_prompt_desc' => 'Deskripsi: Prompt & Agent Engineering',
    'gear_ai_design_desc' => 'Deskripsi: Desain & Manajemen Ide',
    'gear_ai_devops_desc' => 'Deskripsi: DevOps & Hosting',

    'projects_caption'=>'Caption proyek','projects_title'=>'Judul proyek',
    'about_widget_title'=>'Judul kartu','about_widget_status'=>'Status','about_widget_text_one'=>'Paragraf 1','about_widget_text_two'=>'Paragraf 2','about_metric_one_value'=>'Metrik 1 nilai','about_metric_one_label'=>'Metrik 1 label','about_metric_two_value'=>'Metrik 2 nilai','about_metric_two_label'=>'Metrik 2 label',
    'about_caption'=>'Caption section','about_title'=>'Judul section','about_kicker'=>'Kicker','about_heading'=>'Judul panel','about_paragraph_one'=>'Paragraf 1','about_paragraph_two'=>'Paragraf 2','about_paragraph_three'=>'Paragraf 3',
    'skills_caption'=>'Caption section','skills_title'=>'Judul section','skills_lead'=>'Deskripsi section',
    'contact_caption'=>'Caption section','contact_title'=>'Judul section','contact_email_title'=>'Judul kartu email','contact_email_description'=>'Deskripsi kartu email','contact_linkedin_title'=>'Judul kartu LinkedIn','contact_linkedin_description'=>'Deskripsi kartu LinkedIn','contact_github_title'=>'Judul kartu GitHub','contact_github_description'=>'Deskripsi kartu GitHub','contact_instagram_title'=>'Judul kartu Instagram','contact_instagram_description'=>'Deskripsi kartu Instagram','contact_tiktok_title'=>'Judul kartu TikTok','contact_tiktok_description'=>'Deskripsi kartu TikTok','contact_form_title'=>'Judul form pesan','contact_form_button'=>'Teks tombol form',

    'google_client_id' => 'Google OAuth Client ID',
    'google_client_secret' => 'Google OAuth Client Secret',
];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<style>
  .hp-editor-wrap {
    max-width: 1040px;
  }
  .hp-tabs-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    margin-bottom: 1.25rem;
    background: rgba(15, 23, 42, 0.7);
    padding: 0.45rem;
    border-radius: 14px;
    border: 1px solid var(--color-line);
  }
  .hp-tab-btn {
    background: transparent;
    border: 1px solid transparent;
    color: var(--color-text-dim);
    padding: 0.55rem 0.95rem;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    transition: all 0.2s ease;
    white-space: nowrap;
  }
  .hp-tab-btn:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.05);
  }
  .hp-tab-btn.is-active {
    background: rgba(76, 141, 255, 0.22);
    border-color: rgba(76, 141, 255, 0.5);
    color: #ffffff;
    box-shadow: 0 0 14px rgba(76, 141, 255, 0.3);
  }
  .hp-sticky-bar {
    position: sticky;
    top: 0;
    z-index: 30;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    background: rgba(11, 15, 25, 0.95);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid rgba(76, 141, 255, 0.3);
    padding: 0.85rem 1.25rem;
    border-radius: 14px;
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
  }
  .hp-sticky-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  .hp-active-label {
    font-family: var(--font-display);
    font-size: 1rem;
    font-weight: 700;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 0.45rem;
  }
  .hp-section-panel {
    display: none;
    animation: hpFadeIn 0.25s ease-out;
  }
  .hp-section-panel.is-active {
    display: block;
  }
  .hp-show-all .hp-section-panel {
    display: block !important;
    margin-bottom: 2rem;
  }
  @keyframes hpFadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .hp-panel-card {
    border: 1px solid var(--color-line);
    border-radius: 16px;
    padding: 1.5rem;
    background: rgba(15, 23, 42, 0.4);
    margin-bottom: 1.5rem;
  }
  .hp-panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--color-line);
  }
  .hp-panel-title {
    font-family: var(--font-display);
    font-size: 1.1rem;
    font-weight: 700;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .hp-panel-desc {
    font-size: 0.8rem;
    color: var(--color-text-dim);
    margin-top: 0.25rem;
  }
  .hp-tab-nav-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 1.25rem;
    margin-top: 1.25rem;
    border-top: 1px solid var(--color-line);
  }
</style>

<div class="hp-editor-wrap">
  
  <div class="admin-card" style="margin-bottom: 1.25rem;">
    <div class="admin-card-header" style="margin-bottom: 0;">
      <div>
        <div class="admin-card-title">Konten Halaman Utama (Index Editor)</div>
        <p style="font-size: var(--text-xs); color: var(--color-cream-muted); margin-top: 0.25rem;">
          Pilih kategori di bawah untuk mengedit bagian tertentu tanpa harus scroll panjang. Tombol simpan tersedia langsung di atas!
        </p>
      </div>
      <div>
        <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: var(--text-xs); color: var(--color-text-dim); cursor: pointer;">
          <input type="checkbox" id="toggleShowAll">
          <span>Tampilkan Semua Section Sekaligus</span>
        </label>
      </div>
    </div>
  </div>

  <?php if ($errorMessage): ?>
    <div class="flash-alert flash-danger" style="margin-bottom: 1.25rem;">
      <div><?= e($errorMessage) ?></div>
    </div>
  <?php endif; ?>

  <form method="post" id="homepageForm">
    <?= csrf_field() ?>

    <!-- Sticky Action Bar (Tidak perlu scroll ke bawah untuk simpan!) -->
    <div class="hp-sticky-bar">
      <div class="hp-sticky-info">
        <span class="hp-active-label" id="currentSectionTitle">
          <span>⚡</span> Hero &amp; Statistik
        </span>
        <span style="font-size: 0.72rem; color: #34d399; font-weight: 600; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); padding: 0.15rem 0.5rem; border-radius: 9999px;">
          Siap Diedit
        </span>
      </div>
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <button class="btn btn-primary" type="submit" style="padding: 0.55rem 1.25rem; font-size: 0.85rem; font-weight: 700; box-shadow: 0 0 16px rgba(76, 141, 255, 0.4);">
          💾 Simpan Konten
        </button>
        <a class="btn btn-secondary" target="_blank" href="<?= BASE_URL ?>/" style="padding: 0.55rem 1rem; font-size: 0.85rem;">
          Pratinjau Web ↗
        </a>
      </div>
    </div>

    <!-- Category Tabs Navigation -->
    <div class="hp-tabs-bar" id="homepageTabsBar" role="tablist">
      <?php 
      $groupKeys = array_keys($groups);
      foreach ($groups as $id => $grp): 
        $isFirst = ($id === $groupKeys[0]);
      ?>
        <button type="button" 
                class="hp-tab-btn <?= $isFirst ? 'is-active' : '' ?>" 
                data-tab-target="panel-<?= e($id) ?>"
                data-tab-title="<?= e($grp['icon'] . ' ' . $grp['title']) ?>"
                role="tab" 
                aria-selected="<?= $isFirst ? 'true' : 'false' ?>">
          <span><?= $grp['icon'] ?></span>
          <span><?= e($grp['title']) ?></span>
          <span style="font-size: 0.7rem; opacity: 0.6;">(<?= count($grp['keys']) ?>)</span>
        </button>
      <?php endforeach; ?>
    </div>

    <!-- Form Panels -->
    <div id="homepagePanelsContainer">
      <?php 
      $totalGroups = count($groups);
      $groupIndex = 0;
      foreach ($groups as $id => $grp): 
        $isFirst = ($id === $groupKeys[0]);
        $prevId = $groupIndex > 0 ? $groupKeys[$groupIndex - 1] : null;
        $nextId = $groupIndex < ($totalGroups - 1) ? $groupKeys[$groupIndex + 1] : null;
      ?>
        <div class="hp-section-panel <?= $isFirst ? 'is-active' : '' ?>" id="panel-<?= e($id) ?>">
          <div class="hp-panel-card">
            
            <div class="hp-panel-header">
              <div>
                <div class="hp-panel-title">
                  <span><?= $grp['icon'] ?></span>
                  <span><?= e($grp['title']) ?></span>
                </div>
                <div class="hp-panel-desc"><?= e($grp['desc']) ?></div>
              </div>
              <button class="btn btn-primary btn-sm" type="submit">
                💾 Simpan Bagian Ini
              </button>
            </div>

            <div class="form-grid">
              <?php foreach ($grp['keys'] as $key): 
                $isJson = ($key === 'tech_chips_json');
                $isDesc = str_contains($key, '_desc') || str_contains($key, 'description') || str_contains($key, '_tags') || str_contains($key, 'text_') || str_contains($key, 'paragraph') || str_contains($key, '_lead');
                $long = $isJson || $isDesc;
              ?>
                <div class="form-group" style="<?= $long ? 'grid-column: 1 / -1;' : '' ?>">
                  <label class="form-label" for="<?= e($key) ?>">
                    <?= e($labels[$key] ?? $key) ?>
                    <code style="font-size: 0.7rem; color: var(--color-text-faint); margin-left: 0.35rem; font-weight: 400;">(<?= e($key) ?>)</code>
                  </label>
                  
                  <?php if ($isJson): ?>
                    <div style="font-size: 0.75rem; color: var(--color-text-dim); margin-bottom: 0.5rem;">
                      💡 Tips: Format JSON Array berisi objek dengan properti <code>name</code>, <code>category</code> (frontend, backend, database, devops, ai, testing, mobile, analytics), <code>role</code>, dan <code>highlight</code> (true/false).
                    </div>
                    <textarea class="form-textarea" 
                              id="<?= e($key) ?>" 
                              name="<?= e($key) ?>" 
                              rows="12" 
                              style="font-family: 'JetBrains Mono', 'Fira Code', Consolas, monospace; font-size: 0.8rem; background: rgba(10,14,23,0.85); color: #38bdf8;"
                              placeholder="[ { &quot;name&quot;: &quot;Next.js&quot;, ... } ]"><?= e($content[$key] ?? '') ?></textarea>
                  <?php elseif ($long): ?>
                    <textarea class="form-textarea" 
                              id="<?= e($key) ?>" 
                              name="<?= e($key) ?>" 
                              rows="<?= (str_contains($key, 'paragraph') || str_contains($key, '_tags')) ? '3' : '2' ?>" 
                              placeholder="Masukkan teks..."><?= e($content[$key] ?? '') ?></textarea>
                  <?php else: ?>
                    <input class="form-input" 
                           id="<?= e($key) ?>" 
                           name="<?= e($key) ?>" 
                           value="<?= e($content[$key] ?? '') ?>" 
                           <?= $key === 'hero_readiness' ? 'type="number" min="0" max="100"' : 'type="text"' ?>
                           placeholder="Masukkan nilai...">
                  <?php endif; ?>

                  <?php if ($key === 'google_client_id' || $key === 'google_client_secret'): ?>
                    <div style="font-size: 0.72rem; color: var(--color-text-dim); margin-top: 0.35rem;">
                      Diambil dari Google Cloud Console &gt; APIs &amp; Services &gt; Credentials (OAuth 2.0 Client IDs). Jika dikosongkan, tombol login Google di Buku Tamu akan otomatis disembunyikan/dinonaktifkan secara aman.
                    </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Bottom Navigation Bar inside each Panel -->
            <div class="hp-tab-nav-footer">
              <div>
                <?php if ($prevId): ?>
                  <button type="button" class="btn btn-secondary btn-sm btn-nav-jump" data-jump-target="panel-<?= e($prevId) ?>">
                    &larr; <?= e($groups[$prevId]['title']) ?>
                  </button>
                <?php endif; ?>
              </div>
              
              <div style="display: flex; gap: 0.75rem;">
                <button class="btn btn-primary btn-sm" type="submit">
                  💾 Simpan Perubahan
                </button>
                
                <?php if ($nextId): ?>
                  <button type="button" class="btn btn-secondary btn-sm btn-nav-jump" data-jump-target="panel-<?= e($nextId) ?>">
                    <?= e($groups[$nextId]['title']) ?> &rarr;
                  </button>
                <?php endif; ?>
              </div>
            </div>

          </div>
        </div>
      <?php 
        $groupIndex++;
      endforeach; 
      ?>
    </div>

  </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const tabButtons = document.querySelectorAll('#homepageTabsBar .hp-tab-btn');
  const panels = document.querySelectorAll('#homepagePanelsContainer .hp-section-panel');
  const stickyTitle = document.getElementById('currentSectionTitle');
  const toggleShowAll = document.getElementById('toggleShowAll');
  const container = document.getElementById('homepagePanelsContainer');

  function switchTab(targetId) {
    if (toggleShowAll.checked) {
      const el = document.getElementById(targetId);
      if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
      return;
    }

    tabButtons.forEach(btn => {
      const isMatch = (btn.dataset.tabTarget === targetId);
      btn.classList.toggle('is-active', isMatch);
      btn.setAttribute('aria-selected', isMatch ? 'true' : 'false');
      if (isMatch && stickyTitle) {
        stickyTitle.innerHTML = btn.dataset.tabTitle;
      }
    });

    panels.forEach(panel => {
      panel.classList.toggle('is-active', panel.id === targetId);
    });

    // Save active tab preference
    localStorage.setItem('admin_hp_active_tab', targetId);
  }

  tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      switchTab(btn.dataset.tabTarget);
    });
  });

  document.querySelectorAll('.btn-nav-jump').forEach(btn => {
    btn.addEventListener('click', () => {
      switchTab(btn.dataset.jumpTarget);
      window.scrollTo({ top: 120, behavior: 'smooth' });
    });
  });

  toggleShowAll?.addEventListener('change', (e) => {
    if (e.target.checked) {
      container.classList.add('hp-show-all');
      stickyTitle.innerHTML = '<span>📋</span> Seluruh Section (Semua Konten)';
    } else {
      container.classList.remove('hp-show-all');
      const savedTab = localStorage.getItem('admin_hp_active_tab') || 'panel-hero';
      switchTab(savedTab);
    }
  });

  // Restore active tab
  const savedTab = localStorage.getItem('admin_hp_active_tab');
  if (savedTab && document.getElementById(savedTab)) {
    switchTab(savedTab);
  }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
