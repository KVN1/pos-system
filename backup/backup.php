<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Access denied.');
}

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

/*USE IF YOUR XAMPP IS IN DRIVE C
$backupDir = 'C:\\xampp\\htdocs\\POSu\\backups\\';
$mysqldump = '"C:\\xampp\\mysql\\bin\\mysqldump.exe"';*/

if (!is_dir($backupDir)) mkdir($backupDir, 0775, true);

/**
 * Perform backup and update last/next backup timestamps in DB.
 *
 * @param string $type 'auto' or 'manual' (used in filename)
 * @param array $settings current settings from DB (includes frequency)
 * @return bool true on success
 */


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restore_file'])) {
    $restoreFile = $_POST['restore_file'];
    $restorePath = "C:\\xampp\\htdocs\\POSu\\backups\\" . basename($restoreFile);

    if (file_exists($restorePath)) {
        $command = "\"C:\\xampp\\mysql\\bin\\mysql.exe\" --user=$dbUser --password=$dbPass $dbName < \"$restorePath\"";
        exec($command, $output, $return_var);

        if ($return_var === 0) {
            $successMessage = "Database restored successfully from " . htmlspecialchars($restoreFile);
        } else {
            $errorMessage = "Restore failed. Return code: $return_var";
        }
    } else {
        $errorMessage = "Selected backup file not found.";
    }
}



function performBackup($type, $settings) {
    global $model, $dbHost, $dbUser, $dbPass, $dbName, $backupDir, $mysqldump;

    $backupFile = $backupDir . "{$type}_backup_" . date('Ymd_His') . ".sql";
    $command = "$mysqldump --user=$dbUser --password=$dbPass --host=$dbHost $dbName > \"$backupFile\"";

    exec($command, $output, $return_var);

    if ($return_var === 0 && file_exists($backupFile)) {
        // compute next backup based on frequency
        $now = time();
        switch ($settings['frequency']) {
            case 'every_15_min':
                $nextTimestamp = strtotime('+15 minutes', $now);
                break;
            case 'every_30_min':
                $nextTimestamp = strtotime('+30 minutes', $now);
                break;
            case 'hourly':
                $nextTimestamp = strtotime('+1 hour', $now);
                break;
            case 'daily':
                // next day, keep same configured time if provided
                if (!empty($settings['backup_time'])) {
                    $next = new DateTime(date('Y-m-d') . ' ' . $settings['backup_time']);
                    $next->modify('+1 day');
                    $nextTimestamp = $next->getTimestamp();
                } else {
                    $nextTimestamp = strtotime('+1 day', $now);
                }
                break;
            case 'weekly':
                // add 1 week
                if (!empty($settings['backup_time'])) {
                    $next = new DateTime(date('Y-m-d') . ' ' . $settings['backup_time']);
                    $next->modify('+1 week');
                    $nextTimestamp = $next->getTimestamp();
                } else {
                    $nextTimestamp = strtotime('+1 week', $now);
                }
                break;
            case 'monthly':
                // add 1 month
                if (!empty($settings['backup_time'])) {
                    $next = new DateTime(date('Y-m-d') . ' ' . $settings['backup_time']);
                    $next->modify('+1 month');
                    $nextTimestamp = $next->getTimestamp();
                } else {
                    $nextTimestamp = strtotime('+1 month', $now);
                }
                break;
            default:
                $nextTimestamp = strtotime('+1 day', $now);
                break;
        }

        // Save both last and next backup to DB (formatted Y-m-d H:i:s)
        $lastBackup = date('Y-m-d H:i:s', $now);
        $nextBackup = date('Y-m-d H:i:s', $nextTimestamp);
        $model->updateBackupDates($lastBackup, $nextBackup);

        return true;
    }

    return false;
}

/* -----------------------
   AUTO BACKUP LOGIC (triggered when page loads)
   ----------------------- */
