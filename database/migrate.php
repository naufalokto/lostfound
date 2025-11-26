<?php

require_once dirname(__DIR__) . '/config/config.php';

class MigrationRunner {
    private $db;
    private $migrationsPath;
    
    public function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
            $this->db = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            
            $this->db->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->db->exec("USE `" . DB_NAME . "`");
            
            $this->createMigrationsTable();
            
            $this->migrationsPath = dirname(__FILE__) . '/migrations';
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage() . "\n");
        }
    }
    
    private function createMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT PRIMARY KEY AUTO_INCREMENT,
            filename VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);
    }
    
    private function getExecutedMigrations() {
        $stmt = $this->db->query("SELECT filename FROM migrations ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    private function markAsExecuted($filename) {
        $stmt = $this->db->prepare("INSERT INTO migrations (filename) VALUES (?)");
        $stmt->execute([$filename]);
    }
    
    public function run() {
        $executed = $this->getExecutedMigrations();
        $files = glob($this->migrationsPath . '/*.sql');
        sort($files);
        
        $results = [];
        $successCount = 0;
        $skipCount = 0;
        
        foreach ($files as $file) {
            $filename = basename($file);
            
            if (in_array($filename, $executed)) {
                $results[] = [
                    'file' => $filename,
                    'status' => 'skipped',
                    'message' => 'Already executed'
                ];
                $skipCount++;
                continue;
            }
            
            try {
                $sql = file_get_contents($file);
                
                $sql = preg_replace('/--.*$/m', '', $sql);
                $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
                
                $statements = array_filter(array_map('trim', explode(';', $sql)));
                
                foreach ($statements as $statement) {
                    if (!empty($statement)) {
                        try {
                            $this->db->exec($statement);
                        } catch (PDOException $e) {
                            if (strpos($e->getMessage(), 'Duplicate key') === false && 
                                strpos($e->getMessage(), '1061') === false &&
                                strpos($e->getMessage(), '1062') === false) {
                                throw $e;
                            }
                        }
                    }
                }
                
                $this->markAsExecuted($filename);
                
                $results[] = [
                    'file' => $filename,
                    'status' => 'success',
                    'message' => 'Executed successfully'
                ];
                $successCount++;
                
            } catch (PDOException $e) {
                $results[] = [
                    'file' => $filename,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return [
            'results' => $results,
            'success' => $successCount,
            'skipped' => $skipCount,
            'total' => count($files)
        ];
    }
}

$runner = new MigrationRunner();
$output = $runner->run();

if (php_sapi_name() === 'cli') {
    echo "\n=== Database Migration Results ===\n\n";
    foreach ($output['results'] as $result) {
        $status = strtoupper($result['status']);
        $icon = $result['status'] === 'success' ? '✓' : ($result['status'] === 'error' ? '✗' : '○');
        echo "[{$icon}] {$result['file']} - {$status}\n";
        if ($result['status'] === 'error') {
            echo "   Error: {$result['message']}\n";
        }
    }
    echo "\nSummary: {$output['success']} executed, {$output['skipped']} skipped, {$output['total']} total\n\n";
} else {
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Migration</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 800px;
                margin: 50px auto;
                padding: 20px;
                background: #f5f5f5;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            h1 {
                color: #333;
                margin-bottom: 30px;
            }
            .result {
                padding: 10px;
                margin: 5px 0;
                border-radius: 4px;
                border-left: 4px solid;
            }
            .success {
                background: #d4edda;
                border-color: #28a745;
                color: #155724;
            }
            .error {
                background: #f8d7da;
                border-color: #dc3545;
                color: #721c24;
            }
            .skipped {
                background: #e2e3e5;
                border-color: #6c757d;
                color: #383d41;
            }
            .summary {
                margin-top: 30px;
                padding: 20px;
                background: #f8f9fa;
                border-radius: 4px;
            }
            .icon {
                font-weight: bold;
                margin-right: 10px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Database Migration Results</h1>
            
            <?php foreach ($output['results'] as $result): ?>
                <div class="result <?= $result['status'] ?>">
                    <span class="icon">
                        <?= $result['status'] === 'success' ? '✓' : ($result['status'] === 'error' ? '✗' : '○') ?>
                    </span>
                    <strong><?= htmlspecialchars($result['file']) ?></strong>
                    <span style="float: right;"><?= strtoupper($result['status']) ?></span>
                    <?php if ($result['status'] === 'error'): ?>
                        <div style="margin-top: 5px; font-size: 0.9em;">
                            Error: <?= htmlspecialchars($result['message']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <div class="summary">
                <h3>Summary</h3>
                <p><strong>Total Files:</strong> <?= $output['total'] ?></p>
                <p><strong>Executed:</strong> <span style="color: #28a745;"><?= $output['success'] ?></span></p>
                <p><strong>Skipped:</strong> <span style="color: #6c757d;"><?= $output['skipped'] ?></span></p>
            </div>
        </div>
    </body>
    </html>
    <?php
}
