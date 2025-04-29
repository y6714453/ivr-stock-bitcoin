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

// כאן האי פי איי כולל המפתח שלך!
$apiUrl = 'https://api.twelvedata.com/price?symbol=^GSPC&apikey=crp_841rB5X9FT197O7_0OPLewqQ80Il';

// נביא את הנתון
$response = getApiData($apiUrl);

if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['price'])) {
        $price = number_format((float)$data['price'], 0);

        echo "מדד S&P 500 עומד כעת על $price דולר.";
    } else {
        echo "המידע על מדד S&P 500 אינו זמין כרגע, נסו שוב מאוחר יותר.";
    }
} else {
    echo "התקשורת עם שרת המידע נכשלה.";
}
?>
