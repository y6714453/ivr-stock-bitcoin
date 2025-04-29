<?php
$url = "https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT";
$cacheFile = __DIR__ . '/bitcoin_cache.txt';
$cacheTime = 12; // שניות בין עדכון לעדכון

// ננסה למשוך מה-API אם עבר מספיק זמן
$shouldUpdate = !file_exists($cacheFile) || (time() - filemtime($cacheFile)) > $cacheTime;

if ($shouldUpdate) {
    $response = @file_get_contents($url);
    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['price']) && is_numeric($data['price'])) {
            $price = number_format((float)$data['price'], 0);
            file_put_contents($cacheFile, $price);
        }
    }
}

// שליפה מהקובץ
if (file_exists($cacheFile)) {
    $cachedPrice = file_get_contents($cacheFile);

    if (is_numeric($cachedPrice)) {
        $cachedPrice = (int)$cachedPrice;
        $thousands = floor($cachedPrice / 1000);
        $rest = $cachedPrice % 1000;

        if ($thousands > 0 && $rest > 0) {
            echo "הביטקוין עומד כעת על $thousands אלף ו$rest דולר.";
        } elseif ($thousands > 0) {
            echo "הביטקוין עומד כעת על $thousands אלף דולר.";
        } else {
            echo "הביטקוין עומד כעת על $cachedPrice דולר.";
        }
    } else {
        echo "המידע על הביטקוין אינו זמין כרגע.";
    }
} else {
    echo "המידע על הביטקוין אינו זמין כרגע.";
}
?>
