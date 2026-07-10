<?php
session_start();
require_once __DIR__ . '/../models/SystemSettingsModel.php';

$settingsModel = new SystemSettingsModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_code'])) {
    $newCode = trim($_POST['verification_code']);

    if (preg_match('/^\d{6}$/', $newCode)) {
        $settingsModel->updateVerificationCode($newCode);
        $_SESSION['flash_message'] = "System verification code updated!";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Invalid code. Must be 6 digits.";
        $_SESSION['flash_type'] = "error";
    }

    header("Location: /settings");
    exit;
}
