/**
 * Command Palette (⌘K / Ctrl+K) Logic
 * Pencarian cepat client-side untuk navigasi instan
 */

document.addEventListener('DOMContentLoaded', () => {
  const paletteOverlay = document.getElementById('commandPalette');
  const paletteInput = document.getElementById('paletteSearchInput');
  const paletteList = document.getElementById('paletteResultsList');
  const paletteTriggerBtn = document.getElementById('paletteBtn');

  if (!paletteOverlay || !paletteInput || !paletteList) return;

  // Base URL dari window location
  const baseUrl = window.location.origin + (window.location.pathname.includes('/porto') ? '/porto' : '');

  // Daftar tujuan statis
  const commandItems = [
    { label: 'Beranda (Home)', category: 'Halaman', url: baseUrl + '/#hero', icon: '🏠' },
    { label: 'Tentang Ammar (About)', category: 'Section', url: baseUrl + '/#about', icon: '👤' },
    { label: 'Keahlian & Toolkit (Skills)', category: 'Section', url: baseUrl + '/#skills', icon: '⚡' },
    { label: 'Koleksi Proyek (Projects)', category: 'Section', url: baseUrl + '/#projects', icon: '💼' },
    { label: 'GitHub Repository Showcase', category: 'Section', url: baseUrl + '/#github-showcase', icon: '🐙' },
    { label: 'Pencapaian & Sertifikat (Achievements)', category: 'Halaman', url: baseUrl + '/achievements', icon: '🏆' },
    { label: 'Kreasi TikTok & Instagram (Creations)', category: 'Halaman', url: baseUrl + '/creations', icon: '🎬' },
    { label: 'Buku Tamu Pengunjung (Guestbook)', category: 'Halaman', url: baseUrl + '/guestbook', icon: '📖' },
    { label: 'Semua Tautan Penting (Links)', category: 'Halaman', url: baseUrl + '/links', icon: '🔗' },
    { label: 'Hubungi Kontak Langsung (Contact)', category: 'Aksi', url: baseUrl + '/#contact', icon: '📬' },
    { label: 'Unduh Berkas CV (PDF)', category: 'Aksi', url: baseUrl + '/links', icon: '📄' },
    { label: 'Portal Administrator', category: 'Admin', url: baseUrl + '/admin/login', icon: '⚙️' }
  ];

  let selectedIndex = 0;
  let filteredItems = [...commandItems];

  function renderResults() {
    paletteList.innerHTML = '';

    if (filteredItems.length === 0) {
      paletteList.innerHTML = `
        <div style="padding: 1.5rem; text-align: center; color: var(--color-text-faint); font-size: 0.85rem;">
          Tidak ada tujuan yang cocok dengan pencarian Anda.
        </div>
      `;
      return;
    }

    filteredItems.forEach((item, index) => {
      const itemEl = document.createElement('div');
      itemEl.className = 'palette-item' + (index === selectedIndex ? ' is-selected' : '');
      itemEl.innerHTML = `
        <div class="palette-item-left">
          <span>${item.icon}</span>
          <span>${item.label}</span>
        </div>
        <span class="palette-item-badge">${item.category}</span>
      `;

      itemEl.addEventListener('click', () => {
        executeItem(item);
      });

      paletteList.appendChild(itemEl);
    });

    // Auto scroll ke item terpilih
    const activeEl = paletteList.children[selectedIndex];
    if (activeEl) {
      activeEl.scrollIntoView({ block: 'nearest' });
    }
  }

  function executeItem(item) {
    closePalette();
    if (item.url.startsWith('#') || item.url.includes('#')) {
      window.location.href = item.url;
    } else {
      window.location.href = item.url;
    }
  }

  function openPalette() {
    paletteOverlay.classList.add('is-active');
    paletteInput.value = '';
    filteredItems = [...commandItems];
    selectedIndex = 0;
    renderResults();
    setTimeout(() => paletteInput.focus(), 50);
  }

  function closePalette() {
    paletteOverlay.classList.remove('is-active');
  }

  // Trigger via button
  paletteTriggerBtn?.addEventListener('click', openPalette);

  // Global Keyboard Shortcut: Cmd+K (Mac) / Ctrl+K (Windows/Linux)
  document.addEventListener('keydown', (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      if (paletteOverlay.classList.contains('is-active')) {
        closePalette();
      } else {
        openPalette();
      }
    } else if (e.key === 'Escape' && paletteOverlay.classList.contains('is-active')) {
      closePalette();
    }
  });

  // Filter input
  paletteInput.addEventListener('input', () => {
    const query = paletteInput.value.toLowerCase().trim();
    if (!query) {
      filteredItems = [...commandItems];
    } else {
      filteredItems = commandItems.filter(item => 
        item.label.toLowerCase().includes(query) ||
        item.category.toLowerCase().includes(query)
      );
    }
    selectedIndex = 0;
    renderResults();
  });

  // Arrow Key & Enter Navigation
  paletteInput.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      if (filteredItems.length > 0) {
        selectedIndex = (selectedIndex + 1) % filteredItems.length;
        renderResults();
      }
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      if (filteredItems.length > 0) {
        selectedIndex = (selectedIndex - 1 + filteredItems.length) % filteredItems.length;
        renderResults();
      }
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (filteredItems[selectedIndex]) {
        executeItem(filteredItems[selectedIndex]);
      }
    }
  });

  // Backdrop click close
  paletteOverlay.addEventListener('click', (e) => {
    if (e.target === paletteOverlay) {
      closePalette();
    }
  });

  // Expose helper globally
  window.openCommandPalette = openPalette;
});
