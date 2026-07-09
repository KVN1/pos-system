<?php
class BackupSchedulerController {

    public function checkAndRunBackup() {
        // Example logic – you can adjust this
        $backupPath = "D:\xampp\htdocs\POSu\backups/";

        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0777, true);
        }

        // Example: automatically create a timestamped backup
        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = $backupPath . "backup_" . $timestamp . ".sql";

        // run mysqldump
        $db = 'infinite_pos';
        $user = 'root';
        $pass = '';
        $cmd = "C:/xampp/mysql/bin/mysqldump.exe -u $user $db > \"$backupFile\"";
        exec($cmd);

        return "Backup completed: " . $backupFile;
    }
}
?>
