<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/database.php';

class SystemSettingsModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getVerificationCode() {
        $stmt = $this->db->prepare("SELECT verification_code FROM system_settings LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['verification_code'] ?? null;
    }

    public function updateVerificationCode($newCode) {
        $stmt = $this->db->prepare("UPDATE system_settings SET verification_code = ? WHERE id = 1");
        return $stmt->execute([$newCode]);
    }
}
