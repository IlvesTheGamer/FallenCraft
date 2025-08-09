<?php
header('Content-Type: application/json');

// Välimuisti poistettu testiksi
/*
$cacheFile = __DIR__ . '/status_cache.json';
$cacheTime = 60; // sekunteina

if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
    echo file_get_contents($cacheFile);
    exit;
}
*/

// Palvelinlista
$servers = [
    ["name" => "Fallen Survival", "host" => "2fallencraft0.aternos.me", "port" => 12802, "note" => "Survival, PVP rajoitettu"],
    ["name" => "Fallen Prison", "host" => "23.27.139.44", "port" => 25929, "note" => "Grindaus, Tarina."],
    ["name" => "Fallen Events", "host" => "TULOSSAPIAN", "port" => 25567, "note" => "TULOSSA PIAN"]
];

$result = [];
foreach ($servers as $srv) {
    // Huom! Poistetaan portti URL:stä testiksi, koska API ei välttämättä käytä porttia
    $url = "https://api.mcsrvstat.us/2/{$srv['host']}";
    
    $data = @file_get_contents($url);
    if ($data === false) {
        error_log("API-haku epäonnistui palvelimelle: {$srv['host']}");
        $json = null;
    } else {
        $json = json_decode($data, true);
        error_log("API-data palvelimelta {$srv['host']}: " . print_r($json, true));
    }
    
    $result[] = [
        "name" => $srv['name'],
        "host" => $srv['host'],
        "port" => $srv['port'],
        "note" => $srv['note'],
        "online" => $json && !empty($json['online']),
        "players" => ($json && !empty($json['players']['online'])) ? $json['players']['online'] : 0,
        "max" => ($json && !empty($json['players']['max'])) ? $json['players']['max'] : 0
    ];
}

// Tulostetaan JSON-vastaus
echo json_encode($result);
