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

// כתובת API עבור מדד S&P 500 מבית Alpha Vantage
$apiKey = 'OVXGTL0ZUHCS61S7';
$url = "https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=SPY&apikey=$apiKey";

$response = getApiData($url);

if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['Global Quote']['05. price'])) {
        $price = number_format((float)$data['Global Quote']['05. price'], 0);
        echo "מדד S&P 500 עומד כעת על $price דולר.";
    } else {
        echo "המידע על מדד S&P 500 אינו זמין כרגע, נסו שוב מאוחר יותר.";
    }
} else {
    echo "התקשורת עם שרת המידע נכשלה.";
}
?>
