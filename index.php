<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagum City - Discover Natural Beauty</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/mobile-navbar.css">
<?php include 'navbar.php'; ?>
</head>
<body>

    <!-- Hero Carousel Section -->
    <section class="hero-carousel" id="home">
        <div class="carousel-container">
            <!-- Slide 1 -->
            <div class="carousel-slide active" style="background-image: url('images/Background for slide 1.jpg');">
                <div class="slide-overlay"></div>
                <div class="slide-content">
                    <p class="slide-tagline">Tagumeños: Beauty that Shines from Within.</p>
                    <h1 class="slide-title">Discover<br>Natural Beauty</h1>
                    <p class="slide-description">
                        Tagumeños are a reflection of true natural beauty radiating warmth, kindness, and genuine smiles that make everyone feel welcome. 
                    </p>
                    <div class="button-group">
                        <a href="#plan" class="btn btn-primary smooth-scroll">Explore Now</a>
                        <a href="#explore" class="btn btn-secondary smooth-scroll">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="carousel-slide" style="background-image: url('images/Background for slide 2 .jpg');">
                <div class="slide-overlay"></div>
                <div class="slide-content">
                    <p class="slide-tagline">Cultural heritage meets modern charm</p>
                    <h1 class="slide-title">Experience<br>Local Culture</h1>
                    <p class="slide-description">
                        Immerse yourself in the vibrant traditions, local cuisine, and warm hospitality of Tagum City. Discover authentic experiences that celebrate our rich heritage.
                    </p>
                    <div class="button-group">
                        <a href="#plan" class="btn btn-primary smooth-scroll">Explore Now</a>
                        <a href="#explore" class="btn btn-secondary smooth-scroll">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="carousel-slide" style="background-image: url('images/Background for slide 3.jpg');">
                <div class="slide-overlay"></div>
                <div class="slide-content">
                    <p class="slide-tagline">Tagum Adventures: Feel the Thrill, Live the Moment</p>
                    <h1 class="slide-title">Thrilling<br>Adventures</h1>
                    <p class="slide-description">
                        Step into the excitement that awaits in Tagum where every journey is filled with adrenaline, discovery, and unforgettable moments. From outdoor explorations to vibrant city experiences, adventure is always just around the corner.
                    </p>
                    <div class="button-group">
                        <a href="#plan" class="btn btn-primary smooth-scroll">Explore Now</a>
                        <a href="#explore" class="btn btn-secondary smooth-scroll">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Navigation Arrows -->
            <button class="carousel-btn carousel-btn-prev" onclick="changeSlide(-1)">❮</button>
            <button class="carousel-btn carousel-btn-next" onclick="changeSlide(1)">❯</button>
        </div>

        <!-- Carousel Dots -->
        <div class="carousel-dots">
            <span class="dot active" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
        </div>
    </section>

    <!-- Explore Section -->
    <section class="explore" id="explore">
        <h2>Know More about Tagum City</h2>
        <div class="explore-grid">

            <!-- Event Card -->
            <div class="explore-card">
                <div class="card-image">👥</div>
                <h3>Events</h3>
                <p>Experience the rich history and cultural heritage of our community.</p>
                <a href="Explore module/explore.php?section=events" class="card-link">Learn More →</a>
            </div>
            
            <!-- Festivals Card -->
            <div class="explore-card">
                <div class="card-image">🎉</div>
                <h3>Festivals</h3>
                <p>Join vibrant local festivals showcasing music, dance, and culture.</p>
                <a href="Explore module/explore.php?section=festivals" class="card-link">Learn More →</a>
            </div>
        </div>
    </section>

    <!-- Experiences Section -->
    <section class="experiences" id="experiences">
        <h2>Unforgettable Experiences</h2>
        <div class="experiences-grid">
            <?php
            // Load experiences from database with JSON fallback
            $dbFile = 'database.db';
            $experiences = [];
            
            if (file_exists($dbFile)) {
                try {
                    $db = new SQLite3($dbFile);
                    $result = $db->query('SELECT * FROM experiences ORDER BY id DESC');
                    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                        $experiences[] = $row;
                    }
                    $db->close();
                } catch (Exception $e) {
                    // Fallback to JSON if database fails
                    $experiences = json_decode(file_get_contents('data/experiences.json'), true) ?? [];
                }
            } else {
                // Fallback to JSON if database doesn't exist
                $experiences = json_decode(file_get_contents('data/experiences.json'), true) ?? [];
            }
            
            // Sort experiences: featured ones first
            usort($experiences, function($a, $b) {
                $aFeatured = isset($a['featured']) && $a['featured'] === true;
                $bFeatured = isset($b['featured']) && $b['featured'] === true;
                if ($aFeatured === $bFeatured) {
                    return 0;
                }
                return $aFeatured ? -1 : 1;
            });
            
            // Display experiences (limit to 8 for the grid)
            $displayExperiences = array_slice($experiences, 0, 8);
            
            foreach ($displayExperiences as $exp):
                $expType = $exp['type'] ?? 'experience';
            ?>
                <a href="Experience module/experience.php?id=<?php echo $exp['id']; ?>" class="experience-item">
                    <?php if (!empty($exp['image'])): ?>
                        <img src="<?php echo htmlspecialchars($exp['image']); ?>" alt="<?php echo htmlspecialchars($exp['name']); ?>">
                    <?php else: ?>
                        <img src="assets/images/experience-default.jpg" alt="<?php echo htmlspecialchars($exp['name']); ?>">
                    <?php endif; ?>
                    <h3><?php echo isset($exp['featured']) && $exp['featured'] === true ? '⭐ ' : ''; ?><?php echo htmlspecialchars($exp['name']); ?></h3>
                    <p><?php echo htmlspecialchars($exp['description']); ?></p>
                    <span class="experience-cta">View Details →</span>
                </a>
            <?php endforeach; ?>
            
            <?php if (empty($displayExperiences)): ?>
            <a href="Experience module/experience.php?type=river-tours" class="experience-item">
                <img src="assets/images/experience-1.jpg" alt="River Tours">
                <h3>River Tours</h3>
                <p>Navigate pristine waterways with expert guides.</p>
                <span class="experience-cta">View Details →</span>
            </a>
            <a href="Experience module/experience.php?type=mountain-hiking" class="experience-item">
                <img src="assets/images/experience-2.jpg" alt="Hiking">
                <h3>Mountain Hiking</h3>
                <p>Trek through lush forests and scenic trails.</p>
                <span class="experience-cta">View Details →</span>
            </a>
            <a href="Experience module/experience.php?type=cultural-events" class="experience-item">
                <img src="assets/images/experience-3.jpg" alt="Cultural Events">
                <h3>Cultural Events</h3>
                <p>Participate in local festivals and celebrations.</p>
                <span class="experience-cta">View Details →</span>
            </a>
            <a href="Experience module/experience.php?type=food-tours" class="experience-item">
                <img src="assets/images/experience-4.jpg" alt="Food Tours">
                <h3>Food Tours</h3>
                <p>Taste the flavors of authentic local cuisine.</p>
                <span class="experience-cta">View Details →</span>
            </a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Featured Destinations Section -->
    <section class="featured" id="featured">
        <h2>Featured Destinations</h2>
        <p class="section-subtitle">Discover our top-picked attractions and experiences</p>
        <div class="featured-grid">
