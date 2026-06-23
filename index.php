<?php
// Initialize initial variables with empty or default values
$voltage = '';
$current = '';
$rate = '';
$calculated = false;

// ==========================================================================
// FUNGSI KHAS (PHP FUNCTION) - Mengira kadar elektrik berdasarkan jam
// ==========================================================================
function calculate_electricity_bill($v, $c, $r, $hour) {
    // 1. Kira Kuasa dalam Watt (V * A)
    $power_w = $v * $c;
    
    // 2. Kira Tenaga dalam kWh: (Watts * Hours) / 1000
    $energy_kwh = ($power_w * $hour) / 1000;
    
    // 3. Kira Kos dalam RM: Tenaga * (Kadar Sen / 100)
    $total_cost_rm = $energy_kwh * ($r / 100);
    
    return [
        'power' => $power_w,
        'energy' => $energy_kwh,
        'cost' => $total_cost_rm
    ];
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $voltage = floatval($_POST['voltage']);
    $current = floatval($_POST['current']);
    $rate = floatval($_POST['rate']); // Rate in cents (e.g., 21.80 cents)

    if ($voltage > 0 && $current > 0 && $rate > 0) {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        /* ==========================================================================
           TEMA NIGHT MODE (DEFAULT)
           ========================================================================== */
        body { 
            background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)), 
                        url('https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?q=80&w=1920') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            color: #f1f5f9;
            transition: background 0.4s ease, color 0.4s ease;
        }
        .main-card { 
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px; 
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(12px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4); 
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.4s ease, border 0.4s ease;
        }
        .main-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }
        .card-header-gradient-blue { 
            background: linear-gradient(135deg, #1e40af, #1d4ed8); 
            color: white; 
            border-top-left-radius: 16px !important; 
            border-top-right-radius: 16px !important;
            border-bottom: none;
            padding: 1.5rem;
            position: relative;
        }
        .card-header-gradient-green { 
            background: linear-gradient(135deg, #065f46, #047857); 
            color: white; 
            border-top-left-radius: 16px !important; 
            border-top-right-radius: 16px !important;
            border-bottom: none;
            padding: 1.2rem;
        }
        .form-group label { color: #cbd5e1; font-weight: 600; transition: color 0.4s ease; }
        .form-control {
            background-color: #1e293b;
            border: 2px solid #334155;
            color: #ffffff;
            border-radius: 8px;
            padding: 1.2rem 0.75rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            background-color: #0f172a;
            color: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }
        .input-group-text {
            background-color: #334155;
            border: 2px solid #334155;
            border-right: none;
            color: #94a3b8;
            transition: all 0.4s ease;
        }
        .btn-custom {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }
        .table {
            border-radius: 12px;
            overflow: hidden;
            background-color: #1e293b;
            color: #f1f5f9;
            transition: all 0.4s ease;
        }
        .table-bordered th, .table-bordered td { border: 1px solid #334155; transition: border 0.4s ease; }
        .table-premium-header { background-color: #0f172a; color: #94a3b8; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: rgba(255, 255, 255, 0.02); }
        .highlight-val { font-size: 1.1rem; color: #f8fafc; }
        .currency-text { font-size: 1.1rem; color: #34d399; }
        
        /* Butang Suis Tema */
        .theme-toggle-btn {
            position: absolute;
            right: 20px;
            top: 25px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: background 0.3s ease;
            backdrop-filter: blur(5px);
        }
        .theme-toggle-btn:hover { background: rgba(255, 255, 255, 0.3); }

        /* ==========================================================================
           TEMA DAY MODE (SIANG) - DIPERBAIKI UNTUK LEBIH TERANG & JELAS
           ========================================================================== */
        body.light-mode {
            background: linear-gradient(rgba(248, 250, 252, 0.85), rgba(248, 250, 252, 0.85)), 
                        url('https://images.unsplash.com/photo-1509391366360-2e959784a276?q=80&w=1920') no-repeat center center fixed;
            background-size: cover;
            color: #0f172a;
        }
        body.light-mode .main-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(15, 23, 42, 0.12);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
        }
        body.light-mode .card-header-gradient-blue { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
        body.light-mode .card-header-gradient-green { background: linear-gradient(135deg, #10b981, #059669); }
        body.light-mode .form-group label { color: #1e293b; }
        body.light-mode .form-control { background-color: #f8fafc; border: 2px solid #cbd5e1; color: #0f172a; font-weight: 500; }
        body.light-mode .form-control:focus { background-color: #ffffff; border-color: #2563eb; color: #0f172a; }
        body.light-mode .input-group-text { background-color: #e2e8f0; border: 2px solid #cbd5e1; color: #334155; }
        body.light-mode .table { background-color: #ffffff; color: #0f172a; }
        body.light-mode .table-bordered th, body.light-mode .table-bordered td { border: 1px solid #cbd5e1; }
        body.light-mode .table-premium-header { background-color: #f1f5f9; color: #1e293b; }
        body.light-mode .table-striped tbody tr:nth-of-type(odd) { background-color: rgba(15, 23, 42, 0.02); }
        body.light-mode .highlight-val { color: #0f172a; font-weight: 700; }
        body.light-mode .currency-text { color: #047857; }
        body.light-mode .theme-toggle-btn { background: rgba(0, 0, 0, 0.08); color: #1e293b; }
        body.light-mode .theme-toggle-btn:hover { background: rgba(0, 0, 0, 0.15); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            
            <div class="card main-card mb-5">
                <div class="card-header card-header-gradient-blue text-center">
                    <button type="button" class="theme-toggle-btn" id="themeToggle">
                        <i class="fas fa-sun mr-1" id="themeIcon"></i> <span id="themeText">Day Mode</span>
                    </button>

                    <h4 class="mb-1"><i class="fas fa-bolt mr-2 text-warning"></i>Electricity Rate Calculator</h4>
                    <p class="mb-0 small text-white-50">Calculate real-time power, energy, and total charges</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group">
                            <label class="font-weight-bold" for="voltage">Voltage (V)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-plug"></i></span>
                                </div>
                                <input type="number" step="any" name="voltage" id="voltage" class="form-control" value="<?php echo htmlspecialchars($voltage); ?>" placeholder="Example: 230" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold" for="current">Current (A)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-wave-square"></i></span>
                                </div>
                                <input type="number" step="any" name="current" id="current" class="form-control" value="<?php echo htmlspecialchars($current); ?>" placeholder="Example: 5" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold" for="rate">Current Rate (Cents)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-coins text-warning"></i></span>
                                </div>
                                <input type="number" step="any" name="rate" id="rate" class="form-control" value="<?php echo htmlspecialchars($rate); ?>" placeholder="Example: 21.80" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block btn-custom mt-4">
                            <i class="fas fa-calculator mr-2"></i>Calculate Now
                        </button>
                    </form>
                </div>
            </div>

            <?php if ($calculated): 
                // Dapatkan nilai Power asas (Watt) untuk paparan maklumat atas jadual
                $base_data = calculate_electricity_bill($voltage, $current, $rate, 1);
            ?>
                <div class="row mb-3 px-2">
                    <div class="col-6">
                        <strong>POWER: </strong> <span class="badge badge-primary px-3 py-2" style="font-size:0.95rem;"><?php echo number_format($base_data['power'], 2); ?> W</span>
                    </div>
                    <div class="col-6 text-right">
                        <strong>RATE: </strong> <span class="badge badge-warning px-3 py-2" style="font-size:0.95rem; color:#000; font-weight:bold;"><?php echo number_format($rate, 2); ?> ¢</span>
                    </div>
                </div>

                <div class="card main-card mb-4">
                    <div class="card-header card-header-gradient-green text-center">
                        <h5 class="mb-0"><i class="fas fa-poll-h mr-2"></i>Calculation Results (24 Hours Breakdown)</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-center mb-0">
                                <thead class="table-premium-header">
                                    <tr>
                                        <th>#</th>
                                        <th>Hour</th>
                                        <th>Energy (kWh)</th>
                                        <th>TOTAL (RM)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Menjana pecahan data dari Jam 1 hingga Jam 24 menggunakan Loop
                                    for ($h = 1; $h <= 24; $h++) {
                                        // Memanggil custom PHP function yang telah dicipta di atas
                                        $data = calculate_electricity_bill($voltage, $current, $rate, $h);
                                        ?>
                                        <tr>
                                            <td><strong><?php echo $h; ?></strong></td>
                                            <td><?php echo $h; ?></td>
                                            <td class="highlight-val"><?php echo number_format($data['energy'], 5); ?></td>
                                            <td class="font-weight-bold currency-text">
                                                <?php echo number_format($data['cost'], 2); ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const themeToggleBtn = document.getElementById('themeToggle');
    const bodyElement = document.body;
    const themeIcon = document.getElementById('themeIcon');
    const themeText = document.getElementById('themeText');

    if (localStorage.getItem('theme') === 'light') {
        bodyElement.classList.add('light-mode');
        themeIcon.classList.replace('fa-sun', 'fa-moon');
        themeText.innerText = 'Night Mode';
    }

    themeToggleBtn.addEventListener('click', () => {
        bodyElement.classList.toggle('light-mode');
        
        if (bodyElement.classList.contains('light-mode')) {
            themeIcon.classList.replace('fa-sun', 'fa-moon');
            themeText.innerText = 'Night Mode';
            localStorage.setItem('theme', 'light');
        } else {
            themeIcon.classList.replace('fa-moon', 'fa-sun');
            themeText.innerText = 'Day Mode';
            localStorage.setItem('theme', 'dark');
        }
    });
</script>
</body>
</html>