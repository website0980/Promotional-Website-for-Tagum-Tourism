<?php
require_once __DIR__ . '/process-application.php';
require_once dirname(__DIR__) . '/includes/module_links.php';

$hotelsBackUrl = '../Hotel Module/hotels.php?tab=local';

$formData = $_POST ?? [];
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Certification Application - Tagum City</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/mobile-navbar.css">
    <link rel="stylesheet" href="../css/accommodation-form.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body class="cert-form-page">
<?php include dirname(__DIR__) . '/navbar.php'; ?>

<main class="cert-form-main">

    <div class="cert-form-container">
        <a href="<?php echo htmlspecialchars($hotelsBackUrl); ?>" class="cert-back-link">← Back to Hotels</a>

        <?php if (!empty($errors)): ?>
            <div class="cert-alert cert-alert-error" role="alert">
                <strong>Please correct the following:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="accommodation-form.php" class="cert-application-form" id="certApplicationForm" novalidate>
            <header class="cert-form-header">
                <div class="cert-form-header-top">
                    <img src="../images/TagumTourism.jpg" alt="City of Tagum" class="cert-form-logo">
                    <div class="cert-form-header-text">
                        <p class="cert-republic">Republic of the Philippines</p>
                        <p class="cert-province">Province of Davao del Norte</p>
                        <p class="cert-city">City of Tagum</p>
                    </div>
                </div>
                <div class="cert-office-banner">City Tourism and Cultural Office</div>
                <h1 class="cert-form-title">Local Certification Application Form</h1>
                <p class="cert-form-subtitle">Tourism Accommodation Establishment</p>
                <p class="cert-form-note">Please supply all information required. Do not abbreviate. Place check marks in appropriate boxes and indicate &ldquo;N/A&rdquo; if not applicable.</p>
            </header>

            <section class="cert-form-section">
                <div class="cert-form-row cert-form-row-split">
                    <fieldset class="cert-field cert-radio-group">
                        <legend>Application Type *</legend>
                        <label class="cert-check-label">
                            <input type="radio" name="application_type" value="new" required
                                <?php echo (($formData['application_type'] ?? '') === 'new') ? 'checked' : ''; ?>>
                            New Application
                        </label>
                        <label class="cert-check-label">
                            <input type="radio" name="application_type" value="renewal"
                                <?php echo (($formData['application_type'] ?? '') === 'renewal') ? 'checked' : ''; ?>>
                            Renewal
                        </label>
                    </fieldset>
                </div>
            </section>

            <section class="cert-form-section">
                <fieldset class="cert-field cert-radio-group">
                    <legend>Certification Track *</legend>
                    <label class="cert-check-label">
                        <input type="radio" name="certification_track" value="dot_accredited" required
                            <?php echo (($formData['certification_track'] ?? '') === 'dot_accredited') ? 'checked' : ''; ?>>
                        DOT Accredited
                    </label>
                    <label class="cert-check-label">
                        <input type="radio" name="certification_track" value="locally_certified" required
                            <?php echo (($formData['certification_track'] ?? '') === 'locally_certified') ? 'checked' : ''; ?>>
                        Locally Certified
                    </label>
                </fieldset>
            </section>

            <section class="cert-form-section">
                <h2 class="cert-section-title">1. Name of Establishment *</h2>
                <input type="text" name="establishment_name" required class="cert-input"
                       value="<?php echo htmlspecialchars($formData['establishment_name'] ?? ''); ?>"
                       placeholder="Full legal name of establishment">
            </section>

            <section class="cert-form-section">
                <h2 class="cert-section-title">2. Name of Owner *</h2>
                <input type="text" name="owner_name" required class="cert-input"
                       value="<?php echo htmlspecialchars($formData['owner_name'] ?? ''); ?>"
                       placeholder="Full name of owner">
            </section>

            <section class="cert-form-section">
                <h2 class="cert-section-title">3. Address *</h2>
                <input type="text" name="address" required class="cert-input"
                       value="<?php echo htmlspecialchars($formData['address'] ?? ''); ?>"
                       placeholder="Complete business address">
            </section>

            <section class="cert-form-section">
                <h2 class="cert-section-title">4. Contact Information</h2>
                <div class="cert-form-grid">
                    <div class="cert-field">
                        <label for="telephone">4.1 Telephone</label>
                        <input type="tel" id="telephone" name="telephone" class="cert-input"
                               value="<?php echo htmlspecialchars($formData['telephone'] ?? ''); ?>">
                    </div>
                    <div class="cert-field">
                        <label for="mobile_number">4.2 Mobile Number</label>
                        <input type="tel" id="mobile_number" name="mobile_number" class="cert-input"
                               value="<?php echo htmlspecialchars($formData['mobile_number'] ?? ''); ?>">
                    </div>
                    <div class="cert-field">
                        <label for="email">4.3 Email Address</label>
                        <input type="email" id="email" name="email" class="cert-input"
                               value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>">
                    </div>
                    <div class="cert-field">
                        <label for="facebook">4.4 Facebook Page / Messenger</label>
                        <input type="text" id="facebook" name="facebook" class="cert-input"
                               value="<?php echo htmlspecialchars($formData['facebook'] ?? ''); ?>">
                    </div>
                </div>
            </section>

            <section class="cert-form-section">
                <h2 class="cert-section-title">5. Category *</h2>
                <div class="cert-category-grid">
                    <?php
                    $categories = ['Hotel', 'Resort', 'Apartment Hotel', 'Mabuhay Accommodation'];
                    $selectedCategory = $formData['category'] ?? '';
                    foreach ($categories as $cat):
                    ?>
                    <label class="cert-check-label cert-category-option">
                        <input type="radio" name="category" value="<?php echo htmlspecialchars($cat); ?>" required
                            <?php echo ($selectedCategory === $cat) ? 'checked' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="cert-form-section">
                <h2 class="cert-section-title">6. Specific Details</h2>
                <div class="cert-form-grid cert-form-grid-3">
                    <div class="cert-field">
                        <label for="total_rooms">6.1 Total No. of Rooms</label>
                        <input type="number" id="total_rooms" name="total_rooms" min="0" class="cert-input"
                               value="<?php echo htmlspecialchars($formData['total_rooms'] ?? ''); ?>">
                    </div>
                    <div class="cert-field">
                        <label for="total_capacity">6.2 Total Capacity No.</label>
                        <input type="number" id="total_capacity" name="total_capacity" min="0" class="cert-input"
                               value="<?php echo htmlspecialchars($formData['total_capacity'] ?? ''); ?>">
                    </div>
                    <div class="cert-field">
                        <label for="total_employees">6.3 Total No. of Employees</label>
                        <input type="number" id="total_employees" name="total_employees" min="0" class="cert-input"
                               value="<?php echo htmlspecialchars($formData['total_employees'] ?? ''); ?>">
                    </div>
                </div>
                <div class="cert-form-grid cert-form-grid-2 cert-employee-split">
                    <div class="cert-field">
                        <label for="male_employees">Male Employee</label>
                        <input type="number" id="male_employees" name="male_employees" min="0" class="cert-input"
                               value="<?php echo htmlspecialchars($formData['male_employees'] ?? ''); ?>">
                    </div>
                    <div class="cert-field">
                        <label for="female_employees">Female Employee</label>
                        <input type="number" id="female_employees" name="female_employees" min="0" class="cert-input"
                               value="<?php echo htmlspecialchars($formData['female_employees'] ?? ''); ?>">
                    </div>
                </div>

                <h3 class="cert-subsection-title">Room Types</h3>
                <div class="cert-table-wrap">
                    <table class="cert-data-table" id="roomTypesTable">
                        <thead>
                            <tr>
                                <th>Type of Rooms (e.g., Deluxe, Standard)</th>
                                <th>Rates</th>
                                <th>Number</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < 4; $i++): ?>
                            <tr>
                                <td><input type="text" name="room_type_name[]" class="cert-input cert-table-input"
                                    value="<?php echo htmlspecialchars($formData['room_type_name'][$i] ?? ''); ?>"></td>
                                <td><input type="text" name="room_type_rate[]" class="cert-input cert-table-input"
                                    value="<?php echo htmlspecialchars($formData['room_type_rate'][$i] ?? ''); ?>"></td>
                                <td><input type="number" name="room_type_number[]" min="0" class="cert-input cert-table-input"
                                    value="<?php echo htmlspecialchars($formData['room_type_number'][$i] ?? ''); ?>"></td>
                                <td></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                    <button type="button" class="cert-btn-secondary" id="addRoomRow">+ Add Room Type</button>
                </div>

                <h3 class="cert-subsection-title">Amenities (Swimming Pool, Bar, Restaurant, etc.)</h3>
                <div class="cert-table-wrap">
                    <table class="cert-data-table" id="amenitiesTable">
                        <thead>
                            <tr>
                                <th>Amenity</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < 3; $i++): ?>
                            <tr>
                                <td><input type="text" name="amenity_name[]" class="cert-input cert-table-input"
                                    value="<?php echo htmlspecialchars($formData['amenity_name'][$i] ?? ''); ?>"
                                    placeholder="e.g., Swimming Pool"></td>
                                <td></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                    <button type="button" class="cert-btn-secondary" id="addAmenityRow">+ Add Amenity</button>
                </div>
            </section>

            <section class="cert-form-section cert-requirements">
                <h2 class="cert-section-title">7. Submission Requirements</h2>
                <p class="cert-requirements-intro">Please prepare the following documents when submitting your application to the City Tourism and Cultural Office:</p>
                <ol class="cert-requirements-list">
                    <li><strong>7.1</strong> Picture of Establishment (Rooms and Amenities)</li>
                    <li><strong>7.2</strong> Permit Application Form</li>
                    <li><strong>7.3</strong> Pre-requisites:
                        <ul>
                            <li>Valid Mayor&rsquo;s Permit and/or Business License</li>
                            <li>Valid Comprehensive General Liability Insurance Policy (minimum P200,000 coverage for New Applications)</li>
                        </ul>
                    </li>
                    <li><strong>7.4</strong> Other Requirements:
                        <ul>
                            <li>Fire Safety Inspection Certificate</li>
                            <li>Other requirements prescribed by the Business License Office (e.g., DTI/SEC registration, Articles of Incorporation/Cooperation)</li>
                        </ul>
                    </li>
                </ol>
                <p class="cert-renewal-note"><strong>For Renewal Application:</strong> Previous Certificate/Sticker and Application Date.</p>

                <div class="cert-renewal-fields" id="renewalFields">
                    <div class="cert-form-grid cert-form-grid-1">
                        <div class="cert-field">
                            <label for="previous_certificate">Previous Certificate / Sticker No.</label>
                            <input type="text" id="previous_certificate" name="previous_certificate" class="cert-input"
                                   value="<?php echo htmlspecialchars($formData['previous_certificate'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
            </section>

            <div class="cert-form-actions">
                <button type="submit" class="btn btn-primary cert-submit-btn">Submit Application</button>
                <button type="button" class="btn btn-secondary cert-download-btn" onclick="downloadBlankForm()">Download Form (PDF)</button>
                <a href="<?php echo htmlspecialchars($hotelsBackUrl); ?>" class="btn btn-secondary cert-cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</main>

<script src="../js/accommodation-form.js"></script>
</body>
</html>
