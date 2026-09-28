<?php
/**
 * controllers/SourcingController.php
 * Backend logic and POST handlers for Hardware Catalog (sourcing.php)
 * MKR Hartamas Sdn. Bhd.
 */

declare(strict_types=1);

class SourcingController
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

        if ($action === 'create_item') {
            $this->createItem();
        } elseif ($action === 'update_item') {
            $this->updateItem();
        } elseif ($action === 'delete_item') {
            $this->deleteItem();
        }
    }

    private function createItem(): void
    {
        $itemName     = trim($_POST['item_name'] ?? '');
        $supplierId   = (int) ($_POST['supplier_id'] ?? 0);
        $category     = trim($_POST['category'] ?? '');
        $unitCost     = (float) ($_POST['unit_cost'] ?? 0.0);
        $leadTimeDays = (int) ($_POST['lead_time_days'] ?? 7);
        $spec         = trim($_POST['specifications'] ?? '');

        if (!empty($itemName) && $supplierId > 0 && !empty($category)) {
            try {
                $stmt = $this->pdo->prepare('
                    INSERT INTO `hardware_items`
                    (`supplier_id`, `item_name`, `category`, `specifications`, `unit_cost`, `lead_time_days`)
                    VALUES (?, ?, ?, ?, ?, ?)
                ');
                $stmt->execute([$supplierId, $itemName, $category, $spec, $unitCost, $leadTimeDays]);
                header('Location: sourcing.php?success=' . urlencode('Item created successfully!'));
                exit;
            } catch (PDOException $e) {
                error_log('[MKR-Source-Hub] Add item error: ' . $e->getMessage());
                header('Location: sourcing.php?error=' . urlencode('Failed to create item: ' . $e->getMessage()));
                exit;
            }
        } else {
            header('Location: sourcing.php?error=' . urlencode('Please fill in all required fields.'));
            exit;
        }
    }

    private function updateItem(): void
    {
        $itemId       = (int) ($_POST['item_id'] ?? 0);
        $itemName     = trim($_POST['item_name'] ?? '');
        $supplierId   = (int) ($_POST['supplier_id'] ?? 0);
        $category     = trim($_POST['category'] ?? '');
        $unitCost     = (float) ($_POST['unit_cost'] ?? 0.0);
        $leadTimeDays = (int) ($_POST['lead_time_days'] ?? 7);
        $spec         = trim($_POST['specifications'] ?? '');

        if ($itemId > 0 && !empty($itemName) && $supplierId > 0 && !empty($category)) {
            try {
                $stmt = $this->pdo->prepare('
                    UPDATE `hardware_items`
                    SET `supplier_id` = ?, `item_name` = ?, `category` = ?, `specifications` = ?, `unit_cost` = ?, `lead_time_days` = ?
                    WHERE `id` = ?
                ');
                $stmt->execute([$supplierId, $itemName, $category, $spec, $unitCost, $leadTimeDays, $itemId]);
                header('Location: sourcing.php?success=' . urlencode('Item updated successfully!'));
                exit;
            } catch (PDOException $e) {
                error_log('[MKR-Source-Hub] Update item error: ' . $e->getMessage());
                header('Location: sourcing.php?error=' . urlencode('Failed to update item: ' . $e->getMessage()));
                exit;
            }
        }
    }

    private function deleteItem(): void
    {
        $itemId = (int) ($_POST['item_id'] ?? 0);
        if ($itemId > 0) {
            try {
                $stmt = $this->pdo->prepare('DELETE FROM `hardware_items` WHERE `id` = ?');
                $stmt->execute([$itemId]);
                header('Location: sourcing.php?success=' . urlencode('Item deleted successfully!'));
                exit;
            } catch (PDOException $e) {
                error_log('[MKR-Source-Hub] Delete item error: ' . $e->getMessage());
                header('Location: sourcing.php?error=' . urlencode('Cannot delete item: It may be referenced in projects.'));
                exit;
            }
        }
    }

    public function getSuppliers(): array
    {
        return $this->pdo->query('SELECT `id`, `supplier_name` FROM `suppliers` ORDER BY `supplier_name` ASC')->fetchAll();
    }

    public function getFilteredItems(string $search, string $catFilter, int $supFilter): array
    {
        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = ' (h.`item_name` LIKE ? OR h.`specifications` LIKE ? OR s.`supplier_name` LIKE ?) ';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        if (!empty($catFilter)) {
            $where[] = ' h.`category` = ? ';
            $params[] = $catFilter;
        }

        if ($supFilter > 0) {
            $where[] = ' h.`supplier_id` = ? ';
            $params[] = $supFilter;
        }

        $sql = '
            SELECT h.*, s.`supplier_name`
            FROM `hardware_items` h
            LEFT JOIN `suppliers` s ON s.`id` = h.`supplier_id`
        ';

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY h.`id` DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
