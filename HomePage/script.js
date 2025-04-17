let currentLanguage = 'en';

function changeLanguage(lang) {
    currentLanguage = lang;
    document.documentElement.lang = lang;
    updateContent();
}

function updateContent() {
    // Update navigation items
    document.querySelectorAll('.nav-item').forEach(item => {
        const key = item.dataset.section;
        if (translations[currentLanguage][key]) {
            const icon = item.innerHTML.split('</i>')[0] + '</i> ';
            item.innerHTML = icon + translations[currentLanguage][key];
        }
    });

    // Update search placeholder
    document.getElementById('searchInput').placeholder = translations[currentLanguage].searchPlaceholder;

    // Update section title
    document.querySelector('.section-title').textContent = translations[currentLanguage].signatureDishes;

    // Update availability badges
    document.querySelectorAll('.availability-badge span').forEach(badge => {
        if (badge.classList.contains('in-stock')) {
            if (badge.classList.contains('low')) {
                const count = badge.textContent.match(/\d+/)[0];
                badge.textContent = translations[currentLanguage].onlyLeft.replace('{count}', count);
            } else {
                badge.textContent = translations[currentLanguage].inStock;
            }
        } else {
            badge.textContent = translations[currentLanguage].outOfStock;
        }
    });

    // Add more content updates as needed
}

// Add CSS for language selector
const style = document.createElement('style');
style.textContent = `
    .language-selector {
        margin-left: auto;
        padding: 0 15px;
    }

    .language-selector select {
        padding: 5px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: white;
        font-size: 14px;
        cursor: pointer;
    }

    .language-selector select:focus {
        outline: none;
        border-color: #666;
    }
`;
document.head.appendChild(style);

// Initialize translation on page load
document.addEventListener('DOMContentLoaded', () => {
    changeLanguage('en');
});

let cart = [];
let cartTotal = 0;

const cartPreview = document.getElementById('cartPreview');
const cartCount = document.getElementById('cartCount');
const billModal = document.getElementById('billModal');
const billContainer = document.getElementById('billContainer');
const closeBill = document.getElementById('closeBill');
const billItems = document.getElementById('billItems');
const billTotal = document.getElementById('billTotal');
const clearCartBtn = document.getElementById('clearCart');
const checkoutBtn = document.getElementById('checkoutBtn');
const addToCartBtns = document.querySelectorAll('.add-to-cart');

// Location Modal Elements
const locationModal = document.getElementById('locationModal');
const locationContainer = document.getElementById('locationContainer');
const closeLocation = document.getElementById('closeLocation');

// Offers Section Elements
const monthlyOffers = document.getElementById('monthlyOffers');

let offersVisible = false;

// About and Contact Sections
const aboutSection = document.getElementById('about');
const contactSection = document.getElementById('contact');
const homeSection = document.querySelector('.menu-grid');

// Navigation Links
const navItems = document.querySelectorAll('.nav-item');
const footerLinks = document.querySelectorAll('.footer-links a');

// Contact Form Elements
const contactForm = document.getElementById('contactForm');
const submitBtn = document.getElementById('submitBtn');
const successMessage = document.getElementById('successMessage');

// Modify addToCartBtns event listener
addToCartBtns.forEach((btn, index) => {
    btn.addEventListener('click', async function () {
        const menuItem = this.closest('.menu-item');
        const itemName = menuItem.querySelector('.menu-item-title').textContent;
        const itemPrice = parseInt(menuItem.querySelector('.menu-item-price').textContent);
        const badge = menuItem.querySelector('.availability-badge');
        const stockQuantity = parseInt(badge.dataset.stock);

        // Check if item is out of stock
        if (stockQuantity <= 0) {
            showAlert('error', `${itemName} is out of stock`);
            return;
        }

        // Check existing cart quantity
        const existingItem = cart.find(item => item.name === itemName);
        const currentQuantity = existingItem ? existingItem.quantity : 0;

        // Prevent exceeding stock quantity
        if (currentQuantity >= stockQuantity) {
            showAlert('warning', `Cannot add more ${itemName}. Only ${stockQuantity} available.`);
            return;
        }

        // Add to cart
        if (existingItem) {
            existingItem.quantity++;
            existingItem.total = existingItem.quantity * existingItem.price;
        } else {
            cart.push({
                name: itemName,
                price: itemPrice,
                quantity: 1,
                total: itemPrice
            });
        }

        cartTotal += itemPrice;

        // Update stock display
        const newStock = stockQuantity - 1;
        badge.dataset.stock = newStock;
        updateStockDisplay(menuItem, newStock);

        updateCartUI();
        showAlert('success', `${itemName} added to cart!`);
    });
});

