<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
$logFile = __DIR__ . '/access.log'; 
$lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$entries = [];
foreach ($lines as $line) {
    if (preg_match('/^(\S+) \S+ \S+ \[(.*?)\] "(.*?)" (\d{3}) (\d+|-) "(.*?)" "(.*?)"/', $line, $matches)) {
        $entries[] = [
            'ip' => $matches[1],
            'date' => $matches[2],
            'request' => $matches[3],
            'status' => $matches[4],
            'size' => $matches[5],
            'referrer' => $matches[6],
            'agent' => $matches[7],
        ];
    }
}

$page = $_GET['page'] ?? 1;
$limit = $_GET['limit'] ?? 10;
$search = $_GET['search'] ?? '';

if ($search) {
    $entries = array_filter($entries, fn($entry) =>
        str_contains($entry['ip'], $search) || str_contains($entry['request'], $search)
    );
}

$total = count($entries);
$entries = array_slice($entries, ($page - 1) * $limit, $limit);

echo json_encode([
    'data' => array_values($entries),
    'total' => $total,
]);
