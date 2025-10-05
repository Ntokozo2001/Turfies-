<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - Turfies Exam Care</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="main-header">
        <?php include 'navbar.php'; ?>
    </header>
    <main class="main-flex">
        <aside class="sidebar">
            <h2>Buyer Options</h2>
            <ul>
                <li><a href="wishlist.php">My Wishlist</a></li>
                <li><a href="cart.php">My Cart</a></li>
                <li><a href="#">Settings</a></li>
                <li><a href="#">Log Out</a></li>
                <li><a href="index.php">Back To Market</a></li>
            </ul>
        </aside>
        <section class="content-section">
            <h1>My Cart</h1>
            <div class="cart-table-wrapper">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Abanyana</strong><br><span class="cart-product-desc">Package</span></td>
                            <td><input type="number" value="12" min="1" class="cart-qty-input"></td>
                            <td><strong>R200.00</strong></td>
                            <td><button class="cart-delete-btn">Delete</button></td>
                        </tr>
                        <!-- More rows can be added here -->
                    </tbody>
                </table>
                <a href="checkout.php" class="checkout-btn">Check out</a>
            </div>
        </section>
    </main>
    <footer class="main-footer">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>