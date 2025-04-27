<?php
// מושך מידע מה-API של CoinGecko
$response = file_get_contents('https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd');

// הופך את המידע ממחרוזת JSON למערך
$data = json_decode($response, true);

// בודק אם קיבלנו מחיר תקין
if (isset($data['bitcoin']['usd'])) {
    $price = number_format($data['bitcoin']['usd'], 2);
    echo "הביטקוין עומד כעת על $price דולר.";
} else {
    echo "אין כרגע מידע זמין על הביטקוין.";
}
?>
