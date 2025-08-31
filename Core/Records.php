<?php

/**
 * Records - Static class for JSON-based records storage and logging
 */
class Records
{
    private static string $recordsFile = '';
    private static string $logsDir = '';
    private static ?object $cache = null;
    private static bool $cacheEnabled = true;
    private static int $maxLogFiles = 20;

    /**
     * Initialize paths
     */
    private static function init(): void
    {
        if (empty(self::$recordsFile)) {
            self::$recordsFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Cache' . DIRECTORY_SEPARATOR . 'records.json';
            self::$logsDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Cache' . DIRECTORY_SEPARATOR . 'logs';
            
            // Ensure the records file exists
            if (!file_exists(self::$recordsFile)) {
                self::createRecordsFile();
            }
            
            // Ensure the logs directory exists
            if (!is_dir(self::$logsDir)) {
                mkdir(self::$logsDir, 0755, true);
            }
        }
    }

    /**
     * Create records.json file
     */
    private static function createRecordsFile(): void
    {
        $dir = dirname(self::$recordsFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents(self::$recordsFile, '{}');
    }

    /**
     * Load records with caching
     */
    private static function loadRecords(): object
    {
        self::init();

        if (self::$cacheEnabled && self::$cache !== null) {
            return self::$cache;
        }

        if (!file_exists(self::$recordsFile)) {
            self::createRecordsFile();
        }

        $contents = file_get_contents(self::$recordsFile);
        if ($contents === false) {
            throw new Exception("Unable to read records file: " . self::$recordsFile);
        }

        $decoded = json_decode($contents);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON in records file: " . json_last_error_msg());
        }

        if (self::$cacheEnabled) {
            self::$cache = $decoded;
        }

        return $decoded;
    }

    /**
     * Save records and clear cache
     */
    private static function saveRecords(object $records): void
    {
        self::init();
        
        $json = json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new Exception("Unable to encode records to JSON: " . json_last_error_msg());
        }

        if (file_put_contents(self::$recordsFile, $json) === false) {
            throw new Exception("Unable to write to records file: " . self::$recordsFile);
        }

