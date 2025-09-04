<?php

namespace Controllers;

use Request\Request;
use Response\HTTPStatus;
use Cron;

class TestCron extends \Controller
{
    public function index()
    {
        $this->setValidRequestMethods('GET');
        
        // Otomatik cron kontrolü
        $this->checkAndRunCrons();
        
        // Process all cron jobs
        Cron::process();
        
        $this->response->setStatusCode(HTTPStatus::OK);
        $this->response->setContent("Cron jobs processed successfully.");
        $this->response->send();
    }
    
    public function dashboard()
    {
        $this->setValidRequestMethods('GET');
        
        // Otomatik cron kontrolü
        $this->checkAndRunCrons();
        
        // Get all cron execution records
        $records = \Records::getAll();
        $cronRecords = [];
        
        foreach ($records as $key => $value) {
            if (strpos($key, 'cron_') === 0) {
                $cronName = substr($key, 5); // Remove 'cron_' prefix
                $cronRecords[$cronName] = [
                    'lastRun' => date('Y-m-d H:i:s', $value),
                    'timestamp' => $value
                ];
            }
        }
        
        // Get all available cron classes
        $cronFiles = glob(__DIR__ . '/../Crons/*Cron.php');
        $availableCrons = [];
        
        foreach ($cronFiles as $file) {
            $className = basename($file, '.php');
            $availableCrons[] = $className;
        }
        
        // Generate simple HTML interface
        $html = $this->generateDashboardHTML($cronRecords, $availableCrons);
        
        $this->response->setStatusCode(HTTPStatus::OK);
        $this->response->setContent($html);
        $this->response->send();
    }
    
    private function checkAndRunCrons()
    {
        // Sadece 30 saniyede bir cron kontrolü yap
        $lastCronCheck = \Records::get("system_last_cron_check", 0);
        $currentTime = time();
        
        if ($currentTime - $lastCronCheck >= 30) {
            \Cron::process();
            \Records::set("system_last_cron_check", $currentTime);
        }
    }
    
    private function generateDashboardHTML(array $cronRecords, array $availableCrons): string
    {
        // Get project path for correct URLs
        $projectPath = \App::getProjectPath();
        $basePath = $projectPath ? '/' . $projectPath : '';
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cron Dashboard</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
        </head>
        <body>
            <section class="section">
                <div class="container">
                    <h1 class="title">Cron Dashboard</h1>
                    <p class="subtitle">Manage and monitor your cron jobs</p>
                    
                    <div class="box">
                        <h2 class="subtitle">Available Cron Jobs</h2>
                        <table class="table is-fullwidth is-striped">
                            <thead>
                                <tr>
                                    <th>Cron Name</th>
                                    <th>Period</th>
                                    <th>Last Execution</th>
                                    <th>Next Execution</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>';
        
        foreach ($availableCrons as $cronName) {
            // Get cron period
            $period = "Unknown";
            $lastRun = "Never";
            $nextRun = "Unknown";
            $status = "Unknown";
            
            // Include the cron file
            if (file_exists(__DIR__ . "/../Crons/{$cronName}.php")) {
                require_once __DIR__ . "/../Crons/{$cronName}.php";
                
                if (class_exists($cronName)) {
                    // Create instance to get period
                    $cron = new $cronName();
                    $periodSeconds = $cron->getPeriod();
                    $period = $this->formatPeriod($periodSeconds);
                    
                    // Get last run time
                    if (isset($cronRecords[$cronName])) {
                        $lastRun = $cronRecords[$cronName]['lastRun'];
                        $lastRunTimestamp = $cronRecords[$cronName]['timestamp'];
                        $nextRunTimestamp = $lastRunTimestamp + $periodSeconds;
                        $nextRun = date('Y-m-d H:i:s', $nextRunTimestamp);
                        
                        // Calculate time remaining
                        $currentTime = time();
                        if ($nextRunTimestamp > $currentTime) {
                            $diff = $nextRunTimestamp - $currentTime;
                            $hours = floor($diff / 3600);
                            $minutes = floor(($diff % 3600) / 60);
                            $seconds = $diff % 60;
                            $nextRun .= " (" . sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds) . " remaining)";
                            $status = "Waiting";
                        } else {
                            $nextRun .= " (Due)";
                            $status = "Due";
                        }
                    } else {
                        $nextRun = "Now (Due)";
                        $status = "Due";
                    }
                }
            }
            
            $html .= "
                                <tr>
                                    <td>{$cronName}</td>
                                    <td>{$period}</td>
                                    <td>{$lastRun}</td>
                                    <td>{$nextRun}</td>
                                    <td>{$status}</td>
                                    <td>
                                        <form method='POST' action='{$basePath}/TestCron/run/{$cronName}' style='display: inline;'>
                                            <button class='button is-primary is-small' type='submit'>Run Now</button>
                                        </form>
                                    </td>
                                </tr>";
        }
        
        $html .= '
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="box">
                        <h2 class="subtitle">Manual Execution</h2>
                        <form method="POST" action="' . $basePath . '/TestCron/runall">
                            <button class="button is-success" type="submit">Run All Cron Jobs</button>
                        </form>
                    </div>
                </div>
            </section>
        </body>
        </html>';
        
        return $html;
    }
    
    private function formatPeriod(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . " seconds";
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;
            if ($remainingSeconds > 0) {
                return $minutes . " min " . $remainingSeconds . " sec";
            }
            return $minutes . " minutes";
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            if ($minutes > 0) {
                return $hours . " hr " . $minutes . " min";
            }
            return $hours . " hours";
        }
    }
    
    public function runall()
    {
        $this->setValidRequestMethods('POST');
        
        // Process all cron jobs
        \Cron::process();
        
        $this->response->setStatusCode(HTTPStatus::OK);
        $this->response->setContent("All cron jobs executed successfully.");
        $this->response->send();
    }
    
    public function run(string $cronName)
    {
        $this->setValidRequestMethods('POST');
        
        // Try to find and run the specific cron
        $cronFile = __DIR__ . "/../Crons/{$cronName}.php";
        
        if (file_exists($cronFile)) {
            require_once $cronFile;
            
            if (class_exists($cronName)) {
                $reflection = new \ReflectionClass($cronName);
                
                if ($reflection->isSubclassOf('Cron') && !$reflection->isAbstract()) {
                    $cron = new $cronName();
                    $cron->run();
                    
                    // Update last execution time
                    \Records::set("cron_" . $cronName, time());
                    
                    $this->response->setStatusCode(HTTPStatus::OK);
                    $this->response->setContent("Cron job '{$cronName}' executed successfully.");
                    $this->response->send();
                    return;
                }
            }
        }
        
        $this->response->setStatusCode(HTTPStatus::NOT_FOUND);
        $this->response->setContent("Cron job '{$cronName}' not found.");
        $this->response->send();
    }
}