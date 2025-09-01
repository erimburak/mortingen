<?php

namespace Controllers;

use Controller;
use Bulma;
use HTML;
use BulmaClass;
use View;
use Request\Request;
use Request\Method;

require_once __DIR__ . '/../../Core/Records.php';

class TestRecords extends Controller
{
    public function index()
    {
        $content = "";
        $alerts = [];
        
        try {
            $content .= Bulma::Title('📂 Records & Logger Test System', 1);
            $content .= Bulma::Subtitle('Testing modernized Records and Logger classes', 3);
            
            // Handle form submissions
            $this->handleFormSubmissions($alerts);
            
            // Display current records
            $content .= $this->displayCurrentRecords();
            
            // Display test forms
            $content .= $this->displayTestForms();
            
            // Display log files
            $content .= $this->displayLogFiles();
            
        } catch (\Exception $e) {
            $alerts[] = Bulma::Notification(
                '❌ System Error: ' . htmlspecialchars($e->getMessage()),
                false,
                [BulmaClass::IS_DANGER]
            );
        }
        
        // Build final page
        $alertsContent = View::concat(...$alerts);
        $pageContent = Bulma::Container(
            new View(
                $alertsContent->__toString() .
                (string)$content .
                '<div class="box has-text-centered">' .
                '<a class="button is-primary" href="/mortingen">🏠 Back to Home</a>' .
                '</div>'
            )
        );
        
        $this->response->setContentType(\Response\ContentType::TEXT_HTML);
        $this->response->setContent(
            Bulma::Html($pageContent, 'Records & Logger Test - Mortingen Framework')
        );
    }
    
