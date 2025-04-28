<?php
header('Content-Type: text/plain; charset=utf-8');

$url = 'https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT';

$maxAttempts = 3;
$attempt = 0;
$success = false;

while ($attempt < $maxAttempts) {
    $attempt++;

    $response = @file_get_contents($url);

    if ($response !== false) {
        $data = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (isset($data['price'])) {
                $price = number_format((float)$data['price'], 2);
                echo "הביטקוין עומד כעת על $price דולר.";
                $success = true;
                break;
            }
        } else {
            echo "שגיאת JSON: " . json_last_error_msg() . "\n";
            break;
        }
    } else {
        echo "שגיאת file_get_contents: לא התקבלה תשובה מהשרת.\n";
        break;
    }

    usleep(300000); // המתנה קצרה
}

if (!$success) {
    echo "המידע על הביטקוין אינו זמין כרגע, נסו שוב מאוחר יותר.";
}
?>
