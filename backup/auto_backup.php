<?php
date_default_timezone_set('Asia/Manila');
require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/models/BackupModel.php';
$model = new BackupModel();
$settings = $model->getSettings();

$dbHost = 'localhost';
$dbName = 'infinite_pos';
$dbUser = 'root';
$dbPass = '';
$backupDir = 'D:\\xampp\\htdocs\\POSu\\backups\\';
$mysqldump = '"D:\\xampp\\mysql\\bin\\mysqldump.exe"';

/*USE THIS IS YOUR XAMPP IS IN DRIVE C
$backupDir = 'C:\\xampp\\htdocs\\POSu\\backups\\';
$mysqldump = '"C:\\xampp\\mysql\\bin\\mysqldump.exe"';*/

if (!is_dir($backupDir)) mkdir($backupDir, 0775, true);

$currentTime = date('H:i');
$shouldBackup = false;

switch ($settings['frequency']) {
    case 'every_15_min':
        $shouldBackup = (intval(date('i')) % 15 === 0);
        break;
    case 'every_30_min':
        $shouldBackup = (intval(date('i')) % 30 === 0);
        break;
    case 'hourly':
        $shouldBackup = (date('i') === '00');
        break;
    case 'daily':
        $shouldBackup = ($currentTime === $settings['backup_time']);
        break;
    case 'weekly':
        $shouldBackup = (date('N') == 7 && $currentTime === $settings['backup_time']);
        break;
    case 'monthly':
        $shouldBackup = (date('j') == 1 && $currentTime === $settings['backup_time']);
        break;
}

if ($shouldBackup) {
    $backupFile = $backupDir . "auto_backup_" . date('Ymd_His') . ".sql";
    $command = "$mysqldump --user=$dbUser --password=$dbPass --host=$dbHost $dbName > \"$backupFile\"";
    exec($command, $output, $return_var);

    if ($return_var === 0 && file_exists($backupFile)) {
        $model->updateBackupDates(date('Y-m-d H:i:s'), date('Y-m-d H:i:s', strtotime('+1 ' . $settings['frequency'])));
        echo "✅ Auto backup created: " . basename($backupFile) . PHP_EOL;
    } else {
        echo "❌ Backup failed at " . date('Y-m-d H:i:s') . PHP_EOL;
    }
} else {
    echo "⏳ Not yet time for backup (" . date('Y-m-d H:i:s') . ")" . PHP_EOL;
}
