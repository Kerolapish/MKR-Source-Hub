<?php
/**
 * includes/header.php
 * Navigation header aligned with MKR Hartamas Blue & Cyan Theme
 * MKR Hartamas Sdn. Bhd.
 */

$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="MKR-Source-Hub – MKR Hartamas Contractor History and Sourcing System" />
    <title><?= htmlspecialchars($pageTitle ?? 'MKR-Source-Hub', ENT_QUOTES, 'UTF-8') ?> | MKR Hartamas</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        mkr: {
                            50:  '#f0f9ff',
                            100: '#e0f2fe',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#0f172a',
                            950: '#0b1329',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'ui-monospace', 'monospace'],
                    },
                },
            },
        };
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet" />

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <!-- External Custom Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body class="bg-[#0b1329] text-gray-100 flex flex-col min-h-screen">

    <!-- ============================================================
         TOP NAVIGATION BAR (MKR Hartamas Blue Theme)
         ============================================================ -->
    <header class="sticky top-0 z-50 bg-[#0f172a]/95 backdrop-blur-md border-b border-blue-900/60 shadow-lg shadow-blue-950/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">

                <!-- LEFT: LOGO / BRANDING -->
                <a href="index.php" class="flex items-center gap-3 group flex-shrink-0">
                    <div class="h-10 flex items-center gap-3">
                        <img src="assets/picture/logo mkr.jpg" alt="MKR Hartamas Logo" class="h-10 w-auto rounded-lg object-contain shadow-md group-hover:opacity-90 transition-opacity" />
                        <div class="leading-tight">
                            <span class="block text-sm font-black tracking-wider text-white uppercase group-hover:text-sky-300 transition-colors">
                                MKR-SOURCE-HUB
                            </span>
                            <span class="block text-[10px] text-sky-400 font-mono tracking-widest uppercase">
                                MKR HARTAMAS
                            </span>
                        </div>
                    </div>
                </a>

                <!-- CENTER: NAVIGATION TABS -->
                <nav class="hidden md:flex items-center gap-1 h-full" aria-label="Main navigation">
                    <?php
                    $navItems = [
                        ['href' => 'index.php',       'label' => 'DASHBOARD',         'icon' => 'layout-dashboard'],
                        ['href' => 'sourcing.php',    'label' => 'HARDWARE CATALOG',  'icon' => 'package-search'],
                        ['href' => 'contractors.php', 'label' => 'CONTRACTORS',       'icon' => 'hard-hat'],
                    ];

                    foreach ($navItems as $item):
                        $isActive = ($currentPage === $item['href']);
                        $activeClasses = $isActive ? 'nav-link-active font-bold' : 'text-gray-300 hover:text-white hover:bg-blue-900/40';
                    ?>
                    <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                       class="flex items-center gap-2 px-4 h-full text-xs font-semibold tracking-wider transition-colors duration-150 <?= $activeClasses ?>">
                        <i data-lucide="<?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?>" class="w-4 h-4"></i>
                        <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
                    </a>
                    <?php endforeach; ?>
                </nav>

                <!-- RIGHT: DATE & USER BADGE -->
                <div class="flex items-center gap-4 flex-shrink-0">
                    <!-- Wireframe Date Badge in Sky Blue -->
                    <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 bg-blue-950/60 border border-blue-800/60 rounded text-xs font-mono text-sky-300">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-sky-400"></i>
                        <span>[ <?= date('d M Y') ?> ]</span>
                    </div>

                    <!-- User Circle Avatar -->
                    <div class="flex items-center gap-2.5 bg-blue-900/60 border border-blue-700/60 rounded-full px-3 py-1.5 cursor-default" title="Muhammad Khairulhafiz (Procurement Staff)">
                        <div class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white font-extrabold text-xs shadow-inner">
                            U
                        </div>
                        <span class="hidden sm:inline text-xs font-semibold text-blue-100">
                            Khairulhafiz
                        </span>
                    </div>

                    <!-- Mobile Menu Trigger -->
                    <button id="mobile-menu-btn" class="md:hidden p-2 text-gray-400 hover:text-white" aria-label="Toggle menu">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Drawer -->
        <div id="mobile-menu" class="md:hidden hidden border-t border-blue-900 bg-[#0f172a] px-4 py-3 space-y-1">
            <?php foreach ($navItems as $item):
                $isActive = ($currentPage === $item['href']);
                $mobileActiveClasses = $isActive ? 'text-sky-400 bg-blue-600/20 font-bold' : 'text-gray-300 hover:text-white';
            ?>
            <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
               class="flex items-center gap-2 px-3 py-2 rounded text-xs font-semibold tracking-wider <?= $mobileActiveClasses ?>">
                <i data-lucide="<?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?>" class="w-4 h-4"></i>
                <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
            </a>
            <?php endforeach; ?>
        </div>
    </header>

    <script>
        (function () {
            const btn  = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            if (!btn || !menu) return;
            btn.addEventListener('click', function () {
                menu.classList.toggle('hidden');
            });
        })();
    </script>

    <main class="flex-1">