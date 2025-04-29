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

$apiKey = "OVXGTL0ZUHCS61S7";
$response = getApiData("https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=SPY&apikey=$apiKey");

if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data["Global Quote"]["05. price"])) {
        $price = number_format((float)$data["Global Quote"]["05. price"], 0);

        $thousands = floor($price / 1000);
        $rest = $price % 1000;

        if ($rest > 0) {
            echo "מדד האס אנד פי חמש מאות עומד כעת על $thousands אלף ו$rest דולר.";
        } else {
            echo "מדד האס אנד פי חמש מאות עומד כעת על $thousands אלף דולר.";
        }
    } else {
        echo "המידע על מדד האס אנד פי חמש מאות לא זמין כרגע.";
    }
} else {
    echo "התקשורת עם שרת המידע נכשלה.";
}
?>
