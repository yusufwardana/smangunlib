/*
 * Theme Manager entry point untuk Vite.
 * --------------------------------------------------------------
 * Sumber utama perilaku ada di public/js/theme-manager.js (dimuat via
 * <script src="{{ asset('js/theme-manager.js') }}"> pada halaman Theme Manager
 * karena layout admin memakai jQuery/Bootstrap dari CDN).
 *
 * File ini disediakan agar tema juga dapat di-bundle melalui Vite bila
 * proyek beralih ke @vite(['resources/js/theme-manager.js']). Ia mengimpor
 * lapisan CSS variable tema.
 */
import '../css/theme.css';

// Behaviour Theme Manager dipublikasikan sebagai file statis; di-load pada
// halaman terkait. Bila ingin dibundel, salin isi public/js/theme-manager.js
// ke sini. Dibiarkan ringan agar tidak menduplikasi logika.
console.debug('[theme-manager] Vite entry loaded.');
