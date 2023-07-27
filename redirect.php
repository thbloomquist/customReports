<?php
session_start();

$file = fopen("stuff.txt", "r");
$stuffn = fread($file, 15);

$thing = hash("md5", $stuffn);
$input = hash("md5", $_POST['PASSWORD']);



if($input != $thing) {
 $_SESSION = [];
}


if($input == $thing) {
 $_SESSION["key"] = "good";
}



?>


<!DOCTYPE HTML>
<html>
<head>
<style>
        button {
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #76232F;
            color: white;
            border: none;
            cursor: pointer;
        }
        .container {
            text-align: center;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
</style>
</head>

<body>
<div class = "container">
<?php if($input == $thing) { ?>
<form action = "income.php">
<button type = "submit">Click here!</button>
</form>
<?php } ?>

<?php if($input != $thing) { ?>
<h1> Wrong password! </h1>
<?php } ?>
</div>
</body>
</html>
