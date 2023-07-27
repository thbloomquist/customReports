<?php
session_start();
// Backend.php v2.0 this one'll be totally future proof

$currentDate = $_POST['DATE'];
$currentYear = substr($currentDate, 0, 4);
$currentMonth = substr($currentDate, 5, 2);
$currentDay = substr($currentDate, 8, 2);
// the substr function in php creates a substring given the initial starting point (given in integer index) 
// and the length of the substr, we can manipulate this to section off each part of the date that is input
// on the HTML form
$nxtMonth = $currentMonth + 1;
// B/c PHP is loosely structured we can then add integers to a string of numbers
$statement = "York Blockhouse, LP Income Report as of ".$currentMonth."/".$currentDay."/".$currentYear;
//header that appears on the page
if($nxtMonth == 13) {
 $nxtMonth = 1;
}
//edge case for december inputs
if($nxtMonth < 10) {
 $nxtMonth = "0".$nxtMonth;
}
//edge case
$lastYear = $currentYear - 1;
//same as nxtMonth but necessary to do comparisons to last years incomes/expenses
$thisYearPeriodStart = $currentYear."-".$currentMonth."-01 00:00:00";
$thisYearPeriodEnd = $currentYear."-".$nxtMonth."-01 00:00:00";
$lastYearPeriodStart = $lastYear."-".$currentMonth."-01 00:00:00";
$lastYearPeriodEnd = $lastYear."-".$nxtMonth."-01 00:00:00";
$lastYearDayOne = $lastYear."-01-01 00:00:00";
$thisYearDayOne = $currentYear."-01-01 00:00:00";
// Will be used in SQL statements to bind the results based on time
// ex: (date >= StartOfMonth AND date < StartOfNxtMonth)
// structure of strings are specific to SQL

$user = '{CENSORED}';
$password = '{CENSORED}';
$database = '{CENSORED}';
$servername = '{CENSORED}';
//Inputs needed to access Database
//Secured inputs
$mysqli = new mysqli($servername, $user, $password, $database);
//object Acessing database

if ($mysqli->connect_error) {
    die('Connect Error (' .
    $mysqli->connect_errno . ') '.
    $mysqli->connect_error);
}

$code5 = array();
$revSQL = " SELECT code FROM qdm_double_entry_accounts WHERE code >= 400000 AND code <= 410000 AND deleted_at IS NULL ORDER BY code";
$result = $mysqli->query($revSQL);
while($rows=$result->fetch_assoc()) {
$code5[] = $rows['code'];
}
// this returns each account no ordered in ascending order (due to accountings request)
//I then use these numbers in this specific order to construct an array of id #'s in the same order
//Accounting stated all revenue accounts were contained between 400000 and 410000 so it should grab all existing ones
//and any new ones added
$revAccount = array();
for($i = 0; $i < count($code5); $i++) {
 $sql1 = "SELECT id FROM qdm_double_entry_accounts WHERE code = ?";
 $stmt = $mysqli->prepare($sql1);
 $stmt->bind_param("i", $code5[$i]);
 $stmt->execute();
 $result = $stmt->get_result();
 $row = $result->fetch_assoc();
 $revAccount[] = $row['id'];
}
// Accounting stated "All Revenue Accounts will fall between account numbers 400000 and 410000"
// meaning that this array SHOULD be future-proof
// This returns all Account ID's that exist between this set of account numbers
$code6 = array();
$expSQL = " SELECT code FROM qdm_double_entry_accounts WHERE code >= 700000 AND code <= 750000 AND deleted_at IS NULL ORDER BY code ";
$result = $mysqli->query($expSQL);
while($rows=$result->fetch_assoc()) {
$code6[] = $rows['code'];
}
// this returns each account no ordered in ascending order (due to accountings request)
//I then use these numbers in this specific order to construct an array of id #'s in the same order
$expAccount = array();
for($i = 0; $i < count($code6); $i++) {
 $sql1 = "SELECT id FROM qdm_double_entry_accounts WHERE code = ?";
 $stmt = $mysqli->prepare($sql1);
 $stmt->bind_param("i", $code6[$i]);
 $stmt->execute();
 $result = $stmt->get_result();
 $row = $result->fetch_assoc();
 $expAccount[] = $row['id'];
}


