<?php
// router.php

// אם הקובץ המבוקש קיים – תן לשרת להציג אותו ישירות
if (file_exists(__DIR__ . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH))) {
    return false;
} else {
    // אחרת תמיד טען את index.php
    require_once __DIR__ . '/index.php';
}
