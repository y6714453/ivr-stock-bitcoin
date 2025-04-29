<?php
function getApiData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_ENCODING, ''); // תמיכה ב-gzip
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

$url = "https://query1.finance.yahoo.com/v8/finance/chart/^GSPC";
$response = getApiData($url);
$data = json_decode($response, true);

if (isset($data['chart']['result']) && is_array($data['chart']['result']) && isset($data['chart']['result'][0]['meta']['regularMarketPrice'])) {
    $price = $data['chart']['result'][0]['meta']['regularMarketPrice'];
    echo "מחיר מדד S&P 500 כעת הוא: " . number_format($price, 2) . " דולר.";
} else {
    echo "שגיאה: לא נמצא מחיר עדכני למדד.";
}
?>
