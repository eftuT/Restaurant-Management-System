// Main JavaScript for SEN'Q Restaurant

// Utility Functions
function formatPrice(amount) {
    return amount.toFixed(2) + ' Br';
}

function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

// Auth Functions
function checkAuth() {
    fetch('../backend/api/auth/check.php')
        .then(response => response.json())
        .then(data => {
            if (data.loggedIn) {
                document.querySelectorAll('.auth-buttons').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.user-info').forEach(el => el.style.display = 'block');
                document.querySelectorAll('.user-name').forEach(el => el.textContent = data.user.name);
            }
        })
        .catch(error => console.error('Auth check error:', error));
}

function logout() {
    fetch('../backend/api/auth/logout.php')
        .then(() => {
            window.location.href = 'index.html';
        })
        .catch(error => console.error('Logout error:', error));
}

// Cart Functions
function getCart() {
    return JSON.parse(localStorage.getItem('cart') || '[]');
}

function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
}

function addToCart(itemId, name, price, quantity = 1) {
    const cart = getCart();
    const existing = cart.find(item => item.id === itemId);
    if (existing) {
        existing.quantity += quantity;
    } else {
        cart.push({ id: itemId, name, price, quantity });
    }
    saveCart(cart);
    updateCartBadge();
}

function removeFromCart(itemId) {
    let cart = getCart();
    cart = cart.filter(item => item.id !== itemId);
    saveCart(cart);
    updateCartBadge();
}

function updateCartBadge() {
    const cart = getCart();
    const total = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.querySelectorAll('.cart-badge').forEach(el => {
        el.textContent = total;
        el.style.display = total > 0 ? 'inline' : 'none';
    });
}

// Load cart on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartBadge();
});