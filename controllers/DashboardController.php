<?php
/**
 * controllers/DashboardController.php
 * Backend logic and data retrieval for Dashboard (index.php)
 * MKR Hartamas Sdn. Bhd.
 */

declare(strict_types=1);

class DashboardController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getDashboardData(): array
    {
        try {
            $totalHardware = (int) $this->pdo
                ->query('SELECT COUNT(*) FROM `hardware_items`')
                ->fetchColumn();

            $totalContractors = (int) $this->pdo
                ->query('SELECT COUNT(DISTINCT `id`) FROM `contractors`')
                ->fetchColumn();

            $recentHardware = $this->pdo
                ->query(
                    'SELECT h.`item_name`, h.`category`, h.`unit_cost`, h.`lead_time_days`,
                            h.`created_at`, s.`supplier_name`
                     FROM `hardware_items` h
                     LEFT JOIN `suppliers` s ON s.`id` = h.`supplier_id`
                     ORDER BY h.`created_at` DESC
                     LIMIT 5'
                )
                ->fetchAll();

            $categoryStats = $this->pdo
                ->query(
                    'SELECT `category`, COUNT(*) AS `total`
                     FROM `hardware_items`
                     GROUP BY `category`
                     ORDER BY `total` DESC'
                )
                ->fetchAll();

            return [
                'totalHardware'    => $totalHardware,
                'totalContractors' => $totalContractors,
                'recentHardware'   => $recentHardware,
                'categoryStats'    => $categoryStats,
                'dbError'          => null,
            ];
        } catch (PDOException $e) {
            error_log('[MKR-Source-Hub] Dashboard query error: ' . $e->getMessage());
            return [
                'totalHardware'    => 0,
                'totalContractors' => 0,
                'recentHardware'   => [],
                'categoryStats'    => [],
                'dbError'          => 'Unable to load dashboard data. Please check the database connection.',
            ];
        }
    }
}
