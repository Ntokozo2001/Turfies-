<?php
// accounts/sidebar.php
?>
<div class="account-sidebar" style="background:#064291;color:#fff;border-radius:6px;box-shadow:0 1px 4px #0001;padding:18px 0 18px 0;min-width:260px;max-width:290px;font-family:'Segoe UI', Arial, sans-serif;">
    <div style="font-weight:700;font-size:1.1rem;padding:0 24px 12px 24px;color:#fff;font-family:'Segoe UI', Arial, sans-serif;"><a href="/Turfies Code/Frontend/account.php" style="color:#fff;text-decoration:none;font-family:'Segoe UI', Arial, sans-serif;">My Account</a></div>
    <div style="padding:0 24px;">
    <div style="font-weight:600;margin-bottom:4px;display:flex;align-items:center;gap:7px;color:#fff;">
            <span class="bi bi-box"></span> Orders
        </div>
    <ul class="account-sidebar-list" style="margin-bottom:16px;color:#fff;">
            <li><a href="/Turfies Code/Frontend/accounts/orders.php" class="account-sidebar-link<?php if(basename($_SERVER['PHP_SELF'])=='orders.php')echo ' active'; ?>" style="color:#fff;text-decoration:none;font-family:'Segoe UI', Arial, sans-serif;">Orders</a></li>
            <li><a href="/Turfies Code/Frontend/accounts/invoices.php" class="account-sidebar-link<?php if(basename($_SERVER['PHP_SELF'])=='invoices.php')echo ' active'; ?>" style="color:#fff;text-decoration:none;font-family:'Segoe UI', Arial, sans-serif;">Invoices</a></li>
            <li><a href="/Turfies Code/Frontend/accounts/returns.php" class="account-sidebar-link<?php if(basename($_SERVER['PHP_SELF'])=='returns.php')echo ' active'; ?>" style="color:#fff;text-decoration:none;font-family:'Segoe UI', Arial, sans-serif;">Returns</a></li>
            <li><a href="/Turfies Code/Frontend/accounts/product-reviews.php" class="account-sidebar-link<?php if(basename($_SERVER['PHP_SELF'])=='product-reviews.php')echo ' active'; ?>" style="color:#fff;text-decoration:none;font-family:'Segoe UI', Arial, sans-serif;">Product Reviews</a></li>
        </ul>
    <div style="font-weight:600;margin-bottom:4px;display:flex;align-items:center;gap:7px;color:#fff;">
            <span class="bi bi-credit-card"></span> Payments & Credit
        </div>
    <ul class="account-sidebar-list" style="margin-bottom:16px;color:#fff;">
            <li><a href="/Turfies Code/Frontend/accounts/coupons.php" class="account-sidebar-link<?php if(basename($_SERVER['PHP_SELF'])=='coupons.php')echo ' active'; ?>" style="color:#fff;text-decoration:none;font-family:'Segoe UI', Arial, sans-serif;">Coupons & Offers</a></li>
            <li><a href="/Turfies Code/Frontend/accounts/credit.php" class="account-sidebar-link<?php if(basename($_SERVER['PHP_SELF'])=='credit.php')echo ' active'; ?>" style="color:#fff;text-decoration:none;font-family:'Segoe UI', Arial, sans-serif;">Credit & Refunds</a></li>
            <li><a href="/Turfies Code/Frontend/accounts/redeem.php" class="account-sidebar-link<?php if(basename($_SERVER['PHP_SELF'])=='redeem.php')echo ' active'; ?>" style="color:#fff;text-decoration:none;font-family:'Segoe UI', Arial, sans-serif;">Redeem Gift Voucher</a></li>
        </ul>
    <div style="font-weight:600;margin-bottom:4px;display:flex;align-items:center;gap:7px;color:#fff;">
            <span class="bi bi-person"></span> Profile
        </div>
    <ul class="account-sidebar-list" style="color:#fff;">
            <li><a href="/Turfies Code/Frontend/accounts/personal-details.php" class="account-sidebar-link<?php if(basename($_SERVER['PHP_SELF'])=='personal-details.php')echo ' active'; ?>" style="color:#fff;text-decoration:none;font-family:'Segoe UI', Arial, sans-serif;">Personal Details</a></li>
            <li><a href="/Turfies Code/Frontend/accounts/security-settings.php" class="account-sidebar-link<?php if(basename($_SERVER['PHP_SELF'])=='security-settings.php')echo ' active'; ?>" style="color:#fff;text-decoration:none;font-family:'Segoe UI', Arial, sans-serif;">Security Settings</a></li>
            <li><a href="/Turfies Code/Frontend/accounts/address-book.php" class="account-sidebar-link<?php if(basename($_SERVER['PHP_SELF'])=='address-book.php')echo ' active'; ?>" style="color:#fff;text-decoration:none;font-family:'Segoe UI', Arial, sans-serif;">Address Book</a></li>
            <li><a href="/Turfies Code/Frontend/accounts/newsletter.php" class="account-sidebar-link<?php if(basename($_SERVER['PHP_SELF'])=='newsletter.php')echo ' active'; ?>" style="color:#fff;text-decoration:none;font-family:'Segoe UI', Arial, sans-serif;">Newsletter Subscriptions</a></li>
        </ul>
    </div>
</div>
