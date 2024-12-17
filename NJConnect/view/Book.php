
<?php
session_start(); // Ensure session is started
include('../db/config.php');
if (isset($_SESSION['role'])) {
    $user_role = $_SESSION['role']; // This could be 'admin' or 'regular'
} else {
    $user_role = 'guest'; // Default, in case the user is not logged in
}
// Fetch books from the database
try {
    $books_query = "SELECT book_id, title, price, image_path, description FROM books ORDER BY created_at DESC";
    $books_stmt = $conn->prepare($books_query);
    $books_stmt->execute();
    $books_result = $books_stmt->get_result();
} catch (Exception $e) {
    // Log error or handle it appropriately
    error_log("Database error: " . $e->getMessage());
    $books_result = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ministry Books | Glory Life New Jerusalem Generation</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/Book.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="logo-name">
            <img src="../assets/images/GL logo.png" alt="Ministry Logo" class="logo">
            <span class="ministry-name">Glory Life New Jerusalem Generation</span>
        </div>
        <nav class="top-nav">
            <ul>
                <li><a href="About.php">About Us</a></li>
                <li><a href="Events.php">Events</a></li>
                <li><a href="Sermons.php">Sermons</a></li>
                <li><a href="Departments.php">Ministries/Departments</a></li>
                <li><a href="#">Books</a></li>
                <li><a href="Devotional.php">Devotional</a></li>
                <li><a href="Prayer.php">Prayer Wall</a></li>
                <?php if ($user_role == 'admin'): ?>
        <li><a href="admin/admin_dashboard.php">My Account</a></li>  <!-- Admin Dashboard -->
    <?php elseif ($user_role == 'regular'): ?>
        <li><a href="admin/user_dashboard.php">My Account</a></li>    <!-- Regular User Dashboard -->
    <?php endif; ?>
                <li><a href="Cart.php">My Cart</a></li>
            </ul>
        </nav>
    </header>

    <div class="books-container">
        <!-- Dynamically Fetched Books from Database -->
        <?php 
        if ($books_result && $books_result->num_rows > 0) {
            while ($book = $books_result->fetch_assoc()) { 
        ?>
            <div class="book" data-book-id="<?php echo htmlspecialchars($book['book_id']); ?>">
                <div class="book-image-container">
                    <img src="<?php echo htmlspecialchars($book['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($book['title']); ?>">
                </div>
                <div class="book-details">
                    <h2><?php echo htmlspecialchars($book['title']); ?></h2>
                    <div class="price">₵<?php echo number_format($book['price'], 2); ?></div>
                    <button onclick="addToCart(
                        '<?php echo htmlspecialchars($book['title']); ?>', 
                        <?php echo $book['price']; ?>, 
                        <?php echo $book['book_id']; ?>
                    )">Add to Cart</button>
                </div>
            </div>
        <?php 
            } 
        }
        ?>

        <!-- Hardcoded Books (for backup or additional books) -->
