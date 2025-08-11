<?php
header('Content-Type: application/json');

// Välimuisti (60 sekuntia)
$cacheFile = __DIR__ . '/status_cache.json';
$cacheTime = 60;

if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
    echo file_get_contents($cacheFile);
    exit;
}

$servers = [
    ["name" => "Fallen Survival", "host" => "2fallencraft0.aternos.me", "port" => 12802, "note" => "Survival, PVP rajoitettu"],
    ["name" => "Fallen Prison", "host" => "23.27.139.44", "port" => 25929, "note" => "Grindaus, Tarina."],
    ["name" => "Fallen Events", "host" => "TULOSSAPIAN", "port" => 25567, "note" => "TULOSSA PIAN"]
];

$result = [];
foreach ($servers as $srv) {
    $url = "https://api.mcsrvstat.us/2/{$srv['host']}:{$srv['port']}";

    // cURL nopeaan aikakatkaisuun
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2); // 2 sek aikakatkaisu
    $data = curl_exec($ch);
    curl_close($ch);

    $json = $data ? json_decode($data, true) : null;

    $result[] = [
        "name" => $srv['name'],
        "host" => $srv['host'],
        "port" => $srv['port'],
        "note" => $srv['note'],
        "online" => ($json && isset($json['online']) && $json['online'] === true),
        "players" => ($json && isset($json['players']['online'])) ? $json['players']['online'] : 0,
        "max" => ($json && isset($json['players']['max'])) ? $json['players']['max'] : 0
    ];
}

// Tallennetaan välimuistiin ja palautetaan
file_put_contents($cacheFile, json_encode($result));
echo json_encode($result);

