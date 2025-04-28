<?php
$url = "https://api.binance.com/api/v3/ping"; // זה URL פשוט שבודק אם binance חי

$response = @file_get_contents($url);

if ($response !== false) {
    echo "יש חיבור החוצה ל-API!";
} else {
    echo "אין חיבור החוצה ל-API.";
}
?>
