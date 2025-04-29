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

$url = "https://query1.finance.yahoo.com/v8/finance/chart/^GSPC";
$cacheFile = __DIR__ . '/sp500_cache.txt';
$cacheTime = 12; // שניות בין עדכון לעדכון

// עדכון הקובץ רק אם עבר הזמן
if (!file_exists($cacheFile) || (time() - filemtime($cacheFile)) > $cacheTime) {
    $response = getApiData($url);
    if ($response !== false) {
        $data = json_decode($response, true);
        $price = $data['chart']['result'][0]['meta']['regularMarketPrice'] ?? null;
        if ($price !== null) {
            $priceFormatted = number_format((float)$price, 0);
            file_put_contents($cacheFile, $priceFormatted);
        }
    }
}

// הצגת התוצאה
if (file_exists($cacheFile)) {
    $cachedPrice = file_get_contents($cacheFile);
    echo "מדד האס אנד פי 500 עומד כעת על $cachedPrice דולר.";
} else {
    echo "המידע על מדד האס אנד פי 500 אינו זמין כרגע.";
}
?>
