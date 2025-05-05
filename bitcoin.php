<?php
function getApiData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// הגדרת הסימבול והטווח
$symbol = "BTC-USD";
$url = "https://query1.finance.yahoo.com/v8/finance/chart/$symbol?interval=1d&range=1y";

// שליפת הנתונים
$response = getApiData($url);
$data = json_decode($response, true);

// בדיקת קיום תוצאה
if (!isset($data['chart']['result'][0])) {
    echo "המידע על $symbol אינו זמין כעת.";
    exit;
}

// חילוץ נתונים
$result = $data['chart']['result'][0];
$timestamps = $result['timestamp'];
$prices = $result['indicators']['quote'][0]['close'];

$current_price = end($prices);
$start_of_day_price = $prices[count($prices) - 2]; // אתמול
$start_of_week_price = $prices[max(0, count($prices) - 6)];
$start_of_month_price = $prices[max(0, count($prices) - 22)];
$start_of_year_price = $prices[0];
$ath = max($prices);

// חישוב אחוזים
function calc_change($new, $old) {
    if ($old == 0) return 0;
    return round((($new - $old) / $old) * 100, 2);
}

$day_change = calc_change($current_price, $start_of_day_price);
$week_change = calc_change($current_price, $start_of_week_price);
$month_change = calc_change($current_price, $start_of_month_price);
$year_change = calc_change($current_price, $start_of_year_price);
$from_ath = calc_change($current_price, $ath);

// ניסוח התוצאה
echo "מחיר הביטקוין כעת הוא: " . number_format($current_price, 0) . " דולר.\n";
echo "שינוי מתחילת היום: $day_change%.\n";
echo "שינוי מתחילת השבוע: $week_change%.\n";
echo "שינוי מתחילת החודש: $month_change%.\n";
echo "שינוי מתחילת השנה: $year_change%.\n";
echo "מרחק מהשיא השנתי: " . abs($from_ath) . "% " . ($from_ath < 0 ? "נמוך מהשיא." : "מעל השיא.");
?>
