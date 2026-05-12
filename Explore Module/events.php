<?php $tab = $_GET['tab'] ?? ''; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Tagum City</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/explore-full-page.css">
    <style>
        .logo-img-small { width: 32px !important; height: 32px !important; }
        body { padding-top: 0; }
        .experiences { padding: 2rem; max-width: 1200px; margin: 0 auto; }
        .experiences h2 { font-size: 2.5rem; color: #1d5a3d; margin-bottom: 2rem; text-align: center; }
        .experiences-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; }
        .experience-item { background-color: #f3f4f6; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; text-decoration: none; color: inherit; display: block; }
        .experience-item:hover { transform: translateY(-8px); box-shadow: 0 12px 24px rgba(29, 90, 61, 0.2); }
        .experience-item img { width: 100%; height: 220px; object-fit: cover; }
        .experience-item h3 { color: #1d5a3d; padding: 1.25rem 1.25rem 0.75rem; font-size: 1.3rem; font-weight: 600; }
        .experience-item .meta { padding: 0 1.25rem 0.5rem; font-size: 0.9rem; color: #6b7280; }
        .experience-item .distance { color: #1d5a3d; font-weight: 600; }
        .experience-item p { color: #6b7280; padding: 0 1.25rem 1rem; line-height: 1.6; }
        .experience-cta { display: block; color: #1d5a3d; font-weight: 600; padding: 0 1.25rem 1.25rem; font-size: 1rem; }
        .experience-item.no-data { text-align: center; padding: 3rem 2rem; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); }
        .controls { max-width: 1200px; margin: 0 auto 2rem; padding: 0 2rem; display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; justify-content: center; }
        .btn-sort { padding: 0.75rem 1rem; background: #1d5a3d; color: white; border: none; border-radius: 25px; cursor: pointer; transition: all 0.3s; font-weight: 500; font-size: 1rem; }
        .home-btn { background: #3b82f6; font-size: 1rem; padding: 0.75rem 1rem; }
        .location-btn { background: #3b82f6; }
        .loading { opacity: 0.5; pointer-events: none; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <img src="../assets/images/CityofTagum.png" alt="Tagum City" class="logo-img-small">
                <span class="logo-text">Tagum City</span>
            </div>
            <ul class="nav-menu">
                <li><a href="../index.php#home" class="nav-link">Home</a></li>
                <li><a href="../index.php#explore" class="nav-link">Explore</a></li>
                <li><a href="../index.php#experience" class="nav-link">Experience</a></li>
                <li><a href="../index.php#plan" class="nav-link">Plan</a></li>
                <li><a href="../index.php#contact" class="nav-link">Contact Us</a></li>
            </ul>
        </div>
    </nav>

    <section class="experiences">
        <div class="controls">
            <button id="get-home" class="btn-sort home-btn">🏠 Home</button>
            <button id="get-location" class="btn-sort location-btn">📍 Scan</button>
        </div>

        <?php
        $dbFile = '../database.db';
        $events = [];
        $userLat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
        $userLng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;
        $sortByDistance = $userLat !== null && $userLng !== null;

        function haversineDistance($lat1, $lon1, $lat2, $lon2) {
            $earthRadius = 6371; // km
            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lon2 - $lon1);
            $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
            return $earthRadius * $c;
        }
        
        if (file_exists($dbFile)) {
            $db = new SQLite3($dbFile);
            $result = $db->query('SELECT * FROM events ORDER BY id DESC');
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                if ($sortByDistance && !is_null($row['latitude']) && !is_null($row['longitude'])) {
                    $row['distance'] = haversineDistance($userLat, $userLng, $row['latitude'], $row['longitude']);
                } else {
                    $row['distance'] = 0.0;
                }
                $events[] = $row;
            }
            $db->close();
            
            if ($sortByDistance) {
                usort($events, function($a, $b) {
                    return $a['distance'] <=> $b['distance'];
                });
            }
        }
        foreach ($events as $event): ?>
            <a href="event1-detail.php?id=<?php echo $event['id']; ?>" class="experience-item" style="position: relative; z-index: 10;"></a>
                <?php if (!empty($event['image'])): ?>
                    <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['name']); ?>">
                <?php else: ?>
                    <img src="../assets/images/experience-default.jpg" alt="<?php echo htmlspecialchars($event['location']); ?>">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                <p><?php echo htmlspecialchars(substr($event['description'] ?? $event['history'] ?? 'No description', 0, 100)); ?>...</p>
                <span class="experience-cta distance"><?php echo number_format($event['distance'], 1); ?> km away</span>
            </a>
        <?php endforeach; ?>
        <?php if (empty($events)): ?>
            <div class="experience-item no-data">
                <img src="../assets/images/experience-default.jpg" alt="No data">
                <h3>No Events Found</h3>
                <p>Add events from admin panel.</p>
            </div>
        <?php endif; ?>
    </section>

    <script>
        document.getElementById('get-home').onclick = function() {
            window.location.href = '../index.php';
        };

        document.getElementById('get-location').onclick = function() {
            const btn = this;
            btn.textContent = '📍 Getting Location...';
            btn.classList.add('loading');
            
            if (!navigator.geolocation) {
                alert('Geolocation not supported.');
                resetBtn();
                return;
            }
            
            navigator.geolocation.getCurrentPosition(
                position => {
                    location.href = `?lat=${position.coords.latitude}&lng=${position.coords.longitude}`;
                },
                error => {
                    let msg = 'Location access denied.';
                    if (error.code === error.TIMEOUT) msg = 'Location timeout.';
                    alert(msg);
                    resetBtn();
                },
                { timeout: 10000, enableHighAccuracy: true }
            );
            
            function resetBtn() {
                btn.textContent = '📍 Scan';
                btn.classList.remove('loading');
            }
        };
    </script>
</body>
</html>

