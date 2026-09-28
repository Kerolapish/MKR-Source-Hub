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

            $recentProjects = $this->pdo
                ->query(
                    'SELECT `project_title`, `client_agency`, `status`, `budget`, `start_date`, `end_date`
                     FROM `projects`
                     ORDER BY `created_at` DESC
                     LIMIT 5'
                )
                ->fetchAll();

            $topContractors = $this->pdo
                ->query(
                    'SELECT c.`company_name`, c.`contact_person`, c.`performance_rating`,
                            COUNT(cp.`id`) AS project_count
                     FROM `contractors` c
                     LEFT JOIN `contractor_projects` cp ON cp.`contractor_id` = c.`id`
                     GROUP BY c.`id`
                     ORDER BY c.`performance_rating` DESC
                     LIMIT 4'
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
                'recentProjects'  => $recentProjects,
                'topContractors'   => $topContractors,
                'categoryStats'    => $categoryStats,
                'dbError'          => null,
            ];
        } catch (PDOException $e) {
            error_log('[MKR-Source-Hub] Dashboard query error: ' . $e->getMessage());
            return [
                'totalHardware'    => 0,
                'totalContractors' => 0,
                'recentProjects'  => [],
                'topContractors'   => [],
                'categoryStats'    => [],
                'dbError'          => 'Unable to load dashboard data. Please check the database connection.',
            ];
        }
    }
}
