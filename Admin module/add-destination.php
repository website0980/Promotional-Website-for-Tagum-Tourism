<?php
// Add/Edit Destination Page
require_once 'config.php';
requireAuth();

$destinations = loadDestinations();
$isEdit = false;
$destinationIndex = $_GET['id'] ?? $_POST['id'] ?? null;
if ($destinationIndex !== null && $destinationIndex !== '') {
    $destinationIndex = (int) $destinationIndex;
}
$errors = [];
$message = '';

$destination = [
    'name' => '',
    'type' => '',
    'description' => '',
    'location' => '',
    'accessibility' => '',
    'features' => '',
    'facilities' => '',
    'entrance_fee' => '',
    'contact' => '',
    'best_time' => '',
    'what_to_pack' => '',
    'visiting_rules' => '',
    'image' => '',
    'featured' => false
];

// If editing, load the destination
if ($destinationIndex !== null && $destinationIndex !== '' && isset($destinations[$destinationIndex])) {
    $isEdit = true;
    $destination = $destinations[$destinationIndex];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destination = [
        'name' => $_POST['name'] ?? '',
        'type' => $_POST['type'] ?? '',
        'description' => $_POST['description'] ?? '',
        'location' => $_POST['location'] ?? '',
        'accessibility' => $_POST['accessibility'] ?? '',
        'features' => $_POST['features'] ?? '',
        'facilities' => $_POST['facilities'] ?? '',
        'entrance_fee' => $_POST['entrance_fee'] ?? '',
        'contact' => $_POST['contact'] ?? '',
        'best_time' => $_POST['best_time'] ?? '',
        'what_to_pack' => $_POST['what_to_pack'] ?? '',
        'visiting_rules' => $_POST['visiting_rules'] ?? '',
        'image' => $_POST['image'] ?? '',
        'featured' => isset($_POST['featured']) ? true : false
    ];

    // Validate image file if uploaded (do not save/delete until validation passes)
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $validation = validateImageUpload($_FILES['image_file']);
        if (!$validation['success']) {
            $errors[] = $validation['error'];
        }
    }

    // Validation
    if (empty($destination['name'])) $errors[] = 'Destination name is required';
    if (empty($destination['type'])) $errors[] = 'Destination type is required';
    if (empty($destination['description'])) $errors[] = 'Description is required';

    // Save if no errors (file operations only after validation)
    if (empty($errors)) {
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $result = saveUploadedImage($_FILES['image_file']);
            if ($result['success']) {
                if (!empty($destination['image'])) {
                    deleteImage($destination['image']);
                }
                $destination['image'] = $result['path'];
            } else {
                $errors[] = $result['error'] ?? 'Image upload failed';
            }
        }
        if (empty($errors)) {
            if ($isEdit) {
                $destinations[$destinationIndex] = $destination;
            } else {
                $destinations[] = $destination;
            }
            
            saveDestinations($destinations);
            header('Location: dashboard.php?tab=destinations&message=' . ($isEdit ? 'updated' : 'added'));
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Destination - Tagum Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="admin-header-content">
            <div class="admin-title">
                <a href="dashboard.php?tab=destinations" class="back-link">← Dashboard</a>
                <h1><?php echo $isEdit ? 'Edit' : 'Add New'; ?> Destination</h1>
            </div>
            <div class="admin-nav">
                <a href="logout.php" class="btn btn-primary tab-btn logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-container">
            <!-- Errors -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" enctype="multipart/form-data" class="destination-form">
                <?php if ($isEdit && $destinationIndex !== null && $destinationIndex !== ''): ?>
                <input type="hidden" name="id" value="<?php echo (int) $destinationIndex; ?>">
                <?php endif; ?>
                <div class="form-section">
                    <h2>Basic Information</h2>
                    
                    <div class="form-group">
                        <label for="name">Destination Name *</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="<?php echo htmlspecialchars($destination['name']); ?>" 
                            required
                            class="form-control"
                            placeholder="e.g., Pumauna Waterfalls"
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="type">Destination Type *</label>
                            <select id="type" name="type" required class="form-control">
                                <option value="">-- Select Type --</option>
                                <option value="Natural Wonder" <?php echo $destination['type'] === 'Natural Wonder' ? 'selected' : ''; ?>>Natural Wonder</option>
                                <option value="Cultural Site" <?php echo $destination['type'] === 'Cultural Site' ? 'selected' : ''; ?>>Cultural Site</option>
                                <option value="Adventure" <?php echo $destination['type'] === 'Adventure' ? 'selected' : ''; ?>>Adventure</option>
                                <option value="Historical" <?php echo $destination['type'] === 'Historical' ? 'selected' : ''; ?>>Historical</option>
                                <option value="Religious" <?php echo $destination['type'] === 'Religious' ? 'selected' : ''; ?>>Religious</option>
                                <option value="Museum" <?php echo $destination['type'] === 'Museum' ? 'selected' : ''; ?>>Museum</option>
                                <option value="Festival" <?php echo $destination['type'] === 'Festival' ? 'selected' : ''; ?>>Festival</option>
                                <option value="Local Cuisine" <?php echo $destination['type'] === 'Local Cuisine' ? 'selected' : ''; ?>>Local Cuisine</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="entrance_fee">Entrance Fee</label>
                            <input 
                                type="text" 
                                id="entrance_fee" 
                                name="entrance_fee" 
                                value="<?php echo htmlspecialchars($destination['entrance_fee']); ?>" 
                                class="form-control"
                                placeholder="e.g., ₱50 per person"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            required
                            class="form-control form-textarea"
                            rows="4"
                            placeholder="Brief description of the destination"
                        ><?php echo htmlspecialchars($destination['description']); ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Destination Details</h2>
                    
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input 
                            type="text" 
                            id="location" 
                            name="location" 
                            value="<?php echo htmlspecialchars($destination['location']); ?>" 
                            class="form-control"
                            placeholder="e.g., 30km north of Tagum City"
                        >
                    </div>

                    <div class="form-group">
                        <label for="accessibility">Accessibility</label>
                        <input 
                            type="text" 
                            id="accessibility" 
                            name="accessibility" 
                            value="<?php echo htmlspecialchars($destination['accessibility']); ?>" 
                            class="form-control"
                            placeholder="e.g., 45 minutes by car"
                        >
                    </div>

                    <div class="form-group">
                        <label for="features">Main Features</label>
                        <textarea 
                            id="features" 
                            name="features" 
                            class="form-control form-textarea"
                            rows="3"
                            placeholder="Separate features with line breaks"
                        ><?php echo htmlspecialchars($destination['features']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="facilities">Facilities</label>
                        <textarea 
                            id="facilities" 
                            name="facilities" 
                            class="form-control form-textarea"
                            rows="3"
                            placeholder="Separate facilities with line breaks"
                        ><?php echo htmlspecialchars($destination['facilities']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="contact">Contact Information</label>
                        <input 
                            type="text" 
                            id="contact" 
                            name="contact" 
                            value="<?php echo htmlspecialchars($destination['contact']); ?>" 
                            class="form-control"
                            placeholder="e.g., +63-904-555-0123"
                        >
                    </div>
                </div>

                <div class="form-section">
                    <h2>Travel Information</h2>
                    
                    <div class="form-group">
                        <label for="best_time">Best Time to Visit</label>
                        <textarea 
                            id="best_time" 
                            name="best_time" 
                            class="form-control form-textarea"
                            rows="4"
                            placeholder="Include weather, crowds, seasonal info. Separate sections with line breaks"
                        ><?php echo htmlspecialchars($destination['best_time']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="what_to_pack">What to Pack</label>
                        <textarea 
                            id="what_to_pack" 
                            name="what_to_pack" 
                            class="form-control form-textarea"
                            rows="4"
                            placeholder="List items to pack. Separate with line breaks"
                        ><?php echo htmlspecialchars($destination['what_to_pack']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="visiting_rules">Visiting Rules & Guidelines</label>
                        <textarea 
                            id="visiting_rules" 
name="visiting_rules" 

                            class="form-control form-textarea"
                            rows="4"
                            placeholder="List rules and guidelines. Separate with line breaks"
                        ><?php echo htmlspecialchars($destination['visiting_rules']); ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Media & Settings</h2>
                    <?php $image = $destination['image'] ?? ''; ?>\n                    <?php include '../admin/media-picker.php'; ?> 

                    <div class="form-group checkbox">
                        <input 
                            type="checkbox" 
                            id="featured" 
                            name="featured" 
                            <?php echo ($destination['featured'] ?? false) ? 'checked' : ''; ?>
                        >
                        <label for="featured">Mark as Featured Destination</label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $isEdit ? '✏️ Update Destination' : '➕ Add Destination'; ?>
                        </button>
                        <a href="dashboard.php?tab=destinations" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="admin-footer">
        <p>&copy; 2026 Tagum City Admin. All rights reserved.</p>
    </footer>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
