<?php
$dbFile = '../database.db';
$restaurant = null;
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (file_exists($dbFile) && $id > 0) {
    try {
        $db = new SQLite3($dbFile);
        $stmt = $db->prepare('SELECT * FROM restaurant_items WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        if ($row) {
            $restaurant = $row;
        }
        $db->close();
    } catch (Exception $e) {
        // Keep fallback state and show not-found UI.
    }
}

$pageTitle = $restaurant ? ($restaurant['name'] . ' - Tagum City') : 'Restaurant Not Found - Tagum City';

$lat = null;
$lng = null;
$mapEmbedUrl = '';
$openMapUrl = '';
$emailLink = '';
$telLink = '';

if ($restaurant) {
    if (isset($restaurant['latitude']) && isset($restaurant['longitude']) && is_numeric($restaurant['latitude']) && is_numeric($restaurant['longitude'])) {
        $lat = (float) $restaurant['latitude'];
        $lng = (float) $restaurant['longitude'];

        $delta = 0.01;
        $left = $lng - $delta;
        $right = $lng + $delta;
        $top = $lat + $delta;
        $bottom = $lat - $delta;

        $mapEmbedUrl = 'https://www.openstreetmap.org/export/embed.html?bbox='
            . rawurlencode($left . ',' . $bottom . ',' . $right . ',' . $top)
            . '&layer=mapnik&marker=' . rawurlencode($lat . ',' . $lng);
        $openMapUrl = 'https://www.openstreetmap.org/?mlat=' . rawurlencode((string) $lat)
            . '&mlon=' . rawurlencode((string) $lng) . '#map=15/' . rawurlencode((string) $lat) . '/' . rawurlencode((string) $lng);
    } elseif (!empty($restaurant['location'])) {
        $query = rawurlencode($restaurant['location'] . ', Tagum City');
        $openMapUrl = 'https://www.openstreetmap.org/search?query=' . $query;
    }

    if (!empty($restaurant['email'])) {
        $emailLink = 'mailto:' . rawurlencode(trim($restaurant['email']));
    }

    if (!empty($restaurant['contact'])) {
        $digitsOnly = preg_replace('/[^0-9+]/', '', $restaurant['contact']);
        if (!empty($digitsOnly)) {
            $telLink = 'tel:' . $digitsOnly;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/experience-details.css">
    <style>
        .restaurant-detail-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 1.25rem;
            margin: 1.5rem 0;
        }

        .restaurant-panel {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem 1.1rem;
        }

        .restaurant-panel h3 {
            margin: 0 0 0.75rem;
            color: #1d5a3d;
        }

        .restaurant-description {
            color: #374151;
            line-height: 1.7;
            font-size: 1rem;
        }

        .restaurant-meta-list {
            display: grid;
            gap: 0.6rem;
            color: #374151;
            font-size: 0.98rem;
        }

        .restaurant-meta-list strong {
            color: #111827;
        }

        .restaurant-meta-list a {
            color: #1d5a3d;
            font-weight: 600;
            text-decoration: none;
        }

        .restaurant-meta-list a:hover {
            text-decoration: underline;
        }

        .restaurant-map-wrap {
            margin-top: 1.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
        }

        .restaurant-map-wrap iframe {
            width: 100%;
            height: 320px;
            border: 0;
            display: block;
        }

        .restaurant-map-fallback {
            padding: 1rem 1.1rem;
            color: #374151;
            line-height: 1.6;
        }

        .restaurant-map-actions {
            padding: 0.8rem 1.1rem 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .restaurant-map-actions a {
            color: #1d5a3d;
            font-weight: 600;
            text-decoration: none;
        }

        .restaurant-map-actions a:hover {
            text-decoration: underline;
        }

        @media (max-width: 900px) {
            .restaurant-detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>

    <main class="experience-single">
        <div class="container">
            <?php if ($restaurant): ?>
                <article class="experience-detail active">
                    <header class="experience-header">
                        <h1><?php echo htmlspecialchars($restaurant['name']); ?></h1>
                        <div class="experience-meta">
                            <span class="exp-type"><?php echo htmlspecialchars($restaurant['category'] ?? 'Restaurant'); ?></span>
                        </div>
                    </header>

                    <?php if (!empty($restaurant['image'])): ?>
                        <div class="experience-image">
                            <img src="<?php echo htmlspecialchars($restaurant['image']); ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?>">
                        </div>
                    <?php endif; ?>

                    <div class="restaurant-detail-grid">
                        <div class="restaurant-panel">
                            <h3>Description</h3>
                            <?php
                                $descriptionText = trim($restaurant['description'] ?? '');
                                if ($descriptionText === '') {
                                    $descriptionText = 'No description available.';
                                }
                            ?>
                            <p class="restaurant-description"><?php echo nl2br(htmlspecialchars($descriptionText)); ?></p>
                            <?php if (!empty($restaurant['information'])): ?>
                                <h3 style="margin-top: 1rem;">Information</h3>
                                <p class="restaurant-description"><?php echo nl2br(htmlspecialchars($restaurant['information'])); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="restaurant-panel">
                            <h3>Restaurant Details</h3>
                            <div class="restaurant-meta-list">
                                <?php if (!empty($restaurant['category'])): ?>
                                    <div><strong>Category:</strong> <?php echo htmlspecialchars($restaurant['category']); ?></div>
                                <?php endif; ?>
                                <div><strong>Location:</strong> <?php echo htmlspecialchars($restaurant['location'] ?? 'Location not available'); ?></div>
                                <?php if (!empty($restaurant['contact'])): ?>
                                    <div>
                                        <strong>Contact:</strong>
                                        <?php if (!empty($telLink)): ?>
                                            <a href="<?php echo htmlspecialchars($telLink); ?>"><?php echo htmlspecialchars($restaurant['contact']); ?></a>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($restaurant['contact']); ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($restaurant['email'])): ?>
                                    <div>
                                        <strong>Email:</strong>
                                        <?php if (!empty($emailLink)): ?>
                                            <a href="<?php echo htmlspecialchars($emailLink); ?>"><?php echo htmlspecialchars($restaurant['email']); ?></a>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($restaurant['email']); ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($lat !== null && $lng !== null): ?>
                                    <div><strong>Coordinates:</strong> <?php echo htmlspecialchars(number_format($lat, 6)); ?>, <?php echo htmlspecialchars(number_format($lng, 6)); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="restaurant-map-wrap">
                        <?php if (!empty($mapEmbedUrl)): ?>
                            <iframe
                                src="<?php echo htmlspecialchars($mapEmbedUrl); ?>"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Map location of <?php echo htmlspecialchars($restaurant['name']); ?>">
                            </iframe>
                            <div class="restaurant-map-actions">
                                <a href="<?php echo htmlspecialchars($openMapUrl); ?>" target="_blank" rel="noopener noreferrer">Open full map</a>
                            </div>
                        <?php else: ?>
                            <div class="restaurant-map-fallback">
                                Map coordinates are not available for this restaurant yet.
                                <?php if (!empty($restaurant['location'])): ?>
                                    <br>
                                    Location: <?php echo htmlspecialchars($restaurant['location']); ?>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($openMapUrl)): ?>
                                <div class="restaurant-map-actions">
                                    <a href="<?php echo htmlspecialchars($openMapUrl); ?>" target="_blank" rel="noopener noreferrer">Search this location on map</a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="experience-actions">
                        <a href="restaurants.php" class="smooth-scroll btn btn-secondary">← Back to Restaurants</a>
                    </div>
                </article>
            <?php else: ?>
                <div class="not-found">
                    <h1>Restaurant Not Found</h1>
                    <p>The restaurant you're looking for does not exist.</p>
                    <a href="restaurants.php" class="btn btn-primary">← Back to Restaurants</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <p></p>
            <div class="footer-links">
                <a href="#"></a>
                <a href="#"></a>
                <a href="#"></a>
            </div>
        </div>
    </footer>

    <script src="../assets/js/experience-details.js"></script>
</body>
</html>
