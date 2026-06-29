<?php
/**
 * Download & generate images during Docker build.
 * Images are not stored in git (HF Spaces blocks binary files).
 * Instead, they are fetched from original URLs and converted at build time.
 */

// Use curl if available, fallback to file_get_contents
function download($url, $timeout = 30) {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'Docker/1.0',
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($httpCode >= 200 && $httpCode < 300) ? $data : false;
    }
    $ctx = stream_context_create([
        'http' => ['timeout' => $timeout, 'user_agent' => 'Docker/1.0'],
        'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
    ]);
    $data = @file_get_contents($url, false, $ctx);
    return $data;
}

$urls = [
    'bg'   => 'https://dpmd.pasuruankab.go.id/storage/file_media/cc16405a863814d5402ea575f2e4d972.jpg',
    'logo' => 'https://upload.wikimedia.org/wikipedia/commons/9/9a/Lambang_Kabupaten_Pasuruan.png',
];

$dir = '/var/www/html/public/images';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// Background: download JPEG, convert to WebP
echo "  -> Downloading background...\n";
$jpg = download($urls['bg']);
if ($jpg !== false) {
    $tmp = tempnam(sys_get_temp_dir(), 'bg_') . '.jpg';
    file_put_contents($tmp, $jpg);
    $img = @imagecreatefromjpeg($tmp);
    if ($img) {
        $out = $dir . '/bg-login.webp';
        imagewebp($img, $out, 60);
        imagedestroy($img);
        echo "  -> Background: " . round(filesize($out) / 1024) . "KB\n";
    } else {
        echo "  [WARN] Failed to decode JPEG\n";
    }
    @unlink($tmp);
} else {
    echo "  [WARN] Background download failed\n";
}

// Logo: download PNG directly
echo "  -> Downloading logo...\n";
$png = download($urls['logo']);
if ($png !== false) {
    $out = $dir . '/logo-pasuruan.png';
    file_put_contents($out, $png);
    echo "  -> Logo: " . round(filesize($out) / 1024) . "KB\n";
} else {
    echo "  [WARN] Logo download failed\n";
}

echo "  -> Done.\n";
