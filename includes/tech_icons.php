<?php
/**
 * Ultra-Comprehensive Authentic SVG Tech Stack & Skill Icon Library
 * Provides pixel-perfect, authentic brand SVGs with multi-color & monocolor styling.
 */

if (!function_exists('get_tech_svg_icon')) {
    /**
     * Mengambil SVG icon otentik berdasarkan nama teknologi / skill
     * 
     * @param string $techName Nama teknologi (misal: "Next.js", "Laravel", "Docker", "MySQL", "React", "Algoritma C4.5")
     * @param int $size Ukuran icon dalam piksel (default: 16)
     * @param string $extraClass Kelas CSS tambahan
     * @return string Kode HTML/SVG
     */
    function get_tech_svg_icon(string $techName, int $size = 16, string $extraClass = ''): string
    {
        $raw = trim($techName);
        // Penting: strtolower() DULU sebelum regex agar huruf kapital tidak hilang!
        $clean = preg_replace('/[^a-z0-9]/', '', strtolower($raw));
        $w = (int)$size;
        $h = (int)$size;
        $cls = $extraClass ? ' class="' . htmlspecialchars($extraClass) . '"' : '';

        // ── 1. FRONTEND, FRAMEWORKS & STYLING ──
        
        // React
        if (str_contains($clean, 'react') && !str_contains($clean, 'reactnative')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="-11.5 -10.23174 23 20.46348" fill="none"'.$cls.'><circle cx="0" cy="0" r="2.05" fill="#61DAFB"/><g stroke="#61DAFB" stroke-width="1"><ellipse rx="11" ry="4.2"/><ellipse rx="11" ry="4.2" transform="rotate(60)"/><ellipse rx="11" ry="4.2" transform="rotate(120)"/></g></svg>';
        }

        // Next.js
        if (str_contains($clean, 'next') || str_contains($clean, 'approuter')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 180 180" fill="none"'.$cls.'><circle cx="90" cy="90" fill="#000" r="90"/><path d="M149.5 157.5L69.1 54H54v72h12.1V69.4l73.9 95.5c3.3-2.3 6.5-4.7 9.5-7.4z" fill="#fff"/><rect fill="#fff" height="72" width="12" x="115" y="54" opacity="0.85"/></svg>';
        }

        // TypeScript
        if (str_contains($clean, 'typescript') || $clean === 'ts') {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><rect width="24" height="24" rx="4" fill="#3178C6"/><path d="M11.5 8.5H5.5V10.2H7.5V18.5H9.5V10.2H11.5V8.5Z" fill="#FFFFFF"/><path d="M17.8 11.2C17.4 10.4 16.5 9.9 15.3 9.9C13.8 9.9 12.8 10.8 12.8 12.1C12.8 14.8 16.8 13.8 16.8 15.6C16.8 16.3 16.1 16.8 15.1 16.8C14.1 16.8 13.3 16.2 13 15.3L11.5 16.1C12 17.5 13.4 18.5 15.1 18.5C17 18.5 18.7 17.4 18.7 15.5C18.7 12.8 14.7 13.7 14.7 12C14.7 11.5 15.2 11.1 16 11.1C16.7 11.1 17.2 11.4 17.5 11.9L17.8 11.2Z" fill="#FFFFFF"/></svg>';
        }

        // JavaScript
        if (str_contains($clean, 'javascript') || $clean === 'js' || str_contains($clean, 'es6')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><rect width="24" height="24" rx="4" fill="#F7DF1E"/><path d="M7 17.5L8.5 16.6C8.8 17.2 9.2 17.6 9.8 17.6C10.5 17.6 11 17.2 11 16.2V11H12.8V16.2C12.8 18.2 11.6 19 10 19C8.6 19 7.6 18.3 7 17.5ZM14.2 17.3L15.7 16.4C16.1 17.1 16.8 17.6 17.6 17.6C18.4 17.6 18.9 17.2 18.9 16.6C18.9 15.9 18.4 15.6 17.4 15.2L16.9 15C15.3 14.3 14.4 13.5 14.4 12C14.4 10.5 15.6 9.4 17.3 9.4C18.6 9.4 19.6 10 20.2 11.1L18.7 12C18.3 11.4 17.8 11.1 17.2 11.1C16.6 11.1 16.2 11.5 16.2 12C16.2 12.6 16.6 12.9 17.4 13.2L17.9 13.4C19.7 14.2 20.7 15 20.7 16.5C20.7 18.1 19.4 19.2 17.6 19.2C15.9 19.2 14.7 18.4 14.2 17.3Z" fill="#000000"/></svg>';
        }

        // Tailwind CSS
        if (str_contains($clean, 'tailwind')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M12.001 4.8c-3.2 0-5.2 1.6-6 4.8 1.2-1.6 2.6-2.2 4.2-1.8.913.228 1.565.89 2.288 1.624C13.666 10.618 15.027 12 18.001 12c3.2 0 5.2-1.6 6-4.8-1.2 1.6-2.6 2.2-4.2 1.8-.913-.228-1.565-.89-2.288-1.624C16.336 6.182 14.975 4.8 12.001 4.8zm-6 7.2c-3.2 0-5.2 1.6-6 4.8 1.2-1.6 2.6-2.2 4.2-1.8.913.228 1.565.89 2.288 1.624 1.177 1.194 2.538 2.576 5.512 2.576 3.2 0 5.2-1.6 6-4.8-1.2 1.6-2.6 2.2-4.2 1.8-.913-.228-1.565-.89-2.288-1.624C10.336 13.382 8.975 12 6.001 12z" fill="#38BDF8"/></svg>';
        }

        // Vue.js
        if (str_contains($clean, 'vue')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M1.5 3.5h4.25L12 14.25 18.25 3.5H22.5L12 21.5 1.5 3.5z" fill="#41B883"/><path d="M5.75 3.5H9.5L12 7.75 14.5 3.5h3.75L12 14.25 5.75 3.5z" fill="#35495E"/></svg>';
        }

        // HTML / HTML5 / Semantik
        if (str_contains($clean, 'html') || str_contains($clean, 'semantik')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M3 2l1.6 18.2L12 22.5l7.4-2.3L21 2H3zm14.8 5.6h-8.2l.2 2.4h7.8l-.6 6.8-4.2 1.2-4.2-1.2-.3-3.2h2.2l.2 1.6 2.1.6 2.1-.6.3-2.6H7.4L6.8 5.2h11.2l-.2 2.4z" fill="#E34F26"/></svg>';
        }

        // CSS / CSS3 / Glassmorphism
        if (str_contains($clean, 'css') || str_contains($clean, 'glassmorphism') || str_contains($clean, 'vanillacss')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M3 2l1.6 18.2L12 22.5l7.4-2.3L21 2H3zm14.8 5.6h-8.2l.2 2.4h7.8l-.6 6.8-4.2 1.2-4.2-1.2-.3-3.2h2.2l.2 1.6 2.1.6 2.1-.6.3-2.6H7.4L6.8 5.2h11.2l-.2 2.4z" fill="#1572B6"/></svg>';
        }

        // Bootstrap
        if (str_contains($clean, 'bootstrap')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><rect width="24" height="24" rx="5" fill="#7952B3"/><path d="M8 6h4.5c1.7 0 2.8.9 2.8 2.3 0 1-.6 1.8-1.6 2.1 1.3.3 2.1 1.2 2.1 2.5 0 1.6-1.3 2.6-3.1 2.6H8V6zm2.4 3.7h1.9c.7 0 1.2-.4 1.2-1s-.5-1-1.2-1h-1.9v2zm0 3.8v2.3h2.2c.8 0 1.3-.4 1.3-1.1 0-.7-.5-1.2-1.3-1.2h-2.2z" fill="#FFFFFF"/></svg>';
        }

        // Shadcn UI
        if (str_contains($clean, 'shadcn')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 256 256" fill="none" stroke="#FFFFFF" stroke-width="22" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><line x1="208" y1="128" x2="128" y2="208"/><line x1="192" y1="40" x2="40" y2="192"/></svg>';
        }

        // Alpine.js
        if (str_contains($clean, 'alpine')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M18 7.5L24 13.5L18 19.5L12 13.5L18 7.5Z" fill="#77C1D2"/><path d="M6 7.5L18 19.5H6L0 13.5L6 7.5Z" fill="#2D3748"/></svg>';
        }

        // Inertia.js
        if (str_contains($clean, 'inertia')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M4 17L8.5 12.5L4 8H9.5L14 12.5L9.5 17H4Z" fill="#9553E9"/><path d="M12 17L16.5 12.5L12 8H17.5L22 12.5L17.5 17H12Z" fill="#B47BFF"/></svg>';
        }

        // Blade Template / Laravel Blade
        if (str_contains($clean, 'blade')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M12 2L3 7l9 5 9-5-9-5zm0 8L3 15l9 5 9-5-9-5z" fill="#FF2D20"/></svg>';
        }

        // Livewire
        if (str_contains($clean, 'livewire')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><circle cx="6" cy="12" r="3" fill="#FB70A9"/><circle cx="12" cy="12" r="4" fill="#FB70A9" opacity="0.6"/><circle cx="18" cy="12" r="3" fill="#FB70A9"/></svg>';
        }

        // ── 2. BACKEND & LANGUAGES ──

        // Laravel & Pint
        if (str_contains($clean, 'laravel') || str_contains($clean, 'pint')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M12 2L2.5 7.5v9L12 22l9.5-5.5v-9L12 2zm0 2.3l7 4-7 4-7-4 7-4zm-8 6l7 4v7.4l-7-4.1V10.3zm9 11.4v-7.4l7-4v7.3l-7 4.1z" fill="#FF2D20"/></svg>';
        }

        // PHP / PHP Native / PHP 8.x
        if (str_contains($clean, 'php') && !str_contains($clean, 'pest') && !str_contains($clean, 'stan') && !str_contains($clean, 'unit')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><rect width="24" height="24" rx="4" fill="#777BB4"/><path d="M6.5 8h2.8c1.3 0 2.2.8 2.2 2s-.9 2-2.2 2H8v3H6.5V8zm1.5 2.7h1.2c.6 0 1-.3 1-.8s-.4-.8-1-.8H8v1.6zm5 0h2.8c1.3 0 2.2.8 2.2 2s-.9 2-2.2 2H16v3h-1.5V8zm1.5 2.7h1.2c.6 0 1-.3 1-.8s-.4-.8-1-.8H16v1.6z" fill="#FFFFFF"/></svg>';
        }

        // Python
        if (str_contains($clean, 'python')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M11.9 2c-5 0-4.7 2.2-4.7 2.2l.1 2.3h4.7v.7H5.2S2 6.8 2 11.8s2.8 4.9 2.8 4.9h1.7v-2.4s-.1-2.8 2.8-2.8h4.7s2.7.1 2.7-2.6V4.7S17 2 11.9 2zm-2.6 1.5c.5 0 .9.4.9.9s-.4.9-.9.9-.9-.4-.9-.9.4-.9.9-.9z" fill="#3776AB"/><path d="M12.1 22c5 0 4.7-2.2 4.7-2.2l-.1-2.3H12v-.7h6.8s3.2.4 3.2-4.6-2.8-4.9-2.8-4.9h-1.7v2.4s.1 2.8-2.8 2.8H10s-2.7-.1-2.7 2.6v4.2S7 22 12.1 22zm2.6-1.5c-.5 0-.9-.4-.9-.9s.4-.9.9-.9.9.4.9.9-.4.9-.9.9z" fill="#FFD43B"/></svg>';
        }

        // Node.js
        if (str_contains($clean, 'nodejs') || (str_contains($clean, 'node') && !str_contains($clean, 'deno'))) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M12 2l9.5 5.5v11L12 24 2.5 18.5v-11L12 2z" fill="#339933"/><path d="M12 6.5l5.5 3.2v6.4L12 19.3l-5.5-3.2V9.7L12 6.5z" fill="#FFFFFF" opacity="0.9"/></svg>';
        }

        // Webhook Listeners / Webhook
        if (str_contains($clean, 'webhook')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#A855F7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><path d="M18 16.98h-5.99c-1.1 0-1.95.94-2.48 1.9A4 4 0 0 1 2 17c0-2.21 1.79-4 4-4h1"/><circle cx="18" cy="17" r="3"/><circle cx="6" cy="17" r="3"/><circle cx="18" cy="7" r="3"/><path d="M18 10V7a3 3 0 0 0-3-3H9a3 3 0 0 0-3 3v4"/></svg>';
        }

        // Queue / Jobs Worker / Horizon / Worker
        if (str_contains($clean, 'queue') || str_contains($clean, 'job') || str_contains($clean, 'worker')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><rect x="2" y="4" width="20" height="4" rx="1"/><rect x="4" y="10" width="16" height="4" rx="1"/><rect x="6" y="16" width="12" height="4" rx="1"/><path d="M18 12l3 3-3 3"/></svg>';
        }

        // Scheduler / Cron / Automated Tasks
        if (str_contains($clean, 'scheduler') || str_contains($clean, 'cron')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#06B6D4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><circle cx="12" cy="12" r="9"/><polyline points="12 6 12 12 16 14"/><path d="M12 3v-2M12 23v-2M3 12h-2M23 12h-2"/></svg>';
        }

        // REST API / Web Service / Endpoint / Middleware / JSON API
        if (str_contains($clean, 'api') || str_contains($clean, 'rest') || str_contains($clean, 'endpoint') || str_contains($clean, 'middleware') || str_contains($clean, 'service')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#38BDF8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>';
        }

        // Arsitektur MVC / Repository Pattern / Clean Architecture
        if (str_contains($clean, 'mvc') || str_contains($clean, 'arsitektur') || str_contains($clean, 'repository') || str_contains($clean, 'architecture') || str_contains($clean, 'clean')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#A855F7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>';
        }

        // ── 3. DATABASE & STORAGE ──

        // MySQL
        if (str_contains($clean, 'mysql')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M19.5 12.5C18.8 9.2 16.5 6.7 13.8 6.5c-.3 0-.6.1-.8.2C11.5 5.4 9.6 5 8 5.5c-2.8.9-4.8 3.8-5 7.1-.1 1.6.4 3.1 1.3 4.2 1.2 1.5 3.1 2.3 5.2 2.2 2.6-.1 5-1.4 6.8-3.5 1.5-1.7 2.6-2.5 3.2-3.2z" fill="#00758F"/><path d="M19.5 12.5c-.7.5-2.2 1.3-4 1.5-2.3.2-4.5-.4-6-1.8-.4-.4-.8-.9-1-1.4 1.1-.3 2.4-.4 3.6-.2 2.1.3 4 1.2 5.5 2.1.8.5 1.4.9 1.9-.2z" fill="#F29111"/></svg>';
        }

        // MariaDB
        if (str_contains($clean, 'mariadb')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9-4.03-9-9-9zm0 15c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z" fill="#003545"/><path d="M12 6v6l4 2" stroke="#C88E3E" stroke-width="2"/></svg>';
        }

        // PostgreSQL
        if (str_contains($clean, 'postgres') || str_contains($clean, 'psql')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 16.5v-3h2v-2h-2v-2h3V9.5h-3V7h-2v2.5H9V11h2v2H9v2h2v3.5h2z" fill="#336791"/></svg>';
        }

        // TablePlus / SQLite
        if (str_contains($clean, 'tableplus') || str_contains($clean, 'sqlite')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 9v12M15 9v12"/></svg>';
        }

        // Redis / In-Memory Cache
        if (str_contains($clean, 'redis') || str_contains($clean, 'cache') || str_contains($clean, 'inmemory')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M12 2L2 7l10 5 10-5-10-5zm0 8L2 15l10 5 10-5-10-5zm0 7l-7-3.5L2 17l10 5 10-5-3-1.5-7 3.5z" fill="#DC382D"/></svg>';
        }

        // PDO Prepared Stmt / Security Database
        if (str_contains($clean, 'pdo') || str_contains($clean, 'prepared')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>';
        }

        // Query Indexing & ACID / Normalisasi / Relasional ERD (3NF) / DFD
        if (str_contains($clean, 'erd') || str_contains($clean, 'dfd') || str_contains($clean, '3nf') || str_contains($clean, '1nf') || str_contains($clean, 'normaliz') || str_contains($clean, 'acid') || str_contains($clean, 'index') || str_contains($clean, 'tuning') || str_contains($clean, 'rdbms') || str_contains($clean, 'relasion') || str_contains($clean, 'eloquent') || str_contains($clean, 'database') || str_contains($clean, 'sql')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#4C8DFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>';
        }

        // ── 4. DEVOPS, SERVERS & CLOUD ──

        // Docker
        if (str_contains($clean, 'docker') || str_contains($clean, 'container')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M22.5 11c-.4-.3-1.5-.4-2.3.1-.2-.8-.8-1.5-1.7-1.8-.4-.2-.9-.2-1.4-.2-.5-1.5-1.9-2.5-3.6-2.5-1.2 0-2.3.5-3 1.4H4c-.8 0-1.5.7-1.5 1.5v3.5c0 3.3 2.7 6 6 6h7c3.3 0 6-2.7 6-6v-.5c.7-.4 1.3-.9 1-1.5zM6.5 10h2v2h-2v-2zm3 0h2v2h-2v-2zm3 0h2v2h-2v-2zm-6-3h2v2h-2V7zm3 0h2v2h-2V7zm3 0h2v2h-2V7zm3 3h2v2h-2v-2z" fill="#2496ED"/></svg>';
        }

        // Git / GitHub
        if (str_contains($clean, 'github')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" fill="#FFFFFF"/></svg>';
        }
        if (str_contains($clean, 'git') && !str_contains($clean, 'digit')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M21.6 10.8L13.2 2.4c-.6-.6-1.5-.6-2.1 0L9 4.5l2.7 2.7c.6-.2 1.3 0 1.8.5.5.5.7 1.2.5 1.8l2.6 2.6c.6-.2 1.3 0 1.8.5.7.7.7 1.9 0 2.6-.7.7-1.9.7-2.6 0-.6-.6-.7-1.4-.4-2.1L12.9 10v4.8c.2.2.4.4.5.6.7.7.7 1.9 0 2.6-.7.7-1.9.7-2.6 0-.7-.7-.7-1.9 0-2.6.2-.2.5-.4.8-.5V9.8c-.3-.1-.6-.3-.8-.5-.6-.6-.7-1.4-.4-2.1L7.7 4.5 2.4 9.8c-.6.6-.6 1.5 0 2.1l8.4 8.4c.6.6 1.5.6 2.1 0l8.7-8.7c.6-.6.6-1.5 0-2.1z" fill="#F05032"/></svg>';
        }

        // Composer Package Manager
        if (str_contains($clean, 'composer') || str_contains($clean, 'packagemgr') || str_contains($clean, 'packagist')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><path d="M16.5 9.4 7.55 4.24a1.78 1.78 0 0 0-2.5 1.55v12.42a1.78 1.78 0 0 0 2.5 1.55L16.5 14.6a1.78 1.78 0 0 0 0-3.2z"/><polyline points="21 16 21 8"/></svg>';
        }

        // Linux / Ubuntu / VPS / WSL2
        if (str_contains($clean, 'linux') || str_contains($clean, 'ubuntu') || str_contains($clean, 'wsl') || str_contains($clean, 'vps') || str_contains($clean, 'serveros') || str_contains($clean, 'ssh') || str_contains($clean, 'hosting')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M12 2a4 4 0 00-4 4v5a4 4 0 008 0V6a4 4 0 00-4-4zm-2 4a2 2 0 114 0v1a2 2 0 11-4 0V6zm6.8 7.3c-.5-.7-1.2-1.1-1.8-1.3v1a3 3 0 01-6 0v-1c-.6.2-1.3.6-1.8 1.3-1.2 1.7-1.2 4-.3 5.7h12.2c.9-1.7.9-4-.3-5.7zM7 21a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z" fill="#FCC624"/></svg>';
        }

        // Nginx
        if (str_contains($clean, 'nginx') || str_contains($clean, 'reverseproxy')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M12 2L2 7.5v9L12 22l10-5.5v-9L12 2zm6 13.5l-2.5-3.5v3.5H14V8.5h1.5l2.5 3.5V8.5H18v7z" fill="#009639"/></svg>';
        }

        // Apache
        if (str_contains($clean, 'apache')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M12.5 2.5C11 5 8 10 7 13.5c-.7 2.4-.2 4.5 1.5 6 1.7 1.5 4 1.5 6 0 1.7-1.5 2.2-3.6 1.5-6-1-3.5-4-8.5-5.5-11z" fill="#D22128"/><path d="M12.5 2.5v17" stroke="#F6921E" stroke-width="1.5"/></svg>';
        }

        // Supervisor Daemon / Process Manager
        if (str_contains($clean, 'supervisor') || str_contains($clean, 'daemon')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>';
        }

        // Cloudflare / CDN
        if (str_contains($clean, 'cloudflare') || str_contains($clean, 'cdn')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M18.8 11.2c-.4-2.8-2.8-4.9-5.7-4.9-2.2 0-4.1 1.2-5.1 3-1.8.3-3.2 1.9-3.2 3.8 0 2.1 1.7 3.8 3.8 3.8h10.2c1.9 0 3.4-1.5 3.4-3.4 0-1.8-1.4-3.2-3.4-3.3z" fill="#F38020"/></svg>';
        }

        // CI/CD / Automated Deployment / Actions
        if (str_contains($clean, 'cicd') || str_contains($clean, 'deploy') || str_contains($clean, 'automate') || str_contains($clean, 'action')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#34D399" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/><circle cx="12" cy="12" r="2" fill="#34D399"/></svg>';
        }

        // SSL / TLS / Security / HTTPS
        if (str_contains($clean, 'ssl') || str_contains($clean, 'tls') || str_contains($clean, 'https') || str_contains($clean, 'encrypt') || str_contains($clean, 'security') || str_contains($clean, 'auth')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#34D399" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>';
        }

        // Postman / Insomnia / API Testing
        if (str_contains($clean, 'postman') || str_contains($clean, 'insomnia')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><circle cx="12" cy="12" r="10" fill="#FF6C37"/><path d="M12 7l4 5-4 5-4-5 4-5z" fill="#FFFFFF"/></svg>';
        }

        // Windows Terminal / PowerShell / Shell / Starship Prompt
        if (str_contains($clean, 'powershell') || str_contains($clean, 'terminal') || str_contains($clean, 'starship') || str_contains($clean, 'shell') || str_contains($clean, 'windows')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#38BDF8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/></svg>';
        }

        // ── 5. TESTING & QUALITY ──

        // Pest PHP
        if (str_contains($clean, 'pest')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><rect width="24" height="24" rx="4" fill="#18181B"/><path d="M6 7h8a4 4 0 014 4v0a4 4 0 01-4 4H6V7z" fill="#F43F5E"/><circle cx="10" cy="11" r="1.5" fill="#FFFFFF"/></svg>';
        }

        // PHPUnit
        if (str_contains($clean, 'phpunit') || str_contains($clean, 'unittest') || str_contains($clean, 'testing')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><rect width="24" height="24" rx="4" fill="#3B82F6"/><path d="M8 12l3 3 5-6" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        }

        // Larastan / PHPStan / Static Analysis
        if (str_contains($clean, 'larastan') || str_contains($clean, 'stan') || str_contains($clean, 'static') || str_contains($clean, 'analysis')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#A855F7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><path d="M11 8v6M8 11h6"/></svg>';
        }

        // ── 6. MOBILE, IDE & DESIGN ──

        // Flutter
        if (str_contains($clean, 'flutter')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M14.3 2L4 12.3l3.2 3.2L17.5 5.2h5.7L14.3 2z" fill="#42A5F5"/><path d="M14.3 14.8L9.5 19.6 12.7 22.8 17.5 18h5.7l-8.9-3.2z" fill="#0D47A1"/><path d="M17.5 11.6l-3.2 3.2 3.2 3.2h5.7l-3.2-3.2 3.2-3.2h-5.7z" fill="#29B6F6"/></svg>';
        }

        // Dart
        if (str_contains($clean, 'dart')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M4.5 13L2 19.5 8.5 22 22 8.5 15.5 2 4.5 13z" fill="#0175C2"/><path d="M4.5 13l7.5 7.5L22 8.5 14.5 1 4.5 13z" fill="#02569B"/></svg>';
        }

        // Firebase
        if (str_contains($clean, 'firebase')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M4.5 18.5L2 6.5l6.5 7.5L4.5 18.5z" fill="#FFA000"/><path d="M12 2l-3.5 12 7.5 4.5L12 2z" fill="#FFCA28"/><path d="M19.5 18.5L22 6.5l-6 12 3.5 0z" fill="#F57C00"/></svg>';
        }

        // Figma
        if (str_contains($clean, 'figma') || str_contains($clean, 'prototype')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M8 2h4v5H8a2.5 2.5 0 010-5z" fill="#F24E1E"/><path d="M12 2h4a2.5 2.5 0 010 5h-4V2z" fill="#FF7262"/><path d="M12 7h4a2.5 2.5 0 010 5h-4V7z" fill="#1ABCFE"/><path d="M8 7h4v5H8a2.5 2.5 0 010-5z" fill="#A259FF"/><path d="M8 12h4v2.5a2.5 2.5 0 01-5 0V12z" fill="#0ACF83"/></svg>';
        }

        // VS Code / Antigravity IDE / Editors
        if (str_contains($clean, 'vscode') || str_contains($clean, 'antigravity') || str_contains($clean, 'jetbrains') || str_contains($clean, 'mono') || str_contains($clean, 'editor') || str_contains($clean, 'ide')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><path d="m18 16 4-4-4-4"/><path d="m6 8-4 4 4 4"/><path d="m14.5 4-5 16"/></svg>';
        }

        // ── 7. AI, AGENTS & PROMPT ENGINEERING ──

        // Claude / Anthropic
        if (str_contains($clean, 'claude') || str_contains($clean, 'anthropic')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z" fill="#D97706"/></svg>';
        }

        // OpenAI / ChatGPT
        if (str_contains($clean, 'openai') || str_contains($clean, 'chatgpt') || str_contains($clean, 'gpt')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><circle cx="12" cy="12" r="10" fill="#10A37F"/><path d="M12 7v10M7 12h10M8.5 8.5l7 7M15.5 8.5l-7 7" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round"/></svg>';
        }

        // Google Gemini / AI
        if (str_contains($clean, 'gemini') || str_contains($clean, 'googleai')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none"'.$cls.'><path d="M12 2C12 7.5 7.5 12 2 12C7.5 12 12 16.5 12 22C12 16.5 16.5 12 22 12C16.5 12 12 7.5 12 2Z" fill="url(#geminiGrad)"/><defs><linearGradient id="geminiGrad" x1="2" y1="2" x2="22" y2="22"><stop stop-color="#4C8DFF"/><stop offset="0.5" stop-color="#A855F7"/><stop offset="1" stop-color="#EC4899"/></linearGradient></defs></svg>';
        }

        // Cursor / Agentic Dev / AI Tools / Prompt Engineering
        if (str_contains($clean, 'cursor') || str_contains($clean, 'agentic') || str_contains($clean, 'copilot') || str_contains($clean, 'prompt')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#60A5FA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><path d="M4 4l7 17 2.5-6.5L20 12 4 4z"/></svg>';
        }

        // ── 8. DATA MINING, ALGORITHMS & ANALYTICS ──

        // Algoritma C4.5 / Decision Tree / Pohon Keputusan
        if (str_contains($clean, 'c45') || str_contains($clean, 'tree') || str_contains($clean, 'pohon') || str_contains($clean, 'decisiontree')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><rect x="9" y="2" width="6" height="5" rx="1" fill="#10B981"/><rect x="2" y="17" width="6" height="5" rx="1"/><rect x="16" y="17" width="6" height="5" rx="1"/><path d="M12 7v5m0 0H5v5m7-5h7v5"/></svg>';
        }

        // Data Mining / Klasifikasi / Data Cleansing Pipeline
        if (str_contains($clean, 'mining') || str_contains($clean, 'klasifikasi') || str_contains($clean, 'cleansing') || str_contains($clean, 'pipeline')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><circle cx="6" cy="6" r="3"/><circle cx="18" cy="6" r="3"/><circle cx="12" cy="18" r="3"/><line x1="8.5" y1="7.5" x2="15.5" y2="7.5"/><line x1="7.5" y1="8.5" x2="10.5" y2="15.5"/><line x1="16.5" y1="8.5" x2="13.5" y2="15.5"/></svg>';
        }

        // RapidMiner Studio / RapidMiner
        if (str_contains($clean, 'rapidminer')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><path d="M18 20V10M12 20V4M6 20v-6"/><circle cx="12" cy="4" r="2" fill="#F59E0B"/></svg>';
        }

        // Sistem Informasi / System Analysis / S1
        if (str_contains($clean, 'sisteminformasi') || str_contains($clean, 'informasi') || str_contains($clean, 's1')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>';
        }

        // Entropy & Gain Ratio / Evaluasi / Akurasi / Presisi / Confusion Matrix
        if (str_contains($clean, 'entropy') || str_contains($clean, 'gain') || str_contains($clean, 'ratio') || str_contains($clean, 'akurasi') || str_contains($clean, 'presisi') || str_contains($clean, 'uml') || str_contains($clean, 'perancangan') || str_contains($clean, 'matrix') || str_contains($clean, 'validasi') || str_contains($clean, 'confusion') || str_contains($clean, 'evaluasi')) {
            return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="#EC4899" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>';
        }

        // ── 9. DEFAULT SMART CODE FALLBACK ICON ──
        return '<svg width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'.$cls.'><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>';
    }
}
