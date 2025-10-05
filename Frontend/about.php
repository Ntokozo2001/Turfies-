<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Turfies Exam Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/assets/style.css">
    <style>
        /* Timeline Animation Styles */
        .timeline-stepper-fixed .dot {
            width: 16px;
            height: 16px;
            background: #a97be0;
            border-radius: 50%;
            border: 3px solid #ffbe19;
            z-index: 2;
            display: block;
            position: relative;
            animation: pulseDot 1.5s infinite;
        }
        @keyframes pulseDot {
            0% { box-shadow: 0 0 0 0 #ffbe19aa; }
            70% { box-shadow: 0 0 0 10px #ffbe1900; }
            100% { box-shadow: 0 0 0 0 #ffbe1900; }
        }
        .timeline-stepper-fixed .line {
            flex: 1;
            width: 4px;
            background: #a97be0;
            display: block;
            margin-top: 0;
            position: relative;
            overflow: hidden;
        }
        .timeline-stepper-fixed .line .highlight {
            position: absolute;
            left: 0;
            width: 100%;
            height: 30%;
            background: linear-gradient(180deg, #ffbe19 0%, #fff0 100%);
            animation: moveHighlight 2.5s linear infinite;
        }
        @keyframes moveHighlight {
            0% { top: -30%; }
            100% { top: 100%; }
        }
        body {
            overflow-x: hidden;
        }
        .hero-section {
            background: linear-gradient(135deg, #a97be0, #faf8fd);
            color: #fff;
            padding: 80px 0;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 3rem;
            font-weight: 700;
        }
        .section-padding {
            padding: 60px 0;
        }
        .team-card {
            text-align: center;
            margin-bottom: 30px;
        }
        .team-card img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }
        .award-img {
            width: 200px;
            height: auto;
            margin: 20px auto;
            display: block;
        }
        .timeline-container {
            max-width: 700px;
            margin: 0 auto;
        }
        .timeline-stepper {
            list-style: none;
            padding: 0;
            position: relative;
        }
        .timeline-stepper li {
            margin-bottom: 36px;
            position: relative;
        }
        .timeline-stepper li div {
            position: absolute;
            left: -30px;
            top: 0;
            width: 16px;
            height: 16px;
            background: #a97be0;
            border-radius: 50%;
            border: 3px solid #ffbe19;
        }
        .timeline-stepper li div.content {
            margin-left: 20px;
            padding-left: 30px;
        }
        .timeline-stepper div.line {
            position: absolute;
            left: -22px;
            top: 16px;
            bottom: 16px;
            width: 4px;
            background: #a97be0;
            z-index: -1;
        }
        /* Hamburger styles */
        .hamburger {
            display: none;
            flex-direction: column;
            justify-content: center;
            width: 32px;
            height: 32px;
            cursor: pointer;
            z-index: 1001;
        }
        .hamburger span {
            height: 4px;
            width: 100%;
            background: #fff;
            margin: 4px 0;
            border-radius: 2px;
            transition: 0.3s;
        }
        @media (max-width: 900px) {
            .main-nav {
                position: fixed;
                top: 0; right: 0;
                height: 100vh;
                width: 220px;
                background: #0a2a47;
                flex-direction: column;
                align-items: flex-start;
                padding: 60px 24px 24px 24px;
                gap: 18px;
                transform: translateX(100%);
                transition: transform 0.3s;
                z-index: 1000;
            }
            .main-nav.open {
                transform: translateX(0);
            }
            .header-top {
                flex-wrap: wrap;
            }
            .hamburger {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <?php include '../Frontend/navbar.php'; ?>
    </header>

    <section class="hero-section">
        <div class="container">
            <h1>About Turfies Exam Care</h1>
            <p class="lead">Supporting students with love and care during exam season.</p>
        </div>
    </section>

    <main class="container">
        <section class="section-padding">
            <div class="row">
                <div class="col-md-6">
                    <h2 style="color:#a97be0;">About Turfies Exam Care Packages</h2>
                    <p style="font-size:1.1rem;line-height:1.6;">
                        Turfies Exam Care is a business dedicated to supporting students during exam season by providing thoughtful care packages filled with essentials like stationery, snacks, and energizers to help students stay focused, motivated, and confident.
                    </p>
                </div>
                <div class="col-md-6">
                    <!-- Video instead of slideshow -->
                    <video controls poster="assets/images/gift box.jpg" style="max-width:350px;width:100%;height:260px;object-fit:cover;display:block;margin:0 auto;border-radius:12px;">
                        <source src="assets/images/About Turfies video.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </section>

        <section class="section-padding bg-light">
            <h2 style="color:#a97be0;text-align:center;margin-bottom:40px;">About Us</h2>
            <div class="row">
                <div class="col-12 text-center team-card">
                    <img src="assets/images/founder.jpg" alt="Jerry Pete and Percy - Founders" style="width:200px; height:200px; border-radius:50%; object-fit:cover; margin-bottom:15px;">
                    <h4>Jerry Pete & Percy</h4>
                    <p>Founder & Operations Manager</p>
                </div>
            </div>
            <p class="text-center mt-4" style="font-size:1.1rem;line-height:1.6;">
                Founded by Jerry Pete (Founder) and managed with Percy (Operations Manager). The team understands the stress of exam season and created Turfies to provide students with a convenient and supportive way to stay fueled and prepared.
            </p>
        </section>

        <section class="section-padding">
            <div class="row">
                <div class="col-md-6">
                    <h2 style="color:#a97be0;">Our Story So Far</h2>
                    <p style="font-size:1.1rem;line-height:1.6;">
                        Turfies has been serving both university and high school students for 5 years, offering tools and treats to help them succeed during exams.
                    </p>
                </div>
                <div class="col-md-6">
                    <img src="assets/images/our story.jpg" alt="Our Story" class="img-fluid rounded">
                </div>
            </div>
        </section>

        <!-- Timeline/Stepper for Our Story So Far -->
        <section class="section-padding" style="padding-top:20px;">
            <h3 style="color:#1976d2;text-align:center;margin-bottom:32px;">Milestones Timeline</h3>
            <div class="timeline-container" style="max-width:900px;margin:0 auto;position:relative;">
                <div style="position:absolute;left:50%;top:0;bottom:0;width:4px;background:#1976d2;z-index:0;transform:translateX(-50%);box-shadow:0 0 16px #1976d299;"></div>
                <div style="position:relative;z-index:1;">
                    <!-- 2020 -->
                    <div style="display:flex;align-items:center;margin-bottom:80px;">
                        <div style="flex:1;text-align:right;padding-right:40px;">
                            <div style="display:inline-block;background:rgba(255,255,255,0.10);border-radius:16px;padding:24px 32px 18px 32px;box-shadow:0 4px 24px #0002;border:2px solid #ffbe19;backdrop-filter:blur(2px);min-width:220px;max-width:340px;">
                                <div style="color:#1976d2;font-size:1.1rem;font-weight:600;margin-bottom:8px;">2020</div>
                                <div style="color:#222;font-size:1.05rem;">Turfies Exam Care founded by Jerry Pete.</div>
                            </div>
                        </div>
                        <div style="position:relative;z-index:2;">
                            <div style="width:48px;height:48px;background:#1976d2;box-shadow:0 0 24px #1976d288;display:flex;align-items:center;justify-content:center;border-radius:50%;border:5px solid #ffbe19;">
                                <span style="font-size:1.7rem;color:#fff;">&#128161;</span>
                            </div>
                        </div>
                        <div style="flex:1;"></div>
                    </div>
                    <!-- 2021 -->
                    <div style="display:flex;align-items:center;margin-bottom:80px;">
                        <div style="flex:1;"></div>
                        <div style="position:relative;z-index:2;">
                            <div style="width:48px;height:48px;background:#1976d2;box-shadow:0 0 24px #1976d288;display:flex;align-items:center;justify-content:center;border-radius:50%;border:5px solid #ffbe19;">
                                <span style="font-size:1.7rem;color:#fff;">&#128230;</span>
                            </div>
                        </div>
                        <div style="flex:1;text-align:left;padding-left:40px;">
                            <div style="display:inline-block;background:rgba(255,255,255,0.10);border-radius:16px;padding:24px 32px 18px 32px;box-shadow:0 4px 24px #0002;border:2px solid #ffbe19;backdrop-filter:blur(2px);min-width:220px;max-width:340px;">
                                <div style="color:#1976d2;font-size:1.1rem;font-weight:600;margin-bottom:8px;">2021</div>
                                <div style="color:#222;font-size:1.05rem;">First 100 care packages delivered to students.</div>
                            </div>
                        </div>
                    </div>
                    <!-- 2022 -->
                    <div style="display:flex;align-items:center;margin-bottom:80px;">
                        <div style="flex:1;text-align:right;padding-right:40px;">
                            <div style="display:inline-block;background:rgba(255,255,255,0.10);border-radius:16px;padding:24px 32px 18px 32px;box-shadow:0 4px 24px #0002;border:2px solid #ffbe19;backdrop-filter:blur(2px);min-width:220px;max-width:340px;">
                                <div style="color:#1976d2;font-size:1.1rem;font-weight:600;margin-bottom:8px;">2022</div>
                                <div style="color:#222;font-size:1.05rem;">Won Absa Youth Entrepreneur Award.</div>
                            </div>
                        </div>
                        <div style="position:relative;z-index:2;">
                            <div style="width:48px;height:48px;background:#1976d2;box-shadow:0 0 24px #1976d288;display:flex;align-items:center;justify-content:center;border-radius:50%;border:5px solid #ffbe19;">
                                <span style="font-size:1.7rem;color:#fff;">&#127942;</span>
                            </div>
                        </div>
                        <div style="flex:1;"></div>
                    </div>
                    <!-- 2023 -->
                    <div style="display:flex;align-items:center;margin-bottom:80px;">
                        <div style="flex:1;"></div>
                        <div style="position:relative;z-index:2;">
                            <div style="width:48px;height:48px;background:#1976d2;box-shadow:0 0 24px #1976d288;display:flex;align-items:center;justify-content:center;border-radius:50%;border:5px solid #ffbe19;">
                                <span style="font-size:1.7rem;color:#fff;">&#128188;</span>
                            </div>
                        </div>
                        <div style="flex:1;text-align:left;padding-left:40px;">
                            <div style="display:inline-block;background:rgba(255,255,255,0.10);border-radius:16px;padding:24px 32px 18px 32px;box-shadow:0 4px 24px #0002;border:2px solid #ffbe19;backdrop-filter:blur(2px);min-width:220px;max-width:340px;">
                                <div style="color:#1976d2;font-size:1.1rem;font-weight:600;margin-bottom:8px;">2023</div>
                                <div style="color:#222;font-size:1.05rem;">Expanded to serve high schools and universities nationwide.</div>
                            </div>
                        </div>
                    </div>
                    <!-- 2024 -->
                    <div style="display:flex;align-items:center;margin-bottom:0;">
                        <div style="flex:1;text-align:right;padding-right:40px;">
                            <div style="display:inline-block;background:rgba(255,255,255,0.10);border-radius:16px;padding:24px 32px 18px 32px;box-shadow:0 4px 24px #0002;border:2px solid #ffbe19;backdrop-filter:blur(2px);min-width:220px;max-width:340px;">
                                <div style="color:#1976d2;font-size:1.1rem;font-weight:600;margin-bottom:8px;">2024</div>
                                <div style="color:#222;font-size:1.05rem;">500+ students supported, 100% satisfaction rate.</div>
                            </div>
                        </div>
                        <div style="position:relative;z-index:2;">
                            <div style="width:48px;height:48px;background:#1976d2;box-shadow:0 0 24px #1976d288;display:flex;align-items:center;justify-content:center;border-radius:50%;border:5px solid #ffbe19;">
                                <span style="font-size:1.7rem;color:#fff;">&#128640;</span>
                            </div>
                        </div>
                        <div style="flex:1;"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-padding bg-light">
            <h2 style="color:#a97be0;text-align:center;margin-bottom:40px;">Our Impact</h2>
            <p class="text-center" style="font-size:1.1rem;line-height:1.6;">
                Turfies has supported numerous students who have gone on to graduate and achieve academic goals, contributing positively to their journey.
            </p>
            <div class="row mt-4">
                <div class="col-md-4 text-center">
                    <h3 style="color:#a97be0;">500+</h3>
                    <p>Students Supported</p>
                </div>
                <div class="col-md-4 text-center">
                    <h3 style="color:#a97be0;">5 Years</h3>
                    <p>Of Service</p>
                </div>
                <div class="col-md-4 text-center">
                    <h3 style="color:#a97be0;">100%</h3>
                    <p>Satisfaction Rate</p>
                </div>
            </div>
        </section>

        <section class="section-padding">
            <h2 style="color:#a97be0;text-align:center;margin-bottom:40px;">Awards & Recognition</h2>
            <div class="text-center">
                <img src="assets/images/award.jpg" alt="Absa Youth Entrepreneur Award" class="award-img">
                <p style="font-size:1.1rem;line-height:1.6;">
                    Winner of the Absa Youth Entrepreneur Award (2022), recognizing Turfies as a promising young business.
                </p>
            </div>
        </section>

        <section class="section-padding">
            <h2 style="color:#a97be0;text-align:center;margin-bottom:40px;">Our Mission & Vision</h2>
            <div class="row">
                <div class="col-md-6">
                    <h3>Mission</h3>
                    <p>To empower students with essential tools and treats that boost their confidence and performance during exam periods.</p>
                </div>
                <div class="col-md-6">
                    <h3>Vision</h3>
                    <p>To be the leading provider of exam care packages, supporting academic success across universities and high schools.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <?php include 'footer.php'; ?>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const hamburger = document.getElementById('hamburger-menu');
        const nav = document.getElementById('main-nav');
        hamburger.addEventListener('click', () => {
            nav.classList.toggle('open');
        });
        hamburger.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' || e.key === ' ') nav.classList.toggle('open');
        });
        // Close nav when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 900 && nav.classList.contains('open')) {
                if (!nav.contains(e.target) && !hamburger.contains(e.target)) {
                    nav.classList.remove('open');
                }
            }
        });
    </script>
</body>
</html>
