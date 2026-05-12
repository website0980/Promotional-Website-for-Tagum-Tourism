<?php
$dbFile = '../database.db';
$hotel = null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (file_exists($dbFile) && $id > 0) {
    try {
        $db = new SQLite3($dbFile);
        $stmt = $db->prepare('SELECT * FROM hotel_items WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        if ($row) {
            $hotel = $row;
        }
        $db->close();
    } catch (Exception $e) {
        // Keep fallback state and show "not found" UI.
    }
}

$pageTitle = $hotel ? ($hotel['name'] . ' - Tagum City') : 'Hotel Not Found - Tagum City';

$lat = null;
$lng = null;
$mapEmbedUrl = '';
$openMapUrl = '';
$emailLink = '';
$telLink = '';

if ($hotel) {
    if (isset($hotel['latitude']) && isset($hotel['longitude']) && is_numeric($hotel['latitude']) && is_numeric($hotel['longitude'])) {
        $lat = (float) $hotel['latitude'];
        $lng = (float) $hotel['longitude'];

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
    } elseif (!empty($hotel['location'])) {
        $query = rawurlencode($hotel['location'] . ', Tagum City');
        $openMapUrl = 'https://www.openstreetmap.org/search?query=' . $query;
    }

    if (!empty($hotel['email'])) {
        $emailLink = 'mailto:' . rawurlencode(trim($hotel['email']));
    }

    if (!empty($hotel['contact'])) {
        $digitsOnly = preg_replace('/[^0-9+]/', '', $hotel['contact']);
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
        .hotel-detail-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 1.25rem;
            margin: 1.5rem 0;
        }

        .hotel-panel {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem 1.1rem;
        }

        .hotel-panel h3 {
            margin: 0 0 0.75rem;
            color: #1d5a3d;
        }

        .hotel-description {
            color: #374151;
            line-height: 1.7;
            font-size: 1rem;
        }

        .hotel-price-tag {
            display: inline-block;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            background: #e8f5ee;
            color: #1d5a3d;
            font-weight: 700;
            font-size: 1.05rem;
            margin-bottom: 0.6rem;
        }

        .hotel-meta-list {
            display: grid;
            gap: 0.6rem;
            color: #374151;
            font-size: 0.98rem;
        }

        .hotel-meta-list strong {
            color: #111827;
        }

        .hotel-meta-list a {
            color: #1d5a3d;
            font-weight: 600;
            text-decoration: none;
        }

        .hotel-meta-list a:hover {
            text-decoration: underline;
        }

        .hotel-map-wrap {
            margin-top: 1.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
        }

        .hotel-map-wrap iframe {
            width: 100%;
            height: 320px;
            border: 0;
            display: block;
        }

        .hotel-map-fallback {
            padding: 1rem 1.1rem;
            color: #374151;
            line-height: 1.6;
        }

        .hotel-map-actions {
            padding: 0.8rem 1.1rem 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .hotel-map-actions a {
            color: #1d5a3d;
            font-weight: 600;
            text-decoration: none;
        }

        .hotel-map-actions a:hover {
            text-decoration: underline;
        }

        @media (max-width: 900px) {
            .hotel-detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>

    <main class="experience-single">
        <div class="container">
            <?php if ($hotel): ?>
                <article class="experience-detail active">
                    <header class="experience-header">
                        <h1><?php echo htmlspecialchars($hotel['name']); ?></h1>
                        <div class="experience-meta">
                            <span class="exp-type"><?php echo htmlspecialchars($hotel['category'] ?? 'Hotel'); ?></span>
                        </div>
                    </header>

                    <?php if (!empty($hotel['image'])): ?>
                        <div class="experience-image">
                            <?php $detailImagePath = !empty($hotel['image']) ? basename($hotel['image']) : 'experience-default.jpg'; ?>
                            <img src="../assets/images/hotels/<?php echo htmlspecialchars($detailImagePath); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
                        </div>
                    <?php endif; ?>

                    <div class="hotel-detail-grid">
                        <div class="hotel-panel">
                            <h3>Description</h3>
                            <p class="hotel-description"><?php echo nl2br(htmlspecialchars($hotel['description'] ?? 'No description available.')); ?></p>
                            <?php if (!empty($hotel['information'])): ?>
                                <h3 style="margin-top: 1rem;">Information</h3>
                                <p class="hotel-description"><?php echo nl2br(htmlspecialchars($hotel['information'])); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="hotel-panel">
                            <h3>Hotel Details</h3>
                            <div class="hotel-price-tag">
                                PHP <?php echo htmlspecialchars($hotel['price'] ?? 'N/A'); ?>
                            </div>
                            <div class="hotel-meta-list">
                                <div><strong>Category:</strong> <?php echo htmlspecialchars($hotel['category'] ?? 'N/A'); ?></div>
                                <div><strong>Location:</strong> <?php echo htmlspecialchars($hotel['location'] ?? 'Location not available'); ?></div>
                                <?php if (!empty($hotel['contact'])): ?>
                                    <div>
                                        <strong>Contact:</strong>
                                        <?php if (!empty($telLink)): ?>
                                            <a href="<?php echo htmlspecialchars($telLink); ?>"><?php echo htmlspecialchars($hotel['contact']); ?></a>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($hotel['contact']); ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($hotel['email'])): ?>
                                    <div>
                                        <strong>Email:</strong>
                                        <?php if (!empty($emailLink)): ?>
                                            <a href="<?php echo htmlspecialchars($emailLink); ?>"><?php echo htmlspecialchars($hotel['email']); ?></a>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($hotel['email']); ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($lat !== null && $lng !== null): ?>
                                    <div><strong>Coordinates:</strong> <?php echo htmlspecialchars(number_format($lat, 6)); ?>, <?php echo htmlspecialchars(number_format($lng, 6)); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="hotel-map-wrap">
                        <?php if (!empty($mapEmbedUrl)): ?>
                            <iframe
                                src="<?php echo htmlspecialchars($mapEmbedUrl); ?>"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Map location of <?php echo htmlspecialchars($hotel['name']); ?>">
                            </iframe>
                            <div class="hotel-map-actions">
                                <a href="<?php echo htmlspecialchars($openMapUrl); ?>" target="_blank" rel="noopener noreferrer">Open full map</a>
                            </div>
                        <?php else: ?>
                            <div class="hotel-map-fallback">
                                Map coordinates are not available for this hotel yet.
                                <?php if (!empty($hotel['location'])): ?>
                                    <br>
                                    Location: <?php echo htmlspecialchars($hotel['location']); ?>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($openMapUrl)): ?>
                                <div class="hotel-map-actions">
                                    <a href="<?php echo htmlspecialchars($openMapUrl); ?>" target="_blank" rel="noopener noreferrer">Search this location on map</a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="experience-actions">
                        <a href="hotels.php" class="smooth-scroll btn btn-secondary">← Back to Hotels</a>
                    </div>
                </article>
            <?php else: ?>
                <div class="not-found">
                    <h1>Hotel Not Found</h1>
                    <p>The hotel you're looking for does not exist.</p>
                    <a href="hotels.php" class="btn btn-primary">← Back to Hotels</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="../assets/js/experience-details.js"></script>
</body>
</html>
