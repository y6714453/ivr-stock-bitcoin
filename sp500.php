<?php
function getApiData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// הגדרות
$apiKey = 'OVXGTL0ZUHUCS61S7'; // שים לב להסיר רווח אם הועתק עם רווח באמצע
$symbol = '^GSPC'; // מדד S&P 500
$url = "https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=$symbol&apikey=$apiKey";

$response = getApiData($url);

if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['Global Quote']['05. price'])) {
        $price = number_format((float)$data['Global Quote']['05. price'], 0);

        $thousands = floor($price / 1000);
        $rest = $price % 1000;

        if ($rest > 0) {
            echo "מדד האס אנד פי חמש מאות עומד כעת על $thousands אלף ו$rest דולר.";
        } else {
            echo "מדד האס אנד פי חמש מאות עומד כעת על $thousands אלף דולר.";
        }
    } else {
        echo "המידע על מדד האס אנד פי חמש מאות לא זמין כרגע, נסו שוב מאוחר יותר.";
    }
} else {
    echo "התקשורת עם שרת המידע נכשלה.";
}
?>
