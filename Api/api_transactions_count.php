<?php
// Dosya: Api/api_transactions_count.php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
set_time_limit(120);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$filename = '../CSV/transactions.csv';
$stats = [];

// Tarih Hesabı: Son 6 Ay
$sixMonthsAgo = date('Y-m-d H:i:s', strtotime('-6 months'));

if (file_exists($filename) && ($handle = fopen($filename, "r")) !== FALSE) {
    // Başlık satırını atla
    fgetcsv($handle, 10000, ",");

    while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
        // Index 1: INSTANCE_DATE
        // Index 7: AREA_EN (Bölge Adı)
        // Index 11: PROCEDURE_AREA (İşlem Metrekaresi)
        // Index 10: TRANS_VALUE (Satış Fiyatı)

        if (isset($row[1]) && isset($row[7]) && isset($row[10])) {
            $dateStr = $row[1];

            if ($dateStr < $sixMonthsAgo) {
                continue;
            }

            $areaName = trim($row[7]);

            // Sayısal değerleri temizle (virgül varsa kaldır)
            $price = (float) str_replace(',', '', $row[10]);

            // Eğer 9. sütun boşsa veya yoksa 0 kabul et
            $sqm = isset($row[11]) ? (float) str_replace(',', '', $row[11]) : 0;

            if (!empty($areaName)) {
                $cleanName = strtoupper($areaName);

                if (!isset($stats[$cleanName])) {
                    $stats[$cleanName] = [
                        'count' => 0,
                        'total_value' => 0,
                        'total_area' => 0 // Yeni alanımız
                    ];
                }

                $stats[$cleanName]['count']++;
                $stats[$cleanName]['total_value'] += $price;
                $stats[$cleanName]['total_area'] += $sqm;
            }
        }
    }
    fclose($handle);
} else {
    echo json_encode(["error" => "CSV dosyası bulunamadı."]);
    exit;
}

echo json_encode($stats);
