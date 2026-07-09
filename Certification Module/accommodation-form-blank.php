<?php
require_once dirname(__DIR__) . '/includes/module_links.php';

$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Certification Application Form (Blank) - Tagum City</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/accommodation-form.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body class="cert-form-page">

<main class="cert-form-main">
    <div class="cert-form-container">
        <form class="cert-application-form" id="certApplicationForm">
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
                <p class="cert-form-note">Please supply all information required. Do not abbreviate. Place check marks in appropriate boxes and indicate "N/A" if not applicable.</p>
            </header>

            <section class="cert-form-section">
                <div class="cert-form-row cert-form-row-split">
                    <div class="cert-field">
                        <label for="application_date">Date of Application *</label>
                        <input type="date" id="application_date" name="application_date" class="cert-input">
                    </div>
                    <fieldset class="cert-field cert-radio-group">
                        <legend>Application Type *</legend>
                        <label class="cert-check-label">
                            <input type="radio" name="application_type" value="new">
                            New Application
                        </label>
                        <label class="cert-check-label">
                            <input type="radio" name="application_type" value="renewal">
                            Renewal
                        </label>
                    </fieldset>
                </div>
            </section>

            <section class="cert-form-section">
                <fieldset class="cert-field cert-radio-group">
                    <legend>Certification Track *</legend>
                    <label class="cert-check-label">
                        <input type="radio" name="certification_track" value="dot_accredited">
                        DOT Accredited
                    </label>
                    <label class="cert-check-label">
                        <input type="radio" name="certification_track" value="locally_certified">
                        Locally Certified
                    </label>
                </fieldset>
            </section>

            <section class="cert-form-section">
                <h2 class="cert-section-title">1. Name of Establishment *</h2>
                <input type="text" name="establishment_name" class="cert-input" placeholder="Full legal name of establishment">
            </section>

            <section class="cert-form-section">
                <h2 class="cert-section-title">2. Name of Owner *</h2>
                <input type="text" name="owner_name" class="cert-input" placeholder="Full name of owner">
            </section>

            <section class="cert-form-section">
                <h2 class="cert-section-title">3. Address *</h2>
                <input type="text" name="address" class="cert-input" placeholder="Complete business address">
            </section>

            <section class="cert-form-section">
                <h2 class="cert-section-title">4. Contact Information</h2>
                <div class="cert-form-grid">
                    <div class="cert-field">
                        <label for="telephone">4.1 Telephone</label>
                        <input type="tel" id="telephone" name="telephone" class="cert-input">
                    </div>
                    <div class="cert-field">
                        <label for="mobile_number">4.2 Mobile Number</label>
                        <input type="tel" id="mobile_number" name="mobile_number" class="cert-input">
                    </div>
                    <div class="cert-field">
                        <label for="email">4.3 Email Address</label>
                        <input type="email" id="email" name="email" class="cert-input">
                    </div>
                    <div class="cert-field">
                        <label for="facebook">4.4 Facebook Page / Messenger</label>
                        <input type="text" id="facebook" name="facebook" class="cert-input">
                    </div>
                </div>
            </section>

            <section class="cert-form-section">
                <h2 class="cert-section-title">5. Category *</h2>
                <div class="cert-category-grid">
                    <?php
                    $categories = ['Hotel', 'Resort', 'Apartment Hotel', 'Mabuhay Accommodation', 'Others'];
                    foreach ($categories as $cat):
                    ?>
                    <label class="cert-check-label cert-category-option">
                        <input type="radio" name="category" value="<?php echo htmlspecialchars($cat); ?>">
                        <?php echo htmlspecialchars($cat); ?>
                    </label>
                    <?php endforeach; ?>
                </div>
                <div class="cert-field" style="margin-top: 10px;">
                    <label for="other_category_text">If Others, please specify:</label>
                    <input type="text" id="other_category_text" name="other_category_text" class="cert-input" placeholder="Specify other category">
                </div>
            </section>

            <section class="cert-form-section">
                <h2 class="cert-section-title">6. Specific Details</h2>
                <div class="cert-form-grid cert-form-grid-3">
                    <div class="cert-field">
                        <label for="total_rooms">6.1 Total No. of Rooms</label>
                        <input type="number" id="total_rooms" name="total_rooms" min="0" class="cert-input">
                    </div>
                    <div class="cert-field">
                        <label for="total_capacity">6.2 Total Capacity No.</label>
                        <input type="number" id="total_capacity" name="total_capacity" min="0" class="cert-input">
                    </div>
                    <div class="cert-field">
                        <label for="total_employees">6.3 Total No. of Employees</label>
                        <input type="number" id="total_employees" name="total_employees" min="0" class="cert-input">
                    </div>
                </div>
                <div class="cert-form-grid cert-form-grid-3">
                    <div class="cert-field">
                        <label for="male_employees">Male Employee</label>
                        <input type="number" id="male_employees" name="male_employees" min="0" class="cert-input">
                    </div>
                    <div class="cert-field">
                        <label for="female_employees">Female Employee</label>
                        <input type="number" id="female_employees" name="female_employees" min="0" class="cert-input">
                    </div>
                    <div class="cert-field">
                        <label for="year_started">6.4 Year Started/Established</label>
                        <input type="text" id="year_started" name="year_started" class="cert-input" placeholder="YYYY or year established">
                    </div>
                </div>
                    <div class="cert-field">
                        <label for="male_employees">Male Employee</label>
                        <input type="number" id="male_employees" name="male_employees" min="0" class="cert-input">
                    </div>
                    <div class="cert-field">
                        <label for="female_employees">Female Employee</label>
                        <input type="number" id="female_employees" name="female_employees" min="0" class="cert-input">
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
                                <td><input type="text" name="room_type_name[]" class="cert-input cert-table-input"></td>
                                <td><input type="text" name="room_type_rate[]" class="cert-input cert-table-input"></td>
                                <td><input type="number" name="room_type_number[]" min="0" class="cert-input cert-table-input"></td>
                                <td></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
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
                                <td><input type="text" name="amenity_name[]" class="cert-input cert-table-input" placeholder="e.g., Swimming Pool"></td>
                                <td></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
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
                            <li>Valid Mayor's Permit and/or Business License</li>
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
                    <div class="cert-form-grid cert-form-grid-2">
                        <div class="cert-field">
                            <label for="previous_certificate">Previous Certificate / Sticker No.</label>
                            <input type="text" id="previous_certificate" name="previous_certificate" class="cert-input">
                        </div>
                        <div class="cert-field">
                            <label for="renewal_date">Renewal Application Date</label>
                            <input type="date" id="renewal_date" name="renewal_date" class="cert-input">
                        </div>
                    </div>
                </div>
            </section>

            <section class="cert-form-section cert-certification">
                <p class="cert-certify-text">I certify further that all the foregoing data and documents supporting this application are true and correct.</p>
                <div class="cert-field">
                    <label for="applicant_signature">Applicant Signature (Type Full Name) *</label>
                    <input type="text" id="applicant_signature" name="applicant_signature" class="cert-input cert-signature-input" placeholder="Full name as signature">
                </div>
            </section>

            <div class="cert-form-actions">
                <button type="button" class="btn btn-primary cert-submit-btn" onclick="downloadPDF()">Download as PDF</button>
                <button type="button" class="btn btn-secondary cert-cancel-btn" onclick="window.print()">Print</button>
                <button type="button" class="btn btn-secondary cert-cancel-btn" onclick="window.close()">Close</button>
            </div>
        </form>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const applicationTypeInputs = document.querySelectorAll('input[name="application_type"]');
    const renewalFields = document.getElementById('renewalFields');

    function toggleRenewalFields() {
        const selected = document.querySelector('input[name="application_type"]:checked');
        const isRenewal = selected && selected.value === 'renewal';
        if (renewalFields) {
            renewalFields.classList.toggle('is-visible', isRenewal);
        }
    }

    applicationTypeInputs.forEach((input) => {
        input.addEventListener('change', toggleRenewalFields);
    });
});

function downloadPDF() {
    const element = document.getElementById('certApplicationForm');
    const formActions = document.querySelector('.cert-form-actions');
    
    // Hide action buttons for PDF
    if (formActions) {
        formActions.style.display = 'none';
    }

    const opt = {
        margin: 0.5,
        filename: 'Application Form.docx.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save().then(() => {
        // Show action buttons again
        if (formActions) {
            formActions.style.display = 'flex';
        }
    });
}
</script>
</body>
</html>
