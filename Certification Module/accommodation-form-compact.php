<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Form - Tagum City, Tourism Department</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.2;
            padding: 8px;
            background: white;
        }
        .form-container {
            max-width: 100%;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 8px;
        }
        .header {
            text-align: center;
            margin-bottom: 6px;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
        }
        .header-top {
            font-size: 9px;
            margin-bottom: 3px;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 8px;
        }
        .form-logo {
            max-width: 80px;
            height: auto;
            display: inline-block;
        }
        .header h1 {
            font-size: 13px;
            font-weight: bold;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 10px;
            margin: 2px 0;
            font-weight: bold;
        }
        .header p {
            font-size: 8px;
            margin: 1px 0;
        }
        .application-date {
            font-size: 9px;
            margin-top: 6px;
            margin-bottom: 4px;
            text-align: center;
            font-weight: normal;
        }
        .section {
            margin-bottom: 6px;
            border: 1px solid #000;
            padding: 4px;
        }
        .section-title {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 4px;
            background: #e8e8e8;
            padding: 2px 4px;
            border-bottom: 1px solid #000;
        }
        .form-row {
            display: flex;
            gap: 8px;
            margin-bottom: 4px;
            align-items: center;
            flex-wrap: wrap;
        }
        .form-col {
            flex: 1;
            min-width: 0;
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
            font-size: 7.5px;
            margin-left: 12px;
            margin-top: 2px;
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
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-top: 8px;
            padding-top: 5px;
            flex-wrap: nowrap;
        }
        .signature .sig-col {
            flex: 0 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .signature .sig-col--right { margin-left: auto; }
        .signature-line {
            border-top: 1px solid #000;
            width: 180px;
            margin-top: 20px;
            height: 2px;
            display: block;
        }
        .signature .sig-col--right .signature-line {
            width: 120px;
        }
        .signature .sig-label {
            font-size: 8px;
            text-align: center;
            margin-top: 6px;
        }
        @page {
            margin: 4mm;
        }
        @media (max-width: 600px) {
            body {
                padding: 6px;
                font-size: 8.5px;
            }
            .form-container {
                padding: 6px;
            }
            .header {
                margin-bottom: 5px;
                padding-bottom: 5px;
            }
            .form-logo {
                max-width: 64px;
            }
            .header h1 {
                font-size: 11px;
            }
            .header h2 {
                font-size: 9px;
            }
            .section {
                padding: 4px;
                margin-bottom: 5px;
            }
            .form-row {
                flex-direction: column;
                align-items: stretch;
                gap: 4px;
            }
            .form-col {
                width: 100%;
            }
            .radio-group label {
                display: block;
                margin-right: 0;
                margin-bottom: 2px;
            }
            .signature-line {
                width: 180px;
            }
            .signature .sig-col--right .signature-line {
                width: 120px;
            }
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
                font-size: 8.5px;
            }
            .form-container {
                border: none;
                padding: 4px;
            }
            .section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="header">
            <div class="logo-container">
                <img src="../images/City%20of%20Tagum.png" alt="City of Tagum" class="form-logo">
            </div>
            <div class="header-top">Republic of the Philippines | Province of Davao del Norte | City of Tagum</div>
            <p>City Tourism and Cultural Office</p>
            <h1>LOCAL CERTIFICATION APPLICATION FORM</h1>
            <h2>Tourism Accommodation Establishment</h2>
            <p class="application-date">Application Date: ________________________</p>
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
                <label><input type="radio" name="category" value="others"> Others</label>
            </div>
            <div style="margin-top: 6px;">
                <label style="font-weight: bold; font-size: 9px; display:block;">If Others, please specify:</label>
                <input type="text" name="other_category_text" style="width:100%; padding:3px; font-size:9px; border:1px solid #000; height:22px;">
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
                <div class="form-col">
                    <label>6.4 Year Started/Established:</label>
                    <input type="text">
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
            <div class="requirements-list" style="font-size:8px; line-height:1.2;">
                <p>7. Submit copy of the following documents for New and Renewal Application:</p>
                <p style="margin-left:12px;">7.1 Picture of Establishment</p>
                <p style="margin-left:24px;">a. Rooms and Amenities</p>
                <p style="margin-left:12px;">7.2 Permit Application Form</p>
                <p style="margin-left:12px;">7.3 Pre-requisites</p>
                <p style="margin-left:24px;">a. Valid Mayor’s Permit and/or Business License (New Application & Renewal)</p>
                <p style="margin-left:24px;">b. Valid Comprehensive General Liability Insurance Policy with a minimum amount of coverage (P200,000) – (New Application)</p>
                <p style="margin-left:12px;">7.4 Other Requirements</p>
                <p style="margin-left:24px;">a. Fire Safety Inspection Certificate.</p>
                <p style="margin-left:24px;">b. And other requirements prescribed by the Business License Office</p>
                <p style="margin-left:36px; font-style:italic;">e.g.: (DTI Business Name Certificate (for Sole Proprietor) or SEC Registration Certificate and Articles of Incorporation and its By-Laws (for Partnerships & Corporations) or Articles of Cooperation and Its By-Laws (for Cooperatives), etc.)</p>
                <p style="margin-top:4px;">Additional requirements for Renewal Application:</p>
                <p style="margin-left:12px;">1. Previous Certificate/Sticker</p>
                <p style="margin-left:12px;">2. Application Date</p>
            </div>
        </div>

        <div class="section">
            <div class="section-title">VII. CERTIFICATION</div>
            <p class="certify-text">I certify further that all the foregoing data and documents supporting this application are true and correct.</p>
            <div class="signature">
                <div class="sig-col">
                    <div class="signature-line"></div>
                    <div class="sig-label">Signature over Printed Name</div>
                </div>
                <div class="sig-col sig-col--right">
                    <div class="signature-line"></div>
                    <div class="sig-label">Date</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
