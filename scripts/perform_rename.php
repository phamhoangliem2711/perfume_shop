<?php
require_once __DIR__ . '/../helpers.php';

$apply = in_array('--apply', $argv);
$previewFile = __DIR__ . '/rename_preview.json';
if (!file_exists($previewFile)) {
    echo "Preview file not found. Run preview_rename_images.php first.\n";
    exit(1);
}
$preview = json_decode(file_get_contents($previewFile), true);
$dir = __DIR__ . '/../public/assets/uploads';

foreach ($preview as $orig => $info) {
    if ($info['status'] === 'match') {
        $src = $dir . '/' . $orig;
        $dst = $dir . '/' . $info['new_name'];
        if ($apply) {
            if (!file_exists($src)) { echo "Source missing: $orig\n"; continue; }
            if (file_exists($dst)) { echo "Destination exists, skip: $dst\n"; continue; }
            if (rename($src, $dst)) echo "Renamed $orig -> {$info['new_name']}\n";
            else echo "Failed to rename $orig\n";
        } else {
            echo "Would rename: $orig -> {$info['new_name']}\n";
        }
    }
}

if (!$apply) echo "\nPass --apply to actually perform renames.\n";
