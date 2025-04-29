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

// מפתח API של Finnhub
$apiKey = 'd08h4vpr01qh1ecc64o0d08h4vpr01qh1ecc64og'; // אל תשכח להכניס את המפתח שלך כאן!

// כתובת ה-API של Finnhub למדד S&P 500
$url = "https://finnhub.io/api/v1/quote?symbol=^GSPC&token=$apiKey";

$cacheFile = __DIR__ . '/sp500_cache.txt';
$cacheTime = 12; // שניות בין עדכון לעדכון

// בדיקת זמן אחרון
if (!file_exists($cacheFile) || (time() - filemtime($cacheFile)) > $cacheTime) {
    $response = getApiData($url);
    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['c'])) { // c = current price לפי התיעוד של Finnhub
            $price = number_format((float)$data['c'], 0);
            file_put_contents($cacheFile, $price);
        }
    }
}

// קריאה מהזיכרון
if (file_exists($cacheFile)) {
    $cachedPrice = file_get_contents($cacheFile);
    echo "מדד האס אנד פי 500 עומד כעת על $cachedPrice דולר.";
} else {
    echo "המידע על מדד האס אנד פי 500 אינו זמין כרגע.";
}
?>
