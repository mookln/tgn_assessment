<?php
// src/Logger.php
declare(strict_types=1);

namespace App\Traits;

use Exception;

trait Logger
{

    /**
     * Log an error
     */
    public function error(string $message): void
    {
        $this->log('ERROR', $message);
    }

    /**
     * Log a warning
     */
    public function warning(string $message): void
    {
        $this->log('WARNING', $message);
    }

    /**
     * Log a message
     */
    public function info(string $message): void
    {
        $this->log('INFO', $message);
    }

    /**
     * Write to log file
     */
    private function log(string $level, string $message): void
    {

        try {
            $timestamp = date('Y-m-d H:i:s');
            $logEntry = "[{$timestamp}] [{$level}] {$message}\n";

            $path = dirname(__DIR__,2) . "/{$_ENV['DATA_PATH']}/{$_ENV['LOG_FILE']}";

            file_put_contents($path, $logEntry, FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            echo "Failed to write to log: " . $e->getMessage() . "\n";
        }
    }
}
