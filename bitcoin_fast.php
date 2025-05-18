<?php
function getApiData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: Mozilla/5.0']);
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

function formatNumberWithDotText($number) {
    $parts = explode(".", number_format($number, 2, ".", ""));
    if (count($parts) == 2) {
        return $parts[0] . " נקודה " . ltrim($parts[1], "0");
    } else {
        return $parts[0];
    }
}

function formatChange($current, $previous) {
    if ($previous === null || $previous == 0) return "אין נתון זמין";
    $change = (($current - $previous) / $previous) * 100;
    $sign = $change > 0 ? "עלייה" : ($change < 0 ? "ירידה" : "שינוי אפסי");
    $absChange = formatNumberWithDotText(abs($change));
    return "$sign של $absChange אחוז";
}

function spellOutPrice($price) {
    $price = round($price);
    $thousands = floor($price / 1000);
    $remainder = $price % 1000;
    $text = '';
    if ($thousands > 0) {
        if ($thousands == 1) $text .= "1000";
        elseif ($thousands == 2) $text .= "2000";
        else $text .= $thousands * 1000;
    }
    if ($remainder > 0) {
        if ($thousands > 0) $text .= " ו ";
        $text .= $remainder;
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
    $priceDay = findClosestPriceBefore($timestamps, $prices, $startOfDay);
    $yearHigh = $data['chart']['result'][0]['meta']['fiftyTwoWeekHigh'];
    $priceText = spellOutPrice($currentPrice);

    echo "הביטקוין עומד כעת על $priceText דולר.";
    echo " מאז פתיחת היום נרשמה " . formatChange($currentPrice, $priceDay) . ".";
    
    if ($yearHigh && $yearHigh != 0) {
        $distance = (($currentPrice - $yearHigh) / $yearHigh) * 100;
        $absDistText = formatNumberWithDotText(abs($distance));
        echo " המחיר הנוכחי רחוק מהשיא ב $absDistText אחוז.";
    }
} else {
    echo "המידע על הביטקוין אינו זמין כעת.";
}
?>
