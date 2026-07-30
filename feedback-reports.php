<?php
require_once __DIR__ . '/../database/setup_feedback.php';
require_once __DIR__ . '/config.php';
requireAuth();

ensureFeedbackTable();

$dbFile = __DIR__ . '/../database.db';
$db = new SQLite3($dbFile);

// Apply filters
$where = ['1=1'];
$params = [];

if (!empty($_GET['establishment_type'])) {
    $where[] = 'establishment_type = ?';
    $params[] = $_GET['establishment_type'];
}

if (!empty($_GET['status'])) {
    $where[] = 'status = ?';
    $params[] = $_GET['status'];
}

if (!empty($_GET['rating'])) {
    $where[] = 'overall_rating = ?';
    $params[] = (int)$_GET['rating'];
}

if (!empty($_GET['date_from'])) {
    $where[] = 'created_at >= ?';
    $params[] = $_GET['date_from'];
}

if (!empty($_GET['date_to'])) {
    $where[] = 'created_at <= ?';
    $params[] = $_GET['date_to'] . ' 23:59:59';
}

$whereClause = implode(' AND ', $where);

// Get filtered feedback
$query = "SELECT * FROM feedback WHERE $whereClause ORDER BY created_at DESC";
$stmt = $db->prepare($query);
foreach ($params as $i => $param) {
    $stmt->bindValue($i + 1, $param);
}
$result = $stmt->execute();

$feedbackList = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $feedbackList[] = $row;
}

// Calculate statistics
$stats = [
    'total' => count($feedbackList),
    'approved' => 0,
    'pending' => 0,
    'rejected' => 0,
    'anonymous' => 0,
    'avg_rating' => 0,
    'nps' => 0
];

$totalRating = 0;
$totalApproved = 0;
$promoters = 0;
$detractors = 0;

foreach ($feedbackList as $feedback) {
    if ($feedback['status'] === 'Approved') {
        $stats['approved']++;
        $totalRating += $feedback['overall_rating'];
        $totalApproved++;
        
        if (in_array($feedback['recommendation'], ['Definitely Yes', 'Probably Yes'])) {
            $promoters++;
        } elseif (in_array($feedback['recommendation'], ['Probably No', 'Definitely No'])) {
            $detractors++;
        }
    } elseif ($feedback['status'] === 'Pending Review') {
        $stats['pending']++;
    } elseif ($feedback['status'] === 'Rejected') {
        $stats['rejected']++;
    }
    
    if ($feedback['anonymous']) {
        $stats['anonymous']++;
    }
}

if ($totalApproved > 0) {
    $stats['avg_rating'] = round($totalRating / $totalApproved, 1);
    $stats['nps'] = round((($promoters - $detractors) / $totalApproved) * 100);
}

$db->close();

// Export functionality
if (isset($_GET['export']) && in_array($_GET['export'], ['csv', 'excel'])) {
    $filename = 'feedback_report_' . date('Y-m-d') . '.' . $_GET['export'];
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // CSV Header
    fputcsv($output, [
        'ID',
        'Establishment Type',
        'Establishment ID',
        'Overall Rating',
        'Comment',
        'Anonymous',
        'Email',
        'Recommendation',
        'Status',
        'Created At',
        'Reviewed At',
        'Reviewed By'
    ]);
    
    // CSV Data
    foreach ($feedbackList as $feedback) {
        fputcsv($output, [
            $feedback['id'],
            $feedback['establishment_type'],
            $feedback['establishment_id'],
            $feedback['overall_rating'],
            $feedback['comment'] ?? '',
            $feedback['anonymous'] ? 'Yes' : 'No',
            $feedback['email'],
            $feedback['recommendation'],
            $feedback['status'],
            $feedback['created_at'],
            $feedback['reviewed_at'] ?? '',
            $feedback['reviewed_by'] ?? ''
        ]);
    }
    
    fclose($output);
    exit;
}

// PDF Export - Redirect to the dedicated comprehensive report generator
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
    // Build query params to pass filter context to the report generator
    $params = ['format' => 'pdf'];
    if (!empty($_GET['establishment_type'])) {
        $params['username'] = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Administrator';
    }
    $redirectUrl = '../Feedback Module/generate_report.php?' . http_build_query($params);
    header('Location: ' . $redirectUrl);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Reports - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/feedback-admin.css">
