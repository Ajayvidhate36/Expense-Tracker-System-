<?php
// Include database connection
require_once 'db.php';

// Fetch all expenses from the database
function getExpenses($category = '', $startDate = '', $endDate = '') {
    global $pdo;
    
    $query = "SELECT * FROM expenses WHERE 1";
    
    if ($category) {
        $query .= " AND category = :category";
    }
    
    if ($startDate && $endDate) {
        $query .= " AND date BETWEEN :start_date AND :end_date";
    }

    $stmt = $pdo->prepare($query);

    if ($category) {
        $stmt->bindParam(':category', $category);
    }

    if ($startDate && $endDate) {
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Insert a new expense record into the database
function addExpense($amount, $category, $description, $date) {
    global $pdo;

    $stmt = $pdo->prepare("INSERT INTO expenses (amount, category, description, date) VALUES (:amount, :category, :description, :date)");
    $stmt->execute([
        ':amount' => $amount,
        ':category' => $category,
        ':description' => $description,
        ':date' => $date
    ]);
}

// Update an existing expense record
function updateExpense($id, $amount, $category, $description, $date) {
    global $pdo;

    $stmt = $pdo->prepare("UPDATE expenses SET amount = :amount, category = :category, description = :description, date = :date WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':amount' => $amount,
        ':category' => $category,
        ':description' => $description,
        ':date' => $date
    ]);
}

// Delete an expense record
function deleteExpense($id) {
    global $pdo;

    $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = :id");
    $stmt->execute([':id' => $id]);
}
?>
