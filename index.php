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

$response = getApiData('https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT');

if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['price'])) {
        $fullPrice = (int) $data['price'];
        $thousands = floor($fullPrice / 1000);
        $remainder = $fullPrice % 1000;

        if ($thousands > 0 && $remainder > 0) {
            echo "הביטקוין עומד כעת על $thousands אלף ו $remainder דולר.";
        } elseif ($thousands > 0 && $remainder == 0) {
            echo "הביטקוין עומד כעת על $thousands אלף דולר.";
        } else {
            echo "הביטקוין עומד כעת על $fullPrice דולר.";
        }
    } else {
        echo "המידע על הביטקוין אינו זמין כרגע, נסו שוב מאוחר יותר.";
    }
} else {
    echo "התקשורת עם שרת המידע נכשלה.";
}
?>
