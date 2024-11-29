<?php
require_once 'includes/functions.php';

// Get ID from the URL
$id = $_GET['id'] ?? '';

if ($id) {
    deleteExpense($id);
    echo json_encode(['message' => 'Expense deleted successfully']);
} else {
    echo json_encode(['message' => 'Invalid expense ID']);
}
?>
