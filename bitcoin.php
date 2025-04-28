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

$response = getApiData('https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd');

header('Content-Type: application/json; charset=utf-8');

if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['bitcoin']['usd'])) {
        $price = number_format($data['bitcoin']['usd'], 2);
        echo json_encode([
            "say" => "הביטקוין עומד כעת על $price דולר.",
            "goto" => "end"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "say" => "המידע על הביטקוין אינו זמין כרגע, נסו שוב מאוחר יותר.",
            "goto" => "end"
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        "say" => "התקשורת עם שרת המידע נכשלה.",
        "goto" => "end"
    ], JSON_UNESCAPED_UNICODE);
}
?>
