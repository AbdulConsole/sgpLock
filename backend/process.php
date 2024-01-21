<?php
// Connect to your MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sgplock";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to validate and sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Handle CSV file uploads
for ($i = 1; $i <= 4; $i++) {
    $fileKey = "file" . $i;

    if ($_FILES[$fileKey]["error"] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES[$fileKey]["tmp_name"];
        $name = basename($_FILES[$fileKey]["name"]);

        move_uploaded_file($tmp_name, "../uploads/" . $name);

        // Parse the CSV file
        $csvData = array_map('str_getcsv', file("../uploads/" . $name));

        // Process and insert data into the database
        foreach ($csvData as $row) {
            $sn = sanitizeInput($row[0]);
            $name = sanitizeInput($row[1]);
            $testScore = sanitizeInput($row[2]);
            $examScore = sanitizeInput($row[3]);
            $total = sanitizeInput($row[4]);

            // Insert data into the 'students' table
            $sql = "INSERT INTO students (sn, name, test_score, exam_score, total) VALUES ('$sn', '$name', '$testScore', '$examScore', '$total')";

            if ($conn->query($sql) !== TRUE) {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}

// Close the database connection
$conn->close();

// Redirect to a success page or provide download links for PDFs
header("Location: success.php");
?>
