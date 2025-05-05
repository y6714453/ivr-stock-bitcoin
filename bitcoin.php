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

function findClosestPriceBefore($timestamps, $prices, $targetTimestamp) {
    $bestPrice = null;
    for ($i = count($timestamps) - 1; $i >= 0; $i--) {
        if ($timestamps[$i] <= $targetTimestamp) {
            $bestPrice = $prices[$i];
            break;
        }
    }
    return $bestPrice;
}

function formatChange($current, $previous) {
    if ($previous === null || $previous == 0) {
        return "אין נתון זמין.";
    }
    $change = (($current - $previous) / $previous) * 100;
    $sign = $change > 0 ? "עלייה" : ($change < 0 ? "ירידה" : "שינוי אפסי");
    return "$sign של " . number_format(abs($change), 2) . "%";
}

$url = "https://query1.finance.yahoo.com/v8/finance/chart/BTC-USD?range=6mo&interval=1d";
$response = getApiData($url);
$data = json_decode($response, true);

if (
    isset($data['chart']['result'][0]['meta']['regularMarketPrice']) &&
    isset($data['chart']['result'][0]['timestamp']) &&
    isset($data['chart']['result'][0]['indicators']['quote'][0]['close'])
) {
    $currentPrice = $data['chart']['result'][0]['meta']['regularMarketPrice'];
    $timestamps = $data['chart']['result'][0]['timestamp'];
    $prices = $data['chart']['result'][0]['indicators']['quote'][0]['close'];

    $now = time();
    $startOfDay = strtotime("today", $now);
    $startOfWeek = strtotime("last sunday", $now); // או "this week"
    $startOfMonth = strtotime(date("Y-m-01", $now));
    $startOfYear = strtotime(date("Y-01-01", $now));

    $priceDay = findClosestPriceBefore($timestamps, $prices, $startOfDay);
    $priceWeek = findClosestPriceBefore($timestamps, $prices, $startOfWeek);
    $priceMonth = findClosestPriceBefore($timestamps, $prices, $startOfMonth);
    $priceYear = findClosestPriceBefore($timestamps, $prices, $startOfYear);
    $yearHigh = $data['chart']['result'][0]['meta']['fiftyTwoWeekHigh'];

    echo "הביטקויין עומד כעת על: " . number_format($currentPrice, 0) . " דולר. ";
    echo "מאז פתיחת היום נרשמה " . formatChange($currentPrice, $priceDay) . ". ";
    echo "מתחילת השבוע נרשמה " . formatChange($currentPrice, $priceWeek) . ". ";
    echo "מתחילת החודש נרשמה " . formatChange($currentPrice, $priceMonth) . ". ";
    echo "מתחילת השנה נרשמה " . formatChange($currentPrice, $priceYear) . ". ";

    if ($yearHigh && $yearHigh != 0) {
        $distanceFromHigh = (($currentPrice - $yearHigh) / $yearHigh) * 100;
        $sign = $distanceFromHigh >= 0 ? "מעל" : "מתחת";
        echo "המחיר הנוכחי $sign לשיא השנתי ב־" . number_format(abs($distanceFromHigh), 2) . "%.";
    }
} else {
    echo "המידע על הביטקוין אינו זמין כעת.";
}
?>
