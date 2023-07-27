<?php

?>

<!DOCTYPE HTML>
<head>
    <title>Password Input</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
        }
        input[type="password"] {
            padding: 10px;
            font-size: 16px;
        }
        button {
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #76232F;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<html>
<form action="redirect.php" method="post">
    <input type="password" name="PASSWORD" placeholder="Enter your password" required>
    <button type="submit">Submit</button>
</form>
</html>
