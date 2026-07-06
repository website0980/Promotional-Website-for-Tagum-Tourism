<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Form - Tagum City</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            padding: 15px;
            background: white;
        }
        .form-container {
            max-width: 100%;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        .header-top {
            font-size: 9px;
            margin-bottom: 3px;
        }
        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin: 3px 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 11px;
            margin: 2px 0;
            font-weight: bold;
        }
        .header p {
            font-size: 9px;
            margin: 1px 0;
        }
        .section {
            margin-bottom: 8px;
            border: 1px solid #000;
            padding: 5px;
        }
        .section-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 5px;
            background: #e8e8e8;
            padding: 3px 5px;
            border-bottom: 1px solid #000;
        }
        .form-row {
            display: flex;
            gap: 8px;
            margin-bottom: 4px;
            align-items: center;
        }
        .form-col {
            flex: 1;
        }
        label {
            display: block;
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="number"],
        select {
            width: 100%;
            padding: 2px 4px;
            font-size: 9px;
            border: 1px solid #000;
            height: 18px;
        }
        .radio-group label {
            display: inline;
            margin-right: 15px;
            font-weight: normal;
            font-size: 9px;
        }
        .radio-group input {
            margin-right: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin-top: 3px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        td input {
            width: 100%;
            height: 25px;
            border: none;
            font-size: 9px;
        }
        th {
            background: #e8e8e8;
            font-weight: bold;
        }
        .requirements-list {
            font-size: 8px;
            margin-left: 15px;
            margin-top: 3px;
        }
        .requirements-list li {
            margin-bottom: 2px;
        }
        .certify-text {
            font-size: 9px;
            font-style: italic;
            margin: 5px 0;
        }
        .signature {
            margin-top: 8px;
            padding-top: 5px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 300px;
            margin-top: 20px;
        }
        @media print {
            body {
                padding: 0;
            }
            .form-container {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="header">
            <div class="header-top">Republic of the Philippines | Province of Davao del Norte | City of Tagum</div>
            <p>City Tourism and Cultural Office</p>
            <h1>LOCAL CERTIFICATION APPLICATION FORM</h1>
            <h2>Tourism Accommodation Establishment</h2>
        </div>

        <div class="section">
            <div class="section-title">I. APPLICATION DETAILS</div>
            <div class="form-row">
                <div class="form-col" style="flex: 0.4;">
                    <label>Date of Application:</label>
                    <input type=>
                </div>
                <div class="form-col">
                    <label>Application Type:</label>
                    <div class="radio-group">
                        <label><input type="radio" name="type" value="new"> New Application</label>
                        <label><input type="radio" name="type" value="renewal"> Renewal</label>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label>Certification Track:</label>
                    <div class="radio-group">
                        <label><input type="radio" name="track" value="dot"> DOT Accredited</label>
                        <label><input type="radio" name="track" value="local"> Locally Certified</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">II. ESTABLISHMENT INFORMATION</div>
            <div class="form-row">
                <div class="form-col">
                    <label>Name of Establishment:</label>
                    <input type="text">
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label>Name of Owner:</label>
                    <input type="text">
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label>Address:</label>
                    <input type="text">
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">III. CONTACT INFORMATION</div>
            <div class="form-row">
                <div class="form-col">
                    <label>Telephone:</label>
                    <input type="tel">
                </div>
                <div class="form-col">
                    <label>Mobile Number:</label>
                    <input type="tel">
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label>Email Address:</label>
                    <input type="email">
                </div>
                <div class="form-col">
                    <label>Facebook Page / Messenger:</label>
                    <input type="text">
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">IV. CATEGORY</div>
            <div class="radio-group">
                <label><input type="radio" name="category" value="hotel"> Hotel</label>
                <label><input type="radio" name="category" value="resort"> Resort</label>
                <label><input type="radio" name="category" value="apartment"> Apartment Hotel</label>
                <label><input type="radio" name="category" value="mabuhay"> Mabuhay Accommodation</label>
            </div>
        </div>

        <div class="section">
            <div class="section-title">V. SPECIFIC DETAILS</div>
            <div class="form-row">
                <div class="form-col">
                    <label>Total No. of Rooms:</label>
                    <input type="number">
                </div>
                <div class="form-col">
                    <label>Total Capacity No.:</label>
                    <input type="number">
                </div>
                <div class="form-col">
                    <label>Total No. of Employees:</label>
                    <input type="number">
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label>Male Employees:</label>
                    <input type="number">
                </div>
                <div class="form-col">
                    <label>Female Employees:</label>
                    <input type="number">
                </div>
            </div>
            <div style="margin-top: 5px;">
                <label>Room Types:</label>
                <table>
                    <tr><th>Type of Rooms</th><th>Rates</th><th>Number</th></tr>
                    <tr><td><input type="text"></td><td><input type="text"></td><td><input type="number"></td></tr>
                    <tr><td><input type="text"></td><td><input type="text"></td><td><input type="number"></td></tr>
                    <tr><td><input type="text"></td><td><input type="text"></td><td><input type="number"></td></tr>
                </table>
            </div>
            <div style="margin-top: 5px;">
                <label>Amenities (Swimming Pool, Bar, Restaurant, etc.):</label>
                <table>
                    <tr><th>Amenity</th></tr>
                    <tr><td><input type="text"></td></tr>
                    <tr><td><input type="text"></td></tr>
                    <tr><td><input type="text"></td></tr>
                </table>
            </div>
        </div>

        <div class="section">
            <div class="section-title">VI. SUBMISSION REQUIREMENTS</div>
            <p style="font-size: 8px; margin-bottom: 3px;">Please prepare the following documents when submitting your application to the City Tourism and Cultural Office:</p>
            <ul class="requirements-list">
                <li>Picture of Establishment (Rooms and Amenities)</li>
                <li>Permit Application Form</li>
                <li>Pre-requisites: Valid Mayor's Permit and/or Business License, Valid Comprehensive General Liability Insurance Policy (minimum P200,000 coverage for New Applications)</li>
                <li>Other Requirements: Fire Safety Inspection Certificate, Other requirements prescribed by the Business License Office (e.g., DTI/SEC registration, Articles of Incorporation/Cooperation)</li>
            </ul>
            <div class="form-row" style="margin-top: 5px;">
                <div class="form-col">
                    <label>Previous Certificate / Sticker No. (For Renewal):</label>
                    <input type="text">
                </div>
                <div class="form-col">
                    <label>Renewal Application Date:</label>
                    <input type=>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">VII. CERTIFICATION</div>
            <p class="certify-text">I certify further that all the foregoing data and documents supporting this application are true and correct.</p>
            <div class="signature">
                <label>Applicant Signature (Type Full Name):</label>
                <div class="signature-line"></div>
            </div>
        </div>
    </div>
</body>
</html>
