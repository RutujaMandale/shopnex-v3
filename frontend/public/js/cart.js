// Cart management using localStorage
function getCart() {
    return JSON.parse(localStorage.getItem('shopnex_cart') || '[]');
}

function saveCart(cart) {
    localStorage.setItem('shopnex_cart', JSON.stringify(cart));
}

function addToCart(id, name, price, image, qty) {
    qty = qty || 1;
    var cart = getCart();
    var found = false;
    for (var i = 0; i < cart.length; i++) {
        if (cart[i].id == id) {
            cart[i].qty += qty;
            found = true;
            break;
        }
    }
    if (!found) {
        cart.push({ id: id, name: name, price: price, image: image, qty: qty });
    }
    saveCart(cart);
    updateCartBadge();
}

function removeFromCart(id) {
    var cart = getCart().filter(function(item) { return item.id != id; });
    saveCart(cart);
    updateCartBadge();
}

function clearCart() {
    localStorage.removeItem('shopnex_cart');
    updateCartBadge();
}

function updateCartBadge() {
    var cart = getCart();
    var count = 0;
    cart.forEach(function(item) { count += item.qty; });
    var badges = document.querySelectorAll('#cartCount, .cart-badge');
    badges.forEach(function(b) { b.textContent = count; });
}
