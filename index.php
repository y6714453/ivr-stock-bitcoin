<?php
// מיקום שמירת המחיר
$file = 'bitcoin_cache.txt';

// אם קובץ קיים והוא מעודכן מה־12 שניות האחרונות, נקרא ממנו
if (file_exists($file) && (time() - filemtime($file)) < 12) {
    $price = file_get_contents($file);
} else {
    // מושכים נתון חדש מ־Binance
    $url = "https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT";
    $response = @file_get_contents($url);

    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['price'])) {
            $price = number_format((float)$data['price'], 0);
            file_put_contents($file, $price);
        } else {
            $price = null;
        }
    } else {
        $price = null;
    }
}

// הדפסת התוצאה
if ($price !== null) {
    $thousands = floor($price / 1000);
    $rest = $price % 1000;

    if ($thousands > 0 && $rest > 0) {
        echo "הביטקוין עומד כעת על $thousands אלף ו$rest דולר.";
    } elseif ($thousands > 0) {
        echo "הביטקוין עומד כעת על $thousands אלף דולר.";
    } else {
        echo "הביטקוין עומד כעת על $price דולר.";
    }
} else {
    echo "המידע על הביטקוין אינו זמין כרגע, נסו שוב מאוחר יותר.";
}
?>
