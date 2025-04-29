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

$response = getApiData('https://api.binance.com/api/v3/ticker/price?symbol=SPXUSDT'); // שים לב! זה אס אנד פי בתצורת דולר אמריקאי

if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['price'])) {
        $price = number_format((float)$data['price'], 0);
        $parts = explode(',', $price);

        if (count($parts) > 1) {
            $formattedPrice = $parts[0] . ' אלף ו' . $parts[1];
        } else {
            $formattedPrice = $parts[0];
        }

        echo "אס אנד פי חמש מאות עומד כעת על $formattedPrice דולר.";
    } else {
        echo "המידע על אס אנד פי חמש מאות אינו זמין כרגע, נסו שוב מאוחר יותר.";
    }
} else {
    echo "התקשורת עם שרת המידע נכשלה.";
}
?>
