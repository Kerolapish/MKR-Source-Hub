<?php
/**
 * controllers/ContractorController.php
 * Backend logic and POST handlers for Contractor Registry (contractors.php)
 * MKR Hartamas Sdn. Bhd.
 */

declare(strict_types=1);

class ContractorController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function handleRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $action = $_POST['action'] ?? '';

        if ($action === 'create_contractor') {
            $this->createContractor();
        } elseif ($action === 'update_contractor') {
            $this->updateContractor();
        } elseif ($action === 'delete_contractor') {
            $this->deleteContractor();
        }
    }

    private function createContractor(): void
    {
        $companyName    = trim($_POST['company_name'] ?? '');
        $regNo          = trim($_POST['registration_no'] ?? '');
        $contactPerson  = trim($_POST['contact_person'] ?? '');
        $email          = trim($_POST['email'] ?? '');
        $phone          = trim($_POST['phone_number'] ?? '');
        $address        = trim($_POST['address'] ?? '');
        $rating         = (float) ($_POST['performance_rating'] ?? 4.0);

        if (!empty($companyName) && !empty($regNo) && !empty($contactPerson) && !empty($email)) {
            try {
                $stmt = $this->pdo->prepare('
                    INSERT INTO `contractors`
                    (`company_name`, `registration_no`, `contact_person`, `email`, `phone_number`, `address`, `performance_rating`)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ');
                $stmt->execute([$companyName, $regNo, $contactPerson, $email, $phone, $address, $rating]);
                header('Location: contractors.php?success=' . urlencode('Contractor registered successfully!'));
                exit;
            } catch (PDOException $e) {
                error_log('[MKR-Source-Hub] Add contractor error: ' . $e->getMessage());
                header('Location: contractors.php?error=' . urlencode('Failed to create contractor: Reg No or Email may already exist.'));
                exit;
            }
        } else {
            header('Location: contractors.php?error=' . urlencode('Please fill in all required fields.'));
            exit;
        }
    }

    private function updateContractor(): void
    {
        $contractorId   = (int) ($_POST['contractor_id'] ?? 0);
        $companyName    = trim($_POST['company_name'] ?? '');
        $regNo          = trim($_POST['registration_no'] ?? '');
        $contactPerson  = trim($_POST['contact_person'] ?? '');
        $email          = trim($_POST['email'] ?? '');
        $phone          = trim($_POST['phone_number'] ?? '');
        $address        = trim($_POST['address'] ?? '');
        $rating         = (float) ($_POST['performance_rating'] ?? 4.0);

        if ($contractorId > 0 && !empty($companyName) && !empty($regNo)) {
            try {
                $stmt = $this->pdo->prepare('
                    UPDATE `contractors`
                    SET `company_name` = ?, `registration_no` = ?, `contact_person` = ?, `email` = ?, `phone_number` = ?, `address` = ?, `performance_rating` = ?
                    WHERE `id` = ?
                ');
                $stmt->execute([$companyName, $regNo, $contactPerson, $email, $phone, $address, $rating, $contractorId]);
                header('Location: contractors.php?success=' . urlencode('Contractor updated successfully!'));
                exit;
            } catch (PDOException $e) {
                error_log('[MKR-Source-Hub] Update contractor error: ' . $e->getMessage());
                header('Location: contractors.php?error=' . urlencode('Failed to update contractor: ' . $e->getMessage()));
                exit;
            }
        }
    }

    private function deleteContractor(): void
    {
        $contractorId = (int) ($_POST['contractor_id'] ?? 0);
        if ($contractorId > 0) {
            try {
                $stmt = $this->pdo->prepare('DELETE FROM `contractors` WHERE `id` = ?');
                $stmt->execute([$contractorId]);
                header('Location: contractors.php?success=' . urlencode('Contractor deleted successfully!'));
                exit;
            } catch (PDOException $e) {
                error_log('[MKR-Source-Hub] Delete contractor error: ' . $e->getMessage());
                header('Location: contractors.php?error=' . urlencode('Cannot delete contractor: Active project assignments exist.'));
                exit;
            }
        }
    }

    public function getFilteredContractors(string $search, float $minRating): array
    {
        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = ' (c.`company_name` LIKE ? OR c.`registration_no` LIKE ? OR c.`contact_person` LIKE ? OR c.`email` LIKE ?) ';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        if ($minRating > 0) {
            $where[] = ' c.`performance_rating` >= ? ';
            $params[] = $minRating;
        }

        $sql = '
            SELECT c.*, COUNT(cp.`id`) AS `linked_jobs`
            FROM `contractors` c
            LEFT JOIN `contractor_projects` cp ON cp.`contractor_id` = c.`id`
        ';

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' GROUP BY c.`id` ORDER BY c.`performance_rating` DESC, c.`id` DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
