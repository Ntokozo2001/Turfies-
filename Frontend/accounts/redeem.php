<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redeem Gift Voucher</title>
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
            <?php include '../accounts/sidebar.php'; ?>
        </aside>

        <main class="orders-section account-main-content" style="flex:1;">
          <div class="orders-header" style="margin-bottom:24px;">
            <div class="orders-title">Redeem Gift Voucher</div>
            <div class="orders-filter">
                <span>Redeem in:</span>
                <span class="calendar-icon">&#128197;</span>
                <select>
                    <option>Last 3 months</option>
                    <option>2025</option>
                    <option>2024</option>
                    <option>2023</option>
                </select>
            </div>
        </div>
        <!-- Redeem content goes here -->
        <div style="background:#fafafa;border-radius:6px;box-shadow:0 1px 4px #0001;padding:28px 18px 24px 18px;max-width:900px;margin-bottom:18px;">
            <div style="font-weight:600;font-size:1.15rem;margin-bottom:18px;color:#222;">Apply a Gift Voucher Code</div>
            <form style="display:flex;align-items:center;gap:16px;">
                <input type="text" class="form-control" placeholder="Enter a gift voucher code" style="background:#fafafa;border:none;border-bottom:1px solid #ccc;border-radius:0;box-shadow:none;outline:none;max-width:600px;">
                <button type="submit" class="btn" style="background:#90c2e7;color:#fff;font-weight:600;min-width:160px;">Apply Voucher</button>
            </form>
        </div>
    </main>
</div>
<?php include '../footer.php'; ?>
