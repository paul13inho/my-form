<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Your database connection code
$conn = new mysqli("localhost", "root", "", "paulo");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch all IDs and names from the database
function getAllData($conn) {
    $result = $conn->query("SELECT id, name FROM names");
    if (!$result) {
        die("Error fetching data: " . $conn->error);
    }

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return $data;
}

// Get all IDs and names
$data = getAllData($conn);

// Initialize $stmt
$stmt = null;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize user input
    $deleteId = isset($_POST['deleteId']) ? intval($_POST['deleteId']) : 0;

    // Reconnect to the database (this should be done here, as the connection was closed above)
    $conn = new mysqli("localhost", "root", "", "paulo");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the ID exists before attempting to delete
    $checkExistence = $conn->prepare("SELECT id FROM names WHERE id = ?");
    $checkExistence->bind_param("i", $deleteId);
    $checkExistence->execute();
    $checkExistence->store_result();

    if ($checkExistence->num_rows > 0) {
        // Delete data from the database
        $stmt = $conn->prepare("DELETE FROM names WHERE id = ?");
        $stmt->bind_param("i", $deleteId);

        if ($stmt->execute()) {
            $deleteMessage = "Record with ID $deleteId deleted successfully!";

            // Fetch updated data after successful deletion
            $data = getAllData($conn);
        } else {
            $deleteMessage = "Error deleting record: " . $stmt->error;
        }
    } else {
        $deleteMessage = "Record with ID $deleteId does not exist.";
    }

    // Close the prepared statements and database connection
    $checkExistence->close();
}

// Close the $stmt if it's not null
if ($stmt !== null) {
    $stmt->close();
}

// Close the database connection
$conn->close();

echo '<script>var messageTimeout = setTimeout(function() { document.getElementById("deleteMessage").style.display = "none"; }, 3000);</script>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Delete Data</title>
</head>
<body class="bg-gray-200">

<?php include('header.php'); ?>

<div class="mx-auto max-w-md p-4 bg-white rounded">

    <h2 class="text-2xl font-bold mb-4">Delete Data</h2>

    <form action="" method="post" class="mb-4">
        <label for="deleteId" class="block mb-2">ID to Delete:</label>
        <input type="number" id="deleteId" name="deleteId" class="w-full p-2 mb-4 border" required>

        <button type="submit" class="w-full p-2 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
    </form>

    <?php if (isset($deleteMessage)): ?>
        <p id="deleteMessage" class="text-center text-red-500 mb-4"><?= $deleteMessage ?></p>
    <?php endif; ?>

    <h2 class="text-2xl font-bold mb-4">Check Data</h2>

    <?php if ($data): ?>
        <table class="w-full border">
            <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border">ID</th>
                <th class="p-2 border">Name</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td class="p-2 border"><?= $row['id'] ?></td>
                    <td class="p-2 border"><?= $row['name'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No data available.</p>
    <?php endif; ?>

</div>

</body>
</html>
