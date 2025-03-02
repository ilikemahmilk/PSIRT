<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "AlfieHershey";
$dbname = "psirt";
$port = "3308";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection Failed: ' . $conn->connect_error]));
}

// Check if the orderID parameter is set
if (isset($_POST['orderID'])) {
    $orderID = $_POST['orderID'];

    // Check if the clientID is set in the session
    if (!isset($_SESSION['sitter_id'])) {
        echo json_encode(['error' => 'User not logged in.']);
        exit();
    }

    // Check if the order is in 'assigned' state
    $checkStateSql = "SELECT state FROM `order` WHERE orderID = '$orderID' AND state = 'assigned'";
    $checkStateResult = $conn->query($checkStateSql);

    if ($checkStateResult->num_rows == 1) {
        // Update the order state to 'pending'
        $updateStateSql = "UPDATE `order` SET state = 'pending completion', archived = 1 WHERE orderID = '$orderID'";

        if ($conn->query($updateStateSql) === TRUE) {
            echo json_encode(['success' => 'Order marked as pending successfully.']);
        } else {
            echo json_encode(['error' => 'Error marking order as pending: ' . $conn->error]);
        }
    } else {
        echo json_encode(['error' => 'Order is not in the required state for pending.']);
    }
} else {
    echo json_encode(['error' => 'Missing orderID parameter.']);
}

$conn->close();
?>