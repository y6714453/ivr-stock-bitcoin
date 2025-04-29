<?php
function getApiData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

$url = "https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT";
$cacheFile = __DIR__ . '/bitcoin_cache.txt';
$cacheTime = 12; // שניות

// בדיקת זמן אחרון
if (!file_exists($cacheFile) || (time() - filemtime($cacheFile)) > $cacheTime) {
    $response = getApiData($url);
    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['price'])) {
            $price = number_format((float)$data['price'], 0);
            file_put_contents($cacheFile, $price);
        }
    }
}

// קריאה מהזיכרון
if (file_exists($cacheFile)) {
    $cachedPrice = file_get_contents($cacheFile);

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
?>
