<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - Turfies Exam Care</title>
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
            <h1>My Wishlist</h1>
            <div class="wishlist-table">
                <p>Your wishlist is empty.</p>
            </div>
        </section>
    </main>
    <footer class="main-footer">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>