    private function handleFormSubmissions(&$alerts): void
    {
        if (Request::getMethod() === Method::POST) {
            $action = Request::getDataAll()['action'] ?? '';
            
            try {
                switch ($action) {
                    case 'set_record':
                        $key = Request::getData('key') ?? '';
                        $value = Request::getData('value') ?? '';
                        if (!empty($key)) {
                            \Records::set($key, $value);
                            $alerts[] = Bulma::Notification(
                                "✅ Record '{$key}' set successfully!",
                                false,
                                [BulmaClass::IS_SUCCESS]
                            );
                            \Records::logInfo("Record set: {$key} = {$value}");
                        }
                        break;
                        
                    case 'append_record':
                        $key = Request::getData('append_key') ?? '';
                        $value = Request::getData('append_value') ?? '';
                        if (!empty($key) && !empty($value)) {
                            \Records::append($key, $value);
                            $alerts[] = Bulma::Notification(
                                "✅ Value appended to '{$key}' successfully!",
                                false,
                                [BulmaClass::IS_SUCCESS]
                            );
                            \Records::logInfo("Value appended to array: {$key}[] = {$value}");
                        }
                        break;
                        
                    case 'merge_record':
                        $key = Request::getData('merge_key') ?? '';
                        $values = Request::getData('merge_values') ?? '';
                        if (!empty($key) && !empty($values)) {
                            // Parse input: support both "key1:val1, key2:val2" and "val1, val2" formats
                            $inputArray = array_map('trim', explode(',', $values));
                            $valuesArray = [];
                            
                            foreach ($inputArray as $i => $item) {
                                if (strpos($item, ':') !== false) {
                                    // Associative format: "key:value"
                                    list($k, $v) = explode(':', $item, 2);
                                    $valuesArray[trim($k)] = trim($v);
                                } else {
                                    // Indexed format: "value" -> use index as key
                                    $valuesArray[$i] = $item;
                                }
                            }
                            
                            \Records::merge($key, $valuesArray);
                            $alerts[] = Bulma::Notification(
                                "✅ Array merged to '{$key}' successfully!",
                                false,
                                [BulmaClass::IS_SUCCESS]
                            );
                            \Records::logInfo("Array merged: {$key} += " . json_encode($valuesArray));
                        }
                        break;
                        
                    case 'increment':
                        $key = Request::getData('inc_key') ?? '';
                        $amount = (int)(Request::getData('inc_amount') ?? 1);
                        if (!empty($key)) {
                            \Records::increment($key, $amount);
                            $alerts[] = Bulma::Notification(
                                "✅ '{$key}' incremented by {$amount}!",
                                false,
                                [BulmaClass::IS_SUCCESS]
                            );
                            \Records::logInfo("Incremented: {$key} += {$amount}");
                        }
                        break;
                        
                    case 'remove_record':
                        $key = Request::getData('remove_key') ?? '';
                        if (!empty($key)) {
                            // Check if key exists before removing
                            if (\Records::exists($key)) {
                                \Records::remove($key);
                                $alerts[] = Bulma::Notification(
                                    "✅ Record '{$key}' removed successfully!",
                                    false,
                                    [BulmaClass::IS_SUCCESS]
                                );
                                \Records::logWarning("Record removed: {$key}");
                            } else {
                                $alerts[] = Bulma::Notification(
                                    "❌ Record '{$key}' does not exist!",
                                    false,
                                    [BulmaClass::IS_WARNING]
                                );
                                \Records::logInfo("Attempted to remove non-existent key: {$key}");
                            }
                        }
                        break;
                        
                    case 'remove_records':
                        \Records::clearAll();
                        $alerts[] = Bulma::Notification(
                            "✅ All records removed!",
                            false,
                            [BulmaClass::IS_SUCCESS]
                        );
                        \Records::logWarning("All records removed");
                        break;
                        
                    case 'test_log':
                        $message = Request::getData('log_message') ?? 'Test message';
                        $level = Request::getData('log_level') ?? 'info';
                        \Records::log($message, $level);
                        $alerts[] = Bulma::Notification(
                            "✅ Log message written with level '{$level}'!",
                            false,
                            [BulmaClass::IS_SUCCESS]
                        );
                        break;
                        
                    case 'remove_logs':
                        \Records::clearAllLogs();
                        $alerts[] = Bulma::Notification(
                            "✅ All log files removed!",
                            false,
                            [BulmaClass::IS_SUCCESS]
                        );
                        break;
                        
                    case 'check_array_element':
                        $key = Request::getData('array_key') ?? '';
                        $value = Request::getData('check_value') ?? '';
                        if (!empty($key) && !empty($value)) {
                            $exists = \Records::arrayElementExists($key, $value);
                            $alerts[] = Bulma::Notification(
                                "✅ arrayElementExists('{$key}', '{$value}') = " . ($exists ? 'true' : 'false'),
                                false,
                                [BulmaClass::IS_INFO]
                            );
                            \Records::logInfo("Array element check: {$key} contains '{$value}': " . ($exists ? 'true' : 'false'));
                        }
                        break;
                        
                    case 'get_record':
                        $key = Request::getData('get_key') ?? '';
                        if (!empty($key)) {
                            $value = \Records::get($key, 'NOT_FOUND');
                            $alerts[] = Bulma::Notification(
                                "✅ get('{$key}') = " . json_encode($value),
                                false,
                                [BulmaClass::IS_INFO]
                            );
                            \Records::logInfo("Retrieved value for key '{$key}': " . json_encode($value));
                        }
                        break;
                        
                    case 'check_exists':
                        $key = Request::getData('exists_key') ?? '';
                        if (!empty($key)) {
                            $exists = \Records::exists($key);
                            $alerts[] = Bulma::Notification(
                                "✅ exists('{$key}') = " . ($exists ? 'true' : 'false'),
                                false,
                                [BulmaClass::IS_INFO]
                            );
                            \Records::logInfo("Key existence check: '{$key}' = " . ($exists ? 'true' : 'false'));
                        }
                        break;
                        
                    case 'clear_records':
                        \Records::clear();
                        $alerts[] = Bulma::Notification(
                            "✅ All records cleared using clear() method!",
                            false,
                            [BulmaClass::IS_SUCCESS]
                        );
                        \Records::logWarning("All records cleared using clear() method");
                        break;
                        
                    case 'decrement':
                        $key = Request::getData('dec_key') ?? '';
                        $amount = (int)(Request::getData('dec_amount') ?? 1);
                        if (!empty($key)) {
                            \Records::decrement($key, $amount);
                            $alerts[] = Bulma::Notification(
                                "✅ '{$key}' decremented by {$amount}!",
                                false,
                                [BulmaClass::IS_SUCCESS]
                            );
                            \Records::logInfo("Decremented: {$key} -= {$amount}");
                        }
                        break;
                        
                    case 'remove_from_array':
                        $key = Request::getData('array_remove_key') ?? '';
                        $value = Request::getData('array_remove_value') ?? '';
                        if (!empty($key) && !empty($value)) {
                            // Check if key exists and is an array
                            if (!\Records::exists($key)) {
                                $alerts[] = Bulma::Notification(
                                    "❌ Array '{$key}' does not exist!",
                                    false,
                                    [BulmaClass::IS_WARNING]
                                );
                                \Records::logInfo("Attempted to remove from non-existent array: {$key}");
                            } else {
                                // Check if value exists in the array
                                if (\Records::arrayElementExists($key, $value)) {
                                    \Records::removeFromArray($key, $value);
                                    $alerts[] = Bulma::Notification(
                                        "✅ Value '{$value}' removed from array '{$key}'!",
                                        false,
                                        [BulmaClass::IS_SUCCESS]
                                    );
                                    \Records::logInfo("Removed from array: {$key} -= '{$value}'");
                                } else {
                                    $alerts[] = Bulma::Notification(
                                        "❌ Value '{$value}' not found in array '{$key}'!",
                                        false,
                                        [BulmaClass::IS_WARNING]
                                    );
                                    \Records::logInfo("Value '{$value}' not found in array '{$key}'");
                                }
                            }
                        }
                        break;
                        
                    case 'get_array_count':
                        $key = Request::getData('count_key') ?? '';
                        if (!empty($key)) {
                            $count = \Records::getArrayCount($key);
                            $alerts[] = Bulma::Notification(
                                "✅ getArrayCount('{$key}') = {$count}",
                                false,
                                [BulmaClass::IS_INFO]
                            );
                            \Records::logInfo("Array count for '{$key}': {$count}");
                        }
                        break;
                        
                    case 'merge_arrays':
                        $sourceKey1 = Request::getData('merge_source1') ?? '';
                        $sourceKey2 = Request::getData('merge_source2') ?? '';
                        $targetKey = Request::getData('merge_target') ?? '';
                        if (!empty($sourceKey1) && !empty($sourceKey2) && !empty($targetKey)) {
                            $result = \Records::mergeArrays($sourceKey1, $sourceKey2, $targetKey);
                            if ($result) {
                                $alerts[] = Bulma::Notification(
                                    "✅ Arrays '{$sourceKey1}' and '{$sourceKey2}' merged into '{$targetKey}'!",
                                    false,
                                    [BulmaClass::IS_SUCCESS]
                                );
                                \Records::logInfo("Arrays merged: {$sourceKey1} + {$sourceKey2} -> {$targetKey}");
                            } else {
                                $alerts[] = Bulma::Notification(
                                    "❌ One or both source arrays ('{$sourceKey1}', '{$sourceKey2}') do not exist!",
                                    false,
                                    [BulmaClass::IS_WARNING]
                                );
                                \Records::logWarning("Failed to merge arrays: missing source arrays");
                            }
                        }
                        break;
                        
                    case 'clear_cache':
                        \Records::clearCache();
                        $alerts[] = Bulma::Notification(
                            "✅ Internal cache cleared!",
                            false,
                            [BulmaClass::IS_SUCCESS]
                        );
                        \Records::logInfo("Internal cache cleared");
                        break;
                        
                    case 'toggle_cache':
                        $enabled = Request::getData('cache_enabled') === '1';
                        \Records::setCacheEnabled($enabled);
                        $alerts[] = Bulma::Notification(
                            "✅ Cache " . ($enabled ? 'enabled' : 'disabled') . "!",
                            false,
                            [BulmaClass::IS_SUCCESS]
                        );
                        \Records::logInfo("Cache " . ($enabled ? 'enabled' : 'disabled'));
                        break;
                        
                    case 'set_max_log_files':
                        $maxFiles = (int)(Request::getData('max_files') ?? 20);
                        \Records::setMaxLogFiles($maxFiles);
                        $alerts[] = Bulma::Notification(
                            "✅ Max log files set to {$maxFiles}!",
                            false,
                            [BulmaClass::IS_SUCCESS]
                        );
                        \Records::logInfo("Max log files set to: {$maxFiles}");
                        break;
                        
                    case 'get_log_content':
                        $fileName = Request::getData('log_file_name') ?? '';
                        if (!empty($fileName)) {
                            $content = \Records::getLogContent($fileName);
                            if ($content !== null) {
                                $preview = substr($content, 0, 200);
                                $alerts[] = Bulma::Notification(
                                    "✅ Log content preview: " . htmlspecialchars($preview) . "...",
                                    false,
                                    [BulmaClass::IS_INFO]
                                );
                            } else {
                                $alerts[] = Bulma::Notification(
                                    "❌ Log file '{$fileName}' not found!",
                                    false,
                                    [BulmaClass::IS_WARNING]
                                );
                            }
                            \Records::logInfo("Log content requested for: {$fileName}");
                        }
                        break;
                        
                    case 'print_and_log':
                        $message = Request::getData('print_message') ?? 'Test print and log';
                        $level = Request::getData('print_level') ?? 'info';
                        ob_start();
                        \Records::printAndLog($message, $level);
                        $output = ob_get_clean();
                        // Remove HTML tags from output for clean display
                        $cleanOutput = strip_tags($output);
                        $alerts[] = Bulma::Notification(
                            "✅ Message printed and logged! Output: " . htmlspecialchars($cleanOutput),
                            false,
                            [BulmaClass::IS_SUCCESS]
                        );
                        break;
                        
                    case 'test_log_convenience':
                        $message = Request::getData('convenience_message') ?? 'Test convenience method';
                        $method = Request::getData('convenience_method') ?? 'logInfo';
                        
                        switch ($method) {
                            case 'logInfo':
                                \Records::logInfo($message);
                                break;
                            case 'logWarning':
                                \Records::logWarning($message);
                                break;
                            case 'logError':
                                \Records::logError($message);
                                break;
                            case 'logDebug':
                                \Records::logDebug($message);
                                break;
                        }
                        
                        $alerts[] = Bulma::Notification(
                            "✅ {$method}() called with message!",
                            false,
                            [BulmaClass::IS_SUCCESS]
                        );
                        break;
                }
            } catch (\Exception $e) {
                $alerts[] = Bulma::Notification(
                    "❌ Error: " . htmlspecialchars($e->getMessage()),
                    false,
                    [BulmaClass::IS_DANGER]
                );
                \Records::logError("Form action error: " . $e->getMessage());
            }
        }
    }
    
