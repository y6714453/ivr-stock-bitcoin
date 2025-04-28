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

header('Content-Type: text/plain; charset=utf-8');

$url = 'https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT';

$maxAttempts = 10;
$attempt = 0;
$success = false;

while ($attempt < $maxAttempts) {
    $attempt++;
    $response = getApiData($url);

    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['price'])) {
            $price = number_format((float)$data['price'], 2);
            echo "הביטקוין עומד כעת על $price דולר.";
            $success = true;
            break;
        }
    }

    usleep(300000); // 0.3 שניות המתנה בין נסיונות
}

if (!$success) {
    echo "המידע על הביטקוין אינו זמין כרגע, נסו שוב מאוחר יותר.";
}
?>
