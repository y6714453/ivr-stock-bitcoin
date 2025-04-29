<?php
// מפתח API מ-Financial Modeling Prep
$apiKey = 'crp_841rB5X9FT197O7_0OPLewqQ80Il';

// קובץ מטמון לשמירת המידע
$cacheFile = 'sp500_cache.json';
$cacheTime = 12; // שניות (לעדכון מותר)

// אם יש קובץ מטמון והוא עדכני - נטען ממנו
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    $data = json_decode(file_get_contents($cacheFile), true);
} else {
    // אין קובץ עדכני - נבקש מידע חדש מה-API
    $url = "https://financialmodelingprep.com/api/v3/quote/%5EGSPC?apikey=$apiKey";
    $response = @file_get_contents($url);

    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data[0]['price'])) {
            file_put_contents($cacheFile, json_encode($data)); // שומר במטמון
        } else {
            $data = null;
        }
    } else {
        $data = null;
    }
}

// הצגת התוצאה
if ($data && isset($data[0]['price'])) {
    $price = round($data[0]['price']);
    echo "מדד S&P 500 עומד כעת על $price דולר.";
} else {
    echo "המידע על מדד S&P 500 אינו זמין כרגע, נסו שוב מאוחר יותר.";
}
?>
