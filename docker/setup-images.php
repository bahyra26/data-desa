<?php
/**
 * Download & generate images during Docker build.
 * Images are not stored in git (HF Spaces blocks binary files).
 * Instead, they are fetched from original URLs and converted at build time.
 */
$urls = [
    'bg'   => 'https://dpmd.pasuruankab.go.id/storage/file_media/cc16405a863814d5402ea575f2e4d972.jpg',
    'logo' => 'https://upload.wikimedia.org/wikipedia/commons/9/9a/Lambang_Kabupaten_Pasuruan.png',
];

$ctx = stream_context_create([
    'http' => ['timeout' => 30, 'user_agent' => 'Docker/1.0'],
    'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
]);

$dir = '/var/www/html/public/images';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// Background: download JPEG, convert to WebP
$jpg = @file_get_contents($urls['bg'], false, $ctx);
if ($jpg !== false) {
    $tmp = tempnam(sys_get_temp_dir(), 'bg_') . '.jpg';
    file_put_contents($tmp, $jpg);
    $img = @imagecreatefromjpeg($tmp);
    if ($img) {
        $out = $dir . '/bg-login.webp';
        imagewebp($img, $out, 60);
        imagedestroy($img);
        echo "  -> Background: " . round(filesize($out) / 1024) . "KB\n";
    }
    @unlink($tmp);
} else {
    echo "  [WARN] Background download failed\n";
}

// Logo: download PNG directly
$png = @file_get_contents($urls['logo'], false, $ctx);
if ($png !== false) {
    $out = $dir . '/logo-pasuruan.png';
    file_put_contents($out, $png);
    echo "  -> Logo: " . round(filesize($out) / 1024) . "KB\n";
} else {
    echo "  [WARN] Logo download failed\n";
}
