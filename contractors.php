<?php
/**
 * contractors.php
 * Contractor Registry View (MKR Hartamas Blue Theme)
 * MKR Hartamas Sdn. Bhd.
 */

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/ContractorController.php';

// Instantiate controller & handle POST requests
$controller = new ContractorController($pdo);
$controller->handleRequest();

// Flash Messages
$flashSuccess = $_GET['success'] ?? null;
$flashError   = $_GET['error'] ?? null;

// Search & Filter parameters
$search    = trim($_GET['q'] ?? '');
$minRating = (float) ($_GET['min_rating'] ?? 0.0);

// Fetch data from controller
$contractors           = $controller->getFilteredContractors($search, $minRating);
$totalContractorsCount = count($contractors);

$pageTitle = 'Contractor Registry';

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
                CONTRACTOR REGISTRY
            </h1>
            <button id="toggle-form-btn" type="button" class="inline-flex items-center gap-1.5 text-xs text-sky-400 hover:text-sky-300 font-medium mt-1 transition-colors">
                <i data-lucide="chevron-down" class="w-3.5 h-3.5" id="form-chevron"></i>
                <span id="form-toggle-text">show add form</span>
            </button>
        </div>

        <button id="open-add-btn" type="button"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs uppercase tracking-wider px-5 py-2.5 rounded shadow-lg shadow-blue-950/50 transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            + NEW CONTRACTOR
        </button>
    </div>

    <!-- ── SEARCH & FILTER CONTROLS BAR ────────────────────────────────────── -->
    <div class="bg-[#0f172a] border border-blue-900/60 rounded-xl p-4 shadow-lg">
        <form method="GET" action="contractors.php" class="flex flex-col md:flex-row items-center justify-between gap-4">

            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto flex-1">
                <div class="relative min-w-[240px] flex-1 md:flex-initial">
                    <input type="text" name="q" value="<?= e($search) ?>" placeholder="[ Search contractors... ]"
                           class="w-full bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 pl-9 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-sky-400 font-mono">
                    <i data-lucide="search" class="w-4 h-4 text-sky-400/60 absolute left-2.5 top-2.5"></i>
                </div>

                <select name="min_rating" onchange="this.form.submit()"
                        class="bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400 font-mono">
                    <option value="">Rating ▾</option>
                    <option value="4.5" <?= $minRating == 4.5 ? 'selected' : '' ?>>★ 4.5 & above</option>
                    <option value="4.0" <?= $minRating == 4.0 ? 'selected' : '' ?>>★ 4.0 & above</option>
                    <option value="3.5" <?= $minRating == 3.5 ? 'selected' : '' ?>>★ 3.5 & above</option>
                </select>

                <?php if (!empty($search) || $minRating > 0): ?>
                <a href="contractors.php" class="text-xs text-sky-400 hover:text-red-400 underline font-mono">
                    Clear Filters
                </a>
                <?php endif; ?>
            </div>

            <div class="text-xs text-blue-300/70 font-mono flex-shrink-0">
                <span class="text-sky-400 font-bold"><?= $totalContractorsCount ?></span> contractors found
            </div>

        </form>
    </div>

    <!-- ── COLLAPSIBLE NEW CONTRACTOR FORM (DASHED BORDER BOX) ─────────────── -->
    <div id="contractor-form-container" class="hidden bg-[#0f172a] border-2 border-dashed border-sky-400/40 rounded-xl p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-blue-900/60 pb-3">
            <h2 id="form-title" class="text-xs font-bold text-sky-400 uppercase tracking-widest font-mono">
                NEW CONTRACTOR FORM
            </h2>
            <button type="button" id="close-form-btn" class="text-gray-400 hover:text-white text-xs flex items-center gap-1">
                <i data-lucide="x" class="w-4 h-4"></i> Close
            </button>
        </div>

        <form method="POST" action="contractors.php" class="space-y-4" id="contractor-form">
            <input type="hidden" name="action" id="form-action-input" value="create_contractor">
            <input type="hidden" name="contractor_id" id="form-contractor-id" value="">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">Company Name *</label>
                    <input type="text" name="company_name" id="input-company-name" required placeholder="TechFarm Solutions Sdn. Bhd."
                           class="w-full bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-sky-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">Registration No. *</label>
                    <input type="text" name="registration_no" id="input-registration-no" required placeholder="1054321-K"
                           class="w-full bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-sky-400 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">Contact Person *</label>
                    <input type="text" name="contact_person" id="input-contact-person" required placeholder="Razali bin Mahfuz"
                           class="w-full bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-sky-400">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">Email *</label>
                    <input type="email" name="email" id="input-email" required placeholder="razali@techfarm.my"
                           class="w-full bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-sky-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">Phone Number *</label>
                    <input type="text" name="phone_number" id="input-phone" required placeholder="+603-8921-4433"
                           class="w-full bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-sky-400 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">Performance Rating (1.0 - 5.0)</label>
                    <input type="number" step="0.1" min="1.0" max="5.0" name="performance_rating" id="input-rating" value="4.5"
                           class="w-full bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-sky-400 font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">Address</label>
                <textarea name="address" id="input-address" rows="2" placeholder="Full company office / workshop address..."
                          class="w-full bg-[#0b1329] border border-blue-900/80 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-sky-400"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" id="submit-btn" class="bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs uppercase px-5 py-2.5 rounded shadow">
                    SAVE
                </button>
                <button type="button" id="cancel-btn" class="bg-blue-950 hover:bg-blue-900 text-gray-300 font-bold text-xs uppercase px-5 py-2.5 rounded border border-blue-800/80">
                    CANCEL
                </button>
            </div>
        </form>
    </div>

    <!-- ── DATA TABLE ───────────────────────────────────────────────────────── -->
    <div class="bg-[#0f172a] border border-blue-900/60 rounded-xl overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-blue-950/70 text-blue-200 text-[11px] font-mono font-bold uppercase tracking-wider border-b border-blue-900/60">
                        <th class="py-3 px-4">COMPANY</th>
                        <th class="py-3 px-4">CONTACT</th>
                        <th class="py-3 px-4 w-32">REG NO</th>
                        <th class="py-3 px-4 w-32">RATING</th>
                        <th class="py-3 px-4 w-24 text-center">JOBS</th>
                        <th class="py-3 px-4 w-28">SINCE</th>
                        <th class="py-3 px-4 w-28 text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-blue-950 text-xs">
                    <?php if (empty($contractors)): ?>
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-500 font-mono">
                            No contractors found matching your filter criteria.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($contractors as $con):
                        $rating = (float)$con['performance_rating'];
                        $createdDate = date('M Y', strtotime($con['created_at']));
                    ?>
                    <tr class="hover:bg-blue-950/40 transition-colors">
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-white text-sm">
                                <?= e($con['company_name']) ?>
                            </div>
                            <div class="text-[11px] text-gray-300 mt-0.5 line-clamp-1">
                                <?= e($con['address']) ?>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-medium text-gray-200">
                                <?= e($con['contact_person']) ?>
                            </div>
                            <div class="text-[11px] text-gray-400 font-mono">
                                <?= e($con['email']) ?>
                            </div>
                            <div class="text-[11px] text-sky-400 font-mono">
                                <?= e($con['phone_number']) ?>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-gray-300">
                            <?= e($con['registration_no']) ?>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-1.5 font-mono">
                                <span class="flex items-center text-amber-400 text-xs font-bold">
                                    ★ <?= number_format($rating, 1) ?>
                                </span>
                                <span class="inline-block w-2 h-2 rounded-full bg-sky-400" title="Active"></span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono font-bold text-white">
                            <?= (int)$con['linked_jobs'] ?>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-gray-400">
                            <?= e($createdDate) ?>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button"
                                        onclick="editContractor(<?= htmlspecialchars(json_encode($con), ENT_QUOTES, 'UTF-8') ?>)"
                                        class="px-2 py-1 bg-blue-950 hover:bg-blue-900 text-sky-300 border border-blue-800/80 rounded text-[11px] font-mono">
                                    Edit
                                </button>
                                <form method="POST" action="contractors.php" onsubmit="return confirm('Delete this contractor?');" class="inline">
                                    <input type="hidden" name="action" value="delete_contractor">
                                    <input type="hidden" name="contractor_id" value="<?= (int)$con['id'] ?>">
                                    <button type="submit" class="px-2 py-1 bg-red-950/40 hover:bg-red-900/60 text-red-400 border border-red-800/50 rounded text-[11px] font-mono">
                                        Del
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
    const formContainer   = document.getElementById('contractor-form-container');
    const toggleBtn       = document.getElementById('toggle-form-btn');
    const openAddBtn      = document.getElementById('open-add-btn');
    const closeBtn        = document.getElementById('close-form-btn');
    const cancelBtn       = document.getElementById('cancel-btn');
    const formChevron     = document.getElementById('form-chevron');
    const formToggleText  = document.getElementById('form-toggle-text');

    const contractorForm  = document.getElementById('contractor-form');
    const formTitle       = document.getElementById('form-title');
    const formActionInput = document.getElementById('form-action-input');
    const formContractorId= document.getElementById('form-contractor-id');
    const inputName       = document.getElementById('input-company-name');
    const inputRegNo      = document.getElementById('input-registration-no');
    const inputContact    = document.getElementById('input-contact-person');
    const inputEmail      = document.getElementById('input-email');
    const inputPhone      = document.getElementById('input-phone');
    const inputRating     = document.getElementById('input-rating');
    const inputAddress    = document.getElementById('input-address');
    const submitBtn       = document.getElementById('submit-btn');

    function showForm(isEdit = false) {
        formContainer.classList.remove('hidden');
        formToggleText.textContent = 'hide add form';
        if (formChevron) formChevron.setAttribute('data-lucide', 'chevron-up');
        lucide.createIcons();
    }

    function hideForm() {
        formContainer.classList.add('hidden');
        formToggleText.textContent = 'show add form';
        if (formChevron) formChevron.setAttribute('data-lucide', 'chevron-down');
        resetForm();
        lucide.createIcons();
    }

    function resetForm() {
        formTitle.textContent = 'NEW CONTRACTOR FORM';
        formActionInput.value = 'create_contractor';
        formContractorId.value = '';
        contractorForm.reset();
        submitBtn.textContent = 'SAVE';
    }

    toggleBtn.addEventListener('click', () => {
        if (formContainer.classList.contains('hidden')) {
            showForm();
        } else {
            hideForm();
        }
    });

    openAddBtn.addEventListener('click', () => {
        resetForm();
        showForm();
        inputName.focus();
    });

    closeBtn.addEventListener('click', hideForm);
    cancelBtn.addEventListener('click', hideForm);

    function editContractor(con) {
        resetForm();
        formTitle.textContent = 'EDIT CONTRACTOR FORM (ID: ' + con.id + ')';
        formActionInput.value = 'update_contractor';
        formContractorId.value = con.id;
        inputName.value = con.company_name;
        inputRegNo.value = con.registration_no;
        inputContact.value = con.contact_person;
        inputEmail.value = con.email;
        inputPhone.value = con.phone_number;
        inputRating.value = con.performance_rating;
        inputAddress.value = con.address || '';
        submitBtn.textContent = 'UPDATE';
        showForm(true);
        formContainer.scrollIntoView({ behavior: 'smooth' });
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
