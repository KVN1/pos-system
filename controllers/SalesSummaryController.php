<?php
require_once __DIR__ . '/../SalesSummaryModel.php';

class SalesSummaryController {
    private $model;

    public function __construct() {
        $this->model = new SalesSummaryModel();
    }

    public function saveSale($data) {
        return $this->model->addSale(
            $data['user_id'],
            $data['payment_method'],
            $data['reference_number'],
            $data['total_amount'],
            $data['cash_given'],
            $data['change_amount'],
            $data['discount']
        );
    }
}