    private function displayCurrentRecords(): string
    {
        $content = Bulma::Title('📋 Current Records', 3);
        
        try {
            $allRecords = \Records::getAll();
            $recordsFilePath = \Records::getFilePath();
            
            $content .= new View(
                '<div class="box">' .
                '<h5 class="title is-5 has-text-info">📁 Records File: ' . htmlspecialchars($recordsFilePath) . '</h5>'
            );
            
            if (empty($allRecords)) {
                $content .= new View(
                    '<div class="notification is-info">' .
                    '📝 No records found. Use the forms below to create some records!' .
                    '</div>'
                );
            } else {
                $content .= new View(
                    '<div class="table-container">' .
                    '<table class="table is-striped is-hoverable is-fullwidth">' .
                    '<thead class="has-background-info">' .
                    '<tr><th class="has-text-white">Key</th><th class="has-text-white">Value</th><th class="has-text-white">Type</th></tr>' .
                    '</thead>' .
                    '<tbody>'
                );
                
                foreach ($allRecords as $key => $value) {
                    $valueDisplay = is_array($value) ? json_encode($value) : (string)$value;
                    $type = is_array($value) ? 'Array (' . count($value) . ' items)' : gettype($value);
                    
                    $content .= new View(
                        '<tr>' .
                        '<td><strong>' . htmlspecialchars($key) . '</strong></td>' .
                        '<td><code>' . htmlspecialchars($valueDisplay) . '</code></td>' .
                        '<td><span class="tag is-dark">' . $type . '</span></td>' .
                        '</tr>'
                    );
                }
                
                $content .= new View('</tbody></table></div>');
            }
            
            $content .= new View('</div>');
            
        } catch (\Exception $e) {
            $content .= Bulma::Notification(
                '❌ Error loading records: ' . htmlspecialchars($e->getMessage()),
                false,
                [BulmaClass::IS_DANGER]
            );
        }
        
        return $content;
    }
    
