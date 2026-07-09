<?php
require_once __DIR__ . '/../database.php';


class BackupModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
        $this->conn->query("SET time_zone = '+08:00'");

    }

    public function getSettings() {
        $query = "SELECT * FROM backup_settings WHERE id = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // If no row exists, create a default one
        if (!$result) {
            $this->initializeSettings();
            return $this->getSettings();
        }

        return $result;
    }

    public function updateSettings($frequency, $backup_time) {
        $query = "UPDATE backup_settings SET frequency = ?, backup_time = ? WHERE id = 1";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$frequency, $backup_time]);
    }

    public function updateBackupDates($lastBackup, $nextBackup) {
        $query = "UPDATE backup_settings SET last_backup = ?, next_backup = ? WHERE id = 1";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$lastBackup, $nextBackup]);
    }

    private function initializeSettings() {
        $query = "INSERT INTO backup_settings (id, frequency, backup_time, last_backup, next_backup)
                  VALUES (1, 'daily', '00:00:00', NULL, NULL)";
        $this->conn->exec($query);
    }
}
?>