$autoMessage = '';
$currentTime = date('H:i');
$lastBackup = !empty($settings['last_backup']) ? strtotime($settings['last_backup']) : 0;
$now = time();

$shouldBackup = false;

// For minute/hour-based intervals use elapsed time since last backup.
// For time-specific intervals (daily/weekly/monthly) require time match + date checks.
switch ($settings['frequency']) {
    case 'every_15_min':
        if ($now - $lastBackup >= 15 * 60) $shouldBackup = true;
        break;
    case 'every_30_min':
        if ($now - $lastBackup >= 30 * 60) $shouldBackup = true;
        break;
    case 'hourly':
        if ($now - $lastBackup >= 3600) $shouldBackup = true;
        break;
    case 'daily':
        // run once per day at configured backup_time
        if (!empty($settings['backup_time'])) {
            $todayRunTime = strtotime(date('Y-m-d') . ' ' . $settings['backup_time']);
            if ($now >= $todayRunTime && (date('Y-m-d', $lastBackup) !== date('Y-m-d', $now))) {
                $shouldBackup = true;
            }
        }
        break;
    case 'weekly':
        // run once per week on same weekday/time; assume run if 7+ days passed or today's time matches and last backup not this week
        if (!empty($settings['backup_time'])) {
            $todayRunTime = strtotime(date('Y-m-d') . ' ' . $settings['backup_time']);
            // check if today is same weekday as lastBackup's weekday? simpler: if 7 days passed since last backup and now >= configured time
            if ($now >= $todayRunTime && ($now - $lastBackup >= 6 * 24 * 3600)) {
                $shouldBackup = true;
            }
        }
        break;
    case 'monthly':
        if (!empty($settings['backup_time'])) {
            $todayRunTime = strtotime(date('Y-m-d') . ' ' . $settings['backup_time']);
            // if it's the 1st (or any day desired) — earlier code used day 1; here we trigger if month changed since last backup
            if ($now >= $todayRunTime && date('m', $now) != date('m', $lastBackup)) {
                $shouldBackup = true;
            }
        }
        break;
    default:
        // fallback: daily
        if ($now - $lastBackup >= 24 * 3600) $shouldBackup = true;
        break;
}

if ($shouldBackup) {
    if (performBackup('auto', $settings)) {
        $autoMessage = "Automatic backup completed at " . date('Y-m-d H:i:s');
    } else {
        $autoMessage = "Automatic backup failed at " . date('Y-m-d H:i:s');
    }
}

