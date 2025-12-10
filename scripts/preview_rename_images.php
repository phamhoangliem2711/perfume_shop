<?php
require_once __DIR__ . '/../helpers.php';
$db = db_connect();

$dir = __DIR__ . '/../public/assets/uploads';
$files = array_values(array_filter(scandir($dir), function($f){ return !in_array($f, ['.','..']); }));

// Load products (id, ten)
$products = $db->query("SELECT id, ten FROM products")->fetchAll(PDO::FETCH_ASSOC);

function normalize($s) {
    $s = mb_strtolower($s, 'UTF-8');
    // remove accents
    $s = iconv('UTF-8', 'ASCII//TRANSLIT', $s);
    $s = preg_replace('/[^a-z0-9\s]/', ' ', $s);
    $s = preg_replace('/\s+/', ' ', $s);
    return trim($s);
}

$prodIndex = [];
foreach ($products as $p) {
    $prodIndex[$p['id']] = normalize($p['ten']);
}

$preview = [];

foreach ($files as $f) {
    if (!preg_match('/\.(jpe?g|png|gif|webp|svg)$/i', $f)) continue;
    // skip files that already start with digits_ (already have productid_)
    if (preg_match('/^(\d+)_/', $f)) {
        $preview[$f] = [ 'status' => 'skip_already_prefixed' ];
        continue;
    }

    $nameNoExt = pathinfo($f, PATHINFO_FILENAME);
    $norm = normalize($nameNoExt);
    $tokens = preg_split('/\s+/', $norm);

    $best = ['score' => 0, 'product_id' => null];

    foreach ($prodIndex as $pid => $pname) {
        $score = 0;
        foreach ($tokens as $t) {
            if ($t === '') continue;
            if (strpos($pname, $t) !== false) $score += 1;
        }
        if ($score > $best['score']) {
            $best = ['score' => $score, 'product_id' => $pid];
        }
    }

    if ($best['score'] > 0 && $best['product_id']) {
        $newName = $best['product_id'] . '_' . $f;
        $preview[$f] = [ 'status' => 'match', 'product_id' => $best['product_id'], 'score' => $best['score'], 'new_name' => $newName ];
    } else {
        $preview[$f] = [ 'status' => 'no_match' ];
    }
}

// Save preview to JSON
file_put_contents(__DIR__ . '/rename_preview.json', json_encode($preview, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Print summary
$matched = 0; $nomatch = 0; $skipped = 0;
foreach ($preview as $orig => $info) {
    if ($info['status'] === 'match') $matched++;
    elseif ($info['status'] === 'no_match') $nomatch++;
    else $skipped++;
}

echo "Preview generated: " . __DIR__ . "/rename_preview.json\n";
echo "Files scanned: " . count($files) . "\n";
echo "Matched: $matched, No match: $nomatch, Skipped(already prefixed): $skipped\n\n";

foreach ($preview as $orig => $info) {
    if ($info['status'] === 'match') {
        echo sprintf("%s  =>  %s  (product_id=%s, score=%d)\n", $orig, $info['new_name'], $info['product_id'], $info['score']);
    } elseif ($info['status'] === 'no_match') {
        echo sprintf("%s  =>  (no match)\n", $orig);
    } else {
        echo sprintf("%s  =>  (skip: already prefixed)\n", $orig);
    }
}

echo "\nTo actually rename files, run: php scripts/perform_rename.php --apply\n";
