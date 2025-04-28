<?php
function getApiData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // ממתינים עד 5 שניות לכל ניסיון
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

header('Content-Type: text/plain; charset=utf-8');

$url = 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd';

$maxAttempts = 10;
$attempt = 0;
$success = false;

while ($attempt < $maxAttempts) {
    $attempt++;
    $response = getApiData($url);

    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['bitcoin']['usd'])) {
            $price = number_format($data['bitcoin']['usd'], 2);
            echo "הביטקוין עומד כעת על $price דולר.";
            $success = true;
            break;
        }
    }

    // אם לא הצלחנו, מחכים קצת לפני ניסיון נוסף (כדי לא להעמיס את השרת)
    usleep(300000); // 300 מילישניות = 0.3 שניות
}

// אם אחרי 10 ניסיונות עדיין אין מידע - מחזירים הודעת כישלון
if (!$success) {
    echo "המידע על הביטקוין אינו זמין כרגע, נסו שוב מאוחר יותר.";
}
?>
