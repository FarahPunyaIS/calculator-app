<?php
// Initialize initial variables with empty or default values
$voltage = '';
$current = '';
$rate = '';

$power_w = 0;
$energy_hourly_kwh = 0;
$energy_daily_kwh = 0;
$total_hourly_cost = 0;
$total_daily_cost = 0;
$calculated = false;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $voltage = floatval($_POST['voltage']);
    $current = floatval($_POST['current']);
    $rate = floatval($_POST['rate']); // Rate in cents (e.g., 21.80 cents)

    if ($voltage > 0 && $current > 0 && $rate > 0) {
        // 1. Calculate Power in Watts (V * A)
        $power_w = $voltage * $current;

        // 2. Calculate Energy (kWh) based on time
        // Standard Formula: (Watts * Hours) / 1000
        $energy_hourly_kwh = ($power_w * 1) / 1000;
        $energy_daily_kwh = ($power_w * 24) / 1000;

        // 3. Calculate Total Cost in RM (Rate divided by 100 to convert cents -> RM)
        $total_hourly_cost = $energy_hourly_kwh * ($rate / 100);
        $total_daily_cost = $energy_daily_kwh * ($rate / 100);

        $calculated = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electricity Bill Calculator - Internship Assignment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card-header { background-color: #007bff; color: white; border-top-left-radius: 10px; border-top-right-radius: 10px; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header text-center">
                    <h4 class="mb-0">Electricity Rate Calculator</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group">
                            <label for="voltage">Voltage (V)</label>
                            <input type="number" step="any" name="voltage" id="voltage" class="form-control" value="<?php echo htmlspecialchars($voltage); ?>" placeholder="Example: 230" required>
                        </div>
                        <div class="form-group">
                            <label for="current">Current (A)</label>
                            <input type="number" step="any" name="current" id="current" class="form-control" value="<?php echo htmlspecialchars($current); ?>" placeholder="Example: 5" required>
                        </div>
                        <div class="form-group">
                            <label for="rate">Current Rate (Cents)</label>
                            <input type="number" step="any" name="rate" id="rate" class="form-control" value="<?php echo htmlspecialchars($rate); ?>" placeholder="Example: 21.80" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Calculate Now</button>
                    </form>
                </div>
            </div>

            <?php if ($calculated): ?>
                <div class="card bg-white">
                    <div class="card-header bg-success text-center">
                        <h5 class="mb-0">Calculation Results</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Item / Section</th>
                                    <th>Per Hour</th>
                                    <th>Per Day</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Power (W)</strong></td>
                                    <td colspan="2" class="text-center font-weight-bold"><?php echo number_format($power_w, 2); ?> W</td>
                                </tr>
                                <tr>
                                    <td><strong>Energy (kWh)</strong></td>
                                    <td><?php echo number_format($energy_hourly_kwh, 5); ?> kWh</td>
                                    <td><?php echo number_format($energy_daily_kwh, 5); ?> kWh</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Charge (RM)</strong></td>
                                    <td class="text-success font-weight-bold">RM <?php echo number_format($total_hourly_cost, 4); ?></td>
                                    <td class="text-success font-weight-bold">RM <?php echo number_format($total_daily_cost, 4); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>