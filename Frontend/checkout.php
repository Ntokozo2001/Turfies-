<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Turfies Exam Care</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .checkout-container {
            max-width: 700px;
            margin: 40px auto;
            background: #faf8fd;
            border-radius: 18px;
            box-shadow: 0 2px 16px rgba(169,123,224,0.08);
            padding: 32px 24px;
        }
        .checkout-title {
            color: #a97be0;
            text-align: center;
            margin-bottom: 24px;
        }
        .checkout-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 24px;
        }
        .checkout-table th, .checkout-table td {
            padding: 12px 10px;
            text-align: center;
        }
        .checkout-table th {
            background: #a97be0;
            color: #ffbe19;
            font-size: 1.1rem;
        }
        .checkout-table td {
            background: #fff;
        }
        .remove-btn {
            background: #b53d23;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 6px 14px;
            cursor: pointer;
            font-size: 1rem;
        }
        .remove-btn:hover {
            background: #a97be0;
        }
        .payment-section {
            background: #fff;
            border-radius: 12px;
            padding: 18px 18px 10px 18px;
            margin-bottom: 18px;
            box-shadow: 0 1px 6px #a97be033;
        }
        .payment-section label {
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
        }
        .payment-section input, .payment-section select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #a97be0;
            margin-bottom: 12px;
        }
        .checkout-btn {
            background: #a97be0;
            color: #fff;
            font-size: 1.2rem;
            width: 100%;
            margin-top: 18px;
            border-radius: 12px;
            padding: 12px 0;
            border: none;
            font-weight: 700;
            cursor: pointer;
        }
        .checkout-btn:hover {
            background: #ffbe19;
            color: #a97be0;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <?php include 'navbar.php'; ?>
    </header>
    <main class="checkout-container">
        <h1 class="checkout-title">Checkout</h1>
        <div style="display:flex;flex-wrap:wrap;gap:32px;">
            <div style="flex:2;min-width:320px;">
                <form id="checkout-form" method="post" action="#">
                    <div class="payment-section" style="margin-bottom:18px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <div style="font-size:1.05rem;color:#888;">Delivery Method</div>
                                <div style="font-weight:700;font-size:1.2rem;">Delivery</div>
                            </div>
                            <a href="#" style="color:#1976d2;font-size:1rem;">Change</a>
                        </div>
                    </div>
                    <div class="payment-section" style="margin-bottom:18px;">
                        <label for="delivery-address" style="font-size:1.05rem;color:#888;">Delivery Address</label>
                        <textarea id="delivery-address" name="delivery-address" rows="3" placeholder="Enter your delivery address" required style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid #a97be0;margin-bottom:12px;"></textarea>
                        <a href="#" style="color:#1976d2;font-size:1rem;float:right;">Change</a>
                    </div>
                    <div class="payment-section" style="margin-bottom:18px;">
                        <label for="delivery-method" style="font-size:1.05rem;color:#888;">Delivery Option</label>
                        <select id="delivery-method" name="delivery-method" required>
                            <option value="">Select Delivery Option</option>
                            <option value="standard">Standard Delivery (2-5 days) - R35</option>
                            <option value="express">Express Delivery (1-2 days) - R50</option>
                        </select>
                    </div>
                    <div class="payment-section" style="margin-bottom:18px;">
                        <label for="payment-method" style="font-size:1.05rem;color:#888;">Payment Method</label>
                        <select id="payment-method" name="payment-method" required>
                            <option value="">Select Payment Method</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="eft">EFT</option>
                        </select>
                        <div id="card-details" style="display:none;">
                            <label for="card-number">Card Number</label>
                            <input type="text" id="card-number" name="card-number" maxlength="19" placeholder="1234 5678 9012 3456">
                            <label for="card-expiry">Expiry Date</label>
                            <input type="text" id="card-expiry" name="card-expiry" maxlength="5" placeholder="MM/YY">
                            <label for="card-cvc">CVC</label>
                            <input type="text" id="card-cvc" name="card-cvc" maxlength="4" placeholder="CVC">
                        </div>
                    </div>
                    <button class="checkout-btn" type="submit">Pay & Place Order</button>
                </form>
            </div>
            <div style="flex:1;min-width:280px;">
                <div class="payment-section" style="margin-bottom:18px;">
                    <div style="font-weight:700;font-size:1.1rem;color:#444;margin-bottom:8px;">Order Summary</div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                        <span>1 Item</span>
                        <span>R 499</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                        <span>Delivery fee</span>
                        <span>R 35</span>
                    </div>
                    <hr style="margin:10px 0;">
                    <div style="display:flex;justify-content:space-between;font-weight:700;font-size:1.15rem;">
                        <span>TO PAY:</span>
                        <span style="color:#1a7f37;">R 534</span>
                    </div>
                    <button class="checkout-btn" type="submit" style="margin-top:18px;background:#1a7f37;">PAY WITH CARD</button>
                    <div style="margin-top:10px;text-align:center;color:#888;font-size:0.98rem;"><span style="font-size:1.1rem;">&#128274;</span> Secure Checkout</div>
                </div>
                <div class="payment-section">
                    <div style="font-weight:700;font-size:1.05rem;margin-bottom:8px;">Items for delivery</div>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <img src="assets/images/BOOST.png" alt="Product" style="width:48px;height:48px;border-radius:8px;object-fit:cover;">
                        <div>
                            <div style="font-weight:600;">Abanyana Package</div>
                            <div style="font-size:0.98rem;color:#888;">Qty: 2</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="main-footer">
        <?php include 'footer.php'; ?>
    </footer>
    <script>
        // Remove item row
        function removeRow(btn) {
            const row = btn.closest('tr');
            row.parentNode.removeChild(row);
        }
        // Show/hide card details
        document.getElementById('payment-method').addEventListener('change', function() {
            document.getElementById('card-details').style.display = this.value === 'card' ? 'block' : 'none';
        });
    </script>
</body>
</html>
