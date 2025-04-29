<?php
function fetchNewSp500Data() {
    $apiUrl = 'https://financialmodelingprep.com/api/v3/quote/%5EGSPC?apikey=demo'; // אתה תחליף את "demo" ל-API KEY שלך

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data[0]['price'])) {
            return $data[0]['price'];
        }
    }
    return false;
}

// מיקום קובץ הקאש
$cacheFile = 'sp500_cache.txt';

// האם הקובץ קיים ועדכני? (12 שניות)
if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 12)) {
    $price = file_get_contents($cacheFile);
} else {
    $price = fetchNewSp500Data();
    if ($price !== false) {
        file_put_contents($cacheFile, $price);
    } else {
        $price = file_exists($cacheFile) ? file_get_contents($cacheFile) : false;
    }
}

if ($price !== false) {
    $priceFormatted = number_format((float)$price, 0);
    echo "מדד S&P 500 עומד כעת על {$priceFormatted} דולר.";
} else {
    echo "המידע על מדד S&P 500 אינו זמין כרגע, נסו שוב מאוחר יותר.";
}
?>
