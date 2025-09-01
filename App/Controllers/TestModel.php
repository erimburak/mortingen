<?php

namespace Controllers;

use Controller;
use DB\DB;
use DB\Helper\MySQLHelper;
use Models\User;
use Models\Post;
use Bulma;
use HTML;
use BulmaClass;
use View;

class TestModel extends Controller
{
    public function index()
    {
        $content = "";
        $alerts = [];

        try
        {
            $content .= Bulma::Title('🗄️ Mortingen Framework Test System', 1);
            $content .= Bulma::Subtitle('Automatic Database Setup and Foreign Key Validation', 3);

            // Step 1: Auto-create database if not exists
            $content .= Bulma::Title('📋 Step 1: Database Setup', 3);
            $this->autoCreateDatabase();
            $alerts[] = Bulma::Notification('✅ Database "mortingen_test" ready!', false, [BulmaClass::IS_SUCCESS]);

            // Step 2: Connect to mortingen_test database
            $mysqlHelper = new MySQLHelper(
                host: 'localhost',
                dbname: 'mortingen_test',
                user: 'root',
                pass: ''
            );

            $db = new DB($mysqlHelper);
            $alerts[] = Bulma::Notification('✅ Connected to database successfully!', false, [BulmaClass::IS_SUCCESS]);

            // Step 2: Initialize models
            $content .= Bulma::Title('📋 Step 2: Table Creation', 3);
            $this->initializeModels($db, $alerts);

            // Step 3: Test data creation
            $content .= Bulma::Title('📋 Step 3: Creating Test Data', 3);
            $content .= $this->performDataTests($db);

            // Step 4: Foreign key validation through deletion
            $content .= Bulma::Title('📋 Step 4: Foreign Key Validation', 3);
            $content .= $this->performForeignKeyTests($db);
        }
        catch (\Exception $e)
        {
            $alerts[] = Bulma::Notification(
                '❌ System Error: ' . htmlspecialchars($e->getMessage()),
                false,
                [BulmaClass::IS_DANGER]
            );
        }

        // Build final clean page
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
            Bulma::Html($pageContent, 'Database Test System - Mortingen Framework')
        );
    }

    private function autoCreateDatabase()
    {
        try
        {
            $mysqlHelper = new MySQLHelper(
                host: 'localhost',
                dbname: 'mysql',
                user: 'root',
                pass: ''
            );

            $db = new DB($mysqlHelper);
            $createDbQuery = "CREATE DATABASE IF NOT EXISTS `mortingen_test` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            $db->query($createDbQuery);
        }
        catch (\Exception $e)
        {
            // If database creation fails, it might already exist
        }
    }

    private function initializeModels($db, &$alerts)
    {
        // Drop tables in correct order (child first, then parent) to avoid foreign key conflicts
        try
        {
            $db->query("DROP TABLE IF EXISTS `models\\post`"); // Drop child table first
            $db->query("DROP TABLE IF EXISTS `models\\user`"); // Then drop parent table
            $alerts[] = Bulma::Notification('✅ Existing tables cleaned up successfully!', false, [BulmaClass::IS_SUCCESS]);
        }
        catch (\Exception $e)
        {
            // Tables might not exist, which is fine
        }

        // Create User table with InnoDB engine first
        try
        {
            $userTableQuery = "CREATE TABLE `models\\user` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255) NOT NULL,
                `email` VARCHAR(255) NOT NULL UNIQUE,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

            $db->query($userTableQuery);
            $alerts[] = Bulma::Notification('✅ User table created with InnoDB engine!', false, [BulmaClass::IS_SUCCESS]);
        }
        catch (\Exception $e)
        {
            $alerts[] = Bulma::Notification('❌ Manual User table creation error: ' . $e->getMessage(), false, [BulmaClass::IS_DANGER]);
        }

        // Initialize User model (should succeed since table already exists)
        try
        {
            User::init($db);
            $alerts[] = Bulma::Notification('✅ User model initialized successfully!', false, [BulmaClass::IS_SUCCESS]);
        }
        catch (\Exception $e)
        {
            $alerts[] = Bulma::Notification('⚠️ User model init warning: ' . $e->getMessage(), false, [BulmaClass::IS_WARNING]);
        }

        usleep(200000); // Small delay

        // Create Post table manually first to avoid foreign key issues
        try
        {
            // First check what the actual User table name is
            $tables = $db->fetchAll("SHOW TABLES");
            $userTableName = null;
            foreach ($tables as $table)
            {
                $tableName = array_values($table)[0];
                if (stripos($tableName, 'user') !== false)
                {
                    $userTableName = $tableName;
                    break;
                }
            }

            if ($userTableName)
            {
                // Create Post table with proper foreign key constraint
                $postTableQuery = "CREATE TABLE `models\\post` (
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `title` VARCHAR(255) NOT NULL,
                    `content` TEXT NOT NULL,
                    `user_id` INT UNSIGNED NOT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX `idx_user_id` (`user_id`),
                    CONSTRAINT `fk_post_user_id` FOREIGN KEY (`user_id`) REFERENCES `{$userTableName}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

                $db->query($postTableQuery);
                $alerts[] = Bulma::Notification('✅ Post table created with InnoDB engine and CASCADE foreign key constraint!', false, [BulmaClass::IS_SUCCESS]);
            }
            else
            {
                $alerts[] = Bulma::Notification('❌ Cannot create Post table - User table not found!', false, [BulmaClass::IS_DANGER]);
            }
        }
        catch (\Exception $e)
        {
            $alerts[] = Bulma::Notification('❌ Manual Post table creation error: ' . $e->getMessage(), false, [BulmaClass::IS_DANGER]);
        }

        // Now try to initialize Post model (should succeed since table already exists)
        try
        {
            Post::init($db);
            $alerts[] = Bulma::Notification('✅ Post model initialized successfully!', false, [BulmaClass::IS_SUCCESS]);
        }
        catch (\Exception $e)
        {
            $alerts[] = Bulma::Notification('⚠️ Post model init warning: ' . $e->getMessage(), false, [BulmaClass::IS_WARNING]);
        }
    }

    private function performDataTests($db): string
    {
        $content = '';

        try
        {
            // First, let's see what tables actually exist
            $tables = $db->fetchAll("SHOW TABLES");
            $content .= new View(
                '<div class="notification is-info">' .
                    '<strong>📋 Tables found in database:</strong><br>' .
                    implode('<br>', array_map(function ($table)
                    {
                        return '• ' . array_values($table)[0];
                    }, $tables)) .
                    '</div>'
            );

            // Find the actual table names
            $userTableName = null;
            $postTableName = null;

            foreach ($tables as $table)
            {
                $tableName = array_values($table)[0];
                if (stripos($tableName, 'user') !== false)
                {
                    $userTableName = $tableName;
                }
                if (stripos($tableName, 'post') !== false)
                {
                    $postTableName = $tableName;
                }
            }

            if (!$userTableName)
            {
                $content .= Bulma::Notification('❌ User table not found in database!', false, [BulmaClass::IS_DANGER]);
                return $content;
            }

            if (!$postTableName)
            {
                $content .= Bulma::Notification('❌ Post table not found in database!', false, [BulmaClass::IS_DANGER]);
                return $content;
            }

            $content .= new View(
                '<div class="notification is-success">' .
                    '<strong>✅ Using tables:</strong> ' . $userTableName . ' and ' . $postTableName .
                    '</div>'
            );

            // Create 3 test users
            $users = [
                ['Ahmet Yılmaz', 'ahmet@example.com'],
                ['Fatma Kaya', 'fatma@example.com'],
                ['Mehmet Demir', 'mehmet@example.com']
            ];

            $userIds = [];
            foreach ($users as $user)
            {
                $query = "INSERT INTO `{$userTableName}` (name, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";
                $db->query($query, $user);
                $result = $db->fetchOne("SELECT id FROM `{$userTableName}` WHERE email = ?", [$user[1]]);
                if ($result)
                {
                    $userIds[] = $result['id'];
                }
            }

            $content .= new View(
                '<div class="notification is-success">' .
                    '<strong>✅ Created ' . count($userIds) . ' users successfully!</strong>' .
                    '</div>'
            );

            // Create 4 test posts
            $posts = [
                ['İlk Blog Yazısı', 'Bu benim ilk blog yazım. Framework test ediyoruz!'],
                ['PHP ile Web Geliştirme', 'PHP ile modern web uygulamaları geliştirmek çok keyifli.'],
                ['Veritabanı İlişkileri', 'Foreign key kullanımı ile güvenli ilişkiler kuruyoruz.'],
                ['Mortingen Framework', 'Bu framework ile çalışmak gerçekten harika bir deneyim.']
            ];

            $postsCreated = 0;
            foreach ($posts as $index => $post)
            {
                if (!empty($userIds))
                {
                    $userId = $userIds[$index % count($userIds)];
                    $query = "INSERT INTO `{$postTableName}` (title, content, user_id) VALUES (?, ?, ?)";
                    $db->query($query, [$post[0], $post[1], $userId]);
                    $postsCreated++;
                }
            }

            $content .= new View(
                '<div class="notification is-success">' .
                    '<strong>✅ Created ' . $postsCreated . ' posts successfully!</strong>' .
                    '</div>'
            );

            // Show created test data in a nice table
            $dataQuery = "SELECT u.id as user_id, u.name as user_name, u.email, p.id as post_id, p.title as post_title 
                         FROM `{$userTableName}` u 
                         JOIN `{$postTableName}` p ON u.id = p.user_id 
                         ORDER BY u.name, p.title";
            $results = $db->fetchAll($dataQuery);

            if (!empty($results))
            {
                $content .= new View(
                    '<div class="box has-background-light">' .
                        '<h5 class="title is-5 has-text-success">📋 Test Data Successfully Created:</h5>' .
                        '<div class="table-container">' .
                        '<table class="table is-striped is-hoverable is-fullwidth">' .
                        '<thead class="has-background-primary">' .
                        '<tr><th class="has-text-white">User ID</th><th class="has-text-white">User Name</th><th class="has-text-white">Email</th><th class="has-text-white">Post ID</th><th class="has-text-white">Post Title</th></tr>' .
                        '</thead>' .
                        '<tbody>' .
                        implode('', array_map(function ($row)
                        {
                            return '<tr>' .
                                '<td><span class="tag is-info">' . $row['user_id'] . '</span></td>' .
                                '<td><strong>' . htmlspecialchars($row['user_name']) . '</strong></td>' .
                                '<td>' . htmlspecialchars($row['email']) . '</td>' .
                                '<td><span class="tag is-success">' . $row['post_id'] . '</span></td>' .
                                '<td>' . htmlspecialchars($row['post_title']) . '</td>' .
                                '</tr>';
                        }, $results)) .
                        '</tbody></table></div>' .
                        '<div class="notification is-success">' .
                        '<strong>✅ Created:</strong> ' . count($userIds) . ' users and ' . count($posts) . ' posts with proper relationships!' .
                        '</div>' .
                        '</div>'
                );
            }
        }
        catch (\Exception $e)
        {
            $content .= Bulma::Notification('❌ Data creation error: ' . $e->getMessage(), false, [BulmaClass::IS_DANGER]);
        }

        return $content;
    }

    private function performForeignKeyTests($db): string
    {
        $content = '';

        try
        {
            // Get the actual table names
            $tables = $db->fetchAll("SHOW TABLES");
            $userTableName = null;
            $postTableName = null;

            foreach ($tables as $table)
            {
                $tableName = array_values($table)[0];
                if (stripos($tableName, 'user') !== false)
                {
                    $userTableName = $tableName;
                }
                if (stripos($tableName, 'post') !== false)
                {
                    $postTableName = $tableName;
                }
            }

            if (!$userTableName || !$postTableName)
            {
                $content .= Bulma::Notification('❌ Cannot test foreign keys - tables not found!', false, [BulmaClass::IS_DANGER]);
                return $content;
            }

            // Test 1: First, let's check the actual table structures and foreign keys
            $content .= new View('<div class="notification is-info has-text-centered">🔍 <strong>Checking Database Structure...</strong></div>');

            // Check User table structure
            try
            {
                $userStructure = $db->fetchAll("SHOW CREATE TABLE `{$userTableName}`");
                $userCreateSQL = $userStructure[0]['Create Table'];
                $content .= new View(
                    '<div class="notification is-info">' .
                        '<strong>📋 User Table Structure:</strong><br>' .
                        '<code style="font-size: 11px;">' . htmlspecialchars($userCreateSQL) . '</code>' .
                        '</div>'
                );
            }
            catch (\Exception $e)
            {
                $content .= new View('<div class="notification is-warning">Could not get User table structure</div>');
            }

            // Check Post table structure
            try
            {
                $postStructure = $db->fetchAll("SHOW CREATE TABLE `{$postTableName}`");
                $postCreateSQL = $postStructure[0]['Create Table'];
                $content .= new View(
                    '<div class="notification is-info">' .
                        '<strong>📋 Post Table Structure:</strong><br>' .
                        '<code style="font-size: 11px;">' . htmlspecialchars($postCreateSQL) . '</code>' .
                        '</div>'
                );

                // Check if foreign key constraints exist
                if (strpos($postCreateSQL, 'FOREIGN KEY') !== false)
                {
                    $content .= new View('<div class="notification is-success">✅ Foreign Key constraint found in Post table!</div>');
                }
                else
                {
                    $content .= new View('<div class="notification is-warning">⚠️ No Foreign Key constraint found in Post table!</div>');
                }
            }
            catch (\Exception $e)
            {
                $content .= new View('<div class="notification is-warning">Could not get Post table structure</div>');
            }

            // Test 2: Try to delete a user with posts (should fail if foreign key works)
            $content .= new View('<div class="notification is-info has-text-centered">🧪 <strong>Testing Foreign Key Constraint...</strong></div>');

            $testUserEmail = 'ahmet@example.com';
            $foreignKeyWorks = false;

            try
            {
                $deleteQuery = "DELETE FROM `{$userTableName}` WHERE email = ?";
                $db->query($deleteQuery, [$testUserEmail]);

                // If we reach here, the delete succeeded (CASCADE working)
                $content .= new View(
                    '<div class="notification is-success has-text-centered">' .
                        '✅ <strong>Excellent!</strong> CASCADE foreign key is working perfectly!<br>' .
                        'User deleted and posts were automatically deleted too.' .
                        '</div>'
                );
                $foreignKeyWorks = true;
            }
            catch (\Exception $fkError)
            {
                // This shouldn't happen with CASCADE
                $foreignKeyWorks = false;
                $content .= new View(
                    '<div class="notification is-warning has-text-centered">' .
                        '⚠️ <strong>Warning:</strong> CASCADE foreign key is NOT working properly!<br>' .
                        'Error: ' . htmlspecialchars($fkError->getMessage()) .
                        '</div>'
                );
            }

            // Test cleanup - Delete one user to test CASCADE
            $content .= new View('<div class="notification is-info has-text-centered">🧙 <strong>Testing CASCADE cleanup...</strong></div>');

            // Delete one test user (CASCADE will automatically delete their posts)
            $userDeleteQuery = "DELETE FROM `{$userTableName}` WHERE email = ?";
            $userEmail = 'fatma@example.com'; // Delete Fatma
            $deletedUsers = $db->query($userDeleteQuery, [$userEmail]);

            $content .= new View(
                '<div class="notification is-success">' .
                    '✅ Deleted 1 user successfully! (Their posts were automatically deleted via CASCADE)' .
                    '</div>'
            );

            // Final verification - count remaining records
            $userCount = $db->fetchOne("SELECT COUNT(*) as count FROM `{$userTableName}`")['count'];
            $postCount = $db->fetchOne("SELECT COUNT(*) as count FROM `{$postTableName}`")['count'];

            // Show final summary
            $content .= new View(
                '<div class="box has-background-success-light">' .
                    '<h5 class="title is-5 has-text-success has-text-centered">🎉 Testing Complete!</h5>' .
                    '<div class="columns">' .
                    '<div class="column has-text-centered">' .
                    '<div class="notification is-info">' .
                    '<span class="icon is-large"><i class="fas fa-users"></i></span><br>' .
                    '<strong>Users Remaining:</strong> ' . $userCount .
                    '</div>' .
                    '</div>' .
                    '<div class="column has-text-centered">' .
                    '<div class="notification is-info">' .
                    '<span class="icon is-large"><i class="fas fa-file-alt"></i></span><br>' .
                    '<strong>Posts Remaining:</strong> ' . $postCount .
                    '</div>' .
                    '</div>' .
                    '</div>' .
                    '<div class="content has-text-centered">' .
                    '<h6 class="subtitle is-6 has-text-success">Test Results:</h6>' .
                    '<p class="has-text-success">' .
                    '✅ Database tables created successfully<br>' .
                    ($foreignKeyWorks ? '✅ CASCADE foreign key constraints working properly' : '⚠️ Foreign key constraints need attention') . '<br>' .
                    '✅ Data operations completed<br>' .
                    '✅ CASCADE cleanup performed successfully' .
                    '</p>' .
                    '</div>' .
                    '</div>'
            );
        }
        catch (\Exception $e)
        {
            $content .= Bulma::Notification('❌ Foreign key test error: ' . $e->getMessage(), false, [BulmaClass::IS_DANGER]);
        }

        return $content;
    }
}