function gAcc($idx, $db) {
 $sql = "SELECT code FROM qdm_double_entry_accounts WHERE id = ? AND deleted_at IS NULL";
 $stmt = $db->prepare($sql);
 $stmt->bind_param("i",$idx);
 $stmt->execute();
 $result = $stmt->get_result();
 $rows = $result->fetch_assoc();
 $str = $rows['code'];
 return $str;
}
// returns a SINGLE accounts Account Number, given an accounts id #
function gDsc($idx, $db) {
 $sql = "SELECT name FROM qdm_double_entry_accounts WHERE id = ? AND deleted_at IS NULL";
 $stmt = $db->prepare($sql);
 $stmt->bind_param("i", $idx);
 $stmt->execute();
 $result = $stmt->get_result();
 $rows = $result->fetch_assoc();
 $str = $rows['name'];
 return $str;
}
// returns a single accounts 'Description' (name) given an accounts id #
function getMoneyRev($idx, $db, $start, $end) {
 $num = 0;
 $sql = "SELECT credit,debit FROM qdm_double_entry_ledger WHERE account_id = ? AND deleted_at IS NULL AND issued_at >= ? AND issued_at < ?";
 $stmt = $db->prepare($sql);
 $stmt->bind_param("iss", $idx, $start, $end);
 $stmt->execute();
 $result = $stmt->get_result();
 while($rows = $result->fetch_assoc()) {
  $num = $num + $rows['credit'];
  $num = $num - $rows['debit'];
 }
 return $num;
}
//so any actual transactions that are inputted that affect the account are tracked within double_entry_ledger
//I did a fair amount of testing to make sure that this was true, but the only thing that ties the _ledger and _account tables
//together is the account_id and id column
//so this function gathers every instance of non-null credit/debit columns and adds all the credit while subtracting all the debit
//It's credit-debit because this is a revenue account (idk thats an accounting thing but thats how it works)
function getMoneyExp($idx, $db, $start, $end) {
 $num = 0;
 $sql = "SELECT credit,debit FROM qdm_double_entry_ledger WHERE account_id = ? AND deleted_at IS NULL AND issued_at >= ? AND issued_at < ?";
 $stmt = $db->prepare($sql);
 $stmt->bind_param("iss", $idx, $start, $end);
 $stmt->execute();
 $result = $stmt->get_result();
 while($rows = $result->fetch_assoc()) {
  $num = $num - $rows['credit'];
  $num = $num + $rows['debit'];
 }
 return $num;
}
//sums all the credit and debit (subtracting debit) recording on a single account to return its current balance given a period & account id #
//It's debit-credit b/c it's an expense account
function getPercentRev($idx, $db, $start, $end, array $arr) {
 $part = getMoneyRev($idx, $db, $start, $end);
 $whole = getTotalRev($arr, $db, $start, $end);
 if($part == 0 && $whole == 0) {
 return 0;
 }
 if($part > 0 && $whole == 0) {
 return 100;
 }
 if($part < 0 && $whole == 0) {
 return -100;
 }
 $percent = $part/$whole;
 $percent = $percent*100;
 $percent = round($percent, 2);
 return $percent;
}
// simply calculates the total value attached to a single account and compares it to the total value of all accounts
// with a few edge cases to deal with zero b/c division and zero doesnt work very well together
function getPercentExp($idx, $db, $start, $end, array $arr, array $arr2) {
 $part = getMoneyExp($idx, $db, $start, $end);
 $whole = getTotalRev($arr2, $db, $start, $end);
 if($part == 0 && $whole == 0) {
 return 0;
 }
 if($part > 0 && $whole == 0) {
 return 100;
 }
 if($part < 0 && $whole == 0) {
 return -100;
 }
 $percent = $part/$whole;
 $percent = $percent*100;
 $percent = round($percent, 2);
 return $percent;
}
//The percentage of expenditure is relative to the total amount of revenue, that is to say if your total revenue was 100
//and the same years total expenses were 70 your percentage of expense would be 70%
function getTotalRev(array $arr, $db, $start, $end) {
$totalV = 0;
 for($y = 0; $y < count($arr); $y++) {
   $currentID = $arr[$y];
   $totalV = $totalV + getMoneyRev($currentID, $db, $start, $end);
  }
return $totalV;
}
//utilizing the arrays created in the beginning of the program, this function parses through them and calculates
//each accounts revenue adding them into one variable and returning it
function getTotalExp(array $arr, $db, $start, $end) {
$totalV = 0;
 for($v = 0; $v < count($arr); $v++) {
 $currentID = $arr[$v];
 $totalV = $totalV + getMoneyExp($currentID, $db, $start, $end);
 }
return $totalV;
}
//same thing, except using the expense function for the different calculation
function getTotalPercentRev(array $arr, $db, $start, $end) {
 $part = getTotalRev($arr, $db, $start, $end);
 $whole = getTotalRev($arr, $db, $start, $end);
if($part == 0 && $whole == 0) {
 return 0;
 }
 if($part > 0 && $whole == 0) {
 return 100;
 }
 if($part < 0 && $whole == 0) {
 return -100;
 }
 $percent = $part/$whole;
 $percent = $percent * 100;
 $percent = round($percent, 2);
 return $percent;
}
// now you might see this as redundant and granted yes it is
// but it will be an indicator that there is an issue with the code running
// the income statement if this ever returns anything less than 100%
// and will signal accounting that something is up and they definetly
// shouldnt submit this to the irs and itll probably save us some money or something idk
function getTotalPercentExp(array $arr, $db, $start, $end, array $arr2) {
 $part = getTotalExp($arr, $db, $start, $end);
 $whole = getTotalRev($arr2, $db, $start, $end);
if($part == 0 && $whole == 0) {
 return 0;
 }
 if($part > 0 && $whole == 0) {
 return 100;
 }
 if($part < 0 && $whole == 0) {
 return -100;
 }
 $percent = $part/$whole;
 $percent = $percent* 100;
 $percent = round($percent, 2);
 return $percent;
}
//compares total expenses to total revenue and returns it in percentage form
function getNetIncome(array $rArr, array $eArr, $db, $start, $end) {
 $totalRev = getTotalRev($rArr, $db, $start, $end);
 $totalExp = getTotalExp($eArr, $db, $start, $end);
 $netIncome = $totalRev - $totalExp;
 return $netIncome;
}
//net income = totalRev - totalExp
function getNetPercent(array $rArr, array $eArr, $db, $start, $end) {
 $netIncome = getNetIncome($rArr, $eArr, $db, $start, $end);
 $totalRev = getTotalRev($rArr, $db, $start, $end);
if($netIncome == 0 && $totalRev == 0) {
 return 0;
 }
 if($netIncome > 0 && $totalRev == 0) {
 return 100;
 }
 if($netIncome < 0 && $totalRev == 0) {
 return -100;
 }
 $netPercent = $netIncome/$totalRev;
 $netPercent = $netPercent*100;
 $netPercent = round($netPercent, 2);
 return $netPercent;
}
//same thing as before just represented in percentage
//similar to expense, net incomes percentage reference is revenue as net income is a percentage of the entire revenue
?>

