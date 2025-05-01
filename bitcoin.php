<?php
$response = file_get_contents("https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT");
$data = json_decode($response, true);

if (isset($data['price'])) {
    $price = number_format((float)$data['price'], 0);
    echo "מחיר הביטקוין כעת הוא: $price דולר.";
} else {
    echo "המידע על הביטקוין אינו זמין כרגע.";
}
?>
