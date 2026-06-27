<?php
/**
 * Image Handler for GymnasticsHub
 * Resizing and WebP conversion
 */

function gh_process_avatar($file_path) {
    if (!file_exists($file_path)) return false;

    // Get image info
    $info = getimagesize($file_path);
    if (!$info) return false;

    $mime = $info['mime'];
    
    // Create image from source
    switch ($mime) {
        case 'image/jpeg': $src = imagecreatefromjpeg($file_path); break;
        case 'image/png':  $src = imagecreatefrompng($file_path);  break;
        case 'image/webp': $src = imagecreatefromwebp($file_path); break;
        default: return false;
    }

    if (!$src) return false;

    // Resize logic (Square 300x300)
    $width  = imagesx($src);
    $height = imagesy($src);
    $size   = min($width, $height);
    
    $dst = imagecreatetruecolor(300, 300);
    
    // Transparency for WebP
    imagealphablending($dst, false);
    imagesavealpha($dst, true);

    // Crop and Resize
    imagecopyresampled($dst, $src, 0, 0, ($width - $size) / 2, ($height - $size) / 2, 300, 300, $size, $size);

    // Save as WebP
    $new_path = preg_replace('/\.[^.]+$/', '.webp', $file_path);
    imagewebp($dst, $new_path, 80); // 80% quality

    // Cleanup
    imagedestroy($src);
    imagedestroy($dst);
    
    if ($file_path !== $new_path) {
        unlink($file_path); // Delete original if it was not webp
    }

    return basename($new_path);
}

function gh_convert_to_webp($file_path, $quality = 80) {
    if (!file_exists($file_path)) return false;

    $info = getimagesize($file_path);
    if (!$info) return false;

    $mime = $info['mime'];
    if ($mime === 'image/webp') return $file_path; // Already webp

    switch ($mime) {
        case 'image/jpeg': $src = imagecreatefromjpeg($file_path); break;
        case 'image/png':  $src = imagecreatefrompng($file_path);  break;
        default: return false;
    }

    if (!$src) return false;

    $new_path = preg_replace('/\.[^.]+$/', '.webp', $file_path);
    
    // Preserve transparency for PNG
    if ($mime === 'image/png') {
        imagealphablending($src, true);
        imagesavealpha($src, true);
    }

    imagewebp($src, $new_path, $quality);
    imagedestroy($src);

    if ($file_path !== $new_path) {
        unlink($file_path);
    }

    return $new_path;
}
