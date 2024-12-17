<?php
// Initialize variables
$fullName = $email = $phone = $cartItems = $shippingCost = $totalPrice = '';

// Flag to track if form is valid
$isValid = true;
$errors = [];

// Handle POST request for payment form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user details and sanitize
    $fullName = isset($_POST['full-name']) ? trim($_POST['full-name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    
    // Validate user details
    if (empty($fullName)) {
        $isValid = false;
        $errors[] = 'Full name is required.';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $isValid = false;
        $errors[] = 'A valid email is required.';
    }
    
    if (empty($phone) || !preg_match('/^[0-9]{10}$/', $phone)) {
        $isValid = false;
        $errors[] = 'A valid phone number is required.';
    }

    // Get hidden fields (order details)
    $cartItems = isset($_POST['cart-items']) ? $_POST['cart-items'] : '0';
    $shippingCost = isset($_POST['shipping-cost']) ? $_POST['shipping-cost'] : '0';
    $totalPrice = isset($_POST['total-price']) ? $_POST['total-price'] : '0';

    if (!is_numeric($shippingCost) || $shippingCost < 0) {
        $isValid = false;
        $errors[] = 'Invalid shipping cost.';
    }

    if (!is_numeric($totalPrice) || $totalPrice <= 0) {
        $isValid = false;
        $errors[] = 'Invalid total price.';
    }
    
    // If validation fails, show errors
    if (!$isValid) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
        exit;  // Stop the script execution if validation fails
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceed to Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://js.paystack.co/v1/inline.js"></script> <!-- Paystack Script -->
    <link rel="stylesheet" href="../assets/css/pay.css">
</head>
<body>

    <div class="container">
        <h1>Proceed to Payment</h1>

        <!-- Display user details -->
        <div class="user-details">
            <h2>User Details</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($fullName); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
        </div>

        <!-- Display order summary -->
        <div class="order-summary">
            <h3>Order Summary</h3>
            <table>
                <tr>
                    <td>Cart Items</td>
                    <td><strong><?php echo htmlspecialchars($cartItems); ?></strong></td>
                </tr>
                <tr>
                    <td>Shipping Cost</td>
                    <td><strong>₵<?php echo htmlspecialchars($shippingCost); ?></strong></td>
                </tr>
                <tr>
                    <td>Total Price</td>
                    <td><strong>₵<?php echo htmlspecialchars($totalPrice); ?></strong></td>
                </tr>
            </table>
        </div>

        <form id="payment-form">
            <button type="button" class="complete-payment" onclick="payWithPaystack()">Complete Payment</button>
        </form>

    <script>
    function payWithPaystack() {
        // Get the email from the PHP variable and pass it into the JavaScript
        var email = "<?php echo htmlspecialchars($email); ?>"; // Dynamically populate the email
        var totalPrice = <?php echo isset($_POST['total-price']) ? $_POST['total-price'] : 0; ?>; // Dynamically populate the total price

        if (!email) {
            alert("Email is not available.");
            return;
        }

        var handler = PaystackPop.setup({
            key: 'pk_live_e5de9956bd1b9cf1a9f9af47884db05888e7116c', // Replace with your Paystack public key
            email: email, // Use the dynamically captured email from PHP
            amount: totalPrice * 100,  // Amount in kobo (5000 = 50.00 NGN)
            currency: 'GHS',
            ref: 'order-ref-' + new Date().getTime(), 
           callback: function(response) {
                // Show alert on successful payment
                alert('Payment successful. Transaction reference: ' + response.reference);
                
                // Get the cart data to identify the books purchased
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                cart.forEach(function(item) {
                    // Send an AJAX request to the backend to store the purchase
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "process_purchase.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            // Purchase recorded successfully
                            console.log("Purchase recorded: " + xhr.responseText);
                        }
                    };
                    var data = "user_id=" + encodeURIComponent('<?php echo $_SESSION['user_id']; ?>') + // Assuming user_id is stored in session
                               "&book_id=" + encodeURIComponent(item.id) + 
                               "&purchase_date=" + encodeURIComponent(new Date().toISOString()) + 
                               "&transaction_reference=" + encodeURIComponent(response.reference);
                    xhr.send(data);
                });

                localStorage.removeItem('cart');
                // Redirect the user to the user dashboard after successful payment
                window.location.href = "admin/user_dashboard.php"; 
            },
            onClose: function() {
                alert('Payment process was closed');
            }
        });
        handler.openIframe();
    }
</script>
</body>
</html>