<?php
            $dbFile = 'database.db';
            $featuredDestinations = [];
            if (file_exists($dbFile)) {
                try {
                    $db = new SQLite3($dbFile);
                    $result = $db->query('SELECT * FROM destinations WHERE featured = 1 ORDER BY id DESC');
                    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                        $featuredDestinations[] = $row;
                    }
                    $db->close();
                } catch (Exception $e) {
                    // Fallback
                }
            }
            
            // Get type icons
            $typeIcons = [
                'Natural Wonder' => '🏞️',
                'Adventure' => '⛰️',
                'Museum' => '🏛️',
                'Religious' => '⛪',
                'Festival' => '🎉'
            ];
            
            foreach ($featuredDestinations as $dest):
                $icon = '📍';
                $linkName = strtolower(str_replace(' ', '-', $dest['name']));
            ?>
                <a href="Plan module/destination.php?destination=<?php echo $linkName; ?>" class="featured-card">
                    <div class="featured-icon"><?php echo $icon; ?></div>
                    <h3><?php echo htmlspecialchars($dest['name']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($dest['description'], 0, 100)) . (strlen($dest['description']) > 100 ? '...' : ''); ?></p>
                    <span class="featured-cta">View Details →</span>
                </a>
            <?php endforeach; ?>
            
    <?php if (empty($featuredDestinations)): ?>
                <p style="text-align:center;grid-column:1/-1;">No featured destinations yet.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Planning Section -->
    <section class="planning" id="plan">
        <h2>Plan Your Visit</h2>
        <p class="section-subtitle">Explore our top tourist destinations with comprehensive guides, best travel times, packing lists, and visiting guidelines.</p>
