<?php
// Admin Module Configuration & Session Management (HARDENED)

// Secure session initialization
function initSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_samesite', 'Strict');
        session_start();
    }
}
initSecureSession();

// Hashed admin credentials (prevents plaintext leakage)
define('ADMIN_USERNAME', 'PromotionalAdmin');
define('ADMIN_PASSWORD_HASH', '$2y$12$7AwD6yF6sJjeSAoexv3xveoehQUipErVu0/oxycCfHox1616eJXzO'); // default: Tagum2026

// Session timeout: 30 minutes of inactivity
define('SESSION_TIMEOUT', 1800);

define('DESTINATIONS_FILE', dirname(__DIR__) . '/assets/data/destinations.json');
define('EXPERIENCES_FILE', dirname(__DIR__) . '/assets/data/experiences.json');
define('CUISINE_FILE', dirname(__DIR__) . '/assets/data/cuisine.json');
define('CULTURAL_SITES_FILE', dirname(__DIR__) . '/assets/data/cultural-sites.json');
define('FESTIVALS_FILE', dirname(__DIR__) . '/assets/data/festivals.json');

define('IMAGES_DIR', dirname(__DIR__) . '/images/destinations/');
define('IMAGES_URL', '../../images/destinations/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

define('EXPERIENCES_IMAGES_DIR', dirname(__DIR__) . '/assets/images/experiences/');
define('EXPERIENCES_IMAGES_URL', '../../assets/images/experiences/');
define('CUISINE_IMAGES_DIR', dirname(__DIR__) . '/assets/images/cuisine/');
define('CUISINE_IMAGES_URL', '../../assets/images/cuisine/');
define('CULTURAL_SITES_IMAGES_DIR', dirname(__DIR__) . '/images/events/');
define('CULTURAL_SITES_IMAGES_URL', '../../images/events/');
define('FESTIVALS_IMAGES_DIR', dirname(__DIR__) . '/images/festivals/');
define('FESTIVALS_IMAGES_URL', '../../images/festivals/');

define('HOTEL_IMAGES_DIR', dirname(__DIR__) . '/assets/images/hotels/');
define('HOTEL_IMAGES_URL', '../../assets/images/hotels/');

// NEW: Restaurant images (reuse hotel logic)
define('RESTAURANT_IMAGES_DIR', dirname(__DIR__) . '/images/restaurants/');
define('RESTAURANT_IMAGES_URL', '../../images/restaurants/');

define('CAROUSEL_IMAGES_DIR', dirname(__DIR__) . '/images/carousel/');
define('CAROUSEL_IMAGES_URL', 'images/carousel/');

function isLoggedIn() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        return false;
    }
    // Check session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        logout(false);
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}

function requireAuth() {
    if (!isLoggedIn()) {
        // Destroy any partial session data before redirect
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: login.php');
        exit();
    }
}

// CSRF Token helpers
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCsrfToken()) . '">';
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
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return [];
    try {
        $db = new SQLite3($dbFile);
        $query = "SELECT * FROM experiences ORDER BY id DESC";
        $result = $db->query($query);
        $experiences = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) $experiences[] = $row;
        $db->close();
        return $experiences;
    } catch (Exception $e) { return []; }
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
        require_once dirname(__DIR__) . '/includes/events_helpers.php';
        $db = new SQLite3($dbFile);
        ensureEventDateColumn($db);
        $query = "SELECT * FROM events ORDER BY CASE WHEN event_date IS NULL OR event_date = '' THEN 1 ELSE 0 END, event_date ASC, name ASC";
        $result = $db->query($query);
        $culturalSites = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) $culturalSites[] = $row;
        $db->close();
        return $culturalSites;
    } catch (Exception $e) { return []; }
}

function ensureFestivalRelatedEventColumn($db = null) {
    $closeDb = false;
    if ($db === null) {
        $dbFile = dirname(__DIR__) . '/database.db';
        if (!file_exists($dbFile)) {
            return false;
        }
        $db = new SQLite3($dbFile);
        $closeDb = true;
    }

    $columns = [];
    $result = $db->query('PRAGMA table_info(festivals)');
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $columns[] = $row['name'];
    }

    if (!in_array('related_event_id', $columns, true)) {
        $db->exec('ALTER TABLE festivals ADD COLUMN related_event_id INTEGER');
    }

    if (!in_array('related_event_name', $columns, true)) {
        $db->exec('ALTER TABLE festivals ADD COLUMN related_event_name TEXT');
    }

    if ($closeDb) {
        $db->close();
    }
    return true;
}

