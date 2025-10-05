<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turfies Exam Care</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="main-header">
        <?php include 'navbar.php'; ?>
    </header>


    <main>
        <section class="banner-section">
            <div class="banner-video" style="position:relative;max-width:100vw;width:100vw;height:500px;margin:0 auto;overflow:hidden;display:flex;align-items:center;justify-content:center;">
                <video autoplay muted loop style="width:100%;height:100%;object-fit:cover;">
                    <source src="assets/images/Turfies video.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <button id="banner-prev" style="position:absolute;top:50%;left:20px;transform:translateY(-50%);background:rgba(169,123,224,0.7);border:none;color:#fff;font-size:2rem;border-radius:50%;width:38px;height:38px;cursor:pointer;z-index:2;display:none;">&#10094;</button>
                <button id="banner-next" style="position:absolute;top:50%;right:20px;transform:translateY(-50%);background:rgba(169,123,224,0.7);border:none;color:#fff;font-size:2rem;border-radius:50%;width:38px;height:38px;cursor:pointer;z-index:2;display:none;">&#10095;</button>
            </div>
            <div class="welcome-box" style="background:#faf8fd;border-radius:14px;padding:32px 24px;margin:32px auto 0 auto;max-width:600px;box-shadow:0 2px 16px rgba(169,123,224,0.08);text-align:center;">
                <h1 style="color:#a97be0;font-size:2.2rem;margin-bottom:10px;font-weight:700;letter-spacing:1px;">Welcome to Turfies Exam Care</h1>
                <p style="font-size:1.15rem;color:#444;margin-bottom:18px;">
                    The remedy for your exam stress.<br>
                    <span style="color:#a97be0;font-weight:500;">A Package made with Love!</span>
                </p>
                <ul style="list-style:none;padding:0;margin:0 0 22px 0;display:flex;flex-wrap:wrap;justify-content:center;gap:18px;">
                    <li style="background:#fff;border:1px solid #a97be0;border-radius:8px;padding:10px 18px;min-width:140px;box-shadow:0 1px 4px rgba(169,123,224,0.06);font-size:1rem;color:#a97be0;display:flex;align-items:center;gap:8px;">
                        <svg width="20" height="20" fill="none" stroke="#a97be0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><circle cx="10" cy="10" r="9"/><path d="M7 10l2 2 4-4"/></svg>
                        Fast Delivery
                    </li>
                    <li style="background:#fff;border:1px solid #a97be0;border-radius:8px;padding:10px 18px;min-width:140px;box-shadow:0 1px 4px rgba(169,123,224,0.06);font-size:1rem;color:#a97be0;display:flex;align-items:center;gap:8px;">
                        <svg width="20" height="20" fill="none" stroke="#a97be0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><circle cx="10" cy="10" r="9"/><path d="M7 10l2 2 4-4"/></svg>
                        Thoughtful Packages
                    </li>
                    <li style="background:#fff;border:1px solid #a97be0;border-radius:8px;padding:10px 18px;min-width:140px;box-shadow:0 1px 4px rgba(169,123,224,0.06);font-size:1rem;color:#a97be0;display:flex;align-items:center;gap:8px;">
                        <svg width="20" height="20" fill="none" stroke="#a97be0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><circle cx="10" cy="10" r="9"/><path d="M7 10l2 2 4-4"/></svg>
                        Student Approved
                    </li>
                    <li style="background:#fff;border:1px solid #a97be0;border-radius:8px;padding:10px 18px;min-width:140px;box-shadow:0 1px 4px rgba(169,123,224,0.06);font-size:1rem;color:#a97be0;display:flex;align-items:center;gap:8px;">
                        <svg width="20" height="20" fill="none" stroke="#a97be0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><circle cx="10" cy="10" r="9"/><path d="M7 10l2 2 4-4"/></svg>
                        Affordable Prices
                    </li>
                </ul>
                <a href="product.php" style="background:#a97be0;color:#fff;padding:14px 38px;border:none;border-radius:8px;font-size:1.15rem;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,0.07);transition:background 0.2s;display:inline-block;">
                    Shop Now
                </a>
            </div>
        </section>

        <!-- About section with Learn More button -->
        <section class="about-section" style="max-width:600px;margin:32px auto 0 auto;padding:24px 18px 18px 18px;border:1.5px solid #a97be0;border-radius:16px;background:#faf8fd;">
            <h2 style="margin-top:0;color:#a97be0;text-align:center;">About Turfies Exam Care</h2>
            <p style="margin-bottom:18px;">
                Turfies Exam Care is dedicated to supporting students during exam season with thoughtfully curated care packages. Our mission is to bring comfort, motivation, and a touch of joy to every student facing academic challenges.
            </p>
            <div style="text-align:center;">
                <a href="about.php" style="background:#a97be0;color:#fff;padding:10px 28px;border:none;border-radius:7px;font-size:1rem;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,0.06);transition:background 0.2s;display:inline-block;">
                    Learn More
                </a>
            </div>
        </section>

        <section class="carousel-section">
            <h2 class="carousel-title">Featured Packages</h2>
            <div class="carousel-wrapper">
                <a href="product.php?package=saver" class="carousel-item" style="text-decoration:none;color:inherit;">
                    <img src="assets/images/gift box.jpg" alt="Exam Care Bag">
                    <div class="carousel-caption">Best Seller</div>
                </a>
                <a href="product.php?package=premium" class="carousel-item" style="text-decoration:none;color:inherit;">
                    <img src="assets/images/gift box 3.jpg" alt="Gift Box">
                    <div class="carousel-caption">Gift Special</div>
                </a>
                <a href="product.php?package=pro" class="carousel-item" style="text-decoration:none;color:inherit;">
                    <img src="assets/images/gift box 2.jpg" alt="Exam Care Bag">
                    <div class="carousel-caption">Student Favorite</div>
                </a>
            </div>
        </section>

        <div style="display:flex;justify-content:center;margin:32px 0;">
            <a href="product.php" style="background:#a97be0;color:#fff;padding:16px 36px;border:none;border-radius:8px;font-size:1.2rem;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,0.07);transition:background 0.2s;">
                View Products
            </a>
        </div>

        <!-- Testimonials Section with full box border -->
        <section class="testimonials-section" style="max-width:420px;margin:40px auto 0 auto;padding:28px 18px 18px 18px;border:2px solid #a97be0;border-radius:20px;background:#fff;box-shadow:0 2px 12px rgba(169,123,224,0.06);">
            <h2 class="testimonials-title">What Our Customers Say</h2>
            <div class="testimonials-wrapper" id="testimonial-slideshow">
                <div class="testimonial-slide" style="display: block;">
                    <img src="assets/images/customer 1.jpg" alt="Customer 1" class="testimonial-img" style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:4px solid #a97be0;">
                    <p>"Absolutely loved the exam care package! It made my finals so much easier."</p>
                    <span>- Lerato M.</span>
                </div>
                <div class="testimonial-slide" style="display: none;">
                    <img src="assets/images/customer 2.jpg" alt="Customer 2" class="testimonial-img" style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:4px solid #a97be0;">
                    <p>"Fast delivery and great quality. Highly recommend Turfies!"</p>
                    <span>- Sipho K.</span>
                </div>
                <div class="testimonial-slide" style="display: none;">
                    <img src="assets/images/customer 3.jpg" alt="Customer 3" class="testimonial-img" style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:4px solid #a97be0;">
                    <p>"A thoughtful gift for any student. Will order again!"</p>
                    <span>- Nomsa T.</span>
                </div>
                <div style="display:flex;justify-content:center;align-items:center;gap:30px;margin-top:18px;max-width:300px;margin-left:auto;margin-right:auto;background:#fff;">
                    <button class="testimonial-prev" style="background:none;border:none;font-size:2rem;cursor:pointer;color:#a97be0;">&#10094;</button>
                    <button class="testimonial-next" style="background:none;border:none;font-size:2rem;cursor:pointer;color:#a97be0;">&#10095;</button>
                </div>
            </div>
        </section>

    </main>

    <footer class="main-footer">
        <?php include 'footer.php'; ?>
    </footer>
    <script>
        let currentTestimonial = 0;
        const testimonials = document.querySelectorAll('.testimonial-slide');
        const prevBtn = document.querySelector('.testimonial-prev');
        const nextBtn = document.querySelector('.testimonial-next');

        function showTestimonial(index) {
            testimonials.forEach((slide, i) => {
                slide.style.display = i === index ? 'block' : 'none';
            });
        }

        nextBtn.addEventListener('click', () => {
            currentTestimonial = (currentTestimonial + 1) % testimonials.length;
            showTestimonial(currentTestimonial);
        });

        prevBtn.addEventListener('click', () => {
            currentTestimonial = (currentTestimonial - 1 + testimonials.length) % testimonials.length;
            showTestimonial(currentTestimonial);
        });

        // Show first testimonial initially
        showTestimonial(currentTestimonial);
    </script>
</body>
</html>
