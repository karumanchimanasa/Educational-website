<?php
include("db_config.php");

session_start();

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];

// Fetch course details based on CourseID from the URL
if (isset($_GET["course_id"])) {
    $courseID = $_GET["course_id"];

    // Fetch course details from the course_list table
    $courseQuery = "SELECT * FROM course_list WHERE CourseID = $courseID";
    $courseResult = $conn->query($courseQuery);

    if ($courseResult->num_rows == 1) {
        $courseDetails = $courseResult->fetch_assoc();
    } else {
        // Redirect back to the dashboard or another page if the course is not found
        header("Location: dashboard.php");
        exit();
    }

    // Fetch quiz questions for the selected course
    $quizQuery = "SELECT * FROM quiz_questions WHERE CourseID = $courseID";
    $quizResult = $conn->query($quizQuery);

    // Handle quiz submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_quiz"])) {
        $score = 0;

        foreach ($_POST["answers"] as $questionID => $userAnswer) {
            // Fetch correct answer from the database
            $fetchCorrectAnswerQuery = "SELECT CorrectAnswer FROM quiz_questions WHERE QuestionID = $questionID";
            $correctAnswerResult = $conn->query($fetchCorrectAnswerQuery);

            if ($correctAnswerResult->num_rows == 1) {
                $correctAnswer = $correctAnswerResult->fetch_assoc()["CorrectAnswer"];

                // Check if the user's answer is correct
                if ($userAnswer === $correctAnswer) {
                    $score++;
                }
            }
        }

        // Insert the quiz score into the quiz table
        $insertQuizScoreQuery = "INSERT INTO quiz (student_name, score, CourseID) VALUES ('$username', $score, $courseID)";
        $conn->query($insertQuizScoreQuery);

        echo "Quiz submitted successfully! Your score: $score";
    }
} else {
    // Redirect back to the dashboard or another page if CourseID is not provided
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll - <?php echo $courseDetails["CourseName"]; ?></title>
    <style>
        body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    header {
        background-color: #4CAF50;
        color: white;
        padding: 15px 0;
        text-align: center;
    }

    .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
    }

    .logo img {
        width: 80px;
        height: 50px;
        margin-right: 10px;
    }

    .logo h1 {
        margin: 0;
    }

    .user-info {
        font-size: 18px;
    }

    .buttons a {
        color: white;
        text-decoration: none;
        margin-left: 20px;
        padding: 10px;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .buttons a:hover {
        background-color: #333;
    }

    main {
        max-width: 800px;
        margin: 20px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        color: #333;
    }

    .tabs {
        margin-top: 20px;
        display: flex;
        justify-content: space-around;
    }

    .tabs button {
        padding: 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .tabs button:hover {
        background-color: #333;
    }

    .details {
        margin-top: 20px;
        display: none;
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
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
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <main>
        <h2><?php echo $courseDetails["CourseName"]; ?></h2>

        <!-- Tabs for Course Details -->
        <div class="tabs">
            <button onclick="showDetails('description')">Description</button>
            <button onclick="showDetails('contents')">Contents</button>
            <button onclick="showDetails('textbook')">Textbook</button>
            <button onclick="showDetails('quiz')">Quiz</button>
        </div>

        <!-- Course Details -->
        <div class="details" id="description-details">
            <h3>Description:</h3>
            <p><?php echo $courseDetails["Description"]; ?></p>
        </div>

        <div class="details" id="contents-details" style="display: none;">
            <h3>Contents:</h3>
            <p><?php echo $courseDetails["Contents"]; ?></p>
           
        </div>

        <div class="details" id="textbook-details" style="display: none;">
            <h3>Textbook:</h3>
            <p><?php echo $courseDetails["Textbook"]; ?></p>
            
        </div>

        <!-- Quiz Section -->
<div class="details" id="quiz-details" style="display: none;">
    <h3>Quiz:</h3>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?course_id=" . $courseID; ?>" method="post">
        <?php
        if ($quizResult->num_rows > 0) {
            while ($question = $quizResult->fetch_assoc()) {
                echo "<p>{$question['Question']}</p>";
                echo "<label>Your Answer: <input type='text' name='answers[{$question['QuestionID']}]'></label><br><br>";
                echo "<input type='hidden' name='correct_answers[{$question['QuestionID']}]' value='{$question['CorrectAnswer']}'>";
            }
        } else {
            echo "No quiz questions available for this course.";
        }
        ?>
        <button type="submit" name="submit_quiz">Submit Quiz</button>
        <button onclick="speakQuizPrompt()">Speak Quiz Prompt</button>
    </form>
</div>
    </main>

    <script>
        function speak(text) {
        const synth = window.speechSynthesis;
        const utterance = new SpeechSynthesisUtterance(text);
        synth.speak(utterance);
    }

    function speakQuizPrompt() {
        const questions = document.querySelectorAll('.details#quiz-details p');
        let text = "Here are the quiz questions. ";
        questions.forEach((question, index) => {
            text += `Question number ${index + 1}: ${question.innerText}. `;
        });
        speak(text);
    }
   

            

        function showDetails(tabName) {
            // Hide all details
            var details = document.getElementsByClassName("details");
            for (var i = 0; i < details.length; i++) {
                details[i].style.display = "none";
            }

            // Show the selected details
            document.getElementById(tabName + "-details").style.display = "block";
        }
    </script>
</body>
</html>