/* -----------------------
   MANUAL BACKUP (download)
   ----------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['manual_backup'])) {
    $backupFilePath = $backupDir . "manual_backup_" . date('Ymd_His') . ".sql";
    $command = "$mysqldump --user=$dbUser --password=$dbPass --host=$dbHost $dbName > \"$backupFilePath\"";
    exec($command, $output, $return_var);

    if ($return_var === 0 && file_exists($backupFilePath)) {
        // update only last_backup (because manual happened now) and compute next based on frequency
        $now = time();
        switch ($settings['frequency']) {
            case 'every_15_min':
                $nextTimestamp = strtotime('+15 minutes', $now);
                break;
            case 'every_30_min':
                $nextTimestamp = strtotime('+30 minutes', $now);
                break;
            case 'hourly':
                $nextTimestamp = strtotime('+1 hour', $now);
                break;
            case 'daily':
                $nextTimestamp = strtotime('+1 day', $now);
                break;
            case 'weekly':
                $nextTimestamp = strtotime('+1 week', $now);
                break;
            case 'monthly':
                $nextTimestamp = strtotime('+1 month', $now);
                break;
            default:
                $nextTimestamp = strtotime('+1 day', $now);
                break;
        }
        $model->updateBackupDates(date('Y-m-d H:i:s', $now), date('Y-m-d H:i:s', $nextTimestamp));

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($backupFilePath) . '"');
        header('Content-Length: ' . filesize($backupFilePath));
        readfile($backupFilePath);
        exit;
    } else {
        $errorMessage = "Backup failed (code $return_var)";
    }
}

/* -----------------------
   SAVE SCHEDULE FROM FORM
   ----------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $frequency = $_POST['frequency'];
    $time = $_POST['backup_time'];
    $model->updateSettings($frequency, $time);
    $settings = $model->getSettings(); // refresh settings after update
    $successMessage = "Automatic backup schedule updated successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Backup</title>
    <link rel="stylesheet" href="/POSu/styles/stylee.css">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/POSu/includes/sidebar.php'; ?>
    <style>
        main { padding: 20px; }
        .backup-section { background:#fff;padding:20px;border-radius:12px;box-shadow:0 0 10px rgba(0,0,0,0.1);max-width:700px;margin:auto; }
        h2{color:#333;}
        select,input[type="time"],button{padding:8px;margin-top:10px;}
    </style>
</head>
<body>
<main class="main-content">
    <div class="header">
        <h1>Database Backup</h1>
        <p>Current Time: <?= date('Y-m-d H:i:s') ?> (Asia/Manila)</p>
    </div>

    <div class="backup-section">
        <?php if (!empty($autoMessage)): ?>
            <p style="color:green;"><?= htmlspecialchars($autoMessage) ?></p>
        <?php endif; ?>
        <?php if (isset($errorMessage)): ?>
            <p style="color:red;"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>
        <?php if (isset($successMessage)): ?>
            <p style="color:green;"><?= htmlspecialchars($successMessage) ?></p>
        <?php endif; ?>

        <h2>Manual Backup</h2>
        <form method="post" onsubmit="return confirm('Download a database backup now?');">
            <button type="submit" name="manual_backup">Download Backup</button>
        </form>

        <hr>

        <h2>Automatic Backup Settings</h2>
        <form method="POST">
            <label>Frequency:</label>
            <select name="frequency" required>
                <option value="every_15_min" <?= $settings['frequency'] === 'every_15_min' ? 'selected' : '' ?>>Every 15 Minutes</option>
                <option value="every_30_min" <?= $settings['frequency'] === 'every_30_min' ? 'selected' : '' ?>>Every 30 Minutes</option>
                <option value="hourly" <?= $settings['frequency'] === 'hourly' ? 'selected' : '' ?>>Every Hour</option>
                <option value="daily" <?= $settings['frequency'] === 'daily' ? 'selected' : '' ?>>Daily</option>
                <option value="weekly" <?= $settings['frequency'] === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                <option value="monthly" <?= $settings['frequency'] === 'monthly' ? 'selected' : '' ?>>Monthly</option>
            </select>
            <br><br>
            <label>Backup Time:</label>
            <input type="time" name="backup_time" value="<?= htmlspecialchars($settings['backup_time']) ?>" required>
            <br><br>
            <button type="submit" name="save_settings">Save Schedule</button>
        </form>

        <br>
        <p><b>Last Backup:</b> <?= !empty($settings['last_backup']) ? $settings['last_backup'] : 'No backup yet' ?></p>
        <p><b>Next Scheduled Backup:</b> <?= !empty($settings['next_backup']) ? $settings['next_backup'] : 'Not scheduled' ?></p>

        <br>
        <button onclick="window.location.href='/POSu/views/settings.php'">Back to Settings</button>
    </div>
    <hr>
<h2>Restore from Backup</h2>
<form method="POST" onsubmit="return confirm('⚠️ This will overwrite your current database. Continue?');">
    <label>Select backup file to restore:</label><br>
    <select name="restore_file" required>
        <?php
        $files = glob('C:\\xampp\\htdocs\\POSu\\backups\\*.sql');
        foreach ($files as $file) {
            echo "<option value='" . basename($file) . "'>" . basename($file) . "</option>";
        }
        ?>
    </select>
    <br><br>
    <button type="submit" name="restore_now">Restore Database</button>
</form>

</main>
</body>
</html>
