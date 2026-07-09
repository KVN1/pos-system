<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/database.php';

class ActivityLogger {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Log an activity.
     * @param int $userId The ID of the user performing the action
     * @param string $action The action type (e.g., 'Returned Sale', 'Added Product')
     * @param string $details Additional info to store
     */
    public function log($userId, $action, $details = '') {
        if (is_array($details)) {
    $details = implode("; ", $details);
}
$stmt = $this->db->prepare("
    INSERT INTO activity_log (user_id, action, details, log_time) 
    VALUES (?, ?, ?, NOW())
");

        $stmt->execute([$userId, $action, $details]);
    }
}
?>