<div class="experiences-grid">
<?php
            $dbFile = 'database.db';
            $destinations = [];
            if (file_exists($dbFile)) {
                try {
                    $db = new SQLite3($dbFile);
                    $result = $db->query('SELECT * FROM destinations ORDER BY featured DESC, id DESC LIMIT 8');
                    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                        $destinations[] = $row;
                    }
                    $db->close();
                } catch (Exception $e) {
                    // Fallback static
                }
            }
            
            // Get type icons
            $typeIcons = [
                'Natural Wonder' => '🏞️',
                'Adventure' => '⛰️',
                'Museum' => '🏛️',
                'Religious' => '⛪',
                'Festival' => '🎉',
                'Historical' => '📜',
                'Local Cuisine' => '🍽️'
            ];
            
            foreach ($destinations as $dest):
                $icon = '📍';
                $linkName = strtolower(str_replace(' ', '-', $dest['name']));
            ?>
                <a href="Plan module/destination.php?destination=<?php echo $linkName; ?>" class="experience-item">
                    <?php if (!empty($dest['image'])): ?>
                        <img src="<?php echo str_replace('../../', '', htmlspecialchars($dest['image'])); ?>" alt="<?php echo htmlspecialchars($dest['name']); ?>">
                    <?php else: ?>
                        <img src="assets/images/destination-default.jpg" alt="<?php echo htmlspecialchars($dest['name']); ?>">
                    <?php endif; ?>
                    <h3><?php echo ($dest['featured'] == 1) ? '⭐ ' : ''; ?><?php echo htmlspecialchars($dest['name']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($dest['description'], 0, 100)) . (strlen($dest['description']) > 100 ? '...' : ''); ?></p>
                    <span class="experience-cta">View Details →</span>
                </a>
            <?php endforeach; ?>
            
            <?php if (empty($destinations)): ?>
            <a href="Plan module/destination.php?destination=pumauna-waterfalls" class="experience-item">
                <img src="assets/images/destinations/pumauna-waterfalls.jpg" alt="Pumauna Waterfalls">
                <h3>Pumauna Waterfalls</h3>
                <p>Magnificent cascade with natural pools and scenic hiking trails.</p>
                <span class="experience-cta">View Details →</span>
            </a>
            <a href="Plan module/destination.php?destination=azuela-springs" class="experience-item">
                <img src="assets/images/destinations/azuela-springs.jpg" alt="Azuela Springs">
                <h3>Azuela Springs</h3>
                <p>Crystal clear natural pools fed by underground springs.</p>
                <span class="experience-cta">View Details →</span>
            </a>
            <a href="Plan module/destination.php?destination=mt-kampalilis" class="experience-item">
                <img src="assets/images/destinations/mt-kampalilis.jpg" alt="Mt. Kampalilis">
                <h3>Mt. Kampalilis</h3>
                <p>Challenge yourself with a scenic mountain trek to 1,240m peak.</p>
                <span class="experience-cta">View Details →</span>
            </a>
            <a href="Plan module/destination.php?destination=tagum-river" class="experience-item">
                <img src="assets/images/destinations/tagum-river.jpg" alt="Tagum River">
                <h3>Tagum River</h3>
                <p>Pristine waterway perfect for boating, fishing, and relaxation.</p>
                <span class="experience-cta">View Details →</span>
            </a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Hotels & Restaurants Section -->
    <section class="hotels-restaurants" id="hotels-restaurants">
        <h2 class="section-title">Hotels & Restaurants</h2>
        <div class="explore-grid">
            <!-- Hotel Categories Card -->
            <div class="explore-card">
                <div class="card-image">🏨</div>
                <h3>Hotel Categories</h3>
                <p>Find perfect accommodations from luxury hotels to cozy stays across various categories.</p>
                <a href="Hotel Module/hotels.php" class="card-link">Explore Hotels →</a>
            </div>
            
            <!-- Restaurant Categories Card -->
            <div class="explore-card">
                <div class="card-image">🍴</div>
                <h3>Restaurant Categories</h3>
                <p>Discover diverse dining experiences from local eateries to fine dining.</p>
                <a href="Restaurant Module/restaurants.php" class="card-link">Explore Restaurants →</a>
            </div>
        </div>
    </section>

    <style>
        .hotels-restaurants {
            padding: 4rem 2rem;
            background-color: var(--light-gray, #f3f4f6);
            max-width: 1200px;
            margin: 0 auto;
        }
        .hotels-restaurants .section-title {
            font-size: 2.5rem;
            color: var(--dark-green, #1d5a3d);
            margin-bottom: 3rem;
            text-align: center;
        }
    </style>

    <!-- Contact Information Section -->
    <section class="contact-information" id="contact">
        <h2 class="section-title">Contact Information</h2>
        <div class="contact-display">
            <div class="contact-details">
                <div class="contact-row">
                    <span class="contact-icon">📍</span>
                    <div>
                        <strong>Address:</strong><br> 1st Floor, City of Tagum Cultural Center Bldg., Osmeña St., Tagum City, Philippines, 8100 <br>
                    </div>
                </div>
                <div class="contact-row">
                    <span class="contact-icon">📞</span>
                    <div>
                        <strong>Contact Number:</strong><br><a href="tel:09534971605">0953 497 1605</a>
                    </div>
                </div>
                <div class="contact-row">
                    <span class="contact-icon">✉️</span>
                    <div>
                        <strong>Email:</strong><br><a href="mailto:tagumtourismcultural@gmail.com" target="_self" rel="noopener noreferrer">tagumtourismcultural@gmail.com</a>
                    </div>
                </div>
                <div class="contact-row">
                    <span class="contact-icon">🌐</span>
                    <div>
                        <strong>Website:</strong><br><a href="https://tagumcity.gov.ph" target="_blank" rel="noopener noreferrer">tagumcity.gov.ph</a>
                    </div>
                </div>
                <div class="contact-row">
                    <span class="contact-icon">📘</span>
                    <div>
                        <strong>Facebook:</strong><br><a href="https://www.facebook.com/tagumtourismandcultural" target="_blank" rel="noopener noreferrer">Tagum Tourism and Cultural Office</a>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .contact-information {
                padding: 4rem 2rem;
                background-color: var(--light-gray, #f3f4f6);
                max-width: 1200px;
                margin: 0 auto;
            }
            .contact-information .section-title {
                font-size: 2.5rem;
                color: var(--dark-green, #1d5a3d);
                margin-bottom: 3rem;
                text-align: center;
            }
            .contact-display {
                max-width: 800px;
                margin: 0 auto;
            }
            .contact-details {
                background: white;
                padding: 3rem;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
            .contact-row {
                display: flex;
                align-items: flex-start;
                gap: 1.5rem;
                margin-bottom: 2rem;
                padding-bottom: 1.5rem;
                border-bottom: 1px solid #eee;
            }
            .contact-row:last-child {
                margin-bottom: 0;
                border-bottom: none;
            }
            .contact-icon {
                font-size: 2.5rem;
                flex-shrink: 0;
                margin-top: 0.2rem;
            }
            .contact-row strong {
                color: var(--dark-green, #1d5a3d);
                font-size: 1.1rem;
            }
            .contact-row div {
                flex: 1;
            }
            .contact-row a {
                color: var(--primary-color, #1d5a3d);
                text-decoration: none;
            }
            .contact-row a:hover {
                text-decoration: underline;
            }

            /* Mobile: prevent email/phone text from overflowing the box */
            @media (max-width: 480px) {
                .contact-details {
                    padding: 1.25rem;
                }

                .contact-row {
                    gap: 0.9rem;
                    margin-bottom: 1.25rem;
                    padding-bottom: 1rem;
                }

                .contact-row div {
                    flex: 1 1 auto;
                    min-width: 0;
                }

                .contact-row a,
                .contact-row div {
                    word-break: break-word;
                    overflow-wrap: anywhere;
                    white-space: normal;
                }
            }
        </style>

<script src="js/script.js"></script>
</body>
</html>

