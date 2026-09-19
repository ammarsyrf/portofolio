<?php
/**
 * Markup Overlay Command Palette (⌘K)
 * Di-include di bagian bawah (sebelum </body>) di seluruh halaman publik
 */
?>
<!-- Command Palette Overlay -->
<div class="palette-overlay" id="commandPalette" role="dialog" aria-modal="true" aria-label="Command Palette">
  <div class="palette-dialog">
    <div class="palette-search-box">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-accent-bright); flex-shrink: 0;">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input type="text" id="paletteSearchInput" class="palette-input" placeholder="Ketik halaman atau aksi tujuan..." autocomplete="off" spellcheck="false">
      <span class="kbd-shortcut" style="font-size: 0.7rem;">ESC</span>
    </div>

    <div class="palette-results-list" id="paletteResultsList">
      <!-- Di-render dinamis oleh command_palette.js -->
    </div>

    <div class="palette-footer-hint">
      <span>Gunakan <kbd class="kbd-shortcut">&uarr;</kbd> <kbd class="kbd-shortcut">&darr;</kbd> untuk navigasi</span>
      <span><kbd class="kbd-shortcut">&crarr;</kbd> untuk memilih</span>
    </div>
  </div>
</div>
