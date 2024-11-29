<?php
session_start(); // Start session to check if the user is logged in

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection
require_once 'includes/db.php';

// Initialize variables for the date range
$start_date = '';
$end_date = '';
$averagePerCategory = [];  // Initialize $averagePerCategory to avoid undefined variable warnings

// Handle generating an expense report for a specific date range
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate_report'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    // Query to get expenses within the selected date range
    $stmt = $pdo->prepare("SELECT * FROM expenses WHERE user_id = :user_id AND date BETWEEN :start_date AND :end_date ORDER BY date DESC");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->execute();
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total expenses and average spending per category
    $totalExpenses = 0;
    $categoryTotals = [];
    foreach ($expenses as $expense) {
        $totalExpenses += $expense['amount'];
        $category = $expense['category'];
        if (!isset($categoryTotals[$category])) {
            $categoryTotals[$category] = ['total' => 0, 'count' => 0];
        }
        $categoryTotals[$category]['total'] += $expense['amount'];
        $categoryTotals[$category]['count']++;
    }
    
    // Calculate average spending per category
    $averagePerCategory = [];
    foreach ($categoryTotals as $category => $data) {
        $averagePerCategory[$category] = $data['total'] / $data['count'];
    }
} else {
    // Fetch all expenses if no date range is selected
    $stmt = $pdo->prepare("SELECT * FROM expenses WHERE user_id = :user_id ORDER BY date DESC");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total expenses
    $totalExpenses = 0;
    foreach ($expenses as $expense) {
        $totalExpenses += $expense['amount'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Expense Tracker</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
        <a href="logout.php">Logout</a>
    </header>

    <main>
        <div id="expenseOverview">
            <h2>Expense Overview</h2>
            <p>Total Expenses: $<?= number_format($totalExpenses, 2) ?></p>
        </div>

        <!-- Add Expense Form -->
        <div id="addExpense">
            <h2>Add New Expense</h2>
            <form method="POST" action="index.php">
                <label for="category">Category:</label>
                <input type="text" id="category" name="category" required>
                
                <label for="amount">Amount ($):</label>
                <input type="number" id="amount" name="amount" step="0.01" required>
                
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
                
                <label for="description">Description:</label>
                <textarea id="description" name="description"></textarea>
                
                <button type="submit" name="add_expense">Add Expense</button>
            </form>
        </div>

        <!-- Generate Expense Report Form -->
        <div id="expenseReport">
            <h2>Generate Expense Report</h2>
            <form method="POST" action="index.php">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>
                
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required>
                
                <button type="submit" name="generate_report">Generate Report</button>
            </form>
        </div>

        <?php if (isset($expenses)): ?>
            <div id="expenseList">
                <h2>Your Expenses</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td><?= htmlspecialchars($expense['date']) ?></td>
                                <td><?= htmlspecialchars($expense['category']) ?></td>
                                <td>$<?= number_format($expense['amount'], 2) ?></td>
                                <td><?= htmlspecialchars($expense['description']) ?></td>
                                <td>
                                    <a href="index.php?delete_id=<?= $expense['id'] ?>" onclick="return confirm('Are you sure you want to delete this expense?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if (isset($start_date) && isset($end_date)): ?>
            <!-- Display Summary Statistics -->
            <div id="expenseReportSummary">
                <h2>Expense Report Summary</h2>
                <p><strong>Total Expenses from <?= $start_date ?> to <?= $end_date ?>: </strong>$<?= number_format($totalExpenses, 2) ?></p>
                
                <h3>Average Spending Per Category</h3>
                <ul>
                    <?php if (!empty($averagePerCategory)): ?>
                        <?php foreach ($averagePerCategory as $category => $average): ?>
                            <li><?= htmlspecialchars($category) ?>: $<?= number_format($average, 2) ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No expenses found for this date range.</li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>

    </main>
</body>
</html>
