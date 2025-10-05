<?php
// accounts/security-settings.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Settings</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    /* background now set in assets/style.css for .accounts-bg */
        .security-section { max-width: 1200px; margin: 32px auto; padding: 0 16px; }
        .security-title { font-size: 1.5rem; font-weight: bold; color: #444; margin-bottom: 24px; }
        .security-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0001; padding: 24px 24px 18px 24px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; }
        .security-card .left { display: flex; flex-direction: column; }
        .security-card .card-title { font-weight: 600; font-size: 1.1rem; color: #222; }
        .security-card .card-desc { color: #444; font-size: 1rem; margin-top: 2px; }
        .security-card .card-btn { min-width: 110px; font-weight: 500; }
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
        <main class="security-section account-main-content" style="flex:1;">
            <div class="security-title">Security Settings</div>
            <div class="security-card">
                <div class="left">
                    <div class="card-title">Password</div>
                    <div class="card-desc">********</div>
                </div>
                <button class="btn btn-outline-dark card-btn">Reset</button>
            </div>
            <div class="security-card">
                <div class="left">
                    <div class="card-title">Trusted Devices</div>
                    <div class="card-desc">Devices that don't require a One-Time PIN</div>
                </div>
                <button class="btn btn-outline-dark card-btn">Manage</button>
            </div>
            <div class="security-card">
                <div class="left">
                    <div class="card-title">Device Login Activity</div>
                    <div class="card-desc">Devices that have logged into your account</div>
                </div>
                <button class="btn btn-outline-dark card-btn">View</button>
            </div>
        </main>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>
