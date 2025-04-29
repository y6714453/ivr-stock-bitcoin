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

// כאן תכניס את המפתח שלך:
$apiKey = 'OVXGTL0ZUHCS61S7'; // ← תוכל להחליף במפתח שלך אם תקבל חדש
$response = getApiData("https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=SPY&apikey=$apiKey");

if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data["Global Quote"]["05. price"])) {
        $price = (float)$data["Global Quote"]["05. price"];
        $price = round($price); // עיגול למספר שלם

        if ($price < 1000) {
            echo "מדד האס אנד פי חמש מאות עומד כעת על $price דולר.";
        } else {
            $thousands = floor($price / 1000);
            $rest = $price % 1000;

            if ($rest > 0) {
                echo "מדד האס אנד פי חמש מאות עומד כעת על $thousands אלף ו$rest דולר.";
            } else {
                echo "מדד האס אנד פי חמש מאות עומד כעת על $thousands אלף דולר.";
            }
        }
    } else {
        echo "המידע על מדד האס אנד פי חמש מאות אינו זמין כרגע.";
    }
} else {
    echo "התקשורת עם שרת המידע נכשלה.";
}
?>