    private function displayTestForms(): string
    {
        $content = Bulma::Title('🧪 Test Forms', 3);
        
        $content .= new View(
            '<div class="columns is-multiline">' .
            
            // Set Record Form
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-primary">Set Record</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="set_record">' .
            '<div class="field">' .
            '<label class="label">Key</label>' .
            '<div class="control"><input class="input" type="text" name="key" placeholder="Enter key" required></div>' .
            '</div>' .
            '<div class="field">' .
            '<label class="label">Value</label>' .
            '<div class="control"><input class="input" type="text" name="value" placeholder="Enter value" required></div>' .
            '</div>' .
            '<div class="control"><button class="button is-primary" type="submit">Set Record</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            // Append to Array Form
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-success">Append to Array</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="append_record">' .
            '<div class="field">' .
            '<label class="label">Array Key</label>' .
            '<div class="control"><input class="input" type="text" name="append_key" placeholder="Enter array key" required></div>' .
            '</div>' .
            '<div class="field">' .
            '<label class="label">Value</label>' .
            '<div class="control"><input class="input" type="text" name="append_value" placeholder="Enter value to append" required></div>' .
            '</div>' .
            '<div class="control"><button class="button is-success" type="submit">Append Value</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            // Merge Array Form
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-warning">Merge Array</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="merge_record">' .
            '<div class="field">' .
            '<label class="label">Array Key</label>' .
            '<div class="control"><input class="input" type="text" name="merge_key" placeholder="Enter array key" required></div>' .
            '</div>' .
            '<div class="field">' .
            '<label class="label">Values (comma-separated)</label>' .
            '<div class="control"><input class="input" type="text" name="merge_values" placeholder="value1, value2 OR key1:val1, key2:val2" required></div>' .
            '</div>' .
            '<div class="control"><button class="button is-warning" type="submit">Merge Array</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            // Increment Form
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-info">Increment Number</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="increment">' .
            '<div class="field">' .
            '<label class="label">Key</label>' .
            '<div class="control"><input class="input" type="text" name="inc_key" placeholder="Enter key" required></div>' .
            '</div>' .
            '<div class="field">' .
            '<label class="label">Amount</label>' .
            '<div class="control"><input class="input" type="number" name="inc_amount" value="1" required></div>' .
            '</div>' .
            '<div class="control"><button class="button is-info" type="submit">Increment</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            // Decrement Form
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-info">Decrement Number</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="decrement">' .
            '<div class="field">' .
            '<label class="label">Key</label>' .
            '<div class="control"><input class="input" type="text" name="dec_key" placeholder="Enter key" required></div>' .
            '</div>' .
            '<div class="field">' .
            '<label class="label">Amount</label>' .
            '<div class="control"><input class="input" type="number" name="dec_amount" value="1" required></div>' .
            '</div>' .
            '<div class="control"><button class="button is-info" type="submit">Decrement</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            // Check Array Element Form
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-dark">Check Array Element</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="check_array_element">' .
            '<div class="field">' .
            '<label class="label">Array Key</label>' .
            '<div class="control"><input class="input" type="text" name="array_key" placeholder="Enter array key" required></div>' .
            '</div>' .
            '<div class="field">' .
            '<label class="label">Value to Check</label>' .
            '<div class="control"><input class="input" type="text" name="check_value" placeholder="Enter value to check" required></div>' .
            '</div>' .
            '<div class="control"><button class="button is-dark" type="submit">Check Element</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            // Remove From Array Form
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-danger">Remove From Array</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="remove_from_array">' .
            '<div class="field">' .
            '<label class="label">Array Key</label>' .
            '<div class="control"><input class="input" type="text" name="array_remove_key" placeholder="Enter array key" required></div>' .
            '</div>' .
            '<div class="field">' .
            '<label class="label">Value to Remove</label>' .
            '<div class="control"><input class="input" type="text" name="array_remove_value" placeholder="Enter value to remove" required></div>' .
            '</div>' .
            '<div class="control"><button class="button is-danger" type="submit">Remove From Array</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            // Get Record Form
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-grey">Get Record</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="get_record">' .
            '<div class="field">' .
            '<label class="label">Key</label>' .
            '<div class="control"><input class="input" type="text" name="get_key" placeholder="Enter key to get" required></div>' .
            '</div>' .
            '<div class="control"><button class="button is-grey" type="submit">Get Value</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            // Check Exists Form
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-black">Check Key Exists</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="check_exists">' .
            '<div class="field">' .
            '<label class="label">Key</label>' .
            '<div class="control"><input class="input" type="text" name="exists_key" placeholder="Enter key to check" required></div>' .
            '</div>' .
            '<div class="control"><button class="button is-black" type="submit">Check Exists</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            // Get Array Count Form
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-primary">Get Array Count</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="get_array_count">' .
            '<div class="field">' .
            '<label class="label">Array Key</label>' .
            '<div class="control"><input class="input" type="text" name="count_key" placeholder="Enter array key" required></div>' .
            '</div>' .
            '<div class="control"><button class="button is-primary" type="submit">Get Count</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            // Merge Arrays Form
            '<div class="column is-6 is-offset-3">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-success">Merge Two Arrays</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="merge_arrays">' .
            '<div class="field">' .
            '<label class="label">Source Array 1</label>' .
            '<div class="control"><input class="input" type="text" name="merge_source1" placeholder="First array key" required></div>' .
            '</div>' .
            '<div class="field">' .
            '<label class="label">Source Array 2</label>' .
            '<div class="control"><input class="input" type="text" name="merge_source2" placeholder="Second array key" required></div>' .
            '</div>' .
            '<div class="field">' .
            '<label class="label">Target Key</label>' .
            '<div class="control"><input class="input" type="text" name="merge_target" placeholder="New merged array key" required></div>' .
            '</div>' .
            '<div class="control"><button class="button is-success" type="submit">Merge Arrays</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            '</div>'
        );
        
        // Logging and Management Forms
        $content .= new View(
            '<div class="columns is-multiline">' .
            
            // Log Message Form
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-link">Add Log Message</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="test_log">' .
            '<div class="field">' .
            '<label class="label">Message</label>' .
            '<div class="control"><textarea class="textarea" name="log_message" placeholder="Enter log message" required></textarea></div>' .
            '</div>' .
            '<div class="field">' .
            '<label class="label">Level</label>' .
            '<div class="control">' .
            '<div class="select is-fullwidth">' .
            '<select name="log_level">' .
            '<option value="info">Info</option>' .
            '<option value="warning">Warning</option>' .
            '<option value="error">Error</option>' .
            '<option value="debug">Debug</option>' .
            '</select>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '<div class="control"><button class="button is-link" type="submit">Add Log</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            // Print and Log Form
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-info">Print and Log</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="print_and_log">' .
            '<div class="field">' .
            '<label class="label">Message</label>' .
            '<div class="control"><textarea class="textarea" name="print_message" placeholder="Enter message to print and log" required></textarea></div>' .
            '</div>' .
            '<div class="field">' .
            '<label class="label">Level</label>' .
            '<div class="control">' .
            '<div class="select is-fullwidth">' .
            '<select name="print_level">' .
            '<option value="info">Info</option>' .
            '<option value="warning">Warning</option>' .
            '<option value="error">Error</option>' .
            '<option value="debug">Debug</option>' .
            '</select>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '<div class="control"><button class="button is-info" type="submit">Print & Log</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            '</div>'
        );
        
        // Advanced Testing Forms
        $content .= Bulma::Title('🔧 Advanced & Utility Functions', 3);
        $content .= new View(
            '<div class="columns is-multiline">' .
            
            // Convenience Log Methods Form
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-success">Log Convenience Methods</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="test_log_convenience">' .
            '<div class="field">' .
            '<label class="label">Message</label>' .
            '<div class="control"><textarea class="textarea" name="convenience_message" placeholder="Enter message" required></textarea></div>' .
            '</div>' .
            '<div class="field">' .
            '<label class="label">Method</label>' .
            '<div class="control">' .
            '<div class="select is-fullwidth">' .
            '<select name="convenience_method">' .
            '<option value="logInfo">logInfo()</option>' .
            '<option value="logWarning">logWarning()</option>' .
            '<option value="logError">logError()</option>' .
            '<option value="logDebug">logDebug()</option>' .
            '</select>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '<div class="control"><button class="button is-success" type="submit">Test Method</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            // Get Log Content Form
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-warning">Get Log Content</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="get_log_content">' .
            '<div class="field">' .
            '<label class="label">Log File Name</label>' .
            '<div class="control"><input class="input" type="text" name="log_file_name" placeholder="e.g. log_2025_08_31_14_30.log" required></div>' .
            '</div>' .
            '<div class="control"><button class="button is-warning" type="submit">Get Content</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            // Cache Management Forms
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-grey">Cache Management</h6>' .
            '<form method="post" style="margin-bottom: 1rem;">' .
            '<input type="hidden" name="action" value="clear_cache">' .
            '<button class="button is-grey is-fullwidth" type="submit">Clear Cache</button>' .
            '</form>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="toggle_cache">' .
            '<div class="field">' .
            '<label class="label">Cache Setting</label>' .
            '<div class="control">' .
            '<div class="select is-fullwidth">' .
            '<select name="cache_enabled">' .
            '<option value="1">Enable Cache</option>' .
            '<option value="0">Disable Cache</option>' .
            '</select>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '<div class="control"><button class="button is-grey" type="submit">Set Cache</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            // Max Log Files Form
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-dark">Set Max Log Files</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="set_max_log_files">' .
            '<div class="field">' .
            '<label class="label">Max Files</label>' .
            '<div class="control"><input class="input" type="number" name="max_files" value="20" min="1" max="100" required></div>' .
            '</div>' .
            '<div class="control"><button class="button is-dark" type="submit">Set Max Files</button></div>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            '</div>'
        );
        
        // Management Actions Section
        $content .= Bulma::Title('⚠️ Management Actions', 3);
        $content .= new View(
            '<div class="columns is-multiline">' .
            
            // Record Management
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-danger">Record Management</h6>' .
            '<form method="post" style="margin-bottom: 1rem;">' .
            '<input type="hidden" name="action" value="remove_record">' .
            '<div class="field has-addons">' .
            '<div class="control is-expanded">' .
            '<input class="input" type="text" name="remove_key" placeholder="Key to remove" required>' .
            '</div>' .
            '<div class="control">' .
            '<button class="button is-danger" type="submit">Remove Key</button>' .
            '</div>' .
            '</div>' .
            '</form>' .
            
            '<form method="post" style="margin-bottom: 1rem;">' .
            '<input type="hidden" name="action" value="remove_records">' .
            '<button class="button is-danger is-fullwidth" type="submit" onclick="return confirm(\'Are you sure you want to remove all records?\')">Remove All Records</button>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            // Log Management
            '<div class="column is-half">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-warning">Log Management</h6>' .
            '<form method="post">' .
            '<input type="hidden" name="action" value="remove_logs">' .
            '<button class="button is-warning is-fullwidth" type="submit" onclick="return confirm(\'Are you sure you want to remove all logs?\')">Remove All Logs</button>' .
            '</form>' .
            '</div>' .
            '</div>' .
            
            '</div>'
        );
        
        return $content;
    }
    
