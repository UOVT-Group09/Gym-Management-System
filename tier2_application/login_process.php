//<?php

//$username = $_POST['username'];
//$password = $_POST['password'];

// simple test login (later you connect database)
//if ($username == "admin" && $password == "123") {
  //  echo "Login Successful!";
//} else {
 //   echo "Invalid Username or Password";
//}

//?>

<?php
session_start();
include "../config/db.php"; // adjust if needed

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $_SESSION['user'] = $username;
    header("Location: dashboard.php");
    exit();
} else {
    echo "<script>
        alert('Invalid login!');
        window.location='login.php';
    </script>";
}
?>
