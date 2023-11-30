<?php
include("db_config.php");

session_start();

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];

// Handle form submission for submitting a query
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["submit_query"])) {
        $course = $_POST["course"];
        $description = $_POST["description"];

        // Insert the query into the 'query' table
        $insertQuery = "INSERT INTO `query` (`course`, `description`) VALUES ('$course', '$description')";
        if ($conn->query($insertQuery) === TRUE) {
            echo "Query submitted successfully!";
            // You can redirect the user to the dashboard or another page after submitting the query.
        } else {
            echo "Error submitting query: " . $conn->error;
        }
    } elseif (isset($_POST["cancel"])) {
        // Redirect back to the dashboard
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Query - Course Court</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #ff7e5f, #feb47b);
        }

        header {
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 80%;
            margin: 0 auto;
        }

        .logo img {
            width: 80px;
            height: 50px;
        }

        .logo h1 {
            margin-left: 10px;
            color: #333;
        }

        .user-info p {
            margin: 0;
            color: #333;
            font-weight: bold;
        }

        .buttons {
            display: flex;
            align-items: center;
        }

        .buttons a {
            text-decoration: none;
            color: #333;
            padding: 10px;
            margin: 0 10px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .buttons a:hover {
            background: #333;
            color: #fff;
        }

        main {
            padding: 20px;
            width: 80%;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        h2 {
            color: #333;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin-top: 10px;
            font-weight: bold;
            color: #333;
        }

        input,
        textarea {
            padding: 10px;
            margin: 5px 0;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #333;
            border-radius: 5px;
        }

        button {
            background: #333;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            background: #555;
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        form {
            animation: fadeInUp 0.8s ease;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="images/logo.png" alt="Your Logo" style="width: 80px; height: 50px;">
                <h1>| Course Court</h1>
            </div>
            <div class="user-info">
                <p>Welcome, <?php echo $username; ?></p>
            </div>
            <div class="buttons">
                <a href="dashboard.php" class="dashboard-btn">Dashboard</a>
                <a href="edit_profile.php" class="edit-profile-btn">Edit Profile</a>
                <a href="#" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <main>
        <h2>Submit Query</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="course">Course:</label>
            <input type="text" name="course" required>

            <label for="description">Description:</label>
            <textarea name="description" rows="4" required></textarea>

            <button type="submit" name="submit_query">Submit Query</button>
            <button type="submit" name="cancel">Cancel</button>
        </form>
    </main>
</body>
</html>
