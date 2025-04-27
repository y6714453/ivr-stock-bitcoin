<?php
// מביא מידע מ-Binance API
$response = @file_get_contents('https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT');

// הופך את המידע ממחרוזת JSON למערך
$data = json_decode($response, true);

// בודק אם קיבלנו מחיר תקין
if ($response && isset($data['price'])) {
    $price = number_format($data['price'], 2);
    echo "הביטקוין עומד כעת על $price דולר.";
} else {
    echo "המידע על הביטקוין אינו זמין כרגע, אנא נסו שוב מאוחר יותר.";
}
?>
