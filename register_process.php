<?php
include("db_config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"]; // Store the password as provided by the user
    $dob = $_POST["dob"];

    // Insert data into the register_details table
    $sql = "INSERT INTO register_details (FirstName, Lastname, Date_of_birth, Email_id, User_id, Password)
            VALUES ('$firstName', '$lastName', '$dob', '$email', '$username', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful!";
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
