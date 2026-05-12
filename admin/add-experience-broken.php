<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Add/Edit Experience Page
require_once 'config.php';
requireAuth();

$experiences = loadExperiences();
$isEdit = false;
$experience = [
    'id' => '',
    'name' => '',
    'type' => '',
    'description' => '',
    'image' => '',
    'date' => '',
    'featured' => false
];

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if ($id !== null && $id !== '') {
    $id = (int) $id;
}
$errors = [];
    $message = '';

if ($id !== null && $id !== '' && isset($experiences[$id])) {
    $isEdit = true;
    $experience = $experiences[$id];
}

// Handle form submission (with debug)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; margin: 20px 0; border-radius: 8px;"><h3>🛠 DEBUG - POST RECEIVED</h3>';
    echo '<p><strong>ID:</strong> ' . htmlspecialchars($id ?? 'null') . ' | <strong>Edit:</strong> ' . ($isEdit ? 'YES' : 'NO') . '</p>';
    echo '<p><strong>POST:</strong><pre style="background:#f8f9fa;">' . print_r($_POST, true) . '</pre></p>';
    
    $debugExperience = [
        'id' => $_POST['id'] ?? time(),
        'name' => $_POST['name'] ?? '',
        'type' => $_POST['type'] ?? '',
        'description' => $_POST['description'] ?? '',
        'date' => $_POST['date'] ?? '',
        'image' => $_POST['image'] ?? '',
        'featured' => isset($_POST['featured']) ? true : false
    ];
    echo '<p><strong>Data to save:</strong><pre>' . print_r($debugExperience, true) . '</pre></p>';
    echo '<div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; margin: 20px 0; border-radius: 8px;"><h3>🛠 DEBUG INFO (Remove after fix):</h3>';
    echo '<p><strong>Is Edit Mode:</strong> ' . ($isEdit ? 'YES (ID=' . $id . ')' : 'NO') . '</p>';
    echo '<p><strong>POST Data:</strong><pre>' . print_r($_POST, true) . '</pre></p>';
    
    // Rest of POST code here...
    $experience = [
        'id' => $_POST['id'] ?? time(),
        'name' => $_POST['name'] ?? '',
        'type' => $_POST['type'] ?? '',
        'description' => $_POST['description'] ?? '',
        'date' => $_POST['date'] ?? '',
        'image' => $_POST['image'] ?? '',
        'featured' => isset($_POST['featured']) ? true : false
    ];

    echo '<p><strong>Processed Data:</strong><pre>' . print_r($experience, true) . '</pre></p>';

    // Validate image file if uploaded (do not save/delete until validation passes)
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $validation = validateImageUpload($_FILES['image_file']);
        echo '<p><strong>Image Validation:</strong> ' . ($validation['success'] ? 'OK' : 'FAILED: ' . $validation['error']) . '</p>';
        if (!$validation['success']) {
            $errors[] = $validation['error'];
        }
    }

    echo '<p>🔍 Running validation...</p>';
    // Validation
    if (empty($experience['name'])) $errors[] = 'Experience name is required';
    if (empty($experience['type'])) $errors[] = 'Experience type is required';
    if (empty($experience['description'])) $errors[] = 'Description is required';
    if (empty($experience['date'])) $errors[] = 'Date is required';

    echo '<p><strong>Validation Errors (' . count($errors) . '):</strong><pre>' . print_r($errors, true) . '</pre></p>';

    // Save if no errors (file operations only after validation)
    if (empty($errors)) {
        echo '<p><strong>Proceeding to SAVE...</strong></p>';
        
        $beforeCount = count($experiences);
        echo '<p><strong>Experiences before save:</strong> ' . $beforeCount . '</p>';

        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $result = saveExperienceImage($_FILES['image_file']);
            echo '<p><strong>Image Save Result:</strong> ' . ($result['success'] ? 'SUCCESS: ' . $result['path'] : 'FAILED: ' . ($result['error'] ?? 'Unknown')) . '</p>';
            if ($result['success']) {
                if (!empty($experience['image'])) {
                    deleteExperienceImage($experience['image']);
                    echo '<p>Deleted old image.</p>';
                }
                $experience['image'] = $result['path'];
            } else {
                $errors[] = $result['error'] ?? 'Image upload failed';
            }
        }
        
        if (empty($errors)) {
            if ($isEdit) {
                $experiences[$id] = $experience;
                echo '<p>Updated index ' . $id . '</p>';
            } else {
                $experiences[] = $experience;
                echo '<p>Added new at index ' . (count($experiences)-1) . '</p>';
            }
            
            $saveResult = saveExperiences($experiences);
            $afterCount = count($experiences);
            echo '<p><strong>Save Result:</strong> ' . ($saveResult ? 'SUCCESS (now ' . $afterCount . ' items)' : 'FAILED') . '</p>';
            
            if ($saveResult) {
                echo '<p><strong>✅ SUCCESS - Should redirect now!</strong></p>';
                // Comment out redirect for debug
                //header('Location: dashboard.php?tab=experiences&message=' . ($isEdit ? 'updated' : 'added'));
                //exit();
                echo '</div>';
            } else {
                echo '<p><strong>❌ SAVE FAILED!</strong></p>';
                echo '</div>';
            }
        } else {
            echo '<p><strong>❌ Validation failed - not saving.</strong></p>';
            echo '</div>';
        }
    } else {
        echo '<p><strong>❌ Errors before save:</strong></p>';
        echo '</div>';
    }
    // End debug - normal flow continues below
}
    $experience = [
        'id' => $_POST['id'] ?? time(),
        'name' => $_POST['name'] ?? '',
        'type' => $_POST['type'] ?? '',
        'description' => $_POST['description'] ?? '',
        'date' => $_POST['date'] ?? '',
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
    if (empty($experience['name'])) $errors[] = 'Experience name is required';
    if (empty($experience['type'])) $errors[] = 'Experience type is required';
    if (empty($experience['description'])) $errors[] = 'Description is required';
    if (empty($experience['date'])) $errors[] = 'Date is required';

    // Save if no errors (file operations only after validation)
    if (empty($errors)) {
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $result = saveExperienceImage($_FILES['image_file']);
            if ($result['success']) {
                if (!empty($experience['image'])) {
                    deleteExperienceImage($experience['image']);
                }
                $experience['image'] = $result['path'];
            } else {
                $errors[] = $result['error'] ?? 'Image upload failed';
            }
        }
        if (empty($errors)) {
            if ($isEdit) {
                $experiences[$id] = $experience;
            } else {
                $experiences[] = $experience;
            }
            
            saveExperiences($experiences);
            header('Location: dashboard.php?tab=experiences&message=' . ($isEdit ? 'updated' : 'added'));
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Experience - Tagum Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="admin-header-content">
            <div class="admin-title">
                <a href="dashboard.php?tab=experiences" class="back-link">← Dashboard</a>
                <h1><?php echo $isEdit ? 'Edit Experience' : 'Add New Experience'; ?></h1>
            </div>
            <div class="admin-nav">
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
            
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="alert alert-info">
                <p><strong>Debug session complete.</strong> Check above for detailed info.</p>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" enctype="multipart/form-data" class="destination-form">
                <?php if ($isEdit && $id !== null && $id !== ''): ?>
                <input type="hidden" name="id" value="<?php echo (int) $id; ?>">
                <?php endif; ?>
                <div class="form-section">
                    <h2>Basic Information</h2>
                    
                    <div class="form-group">
                        <label for="name">Experience Name *</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="<?php echo htmlspecialchars($experience['name']); ?>" 
                            required
                            class="form-control"
                            placeholder="e.g., River Tours"
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="type">Experience Type *</label>
<input 
                            type="text" 
                            id="type" 
                            name="type" 
                            value="<?php echo htmlspecialchars($experience['type']); ?>" 
                            required
                            class="form-control"
                            placeholder="Type your experience type (e.g. river-tours, wellness)"
                        >
                        </div>

                        <div class="form-group">
                            <label for="date">Date *</label>
                            <input 
                                type="date" 
                                id="date" 
                                name="date" 
                                value="<?php echo htmlspecialchars($experience['date'] ?? ''); ?>" 
                                required
                                class="form-control"
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
                            placeholder="Brief description of the experience"
                        ><?php echo htmlspecialchars($experience['description']); ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Media & Settings</h2>
                    <?php $image = $experience['image'] ?? ''; ?>                       <?php include 'media-picker.php'; ?> 

                    <div class="form-group checkbox">
                        <input 
                            type="checkbox" 
                            id="featured" 
                            name="featured" 
                            <?php echo ($experience['featured'] ?? false) ? 'checked' : ''; ?>
                        >
                        <label for="featured">Mark as Featured Experience</label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $isEdit ? '✏️ Update Experience' : '➕ Add Experience'; ?>
                        </button>
                        <a href="dashboard.php?tab=experiences" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="admin-footer">
        <p></p>
    </footer>

    <script src="../assets/js/admin.js"></script>
</body>
</html>