// Update the stock display function
function updateStockDisplay(menuItem, newStock) {
    const badge = menuItem.querySelector('.availability-badge');
    const stockDisplay = badge.querySelector('span');

    if (newStock <= 0) {
        stockDisplay.className = 'out-of-stock';
        stockDisplay.textContent = 'Out of Stock';
        menuItem.querySelector('.add-to-cart').disabled = true;
    } else if (newStock <= 5) {
        stockDisplay.className = 'in-stock low';
        stockDisplay.textContent = `Only ${newStock} left`;
    } else {
        stockDisplay.className = 'in-stock';
        stockDisplay.textContent = `In Stock (${newStock})`;
    }
}

// Add this function to show alerts
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' :
            type === 'warning' ? 'exclamation-triangle' :
                'times-circle'}"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(alertDiv);

    // Remove alert after 3 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Update checkout function to verify stock before order
checkoutBtn.addEventListener('click', async function () {
    try {
        console.log('Starting stock verification...');
        const response = await fetch('verify_stock.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ items: cart })
        });
        console.log('Fetch completed:', response);
        const responseText = await response.text();
        console.log('Raw response:', responseText);
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            console.error('JSON parse error:', responseText);
            showAlert('error', 'Server error. Please try again later.');
            return;
        }
        if (!data.success) {
            showAlert('error', data.message);
            if (data.unavailableItems) {
                updateCartWithAvailableStock(data.unavailableItems);
            }
            return;
        }
        // Proceed with order placement
        const orderResult = await sendOrderToServer({
            cart,
            total: cartTotal
        });
        if (orderResult.success) {
            showAlert('success', 'Order placed successfully!');
            cart = [];
            cartTotal = 0;
            updateCartUI();
            billModal.style.display = 'none';
        } else {
            showAlert('error', orderResult.error || 'Order placement failed.');
        }
    } catch (error) {
        console.error('Error during checkout:', error);
        showAlert('error', 'An error occurred. Please try again.');
    }
});

// Add this function to update cart when stock changes
function updateCartWithAvailableStock(unavailableItems) {
    let cartUpdated = false;
    unavailableItems.forEach(item => {
        const cartItem = cart.find(ci => ci.name === item.name);
        if (cartItem) {
            if (!item.available || item.stock === 0) {
                // Remove item from cart
                cart = cart.filter(ci => ci.name !== item.name);
                cartUpdated = true;
            } else if (cartItem.quantity > item.stock) {
                // Update quantity to available stock
                cartItem.quantity = item.stock;
                cartItem.total = cartItem.quantity * cartItem.price;
                cartUpdated = true;
            }
        }
    });
    if (cartUpdated) {
        cartTotal = cart.reduce((sum, item) => sum + item.total, 0);
        updateCartUI();
        showAlert('warning', 'Cart updated due to stock changes');
    }
}

// Function to update availability badges
function updateAvailabilityBadges() {
    const badges = document.querySelectorAll('.availability-badge');
    badges.forEach(badge => {
        const stock = parseInt(badge.dataset.stock);
        const available = badge.dataset.available === "true";
        if (!available || stock <= 0) {
            badge.innerHTML = '<span class="out-of-stock">Out of Stock</span>';
            badge.closest('.menu-item').dataset.available = "false";
        } else {
            if (stock <= 5) { // Low stock threshold
                badge.dataset.stock = "low";
            }
            badge.innerHTML = `<span class="in-stock">In Stock (${stock})</span>`;
        }
    });
}

// Call this function when page loads
document.addEventListener('DOMContentLoaded', updateAvailabilityBadges);

