<?php
function getApiData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
    } else {
        $error_msg = null;
    }

    curl_close($ch);

    return [$response, $error_msg];
}

header('Content-Type: text/plain; charset=utf-8');

$url = 'https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT';

$maxAttempts = 3; // קיצרתי ל-3 ניסיונות לבדיקה מהירה
$attempt = 0;
$success = false;

while ($attempt < $maxAttempts) {
    $attempt++;
    list($response, $error_msg) = getApiData($url);

    if ($error_msg !== null) {
        echo "שגיאת CURL: $error_msg\n";
        break; // אם יש שגיאת רשת עוצרים ישר
    }

    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['price'])) {
            $price = number_format((float)$data['price'], 2);
            echo "הביטקוין עומד כעת על $price דולר.";
            $success = true;
            break;
        }
    }

    usleep(300000); // 0.3 שניות המתנה בין ניסיונות
}

if (!$success && $error_msg === null) {
    echo "המידע על הביטקוין אינו זמין כרגע, נסו שוב מאוחר יותר.";
}
?>
