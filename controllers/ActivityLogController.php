<?php
require_once __DIR__ . '/../ActivityLogModel.php';

class ActivityLogController {
    private $model;

    public function __construct() {
        $this->model = new ActivityLogModel();
    }

    // Add a log
    public function addLog($user_id, $action, $details) {
        $this->model->log($user_id, $action, $details);
    }

    // Get logs with optional pagination
    public function getLogs($search = '', $startDate = '', $endDate = '', $limit = 50, $offset = 0) {
        return $this->model->getLogs($search, $startDate, $endDate, $limit, $offset);
    }

    // Get total count for pagination
    public function getLogsCount($search = '', $startDate = '', $endDate = '') {
        return $this->model->getLogsCount($search, $startDate, $endDate);
    }


    public function getProducts() {
        return $this->model->getProducts();
    }

    public function getCategories() {
        return $this->model->getCategories();
    }

    public function getBatches() {
        return $this->model->getBatches();
    }
}
?>
