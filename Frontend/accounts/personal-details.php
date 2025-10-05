<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Address Book</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    /* background now set in assets/style.css for .accounts-bg */
        .orders-section { max-width: 1200px; margin: 32px auto; padding: 0 16px; }
        .orders-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
        .orders-title { font-size: 1.6rem; font-weight: bold; color: #3d2176; }
        .orders-filter { display: flex; align-items: center; gap: 8px; color: #888; font-size: 1rem; }
        .orders-filter select { padding: 6px 12px; border-radius: 6px; border: 1px solid #ccc; }
        .orders-filter .calendar-icon { color: #1976d2; font-size: 1.2rem; margin-right: 4px; }
    </style>
</head>
<body class="accounts-bg">
    <header class="main-header">
        <?php include '../navbar.php'; ?>
    </header>
    <div class="account-layout" style="display:flex;max-width:1200px;margin:32px auto;gap:32px;">
        <aside class="account-sidebar">
            <?php include 'sidebar.php'; ?>
        </aside>

        <main class="orders-section account-main-content" style="flex:1;">
    <div class="orders-header">
        <div class="orders-title">Personal Details</div>
        <div class="orders-filter">
            <span>Details in:</span>
            <span class="calendar-icon">&#128197;</span>
            <select>
                <option>Last 3 months</option>
                <option>2025</option>
                <option>2024</option>
                <option>2023</option>
            </select>
        </div>
    </div>
    <div style="background:#fff;border-radius:8px;box-shadow:0 2px 8px #0001;padding:24px 24px 18px 24px;max-width:900px;margin-bottom:18px;">
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-weight:600;">Your Name</div>
                <div style="color:#444;">Ntokzo Maseko</div>
            </div>
            <button class="btn btn-outline-dark" style="min-width:110px;">Edit</button>
        </div>
    </div>
    <div style="background:#fff;border-radius:8px;box-shadow:0 2px 8px #0001;padding:24px 24px 18px 24px;max-width:900px;margin-bottom:18px;">
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-weight:600;">Email Address</div>
                <div style="color:#444;">ntokozocmaseko@gmail.com <span style='color:#2ecc40;font-size:1.1em;' title='Verified'>&#10004;</span></div>
            </div>
            <button class="btn btn-outline-dark" style="min-width:110px;">Edit</button>
        </div>
    </div>
    <div style="background:#fff;border-radius:8px;box-shadow:0 2px 8px #0001;padding:24px 24px 18px 24px;max-width:900px;margin-bottom:18px;">
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
                <span style="background:#ffbe19;color:#222;font-size:0.95rem;font-weight:600;padding:2px 12px 2px 12px;border-radius:8px;margin-bottom:6px;display:inline-block;">NOT VERIFIED</span><br>
                <span style="font-weight:600;">Mobile Number</span><br>
                <span style="color:#1976d2;font-size:0.98rem;">Why add a mobile number?</span>
            </div>
            <button class="btn btn-outline-dark" style="min-width:110px;">Add &amp; Verify</button>
        </div>
    </div>
    <div style="background:#fff;border-radius:8px;box-shadow:0 2px 8px #0001;padding:24px 24px 18px 24px;max-width:900px;margin-bottom:18px;">
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-weight:600;">School</div>
                <span style="color:#1976d2;font-size:0.98rem;">Why add your school?</span>
            </div>
            <button class="btn btn-outline-dark" style="min-width:110px;">Add</button>
        </div>
    </div>
</main>
    </div>
    <footer class="main-footer">
        <?php include '../footer.php'; ?>
    </footer>
</body>
</html>