<!DOCTYPE html>
<html>
<head>
 <style>
table {
 margin: 0 auto;
 font-size: large;
 border: 1px solid black;
 }
 h1 {
 text-align: center;
 color: #76232f;
 font-size: xx-large;
 font-family: 'Gill Sans', 'Gill Sans MT', 'Calibri', 'Trebuchet MS', 'sans-serif';
 }
 td {
 background-color: #E4F5D4;
 border: 1px solid black;
 }
 th,
 td {
 font-weight: bold;
 border: 1px solid black;
 padding: 10px;
 text-align: center;
 }
 td {
 font-weight: lighter;
 }
 </style>
</head>

<body>
<h1> <?php echo $statement; ?></h1>
<table>
 <tr>
  <th>Account No.</th>
  <th>Description</th>
  <th>TY-Pd</th>
  <th>TY%-Pd</th>
  <th>LY-Pd</th>
  <th>LY%-Pd</th>
  <th>TY-YTD</th>
  <th>TY%-YTD</th>
  <th>LY-YTD</th>
  <th>LY%-YTD</th>
 </tr>
<tr>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #FFFFFF; font-weight: bold;">Revenues</td>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #000000; font-weight: bold;"></td>
</tr>
<?php for($i = 0; $i < count($revAccount); $i++) { ?>
<tr>
 <td><?php echo gAcc($revAccount[$i], $mysqli); ?></td>
 <td><?php echo gDsc($revAccount[$i], $mysqli); ?></td>
 <td><?php echo getMoneyRev($revAccount[$i], $mysqli, $thisYearPeriodStart, $thisYearPeriodEnd); ?></td>
 <td><?php echo getPercentRev($revAccount[$i], $mysqli, $thisYearPeriodStart, $thisYearPeriodEnd, $revAccount); ?>%</td>
 <td><?php echo getMoneyRev($revAccount[$i], $mysqli, $lastYearPeriodStart, $lastYearPeriodEnd); ?></td>
 <td><?php echo getPercentRev($revAccount[$i], $mysqli, $lastYearPeriodStart, $lastYearPeriodEnd, $revAccount); ?>%</td>
 <td><?php echo getMoneyRev($revAccount[$i], $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
 <td><?php echo getPercentRev($revAccount[$i], $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $revAccount); ?>%</td>
 <td><?php echo getMoneyRev($revAccount[$i], $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo getPercentRev($revAccount[$i], $mysqli, $lastYearDayOne, $lastYearPeriodEnd, $revAccount); ?>%</td>
</tr>
<?php } ?>
<tr>
 <td style ="background-color: #000000; font-weight: bold;"></td>
 <td style="background-color: #fcd299; font-weight: bold;">Total Revenues:</td>
 <td style="background-color: #fcd299; font-weight: bold;"><?php echo getTotalRev($revAccount, $mysqli, $thisYearPeriodStart, $thisYearPeriodEnd); ?></td>
 <td style="background-color: #fcd299; font-weight: bold;"><?php echo getTotalPercentRev($revAccount, $mysqli, $thisYearPeriodStart, $thisYearPeriodEnd); ?>%</td>
 <td style="background-color: #fcd299; font-weight: bold;"><?php echo getTotalRev($revAccount, $mysqli, $lastYearPeriodStart, $lastYearPeriodEnd); ?></td>
 <td style="background-color: #fcd299; font-weight: bold;"><?php echo getTotalPercentRev($revAccount, $mysqli, $lastYearPeriodStart, $lastYearPeriodEnd); ?>%</td>
 <td style="background-color: #fcd299; font-weight: bold;"><?php echo getTotalRev($revAccount, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
 <td style="background-color: #fcd299; font-weight: bold;"><?php echo getTotalPercentRev($revAccount, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?>%</td>
 <td style="background-color: #fcd299; font-weight: bold;"><?php echo getTotalRev($revAccount, $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td style="background-color: #fcd299; font-weight: bold;"><?php echo getTotalPercentRev($revAccount, $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?>%</td>
</tr>
<tr>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #FFFFFF; font-weight: bold;">Expenses</td>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #000000; font-weight: bold;"></td>
</tr>
<?php for($i = 0; $i < count($expAccount); $i++) { ?>
<tr>
 <td><?php echo gAcc($expAccount[$i], $mysqli); ?></td>
 <td><?php echo gDsc($expAccount[$i], $mysqli); ?></td>
 <td><?php echo getMoneyExp($expAccount[$i], $mysqli, $thisYearPeriodStart, $thisYearPeriodEnd); ?></td>
 <td><?php echo getPercentExp($expAccount[$i], $mysqli, $thisYearPeriodStart, $thisYearPeriodEnd, $expAccount, $revAccount); ?>%</td>
 <td><?php echo getMoneyExp($expAccount[$i], $mysqli, $lastYearPeriodStart, $lastYearPeriodEnd); ?></td>
 <td><?php echo getPercentExp($expAccount[$i], $mysqli, $lastYearPeriodStart, $lastYearPeriodEnd, $expAccount, $revAccount); ?>%</td>
 <td><?php echo getMoneyExp($expAccount[$i], $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
 <td><?php echo getPercentExp($expAccount[$i], $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $expAccount, $revAccount); ?>%</td>
 <td><?php echo getMoneyExp($expAccount[$i], $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo getPercentExp($expAccount[$i], $mysqli, $lastYearDayOne, $lastYearPeriodEnd, $expAccount, $revAccount); ?>%</td>
</tr>
 <?php } ?>
<tr>
 <td style ="background-color: #000000; font-weight: bold;"></td>
 <td style="background-color: #fcd299; font-weight: bold;">Total Expenses:</td>
 <td style="background-color: #fcd299; font-weight: bold;"><?php echo getTotalExp($expAccount, $mysqli, $thisYearPeriodStart, $thisYearPeriodEnd); ?></td>
 <td style="background-color: #fcd299; font-weight: bold;"><?php echo getTotalPercentExp($expAccount, $mysqli, $thisYearPeriodStart, $thisYearPeriodEnd, $revAccount); ?>%</td>
 <td style="background-color: #fcd299; font-weight: bold;"><?php echo getTotalExp($expAccount, $mysqli, $lastYearPeriodStart, $lastYearPeriodEnd); ?></td>
 <td style="background-color: #fcd299; font-weight: bold;"><?php echo getTotalPercentExp($expAccount, $mysqli, $lastYearPeriodStart, $lastYearPeriodEnd, $revAccount); ?>%</td>
 <td style="background-color: #fcd299; font-weight: bold;"><?php echo getTotalExp($expAccount, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
 <td style="background-color: #fcd299; font-weight: bold;"><?php echo getTotalPercentExp($expAccount, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $revAccount); ?>%</td>
 <td style="background-color: #fcd299; font-weight: bold;"><?php echo getTotalExp($expAccount, $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td style="background-color: #fcd299; font-weight: bold;"><?php echo getTotalPercentExp($expAccount, $mysqli, $lastYearDayOne, $lastYearPeriodEnd, $revAccount); ?>%</td>
</tr>
<tr>
 <td style = "background-color: #000000; font-weight: bold;"></td>
 <td style = "background-color: #ffd1dc; font-weight: bold;">Net Income:</td>
 <td style="background-color: #ffd1dc; font-weight: bold;"><?php echo getNetIncome($revAccount, $expAccount, $mysqli, $thisYearPeriodStart, $thisYearPeriodEnd); ?></td>
 <td style="background-color: #ffd1dc; font-weight: bold;"><?php echo getNetPercent($revAccount, $expAccount, $mysqli, $thisYearPeriodStart, $thisYearPeriodEnd); ?>%</td>
 <td style="background-color: #ffd1dc; font-weight: bold;"><?php echo getNetIncome($revAccount, $expAccount, $mysqli, $lastYearPeriodStart, $lastYearPeriodEnd); ?></td>
 <td style="background-color: #ffd1dc; font-weight: bold;"><?php echo getNetPercent($revAccount, $expAccount, $mysqli, $lastYearPeriodStart, $lastYearPeriodEnd); ?>%</td>
 <td style="background-color: #ffd1dc; font-weight: bold;"><?php echo getNetIncome($revAccount, $expAccount, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
 <td style="background-color: #ffd1dc; font-weight: bold;"><?php echo getNetPercent($revAccount, $expAccount, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?>%</td>
 <td style="background-color: #ffd1dc; font-weight: bold;"><?php echo getNetIncome($revAccount, $expAccount, $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td style="background-color: #ffd1dc; font-weight: bold;"><?php echo getNetPercent($revAccount, $expAccount, $mysqli, $lastYearDayOne, $lastYearPeriodEnd);?>%</td>
</tr>
</table>
</body>
</html>
<?php $mysqli->close(); ?>
<?php  $_SESSION = [];  ?>