function loadFestivals() {
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return [];
    try {
        $db = new SQLite3($dbFile);
        ensureFestivalRelatedEventColumn($db);
        $query = "SELECT * FROM festivals ORDER BY id";
        $result = $db->query($query);
        $festivals = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) $festivals[] = $row;
        $db->close();
        return $festivals;
    } catch (Exception $e) { return []; }
}

function saveFestivals(array $festivals) {
    $dbFile = dirname(__DIR__) . '/database.db';
    if (!file_exists($dbFile)) return false;

    try {
        $db = new SQLite3($dbFile);
        ensureFestivalRelatedEventColumn($db);
        $db->exec('DELETE FROM festivals');

        $order = 1;
        foreach ($festivals as $festival) {
            $id = isset($festival['id']) && is_numeric($festival['id']) ? (int) $festival['id'] : $order;
            $name = $db->escapeString((string) ($festival['name'] ?? ''));
            $description = $db->escapeString((string) ($festival['description'] ?? ''));
            $image = $db->escapeString((string) ($festival['image'] ?? ''));
            $date = $db->escapeString((string) ($festival['date'] ?? ''));
            $highlights = $db->escapeString((string) ($festival['highlights'] ?? ''));
            $activities = $db->escapeString((string) ($festival['activities'] ?? ''));
            $relatedEventId = '';
            if (!empty($festival['related_event_id'])) {
                $relatedEventId = (int) $festival['related_event_id'];
            }
            $relatedEventName = $db->escapeString((string) ($festival['related_event_name'] ?? ''));

            $stmt = $db->prepare('INSERT INTO festivals (id, name, description, image, date, highlights, activities, related_event_id, related_event_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bindValue(1, $id, SQLITE3_INTEGER);
            $stmt->bindValue(2, $name, SQLITE3_TEXT);
            $stmt->bindValue(3, $description, SQLITE3_TEXT);
            $stmt->bindValue(4, $image, SQLITE3_TEXT);
            $stmt->bindValue(5, $date, SQLITE3_TEXT);
            $stmt->bindValue(6, $highlights, SQLITE3_TEXT);
            $stmt->bindValue(7, $activities, SQLITE3_TEXT);
            if ($relatedEventId !== '') {
                $stmt->bindValue(8, $relatedEventId, SQLITE3_INTEGER);
            } else {
                $stmt->bindValue(8, null, SQLITE3_NULL);
            }
            $stmt->bindValue(9, $relatedEventName, SQLITE3_TEXT);
            $stmt->execute();
            $order++;
        }

        $db->close();
        return true;
    } catch (Exception $e) {
        return false;
    }
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

function saveUploadedImage($file) {
    $validation = validateImageUpload($file);
    if (!$validation['success']) return $validation;
    if (!is_dir(IMAGES_DIR)) mkdir(IMAGES_DIR, 0755, true);
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fileName = 'dest_' . time() . '_' . uniqid() . '.' . $fileExt;
    $filePath = IMAGES_DIR . $fileName;
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return ['success' => true, 'fileName' => $fileName, 'path' => IMAGES_URL . $fileName];
    }
    return ['success' => false, 'error' => 'Failed to save image'];
}

function deleteImage($pathOrFileName) {
    $fileName = basename($pathOrFileName);
    if (empty($fileName)) return false;
    $filePath = IMAGES_DIR . $fileName;
    if (file_exists($filePath)) unlink($filePath);
    return !file_exists($filePath);
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

function saveCulturalSiteImage($file) {
    $validation = validateImageUpload($file);
    if (!$validation['success']) return $validation;
    if (!is_dir(CULTURAL_SITES_IMAGES_DIR)) mkdir(CULTURAL_SITES_IMAGES_DIR, 0755, true);
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fileName = 'event_' . time() . '_' . uniqid() . '.' . $fileExt;
    $filePath = CULTURAL_SITES_IMAGES_DIR . $fileName;
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return ['success' => true, 'fileName' => $fileName, 'path' => CULTURAL_SITES_IMAGES_URL . $fileName];
    }
    return ['success' => false, 'error' => 'Failed to save image'];
}

function deleteCulturalSiteImage($pathOrFileName) {
    $fileName = basename($pathOrFileName);
    if (empty($fileName)) return false;
    $filePath = CULTURAL_SITES_IMAGES_DIR . $fileName;
    if (file_exists($filePath)) unlink($filePath);
    return !file_exists($filePath);
}

// DB CRUD functions for hotels and restaurants
function deleteHotel($id) {
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return false;
    try {
        $db = new SQLite3($dbFile);
        $stmt = $db->prepare('SELECT image FROM hotel_items WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        if ($row && !empty($row['image'])) {
            deleteHotelImage($row['image']);
        }
        $stmt = $db->prepare('DELETE FROM hotel_items WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $stmt->execute();
        $db->close();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function toggleHotelFeatured($id) {
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return false;
    try {
        $db = new SQLite3($dbFile);
        $stmt = $db->prepare('UPDATE hotel_items SET featured = NOT COALESCE(featured, 0) WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $stmt->execute();
        $db->close();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function deleteRestaurant($id) {
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return false;
    try {
        $db = new SQLite3($dbFile);
        $stmt = $db->prepare('SELECT image FROM restaurant_items WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        if ($row && !empty($row['image'])) {
            deleteRestaurantImage($row['image']);
        }
        $stmt = $db->prepare('DELETE FROM restaurant_items WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $stmt->execute();
        $db->close();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function toggleRestaurantFeatured($id) {
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return false;
    try {
        $db = new SQLite3($dbFile);
        $stmt = $db->prepare('UPDATE restaurant_items SET featured = NOT COALESCE(featured, 0) WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $stmt->execute();
        $db->close();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function saveDestination($data, $id = null) {
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return false;
    try {
        $db = new SQLite3($dbFile);
        if ($id === null) {
            // INSERT new
            $stmt = $db->prepare('INSERT INTO destinations (name, description, location, accessibility, features, facilities, entrance_fee, best_time, what_to_pack, visiting_rules, image, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bindValue(1, $data['name'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(2, $data['description'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(3, $data['location'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(4, $data['accessibility'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(5, $data['features'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(6, $data['facilities'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(7, $data['entrance_fee'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(8, $data['best_time'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(9, $data['what_to_pack'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(10, $data['visiting_rules'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(11, $data['image'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(12, isset($data['featured']) ? (int)$data['featured'] : 0, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $newId = $db->lastInsertRowID();
            $db->close();
            return $newId;
        } else {
            // UPDATE existing
            $stmt = $db->prepare('UPDATE destinations SET name=?, description=?, location=?, accessibility=?, features=?, facilities=?, entrance_fee=?, best_time=?, what_to_pack=?, visiting_rules=?, image=?, featured=? WHERE id=?');
            $stmt->bindValue(1, $data['name'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(2, $data['description'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(3, $data['location'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(4, $data['accessibility'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(5, $data['features'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(6, $data['facilities'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(7, $data['entrance_fee'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(8, $data['best_time'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(9, $data['what_to_pack'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(10, $data['visiting_rules'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(11, $data['image'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(12, isset($data['featured']) ? (int)$data['featured'] : 0, SQLITE3_INTEGER);
            $stmt->bindValue(13, $id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $affected = $db->changes();
            $db->close();
            return $affected > 0;
        }
    } catch (Exception $e) {
        return false;
    }
}

function toggleDestinationFeatured($id) {
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return false;
    try {
        $db = new SQLite3($dbFile);
        $stmt = $db->prepare('UPDATE destinations SET featured = NOT COALESCE(featured, 0) WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $stmt->execute();
        $db->close();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function deleteDestination($id) {
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return false;
    try {
        $db = new SQLite3($dbFile);
        // Get image first
        $stmt = $db->prepare('SELECT image FROM destinations WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        if ($row && !empty($row['image'])) {
            deleteImage($row['image']);
        }
        // Delete record
        $stmt = $db->prepare('DELETE FROM destinations WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $stmt->execute();
        $db->close();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Deprecated full wipe function (logs warning)
function saveDestinations($destinations) {
    error_log('saveDestinations() deprecated - use saveDestination() instead');
    return false;
}

function saveExperiences($experiences) {
    $file = EXPERIENCES_FILE;
    $json = json_encode($experiences, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if (file_put_contents($file, $json) !== false) {
        return true;
    }
    return false;
}

// CAROUSEL FUNCTIONS
function ensureCarouselTable() {
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return false;
    try {
        $db = new SQLite3($dbFile);
        $schema = file_get_contents(dirname(__DIR__) . '/database/carousel_schema.sql');
        if ($schema) {
            $db->exec($schema);
        }
        $count = (int) $db->querySingle('SELECT COUNT(*) FROM carousel_slides');
        if ($count === 0) {
            $defaults = [
                ['Tagumeños: Beauty that Shines from Within.', "Discover\nNatural Beauty", 'Tagumeños are a reflection of true natural beauty radiating warmth, kindness, and genuine smiles that make everyone feel welcome.', 'images/Background for slide 1.jpg', 1],
                ['Cultural heritage meets modern charm', "Experience\nLocal Culture", 'Immerse yourself in the vibrant traditions, local cuisine, and warm hospitality of Tagum City. Discover authentic experiences that celebrate our rich heritage.', 'images/Background for slide 2 .jpg', 2],
                ['Tagum Adventures: Feel the Thrill, Live the Moment', "Thrilling\nAdventures", 'Step into the excitement that awaits in Tagum where every journey is filled with adrenaline, discovery, and unforgettable moments. From outdoor explorations to vibrant city experiences, adventure is always just around the corner.', 'images/Background for slide 3.jpg', 3],
            ];
            $stmt = $db->prepare('INSERT INTO carousel_slides (tagline, title, description, image, sort_order, active) VALUES (?, ?, ?, ?, ?, 1)');
            foreach ($defaults as $row) {
                $stmt->bindValue(1, $row[0], SQLITE3_TEXT);
                $stmt->bindValue(2, $row[1], SQLITE3_TEXT);
                $stmt->bindValue(3, $row[2], SQLITE3_TEXT);
                $stmt->bindValue(4, $row[3], SQLITE3_TEXT);
                $stmt->bindValue(5, $row[4], SQLITE3_INTEGER);
                $stmt->execute();
            }
        }
        $db->close();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function loadCarouselSlides($activeOnly = false) {
    ensureCarouselTable();
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return [];
    try {
        $db = new SQLite3($dbFile);
        $query = $activeOnly
            ? 'SELECT * FROM carousel_slides WHERE active = 1 ORDER BY sort_order ASC, id ASC'
            : 'SELECT * FROM carousel_slides ORDER BY sort_order ASC, id ASC';
        $result = $db->query($query);
        $slides = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $slides[] = $row;
        }
        $db->close();
        return $slides;
    } catch (Exception $e) {
        return [];
    }
}

function getCarouselSlideById($id) {
    ensureCarouselTable();
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return null;
    try {
        $db = new SQLite3($dbFile);
        $stmt = $db->prepare('SELECT * FROM carousel_slides WHERE id = ?');
        $stmt->bindValue(1, (int)$id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        $db->close();
        return $row ?: null;
    } catch (Exception $e) {
        return null;
    }
}

function saveCarouselImage($file) {
    $validation = validateImageUpload($file);
    if (!$validation['success']) return $validation;
    if (!is_dir(CAROUSEL_IMAGES_DIR)) mkdir(CAROUSEL_IMAGES_DIR, 0755, true);
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fileName = 'carousel_' . time() . '_' . uniqid() . '.' . $fileExt;
    $filePath = CAROUSEL_IMAGES_DIR . $fileName;
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return ['success' => true, 'fileName' => $fileName, 'path' => CAROUSEL_IMAGES_URL . $fileName];
    }
    return ['success' => false, 'error' => 'Failed to save image'];
}

function deleteCarouselImage($pathOrFileName) {
    $fileName = basename($pathOrFileName);
    if (empty($fileName)) return false;
    $filePath = CAROUSEL_IMAGES_DIR . $fileName;
    if (file_exists($filePath)) unlink($filePath);
    return !file_exists($filePath);
}

function saveCarouselSlide($data, $id = null) {
    ensureCarouselTable();
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return false;
    try {
        $db = new SQLite3($dbFile);
        if ($id === null) {
            $stmt = $db->prepare('INSERT INTO carousel_slides (tagline, title, description, image, btn_primary_text, btn_primary_link, btn_secondary_text, btn_secondary_link, sort_order, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bindValue(1, $data['tagline'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(2, $data['title'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(3, $data['description'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(4, $data['image'] ?? '', SQLITE3_TEXT);
            $stmt->bindValue(5, $data['btn_primary_text'] ?? 'Explore Now', SQLITE3_TEXT);
            $stmt->bindValue(6, $data['btn_primary_link'] ?? '#plan', SQLITE3_TEXT);
            $stmt->bindValue(7, $data['btn_secondary_text'] ?? 'Learn More', SQLITE3_TEXT);
            $stmt->bindValue(8, $data['btn_secondary_link'] ?? '#explore', SQLITE3_TEXT);
            $stmt->bindValue(9, isset($data['sort_order']) ? (int)$data['sort_order'] : 0, SQLITE3_INTEGER);
            $stmt->bindValue(10, isset($data['active']) ? (int)$data['active'] : 1, SQLITE3_INTEGER);
            $stmt->execute();
            $newId = $db->lastInsertRowID();
            $db->close();
            return $newId;
        }
        $stmt = $db->prepare('UPDATE carousel_slides SET tagline=?, title=?, description=?, image=?, btn_primary_text=?, btn_primary_link=?, btn_secondary_text=?, btn_secondary_link=?, sort_order=?, active=? WHERE id=?');
        $stmt->bindValue(1, $data['tagline'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(2, $data['title'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(3, $data['description'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(4, $data['image'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(5, $data['btn_primary_text'] ?? 'Explore Now', SQLITE3_TEXT);
        $stmt->bindValue(6, $data['btn_primary_link'] ?? '#plan', SQLITE3_TEXT);
        $stmt->bindValue(7, $data['btn_secondary_text'] ?? 'Learn More', SQLITE3_TEXT);
        $stmt->bindValue(8, $data['btn_secondary_link'] ?? '#explore', SQLITE3_TEXT);
        $stmt->bindValue(9, isset($data['sort_order']) ? (int)$data['sort_order'] : 0, SQLITE3_INTEGER);
        $stmt->bindValue(10, isset($data['active']) ? (int)$data['active'] : 1, SQLITE3_INTEGER);
        $stmt->bindValue(11, (int)$id, SQLITE3_INTEGER);
        $stmt->execute();
        $affected = $db->changes();
        $db->close();
        return $affected > 0;
    } catch (Exception $e) {
        return false;
    }
}

function deleteCarouselSlide($id) {
    ensureCarouselTable();
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return false;
    try {
        $db = new SQLite3($dbFile);
        $stmt = $db->prepare('SELECT image FROM carousel_slides WHERE id = ?');
        $stmt->bindValue(1, (int)$id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        if ($row && !empty($row['image']) && strpos($row['image'], 'images/carousel/') !== false) {
            deleteCarouselImage($row['image']);
        }
        $stmt = $db->prepare('DELETE FROM carousel_slides WHERE id = ?');
        $stmt->bindValue(1, (int)$id, SQLITE3_INTEGER);
        $stmt->execute();
        $db->close();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function toggleCarouselSlideActive($id) {
    ensureCarouselTable();
    $dbFile = '../database.db';
    if (!file_exists($dbFile)) return false;
    try {
        $db = new SQLite3($dbFile);
        $stmt = $db->prepare('UPDATE carousel_slides SET active = NOT COALESCE(active, 0) WHERE id = ?');
        $stmt->bindValue(1, (int)$id, SQLITE3_INTEGER);
        $stmt->execute();
        $db->close();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function logout($redirect = true) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']);
    }
    session_destroy();
    if ($redirect) {
        header('Location: login.php');
        exit();
    }
}
?>


