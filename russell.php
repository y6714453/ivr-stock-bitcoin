<?php
function getApiData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

$url = "https://query1.finance.yahoo.com/v8/finance/chart/^RUT";
$response = getApiData($url);
$data = json_decode($response, true);

if (isset($data['chart']['result'][0]['meta']['regularMarketPrice'])) {
    $price = (int) $data['chart']['result'][0]['meta']['regularMarketPrice']; // הופך ל־INT
    echo "מדד ה ראסל 2000 עומד על: $price";
} else {
    echo "שגיאה: לא נמצא מחיר עדכני למדד.";
}
?>
