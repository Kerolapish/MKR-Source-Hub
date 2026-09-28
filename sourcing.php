<?php
/**
 * sourcing.php
 * Hardware Catalog / Item Registry View (MKR Hartamas Blue Theme)
 * MKR Hartamas Sdn. Bhd.
 */

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/SourcingController.php';

// Instantiate controller & handle POST actions
$controller = new SourcingController($pdo);
$controller->handleRequest();

// Flash messages
$flashSuccess = $_GET['success'] ?? null;
$flashError   = $_GET['error'] ?? null;

// Search & Filter parameters
$search       = trim($_GET['q'] ?? '');
$catFilter    = trim($_GET['category'] ?? '');
$supFilter    = (int) ($_GET['supplier_id'] ?? 0);

// Fetch data from controller
$suppliers       = $controller->getSuppliers();
$items           = $controller->getFilteredItems($search, $catFilter, $supFilter);
$totalItemsCount = count($items);

$pageTitle = 'Item Registry';

function e(string $val): string {
    return htmlspecialchars($val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Flash Notifications -->
    <?php if ($flashSuccess): ?>
    <div class="flex items-center gap-3 bg-sky-500/10 border border-sky-500/30 rounded-lg px-4 py-3 text-sky-400 text-sm">
        <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
        <span><?= e($flashSuccess) ?></span>
    </div>
    <?php endif; ?>

    <?php if ($flashError): ?>
    <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 rounded-lg px-4 py-3 text-red-400 text-sm">
        <i data-lucide="alert-triangle" class="w-5 h-5 flex-shrink-0"></i>
        <span><?= e($flashError) ?></span>
    </div>
    <?php endif; ?>

    <!-- ── WIREFRAME TITLE BAR ───────────────────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-blue-900/60 pb-4">
        <div>
            <h1 class="text-xl font-extrabold text-white uppercase tracking-wider font-mono">
                ITEM REGISTRY
            </h1>
        </div>

        <button id="open-add-btn" type="button"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs uppercase tracking-wider px-5 py-2.5 rounded shadow-lg shadow-blue-950/50 transition-all">
            <i data-lucide="package-plus" class="w-4 h-4"></i>
            NEW ITEM
        </button>
    </div>

    <!-- ── COLLAPSIBLE NEW/EDIT ITEM FORM (DASHED BORDER WIREFRAME BOX) ─────── -->
    <div id="item-form-container" class="hidden bg-[#0f172a] border-2 border-dashed border-sky-400/40 rounded-xl p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-blue-900/60 pb-3">
            <h2 id="form-title" class="text-xs font-bold text-sky-400 uppercase tracking-widest font-mono">
                NEW ITEM FORM
            </h2>
            <button type="button" id="close-form-btn" class="text-gray-400 hover:text-white text-xs flex items-center gap-1">
                <i data-lucide="x" class="w-4 h-4"></i> Close
            </button>
        </div>

        <form method="POST" action="sourcing.php" class="space-y-4" id="item-form">
            <input type="hidden" name="action" id="form-action-input" value="create_item">
            <input type="hidden" name="item_id" id="form-item-id" value="">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">Item Name *</label>
                    <input type="text" name="item_name" id="input-item-name" required placeholder="e.g. SHT40 Temperature & Humidity Sensor"
                           class="w-full bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-sky-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">Category *</label>
                    <select name="category" id="input-category" required
                            class="w-full bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-sky-400">
                        <option value="">Select Category</option>
                        <option value="Sensor">Sensor</option>
                        <option value="Microcontroller">Microcontroller</option>
                        <option value="Fertigation">Fertigation</option>
                        <option value="Network">Network</option>
                        <option value="Machinery">Machinery</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">Supplier *</label>
                    <select name="supplier_id" id="input-supplier-id" required
                            class="w-full bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-sky-400">
                        <option value="">Select Supplier</option>
                        <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= (int)$sup['id'] ?>"><?= e($sup['supplier_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">Unit Price (RM) *</label>
                    <input type="number" step="0.01" min="0" name="unit_cost" id="input-unit-cost" required placeholder="0.00"
                           class="w-full bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-sky-400 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">Lead Time (Days)</label>
                    <input type="number" min="1" name="lead_time_days" id="input-lead-time" value="7"
                           class="w-full bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-sky-400 font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">Specifications / Details</label>
                <textarea name="specifications" id="input-specifications" rows="2" placeholder="Technical specs, operating range, voltage, protocol..."
                          class="w-full bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-sky-400"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" id="submit-btn" class="bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs uppercase px-5 py-2.5 rounded shadow">
                    SAVE ITEM
                </button>
                <button type="button" id="cancel-btn" class="bg-blue-950 hover:bg-blue-900 text-gray-300 font-bold text-xs uppercase px-5 py-2.5 rounded border border-blue-800/80">
                    CANCEL
                </button>
            </div>
        </form>
    </div>

    <!-- ── SEARCH & FILTER CONTROLS BAR ────────────────────────────────────── -->
    <div class="bg-[#0f172a] border border-blue-900/60 rounded-xl p-4 shadow-lg">
        <form method="GET" action="sourcing.php" class="flex flex-col md:flex-row items-center justify-between gap-4">

            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto flex-1">
                <div class="relative min-w-[240px] flex-1 md:flex-initial">
                    <input type="text" name="q" value="<?= e($search) ?>" placeholder="[ Search by name or spec... ]"
                           class="w-full bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 pl-9 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-sky-400 font-mono">
                    <i data-lucide="search" class="w-4 h-4 text-sky-400/60 absolute left-2.5 top-2.5"></i>
                </div>

                <select name="category" onchange="this.form.submit()"
                        class="bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400 font-mono">
                    <option value="">Category ▾</option>
                    <?php foreach (['Sensor','Microcontroller','Fertigation','Network','Machinery'] as $cat): ?>
                    <option value="<?= $cat ?>" <?= $catFilter === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="supplier_id" onchange="this.form.submit()"
                        class="bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400 font-mono">
                    <option value="">Supplier ▾</option>
                    <?php foreach ($suppliers as $sup): ?>
                    <option value="<?= (int)$sup['id'] ?>" <?= $supFilter === (int)$sup['id'] ? 'selected' : '' ?>>
                        <?= e($sup['supplier_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <?php if (!empty($search) || !empty($catFilter) || $supFilter > 0): ?>
                <a href="sourcing.php" class="text-xs text-sky-400 hover:text-red-400 underline font-mono">
                    Clear Filters
                </a>
                <?php endif; ?>
            </div>

            <div class="text-xs text-blue-300/70 font-mono flex-shrink-0">
                <span class="text-sky-400 font-bold"><?= $totalItemsCount ?></span> items found
            </div>

        </form>
    </div>

    <!-- ── DATA TABLE ───────────────────────────────────────────────────────── -->
    <div class="bg-[#0f172a] border border-blue-900/60 rounded-xl overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-blue-950/70 text-blue-200 text-[11px] font-mono font-bold uppercase tracking-wider border-b border-blue-900/60">
                        <th class="py-3 px-4 w-24">CODE</th>
                        <th class="py-3 px-4">ITEM NAME / DESCRIPTION</th>
                        <th class="py-3 px-4 w-36">CATEGORY</th>
                        <th class="py-3 px-4 w-32">LEAD TIME</th>
                        <th class="py-3 px-4 w-32 text-right">UNIT PRICE</th>
                        <th class="py-3 px-4 w-28 text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-blue-950 text-xs">
                    <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500 font-mono">
                            No hardware items found matching your filters.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($items as $item):
                        $code = sprintf('HW-%03d', (int)$item['id']);
                        $catColors = [
                            'Sensor'          => 'bg-sky-500/15 text-sky-400 border-sky-500/30',
                            'Microcontroller' => 'bg-blue-500/15 text-blue-400 border-blue-500/30',
                            'Fertigation'     => 'bg-cyan-500/15 text-cyan-400 border-cyan-500/30',
                            'Network'         => 'bg-indigo-500/15 text-indigo-400 border-indigo-500/30',
                            'Machinery'       => 'bg-amber-500/15 text-amber-400 border-amber-500/30',
                        ];
                        $badgeClass = $catColors[$item['category']] ?? 'bg-blue-950 text-blue-400 border-blue-800';
                    ?>
                    <tr class="hover:bg-blue-950/40 transition-colors">
                        <td class="py-3.5 px-4 font-mono font-semibold text-sky-400 select-all">
                            <?= e($code) ?>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-white text-sm">
                                <?= e($item['item_name']) ?>
                            </div>
                            <div class="text-[11px] text-gray-300 mt-0.5 line-clamp-1">
                                <?= e($item['specifications']) ?>
                            </div>
                            <div class="text-[10px] text-sky-400/80 font-mono mt-0.5">
                                Supplier: <?= e($item['supplier_name'] ?? 'N/A') ?>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-block px-2.5 py-1 rounded text-[10px] font-bold font-mono border <?= $badgeClass ?>">
                                <?= e($item['category']) ?>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-gray-300">
                            <?= (int)$item['lead_time_days'] ?> days
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono font-bold text-white text-sm">
                            RM <?= number_format((float)$item['unit_cost'], 2) ?>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button"
                                        onclick="editItem(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)"
                                        class="px-2 py-1 bg-blue-950 hover:bg-blue-900 text-sky-300 border border-blue-800/80 rounded text-[11px] font-mono">
                                    Edit
                                </button>
                                <form method="POST" action="sourcing.php" onsubmit="return confirm('Delete this item?');" class="inline">
                                    <input type="hidden" name="action" value="delete_item">
                                    <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">
                                    <button type="submit" class="px-2 py-1 bg-red-950/40 hover:bg-red-900/60 text-red-400 border border-red-800/50 rounded text-[11px] font-mono">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- JavaScript for Add/Edit Form toggle -->
<script>
    const formContainer = document.getElementById('item-form-container');
    const openAddBtn    = document.getElementById('open-add-btn');
    const closeBtn      = document.getElementById('close-form-btn');
    const cancelBtn     = document.getElementById('cancel-btn');

    const itemForm        = document.getElementById('item-form');
    const formTitle       = document.getElementById('form-title');
    const formActionInput = document.getElementById('form-action-input');
    const formItemId      = document.getElementById('form-item-id');
    const inputName       = document.getElementById('input-item-name');
    const inputCategory   = document.getElementById('input-category');
    const inputSupplier   = document.getElementById('input-supplier-id');
    const inputUnitCost   = document.getElementById('input-unit-cost');
    const inputLeadTime   = document.getElementById('input-lead-time');
    const inputSpec       = document.getElementById('input-specifications');
    const submitBtn       = document.getElementById('submit-btn');

    function showForm(isEdit = false) {
        formContainer.classList.remove('hidden');
        lucide.createIcons();
    }

    function hideForm() {
        formContainer.classList.add('hidden');
        resetForm();
        lucide.createIcons();
    }

    function resetForm() {
        formTitle.textContent = 'NEW ITEM FORM';
        formActionInput.value = 'create_item';
        formItemId.value = '';
        itemForm.reset();
        submitBtn.textContent = 'SAVE ITEM';
    }

    openAddBtn.addEventListener('click', () => {
        resetForm();
        showForm();
        inputName.focus();
    });

    closeBtn.addEventListener('click', hideForm);
    cancelBtn.addEventListener('click', hideForm);

    function editItem(item) {
        resetForm();
        formTitle.textContent = 'EDIT ITEM FORM (ID: ' + item.id + ')';
        formActionInput.value = 'update_item';
        formItemId.value = item.id;
        inputName.value = item.item_name;
        inputCategory.value = item.category;
        inputSupplier.value = item.supplier_id;
        inputUnitCost.value = item.unit_cost;
        inputLeadTime.value = item.lead_time_days;
        inputSpec.value = item.specifications || '';
        submitBtn.textContent = 'UPDATE ITEM';
        showForm(true);
        formContainer.scrollIntoView({ behavior: 'smooth' });
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