        self::$cache = null;
    }

    /**
     * Set key value
     */
    public static function set(string $key, $value): void
    {
        $records = self::loadRecords();
        $records->$key = $value;
        self::saveRecords($records);
    }

    /**
     * Get key value or default
     */
    public static function get(string $key, $defaultValue = "")
    {
        $records = self::loadRecords();
        return property_exists($records, $key) ? $records->$key : $defaultValue;
    }

    /**
     * Merge array with existing key
     */
    public static function merge(string $key, array $value): void
    {
        $records = self::loadRecords();
        
        if (!property_exists($records, $key)) {
            $records->$key = [];
        }
        
        // Convert existing to array and merge properly
        $existingArray = (array)$records->$key;
        
        // Check if this is associative or indexed array
        $isAssociative = false;
        foreach ($value as $k => $v) {
            if (!is_int($k)) {
                $isAssociative = true;
                break;
            }
        }
        
        if ($isAssociative) {
            // For associative arrays, use + operator (legacy behavior)
            $records->$key = $existingArray + $value;
        } else {
            // For indexed arrays, use array_merge to properly append
            $records->$key = array_merge($existingArray, $value);
        }
        
        self::saveRecords($records);
    }

    /**
     * Merge two arrays into a new key
     */
    public static function mergeArrays(string $sourceKey1, string $sourceKey2, string $targetKey): bool
    {
        $records = self::loadRecords();
        
        // Check if both source arrays exist
        if (!property_exists($records, $sourceKey1) || !property_exists($records, $sourceKey2)) {
            return false;
        }
        
        $array1 = (array)$records->$sourceKey1;
        $array2 = (array)$records->$sourceKey2;
        
        // Merge arrays
        $mergedArray = array_merge($array1, $array2);
        
        // Save to target key
        $records->$targetKey = $mergedArray;
        self::saveRecords($records);
        
        return true;
    }

    /**
     * Append value to array
     */
    public static function append(string $key, $value): void
    {
        $records = self::loadRecords();
        
        if (!property_exists($records, $key)) {
            $records->$key = [];
        }
        
        $records->$key[] = $value;
        self::saveRecords($records);
    }

    /**
     * Check if value exists in array
     */
    public static function arrayElementExists(string $key, $value): bool
    {
        $records = self::loadRecords();
        
        if (!property_exists($records, $key)) {
            return false;
        }
        
        return in_array($value, (array)$records->$key, true);
    }

    /**
     * Remove value from array
     */
    public static function removeFromArray(string $key, $value): void
    {
        $records = self::loadRecords();
        
        if (!property_exists($records, $key)) {
            return;
        }
        
        $array = (array)$records->$key;
        $records->$key = array_values(array_filter($array, function($item) use ($value) {
            return $item !== $value;
        }));
        
        self::saveRecords($records);
    }

    /**
     * Remove key
     */
    public static function remove(string $key): void
    {
        $records = self::loadRecords();
        
        if (property_exists($records, $key)) {
            unset($records->$key);
            self::saveRecords($records);
        }
    }

    /**
     * Check if key exists
     */
    public static function exists(string $key): bool
    {
        $records = self::loadRecords();
        return property_exists($records, $key);
    }

    /**
     * Get all records
     */
    public static function getAll(): array
    {
        $records = self::loadRecords();
        return (array)$records;
    }

    /**
     * Clear all records
     */
    public static function clear(): void
    {
        self::saveRecords((object)[]);
    }

    /**
     * Clear all records (alias)
     */
    public static function clearAll(): void
    {
        self::clear();
    }

    /**
     * Get array count
     */
    public static function getArrayCount(string $key): int
    {
        $records = self::loadRecords();
        
        if (!property_exists($records, $key)) {
            return 0;
        }
        
        return count((array)$records->$key);
    }

    /**
     * Increment value
     */
    public static function increment(string $key, $amount = 1): void
    {
        $current = self::get($key, 0);
        self::set($key, $current + $amount);
    }

    /**
     * Decrement value
     */
    public static function decrement(string $key, $amount = 1): void
    {
        $current = self::get($key, 0);
        self::set($key, $current - $amount);
    }

    /**
     * Enable/disable caching
     */
    public static function setCacheEnabled(bool $enabled): void
    {
        self::$cacheEnabled = $enabled;
        if (!$enabled) {
            self::$cache = null;
        }
    }

    /**
     * Clear cache
     */
    public static function clearCache(): void
    {
        self::$cache = null;
    }

    /**
     * Get file path
     */
    public static function getFilePath(): string
    {
        self::init();
        return self::$recordsFile;
    }

    // LOGGING METHODS

    /**
     * Core logging method
     */
    public static function log(string $message, string $level = 'info'): void
    {
        self::init();

        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] [{$level}] {$message}\n";
        
        $logFileName = self::$logsDir . DIRECTORY_SEPARATOR . 'log_' . date('Y_m_d_H_i') . '.log';
        
        $oldContents = '';
        if (file_exists($logFileName)) {
            $oldContents = file_get_contents($logFileName);
        }
        
        if (file_put_contents($logFileName, $oldContents . $logEntry) === false) {
            throw new Exception("Unable to write to log file: " . $logFileName);
        }
        
        self::removeOldLogs();
    }

    /**
     * Print and log message
     */
    public static function printAndLog($message, string $level = 'info'): void
    {
        // Print message for display
        echo $message;
        
        // Call shared log() method
        self::log($message, $level);
    }

    /**
     * Remove old log files
     */
    private static function removeOldLogs(): void
    {
        // Get a list of all files in the directory
        $files = scandir(self::$logsDir);
        if ($files === false) {
            return;
        }

        // Filter out files that don't match the .log extension
        $logFiles = array_filter($files, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'log';
        });

        // Sort by modified time (oldest first)
        usort($logFiles, function($a, $b) {
            $pathA = self::$logsDir . DIRECTORY_SEPARATOR . $a;
            $pathB = self::$logsDir . DIRECTORY_SEPARATOR . $b;
            return filemtime($pathA) - filemtime($pathB);
        });

        // Calculate files to keep
        $numLogFiles = count($logFiles);

        // Delete excess log files
        if ($numLogFiles > self::$maxLogFiles) {
            $filesToDelete = array_slice($logFiles, 0, $numLogFiles - self::$maxLogFiles);
            foreach ($filesToDelete as $file) {
                $filePath = self::$logsDir . DIRECTORY_SEPARATOR . $file;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }
    }

    /**
     * Set max log files
     */
    public static function setMaxLogFiles(int $maxFiles): void
    {
        self::$maxLogFiles = max(1, $maxFiles);
    }

    /**
     * Get max log files
     */
    public static function getMaxLogFiles(): int
    {
        return self::$maxLogFiles;
    }

    /**
     * Get log files
     */
    public static function getLogFiles(): array
    {
        self::init();

        $files = scandir(self::$logsDir);
        if ($files === false) {
            return [];
        }

        return array_filter($files, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'log';
        });
    }

    /**
     * Get log file content
     */
    public static function getLogContent(string $fileName): ?string
    {
        self::init();
        
        $filePath = self::$logsDir . DIRECTORY_SEPARATOR . $fileName;
        
        if (!file_exists($filePath)) {
            return null;
        }
        
        return file_get_contents($filePath);
    }

    /**
     * Clear all logs
     */
    public static function clearAllLogs(): void
    {
        self::init();

        $logFiles = self::getLogFiles();
        foreach ($logFiles as $file) {
            $filePath = self::$logsDir . DIRECTORY_SEPARATOR . $file;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    /**
     * Get logs directory
     */
    public static function getLogsDir(): string
    {
        self::init();
        return self::$logsDir;
    }

    // Log convenience methods
    public static function logInfo($message): void
    {
        self::log($message, 'info');
    }

    public static function logWarning($message): void
    {
        self::log($message, 'warning');
    }

    public static function logError($message): void
    {
        self::log($message, 'error');
    }

    public static function logDebug($message): void
    {
        self::log($message, 'debug');
    }
}