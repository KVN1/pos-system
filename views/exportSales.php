<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/POSu/controllers/SalesReportController.php';

$reportController = new SalesReportController();

// Get filters from GET
$from = $_GET['from_date'] ?? null;
$to = $_GET['to_date'] ?? null;
$payment = $_GET['payment_method'] ?? '';
$user = $_GET['user_id'] ?? '';

// Get all filtered sales (no pagination)
$sales = $reportController->getFilteredSales($from, $to, $payment, $user, null); // null = all

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="sales_report.csv"');

$output = fopen('php://output', 'w');

// Header row
fputcsv($output, ['ID','User','Total Amount','Payment Method','Sale Date','Status']);

// Data rows
foreach($sales as $sale) {
    $userName = $sale['first_name'].' '.$sale['last_name'];
    fputcsv($output, [
        $sale['id'],
        $userName,
        number_format($sale['total_amount'],2),
        $sale['payment_method'],
        $sale['sale_date'],
        $sale['status'] ?? ''
    ]);
}

fclose($output);
exit;
?>
