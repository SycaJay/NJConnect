<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Cart</title>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/Cart.css">
</head>
<body>

<header>
    Book Cart
</header>

<div class="cart-container">
    <div class="cart-header">Your Shopping Cart</div>

    <div id="cart-items">
        <!-- Cart items will be displayed here -->
    </div>

    <!-- Total and Checkout -->
    <div class="cart-actions">
        <div class="total-price" id="total-price">Total: ₵0.00</div>
        <button class="checkout-btn" id="checkout-btn" onclick="proceedToCheckout()" disabled>Proceed to Checkout</button>
        <button class="return-btn" onclick="window.location.href='Book.php';">Return to Books Page</button>
        <button class="clear-cart-btn" onclick="clearCart()">Clear Cart</button>
    </div>
</div>

<script>
    function displayCart() {
        const cartItems = document.getElementById('cart-items');
        const totalPriceElement = document.getElementById('total-price');
        const checkoutButton = document.getElementById('checkout-btn');
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        if (cart.length === 0) {
            cartItems.innerHTML = '<div class="empty-cart">Your cart is empty. Add some books to your cart to begin!</div>';
            totalPriceElement.innerHTML = 'Total: ₵0.00';
            checkoutButton.disabled = true; // Disable checkout button
            return;
        }

        let total = 0;
        cartItems.innerHTML = '';

        // Group items by book ID and count quantities
        const groupedItems = cart.reduce((acc, item) => {
            if (!item.id || !item.price || !item.title) {
                console.error('Invalid cart item:', item); // Log invalid items for debugging
                return acc;
            }

            if (!acc[item.id]) {
                acc[item.id] = {
                    ...item,
                    quantity: 1
                };
            } else {
                acc[item.id].quantity += 1;
            }
            return acc;
        }, {});

        Object.values(groupedItems).forEach(item => {
            const price = parseFloat(item.price) || 0; // Ensure price is a valid number
            const quantity = item.quantity || 1; // Default quantity to 1 if undefined
            const subtotal = price * quantity;
            total += subtotal;

            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.innerHTML = ` 
                <div class="cart-item-details">
                    <div class="cart-item-name">${item.title}</div>
                    <div class="cart-item-price">₵${price.toFixed(2)} each</div>
                </div>
                <div class="cart-item-quantity">
                    <button class="quantity-btn" onclick="updateQuantity('${item.id}', false)">-</button>
                    <span class="quantity-display">${quantity}</span>
                    <button class="quantity-btn" onclick="updateQuantity('${item.id}', true)">+</button>
                </div>
            `;
            cartItems.appendChild(cartItem);
        });

        totalPriceElement.innerHTML = `Total: ₵${total.toFixed(2)}`;
        checkoutButton.disabled = false; // Enable checkout button if there are items
    }

    function updateQuantity(bookId, increase) {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        if (increase) {
            // Add one more instance of the book
            const bookToAdd = cart.find(item => item.id === bookId);
            if (bookToAdd) {
                cart.push({...bookToAdd});
            }
        } else {
            // Remove one instance of the book
            const index = cart.findLastIndex(item => item.id === bookId);
            if (index !== -1) {
                cart.splice(index, 1);
            }
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        displayCart();
    }

    function clearCart() {
        // Clear cart from localStorage
        localStorage.removeItem('cart');
        displayCart();
    }

    // Display cart when page loads
    window.onload = displayCart;
    
    function proceedToCheckout() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];

        // Prepare cart summary with grouped items
        const cartSummary = Object.values(cart.reduce((acc, item) => {
            if (!acc[item.id]) {
                acc[item.id] = {
                    ...item,
                    quantity: 1
                };
            } else {
                acc[item.id].quantity += 1;
            }
            return acc;
        }, {}));

        // Save the cart summary data to localStorage for the checkout page
        localStorage.setItem('orderSummary', JSON.stringify(cartSummary));

        // Redirect to the checkout page
        window.location.href = 'Checkout.php';
    }
</script>

</body>
</html>
