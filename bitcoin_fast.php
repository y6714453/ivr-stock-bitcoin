<?php
$response = file_get_contents("https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT");
$data = json_decode($response, true);

if (isset($data['price'])) {
    $price = round((float)$data['price']);
    echo $price . " דולר";
} else {
    echo "אין נתונים זמינים";
}
?>
