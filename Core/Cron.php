<?php

abstract class Cron
{
    protected int $period = 60; // Default period in seconds
    protected string $cronName;

    public function __construct()
    {
        // Set the cron name based on the class name
        $this->cronName = static::class;
    }

    /**
     * Abstract method that must be implemented by all cron classes
     */
    abstract public function run(): void;

    /**
     * Set the period for this cron job in seconds
     */
    protected function setPeriod(int $seconds): void
    {
        $this->period = $seconds;
    }

    /**
     * Get the period for this cron job
     */
    public function getPeriod(): int
    {
        return $this->period;
    }

    /**
     * Get the name of this cron job
     */
    public function getName(): string
    {
        return $this->cronName;
    }

    /**
     * Process all cron jobs
     */
    public static function process(): void
    {
        // Get all cron files from App/Crons directory
        $cronFiles = glob(__DIR__ . '/../App/Crons/*Cron.php');
        
        foreach ($cronFiles as $file) {
            // Include the file
            require_once $file;
            
            // Get class name from file name
            $className = basename($file, '.php');
            
            // Check if class exists and is instantiable
            if (class_exists($className)) {
                $reflection = new \ReflectionClass($className);
                
                // Only process classes that extend Cron
                if ($reflection->isSubclassOf(self::class) && !$reflection->isAbstract()) {
                    // Create instance and run
                    $cron = new $className();
                    $cron->execute();
                }
            }
        }
    }

    /**
     * Execute this cron job if its period has elapsed
     */
    public function execute(): void
    {
        // Get last execution time from Records
        $lastRun = \Records::get("cron_" . $this->cronName, 0);
        $currentTime = time();
        
        // Check if period has elapsed
        if ($currentTime - $lastRun >= $this->period) {
            // Execute the cron job
            $this->run();
            
            // Update last execution time
            \Records::set("cron_" . $this->cronName, $currentTime);
        }
    }
}