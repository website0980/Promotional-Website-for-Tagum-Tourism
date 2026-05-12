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
                <button class="tab-btn active" data-section="events">Events</button>
                <button class="tab-btn" data-section="festivals">Festivals</button>
            </div>

            <!-- Events Content -->
            <div class="section-content active" id="events">
                <h1>Events</h1>
                <div class="content-text">
                    <h2>Discover Local Events</h2>
                    <p>Experience vibrant events and gatherings that celebrate Tagum City's culture, traditions, and community spirit. From festivals to cultural celebrations, there's always something happening.</p>
                    
                    <?php
                    $dbFile = '../database.db';
                    $events = [];
                    if (file_exists($dbFile)) {
                        try {
                            $db = new SQLite3($dbFile);
                            $result = $db->query('SELECT * FROM events ORDER BY id DESC');
                            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                                $events[] = $row;
                            }
                            $db->close();
                        } catch (Exception $e) {
                            // Fallback to JSON if database fails
                            $events = json_decode(file_get_contents('../data/cultural-sites.json'), true) ?? [];
                        }
                    } else {
                        // Fallback to JSON if database doesn't exist
                        $events = json_decode(file_get_contents('../data/cultural-sites.json'), true) ?? [];
                    }
                    ?>
                    
                    <?php if (!empty($events)): ?>
                    <div class="cuisine-grid">
                        <?php foreach ($events as $site): ?>
                            <div class="cuisine-category">
                                <?php if (!empty($site['image'])): ?>
                                    <?php 
                                    $imagePath = $site['image'];
                                    // Fix image path if it has ../../ prefix
                                    if (strpos($imagePath, '../../') === 0) {
                                        $imagePath = str_replace('../../', '../', $imagePath);
                                    }
                                    // Fix image path if it has cultural-sites in it
                                    if (strpos($imagePath, 'cultural-sites') !== false) {
                                        $imagePath = str_replace('cultural-sites', 'events', $imagePath);
                                    }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($site['name']); ?>" class="category-image" onerror="this.style.display='none'">
                                <?php endif; ?>
                                <h3><?php echo htmlspecialchars($site['name']); ?></h3>
                                <!-- No description - tease with Read More! -->
<a href="event-detail.php?id=<?php echo $site['id']; ?>" class="read-more-btn btn btn-primary">Read More</a>

                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <h3>Featured Events:</h3>
                    <ul>
                        <li><strong>Araw ng Tagum Festival</strong> - Annual celebration of the city's founding</li>
                        <li><strong>Davao Food Festival</strong> - Showcase of local culinary traditions</li>
                        <li><strong>Tagum Sports Fest</strong> - Community sports and recreation events</li>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Festivals Content -->
            <div class="section-content" id="festivals">
                <h1>Festivals</h1>
                <div class="content-text">
                    <h2>Celebrate Local Culture</h2>
                    <p>Tagum City comes alive with vibrant festivals throughout the year, showcasing the rich traditions, music, dance, and culinary heritage of the region. Experience the warmth and hospitality of the local community.</p>
                    
                    <?php
                    $dbFile = '../database.db';
                    $festivals = [];
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
