<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "hotel_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update menu items with high-quality food images
$sql_update_images = "
UPDATE menu_items SET image_url = CASE name
    -- Breakfast Items
    WHEN 'Idli Sambar' THEN 'https://media.istockphoto.com/id/2159618247/photo/idli-vada-with-sambar.jpg?s=612x612&w=0&k=20&c=0HNP26WxESqfA3i3Xr1uTxxpKKYc69d9NRn9Dai4xok='
    WHEN 'Pongal' THEN 'https://www.spiceindiaonline.com/wp-content/uploads/2014/01/Ven-Pongal-3.jpg'
    WHEN 'Vada' THEN 'https://vaya.in/recipes/wp-content/uploads/2018/02/dreamstime_xs_44383666.jpg'
    WHEN 'Poori Masala' THEN 'https://palakkadbusiness.com/Gangashankaram/wp-content/uploads/sites/79/2023/11/Poori-Masala.png'
    WHEN 'Ghee Roast Dosa' THEN 'https://www.squarecut.net/wp-content/uploads/2024/08/crispy-crepes-made-barnyard-millets-lentils-commonly-known-as-milled-ghee-roast-dosa-plated-conical-shape-rolls-served-238893976.webp'

    -- Main Course - Vegetarian
    WHEN 'Paneer Butter Masala' THEN 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgIhLcOIgSfPph9kwyJScX0oZOf9W6XT26Chnlc5uXPP4C8_52cTsozMURL_SDruHd-DQtC9GLHqWKFvqHvnWlsqULIkpwga-6KTUiXW1btD7KQI7oNmljdwykZ1WGZB7QZr8fsqGgqoy4/s2048/paneer+butter+masala+15.JPG'
    WHEN 'Vegetable Biryani' THEN 'https://media.istockphoto.com/id/179085494/photo/indian-biryani.jpg?s=612x612&w=0&k=20&c=VJAUfiuavFYB7PXwisvUhLqWFJ20-9m087-czUJp9Fs='
    WHEN 'Dal Tadka' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSY1l_jZRmr6YriO6mvEXEofhE1yhpb5HES1w&s'
    WHEN 'Kadai Mushroom' THEN 'https://static.toiimg.com/photo/62997250.cms'
    
    -- Main Course - Non-Vegetarian
    WHEN 'Chicken 65' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQuBWfoWkhdZVPd16GGTM93qjTG7AWwPULmPA&s'
    WHEN 'Mutton Curry' THEN 'https://atanurrannagharrecipe.com/wp-content/uploads/2023/03/Best-Mutton-Curry-Recipe-Atanur-Rannaghar.jpg'
    WHEN 'Fish Curry' THEN 'https://www.recipetineats.com/tachyon/2020/10/Goan-Fish-Curry_6-SQ.jpg'
    WHEN 'Prawn Masala' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR_0Z0p1dWKj_Ltu3e_kEqMHAGy7HalMdX8oQ&s'
    
    -- Rice Dishes
    WHEN 'Sambar Rice' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR1VeM9HJJksvX4STFIUUyFUmMwRe3F_ddKag&s'
    WHEN 'Curd Rice' THEN 'https://maharajaroyaldining.com/wp-content/uploads/2024/03/Curd-Rice-1.webp'
    WHEN 'Lemon Rice' THEN 'https://www.flavourstreat.com/wp-content/uploads/2020/12/turmeric-lemon-rice-recipe-02.jpg'
    WHEN 'Coconut Rice' THEN 'https://static.toiimg.com/thumb/52413325.cms?imgsize=190896&width=800&height=800'
    
    -- Breads
    WHEN 'Parotta' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQl2ExoO9ArN3mJ13eP-4AoLHhmgYrDGXCL4Q&s'
    WHEN 'Naan' THEN 'https://www.thespruceeats.com/thmb/MReCj8olqrCsPaGvikesPJie02U=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/naan-leavened-indian-flatbread-1957348-final-08-116a2e523f6e4ee693b1a9655784d9b9.jpg'
    WHEN 'Chapati' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT_freDZrX7LsLnPPwG27dGa443MeYjcsE_mQ&s'
    WHEN 'Butter Roti' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTGFVv0p_3-hNCCIR_1gVoJoXv7YHTCbYzQGw&s'
    
    -- Desserts
    WHEN 'Gulab Jamun' THEN 'https://carveyourcraving.com/wp-content/uploads/2020/09/gulab-jamun-mousse-layered-dessert.jpg'
    WHEN 'Payasam' THEN 'https://www.whiskaffair.com/wp-content/uploads/2020/11/Semiya-Payasam-2-3.jpg'
    WHEN 'Masala Chai' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR8LMM6j0uSjpwGASdoFVtMLW_iojIyFp6ZfQ&s'
    WHEN 'Rasmalai' THEN 'https://prashantcorner.com/cdn/shop/files/RasmalaiSR-2.png?v=1720595089&width=1946'
    WHEN 'Jalebi' THEN 'https://static.toiimg.com/thumb/53099699.cms?imgsize=182393&width=800&height=800'
    WHEN 'Filter Coffee' THEN 'https://www.clubmahindra.com/blog/media/section_images/indianfilt-351110d18aec48f.jpg'
    WHEN 'Buttermilk' THEN 'https://static.toiimg.com/thumb/msid-76625491,imgsize-957295,width-400,resizemode-4/76625491.jpg'
    WHEN 'Fresh Lime Soda' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQIxacpaTgsCyexsCPzWztI8aIFGqnZ3bAKzA&s'
    
    -- Sides
    WHEN 'Onion Pakoda' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTIT8uIqE1VMbvmPrCEQr_Pm7_t9JT486YuxQ&s'
    WHEN 'Paneer 65' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQX-aS8DhH5p_6IFqic0Y4WAfLnbvOjRVkaGA&s'
    WHEN 'Gobi Manchurian' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSJSTFk6lnhBZh05OqwHyuyjjzhrL6321XVUw&s'
    WHEN 'Papad' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQgIjv2Y9pyRYsvpuwQI4DTQ3Qc4YfqGpzXYQ&s'
    
    ELSE image_url
