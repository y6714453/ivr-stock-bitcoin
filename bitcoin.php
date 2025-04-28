<?php
$response = @file_get_contents('https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT');

if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['price'])) {
        $price = number_format($data['price'], 2);
        echo "הביטקוין עומד כעת על $price דולר.";
    } else {
        echo "המידע על הביטקוין אינו זמין כרגע, נסה שוב מאוחר יותר.";
    }
} else {
    echo "התקשורת עם שרת המידע נכשלה.";
}
?>
