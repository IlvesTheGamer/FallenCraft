<?php
header('Content-Type: application/json');

// Välimuistitiedosto
$cacheFile = __DIR__ . '/status_cache.json';
$cacheTime = 60; // sekunteina

// Jos välimuisti on olemassa ja alle minuutin vanha, käytä sitä
if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
    echo file_get_contents($cacheFile);
    exit;
}

// Palvelinlista
$servers = [
    ["name" => "Fallen Survival", "host" => "2fallencraft0.aternos.me", "port" => 12802, "note" => "Survival, PVP rajoitettu"],
    ["name" => "Fallen Prison", "host" => "23.27.139.44", "port" => 25929, "note" => "Grindaus, Tarina."],
    ["name" => "Fallen Events", "host" => "TULOSSAPIAN", "port" => 25567, "note" => "TULOSSA PIAN"]
];

$result = [];
foreach ($servers as $srv) {
    $url = "https://api.mcsrvstat.us/2/{$srv['host']}:{$srv['port']}";
    $data = @file_get_contents($url);
    $json = $data ? json_decode($data, true) : null;
    $result[] = [
        "name" => $srv['name'],
        "host" => $srv['host'],
        "port" => $srv['port'],
        "note" => $srv['note'],
        "online" => $json && !empty($json['online']),
        "players" => $json['online'] ? $json['players']['online'] ?? 0 : 0,
        "max" => $json['online'] ? $json['players']['max'] ?? 0 : 0
    ];
}

// Tallenna välimuistiin
file_put_contents($cacheFile, json_encode($result));
echo json_encode($result);
