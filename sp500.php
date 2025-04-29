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

$apiKey = 'OVXGTL0ZUHCS61S7';
$url = "https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=SPY&apikey=$apiKey";
$cacheFile = __DIR__ . '/sp500_cache.txt';
$cacheTime = 12; // שניות בין עדכון לעדכון

// בדיקת זמן אחרון
if (!file_exists($cacheFile) || (time() - filemtime($cacheFile)) > $cacheTime) {
    $response = getApiData($url);
    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['Global Quote']['05. price'])) {
            $price = number_format((float)$data['Global Quote']['05. price'], 0);
            file_put_contents($cacheFile, $price);
        }
    }
}

// קריאה מהזיכרון
if (file_exists($cacheFile)) {
    $cachedPrice = file_get_contents($cacheFile);
    echo "מדד S&P 500 עומד כעת על $cachedPrice דולר.";
} else {
    echo "המידע על מדד S&P 500 אינו זמין כרגע.";
}
?>
