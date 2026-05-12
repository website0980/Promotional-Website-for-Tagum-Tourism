<?php
// Admin Module Configuration & Session Management (UPDATED for restaurants)

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'tagum2026');

define('DESTINATIONS_FILE', dirname(__DIR__) . '/assets/data/destinations.json');
define('EXPERIENCES_FILE', dirname(__DIR__) . '/assets/data/experiences.json');
define('CUISINE_FILE', dirname(__DIR__) . '/assets/data/cuisine.json');
define('CULTURAL_SITES_FILE', dirname(__DIR__) . '/assets/data/cultural-sites.json');
define('FESTIVALS_FILE', dirname(__DIR__) . '/assets/data/festivals.json');

define('IMAGES_DIR', dirname(__DIR__) . '/assets/images/destinations/');
define('IMAGES_URL', '../../assets/images/destinations/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

define('EXPERIENCES_IMAGES_DIR', dirname(__DIR__) . '/assets/images/experiences/');
define('EXPERIENCES_IMAGES_URL', '../../assets/images/experiences/');
define('CUISINE_IMAGES_DIR', dirname(__DIR__) . '/assets/images/cuisine/');
define('CUISINE_IMAGES_URL', '../../assets/images/cuisine/');
define('CULTURAL_SITES_IMAGES_DIR', dirname(__DIR__) . '/assets/images/cultural-sites/');
define('CULTURAL_SITES_IMAGES_URL', '../../assets/images/cultural-sites/');
define('FESTIVALS_IMAGES_DIR', dirname(__DIR__) . '/assets/images/festivals/');
define('FESTIVALS_IMAGES_URL', '../../assets/images/festivals/');

define('HOTEL_IMAGES_DIR', dirname(__DIR__) . '/assets/images/hotels/');
define('HOTEL_IMAGES_URL', '../../assets/images/hotels/');

// NEW: Restaurant images (reuse hotel logic)
define('RESTAURANT_IMAGES_DIR', dirname(__DIR__) . '/assets/images/restaurants/');
define('RESTAURANT_IMAGES_URL', '../../assets/images/restaurants/');

function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function loadDestinations() {
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return [];
    try {
        $db = new SQLite3($dbFile);
        $query = "SELECT * FROM destinations";
        $result = $db->query($query);
        $destinations = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) $destinations[] = $row;
        $db->close();
        return $destinations;
    } catch (Exception $e) { return []; }
}

function loadExperiences() {
    $file = dirname(__DIR__) . '/assets/data/experiences.json';
    if (!file_exists($file)) return [];
    $json = file_get_contents($file);
    $data = json_decode($json, true) ?: [];
    return is_array($data) ? $data : [];
}

function loadCuisine() {
    $file = dirname(__DIR__) . '/assets/data/cuisine.json';
    if (!file_exists($file)) return [];
    $json = file_get_contents($file);
    $data = json_decode($json, true) ?: [];
    return is_array($data) ? $data : [];
}

function loadCulturalSites() {
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return [];
    try {
        $db = new SQLite3($dbFile);
        $query = "SELECT * FROM cultural_sites";
        $result = $db->query($query);
        $culturalSites = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) $culturalSites[] = $row;
        $db->close();
        return $culturalSites;
    } catch (Exception $e) { return []; }
}

function loadFestivals() {
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return [];
    try {
        $db = new SQLite3($dbFile);
        $query = "SELECT * FROM festivals";
        $result = $db->query($query);
        $festivals = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) $festivals[] = $row;
        $db->close();
        return $festivals;
    } catch (Exception $e) { return []; }
}

// HOTEL FUNCTIONS
function loadHotels() {
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return [];
    try {
        $db = new SQLite3($dbFile);
        $query = "SELECT * FROM hotel_items ORDER BY id";
        $result = $db->query($query);
        $hotels = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) $hotels[] = $row;
        $db->close();
        return $hotels;
    } catch (Exception $e) { return []; }
}

// RESTAURANT FUNCTIONS
function loadRestaurants() {
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return [];
    try {
        $db = new SQLite3($dbFile);
        $query = "SELECT * FROM restaurant_items ORDER BY id";
        $result = $db->query($query);
        $restaurants = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) $restaurants[] = $row;
        $db->close();
        return $restaurants;
    } catch (Exception $e) { return []; }
}

function validateImageUpload($file) {
    $errors = [];
    if ($file['error'] !== UPLOAD_ERR_OK) $errors[] = 'Upload error';
    if ($file['size'] > MAX_FILE_SIZE) $errors[] = 'File too large';
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS)) $errors[] = 'Invalid file type';
    return ['success' => empty($errors), 'error' => implode(', ', $errors)];
}

function saveHotelImage($file) {
    $validation = validateImageUpload($file);
    if (!$validation['success']) return $validation;
    if (!is_dir(HOTEL_IMAGES_DIR)) mkdir(HOTEL_IMAGES_DIR, 0755, true);
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fileName = 'hotel_' . time() . '_' . uniqid() . '.' . $fileExt;
    $filePath = HOTEL_IMAGES_DIR . $fileName;
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return ['success' => true, 'fileName' => $fileName, 'path' => HOTEL_IMAGES_URL . $fileName];
    }
    return ['success' => false, 'error' => 'Failed to save image'];
}

function deleteHotelImage($pathOrFileName) {
    $fileName = basename($pathOrFileName);
    if (empty($fileName)) return false;
    $filePath = HOTEL_IMAGES_DIR . $fileName;
    if (file_exists($filePath)) unlink($filePath);
    return !file_exists($filePath);
}

// NEW: Restaurant specific image functions
function saveRestaurantImage($file) {
    $validation = validateImageUpload($file);
    if (!$validation['success']) return $validation;
    if (!is_dir(RESTAURANT_IMAGES_DIR)) mkdir(RESTAURANT_IMAGES_DIR, 0755, true);
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fileName = 'restaurant_' . time() . '_' . uniqid() . '.' . $fileExt;
    $filePath = RESTAURANT_IMAGES_DIR . $fileName;
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return ['success' => true, 'fileName' => $fileName, 'path' => RESTAURANT_IMAGES_URL . $fileName];
    }
    return ['success' => false, 'error' => 'Failed to save image'];
}

function deleteRestaurantImage($pathOrFileName) {
    $fileName = basename($pathOrFileName);
    if (empty($fileName)) return false;
    $filePath = RESTAURANT_IMAGES_DIR . $fileName;
    if (file_exists($filePath)) unlink($filePath);
    return !file_exists($filePath);
}

// Alias for backward compatibility
function saveRestaurantsImage($file) {
    return saveRestaurantImage($file);
}

function deleteRestaurantsImage($pathOrFileName) {
    return deleteRestaurantImage($pathOrFileName);
}

function logout() {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
