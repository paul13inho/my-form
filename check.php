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

// Close the database connection
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Check Data</title>
</head>
<body>

<?php include('header.php'); ?>

<div class="mx-auto max-w-md p-4 bg-gray-100 rounded">
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