END
WHERE name IN (
    'Idli Sambar', 'Pongal', 'Vada', 'Poori Masala',
    'Paneer Butter Masala', 'Vegetable Biryani', 'Dal Tadka', 'Kadai Mushroom',
    'Chicken 65', 'Mutton Curry', 'Fish Curry', 'Prawn Masala',
    'Sambar Rice', 'Curd Rice', 'Lemon Rice', 'Coconut Rice',
    'Parotta', 'Naan', 'Chapati', 'Butter Roti',
    'Gulab Jamun', 'Payasam', 'Rasmalai', 'Jalebi',
    'Masala Chai', 'Filter Coffee', 'Buttermilk', 'Fresh Lime Soda',
    'Onion Pakoda', 'Paneer 65', 'Gobi Manchurian', 'Papad','Ghee Roast Dosa'
);
";

if ($conn->query($sql_update_images) === FALSE) {
    die("Error updating images: " . $conn->error);
}

// Fetch menu items grouped by category
$sql = "SELECT * FROM menu_items ORDER BY category";
$result = $conn->query($sql);
$menu_items_by_category = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $menu_items_by_category[$row['category']][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chellappa Hotel - Authentic Tamil Cuisine</title>

    <!-- CSS Links -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css\home.css">
</head>

<body>

    <!-- Add this right after the opening <body> tag -->
    <div id="waitingIdBanner" class="waiting-id-banner" style="display: none;">
        <span class="waiting-id-label">Your Waiting ID:</span>
        <span id="waitingIdDisplay" class="waiting-id-number"></span>
    </div>

    <!-- Header Section -->

    <header class="header">

        <h1 class="logo">CHELLAPPA HOTEL</h1>

        <p class="subtitle">Authentic Tamil Cuisine Since 1985</p>

    </header>



    <!-- Premium Navigation -->

    <nav class="navbar">

        <div class="nav-container">

            <a href="#" class="nav-item active" data-section="home"><i class="fas fa-home"></i> Home</a>
            <a href="#" class="nav-item" data-section="offers"><i class="fas fa-percent"></i> Offers</a>
            <a href="#" class="nav-item" data-section="location"><i class="fas fa-map-marker-alt"></i> Location</a>
            <a href="#" class="nav-item" data-section="about"><i class="fas fa-info-circle"></i> About</a>
            <a href="#" class="nav-item" data-section="contact"><i class="fas fa-phone-alt"></i> Contact</a>

            <!-- Add language selector -->
            <div class="language-selector">
                <select id="languageSelect" onchange="changeLanguage(this.value)">
                    <option value="en">English</option>
                    <option value="ta">தமிழ்</option>
                    <option value="hi">हिंदी</option>
                </select>
            </div>

        </div>

    </nav>



    <!-- Main Content -->

    <div class="container" id="home">

        <h2 class="section-title">Our Signature Dishes</h2>

        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search menu items...">
            <i class="fas fa-search search-icon"></i>
        </div>

        <!-- Replace your static menu items with this dynamic loop -->
        <div class="menu-grid">
            <?php foreach ($menu_items_by_category as $category => $items): ?>
            <div class="category-section">
                <h2 class="category-title">
                    <?php echo htmlspecialchars($category); ?>
                </h2>
                <div class="category-items">
                    <?php foreach ($items as $item): ?>
                    <div class="menu-item" data-item-id="<?php echo htmlspecialchars($item['id']); ?>">
                        <div class="menu-item-img-container">
                            <img src="<?php echo htmlspecialchars($item['image_url'] ?? 'https://via.placeholder.com/150'); ?>"
                                alt="<?php echo htmlspecialchars($item['name']); ?>" class="menu-item-img"
                                onerror="this.onerror=null; this.src='https://via.placeholder.com/150';">

                            <div class="availability-badge"
                                data-stock="<?php echo htmlspecialchars($item['stock_quantity']); ?>"
                                data-available="<?php echo $item['stock_quantity'] > 0 ? 'true' : 'false'; ?>">
                                <?php if($item['stock_quantity'] > 0): ?>
                                <?php if($item['stock_quantity'] <= 5): ?>
                                <span class="in-stock low">Only
                                    <?php echo $item['stock_quantity']; ?> left
                                </span>
                                <?php else: ?>
                                <span class="in-stock">In Stock (
                                    <?php echo $item['stock_quantity']; ?>)
                                </span>
                                <?php endif; ?>
                                <?php else: ?>
                                <span class="out-of-stock">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="menu-item-content">
                            <h3 class="menu-item-title">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </h3>
                            <p class="menu-item-desc">
                                <?php echo htmlspecialchars($item['description']); ?>
                            </p>
                            <div class="menu-item-footer">
                                <span class="menu-item-price">
                                    <?php echo htmlspecialchars($item['price']); ?>
                                </span>
                                <button class="add-to-cart" <?php echo $item['stock_quantity'] <=0 ? 'disabled' : '' ;
                                    ?>>
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>



        <!-- Monthly Offers Section (Initially Hidden) -->

        <div class="monthly-offers" id="monthlyOffers" style="display: none;">

            <h3 class="offers-title">This Month's Special Offers</h3>

            <ul class="offer-list">

                <li class="offer-item">

                    <div class="offer-day">Monday - Thursday</div>

                    <div class="offer-desc">15% off on all main courses during lunch hours (12PM - 3PM)</div>

                </li>

                <li class="offer-item">

                    <div class="offer-day">Friday</div>

                    <div class="offer-desc">Buy 1 Get 1 Free on all desserts from 6PM onwards</div>

                </li>

                <li class="offer-item">

                    <div class="offer-day">Saturday</div>

                    <div class="offer-desc">Family special: 20% off on total bill for groups of 4 or more</div>

                </li>

                <li class="offer-item">

                    <div class="offer-day">Sunday</div>

                    <div class="offer-desc">Grand buffet lunch with unlimited dishes for just ₹350 per person</div>

                </li>

                <li class="offer-item">

                    <div class="offer-day">Everyday</div>

                    <div class="offer-desc">10% discount for senior citizens and students (valid ID required)</div>

                </li>

            </ul>

        </div>



        <!-- About Section -->

        <div class="about-section" id="about" style="display: none;">

            <div class="about-content">

                <div class="about-text">

                    <h3>Our Heritage</h3>

                    <p>Established in 1985, Chellappa Hotel has been serving authentic Tamil cuisine for over three
                        decades. What began as a small eatery in Tenkasi has grown into a beloved culinary landmark,
                        cherished by locals and visitors alike.</p>

                    <p>Our founder, Mr. Chellappa, started with a simple vision: to preserve the traditional flavors of
                        Tamil Nadu while maintaining uncompromising quality. Today, his legacy continues under the
                        guidance of his son, who upholds the same values of authenticity and hospitality.</p>

                    <p>We take pride in using only the freshest local ingredients, traditional cooking methods, and
                        recipes passed down through generations. Each dish tells a story of our rich culinary heritage.
                    </p>

                </div>

                <div class="about-image">

                    <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                        alt="Chellappa Hotel Interior">

                </div>

            </div>

        </div>



        <!-- Contact Section -->

        <div class="contact-section" id="contact" style="display: none;">

            <div class="contact-content">

                <div class="contact-info">

                    <h3>Get In Touch</h3>

                    <p><i class="fas fa-map-marker-alt"></i> 12 Temple Road, Near Thirumalai Nayakkar Mahal, Tenkasi,
                        Tamil Nadu 627811</p>

                    <p><i class="fas fa-phone-alt"></i> +91 4634 223344</p>

                    <p><i class="fas fa-mobile-alt"></i> +91 98765 43210</p>

                    <p><i class="fas fa-envelope"></i> info@chellappahotel.com</p>

                    <p><i class="fas fa-clock"></i> Open Daily: 7:00 AM - 11:00 PM</p>

                    <div class="social-links">

                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>

                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>

                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>

                        <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>

                    </div>

                </div>

                <div class="contact-form">

                    <h3>Send Us a Message</h3>

                    <form id="contactForm">

                        <div class="form-group">

                            <label for="name">Your Name</label>

                            <input type="text" id="name" name="name" required>

                            <div class="error-message">Please enter your name</div>

                        </div>

                        <div class="form-group">

                            <label for="email">Email Address</label>

                            <input type="email" id="email" name="email" required>

                            <div class="error-message">Please enter a valid email</div>

                        </div>

                        <div class="form-group">

                            <label for="phone">Phone Number</label>

                            <input type="tel" id="phone" name="phone">

                        </div>

                        <div class="form-group">

                            <label for="message">Your Message</label>

                            <textarea id="message" name="message" required></textarea>

                            <div class="error-message">Please enter your message</div>

                        </div>

                        <button type="submit" class="submit-btn" id="submitBtn">

                            <i class="fas fa-paper-plane"></i> Send Message

                        </button>

                        <div class="success-message" id="successMessage">

                            <i class="fas fa-check-circle"></i>

                            <p>Thank you for your message! We'll contact you soon.</p>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>



    <!-- Cart Preview -->

    <div class="cart-preview" id="cartPreview">

        <i class="fas fa-shopping-cart"></i>

        <span class="cart-count" id="cartCount">0</span>

    </div>



    <!-- Bill Modal -->

    <div class="bill-modal" id="billModal">
        <div class="bill-container" id="billContainer">
            <div class="bill-header">
                <h3 class="bill-title">Your Order Bill</h3>
                <button class="close-bill" id="closeBill">&times;</button>
            </div>
            <div class="bill-content">
                <div class="bill-items" id="billItems">
                    <div class="empty-cart">
                        <i class="fas fa-shopping-basket"></i>
                        <p>Your cart is empty</p>
                    </div>
                </div>
                <div class="bill-total">
                    <span>Total:</span>
                    <span class="bill-total-amount" id="billTotal">₹0</span>
                </div>

                <!-- Add this new section -->
                <div class="order-details">
                    <h4>Order Location</h4>
                    <div class="location-options">
                        <label>
                            <input type="radio" name="orderType" value="table" checked>
                            Table Number
                        </label>
                        <label>
                            <input type="radio" name="orderType" value="room">
                            Room Number
                        </label>
                        <label>
                            <input type="radio" name="orderType" value="takeaway">
                            On the Way
                        </label>
                    </div>

                    <div class="location-input" id="locationInputContainer">
                        <input type="number" id="locationNumber" placeholder="Enter table number" min="1">
                        <span class="error-message" style="display: none; color: red;">This field is required</span>
                    </div>
                </div>

                <div class="bill-actions">
                    <button class="bill-btn bill-btn-secondary" id="clearCart">
                        <i class="fas fa-trash-alt"></i> Clear Cart
                    </button>
                    <button class="bill-btn bill-btn-primary" id="checkoutBtn">
                        <i class="fas fa-credit-card"></i> Place Order
                    </button>
                </div>
            </div>
        </div>
    </div>



    <!-- Location Modal - Updated for Chellappa Hotel -->

    <div class="location-modal" id="locationModal">

        <div class="location-container" id="locationContainer">

            <div class="location-header">

                <h3 class="location-title">Our Location</h3>

                <button class="close-location" id="closeLocation">&times;</button>

            </div>

            <div class="location-content">

                <div class="location-map">

                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3932.041389346015!2d77.30818931537399!3d8.95598019364245!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3b0429c8b5c3a4a5%3A0x7e9b9ef9f0e7f4d5!2sChellappa%20Hotel%2C%20Tenkasi!5e0!3m2!1sen!2sin!4v1620000000000!5m2!1sen!2sin"
                        allowfullscreen="" loading="lazy"></iframe>

                </div>

                <div class="location-details">

                    <div class="location-info">

                        <h4>Chellappa Hotel</h4>

                        <p><i class="fas fa-map-marker-alt"></i> 12 Temple Road, Near Thirumalai Nayakkar Mahal,
                            Tenkasi, Tamil Nadu 627811</p>

                        <p><i class="fas fa-clock"></i> Open Daily: 7:00 AM - 11:00 PM</p>

                        <p><i class="fas fa-phone-alt"></i> +91 4634 223344</p>

                        <p><i class="fas fa-envelope"></i> info@chellappahotel.com</p>

                        <div class="action-buttons">

                            <a href="https://www.google.com/maps/dir//Chellappa+Hotel,+12+Temple+Road,+Near+Thirumalai+Nayakkar+Mahal,+Tenkasi,+Tamil+Nadu+627811/@8.9559801,77.3081893,17z/data=!4m8!4m7!1m0!1m5!1m1!1s0x3b0429c8b5c3a4a5:0x7e9b9ef9f0e7f4d5!2m2!1d77.310378!2d8.9559801"
                                target="_blank" class="direction-btn">

                                <i class="fas fa-directions"></i> Get Directions

                            </a>

                            <a href="tel:+914634223344" class="call-btn">

                                <i class="fas fa-phone"></i> Call Now

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <!-- Premium Footer -->

    <footer class="footer">

        <div class="footer-content">

            <div class="footer-column">

                <h3>Chellappa Hotel</h3>

                <p>Experience authentic Tamil cuisine in the heart of Tenkasi. Our restaurant blends traditional flavors
                    with warm hospitality for an unforgettable dining experience.</p>

                <div class="social-links">

                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>

                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>

                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>

                    <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>

                </div>

            </div>



            <div class="footer-column">

                <h3>Quick Links</h3>

                <ul class="footer-links">

                    <li><a href="#" data-section="home">Home</a></li>

                    <li><a href="#" data-section="offers">Offers</a></li>

                    <li><a href="#" data-section="location">Location</a></li>

                    <li><a href="#" data-section="about">About Us</a></li>

                    <li><a href="#" data-section="contact">Contact</a></li>

                </ul>

            </div>



            <div class="footer-column">

                <h3>Opening Hours</h3>

                <div class="contact-info">

                    <p><i class="fas fa-clock"></i> Monday - Sunday: 7:00 AM - 11:00 PM</p>

                    <p><i class="fas fa-utensils"></i> Breakfast: 7:00 AM - 11:00 AM</p>

                    <p><i class="fas fa-lunch"></i> Lunch: 12:00 PM - 3:30 PM</p>

                    <p><i class="fas fa-moon"></i> Dinner: 6:30 PM - 11:00 PM</p>

                </div>

            </div>



            <div class="footer-column">

                <h3>Contact Info</h3>

                <div class="contact-info">

                    <p><i class="fas fa-map-marker-alt"></i> 12 Temple Road, Near Thirumalai Nayakkar Mahal, Tenkasi,
                        Tamil Nadu 627811</p>

                    <p><i class="fas fa-phone-alt"></i> +91 4634 223344</p>

                    <p><i class="fas fa-envelope"></i> info@chellappahotel.com</p>

                </div>

            </div>

        </div>
        <div class="copyright">

            &copy; 2023 Chellappa Hotel. All Rights Reserved. | GSTIN: 33AAAAA0000A1Z5

        </div>

    </footer>


</body>
<script src="translations.js"></script>
<script src="script.js"></script>

</html>