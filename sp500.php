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

$response = getApiData('https://query1.finance.yahoo.com/v7/finance/quote?symbols=^GSPC');

if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['quoteResponse']['result'][0]['regularMarketPrice'])) {
        $price = (int) round($data['quoteResponse']['result'][0]['regularMarketPrice']);

        $thousands = floor($price / 1000);
        $remainder = $price % 1000;

        if ($remainder > 0) {
            $spokenPrice = $thousands . " אלף ו" . $remainder;
        } else {
            $spokenPrice = $thousands . " אלף";
        }

        echo "אס אנד פי חמש מאות עומד כעת על " . $spokenPrice . " נקודות.";
    } else {
        echo "המידע על מדד אס אנד פי אינו זמין כרגע, נסו שוב מאוחר יותר.";
    }
} else {
    echo "התקשורת עם שרת המידע נכשלה.";
}
?>
