<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Festival Details - Tagum City</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/experience-details.css">
    <style>
        .event-detail-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .event-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .event-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 2rem;
        }
        .event-content {
            line-height: 1.8;
            color: #555;
        }
        .event-section {
            margin: 2rem 0;
            padding: 1.5rem;
            background: #f9f9f9;
            border-radius: 8px;
            border-left: 4px solid var(--primary-color, #1d5a3d);
        }
        .event-section h3 {
            color: var(--primary-color, #1d5a3d);
            margin-bottom: 1rem;
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 1rem;
            padding: 0.75rem 1.5rem;
            background: var(--primary-color, #1d5a3d);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s;
        }
        .back-btn:hover {
            background: var(--dark-green, #145233);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <img src="../assets/images/CityofTagum.png" alt="Tagum City" class="logo-img">
                <span class="logo-text">Tagum City</span>
            </div>
            <ul class="nav-menu">
                <li><a href="../index.php#home" class="nav-link">Home</a></li>
                <li><a href="../index.php#explore" class="nav-link">Explore</a></li>
                <li><a href="../index.php#experiences" class="nav-link">Experiences</a></li>
                <li><a href="../index.php#plan" class="nav-link">Plan</a></li>
                <li><a href="../index.php#hotels-restaurants" class="nav-link">Hotels & Restaurants</a></li>
                <li><a href="../index.php#contact" class="nav-link">Contact us</a></li>
            </ul>
        </div>
    </nav>

    <!-- Festival Detail -->
    <section class="explore-full-page">
        <div class="event-detail-container">
            <a href="explore.php?section=festivals" class="back-btn">← Back to Festivals</a>

            <?php
            $dbFile = '../database.db';
            $id = $_GET['id'] ?? null;
            $festival = null;

            if ($id !== null && is_numeric($id)) {
                if (file_exists($dbFile)) {
                    try {
                        $db = new SQLite3($dbFile);
                        $stmt = $db->prepare('SELECT * FROM festivals WHERE id = :id');
                        $stmt->bindValue(':id', (int)$id, SQLITE3_INTEGER);
                        $result = $stmt->execute();
                        $festival = $result->fetchArray(SQLITE3_ASSOC);
                        $db->close();
                    } catch (Exception $e) {
                        $festival = null;
                    }
                }
            }

            if ($festival):
            ?>

            <div class="event-header">
                <h1><?php echo htmlspecialchars($festival['name']); ?></h1>
            </div>

            <?php if (!empty($festival['image'])): ?>
                <img src="<?php echo htmlspecialchars($festival['image']); ?>" alt="<?php echo htmlspecialchars($festival['name']); ?>" class="event-image" onerror="this.style.display='none'">
            <?php endif; ?>

            <div class="event-content">
                <?php if (!empty($festival['description'])): ?>
                    <div class="event-section">
                        <h3>📝 About</h3>
                        <p><?php echo nl2br(htmlspecialchars($festival['description'])); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($festival['date'])): ?>
                    <div class="event-section">
                        <h3>📅 Date</h3>
                        <p><?php echo htmlspecialchars($festival['date']); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($festival['highlights'])): ?>
                    <div class="event-section">
                        <h3>⭐ Highlights</h3>
                        <p><?php echo nl2br(htmlspecialchars($festival['highlights'])); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($festival['activities'])): ?>
                    <div class="event-section">
                        <h3>✨ Activities</h3>
                        <p><?php echo nl2br(htmlspecialchars($festival['activities'])); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <?php else: ?>
                <div class="event-section">
                    <h2>Festival Not Found</h2>
                    <p>Sorry, we couldn't find the festival you're looking for. <a href="explore.php?section=festivals">Go back to festivals</a></p>
                </div>
            <?php endif; ?>
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

    <script src="../assets/js/navbar.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
