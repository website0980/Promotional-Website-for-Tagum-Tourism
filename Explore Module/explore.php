<?php
$requestedSection = isset($_GET['section']) && in_array($_GET['section'], ['events', 'festivals'], true)
    ? $_GET['section']
    : 'events';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Know More about Tagum City</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/explore-full-page.css">
    <link rel="stylesheet" href="../css/explore-cuisine-landscape.css">
    <link rel="stylesheet" href="../css/mobile-navbar.css">
    <link rel="stylesheet" href="../css/explore-full-page-landscape.css">
    <script src="../js/navbar.js"></script>
</head>
<body>
<?php include '../navbar.php'; ?>

    <!-- Explore Content Section -->
    <section class="explore-full-page">
        <div class="explore-container">
            <!-- Section Tabs -->
            <div class="section-tabs">
                <button class="tab-btn <?php echo $requestedSection === 'events' ? 'active' : ''; ?>" data-section="events">Events</button>
                <button class="tab-btn <?php echo $requestedSection === 'festivals' ? 'active' : ''; ?>" data-section="festivals">Festivals</button>
            </div>

            <!-- Events Content -->
            <div class="section-content <?php echo $requestedSection === 'events' ? 'active' : ''; ?>" id="events">
                <h1>Events</h1>
                <div class="content-text">
                    <h2>Discover Local Events</h2>
                    <div class="calendar-view-link">
                        <a href="events-calendar.php" class="calendar-view-btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            View Calendar
                        </a>
                    </div>
                    <p>Browse events by month. Click a month to see what's happening — each group shows the event names at a glance, then expands to full details.</p>
                </div>

                <?php
                require_once dirname(__DIR__) . '/includes/events_helpers.php';
                $events = loadEvents(dirname(__DIR__) . '/database.db');
                ?>

                <div class="events-section-wrapper">
                    <?php if (!empty($events)): ?>
                        <?php include dirname(__DIR__) . '/includes/events_month_view.php'; ?>
                    <?php else: ?>
                        <div class="content-text">
                            <h3>Featured Events:</h3>
                            <ul>
                                <li><strong>Araw ng Tagum Festival</strong> - Annual celebration of the city's founding</li>
                                <li><strong>Davao Food Festival</strong> - Showcase of local culinary traditions</li>
                                <li><strong>Tagum Sports Fest</strong> - Community sports and recreation events</li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Festivals Content -->
            <div class="section-content <?php echo $requestedSection === 'festivals' ? 'active' : ''; ?>" id="festivals">
                <h1>Festivals</h1>
                <div class="content-text">
                    <h2>Celebrate Local Culture</h2>
                    <p>Tagum City comes alive with vibrant festivals throughout the year, showcasing the rich traditions, music, dance, and culinary heritage of the region. Experience the warmth and hospitality of the local community.</p>
                    
                    <?php
                    $dbFile = '../database.db';
                    $festivals = [];
                    $events = loadEvents(dirname(__DIR__) . '/database.db');
                    if (file_exists($dbFile)) {
                        try {
                            $db = new SQLite3($dbFile);
                            $result = $db->query('SELECT * FROM festivals ORDER BY id DESC');
                            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                                $festivals[] = $row;
                            }
                            $db->close();
                        } catch (Exception $e) {
                            // Fallback to JSON if database fails
                            $festivals = json_decode(file_get_contents('../data/festivals.json'), true) ?? [];
                        }
                    } else {
                        // Fallback to JSON if database doesn't exist
                        $festivals = json_decode(file_get_contents('../data/festivals.json'), true) ?? [];
                    }

                    $festivals = sortFestivalsByRelatedEvents($festivals, $events);
                    ?>
                    
                    <?php if (!empty($festivals)): ?>
                    <div class="cuisine-grid">
                        <?php foreach ($festivals as $index => $festival): ?>
                            <div class="cuisine-category">
                                <?php if (!empty($festival['image'])): ?>
                                    <?php 
                                    $imagePath = $festival['image'];
                                    // Fix image path if it has ../../ prefix
                                    if (strpos($imagePath, '../../assets/') === 0) {
                                        $imagePath = str_replace('../../assets/', '../assets/', $imagePath);
                                    }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($festival['name']); ?>" class="category-image" onerror="this.style.display='none'">
                                <?php endif; ?>
                                <h3><?php echo htmlspecialchars($festival['name']); ?></h3>
                                <p><?php echo htmlspecialchars($festival['description']); ?></p>
                                <?php if (!empty($festival['date'])): ?>
                                    <p class="item-count">📅 <?php echo htmlspecialchars($festival['date']); ?></p>
                                <?php endif; ?>
<a href="festival-detail.php?id=<?php echo $festival['id']; ?>" class="read-more-btn btn btn-primary">Read More</a>

                            </div>
                        <?php endforeach; ?>

                    </div>
                    <?php else: ?>
                    <h3>Annual Festivals:</h3>
                    <ul>
                        <li><strong>Kadayawan Festival</strong> - Week-long celebration of thanksgiving</li>
                        <li><strong>Araw ng Tagum</strong> - Foundation day festivities</li>
                        <li><strong>Sinigang Festival</strong> - Food and cultural festival</li>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

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

<script src="../js/explore-full-page.js"></script>
    <script src="../js/explore-cuisine-landscape.js"></script>
</body>
</html>
