<?php
session_start();
$token = $_SESSION["key"];

if($_SESSION["key"] != "good") {
return;
}

//session_unset($_SESSION["key"]);
?>

<!DOCTYPE html>
<html>
<head>
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .form-container {
      display: flex;
      justify-content: space-between;
      width: 600px;
    }

    .form-container form {
      width: 280px;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <form action="incomeReport.php" method="post">
      <!-- Income Statement form content -->
      <h2>Income Statement</h2>
      <label for="income-input">Input Period:</label>
      <input type="date" id="income-input" name="DATE" required>
      <br>
      <input type="submit" value="Submit">
    </form>

    <form action="balanceSheet.php" method="post">
      <!-- Balance Sheet form content -->
      <h2>Balance Sheet</h2>
      <label for="balance-input">Input Period:</label>
      <input type="date" id="balance-input" name="PERIOD" required>
      <br>
      <input type="submit" value="Submit">
    </form>
  </div>
</body>

