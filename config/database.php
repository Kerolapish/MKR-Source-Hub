<?php
/**
 * config/database.php
 * Secure PDO MySQL connection for MKR-Source-Hub
 * MKR Hartamas Sdn. Bhd.
 */

declare(strict_types=1);

define('DB_HOST',    '127.0.0.1');
define('DB_PORT',    '3306');
define('DB_NAME',    'mkr_source_hub');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a singleton PDO connection instance.
 *
 * @return PDO
 * @throws PDOException on connection failure
 */
function getDBConnection(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // In production, log the error and show a friendly message.
            // Never expose raw PDO exceptions to end users.
            error_log('[MKR-Source-Hub] DB Connection Error: ' . $e->getMessage());
            die(json_encode([
                'error'   => true,
                'message' => 'Database connection failed. Please contact the system administrator.',
            ]));
        }
    }

    return $pdo;
}

// Establish connection eagerly so errors surface immediately on include.
$pdo = getDBConnection();