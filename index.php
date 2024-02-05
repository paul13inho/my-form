<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = "";  // Initialize an empty message

// Your database connection code
$conn = new mysqli("localhost", "root", "", "paulo");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get the next available ID from the database
function getNextId($conn) {
    $result = $conn->query("SELECT MAX(id) + 1 AS next_id FROM names");
    if (!$result) {
        die("Error fetching next ID: " . $conn->error);
    }

    $row = $result->fetch_assoc();
    return isset($row['next_id']) ? $row['next_id'] : 1;
}

// Get the next available ID
$nextId = getNextId($conn);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize user input
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';

    // Reconnect to the database (this should be done here, as the connection was closed above)
    $conn = new mysqli("localhost", "root", "", "paulo");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO names (id, name) VALUES (?, ?)");
    $stmt->bind_param("is", $nextId, $name);

    if ($stmt->execute()) {
        $message = "Record added successfully!";
    } else {
        $message = "Error adding record: " . $stmt->error;
    }

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();

    // Set a JavaScript variable to indicate the message should disappear after 3 seconds
    echo '<script>var messageTimeout = setTimeout(function() { document.getElementById("message").style.display = "none"; }, 3000);</script>';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Simple Form</title>
</head>
<body>

<?php include('header.php'); ?>

<form action="" method="post" class="mx-auto max-w-md p-4 bg-gray-100 rounded">
    <label for="id" class="block mb-2">ID:</label>
    <input type="text" id="id" name="id" value="<?= $nextId ?>" disabled class="w-full p-2 mb-4 border">

    <label for="name" class="block mb-2">Name:</label>
    <input type="text" id="name" name="name" class="w-full p-2 mb-4 border">

    <?php if ($message): ?>
        <p id="message" class="text-center text-green-500 mb-4"><?= $message ?></p>
    <?php endif; ?>

    <button type="submit" class="w-full p-2 bg-green-500 text-white rounded hover:bg-green-600">Add to Database</button>
</form>

</body>
</html>
