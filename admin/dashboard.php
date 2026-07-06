<?php
// Admin Dashboard - Tagum City
require_once 'config.php';
requireAuth();

$destinations = loadDestinations();
$experiences = loadExperiences();
$events = loadCulturalSites(); // Events data loaded from cultural sites
$festivals = loadFestivals();
$hotels = loadHotels();
$restaurants = loadRestaurants();
$carouselSlides = loadCarouselSlides();

$message = '';
$messageType = '';

$currentTab = $_GET['tab'] ?? 'destinations';

function normalizeHotelCategory($category) {
    $value = trim((string)($category ?? ''));
    if ($value === '') {
        return 'N/A';
    }
    if (stripos($value, 'dot accredited') !== false) {
        return 'DOT Accredited';
    }
    if (stripos($value, 'locally certified') !== false) {
        return 'Locally Certified';
    }
    return $value;
}

// Handle POST requests - Toggle Featured ONLY (no delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;

    switch ($action) {
case 'toggle-featured':
            if ($currentTab === 'destinations' && $id !== null) {
                toggleDestinationFeatured($id);
                $message = 'Featured status updated!';
                $messageType = 'success';
                $destinations = loadDestinations(); // Reload
            }
            break;

        case 'toggle-featured-experience':
            if ($id !== null && isset($experiences[$id])) {
                $experiences[$id]['featured'] = !($experiences[$id]['featured'] ?? false);
                saveExperiences($experiences);
                $message = 'Featured status updated!';
                $messageType = 'success';
            }
            break;

        case 'toggle-carousel-active':
            if ($id !== null) {
                toggleCarouselSlideActive($id);
                $message = 'Slide visibility updated!';
                $messageType = 'success';
                $carouselSlides = loadCarouselSlides();
            }
            break;

    }
}

