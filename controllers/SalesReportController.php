<?php
require_once __DIR__ . '/../SalesReportModel.php';
require_once __DIR__ . '/../ActivityLogController.php';

class SalesReportController {
    private $model;
    private $activityLog;

    public function __construct() {
        $this->model = new SalesReportModel();
        $this->activityLog = new ActivityLogController();
    }

    public function getFilteredSales($from = null, $to = null, $payment = '', $user = '', $page = 1) {
        $limit = 15;
        $offset = ($page - 1) * $limit;
        return $this->model->getFilteredSales($from, $to, $payment, $user, $limit, $offset);
    }

    public function getTotalSales($from = null, $to = null, $payment = '', $user = '') {
        return $this->model->getTotalSales($from, $to, $payment, $user);
    }

    public function getTotalTransactions($from = null, $to = null, $payment = '', $user = '') {
        return $this->model->getTotalTransactions($from, $to, $payment, $user);
    }

    public function getBestSellers($from = null, $to = null, $page = 1) {
        return $this->model->getBestSellers($from, $to);
    }

    public function getSlowMovingProducts($from = null, $to = null, $page = 1) {
        return $this->model->getSlowMovingProducts($from, $to);
    }

    public function getAllUsers() {
        return $this->model->getAllUsers();
    }

public function getSaleItems($saleId) {
    return $this->model->getSaleItems($saleId);
}



    public function returnSale($sale_id, $user_id, $reason = '') {
        if (!empty($sale_id) && !empty($user_id)) {
            $updateSuccess = $this->model->returnSale($sale_id, $user_id, $reason);

            if ($updateSuccess) {
                // Log the activity
                $action = "Returned sale ID $sale_id";
                $details = "Reason: $reason";
                $this->activityLog->addLog($user_id, $action, $details);
            }

            return $updateSuccess;
        }
        return false;
    }

    public function exportFilteredSales($from = null, $to = null, $payment = '', $user = '') {
        $salesData = $this->model->getFilteredSales($from, $to, $payment, $user, 1000000, 0); // get all
        $filename = "sales_report_" . date('Ymd_His') . ".csv";

        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $output = fopen('php://output', 'w');

        // Column headers
        fputcsv($output, ['Sale ID', 'User', 'Total Amount', 'Payment Method', 'Date']);

        foreach ($salesData as $sale) {
            fputcsv($output, [
                $sale['id'] ?? '',
                $sale['user_name'] ?? '',
                $sale['total_amount'] ?? '',
                $sale['payment_method'] ?? '',
                $sale['sale_date'] ?? ''
            ]);
        }

        fclose($output);
        exit;
    }
}
?>
