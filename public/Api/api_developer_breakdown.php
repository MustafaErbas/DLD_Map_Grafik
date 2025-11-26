<?php
// Dosya: Api/api_developer_breakdown.php

ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('memory_limit', '512M');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$filename = dirname(__DIR__) . '/CSV/projects.csv';
$developers = [];

if (file_exists($filename) && ($handle = fopen($filename, "r")) !== FALSE) {

    // UTF-8 BOM Temizliği
    $bom = fread($handle, 3);
    if ($bom != "\xEF\xBB\xBF") rewind($handle);

    fgetcsv($handle, 10000, ","); // Başlıkları atla

    while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
        // Index 3:  DEVELOPER_EN (Geliştirici)
        // Index 8:  PROJECT_VALUE (Değer)
        // Index 15: AREA_EN (Bölge)

        if (isset($row[3]) && isset($row[15])) {
            $devName = trim($row[3]);
            $areaName = trim($row[15]);

            if (empty($devName) || empty($areaName)) continue;

            // Değeri temizle
            $valStr = isset($row[8]) ? $row[8] : "0";
            $projectValue = (float) str_replace(['"', ',', ' '], '', $valStr);

            // İsimleri standartlaştır
            $cleanDev = mb_strtoupper($devName, 'UTF-8');
            $cleanArea = mb_strtoupper($areaName, 'UTF-8');

            // Yapı: Developers -> [Developer Adı] -> [Bölge Adı] -> {count, value}

            if (!isset($developers[$cleanDev])) {
                $developers[$cleanDev] = [
                    'total_global_count' => 0, // Sıralama yapmak için genel toplam
                    'areas' => []
                ];
            }

            if (!isset($developers[$cleanDev]['areas'][$cleanArea])) {
                $developers[$cleanDev]['areas'][$cleanArea] = [
                    'count' => 0,
                    'value' => 0
                ];
            }

            // Verileri işle
            $developers[$cleanDev]['areas'][$cleanArea]['count']++;
            $developers[$cleanDev]['areas'][$cleanArea]['value'] += $projectValue;

            // Genel sayacı artır (Listede sıralama yapmak için)
            $developers[$cleanDev]['total_global_count']++;
        }
    }
    fclose($handle);
}

ob_end_clean();
echo json_encode($developers);