</head>
<body class="admin-body">
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="admin-header-content">
            <div class="admin-title">
                <img src="../images/TagumTourism.jpg" alt="Tagum City Logo" class="admin-logo" loading="lazy">
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
    <div class="admin-header">
        <h1>Feedback Reports</h1>
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>

    <!-- Statistics Summary -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['total']; ?></div>
            <div class="stat-label">Total Feedback</div>
            <div class="stat-icon">📊</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['approved']; ?></div>
            <div class="stat-label">Approved Reviews</div>
            <div class="stat-icon">✓</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['pending']; ?></div>
            <div class="stat-label">Pending Reviews</div>
            <div class="stat-icon">⏳</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['rejected']; ?></div>
            <div class="stat-label">Rejected Reviews</div>
            <div class="stat-icon">✗</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['avg_rating']; ?> / 5</div>
            <div class="stat-label">Average Rating</div>
            <div class="stat-icon">⭐</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['nps'] >= 0 ? '+' : ''; ?><?php echo $stats['nps']; ?></div>
            <div class="stat-label">NPS Score</div>
            <div class="stat-icon">📈</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['anonymous']; ?></div>
            <div class="stat-label">Anonymous Reviews</div>
            <div class="stat-icon">👤</div>
        </div>
    </div>

    <!-- Report Filters -->
    <div class="filters-section">
        <form method="GET" class="filters-form">
            <div class="filter-group">
                <label>Establishment Type:</label>
                <select name="establishment_type">
                    <option value="">All</option>
                    <option value="hotel" <?php echo ($_GET['establishment_type'] ?? '') === 'hotel' ? 'selected' : ''; ?>>Hotel</option>
                    <option value="restaurant" <?php echo ($_GET['establishment_type'] ?? '') === 'restaurant' ? 'selected' : ''; ?>>Restaurant</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Status:</label>
                <select name="status">
                    <option value="">All</option>
                    <option value="Pending Review" <?php echo ($_GET['status'] ?? '') === 'Pending Review' ? 'selected' : ''; ?>>Pending Review</option>
                    <option value="Approved" <?php echo ($_GET['status'] ?? '') === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="Rejected" <?php echo ($_GET['status'] ?? '') === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Rating:</label>
                <select name="rating">
                    <option value="">All</option>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($_GET['rating'] ?? '') == $i ? 'selected' : ''; ?>><?php echo $i; ?> Stars</option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Date From:</label>
                <input type="date" name="date_from" value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
            </div>
            <div class="filter-group">
                <label>Date To:</label>
                <input type="date" name="date_to" value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Apply Filters</button>
            <a href="feedback-reports.php" class="btn btn-secondary">Clear</a>
        </form>
    </div>

    <!-- Export Options -->
    <div class="export-section">
        <h3>Export Report</h3>
        <div class="export-buttons">
            <a href="?<?php echo http_build_query(array_merge($_GET, ['export' => 'csv'])); ?>" class="btn btn-primary">
                📄 Download CSV
            </a>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['export' => 'excel'])); ?>" class="btn btn-primary">
                📊 Download Excel
            </a>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['export' => 'pdf'])); ?>" class="btn btn-primary">
                📑 Download PDF
            </a>
        </div>
    </div>

    <!-- Report Preview -->
    <div class="report-preview">
        <h3>Report Preview (<?php echo count($feedbackList); ?> records)</h3>
        <div class="feedback-table-container">
            <table class="feedback-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Establishment</th>
                        <th>Rating</th>
                        <th>Recommendation</th>
                        <th>Status</th>
                        <th>Anonymous</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($feedbackList)): ?>
                        <tr>
                            <td colspan="6" class="no-data">No feedback found matching the filters</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach (array_slice($feedbackList, 0, 50) as $feedback): ?>
                            <tr>
                                <td><?php echo date('M j, Y', strtotime($feedback['created_at'])); ?></td>
                                <td><?php echo ucfirst($feedback['establishment_type']); ?> #<?php echo $feedback['establishment_id']; ?></td>
                                <td><?php echo $feedback['overall_rating']; ?>/5</td>
                                <td><?php echo htmlspecialchars($feedback['recommendation']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['status']); ?></td>
                                <td><?php echo $feedback['anonymous'] ? 'Yes' : 'No'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (count($feedbackList) > 50): ?>
                            <tr>
                                <td colspan="6" class="no-data">
                                    ... and <?php echo count($feedbackList) - 50; ?> more records (download full report to see all)
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.export-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 2rem;
}

.export-section h3 {
    font-size: 1.25rem;
    color: #1d5a3d;
    margin-bottom: 1rem;
}

.export-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.report-preview {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.report-preview h3 {
    font-size: 1.25rem;
    color: #1d5a3d;
    margin-bottom: 1rem;
}
</style>
</body>
</html>
