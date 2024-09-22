<?php
// Include the database configuration file
require_once 'config.php';

// Create a connection to the database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch daily, weekly, and overall statistics for 'view', '30sec', and '60sec'
$eventTypes = ['view', '30sec', '60sec'];

$dailyStats = [];
$weeklyStats = [];
$overallStats = [];

foreach ($eventTypes as $event) {
    $dailyStats[$event] = $conn->query("SELECT COUNT(*) as count FROM tracking_events WHERE event_type = '$event' AND DATE(event_time) = CURDATE()")->fetch_assoc();
    $weeklyStats[$event] = $conn->query("SELECT COUNT(*) as count FROM tracking_events WHERE event_type = '$event' AND WEEK(event_time) = WEEK(CURDATE())")->fetch_assoc();
    $overallStats[$event] = $conn->query("SELECT COUNT(*) as count FROM tracking_events WHERE event_type = '$event'")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Statistics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dashboard-header {
            margin: 20px 0;
        }
        .card-title {
            font-size: 1.5rem;
        }
        .stat-section {
            margin-bottom: 30px;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="dashboard-header text-center">
            <h1 class="display-4">Statistics Dashboard</h1>
            <p class="lead">Track daily, weekly, and overall event data</p>
        </header>

        <!-- Daily Statistics Section -->
        <section class="stat-section">
            <h2>Daily Statistics</h2>
            <div class="row">
                <?php foreach ($dailyStats as $event => $data) { ?>
                    <div class="col-md-4">
                        <div class="card text-center mb-4">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo ucfirst($event); ?></h5>
                                <p class="card-text display-6"><?php echo $data['count']; ?></p>
                                <small>Tracked Today</small>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>

        <!-- Weekly Statistics Section -->
        <section class="stat-section">
            <h2>Weekly Statistics</h2>
            <div class="row">
                <?php foreach ($weeklyStats as $event => $data) { ?>
                    <div class="col-md-4">
                        <div class="card text-center mb-4">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo ucfirst($event); ?></h5>
                                <p class="card-text display-6"><?php echo $data['count']; ?></p>
                                <small>Tracked This Week</small>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>

        <!-- Overall Statistics Section -->
        <section class="stat-section">
            <h2>Overall Statistics</h2>
            <div class="row">
                <?php foreach ($overallStats as $event => $data) { ?>
                    <div class="col-md-4">
                        <div class="card text-center mb-4">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo ucfirst($event); ?></h5>
                                <p class="card-text display-6"><?php echo $data['count']; ?></p>
                                <small>Total Tracked</small>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
