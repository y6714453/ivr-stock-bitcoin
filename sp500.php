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

// הכנס את המפתח שלך כאן
$apiKey = "OXe5vDCaDebbABKvEJlYUVF3awqN0e1f";
$url = "https://api.polygon.io/v2/aggs/ticker/I:SPX/prev?adjusted=true&apiKey=$apiKey";

$response = getApiData($url);

if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['results'][0]['c'])) {
        $price = number_format((float)$data['results'][0]['c'], 0);

        $thousands = floor($price / 1000);
        $rest = $price % 1000;

        if ($rest > 0) {
            echo "אס אנד פי 500 עומד כעת על $thousands אלף ו$rest דולר.";
        } else {
            echo "אס אנד פי 500 עומד כעת על $thousands אלף דולר.";
        }
    } else {
        echo "המידע על אס אנד פי 500 אינו זמין כרגע, נסו שוב מאוחר יותר.";
    }
} else {
    echo "התקשורת עם שרת המידע נכשלה.";
}
?>
