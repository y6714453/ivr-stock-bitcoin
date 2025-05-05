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

function formatChange($percent) {
    $symbol = $percent >= 0 ? "עלייה" : "ירידה";
    $value = number_format(abs($percent), 2);
    return "$symbol של $value%";
}

// שינוי תסמל כאן את הכתובת הרלוונטית – ביטקוין
$url = "https://query1.finance.yahoo.com/v8/finance/chart/BTC-USD?range=1y&interval=1d";
$response = getApiData($url);
$data = json_decode($response, true);

if (!isset($data['chart']['result'][0])) {
    echo "המידע על הביטקוין אינו זמין כעת.";
    exit;
}

$chart = $data['chart']['result'][0];
$prices = $chart['indicators']['quote'][0]['close'];
$timestamps = $chart['timestamp'];
$currentPrice = end($prices);
$firstDate = date('Y-m-d', $timestamps[0]);

// פונקציה למציאת מחיר לפי תנאי תאריך
function findClosestPrice($timestamps, $prices, $targetDate) {
    foreach ($timestamps as $i => $ts) {
        $date = date('Y-m-d', $ts);
        if ($date >= $targetDate && isset($prices[$i])) {
            return $prices[$i];
        }
    }
    return null;
}

// תאריכים רלוונטיים
$today = date('Y-m-d');
$weekStart = date('Y-m-d', strtotime('monday this week'));
$monthStart = date('Y-m-01');
$yearStart = date('Y-01-01');

// חישוב שינויים
$dayStartPrice = findClosestPrice($timestamps, $prices, $today);
$weekStartPrice = findClosestPrice($timestamps, $prices, $weekStart);
$monthStartPrice = findClosestPrice($timestamps, $prices, $monthStart);
$yearStartPrice = findClosestPrice($timestamps, $prices, $yearStart);
$yearHigh = $chart['meta']['fiftyTwoWeekHigh'];

function calcChange($from, $to) {
    if ($from === null || $to === null || $from == 0) return null;
    return (($to - $from) / $from) * 100;
}

// ניסוח סופי
echo "הביטקויין עומד כעת על: " . number_format($currentPrice, 0) . " דולר.\n";

if (($change = calcChange($dayStartPrice, $currentPrice)) !== null)
    echo "מאז פתיחת היום נרשמה " . formatChange($change) . ".\n";

if (($change = calcChange($weekStartPrice, $currentPrice)) !== null)
    echo "מתחילת השבוע נרשמה " . formatChange($change) . ".\n";

if (($change = calcChange($monthStartPrice, $currentPrice)) !== null)
    echo "מתחילת החודש נרשמה " . formatChange($change) . ".\n";

if (($change = calcChange($yearStartPrice, $currentPrice)) !== null)
    echo "מתחילת השנה נרשמה " . formatChange($change) . ".\n";

if (($fromPeak = calcChange($currentPrice, $yearHigh)) !== null)
    echo "המחיר הנוכחי נמוך ב־" . number_format(abs($fromPeak), 2) . "% מהשיא השנתי.";
?>