    private function displayLogFiles(): string
    {
        $content = Bulma::Title('📝 Log Files', 3);
        
        try {
            $logFiles = \Records::getLogFiles();
            $logsDir = \Records::getLogsDir();
            
            $content .= new View(
                '<div class="box">' .
                '<h5 class="title is-5 has-text-info">📁 Logs Directory: ' . htmlspecialchars($logsDir) . '</h5>' .
                '<p class="subtitle is-6">Max log files: ' . \Records::getMaxLogFiles() . '</p>'
            );
            
            if (empty($logFiles)) {
                $content .= new View(
                    '<div class="notification is-info">' .
                    '📝 No log files found. Create some logs using the form above!' .
                    '</div>'
                );
            } else {
                $content .= new View(
                    '<div class="table-container">' .
                    '<table class="table is-striped is-hoverable is-fullwidth">' .
                    '<thead class="has-background-link">' .
                    '<tr><th class="has-text-white">File Name</th><th class="has-text-white">Size</th><th class="has-text-white">Modified</th></tr>' .
                    '</thead>' .
                    '<tbody>'
                );
                
                foreach ($logFiles as $file) {
                    $filePath = $logsDir . DIRECTORY_SEPARATOR . $file;
                    $size = file_exists($filePath) ? filesize($filePath) : 0;
                    $modified = file_exists($filePath) ? date('Y-m-d H:i:s', filemtime($filePath)) : 'Unknown';
                    
                    $content .= new View(
                        '<tr>' .
                        '<td><strong>' . htmlspecialchars($file) . '</strong></td>' .
                        '<td>' . $this->formatBytes($size) . '</td>' .
                        '<td>' . $modified . '</td>' .
                        '</tr>'
                    );
                }
                
                $content .= new View('</tbody></table></div>');
            }
            
            $content .= new View('</div>');
            
        } catch (\Exception $e) {
            $content .= Bulma::Notification(
                '❌ Error loading log files: ' . htmlspecialchars($e->getMessage()),
                false,
                [BulmaClass::IS_DANGER]
            );
        }
        
        return $content;
    }
    
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}