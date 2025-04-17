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
    btn.addEventListener('click', async function() {
        const menuItem = this.closest('.menu-item');
        const itemName = menuItem.querySelector('.menu-item-title').textContent;
        const itemPrice = parseInt(menuItem.querySelector('.menu-item-price').textContent);
        const stockQuantity = parseInt(menuItem.querySelector('.availability-badge').dataset.stock);
        
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

        // Add to cart with stock validation
        if (existingItem) {
            existingItem.quantity++;
            existingItem.total = existingItem.quantity * existingItem.price;
        } else {
            cart.push({
                name: itemName,
                price: itemPrice,
                quantity: 1,
                total: itemPrice,
                maxStock: stockQuantity
            });
        }

        cartTotal += itemPrice;
        
        // Update displayed stock quantity
        const newStock = stockQuantity - 1;
        menuItem.querySelector('.availability-badge').dataset.stock = newStock;
        updateStockDisplay(menuItem, newStock);
        
        updateCartUI();
        showAlert('success', `${itemName} added to cart!`);
    });
});

// Add this new function to update stock display
function updateStockDisplay(menuItem, newStock) {
    const badge = menuItem.querySelector('.availability-badge');
    
    if (newStock <= 0) {
        badge.innerHTML = '<span class="out-of-stock">Out of Stock</span>';
        menuItem.querySelector('.add-to-cart').disabled = true;
    } else if (newStock <= 5) {
        badge.innerHTML = `<span class="in-stock low">Only ${newStock} left</span>`;
    } else {
        badge.innerHTML = `<span class="in-stock">In Stock (${newStock})</span>`;
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
checkoutBtn.addEventListener('click', async function() {
    if (cart.length === 0) {
        showAlert('error', 'Your cart is empty!');
        return;
    }

    try {
        const response = await fetch('verify_stock.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({items: cart})
        });

        const data = await response.json();

        if (!data.success) {
            showAlert('error', data.message);
            // Update cart if items are no longer available
            if (data.unavailableItems) {
                updateCartWithAvailableStock(data.unavailableItems);
            }
            return;
        }

        // Process order if stock is available
        const orderResult = await sendOrderToServer({
            cart: cart,
            total: cartTotal
        });

        if (orderResult.success) {
            showAlert('success', 'Order placed successfully!');
            cart = [];
            cartTotal = 0;
            updateCartUI();
            billModal.style.display = 'none';
        }

    } catch (error) {
        showAlert('error', 'Failed to process order. Please try again.');
        console.error('Order error:', error);
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
            <div class="bill-item-name">${item.name} <span class="bill-item-quantity">× ${item.quantity}</span></div>
            <div class="bill-item-price">${item.total}</div>
        `;
        billItems.appendChild(billItem);
    }); 

    // Calculate total
    const total = cart.reduce((sum, item) => sum + item.total, 0);
    billTotal.textContent = `₹${total}`;
}
// Open Bill Modal
cartPreview.addEventListener('click', function() {
    billModal.style.display = 'flex';
    setTimeout(() => {
        billContainer.style.transform = 'translateY(0)';
    }, 10);
});

// Close Bill Modal
closeBill.addEventListener('click', function() {
    billContainer.style.transform = 'translateY(20px)';
    setTimeout(() => {
        billModal.style.display = 'none';
    }, 300);
});

// Close modal when clicking outside
billModal.addEventListener('click', function(e) {
    if (e.target === billModal) {
        billContainer.style.transform = 'translateY(20px)';
        setTimeout(() => {
            billModal.style.display = 'none';
        }, 300);
    }
});

// Clear Cart
clearCartBtn.addEventListener('click', function() {
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

closeLocation.addEventListener('click', function() {
    locationContainer.style.transform = 'translateY(20px)';
    setTimeout(() => {
        locationModal.style.display = 'none';
    }, 300);
});

locationModal.addEventListener('click', function(e) {
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
    switch(section) {
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
    item.addEventListener('click', function(e) {
        e.preventDefault();
        const section = this.getAttribute('data-section');
        handleNavigation(section);
    });
});
// Add event listeners to footer links
footerLinks.forEach(link => {
    link.addEventListener('click', function(e) {
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
    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Reset previous states
        submitBtn.classList.remove('loading');

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
    // Format the order data
    const formattedOrder = {
        customerName: "Guest",
        items: orderDetails.cart.map(item => ({
            name: item.name,
            quantity: parseInt(item.quantity)
        })),
        quantity: orderDetails.cart.reduce((sum, item) => sum + parseInt(item.quantity), 0),
        totalAmount: parseFloat(orderDetails.total)
    };

    // Debug log
    console.log('Sending order:', formattedOrder);

    return fetch('test.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formattedOrder)
    })
    .then(async response => {
        const responseText = await response.text();
        console.log('Raw response:', responseText); // Debug log

        try {
            const data = JSON.parse(responseText);
            if (!response.ok) {
                throw new Error(data.error || `HTTP error! status: ${response.status}`);
            }
            return data;
        } catch (e) {
            console.error('JSON Parse Error:', e);
            throw new Error('Server response was not valid JSON');
        }
    })
    .then(data => {
        if (data.success) {
            console.log('Order saved:', data.message);
            return { success: true, message: data.message };
        } else {
            throw new Error(data.error || 'Failed to save order');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        return { success: false, error: error.message };
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
document.addEventListener('DOMContentLoaded', function() {
    const menuImages = document.querySelectorAll('.menu-item-img');
    
    menuImages.forEach(img => {
        img.classList.add('loading');
        
        img.addEventListener('load', function() {
            this.classList.remove('loading');
        });
        
        img.addEventListener('error', function() {
            this.classList.remove('loading');
            this.src = 'images/default-food.jpg';
        });
    });
});
