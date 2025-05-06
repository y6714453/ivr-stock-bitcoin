<?php
function getApiData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function findClosestPriceBefore($timestamps, $prices, $targetTimestamp) {
    $bestPrice = null;
    for ($i = count($timestamps) - 1; $i >= 0; $i--) {
        if ($timestamps[$i] <= $targetTimestamp && $prices[$i] !== null) {
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
    $changeText = str_replace(".", " נקודה ", number_format(abs($change), 2));
    return "$sign של $changeText אחוז";
}

function spellOutPrice($price) {
    $price = round($price);
    $thousands = floor($price / 1000);
    $remainder = $price % 1000;

    $text = '';
    if ($thousands > 0) {
        $text .= number_format($thousands, 0) . " אלף";
    }
    if ($remainder > 0) {
        if ($thousands > 0) {
            $text .= " ו ";
        }
        $text .= number_format($remainder, 0);
    }
    return $text;
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
    $startOfWeek = strtotime("last sunday", $now);
    $startOfYear = strtotime(date("Y-01-01", $now));

    $priceDay = findClosestPriceBefore($timestamps, $prices, $startOfDay);
    $priceWeek = findClosestPriceBefore($timestamps, $prices, $startOfWeek);
    $priceYear = findClosestPriceBefore($timestamps, $prices, $startOfYear);
    $yearHigh = $data['chart']['result'][0]['meta']['fiftyTwoWeekHigh'];

    $priceText = spellOutPrice($currentPrice);

    echo "הביטקויין עומד כעת על: $priceText דולר. ";
    echo "מאז פתיחת היום נרשמה " . formatChange($currentPrice, $priceDay) . ". ";
    echo "מתחילת השבוע נרשמה " . formatChange($currentPrice, $priceWeek) . ". ";
    echo "מתחילת השנה נרשמה " . formatChange($currentPrice, $priceYear) . ". ";

    if ($yearHigh && $yearHigh != 0) {
        $distance = (($currentPrice - $yearHigh) / $yearHigh) * 100;
        $distanceText = str_replace(".", " נקודה ", number_format(abs($distance), 2));
        echo "המחיר הנוכחי במרחק $distanceText אחוז מהשיא השנתי.";
    }
} else {
    echo "המידע על הביטקוין אינו זמין כעת.";
}
?>