<div class="book" data-book-id="101">
    <div class="book-image-container">
        <img src="../assets/images/A Walk With God.png" alt="A Walk With God">
    </div>
    <div class="book-details">
        <h2>A Walk With God</h2>
        <div class="price">₵20.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="102">
    <div class="book-image-container">
        <img src="../assets/images/Angels,Church and Harvest.jpg" alt="Angels, The Church and The Harvest">
    </div>
    <div class="book-details">
        <h2>Angels, The Church and The Harvest</h2>
        <div class="price">₵20.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="103">
    <div class="book-image-container">
        <img src="../assets/images/Children for Signs.png" alt="Children for Signs and Wonders">
    </div>
    <div class="book-details">
        <h2>Children for Signs and Wonders</h2>
        <div class="price">₵15.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="104">
    <div class="book-image-container">
        <img src="../assets/images/Battle of hearts.png" alt="Battle of Hearts">
    </div>
    <div class="book-details">
        <h2>Battle of Hearts</h2>
        <div class="price">₵25.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="105">
    <div class="book-image-container">
        <img src="../assets/images/The Gift of Righteousness.png" alt="The Gift of Righteousness">
    </div>
    <div class="book-details">
        <h2>The Gift of Righteousness</h2>
        <div class="price">₵25.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="106">
    <div class="book-image-container">
        <img src="../assets/images/Christ In You.jpg" alt="Christ In You">
    </div>
    <div class="book-details">
        <h2>Christ In You</h2>
        <div class="price">₵20.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="107">
    <div class="book-image-container">
        <img src="../assets/images/He Sent His Word.png" alt="He Sent His Word">
    </div>
    <div class="book-details">
        <h2>He Sent His Word</h2>
        <div class="price">₵20.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="108">
    <div class="book-image-container">
        <img src="../assets/images/Realism.jpg" alt="Realism">
    </div>
    <div class="book-details">
        <h2>Realism</h2>
        <div class="price">₵30.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="109">
    <div class="book-image-container">
        <img src="../assets/images/The Eternal Purpose.jpg" alt="The Eternal Purpose">
    </div>
    <div class="book-details">
        <h2>The Eternal Purpose</h2>
        <div class="price">₵30.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="110">
    <div class="book-image-container">
        <img src="../assets/images/The Two Cities.jpg" alt="The Two Cities">
    </div>
    <div class="book-details">
        <h2>The Two Cities</h2>
        <div class="price">₵40.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="111">
    <div class="book-image-container">
        <img src="../assets/images/The Fourth Wind.jpg" alt="The Fourth Wind">
    </div>
    <div class="book-details">
        <h2>The Fourth Wind</h2>
        <div class="price">₵25.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="112">
    <div class="book-image-container">
        <img src="../assets/images/Heavenly Walk.jpg" alt="A Heavenly Walk">
    </div>
    <div class="book-details">
        <h2>A Heavenly Walk</h2>
        <div class="price">₵20.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="113">
    <div class="book-image-container">
        <img src="../assets/images/BOTA1.jpg" alt="Battle of The Ages Vol. I">
    </div>
    <div class="book-details">
        <h2>Battle of The Ages Vol. I</h2>
        <div class="price">₵30.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="114">
    <div class="book-image-container">
        <img src="../assets/images/BOTA2.jpg" alt="Battle of The Ages Vol. II">
    </div>
    <div class="book-details">
        <h2>Battle of The Ages Vol. II</h2>
        <div class="price">₵30.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="115">
    <div class="book-image-container">
        <img src="../assets/images/The Night.jpg" alt="The Night">
    </div>
    <div class="book-details">
        <h2>The Night</h2>
        <div class="price">₵20.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="116">
    <div class="book-image-container">
        <img src="../assets/images/The Hope Of The Day.jpg" alt="The Hope Of The Day">
    </div>
    <div class="book-details">
        <h2>The Hope Of The Day</h2>
        <div class="price">₵20.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="117">
    <div class="book-image-container">
        <img src="../assets/images/MOTL.jpg" alt="On The Mountain Of The Lord">
    </div>
    <div class="book-details">
        <h2>On The Mountain Of The Lord</h2>
        <div class="price">₵25.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="118">
    <div class="book-image-container">
        <img src="../assets/images/Jesus.jpg" alt="Jesus, Revealing The Eternal One">
    </div>
    <div class="book-details">
        <h2>Jesus, Revealing The Eternal One</h2>
        <div class="price">₵40.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="119">
    <div class="book-image-container">
        <img src="../assets/images/SOTL.jpg" alt="Songs Of The Lamb">
    </div>
    <div class="book-details">
        <h2>Songs Of The Lamb</h2>
        <div class="price">₵20.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="120">
    <div class="book-image-container">
        <img src="../assets/images/Total Death to Self.jpg" alt="Total Death To Self">
    </div>
    <div class="book-details">
        <h2>Total Death To Self</h2>
        <div class="price">₵20.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="121">
    <div class="book-image-container">
        <img src="../assets/images/TOTA Journal.jpg" alt="Trumpets of The Ages Journal">
    </div>
    <div class="book-details">
        <h2>Trumpets of The Ages Journal</h2>
        <div class="price">₵30.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="122">
    <div class="book-image-container">
        <img src="../assets/images/TOTA Study Bible.jpg" alt="Trumpets Of The Ages Study Bible; The Book of Acts">
    </div>
    <div class="book-details">
        <h2>Trumpets Of The Ages Study Bible; The Book of Acts</h2>
        <div class="price">₵45.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="123">
    <div class="book-image-container">
        <img src="../assets/images/Daniel.jpg" alt="The Book of Daniel">
    </div>
    <div class="book-details">
        <h2>The Book of Daniel</h2>
        <div class="price">₵40.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="124">
    <div class="book-image-container">
        <img src="../assets/images/Millenium.jpg" alt="In The Millenium">
    </div>
    <div class="book-details">
        <h2>In The Millenium</h2>
        <div class="price">₵35.00</div>
        <button>Add to Cart</button>
    </div>
</div>
<div class="book" data-book-id="125">
    <div class="book-image-container">
        <img src="../assets/images/Melchizedek.jpg" alt="Melchizedek">
    </div>
    <div class="book-details">
        <h2>Melchizedek</h2>
        <div class="price">₵30.00</div>
        <button>Add to Cart</button>
    </div>
</div>

    <script>
    // Store cart data
    const cart = JSON.parse(localStorage.getItem('cart')) || [];

    // Add to cart function
    function addToCart(bookTitle, bookPrice, bookId) {
        const cartItem = { 
            title: bookTitle, 
            price: bookPrice, 
            id: bookId 
        };
        cart.push(cartItem);
        localStorage.setItem('cart', JSON.stringify(cart));
        alert(`${bookTitle} has been added to your cart! Kindly check your cart to proceed.`);
    }

    // Attach event listeners to all "Add to Cart" buttons
    document.querySelectorAll('.book').forEach((book) => {
        const button = book.querySelector('button');
        const title = book.querySelector('h2').innerText;
        const price = book.querySelector('.price').innerText.replace('₵', '');
        const bookId = book.dataset.bookId || null;

        button.addEventListener('click', () => addToCart(title, parseFloat(price), bookId));
    });
    </script>
</body>
</html>
