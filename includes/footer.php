<?php
/**
 * includes/footer.php
 * Site-wide footer aligned with MKR Hartamas Blue & Cyan Theme
 * MKR Hartamas Sdn. Bhd.
 */
?>

    <!-- ============================================================
         FOOTER
         ============================================================ -->
    <footer class="bg-[#0f172a] border-t border-blue-900/60 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">

                <!-- Branding block -->
                <div class="flex items-center gap-3">
                    <img src="assets/picture/logo mkr.jpg" alt="MKR Hartamas Logo" class="w-10 h-10 object-contain rounded-lg shadow-sm" />
                    <div>
                        <p class="text-sm font-semibold text-white leading-tight">MKR Hartamas Sdn. Bhd.</p>
                        <p class="text-xs text-blue-300/80 leading-tight">Contractor History &amp; Agritech Item Sourcing Management System</p>
                    </div>
                </div>

                <!-- Centre tagline -->
                <div class="hidden lg:flex items-center gap-2 text-xs text-blue-400/80">
                    <i data-lucide="cpu" class="w-3.5 h-3.5 text-sky-400"></i>
                    <span>Precision Agriculture &bull; Smart Sourcing &bull; Project Excellence</span>
                    <i data-lucide="cpu" class="w-3.5 h-3.5 text-sky-400"></i>
                </div>

                <!-- Right: copyright + version -->
                <div class="text-right">
                    <p class="text-xs text-gray-400">&copy; <?= date('Y') ?> MKR Hartamas Sdn. Bhd.</p>
                    <p class="text-xs text-blue-400/60 mt-0.5">MKR-Source-Hub &bull; v1.0.0</p>
                </div>

            </div>

            <!-- Divider -->
            <div class="border-t border-blue-900/40 mt-6 pt-4 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-xs text-gray-400">
                    Built for internal procurement &amp; contractor management operations.
                </p>
                <div class="flex items-center gap-4">
                    <a href="index.php"         class="text-xs text-gray-400 hover:text-sky-400 transition-colors duration-150">Dashboard</a>
                    <a href="sourcing.php"       class="text-xs text-gray-400 hover:text-sky-400 transition-colors duration-150">Hardware Catalog</a>
                    <a href="contractors.php"    class="text-xs text-gray-400 hover:text-sky-400 transition-colors duration-150">Contractors</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ============================================================
         Lucide Icons Initialisation
         ============================================================ -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>