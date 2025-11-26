<?php
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('memory_limit', '512M');

$filename = dirname(__DIR__) . '/CSV/projects.csv';
$stats = [];

// Tarih Hesabı: Son 6 Ay
$sixMonthsAgo = date('Y-m-d H:i:s', strtotime('-6 months'));

if (file_exists($filename) && ($handle = fopen($filename, "r")) !== FALSE) {

    $bom = fread($handle, 3);
    if ($bom != "\xEF\xBB\xBF") {
        rewind($handle);
    }

    // Başlık satırını atla
    fgetcsv($handle, 10000, ",");

    while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
        // Index 3:  DEVELOPER_EN
        // Index 4:  START_DATE (Tarih Filtresi İçin)
        // Index 8:  PROJECT_VALUE
        // Index 15: AREA_EN

        // 1. Önce Bölge Kontrolü
        if (isset($row[15]) && !empty(trim($row[15]))) {

            // 2. Tarih Kontrolü (Son 6 Ay)
            if (isset($row[4])) {
                $projectDate = $row[4];

                if (!empty($projectDate) && $projectDate < $sixMonthsAgo) {
                    continue;
                }
            }

            $areaName = trim($row[15]);

            // Değeri temizle
            $valStr = isset($row[8]) ? $row[8] : "0";
            $projectValue = (float) str_replace(['"', ',', ' '], '', $valStr);

            // Developer İsmi
            $developer = isset($row[3]) ? trim($row[3]) : '';

            $cleanName = mb_strtoupper($areaName, 'UTF-8');

            if (!isset($stats[$cleanName])) {
                $stats[$cleanName] = [
                    'count' => 0,
                    'total_value' => 0,
                    'developers' => []
                ];
            }

            // İstatistikleri Ekle
            $stats[$cleanName]['count']++;
            $stats[$cleanName]['total_value'] += $projectValue;

            // Developer Ekle (Benzersiz)
            if (!empty($developer) && !in_array($developer, $stats[$cleanName]['developers'])) {
                $stats[$cleanName]['developers'][] = $developer;
            }
        }
    }
    fclose($handle);
} else {
    echo json_encode(["error" => "CSV dosyası bulunamadı."]);
    exit;
}

ob_end_clean();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

echo json_encode($stats);
