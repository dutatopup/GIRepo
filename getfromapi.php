<?php
header('Content-Type: application/json');
$curl = curl_init();

curl_setopt_array(
    $curl,
    array(
        CURLOPT_URL => "https://sg-public-api.serenetia.com/api/hyp_global?game_id=gopR6Cufr3",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    )
);

$ResponcURL = json_decode(curl_exec($curl), true);
curl_close($curl);

// Mendapatkan array predownload dan patch
$preDownload = $ResponcURL['data']['game_packages'][0]['pre_download'];
$patches = $preDownload['patches'];

// Inisialisasi array untuk URL, versi, dan ukuran dari predownload dan patch
$preDownloadFiles = [];
$patchFiles = [];

// Fungsi untuk mengkonversi ukuran file ke MB atau GB
function formatSize($size) {
    $size = (int)$size;
    if ($size >= 1073741824) {
        return number_format($size / 1073741824, 2) . ' GB';
    } elseif ($size >= 1048576) {
        return number_format($size / 1048576, 2) . ' MB';
    } else {
        return number_format($size / 1024, 2) . ' KB';
    }
}

// Mendapatkan URL, versi, dan ukuran dari predownload game_pkgs
foreach ($preDownload['major']['game_pkgs'] as $game_pkg) {
    $preDownloadFiles[] = [
        'version' => $preDownload['major']['version'],
        'url' => $game_pkg['url'],
        'size' => formatSize($game_pkg['size'])
    ];
}

// Mendapatkan URL dan ukuran dari predownload audio_pkgs
foreach ($preDownload['major']['audio_pkgs'] as $audio_pkg) {
    $preDownloadFiles[] = [
        'version' => $preDownload['major']['version'],
        'url' => $audio_pkg['url'],
        'size' => formatSize($audio_pkg['size'])
    ];
}

// Mendapatkan URL, versi, dan ukuran dari patch game_pkgs dan audio_pkgs
foreach ($patches as $patch) {
    foreach ($patch['game_pkgs'] as $game_pkg) {
        $patchFiles[] = [
            'version' => $patch['version'],
            'url' => $game_pkg['url'],
            'size' => formatSize($game_pkg['size'])
        ];
    }

    foreach ($patch['audio_pkgs'] as $audio_pkg) {
        $patchFiles[] = [
            'version' => $patch['version'],
            'url' => $audio_pkg['url'],
            'size' => formatSize($audio_pkg['size'])
        ];
    }
}

// Menampilkan array predownload
echo "PreDownload:\n";
foreach ($preDownloadFiles as $file) {
    echo "Version: " . $file['version'] . "\n";
    echo "URL: " . $file['url'] . "\n";
    echo "Size: " . $file['size'] . "\n\n";
}

// Menampilkan array patch dengan label "PATCH"
echo "PATCH:\n";
foreach ($patchFiles as $file) {
    echo "Version: " . $file['version'] . "\n";
    echo "URL: " . $file['url'] . "\n";
    echo "Size: " . $file['size'] . "\n\n";
}