// Update Cart UI
function updateCartUI() {
    // Update cart count
    const itemCount = cart.reduce((total, item) => total + item.quantity, 0);
    cartCount.textContent = itemCount;
    // Update bill modal
    renderBill();
}

// Render Bill
function renderBill() {
    billItems.innerHTML = '';
    if (cart.length === 0) {
        billItems.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-basket"></i>
                <p>Your cart is empty</p>
            </div>
        `;
        billTotal.textContent = '₹0';
        return;
    }
    cart.forEach(item => {
        const billItem = document.createElement('div');
        billItem.className = 'bill-item';
        billItem.innerHTML = `
            <div class="bill-item-name">${item.name}</div>
            <div class="bill-item-details">
                <span class="bill-item-quantity">Qty: ${item.quantity}</span>
                <span class="bill-item-price">Price: ₹${item.price}</span>
                <span class="bill-item-total">Total: ₹${item.total}</span>
            </div>
        `;
        billItems.appendChild(billItem);
    });
    // Calculate total
    const total = cart.reduce((sum, item) => sum + item.total, 0);
    billTotal.textContent = `₹${total}`;
}

// Open Bill Modal
cartPreview.addEventListener('click', function () {
    billModal.style.display = 'flex';
    setTimeout(() => {
        billContainer.style.transform = 'translateY(0)';
    }, 10);
});

// Close Bill Modal
closeBill.addEventListener('click', function () {
    billContainer.style.transform = 'translateY(20px)';
    setTimeout(() => {
        billModal.style.display = 'none';
    }, 300);
});

// Close modal when clicking outside
billModal.addEventListener('click', function (e) {
    if (e.target === billModal) {
        billContainer.style.transform = 'translateY(20px)';
        setTimeout(() => {
            billModal.style.display = 'none';
        }, 300);
    }
});

// Clear Cart
clearCartBtn.addEventListener('click', function () {
    if (cart.length === 0) return;
    // Animation
    this.innerHTML = '<i class="fas fa-check"></i> Cleared!';
    this.style.background = 'linear-gradient(135deg, var(--success), #2ecc71)';
    // Clear cart
    cart = [];
    cartTotal = 0;
    updateCartUI();
    setTimeout(() => {
        this.innerHTML = '<i class="fas fa-trash-alt"></i> Clear Cart';
        this.style.background = '#f5f5f5';
    }, 1500);
});

// Location Modal Functionality
function openLocationModal() {
    locationModal.style.display = 'flex';
    setTimeout(() => {
        locationContainer.style.transform = 'translateY(0)';
    }, 10);
}
closeLocation.addEventListener('click', function () {
    locationContainer.style.transform = 'translateY(20px)';
    setTimeout(() => {
        locationModal.style.display = 'none';
    }, 300);
});
locationModal.addEventListener('click', function (e) {
    if (e.target === locationModal) {
        locationContainer.style.transform = 'translateY(20px)';
        setTimeout(() => {
            locationModal.style.display = 'none';
        }, 300);
    }
});

// Offers Functionality
function toggleOffers() {
    offersVisible = !offersVisible;
    monthlyOffers.style.display = offersVisible ? 'block' : 'none';
    // Scroll to offers if showing
    if (offersVisible) {
        monthlyOffers.scrollIntoView({ behavior: 'smooth' });
    }
}

// Navigation Functionality
function handleNavigation(section) {
    // Hide all sections first
    homeSection.style.display = 'none';
    monthlyOffers.style.display = 'none';
    aboutSection.style.display = 'none';
    contactSection.style.display = 'none';
    // Update active nav item
    navItems.forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('data-section') === section) {
            item.classList.add('active');
        }
    });
    // Show selected section
    switch (section) {
        case 'home':
            homeSection.style.display = 'grid';
            break;
        case 'offers':
            homeSection.style.display = 'grid';
            monthlyOffers.style.display = 'block';
            offersVisible = true;
            monthlyOffers.scrollIntoView({ behavior: 'smooth' });
            break;
        case 'location':
            homeSection.style.display = 'grid';
            openLocationModal();
            break;
        case 'about':
            aboutSection.style.display = 'block';
            aboutSection.scrollIntoView({ behavior: 'smooth' });
            break;
        case 'contact':
            contactSection.style.display = 'block';
            contactSection.scrollIntoView({ behavior: 'smooth' });
            break;
    }
}

// Add event listeners to navigation items
navItems.forEach(item => {
    item.addEventListener('click', function (e) {
        e.preventDefault();
        const section = this.getAttribute('data-section');
        handleNavigation(section);
    });
});

// Add event listeners to footer links
footerLinks.forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();
        const section = this.getAttribute('data-section');
        handleNavigation(section);
    });
});

// Contact Form Submission
if (contactForm) {
    // Get form elements
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const messageInput = document.getElementById('message');
    let isValid = true;
    // Real-time validation function
    const validateField = (field, validator) => {
        if (!field) return; // Guard clause if field doesn't exist
        field.addEventListener('input', () => {
            const group = field.closest('.form-group');
            if (validator(field.value.trim())) {
                group.classList.remove('error');
                group.classList.add('success');
            } else {
                group.classList.remove('success');
            }
        });
    };
    // Add validation listeners
    validateField(nameInput, value => value.length > 0);
    validateField(emailInput, value => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value));
    validateField(messageInput, value => value.length > 0);
    // Add focus/blur events for floating label effect
    const formGroups = document.querySelectorAll('.form-group');
    formGroups.forEach(group => {
        const input = group.querySelector('input, textarea');
        if (input) {
            input.addEventListener('focus', () => {
                group.classList.add('focused');
            });
            input.addEventListener('blur', () => {
                if (!input.value) {
                    group.classList.remove('focused');
                }
            });
            // Check if there's existing value (for page reload)
            if (input.value) {
                group.classList.add('focused');
            }
        }
    });
    // Form validation
    contactForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        // Reset previous states
        submitBtn.classList.remove('loading');
        isValid = true; // Reset isValid flag at the start
        // Clear previous error states
        document.querySelectorAll('.form-group').forEach(group => {
            group.classList.remove('error');
        });
        // Name validation
        if (!nameInput.value.trim()) {
            nameInput.closest('.form-group').classList.add('error');
            isValid = false;
        }
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value.trim())) {
            emailInput.closest('.form-group').classList.add('error');
            isValid = false;
        }
        // Message validation
        if (!messageInput.value.trim()) {
            messageInput.closest('.form-group').classList.add('error');
            isValid = false;
        }
        if (!isValid) {
            // Add shake animation to error fields
            const errorFields = document.querySelectorAll('.form-group.error');
            errorFields.forEach(field => {
                field.style.animation = 'none';
                setTimeout(() => {
                    field.style.animation = 'shake 0.5s cubic-bezier(.36,.07,.19,.97) both';
                }, 10);
            });
            return;
        }
        // Form is valid - proceed with submission
        submitBtn.classList.add('loading');
        try {
            await new Promise(resolve => setTimeout(resolve, 1500));
            // Show success message
            successMessage.classList.add('show');
            contactForm.reset();
            // Remove focus from fields
            formGroups.forEach(group => {
                group.classList.remove('focused');
            });
            // Hide success message after 5 seconds
            setTimeout(() => {
                successMessage.classList.remove('show');
            }, 5000);
        } catch (error) {
            alert('There was an error submitting your form. Please try again.');
            console.error('Form submission error:', error);
        } finally {
            submitBtn.classList.remove('loading');
        }
    });
}

// Animate elements on scroll
const animateOnScroll = () => {
    const elements = document.querySelectorAll('.menu-item, .monthly-offers, .section-title, .about-section, .contact-section');
    elements.forEach(element => {
        const elementPosition = element.getBoundingClientRect().top;
        const screenPosition = window.innerHeight / 1.2;
        if (elementPosition < screenPosition) {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }
    });
};

// Set initial state for animated elements
document.querySelectorAll('.menu-item, .monthly-offers, .about-section, .contact-section').forEach(element => {
    element.style.opacity = '0';
    element.style.transform = 'translateY(30px)';
    element.style.transition = 'all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
});

// Add scroll event listener
window.addEventListener('scroll', animateOnScroll);
// Trigger once on load
window.addEventListener('load', animateOnScroll);

// Send Order to Server
function sendOrderToServer(orderDetails) {
    const formattedOrder = {
        customerName: "Guest",
        items: orderDetails.cart.map(item => ({
            name: item.name,
            quantity: parseInt(item.quantity)
        })),
        quantity: orderDetails.cart.reduce((sum, item) => sum + parseInt(item.quantity), 0),
        totalAmount: parseFloat(orderDetails.total)
    };
    return fetch('test.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify(formattedOrder)
    })
        .then(async response => {
            // First check if the response is ok
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("Server didn't return JSON");
            }
            const responseText = await response.text();
            try {
                return JSON.parse(responseText);
            } catch (e) {
                console.error('Server response:', responseText);
                throw new Error('Invalid JSON response from server');
            }
        })
        .then(data => {
            if (!data || typeof data !== 'object') {
                throw new Error('Invalid response format');
            }
            if (data.success) {
                return { success: true, message: data.message };
            } else {
                throw new Error(data.error || 'Order processing failed');
            }
        })
        .catch(error => {
            console.error('Order processing error:', error);
            return {
                success: false,
                error: error.message || 'Failed to process order'
            };
        });
}

// Search functionality
const searchInput = document.getElementById('searchInput');
const menuItems = document.querySelectorAll('.menu-item');
let noResultsDiv;

// Create no results message div
function createNoResultsDiv() {
    noResultsDiv = document.createElement('div');
    noResultsDiv.className = 'no-results';
    noResultsDiv.innerHTML = '<i class="fas fa-search"></i><p>No menu items found</p>';
    document.querySelector('.menu-grid').after(noResultsDiv);
}
createNoResultsDiv();

// Search function
function searchMenuItems() {
    const searchTerm = searchInput.value.toLowerCase();
    let hasResults = false;
    menuItems.forEach(item => {
        const itemName = item.querySelector('.menu-item-title').textContent.toLowerCase();
        const itemDesc = item.querySelector('.menu-item-desc').textContent.toLowerCase();
        if (itemName.includes(searchTerm) || itemDesc.includes(searchTerm)) {
            item.style.display = 'block';
            item.style.animation = 'fadeIn 0.5s ease-out';
            hasResults = true;
        } else {
            item.style.display = 'none';
        }
    });
    // Show/hide no results message
    noResultsDiv.style.display = hasResults ? 'none' : 'block';
}

// Add event listener for search input
searchInput.addEventListener('input', debounce(searchMenuItems, 300));

// Debounce function to limit search frequency
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Clear search when clicking navigation items
navItems.forEach(item => {
    item.addEventListener('click', () => {
        searchInput.value = '';
        searchMenuItems();
    });
});

// Add category filter functionality
const categoryBtns = document.querySelectorAll('.category-btn');
categoryBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        // Remove active class from all buttons
        categoryBtns.forEach(b => b.classList.remove('active'));
        // Add active class to clicked button
        btn.classList.add('active');
        const category = btn.dataset.category;
        // Show/hide items based on category
        menuItems.forEach(item => {
            if (category === 'all' || item.dataset.category === category) {
                item.style.display = 'block';
                item.style.animation = 'fadeIn 0.5s ease-out';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Add lazy loading for images
document.addEventListener('DOMContentLoaded', function () {
    const menuImages = document.querySelectorAll('.menu-item-img');
    menuImages.forEach(img => {
        img.classList.add('loading');
        img.addEventListener('load', function () {
            this.classList.remove('loading');
        });
    });
});

// Add these functions to your existing script.js

document.addEventListener('DOMContentLoaded', function () {
    // Get elements
    const orderTypeInputs = document.getElementsByName('orderType');
    const locationInput = document.getElementById('locationNumber');
    const locationInputContainer = document.getElementById('locationInputContainer');
    const checkoutBtn = document.getElementById('checkoutBtn');

    // Handle order type change
    orderTypeInputs.forEach(input => {
        input.addEventListener('change', function () {
            const placeholder = getPlaceholderText(this.value);
            locationInput.placeholder = placeholder;

            if (this.value === 'takeaway') {
                // Generate waiting ID for takeaway orders
                const waitingId = generateWaitingId();
                locationInput.value = waitingId;
                locationInput.disabled = true;
            } else {
                locationInput.value = '';
                locationInput.disabled = false;
            }
        });
    });

    // Handle checkout button click
    checkoutBtn.addEventListener('click', function () {
        const selectedType = document.querySelector('input[name="orderType"]:checked').value;
        const locationValue = locationInput.value.trim();
        const errorMessage = locationInputContainer.querySelector('.error-message');

        if (!locationValue && selectedType !== 'takeaway') {
            errorMessage.style.display = 'block';
            return;
        }

        errorMessage.style.display = 'none';

        // Proceed with order placement
        placeOrder(selectedType, locationValue);
    });
});

function getPlaceholderText(orderType) {
    switch (orderType) {
        case 'table':
            return 'Enter table number';
        case 'room':
            return 'Enter room number';
        case 'takeaway':
            return 'Waiting ID will be generated';
        default:
            return '';
    }
}

function generateWaitingId() {
    // Generate a random waiting ID: W-XXXX (X = random digit)
    const random = Math.floor(1000 + Math.random() * 9000);
    return `W-${random}`;
}

// Add this function at the top level of your script
function showWaitingId(waitingId) {
    const banner = document.getElementById('waitingIdBanner');
    const display = document.getElementById('waitingIdDisplay');

    display.textContent = waitingId;
    banner.style.display = 'block';

    // Add show class after a small delay to trigger animation
    setTimeout(() => {
        banner.classList.add('show');
    }, 100);
}

// Modify your existing placeOrder function to show the waiting ID
function placeOrder(type, locationValue) {
    try {
        // Validate inputs
        if (!type || !locationValue) {
            throw new Error('Invalid order details');
        }

        const items = getCartItems();
        
        // Check for empty cart
        if (!items || items.length === 0) {
            throw new Error('Your cart is empty');
        }

        // Create order object
        const order = {
            type: type,
            location: locationValue,
            items: items,
            total: calculateTotal(),
            timestamp: new Date().toISOString()
        };

        console.log('Sending order:', order);

        // Send order to server
        return fetch('api/place_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(order)
        })
        .then(async response => {
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to place order');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showOrderConfirmation(order);
                clearCart();
                return true;
            } else {
                throw new Error(data.message || 'Order processing failed');
            }
        });
    } catch (error) {
        console.error('Order error:', error);
        showError(error.message);
        return false;
    }
}

// Add a function to remove the waiting ID banner
function clearWaitingId() {
    const banner = document.getElementById('waitingIdBanner');
    banner.classList.remove('show');
    setTimeout(() => {
        banner.style.display = 'none';
        document.body.style.paddingTop = '0';
    }, 300);
}

// Improve error display
function showError(message) {
    console.error('Error:', message);
    alert(`Error: ${message}`); // Replace with better UI notification
}

function showOrderConfirmation(order) {
    // Show success message with order details
    const message = order.type === 'takeaway'
        ? `Order placed successfully!\nYour waiting ID is: ${order.location}`
        : `Order placed successfully!\nYour ${order.type} number: ${order.location}`;

    alert(message); // Replace with a better UI notification
    closeBillModal(); // Implement this function to close the modal
}

function generateWaitingId() {
    // Generate a 4-digit number between 1000 and 9999
    return Math.floor(1000 + Math.random() * 9000);
}

function validateLocationInput(type, value) {
    const number = parseInt(value);
    
    switch(type) {
        case 'table':
            return number >= 1 && number <= 50;
        case 'room':
            return number >= 100 && number <= 999;
        case 'takeaway':
            return number >= 1000 && number <= 9999;
        default:
            return false;
    }
}

// Update the location input handler
document.querySelector('#locationNumber').addEventListener('input', function(e) {
    const type = document.querySelector('input[name="orderType"]:checked').value;
    const value = e.target.value;
    
    // Allow only numbers
    if (!/^\d*$/.test(value)) {
        e.target.value = value.replace(/[^\d]/g, '');
        return;
    }
    
    // Validate range based on type
    if (value && !validateLocationInput(type, value)) {
        const errorMsg = document.querySelector('.error-message');
        switch(type) {
            case 'table':
                errorMsg.textContent = 'Table number must be between 1 and 50';
                break;
            case 'room':
                errorMsg.textContent = 'Room number must be between 100 and 999';
                break;
            case 'takeaway':
                errorMsg.textContent = 'Waiting number must be between 1000 and 9999';
                break;
        }
        errorMsg.style.display = 'block';
    } else {
        document.querySelector('.error-message').style.display = 'none';
    }
});

// Add these functions to handle cart operations
function validateCartItems(items) {
    if (!Array.isArray(items) || items.length === 0) {
        return false;
    }

    return items.every(item => {
        // Log item for debugging
        console.log('Validating item:', item);

        return item &&
            // Allow string or number for id
            (typeof item.id === 'number' || typeof item.id === 'string') &&
            // Allow string or number for quantity
            (typeof item.quantity === 'number' || typeof item.quantity === 'string') &&
            // Allow string or number for price
            (typeof item.price === 'number' || typeof item.price === 'string') &&
            // Check if quantity and price are valid numbers after conversion
            !isNaN(parseFloat(item.quantity)) &&
            parseFloat(item.quantity) > 0 &&
            !isNaN(parseFloat(item.price)) &&
            parseFloat(item.price) >= 0;
    });
}

// Update getCartItems function
function getCartItems() {
    try {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        console.log('Getting cart items:', cart); // Debug log
        
        if (!Array.isArray(cart) || cart.length === 0) {
            console.log('Cart is empty'); // Debug log
            return [];
        }

        const formattedItems = cart.filter(item => {
            return item && item.id && item.name && 
                   typeof item.quantity === 'number' && 
                   typeof item.price === 'number';
        });

        console.log('Formatted items:', formattedItems); // Debug log
        return formattedItems;
    } catch (error) {
        console.error('Error getting cart items:', error);
        return [];
    }
}

// Update addToCart function
function addToCart(menuItem) {
    try {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        // Get item details
        const itemToAdd = {
            id: parseInt(menuItem.dataset.itemId), // Make sure your menu items have data-item-id attribute
            name: menuItem.querySelector('.menu-item-title').textContent,
            price: parseFloat(menuItem.querySelector('.menu-item-price').textContent.replace(/[^0-9.]/g, '')),
            quantity: 1
        };
        
        console.log('Adding item:', itemToAdd); // Debug log
        
        const existingItem = cart.find(item => item.id === itemToAdd.id);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push(itemToAdd);
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount(cart.reduce((total, item) => total + item.quantity, 0));
        
        console.log('Updated cart:', cart); // Debug log
        showAlert('success', `${itemToAdd.name} added to cart!`);
        return true;
    } catch (error) {
        console.error('Error adding to cart:', error);
        showError('Failed to add item to cart');
        return false;
    }
}

function calculateTotal() {
    const items = getCartItems();
    return items.reduce((total, item) => total + item.subtotal, 0);
}

function clearCart() {
    localStorage.removeItem('cart');
    updateCartCount(0);
    // Update cart preview if it exists
    const cartPreview = document.getElementById('cartPreview');
    if (cartPreview) {
        cartPreview.querySelector('.cart-count').textContent = '0';
    }
}

function updateCartCount(count) {
    const cartCount = document.getElementById('cartCount');
    if (cartCount) {
        cartCount.textContent = count;
    }
}

// Debug Cart Functionality
function debugCart() {
    console.group('Cart Debug Info');
    try {
        const rawCart = localStorage.getItem('cart');
        console.log('Raw cart data:', rawCart);
        
        const parsedCart = JSON.parse(rawCart || '[]');
        console.log('Parsed cart:', parsedCart);
        
        const formattedItems = parsedCart.map(item => ({
            id: parseInt(item.id || 0),
            name: item.name || '',
            quantity: parseInt(item.quantity || 0),
            price: parseFloat(item.price || 0),
            subtotal: parseFloat(item.price || 0) * parseInt(item.quantity || 0)
        }));
        console.log('Formatted items:', formattedItems);
        
        const isValid = validateCartItems(formattedItems);
        console.log('Cart validation:', isValid);
        
    } catch (error) {
        console.error('Cart debug error:', error);
    }
    console.groupEnd();
}

// Call this in the browser console to debug cart issues
// debugCart();