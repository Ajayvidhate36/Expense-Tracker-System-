<?php
require_once 'includes/functions.php';

// Receive and process the data from the frontend
$data = json_decode(file_get_contents("php://input"));

if (isset($data->amount, $data->category, $data->description, $data->date)) {
    addExpense($data->amount, $data->category, $data->description, $data->date);
    echo json_encode(['message' => 'Expense added successfully!']);
} else {
    echo json_encode(['message' => 'Invalid data']);
}
?>
