<?php

class PaymentCron extends \Cron
{
    public function __construct()
    {
        parent::__construct();
        // Set this cron to run every 120 seconds (2 minutes)
        $this->setPeriod(120);
    }
    
    public function run(): void
    {
        // Log that this cron is running
        \Records::log("PaymentCron executed at " . date('Y-m-d H:i:s'));
        
        // Here you would implement your payment processing logic
        // For example:
        // 1. Check for pending payments
        // 2. Process payments
        // 3. Update payment statuses
        
        echo "Processing payments...\n";
    }
}