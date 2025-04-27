<?php
// ננסה למשוך מידע מ-Binance עם ניסיון כפול
$attempts = 0;
$max_attempts = 2;
$price = null;

while ($attempts < $max_attempts && !$price) {
    $response = @file_get_contents('https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT');
    $data = json_decode($response, true);

    if ($response && isset($data['price'])) {
        $price = number_format($data['price'], 2);
    }
    $attempts++;
}

if ($price) {
    echo "הביטקוין עומד כעת על $price דולר.";
} else {
    echo "המידע על הביטקוין אינו זמין כרגע, אנא נסה שוב מאוחר יותר.";
}
?>
