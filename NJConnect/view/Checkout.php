<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <!-- Google Fonts for Stylish Handwriting -->
    <link href="https://fonts.googleapis.com/css2?family=Sacramento&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/Checkout.css">
</head>
<body>

    <div class="container">
        <h1>Checkout</h1>

        <form action="pay.php" method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="full-name">Full Name</label>
                <input type="text" id="full-name" name="full-name" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
            </div>

             <!-- Hidden fields for order details -->
            <input type="hidden" id="total-price" name="total-price" value="">
            <input type="hidden" id="shipping-cost" name="shipping-cost" value="">
            <input type="hidden" id="cart-items" name="cart-items" value="">

            <div class="form-group">
                <label for="region">Select Region</label>
                <select id="region" name="region" required>
                    <option value="">Select your region</option>
                    <option value="Greater Accra">Greater Accra</option>
                    <option value="Ashanti">Ashanti</option>
                    <option value="Western">Western</option>
                    <option value="Eastern">Eastern</option>
                    <option value="Central">Central</option>
                    <option value="Volta">Volta</option>
                    <option value="Northern">Northern</option>
                    <option value="Upper East">Upper East</option>
                    <option value="Upper West">Upper West</option>
                    <option value="Western North">Western North</option>
                    <option value="Ahafo">Ahafo</option>
                    <option value="Oti">Oti</option>
                    <option value="Bono">Bono</option>
                    <option value="Bono East">Bono East</option>
                </select>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" placeholder="Enter your address" required>
            </div>

            <!-- Cart Summary Section -->
            <div id="cart-summary-placeholder" class="cart-summary">
                <!-- Cart summary data will be injected here dynamically -->
            </div>

            <input type="submit" value="Proceed to Payment">
        </form>
        
        <a href="Book.php" class="#" style="color: #fff; background-color: #6c63ff; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px; display: inline-block;">Continue shopping</a>

    </div>

    <script>
         function validateForm() {
        let isValid = true;

        // Validate full name: must contain only letters and spaces
        const fullName = document.getElementById('full-name').value;
        const fullNameError = document.getElementById('full-name-error');
        const nameRegex = /^[A-Za-z\s]+$/;
        if (!fullName.match(nameRegex)) {
            fullNameError.style.display = 'block';
            isValid = false;
        } else {
            fullNameError.style.display = 'none';
        }

        // Validate email: must be a valid email format
        const email = document.getElementById('email').value;
        const emailError = document.getElementById('email-error');
        const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!email.match(emailRegex)) {
            emailError.style.display = 'block';
            isValid = false;
        } else {
            emailError.style.display = 'none';
        }

        // Validate phone number: must be a valid phone number format
        const phone = document.getElementById('phone').value;
        const phoneError = document.getElementById('phone-error');
        const phoneRegex = /^[0-9]{10}$/; // Example: 10 digits
        if (!phone.match(phoneRegex)) {
            phoneError.style.display = 'block';
            isValid = false;
        } else {
            phoneError.style.display = 'none';
        }

        // Validate region: must be selected
        const region = document.getElementById('region').value;
        const regionError = document.getElementById('region-error');
        if (!region) {
            regionError.style.display = 'block';
            isValid = false;
        } else {
            regionError.style.display = 'none';
        }

        // Validate address: must not be empty
        const address = document.getElementById('address').value;
        const addressError = document.getElementById('address-error');
        if (!address) {
            addressError.style.display = 'block';
            isValid = false;
        } else {
            addressError.style.display = 'none';
        }

        return isValid;
    }
    function calculateShippingCost(region, quantity) {
        let shippingCost = 0;
        
        // Define base shipping costs for regions
        const shippingRates = {
            "Western": 0.01, "Western North": 20, "Greater Accra": 25, "Ashanti": 30, 
            "Sunyani": 30, "Central": 20, "Eastern": 50, "Volta": 35, "Northern": 60, 
            "Upper East": 65, "Upper West": 65, "Ahafo": 30, "Bono": 30, "Bono East": 30, 
            "Oti": 40, "Savannah": 55, "North East": 60
        };
        
        // Get base shipping cost for the selected region
        shippingCost = shippingRates[region] || 0;
        
        // Adjust shipping cost based on quantity
        if (quantity >= 6 && quantity <= 15) {
            shippingCost *= 2;  // Double the cost for quantities 6-15
        } else if (quantity > 20) {
            shippingCost *= 3;  // Triple the cost for quantities greater than 20
        }

        return shippingCost;
    }

    function displayOrderSummary() {
        const cart = JSON.parse(localStorage.getItem('orderSummary')) || [];
        const cartSummaryElement = document.getElementById('cart-summary-placeholder');
        const region = document.getElementById('region').value;
        
        if (cart.length === 0) {
            cartSummaryElement.innerHTML = '<p>Your cart is empty.</p>';
            return;
        }

        let total = 0;
        let quantity = 0;
        cartSummaryElement.innerHTML = '';

        cart.forEach(item => {
            // Validate item data to avoid errors
            if (!item.title || !item.price || !item.quantity) {
                console.error('Invalid item in order summary:', item); // Log invalid items for debugging
                return;
            }

            const price = parseFloat(item.price) || 0; // Ensure price is a valid number
            const itemQuantity = parseInt(item.quantity, 10) || 1; // Ensure quantity is a valid integer
            quantity += itemQuantity;
            const subtotal = price * itemQuantity;

            total += subtotal;

            const orderItem = document.createElement('div');
            orderItem.className = 'order-item';
            orderItem.innerHTML = `
                <div class="order-item-name">${item.title} (x${itemQuantity})</div>
                <div class="order-item-price">₵${subtotal.toFixed(2)}</div>
            `;
            cartSummaryElement.appendChild(orderItem);
        });

        const shippingCost = calculateShippingCost(region, quantity);
        total += shippingCost;

        // Add shipping cost to the summary
        const shippingCostElement = document.createElement('div');
        shippingCostElement.className = 'order-item';
        shippingCostElement.innerHTML = `
            <div class="order-item-name">Shipping</div>
            <div class="order-item-price">₵${shippingCost.toFixed(2)}</div>
        `;
        cartSummaryElement.appendChild(shippingCostElement);

        // Display total price
        const totalPriceElement = document.createElement('div');
        totalPriceElement.className = 'total-price';
        totalPriceElement.innerHTML = `Total: ₵${total.toFixed(2)}`;
        cartSummaryElement.appendChild(totalPriceElement);

        // Update hidden fields
        document.getElementById('total-price').value = total.toFixed(2);
        document.getElementById('shipping-cost').value = shippingCost.toFixed(2);
        document.getElementById('cart-items').value = JSON.stringify(cart);
    }

    // Display order summary when the page loads
    window.onload = displayOrderSummary;

    // Recalculate shipping when the region or quantity changes
    document.getElementById('region').addEventListener('change', displayOrderSummary);
</script>


</body>
</html>