if (isset($_GET['message']) && $currentTab === 'carousel') {
    $msg = $_GET['message'];
    if ($msg === 'added') {
        $message = 'Carousel slide added successfully!';
        $messageType = 'success';
    } elseif ($msg === 'updated') {
        $message = 'Carousel slide updated successfully!';
        $messageType = 'success';
    } elseif ($msg === 'deleted') {
        $message = 'Carousel slide deleted.';
        $messageType = 'success';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tagum City</title>
    <link rel="stylesheet" href="../../css/admin.css">
</head>
<body>
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="admin-header-content">
            <div class="admin-title">
                <img src="../../images/TagumTourism.jpg" alt="Tagum City Logo" class="admin-logo">
                <span>Tourism Admin Dashboard</span>
            </div>
            <div class="admin-nav">
                <span class="admin-user">Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></span>
                <a href="logout.php" class="btn btn-primary tab-btn logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-container">
            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Tab Navigation Buttons -->
            <div class="tab-buttons">
                <a href="?tab=destinations" class="btn btn-primary tab-btn <?php echo $currentTab === 'destinations' ? 'active' : ''; ?>">Destinations</a>
                <a href="?tab=experiences" class="btn btn-primary tab-btn <?php echo $currentTab === 'experiences' ? 'active' : ''; ?>">Experiences</a>
                <a href="?tab=events" class="btn btn-primary tab-btn <?php echo $currentTab === 'events' ? 'active' : ''; ?>">Events</a>
                <a href="?tab=festivals" class="btn btn-primary tab-btn <?php echo $currentTab === 'festivals' ? 'active' : ''; ?>">Festivals</a>
                <a href="?tab=hotels" class="btn btn-primary tab-btn <?php echo $currentTab === 'hotels' ? 'active' : ''; ?>">Hotels</a>
                <a href="?tab=restaurants" class="btn btn-primary tab-btn <?php echo $currentTab === 'restaurants' ? 'active' : ''; ?>">Restaurants</a>
                <a href="?tab=carousel" class="btn btn-primary tab-btn <?php echo $currentTab === 'carousel' ? 'active' : ''; ?>">Carousel</a>
                <div class="admin-search-wrapper">
                    <input type="text" id="adminSearch" class="admin-search-input" placeholder="🔍 Search <?php echo ucfirst($currentTab); ?>..." onkeyup="filterAdminTable()">
                </div>
            </div>

            <?php if ($currentTab === 'destinations'): ?>
                <div class="dashboard-header">
                    <h2>Manage Destinations</h2>
                    <a href="add-destination.php" class="btn btn-primary">+ Add New Destination</a>
                </div>

                <div class="table-responsive">
                    <?php if (empty($destinations)): ?>
                        <div class="empty-state">
                            <p>📭 No destinations found</p>
                            <a href="add-destination.php" class="btn btn-primary">Add your first destination</a>
                        </div>
                    <?php else: ?>
                        <table class="destinations-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Featured</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($destinations as $destination): ?>
                                    <tr>
                                        <td class="table-image">
                                            <?php if (!empty($destination['image'])): ?>
                                                <img src="<?php echo htmlspecialchars($destination['image']); ?>" alt="<?php echo htmlspecialchars($destination['name']); ?>">
                                            <?php else: ?>
                                                <span class="no-image">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                             <strong><?php echo htmlspecialchars($destination['name']); ?></strong>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="toggle-featured">
                                                <input type="hidden" name="id" value="<?php echo $destination['id']; ?>">
                                                <button type="submit" class="featured-btn <?php echo ($destination['featured'] ?? false) ? 'active' : ''; ?>">
                                                    ⭐ <?php echo ($destination['featured'] ?? false) ? 'Featured' : 'Not Featured'; ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="action-buttons">
                                            <a href="add-destination.php?id=<?php echo $destination['id']; ?>" class="btn btn-small btn-edit">Edit</a>


</td>
</tr>
<?php endforeach; ?>

                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($currentTab === 'experiences'): ?>
                <div class="dashboard-header">
                    <h2>Manage Experiences</h2>
                    <a href="add-experience.php" class="btn btn-primary">+ Add New Experience</a>
                </div>

                <div class="table-responsive">
                    <?php if (empty($experiences)): ?>
                        <div class="empty-state">
                            <p>📭 No experiences found</p>
                            <a href="add-experience.php" class="btn btn-primary">Add your first experience</a>
                        </div>
                    <?php else: ?>
                        <table class="destinations-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Featured</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
<?php foreach ($experiences as $index => $experience): ?>


                                    <tr>
                                        <td class="table-image">
                                            <?php if (!empty($experience['image'])): ?>
                                                <img src="<?php echo htmlspecialchars($experience['image']); ?>" alt="<?php echo htmlspecialchars($experience['name']); ?>">
                                            <?php else: ?>
                                                <span class="no-image">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($experience['name']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="type-badge"><?php echo htmlspecialchars($experience['type'] ?? 'N/A'); ?></span>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($experience['date'] ?? 'N/A'); ?>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="toggle-featured-experience">
<input type="hidden" name="id" value="<?php echo $experience['id']; ?>">
                                                <button type="submit" class="featured-btn <?php echo ($experience['featured'] ?? false) ? 'active' : ''; ?>">
                                                    ⭐ <?php echo ($experience['featured'] ?? false) ? 'Featured' : 'Not Featured'; ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="action-buttons">
<a href="add-experience.php?id=<?php echo $experience['id']; ?>" class="btn btn-small btn-edit">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>



            <?php if ($currentTab === 'events'): ?>
                <div class="dashboard-header">
                    <h2>Manage Events</h2>
                    <a href="add-events.php" class="btn btn-primary">+ Add New Event</a>
                </div>

                <div class="table-responsive">
                    <?php if (empty($events)): ?>
                        <div class="empty-state">
                            <p>📅 No events found</p>
                            <a href="add-events.php" class="btn btn-primary">Add your first event</a>
                        </div>
                    <?php else: ?>
                        <table class="destinations-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($events as $index => $site): ?>
                                    <tr>
                <td class="table-image">
                                            <?php if (!empty($site['image'])): ?>
<img src="<?php echo htmlspecialchars($site['image']); ?>" alt="<?php echo htmlspecialchars($site['name']); ?>">
                                            <?php else: ?>
                                                <span class="no-image">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($site['name']); ?></strong>
                                        </td>
                                        <td>
                                            <?php
                                            if (!empty($site['event_date'])) {
                                                echo htmlspecialchars(date('M j, Y', strtotime($site['event_date'])));
                                            } else {
                                                echo '<span class="no-image">Not set</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($site['location'] ?? 'N/A'); ?>
                                        </td>

                                        <td class="action-buttons">
                                            <a href="add-events.php?id=<?php echo $site['id']; ?>" class="btn btn-small btn-edit">Edit</a>
</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($currentTab === 'festivals'): ?>
                <div class="dashboard-header">
                    <h2>Manage Festivals</h2>
                    <a href="add-festival.php" class="btn btn-primary">+ Add New Festival</a>
                </div>

                <div class="table-responsive">
                    <?php if (empty($festivals)): ?>
                        <div class="empty-state">
                            <p>🎉 No festivals found</p>
                            <a href="add-festival.php" class="btn btn-primary">Add your first festival</a>
                        </div>
                    <?php else: ?>
                        <table class="destinations-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Highlights</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($festivals as $index => $festival): ?>
                                    <tr>
                                        <td class="table-image">
                                            <?php if (!empty($festival['image'])): ?>
                                                <img src="<?php echo htmlspecialchars($festival['image']); ?>" alt="<?php echo htmlspecialchars($festival['name']); ?>">
                                            <?php else: ?>
                                                <span class="no-image">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($festival['name']); ?></strong>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($festival['date'] ?? 'N/A'); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars(substr($festival['highlights'] ?? '', 0, 50)); ?><?php echo strlen($festival['highlights'] ?? '') > 50 ? '...' : ''; ?>
                                        </td>
                                        <td class="action-buttons">
                                            <a href="add-festival.php?id=<?php echo $index; ?>" class="btn btn-small btn-edit">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($currentTab === 'hotels'): ?>
                <div class="dashboard-header">
                    <h2>Manage Hotels</h2>
                    <a href="add-hotel.php" class="btn btn-primary">+ Add New Hotel</a>
                </div>

                <div class="table-responsive">
                    <?php if (empty($hotels)): ?>
                        <div class="empty-state">
                            <p>🏨 No hotels found</p>
                            <a href="add-hotel.php" class="btn btn-primary">Add your first hotel</a>
                        </div>
                    <?php else: ?>
                        <table class="destinations-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($hotels as $hotel): ?>
                                    <tr>
                                        <td class="table-image">
                                            <?php if (!empty($hotel['image'])): ?>
                                                <img src="<?php echo htmlspecialchars($hotel['image']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
                                            <?php else: ?>
                                                <span class="no-image">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($hotel['name']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="type-badge"><?php echo htmlspecialchars(normalizeHotelCategory($hotel['category'] ?? '')); ?></span>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($hotel['price']); ?></strong>
                                        </td>
                                        <td class="action-buttons">
                                            <a href="add-hotel.php?id=<?php echo $hotel['id']; ?>" class="btn btn-small btn-edit">Edit</a>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($currentTab === 'restaurants'): ?>
                <div class="dashboard-header">
                    <h2>Manage Restaurants</h2>
                    <a href="add-restaurant.php" class="btn btn-primary">+ Add New Restaurant</a>
                </div>

                <div class="table-responsive">
                    <?php if (empty($restaurants)): ?>
                        <div class="empty-state">
                            <p>🍽️ No restaurants found</p>
                            <a href="add-restaurant.php" class="btn btn-primary">Add your first restaurant</a>
                        </div>
                    <?php else: ?>
                        <table class="destinations-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($restaurants as $restaurant): ?>
                                    <tr>
                                        <td class="table-image">
                                            <?php if (!empty($restaurant['image'])): ?>
                                                <img src="<?php echo htmlspecialchars($restaurant['image']); ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?>">
                                            <?php else: ?>
                                                <span class="no-image">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($restaurant['name']); ?></strong>
                                        </td>
                                        <td class="action-buttons">
                                            <a href="add-restaurant.php?id=<?php echo $restaurant['id']; ?>" class="btn btn-small btn-edit">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($currentTab === 'carousel'): ?>
                <div class="dashboard-header">
                    <h2>Manage Homepage Carousel</h2>
                    <a href="add-carousel-slide.php" class="btn btn-primary">+ Add Carousel Slide</a>
                </div>

                <div class="table-responsive">
                    <?php if (empty($carouselSlides)): ?>
                        <div class="empty-state">
                            <p>No carousel slides found</p>
                            <a href="add-carousel-slide.php" class="btn btn-primary">Add your first slide</a>
                        </div>
                    <?php else: ?>
                        <table class="destinations-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Main Heading</th>
                                    <th>Tagline</th>
                                    <th>Order</th>
                                    <th>Visible</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($carouselSlides as $carouselSlide): ?>
                                    <?php
                                    $carouselImg = $carouselSlide['image'] ?? '';
                                    if ($carouselImg && strpos($carouselImg, 'http') !== 0 && strpos($carouselImg, '../') !== 0) {
                                        $carouselImg = '../' . ltrim($carouselImg, '/');
                                    }
                                    $titlePreview = str_replace("\n", ' ', $carouselSlide['title'] ?? '');
                                    ?>
                                    <tr>
                                        <td class="table-image">
                                            <?php if (!empty($carouselSlide['image'])): ?>
                                                <img src="<?php echo htmlspecialchars($carouselImg); ?>" alt="<?php echo htmlspecialchars($titlePreview); ?>">
                                            <?php else: ?>
                                                <span class="no-image">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($titlePreview); ?></strong></td>
                                        <td><?php echo htmlspecialchars($carouselSlide['tagline'] ?? ''); ?></td>
                                        <td><?php echo (int) ($carouselSlide['sort_order'] ?? 0); ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="toggle-carousel-active">
                                                <input type="hidden" name="id" value="<?php echo $carouselSlide['id']; ?>">
                                                <button type="submit" class="featured-btn <?php echo !empty($carouselSlide['active']) ? 'active' : ''; ?>">
                                                    <?php echo !empty($carouselSlide['active']) ? 'Visible' : 'Hidden'; ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="action-buttons">
                                            <a href="add-carousel-slide.php?id=<?php echo $carouselSlide['id']; ?>" class="btn btn-small btn-edit">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="admin-footer">
    </footer>

    <script src="../assets/js/admin.js"></script>
    <script>
    function filterAdminTable() {
        const input = document.getElementById('adminSearch');
        const filter = input.value.toLowerCase();
        const table = document.querySelector('.destinations-table');
        if (!table) return;
        const tbody = table.getElementsByTagName('tbody')[0];
        const rows = tbody.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const rowText = rows[i].textContent.toLowerCase();
            if (rowText.indexOf(filter) > -1) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
    </script>
</body>
</html>
