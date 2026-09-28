<?php
/**
 * index.php
 * Dashboard View – MKR Hartamas Blue Theme
 * MKR Hartamas Sdn. Bhd.
 */

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/DashboardController.php';

// Instantiate controller & fetch view data
$controller = new DashboardController($pdo);
$data       = $controller->getDashboardData();

$pageTitle        = 'Dashboard';
$totalHardware    = $data['totalHardware'];
$totalContractors = $data['totalContractors'];
$recentHardware   = $data['recentHardware'];
$categoryStats    = $data['categoryStats'];
$dbError          = $data['dbError'];

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function formatMYR(float $amount): string
{
    return 'RM ' . number_format($amount, 0, '.', ',');
}

require_once __DIR__ . '/includes/header.php';
?>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

            <?php if ($dbError): ?>
            <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 rounded-xl px-5 py-4 text-red-400">
                <i data-lucide="alert-triangle" class="w-5 h-5 flex-shrink-0"></i>
                <p class="text-sm font-medium"><?= e($dbError) ?></p>
            </div>
            <?php endif; ?>

            <!-- ── PAGE HEADING ──────────────────────────────────────────── -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">
                        Operations Dashboard
                    </h1>
                    <p class="text-sm text-blue-200/70 mt-1">
                        Real-time overview of sourcing inventory, contractors &amp; project tenders.
                    </p>
                </div>
                <div class="flex items-center gap-2 text-xs text-sky-300 bg-blue-950/60 border border-blue-800/60 rounded-lg px-3 py-2 font-mono">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-sky-400"></i>
                    <span>Last updated: <?= e(date('d M Y, H:i')) ?></span>
                </div>
            </div>

            <!-- ── METRIC CARDS ──────────────────────────────────────────── -->
            <section aria-label="Key metrics">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <?php
                    $metrics = [
                        [
                            'label'    => 'Hardware Items',
                            'value'    => $totalHardware,
                            'icon'     => 'package',
                            'accent'   => 'text-sky-400',
                            'sub'      => 'SKUs in catalog',
                            'href'     => 'sourcing.php',
                        ],
                        [
                            'label'    => 'Active Contractors',
                            'value'    => $totalContractors,
                            'icon'     => 'hard-hat',
                            'accent'   => 'text-blue-400',
                            'sub'      => 'Registered companies',
                            'href'     => 'contractors.php',
                        ],
                    ];

                    foreach ($metrics as $metric): ?>
                    <div class="group relative bg-[#0f172a] border border-blue-900/60 rounded-2xl p-5
                                hover:border-blue-700 hover:bg-blue-950/50
                                transition-all duration-200 overflow-hidden shadow-lg">

                        <div class="relative flex items-center justify-between">
                            <!-- Clean Icon Badge without solid blue block -->
                            <div class="flex items-center justify-center p-2.5 rounded-xl bg-blue-950/90 border border-blue-800/80 <?= $metric['accent'] ?>">
                                <i data-lucide="<?= e($metric['icon']) ?>" class="w-6 h-6"></i>
                            </div>

                            <a href="<?= e($metric['href']) ?>"
                               class="inline-flex items-center gap-1 text-xs <?= $metric['accent'] ?> hover:underline font-medium">
                                View details
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>

                        <div class="mt-4">
                            <p class="text-3xl font-extrabold text-white tabular-nums">
                                <?= number_format((float)$metric['value']) ?>
                            </p>
                            <p class="mt-1 text-sm font-semibold text-gray-200"><?= e($metric['label']) ?></p>
                            <p class="mt-0.5 text-xs text-blue-300/70"><?= e($metric['sub']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>

                </div>
            </section>

            <!-- ── QUICK ACTIONS ─────────────────────────────────────────── -->
            <section aria-label="Quick actions">
                <h2 class="text-xs font-bold text-sky-400 uppercase tracking-widest font-mono mb-3">
                    Quick Actions
                </h2>
                <div class="flex flex-wrap gap-3">

                    <a href="sourcing.php"
                       class="group inline-flex items-center gap-2.5 bg-blue-600 hover:bg-blue-500
                              text-white text-sm font-bold px-5 py-3 rounded-xl
                              shadow-lg shadow-blue-950/50 hover:shadow-blue-900/60
                              transition-all duration-150 hover:-translate-y-0.5">
                        <i data-lucide="package-search" class="w-4 h-4 group-hover:scale-110 transition-transform duration-150"></i>
                        Browse Hardware Catalog
                    </a>

                    <a href="contractors.php"
                       class="group inline-flex items-center gap-2.5 bg-blue-950 hover:bg-blue-900
                              border border-blue-700/60 hover:border-blue-600
                              text-white text-sm font-bold px-5 py-3 rounded-xl
                              transition-all duration-150 hover:-translate-y-0.5">
                        <i data-lucide="hard-hat" class="w-4 h-4 group-hover:scale-110 transition-transform duration-150"></i>
                        View Contractors
                    </a>

                </div>
            </section>

            <!-- ── BOTTOM GRID: Recent Hardware + Category Stats ── -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <section class="lg:col-span-2 bg-[#0f172a] border border-blue-900/60 rounded-2xl overflow-hidden shadow-lg"
                         aria-label="Recent hardware">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-blue-900/60 bg-blue-950/40">
                        <div class="flex items-center gap-2">
                            <i data-lucide="package" class="w-4 h-4 text-sky-400"></i>
                            <h2 class="text-sm font-bold text-white font-mono uppercase tracking-wider">Recent Hardware</h2>
                        </div>
                        <span class="text-xs text-blue-300/60 font-mono">Latest 5 records</span>
                    </div>

                    <?php if (empty($recentHardware)): ?>
                    <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                        <i data-lucide="package-open" class="w-10 h-10 mb-3 opacity-40"></i>
                        <p class="text-sm font-mono">No hardware items found.</p>
                    </div>
                    <?php else: ?>
                    <?php
                    $catBadge = [
                        'Sensor'          => 'bg-sky-500/15 text-sky-400 border-sky-500/30',
                        'Microcontroller' => 'bg-blue-500/15 text-blue-400 border-blue-500/30',
                        'Fertigation'     => 'bg-cyan-500/15 text-cyan-400 border-cyan-500/30',
                        'Network'         => 'bg-indigo-500/15 text-indigo-400 border-indigo-500/30',
                        'Machinery'       => 'bg-amber-500/15 text-amber-400 border-amber-500/30',
                    ];
                    ?>
                    <div class="divide-y divide-blue-950">
                        <?php foreach ($recentHardware as $item):
                            $category = $item['category'] ?? '';
                            $badge = $catBadge[$category] ?? 'bg-slate-500/15 text-slate-400 border-slate-500/30';
                        ?>
                        <div class="px-6 py-4 hover:bg-blue-950/40 transition-colors duration-100 group">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-white truncate group-hover:text-sky-300
                                              transition-colors duration-150">
                                        <?= e($item['item_name']) ?>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5 truncate">
                                        <?= e($item['supplier_name']) ?>
                                    </p>
                                    <div class="flex items-center gap-3 mt-2">
                                        <span class="text-xs text-gray-400 flex items-center gap-1 font-mono">
                                            <i data-lucide="clock" class="w-3 h-3 text-sky-400"></i>
                                            <?= (int)$item['lead_time_days'] ?> day lead time
                                        </span>
                                        <span class="text-xs font-mono font-bold text-sky-400">
                                            <?= e(formatMYR((float)$item['unit_cost'])) ?>
                                        </span>
                                    </div>
                                </div>
                                <span class="flex-shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1
                                             rounded-full text-xs font-semibold font-mono border <?= $badge ?>">
                                    <?= e($category) ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </section>

                <div class="flex flex-col gap-6">

                    <section class="bg-[#0f172a] border border-blue-900/60 rounded-2xl overflow-hidden shadow-lg"
                             aria-label="Hardware by category">
                        <div class="flex items-center gap-2 px-5 py-4 border-b border-blue-900/60 bg-blue-950/40">
                            <i data-lucide="pie-chart" class="w-4 h-4 text-sky-400"></i>
                            <h2 class="text-sm font-bold text-white font-mono uppercase tracking-wider">Catalog by Category</h2>
                        </div>
                        <div class="px-5 py-4 space-y-3">
                            <?php
                            $catColors = [
                                'Sensor'          => ['bar' => 'bg-sky-400',   'text' => 'text-sky-400'],
                                'Microcontroller' => ['bar' => 'bg-blue-500',  'text' => 'text-blue-400'],
                                'Fertigation'     => ['bar' => 'bg-cyan-400',  'text' => 'text-cyan-400'],
                                'Network'         => ['bar' => 'bg-indigo-500','text' => 'text-indigo-400'],
                                'Machinery'       => ['bar' => 'bg-amber-400', 'text' => 'text-amber-400'],
                            ];
                            $maxCatTotal = max(array_column($categoryStats, 'total') ?: [1]);

                            foreach ($categoryStats as $cat):
                                $pct = $maxCatTotal > 0
                                    ? round(($cat['total'] / $maxCatTotal) * 100)
                                    : 0;
                                $colors = $catColors[$cat['category']] ?? ['bar' => 'bg-gray-500', 'text' => 'text-gray-400'];
                            ?>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-medium text-gray-300"><?= e($cat['category']) ?></span>
                                    <span class="text-xs font-bold font-mono <?= $colors['text'] ?>"><?= (int)$cat['total'] ?></span>
                                </div>
                                <div class="h-1.5 w-full bg-blue-950 rounded-full overflow-hidden">
                                    <div class="h-full <?= $colors['bar'] ?> rounded-full transition-all duration-500"
                                         style="width: <?= $pct ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                </div>
            </div>

        </div>

    </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
