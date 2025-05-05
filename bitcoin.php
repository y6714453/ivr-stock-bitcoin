<?php
// כתובת API של CoinGecko למחיר ביטקוין במטבע דולר
$url = "https://api.coingecko.com/api/v3/coins/bitcoin/market_chart?vs_currency=usd&days=max";

// שליפת נתונים מה־API
$response = file_get_contents($url);
$data = json_decode($response, true);

// שליפת המחיר הנוכחי מ־Binance
$price_response = file_get_contents("https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT");
$price_data = json_decode($price_response, true);
$current_price = isset($price_data['price']) ? (float)$price_data['price'] : null;

if (!$current_price || !isset($data['prices'])) {
    echo "המידע על הביטקוין אינו זמין כעת.";
    exit;
}

// פונקציה למציאת המחיר בתאריך מסוים
function findClosestPrice($data, $timestamp) {
    $closest = null;
    $min_diff = PHP_INT_MAX;
    foreach ($data as $entry) {
        $diff = abs($entry[0] - $timestamp);
        if ($diff < $min_diff) {
            $min_diff = $diff;
            $closest = $entry[1];
        }
    }
    return $closest;
}

// תאריכים נדרשים
$now = time() * 1000;
$start_of_day = strtotime("today") * 1000;
$start_of_week = strtotime("last sunday") * 1000;
$start_of_month = strtotime("first day of this month") * 1000;
$start_of_year = strtotime("first day of January") * 1000;

// מחירים היסטוריים
$price_day = findClosestPrice($data['prices'], $start_of_day);
$price_week = findClosestPrice($data['prices'], $start_of_week);
$price_month = findClosestPrice($data['prices'], $start_of_month);
$price_year = findClosestPrice($data['prices'], $start_of_year);

// מחיר שיא מאז ומתמיד
$highest = 0;
foreach ($data['prices'] as $entry) {
    if ($entry[1] > $highest) {
        $highest = $entry[1];
    }
}

// חישוב שינויים באחוזים
function percent_change($current, $past) {
    if (!$past) return 0;
    return round((($current - $past) / $past) * 100, 2);
}

$change_day = percent_change($current_price, $price_day);
$change_week = percent_change($current_price, $price_week);
$change_month = percent_change($current_price, $price_month);
$change_year = percent_change($current_price, $price_year);
$from_ath = percent_change($current_price, $highest);

// ניסוח התוצאה
echo "מחיר הביטקוין כעת הוא: " . number_format($current_price, 0) . " דולר.\n";
echo "שינוי מתחילת היום: " . $change_day . "%.\n";
echo "שינוי מתחילת השבוע: " . $change_week . "%.\n";
echo "שינוי מתחילת החודש: " . $change_month . "%.\n";
echo "שינוי מתחילת השנה: " . $change_year . "%.\n";
echo "מרחק מהשיא ההיסטורי: " . abs($from_ath) . "% " . ($from_ath < 0 ? "נמוך מהשיא." : "מעל השיא.");
?>
