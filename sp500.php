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

$response = getApiData('https://query1.finance.yahoo.com/v8/finance/chart/^GSPC');

if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['chart']['result'][0]['meta']['regularMarketPrice'])) {
        $price = number_format((float)$data['chart']['result'][0]['meta']['regularMarketPrice'], 0);
        $thousands = floor($price / 1000);
        $rest = $price % 1000;

        if ($rest > 0) {
            echo "האס אנד פי עומד כעת על $thousands אלף ו$rest דולר.";
        } else {
            echo "האס אנד פי עומד כעת על $thousands אלף דולר.";
        }
    } else {
        echo "המידע על האס אנד פי אינו זמין כרגע, נסו שוב מאוחר יותר.";
    }
} else {
    echo "התקשורת עם שרת המידע נכשלה.";
}
?>
