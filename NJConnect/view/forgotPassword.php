<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/Forgotpassword.css">
    <title>Forgot Password</title>
   
</head>
<body>

    <div class="container">
        <h2>Forgot Password</h2>
        <form action="../actions/resetPassword.php" method="post">
            <label for="email">Enter your email address:</label>
            <input type="email" id="email" name="email" class="input-field" required>
            <button type="submit" class="btn">Reset Password</button>
        </form>
    </div>

</body>
</html>
