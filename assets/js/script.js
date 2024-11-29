// Example of handling expense adding
function openAddExpenseModal() {
    document.getElementById('expenseModal').style.display = 'block';
  }
  
  function closeAddExpenseModal() {
    document.getElementById('expenseModal').style.display = 'none';
  }
  
  function submitExpense(event) {
    event.preventDefault();
    
    // Get form data
    const amount = document.getElementById('amount').value;
    const category = document.getElementById('category').value;
    const description = document.getElementById('description').value;
    const date = document.getElementById('date').value;
  
    // Send data to backend via fetch or AJAX (POST method)
    fetch('add_expense.php', {
      method: 'POST',
      body: JSON.stringify({ amount, category, description, date }),
      headers: { 'Content-Type': 'application/json' }
    }).then(response => {
      if (response.ok) {
        alert('Expense added!');
        location.reload();  // Reload to see the new expense
      }
    });
  }
  
  function deleteExpense(id) {
    fetch(`delete_expense.php?id=${id}`, { method: 'GET' })
      .then(response => response.ok ? alert('Expense deleted!') : alert('Failed to delete'))
      .then(() => location.reload());
  }
  