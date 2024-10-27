<?php
// Include the database configuration file
require_once 'config.php';

// Create a connection to the database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Event types
$eventTypes = ['view', '30sec', '60sec'];

// Fetch daily, weekly, and overall statistics
$dailyStats = [];
$weeklyStats = [];
$overallStats = [];

foreach ($eventTypes as $event) {
    $dailyStats[$event] = $conn->query("SELECT COUNT(*) as count FROM tracking_events WHERE event_type = '$event' AND DATE(event_time) = CURDATE()")->fetch_assoc();
    $weeklyStats[$event] = $conn->query("SELECT COUNT(*) as count FROM tracking_events WHERE event_type = '$event' AND WEEK(event_time) = WEEK(CURDATE())")->fetch_assoc();
    $overallStats[$event] = $conn->query("SELECT COUNT(*) as count FROM tracking_events WHERE event_type = '$event'")->fetch_assoc();
}

// Fetch data for the date input section
$dateStats = [];
$defaultDate = date('Y-m-d');
$selectedDate = $_POST['date'] ?? $defaultDate;

foreach ($eventTypes as $event) {
    $dateStats[$event] = $conn->query("SELECT COUNT(*) as count FROM tracking_events WHERE event_type = '$event' AND DATE(event_time) = '$selectedDate'")->fetch_assoc();
}

// Fetch data for the graph with default range of the last 7 days
$graphData = [];
$startDate = $_POST['start_date'] ?? date('Y-m-d', strtotime('-6 days'));
$endDate = $_POST['end_date'] ?? $defaultDate;

foreach ($eventTypes as $event) {
    $result = $conn->query("SELECT DATE(event_time) as date, COUNT(*) as count FROM tracking_events WHERE event_type = '$event' AND event_time BETWEEN '$startDate' AND '$endDate' GROUP BY DATE(event_time)");
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[$row['date']] = $row['count'];
    }
    $graphData[$event] = $data;
}

// Generate labels for the graph (all dates within the range)
$datePeriod = new DatePeriod(new DateTime($startDate), new DateInterval('P1D'), (new DateTime($endDate))->modify('+1 day'));
$labels = [];
foreach ($datePeriod as $date) {
    $labels[] = $date->format('Y-m-d');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Statistics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
    <style>
        .dashboard-header { margin: 20px 0; }
        .card-title { font-size: 1.5rem; }
        .stat-section { margin-bottom: 30px; }
        .card { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>
    <div class="container">
        <header class="dashboard-header text-center">
            <h1 class="display-4">Statistics Dashboard</h1>
            <p class="lead">Track daily, weekly, and overall event data</p>
        </header>

        

        
        <!-- Graph Section -->
        <section class="stat-section">
            <h2>Daily Trends</h2>
            <form method="POST" class="mb-3">
                <label for="start_date" class="form-label">Start Date:</label>
                <input type="date" id="start_date" name="start_date" class="form-control w-25 d-inline mb-3" value="<?php echo $startDate; ?>" required>
                <label for="end_date" class="form-label">End Date:</label>
                <input type="date" id="end_date" name="end_date" class="form-control w-25 d-inline mb-3" value="<?php echo $endDate; ?>" required>
                <button type="submit" class="btn btn-primary">Update Chart</button>
            </form>
            <canvas id="eventTrendChart" width="400" height="200"></canvas>
        </section>
		
		<!-- Daily Data by Date Section -->
        <section class="stat-section">
            <h2>Check Data by Date</h2>
            <form method="POST" class="mb-3">
                <label for="date" class="form-label">Select Date:</label>
                <input type="date" id="date" name="date" class="form-control w-50 mb-3" value="<?php echo $selectedDate; ?>" required>
                <button type="submit" class="btn btn-primary">Get Statistics</button>
            </form>
            <div class="row">
                <?php foreach ($dateStats as $event => $data) { ?>
                    <div class="col-md-4">
                        <div class="card text-center mb-4">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo ucfirst($event); ?></h5>
                                <p class="card-text display-6"><?php echo $data['count']; ?></p>
                                <small>Tracked on <?php echo htmlspecialchars($selectedDate); ?></small>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>


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

    <script>
		// Prepare data for the chart
		const labels = <?php echo json_encode($labels); ?>;

		const datasets = [
			<?php foreach ($eventTypes as $event): ?>
			{
				label: '<?php echo ucfirst($event); ?>',
				data: labels.map(label => (<?php echo json_encode($graphData[$event]); ?>[label] ?? 0)),
				borderColor: '<?php echo $event === 'view' ? '#007bff' : ($event === '30sec' ? '#28a745' : '#ffc107'); ?>',
				fill: false,
				tension: 0.3
			},
			<?php endforeach; ?>
		];

		// Initialize Chart.js
		const config = {
			type: 'line',
			data: { labels: labels, datasets: datasets },
			options: {
				responsive: true,
				plugins: {
					legend: { position: 'top' },
					title: { display: true, text: 'Event Trends Over Selected Date Range' }
				},
				scales: {
					x: { title: { display: true, text: 'Date' } },
					y: { title: { display: true, text: 'Event Count' }, beginAtZero: true }
				}
			}
		};

		// Render the chart
		new Chart(document.getElementById('eventTrendChart'), config);
	</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
// Close the connection
$conn->close();
?>
