<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password (empty)
$dbname = "hotel_management"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize user inputs
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $fullname = sanitize_input($_POST["fullname"]);
    $email = sanitize_input($_POST["email"]);
    $phone = sanitize_input($_POST["phone"]);
    $room = sanitize_input($_POST["room"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    
    // Validate form data
    $error = false;
    $error_message = "";
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = true;
        $error_message = "Passwords do not match!";
    }
    
    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $error = true;
        $error_message = "Email already exists. Please use a different email or login.";
    }
    
    // If no errors, proceed with registration
    if (!$error) {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare and execute SQL statement
        $sql = "INSERT INTO users (fullname, email, phone, room_number, password, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $fullname, $email, $phone, $room, $hashed_password);
        
        if ($stmt->execute()) {
            $success_message = "Registration successful! You can now login with your email and password.";
            // Redirect to login page with success message
            echo "<script>
                    alert('$success_message');
                  </script>";
            header("Location: loginform.html");
            exit();
        } else {
            // Registration failed
            echo "<script>
                    alert('Registration failed. Please try again later.');
                    window.location.href = 'signform.html';
                  </script>";
        }
        
        $stmt->close();
    } else {
        // Display error message and redirect back to signup form
        echo "<script>
                alert('$error_message');
                window.location.href = 'signform.html';
              </script>";
    }
}

$conn->close();
?>