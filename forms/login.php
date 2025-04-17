<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: adminpage.html');
    exit;
}

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

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Replace with your actual admin credentials
        $admin_username = 'admin';
        $admin_password = 'password123';

        if ($username === $admin_username && $password === $admin_password) {
            // Set session variable
            $_SESSION['admin_logged_in'] = true;

            // Redirect to admin dashboard
            header('Location: adminpage.html');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        // Get form data and sanitize
        $email = sanitize_input($_POST["email"]);
        $password = $_POST["password"];
        
        // Prepare and execute SQL statement to find user
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, start session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['room_number'] = $user['room_number'];
                
                // Redirect to homepage with success
                echo "<script>
                        alert('Login successful! Welcome back.');
                      </script>";
                header("Location: /Project/Homepage/New.php");
                exit();
            } else {
                // Invalid password 
                echo "<script>
                        alert('Invalid email or password. Please try again.');
                        window.location.href = 'loginform.html';
                      </script>";
            }
        } else {
            // User not found
            echo "<script>
                    alert('Invalid email or password. Please try again.');
                    window.location.href = 'loginform.html';
                  </script>";
        }
        
        $stmt->close();
    }
}

$conn->close();
?>