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
$response = getApiData($url);
$data = json_decode($response, true);

// בדיקה מהירה
if (isset($data['chart']['result'][0]['meta']['regularMarketPrice'])) {
    $price = $data['chart']['result'][0]['meta']['regularMarketPrice'];
    echo "מחיר מדד S&P 500 כעת הוא: " . number_format($price, 2) . " דולר.";
} else {
    echo "שגיאה: לא נמצא regularMarketPrice";
}
?>
