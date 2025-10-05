<?php /* Turfies Exam Care Navbar - include this file in all pages */ ?>
<div class="header-top" style="display:flex;align-items:center;justify-content:space-between;padding:12px 24px;">
    <div class="logo">
                <img src="/Turfies Code/Frontend/assets/images/logo.jpg" alt="Turfies Logo" style="height:38px;width:auto;vertical-align:middle;margin-right:10px;border-radius:8px;box-shadow:0 1px 4px #0002;">
    <span class="brand-yellow">Turfies</span> 
    </div>
    <div class="search-bar">
        <form action="/Turfies Code/Frontend/search-results.php" method="GET" style="display: flex; align-items: center;">
            <div style="position: relative;">
                <input type="text" name="q" id="navSearchInput" placeholder="Search for products" style="width:360px;max-width:100%;padding:8px 16px;font-size:1rem;border-radius:8px;border:1px solid #ccc;" autocomplete="off">
                <div id="navSearchSuggestions" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #ddd;border-top:none;border-radius:0 0 8px 8px;max-height:200px;overflow-y:auto;z-index:1000;box-shadow:0 4px 6px rgba(0,0,0,0.1);"></div>
            </div>
            <button type="submit" style="background:#a97be0;color:#fff;border:none;padding:8px 12px;margin-left:5px;border-radius:8px;cursor:pointer;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            </button>
        </form>
    </div>
    <div class="hamburger" id="hamburger-menu" aria-label="Open navigation" tabindex="0">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <nav class="main-nav" id="main-nav" style="display:flex;gap:24px;margin:0;">
    <a href="/Turfies Code/Frontend/index.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='index.php'){echo ' active';} ?>">Home</a>
    <a href="/Turfies Code/Frontend/about.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='about.php'){echo ' active';} ?>">About</a>
    <a href="/Turfies Code/Frontend/product.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='product.php'){echo ' active';} ?>">Product</a>
    <a href="/Turfies Code/Frontend/wishlist.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='wishlist.php'){echo ' active';} ?>">Wishlist</a>
    <a href="/Turfies Code/Frontend/cart.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='cart.php'){echo ' active';} ?>">Cart</a>
    <a href="/Turfies Code/Frontend/checkout.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='checkout.php'){echo ' active';} ?>">Checkout</a>
    <a href="/Turfies Code/Frontend/contact.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='contact.php'){echo ' active';} ?>">Contact</a>
    </nav>
    <div class="user-dropdown" style="position:relative;display:flex;align-items:center;">
        <button id="userDropdownBtn" class="nav-link signup-link" title="Account" style="background:none;border:none;display:flex;align-items:center;gap:4px;cursor:pointer;padding:0;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M21 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/></svg>
        </button>
        <div id="userDropdownMenu" style="display:none;position:absolute;top:36px;right:0;background:#fff;border-radius:8px;box-shadow:0 2px 12px #0002;min-width:160px;z-index:2000;overflow:hidden;">
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <!-- Logged in user options -->
                <a href="/Turfies Code/Frontend/account.php" class="dropdown-item" style="display:block;padding:12px 18px;color:#3d2176;text-decoration:none;font-weight:500;">👤 My Account</a>
                <a href="/Turfies Code/backend/logout.php" class="dropdown-item" style="display:block;padding:12px 18px;color:#e74c3c;text-decoration:none;font-weight:500;">🚪 Logout</a>
            <?php else: ?>
                <!-- Guest user options -->
                <a href="/Turfies Code/Frontend/login.php" class="dropdown-item" style="display:block;padding:12px 18px;color:#3d2176;text-decoration:none;font-weight:500;">🔑 Sign In</a>
                <a href="/Turfies Code/Frontend/signup.php" class="dropdown-item" style="display:block;padding:12px 18px;color:#3d2176;text-decoration:none;font-weight:500;">📝 Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    const hamburger = document.getElementById('hamburger-menu');
    const nav = document.getElementById('main-nav');
    hamburger.addEventListener('click', () => {
        nav.classList.toggle('open');
    });
    hamburger.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' || e.key === ' ') nav.classList.toggle('open');
    });
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 900 && nav.classList.contains('open')) {
            if (!nav.contains(e.target) && !hamburger.contains(e.target)) {
                nav.classList.remove('open');
            }
        }
    });
    // User dropdown logic
    const userDropdownBtn = document.getElementById('userDropdownBtn');
    const userDropdownMenu = document.getElementById('userDropdownMenu');
    userDropdownBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        userDropdownMenu.style.display = userDropdownMenu.style.display === 'block' ? 'none' : 'block';
    });
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.user-dropdown')) {
            userDropdownMenu.style.display = 'none';
        }
    });

    // Search functionality
    const navSearchInput = document.getElementById('navSearchInput');
    const navSearchSuggestions = document.getElementById('navSearchSuggestions');

    if (navSearchInput && navSearchSuggestions) {
        navSearchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            if (query.length >= 2) {
                fetch(`/Turfies Code/backend/search.php?action=suggestions&query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(suggestions => {
                        if (suggestions.length > 0) {
                            navSearchSuggestions.innerHTML = suggestions
                                .map(suggestion => `<div style="padding:10px;cursor:pointer;border-bottom:1px solid #eee;" onclick="selectNavSuggestion('${suggestion.replace(/'/g, "\\'")}')" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">${suggestion}</div>`)
                                .join('');
                            navSearchSuggestions.style.display = 'block';
                        } else {
                            navSearchSuggestions.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching search suggestions:', error);
                        navSearchSuggestions.style.display = 'none';
                    });
            } else {
                navSearchSuggestions.style.display = 'none';
            }
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-bar')) {
                navSearchSuggestions.style.display = 'none';
            }
        });

        // Handle Enter key for search
        navSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                navSearchSuggestions.style.display = 'none';
            }
        });
    }

    // Function to select suggestion
    function selectNavSuggestion(suggestion) {
        if (navSearchInput) {
            navSearchInput.value = suggestion;
            navSearchSuggestions.style.display = 'none';
            // Optionally submit the form automatically
            navSearchInput.closest('form').submit();
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
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