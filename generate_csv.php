<?php

function exportCsv($filename, $collection) {
    if ($collection->isEmpty()) return;
    $fp = fopen(__DIR__ . '/' . $filename, 'w');
    $first = $collection->first()->getAttributes();
    fputcsv($fp, array_keys($first));
    foreach ($collection as $item) {
        $row = $item->getAttributes();
        // flatten any arrays or objects
        foreach($row as $k => $v) {
            if(is_array($v) || is_object($v)) {
                $row[$k] = json_encode($v);
            }
        }
        fputcsv($fp, $row);
    }
    fclose($fp);
}

$users = \App\Models\User::factory()->count(20)->make();
exportCsv('users.csv', $users);

$products = \App\Models\Product::factory()->count(20)->make();
exportCsv('products.csv', $products);

$listings = \App\Models\CatListing::factory()->count(20)->make();
exportCsv('cat_listings.csv', $listings);

echo "CSVs generated successfully!\n";
