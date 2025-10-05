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
                <div class="orders-title">Newsletter Subscriptions</div>
                <div class="orders-filter">
                    <span>Subscriptions in:</span>
            <span class="calendar-icon">&#128197;</span>
            <select>
                <option>Last 3 months</option>
                <option>2025</option>
                <option>2024</option>
                <option>2023</option>
            </select>
        </div>
            </div>
            <div style="background:#fff;border-radius:8px;box-shadow:0 2px 8px #0001;padding:24px 24px 18px 24px;max-width:900px;">
                <div style="margin-bottom:12px;font-size:0.98rem;"><strong>Email Address:</strong> ntokozocmaseko@gmail.com</div>
                <div style="font-weight:600;margin-bottom:10px;">Newsletter Preferences:</div>
                <form>
                    <div style="margin-bottom:18px;">
                        <label style="display:flex;align-items:center;gap:8px;font-size:1.1rem;">
                            <input type="checkbox" name="daily_deals" checked> Daily Deals (Discounts)
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary" style="font-weight:600;">Save Preferences</button>
                </form>
            </div>
        </main>
    </div>
<?php include '../footer.php'; ?>
