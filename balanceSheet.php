<?php
session_start();

//Blockhouse Balance Sheet 2023:
//IMPORTANT
//
//
//You will see in the SQL statements issued_at >= ? AND issued_at < ?
//These specify the time at which these data entries should be found in
//For example if you wanted to gather information about july it would look something like
//issued_at >= 2023-07-01 00:00:00 AND issued_at < 2023-08-01 00:00:00
//So long as the data value is greater than or equal to the first second of the month of july
//it will return as a result of that query but NOT anything contained after the first second of august
$currentDate = $_POST['PERIOD'];

$currentYear = substr($currentDate, 0, 4);
$currentMonth = substr($currentDate, 5, 2);
$currentDay = substr($currentDate, 8, 2);
//sections off specific parts of the input necessary for manipulation
$lastYear = $currentYear - 1;

$nxtMonth = $currentMonth + 1;
if($nxtMonth == 13) {
 $nxtMonth = 1;
}
if($nxtMonth < 10) {
 $nxtMonth = "0".$nxtMonth;
}
//edge cases
$thisYearDayOne = $currentYear."-01-01 00:00:00";
$lastYearDayOne = $lastYear."-01-01 00:00:00";
$thisYearPeriodEnd = $currentYear."-".$nxtMonth."-01 00:00:00";
$lastYearPeriodEnd = $lastYear."-".$nxtMonth."-01 00:00:00";
// Will be used in SQL statements to bind the results based on time
// ex: (date >= StartOfMonth AND date < StartOfNxtMonth)
// structure of strings are specific to SQL

$statement = "York Blockhouse, LP Balance Sheet as of ".$currentMonth."/".$currentDay."/".$currentYear;
//header of HTML page
$user = '{CENSORED}';
$password = '{CENSORED}';
$database = '{CENSORED}';
$servername = '{CENSORED}';

$mysqli = new mysqli($servername, $user, $password, $database);
//object accessing the database
if ($mysqli->connect_error) {
    die('Connect Error (' .
    $mysqli->connect_errno . ') '.
    $mysqli->connect_error);
}

$code1 = array();
$curAssSQL = " SELECT code FROM qdm_double_entry_accounts WHERE code >= 100000 AND code <= 120000 AND deleted_at IS NULL ORDER BY code";
$result = $mysqli->query($curAssSQL);
while($rows=$result->fetch_assoc()) {
 $code1[] = $rows['code'];
}
//creates an array in ascending order containing all non-null code's between those bounds
//the bounds of currentAssets are [100000, 120000] (inclusive)
$currentAssets = array();
for($i = 0; $i < count($code1); $i++) {
 $eSQL2 = " SELECT id FROM qdm_double_entry_accounts WHERE code = ? AND deleted_at IS NULL";
 $stmt = $mysqli->prepare($eSQL2);
 $stmt->bind_param("i", $code1[$i]);
 $stmt->execute();
 $result = $stmt->get_result();
 $row = $result->fetch_assoc();
 $currentAssets[] = $row['id'];
}
$code2 = array();
$landSQL = " SELECT code FROM qdm_double_entry_accounts WHERE code >= 150000 AND code <= 152500 AND deleted_at IS NULL ORDER BY code";
$result = $mysqli->query($landSQL);
while($rows = $result->fetch_assoc()) {
 $code2[] = $rows['code'];
}
//the bounds to land accounts are [150000, 152500] (inclusive)
$land = array();
for($i = 0; $i < count($code2); $i++) {
 $eSQL2 = " SELECT id FROM qdm_double_entry_accounts WHERE code = ? AND deleted_at IS NULL";
 $stmt = $mysqli->prepare($eSQL2);
 $stmt->bind_param("i", $code2[$i]);
 $stmt->execute();
 $result = $stmt->get_result();
 $row = $result->fetch_assoc();
 $land[] = $row['id'];
}
$code3 = array();
$buildSQL = " SELECT code FROM qdm_double_entry_accounts WHERE code >= 153000 AND code <= 160000 AND deleted_at IS NULL ORDER BY code";
$result = $mysqli->query($buildSQL);
while($rows = $result->fetch_assoc()) {
 $code3[] = $rows['code'];
}
//the bounds to building accounts are [153000, 160000] (inclusive)
$buildings = array();
for($i = 0; $i < count($code3); $i++) {
 $eSQL2 = " SELECT id FROM qdm_double_entry_accounts WHERE code = ? AND deleted_at IS NULL";
 $stmt = $mysqli->prepare($eSQL2);
 $stmt->bind_param("i", $code3[$i]);
 $stmt->execute();
 $result = $stmt->get_result();
 $row = $result->fetch_assoc();
 $buildings[] = $row['id'];
}
$code4 = array();
$accDeppSQL = " SELECT code FROM qdm_double_entry_accounts WHERE code >= 163000 AND code <= 170000 AND deleted_at IS NULL ORDER BY code";
$result = $mysqli->query($accDeppSQL);
while($rows = $result->fetch_assoc()) {
 $code4[] = $rows['code'];
}
//the bounds to accumulated depriciation accounts are [163000, 170000] (inclusive)
$accDep = array();
for($i = 0; $i < count($code4); $i++) {
 $eSQL2 = " SELECT id FROM qdm_double_entry_accounts WHERE code = ? AND deleted_at IS NULL";
 $stmt = $mysqli->prepare($eSQL2);
 $stmt->bind_param("i", $code4[$i]);
 $stmt->execute();
 $result = $stmt->get_result();
 $row = $result->fetch_assoc();
 $accDep[] = $row['id'];
}
$code5 = array();
$cLiaSQL = " SELECT code FROM qdm_double_entry_accounts WHERE code >= 200000 AND code <= 240000 AND deleted_at IS NULL ORDER BY code";
$result = $mysqli->query($cLiaSQL);
while($rows = $result->fetch_assoc()) {
 $code5[] = $rows['code'];
}
//the bounds to current liability accounts are [200000, 240000] (inclusive)
$currentLia = array();
for($i = 0; $i < count($code5); $i++) {
 $eSQL2 = " SELECT id FROM qdm_double_entry_accounts WHERE code = ? AND deleted_at IS NULL";
 $stmt = $mysqli->prepare($eSQL2);
 $stmt->bind_param("i", $code5[$i]);
 $stmt->execute();
 $result = $stmt->get_result();
 $row = $result->fetch_assoc();
 $currentLia[] = $row['id'];
}
$code6 = array();
$lLiaSQL = " SELECT code FROM qdm_double_entry_accounts WHERE code >= 250000 AND code <= 260000 AND deleted_at IS NULL ORDER BY code";
$result = $mysqli->query($lLiaSQL);
while($rows = $result->fetch_assoc()) {
 $code6[] = $rows['code'];
}
//the bounds to long term liability accounts are [250000, 260000] (inclusive)
$longTermLia = array();
for($i = 0; $i < count($code6); $i++) {
 $eSQL2 = " SELECT id FROM qdm_double_entry_accounts WHERE code = ? AND deleted_at IS NULL";
 $stmt = $mysqli->prepare($eSQL2);
 $stmt->bind_param("i", $code6[$i]);
 $stmt->execute();
 $result = $stmt->get_result();
 $row = $result->fetch_assoc();
 $longTermLia[] = $row['id'];
}
$code7 = array();
$capSQL = " SELECT code FROM qdm_double_entry_accounts WHERE code >= 300000 AND code <= 310000 AND deleted_at IS NULL ORDER BY code";
$result = $mysqli->query($capSQL);
while($rows = $result->fetch_assoc()) {
 $code7[] = $rows['code'];
}
//the bounds for capital accounts are [300000, 310000] (inclusive)
$capital = array();
for($i = 0; $i < count($code7); $i++) {
 $eSQL2 = " SELECT id FROM qdm_double_entry_accounts WHERE code = ? AND deleted_at IS NULL";
 $stmt = $mysqli->prepare($eSQL2);
 $stmt->bind_param("i", $code7[$i]);
 $stmt->execute();
 $result = $stmt->get_result();
 $row = $result->fetch_assoc();
 $capital[] = $row['id'];
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
//returns an 'account no.' found within the 'code' column of the database given a single accounts sql-specific id #
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
//returns a desc found within the 'name' column
function getMoneyAssets($idx, $db, $start, $end) {
 $num = 0;
 $sql = "SELECT credit,debit FROM qdm_double_entry_ledger WHERE account_id = ? AND deleted_at IS NULL AND issued_at >= ? AND issued_at < ?";
 $stmt = $db->prepare($sql);
 $stmt->bind_param("iss", $idx, $start, $end);
 $stmt->execute();
 $result = $stmt->get_result();
 while($rows = $result->fetch_assoc()) {
 $num = $num + $rows['debit'];
 $num = $num - $rows['credit'];
 }
 return $num;
}
//reference getMoneyExp in incomeReport.php
function getMoneyLiabl($idx, $db, $start, $end) {
 $num = 0;
 $sql = "SELECT credit,debit FROM qdm_double_entry_ledger WHERE account_id = ? AND deleted_at IS NULL AND issued_at >= ? AND issued_at < ?";
 $stmt = $db->prepare($sql);
 $stmt->bind_param("iss", $idx, $start, $end);
 $stmt ->execute();
 $result = $stmt->get_result();
 while($rows = $result->fetch_assoc()) {
 $num = $num + $rows['credit'];
 $num = $num - $rows['debit'];
 }
 return $num;
}
//refernece getMoneyRev in incomeReport.php
function calcChangeAssets($idx, $db, $start1, $end1, $start2, $end2) {
$currYear = getMoneyAssets($idx, $db, $start1, $end1);
$prevYear = getMoneyAssets($idx, $db, $start2, $end2);
$num = $currYear-$prevYear;
return $num;
}
//simply calculates both years worth of money/revenue and subtracts them to calculate the change
function calcChangeLiabl($idx, $db, $start1, $end1, $start2, $end2) {
$currYear = getMoneyLiabl($idx, $db, $start1, $end1);
$prevYear = getMoneyLiabl($idx, $db, $start2, $end2);
$num = $currYear-$prevYear;
return $num;
}
//same thing but with different calculations
function calcChangePercentAssets($idx, $db, $start1, $end1, $start2, $end2) {
$prevYear = getMoneyAssets($idx, $db, $start2, $end2);
$amt = calcChangeAssets($idx, $db, $start1, $end1, $start2, $end2);
if($prevYear == 0 && $amt == 0) {
return 0;
}
if($prevYear == 0 && $amt > 0) {
return 100;
}
if($prevYear == 0 && $amt < 0) {
return -100;
}
$percent = $amt/$prevYear;
$percent = $percent*100;
$percent = round($percent, 2);
return $percent;
}
//part / whole * 100 = %
//reference percentage functions in incomeReport.php
//this percentage is based off of the (change in money)/(Last Year's Money)
function calcChangePercentLiabl($idx, $db, $start1, $end1, $start2, $end2) {
$prevYear = getMoneyLiabl($idx, $db, $start2, $end2);
$amt = calcChangeLiabl($idx, $db, $start1, $end1, $start2, $end2);
if($prevYear == 0 && $amt == 0) {
return 0;
}
if($prevYear == 0 && $amt > 0) {
return 100;
}
if($prevYear == 0 && $amt < 0) {
return -100;
}

$percent = $amt/$prevYear;
$percent = $percent*100;
$percent = round($percent, 2);
return $percent;
}
//same thing but with liabilities calculations
function getTotalAssets(array $arr, $db, $start, $end) {
 $total = 0;
 for($i = 0; $i < count($arr); $i++) {
  $total += getMoneyAssets($arr[$i], $db, $start, $end);
 }
return $total;
}
//uses the array to calculate the singular function for all of the inputs in the array
function getTotalLiabl(array $arr, $db, $start, $end) {
 $total = 0;
 for($i = 0; $i < count($arr); $i++) {
  $total += getMoneyLiabl($arr[$i], $db, $start, $end);
 }
return $total;
}
//same thing but with liabilities
function getTotalChangeAssets(array $arr, $db, $start1, $end1, $start2, $end2) {
 $total = 0;
 for($i = 0; $i < count($arr); $i++) {
 $total += calcChangeAssets($arr[$i], $db, $start1, $end1, $start2, $end2);
}
return $total;
}
//calculates the change in each index of the array and sums it for the grand total change
function getTotalChangeLiabl(array $arr, $db, $start1, $end1, $start2, $end2) {
 $total = 0;
 for($i = 0; $i < count($arr); $i++) {
 $total += calcChangeLiabl($arr[$i], $db, $start1, $end1, $start2, $end2);
}
return $total;
}
//same thing as above but with liabilities calculations
function getTotalChangePercentAssets(array $arr, $db, $start1, $end1, $start2, $end2) {
$part = getTotalChangeAssets($arr, $db, $start1, $end1, $start2, $end2);
$whole = getTotalAssets($arr, $db, $start2, $end2);
if($part == 0 && $whole == 0) {
return 0;
}
if($whole == 0 && $part > 0) {
return 100;
}
if($whole == 0 && $part < 0) {
return -100;
}
$percent = $part/$whole;
$percent = $percent * 100;
$percent = round($percent, 2);
return $percent;
}
//calculates the grand percentage using the total functions and the same calculations as the other percentage function of above
function getTotalChangePercentLiabl(array $arr, $db, $start1, $end1, $start2, $end2) {
$part = getTotalChangeLiabl($arr, $db, $start1, $end1, $start2, $end2);
$whole = getTotalLiabl($arr, $db, $start2, $end2);
if($part == 0 && $whole == 0) {
return 0;
}
if($whole == 0 && $part > 0) {
return 100;
}
if($whole == 0 && $part < 0) {
return -100;
}
$percent = $part/$whole;
$percent = $percent * 100;
$percent = round($percent, 2);
return $percent;
}
// important difference between functions handling liabilities and assets is all liabilities require credit-debit
//meaning that they must reference the original getMoney function specifically designed to calculate credit-debit rather than
//the asset function that calculates debit-credit (cuz finances are weird)
function getNetBuildingCost(array $depArr, array $buildArr, $db, $start, $end) {
$totalDep = getTotalAssets($depArr, $db, $start, $end);
$totalBuild = getTotalAssets($buildArr, $db, $start, $end);
$temp = $totalBuild - $totalDep;
return $temp;
}
//netBuildingCost = totalBuildingCost - LessAccumDepr
//so I simply calculate that given a year value
function getTotalLiabilities(array $curr, array $longT, $db, $start, $end) {
$totalCurr = getTotalLiabl($curr, $db, $start, $end);
$totalLong = getTotalLiabl($longT, $db, $start, $end);
$temp = $totalCurr + $totalLong;
return $temp;
}
//Total Liabilities = LongTermLiabilities+CurrentLiabilities
//so this calculates that given a years bounds of the input range
function getTotalLiabilitiesEq(array $curr, array $longT, array $cap, $db, $start, $end) {
$totalCurr = getTotalLiabl($curr, $db, $start, $end);
$totalLong = getTotalLiabl($longT, $db, $start, $end);
$totalCap = getTotalLiabl($cap, $db, $start, $end);
$temp = $totalCurr + $totalLong + $totalCap;
return $temp;
}
//Total Liabilities & Equity = Partners Capital + Long Term Liabilities + Current Liabilities
function getNetBuildingChange(array $depArr, array $buildArr, $db, $start1, $end1, $start2, $end2) {
$totalDep = getTotalChangeAssets($depArr, $db, $start1, $end1, $start2, $end2);
$totalBuild = getTotalChangeAssets($buildArr, $db, $start1, $end1, $start2, $end2);
return $totalBuild - $totalDep;
}
//The same calculation just using the changes in each to result a corresponding number
function getTotalLiabilitiesChange(array $curr, array $longT, $db, $start1, $end1, $start2, $end2) {
$totalCurr = getTotalChangeLiabl($curr, $db, $start1, $end1, $start2, $end2);
$totalLong = getTotalChangeLiabl($longT, $db, $start1, $end1, $start2, $end2);
$temp = $totalCurr + $totalLong;
return $temp;
}
//same calculation just using the changes
function getTotalLiabilitiesEqChange(array $curr, array $longT, array $cap, $db, $start1, $end1, $start2, $end2) {
$totalCurr = getTotalChangeLiabl($curr, $db, $start1, $end1, $start2, $end2);
$totalLong = getTotalChangeLiabl($longT, $db, $start1, $end1, $start2, $end2);
$totalCap = getTotalChangeLiabl($cap, $db, $start1, $end1, $start2, $end2);
$temp = $totalCurr + $totalLong + $totalCap;
return $temp;
}
//same calculation just using the changes
//Total Liabilities & Equity Change = Partners Capital Change + Long Term Liabilities Change + Current Liabilities Change
function getNetBuildingChangePercent(array $depArr, array $buildArr, $db, $start1, $end1, $start2, $end2) {
// Amt / LY
$lastYear = getNetBuildingCost($depArr, $buildArr, $db, $start2, $end2);
$amt = getNetBuildingChange($depArr, $buildArr, $db, $start1, $end1, $start2, $end2);
if ($amt == 0 && $lastYear == 0) {
return 0;
}
if($lastYear == 0 && $amt > 0) {
return 100;
}
if($lastYear == 0 && $amt < 0) {
return -100;
}
$percent = $amt / $lastYear;
$percent = $percent * 100;
$percent = round($percent, 2);
return $percent;
}
//Percent = (Amt of Total Change)/(Last Years Total Earnings)
function getTotalLiabilitiesEqChangePercent(array $curr, array $longT, array $cap, $db, $start1, $end1, $start2, $end2) {
$lastYear = getTotalLiabilitiesEq($curr, $longT, $cap, $db, $start2, $end2);
$amt = getTotalLiabilitiesEqChange($curr, $longT, $cap, $db, $start1, $end1, $start2, $end2);
if($amt == 0  && $lastYear == 0) {
return 0;
}
if($lastYear == 0 && $amt > 0) {
return 100;
}
if($lastYear == 0 && $amt < 0) {
return -100;
}
$percent = $amt / $lastYear;
$percent = $percent * 100;
$percent = round($percent, 2);
return $percent;
}
function getTotalLiabilitiesChangePercent(array $curr, array $longT, $db, $start1, $end1, $start2, $end2) {
$lastYear = getTotalLiabilities($curr, $longT, $db, $start2, $end2);
$amt = getTotalLiabilitiesChange($curr, $longT, $db, $start1, $end1, $start2, $end2);
if($amt == 0 && $lastYear == 0) {
return 0;
}
//Percent = (Amt of Total Change)/(Last Years Total Earnings)
if($lastYear == 0 && $amt > 0) {
return 100;
}
if($lastYear == 0 && $amt < 0) {
return -100;
}
$percent = $amt / $lastYear;
$percent = $percent * 100;
$percent = round($percent, 2);
return $percent;
}
function getTotalLongTermAssets(array $landArr, array $depArr, array $buildArr, $db, $start, $end) {
$totalLand = getTotalAssets($landArr, $db, $start, $end);
$totalBuild = getTotalAssets($buildArr, $db, $start, $end);
$totalDep = getTotalAssets($depArr, $db, $start, $end);
$sum = ($totalLand + $totalBuild);
$sum = $sum - $totalDep;
return $sum;
}
//total long term assets = (buildings + land) - accum depr
function getTotalLongTermChange(array $landArr, array $depArr, array $buildArr, $db, $start1, $end1, $start2, $end2) {
$totalLand = getTotalChangeAssets($landArr, $db, $start1, $end1, $start2, $end2);
$totalDep = getTotalChangeAssets($depArr, $db, $start1, $end1, $start2, $end2);
$totalBuild = getTotalChangeAssets($buildArr, $db, $start1, $end1, $start2, $end2);
$sum = ($totalLand + $totalBuild);
$sum = $sum - $totalDep;
return $sum;
}
//total long term assets change = (buildings change + land change) - accum depr change
function getTotalLongTermChangePercent(array $landArr, array $depArr, array $buildArr, $db, $start1, $end1, $start2, $end2) {
$lastYear = getTotalLongTermAssets($landArr, $depArr, $buildArr, $db, $start2, $end2);
$amt = getTotalLongTermChange($landArr, $depArr, $buildArr, $db, $start1, $end1, $start2, $end2);
if ($amt == 0 && $lastYear == 0) {
return 0;
}
if ($lastYear == 0 && $amt > 0) {
return 100;
}
if($lastYear == 0 && $amt < 0) {
return -100;
}
$percent = $amt / $lastYear;
$percent = $percent * 100;
$percent = round($percent, 2);
return $percent;
}
//(Amt of LongTermChange)/(LastYears LongTermTotal) * 100 = percent
function getFinTotalAssets(array $currents, array $landArr, array $depArr, array $buildArr, $db, $start, $end) {
$totalCurrent = getTotalAssets($currents, $db, $start, $end);
$totalLand = getTotalAssets($landArr, $db, $start, $end);
$totalBuild = getTotalAssets($buildArr, $db, $start, $end);
$totalDep = getTotalAssets($depArr, $db, $start, $end);
$sum = ($totalCurrent + $totalLand + $totalBuild);
$sum = $sum - $totalDep;
return $sum;
}
//total assets = current assets + buildings + land - accum depr
function getFinTotalAssetsChange(array $currents, array $landArr, array $depArr, array $buildArr, $db, $start1, $end1, $start2, $end2) {
$totalCurrent = getTotalChangeAssets($currents, $db, $start1, $end1, $start2, $end2);
$totalLand = getTotalChangeAssets($landArr, $db, $start1, $end1, $start2, $end2);
$totalDep = getTotalChangeAssets($depArr, $db, $start1, $end1, $start2, $end2);
$totalBuild = getTotalChangeAssets($buildArr, $db, $start1, $end1, $start2, $end2);
$sum = ($totalCurrent + $totalLand + $totalBuild);
$sum = $sum - $totalDep;
return $sum;
}
//same calculation just using change values of previously mentioned arrays
function getFinTotalAssetsChangePercent(array $currents, array $landArr, array $depArr, array $buildArr, $db, $start1, $end1, $start2, $end2) {
$lastYear = getFinTotalAssets($currents, $landArr, $depArr, $buildArr, $db, $start2, $end2);
$amt = getFinTotalAssetsChange($currents, $landArr, $depArr, $buildArr, $db, $start1, $end1, $start2, $end2);
if($amt == 0 && $lastYear == 0) {
return 0;
}
if($lastYear == 0 && $amt > 0) {
return 100;
}
if($lastYear == 0 && $amt < 0) {
return 100;
}
$percent = $amt / $lastYear;
$percent = $percent * 100;
$percent = round($percent, 2);
return $percent;
}
//(TotalAssetsChange)/(TotalAssetsLastYearValue) * 100 = Percent
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
<h1> <?php echo $statement ?></h1>
<table>
<tr>
<th>Acct No.</th>
<th>Description</th>
<th>Current Year</th>
<th>Last Year</th>
<th>Amt Change</th>
<th>% Change</th>
</tr>
<tr>
<th>Assets</th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
</tr>
<tr>
<th style = "background-color: #000000";></th>
<th>Current Assets:</th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
</tr>
<?php for($x = 0; $x < count($currentAssets); $x++) { ?>
 <tr>
 <td><?php echo gAcc($currentAssets[$x], $mysqli); ?></td>
 <td><?php echo gDsc($currentAssets[$x], $mysqli);?></td>
 <td><?php echo getMoneyAssets($currentAssets[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
 <td><?php echo getMoneyAssets($currentAssets[$x], $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo calcChangeAssets($currentAssets[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo calcChangePercentAssets($currentAssets[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</td>
 </tr>
<?php } ?>
<tr>
<th style = "background-color: #000000;"></th>
<td style = "background-color: #FCD299; font-weight: bold;">TOTAL CURRENT ASSETS</td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalAssets($currentAssets, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalAssets($currentAssets, $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalChangeAssets($currentAssets, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalChangePercentAssets($currentAssets, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</td>
</tr>
<tr>
<th style = "background-color: #000000";></th>
<th>Long-Term Assets:</th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
</tr>
<tr>
<th style = "background-color: #000000";></th>
<th>Land</th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
</tr>
<?php for ($x = 0; $x < count($land); $x++) { ?>
 <tr>
 <td><?php echo gAcc($land[$x], $mysqli); ?></td>
 <td><?php echo gDsc($land[$x], $mysqli); ?></td>
 <td><?php echo getMoneyAssets($land[$x], $mysqli, $thisYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo getMoneyAssets($land[$x], $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo calcChangeAssets($land[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo calcChangePercentAssets($land[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</td>
 </tr>
<?php } ?>
<tr>
<th style = "background-color: #000000;"></th>
<td style = "background-color: #FCD299; font-weight: bold;">TOTAL LAND COST</td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalAssets($land, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalAssets($land, $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalChangeAssets($land, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalChangePercentAssets($land, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</td>
</tr>
<tr>
<th style = "background-color: #000000";></th>
<th>Buildings</th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
</tr>
<?php for ($x = 0; $x < count($buildings); $x++) { ?>
 <tr>
 <td><?php echo gAcc($buildings[$x], $mysqli); ?></td>
 <td><?php echo gDsc($buildings[$x], $mysqli); ?></td>
 <td><?php echo getMoneyAssets($buildings[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
 <td><?php echo getMoneyAssets($buildings[$x], $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo calcChangeAssets($buildings[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo calcChangePercentAssets($buildings[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</td>
 </tr>
<?php } ?>
<tr>
<th style = "background-color: #000000;"></th>
<td style = "background-color: #FCD299; font-weight: bold;">SUB-TOTAL</td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalAssets($buildings, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalAssets($buildings, $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalChangeAssets($buildings, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalChangePercentAssets($buildings, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</td>
</tr>
<tr>
<th style = "background-color: #000000";></th>
<th>Accum. Depr</th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
<th style = "background-color: #000000";></th>
</tr>
<?php for ($x = 0; $x < count($accDep); $x++) { ?>
 <tr>
 <td><?php echo gAcc($accDep[$x], $mysqli); ?></td>
 <td><?php echo gDsc($accDep[$x], $mysqli); ?></td>
 <td><?php echo getMoneyAssets($accDep[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
 <td><?php echo getMoneyAssets($accDep[$x], $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo calcChangeAssets($accDep[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo calcChangePercentAssets($accDep[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</td>
 </tr>
<?php } ?>
<tr>
<th style = "background-color: #000000;"></th>
<td style = "background-color: #FCD299; font-weight: bold;">LESS: ACCUM. DEPR</td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalAssets($accDep, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalAssets($accDep, $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalChangeAssets($accDep, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalChangePercentAssets($accDep, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</td>
</tr>
<tr>
<th style = "background-color: #000000;"></th>
<td style = "background-color: #FCD299; font-weight: bold;">NET BUILDING COST</td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getNetBuildingCost($accDep, $buildings, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getNetBuildingCost($accDep, $buildings, $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getNetBuildingChange($accDep, $buildings, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getNetBuildingChangePercent($accDep, $buildings, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</td>
</tr>
<tr>
<th style = "background-color: #000000;"></th>
<td style = "background-color: #FCD299; font-weight: bold;">TOTAL LONG TERM ASSETS</td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalLongTermAssets($land, $accDep, $buildings, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalLongTermAssets($land, $accDep, $buildings, $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalLongTermChange($land, $accDep, $buildings, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
<td style = "background-color: #FCD299; font-weight: bold;"><?php echo getTotalLongTermChangePercent($land, $accDep, $buildings, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</td>
</tr>
<tr>
<th style = "background-color: #000000;"></th>
<td style = "background-color: #FFD1DC; font-weight: bold;">TOTAL ASSETS</td>
<td style = "background-color: #FFD1DC; font-weight: bold;"><?php echo getFinTotalAssets($currentAssets, $land, $accDep, $buildings, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
<td style = "background-color: #FFD1DC; font-weight: bold;"><?php echo getFinTotalAssets($currentAssets, $land, $accDep, $buildings, $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
<td style = "background-color: #FFD1DC; font-weight: bold;"><?php echo getFinTotalAssetsChange($currentAssets, $land, $accDep, $buildings, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
<td style = "background-color: #FFD1DC; font-weight: bold;"><?php echo getFinTotalAssetsChangePercent($currentAssets, $land, $accDep, $buildings, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</td>
</tr>
<tr>
<th style = "background-color: #FFFFFF;">Liabilities</th>
<th style = "background-color: #000000;"></th>
<th style = "background-color: #000000;"></th>
<th style = "background-color: #000000;"></th>
<th style = "background-color: #000000;"></th>
<th style = "background-color: #000000;"></th>
</tr>
<tr>
<th style = "background-color: #000000;"></th>
<th style = "background-color: #FFFFFF;">Current Liabilities</th>
<th style = "background-color: #000000;"></th>
<th style = "background-color: #000000;"></th>
<th style = "background-color: #000000;"></th>
<th style = "background-color: #000000;"></th>
</tr>
<?php for ($x = 0; $x < count($currentLia); $x++) { ?>
 <tr>
 <td><?php echo gAcc($currentLia[$x], $mysqli); ?></td>
 <td><?php echo gDsc($currentLia[$x], $mysqli); ?></td>
 <td><?php echo getMoneyLiabl($currentLia[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
 <td><?php echo getMoneyLiabl($currentLia[$x], $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo calcChangeLiabl($currentLia[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo calcChangePercentLiabl($currentLia[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</td>
 </tr>
<?php } ?>
<tr>
 <th style = "background-color: #000000;"></th>
 <th style = "background-color: #FCD299;">TOTAL CURRENT LIABILITIES</th>
 <th style = "background-color: #FCD299;"><?php echo getTotalLiabl($currentLia, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></th>
 <th style = "background-color: #FCD299;"><?php echo getTotalLiabl($currentLia, $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></th>
 <th style = "background-color: #FCD299;"><?php echo getTotalChangeLiabl($currentLia, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></th>
 <th style = "background-color: #FCD299;"><?php echo getTotalChangePercentLiabl($currentLia, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</th>
</tr>
<tr>
 <th style = "background-color: #000000;"></th>
<th style = "background-color: #FFFFFF;">Long Term Liabilities</th>
<th style = "background-color: #000000;"></th>
<th style = "background-color: #000000;"></th>
<th style = "background-color: #000000;"></th>
<th style = "background-color: #000000;"></th>
</tr>
<?php for ($x = 0; $x < count($longTermLia); $x++) { ?>
 <tr>
 <td><?php echo gAcc($longTermLia[$x], $mysqli); ?></td>
 <td><?php echo gDsc($longTermLia[$x], $mysqli); ?></td>
 <td><?php echo getMoneyLiabl($longTermLia[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
 <td><?php echo getMoneyLiabl($longTermLia[$x], $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo calcChangeLiabl($longTermLia[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo calcChangePercentLiabl($longTermLia[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearDayOne); ?>%</td>
 </tr>
<?php } ?>
<tr>
 <th style = "background-color: #000000;"></th>
 <th style = "background-color: #FCD299;">TOTAL LONG TERM LIABILITIES</th>
 <th style = "background-color: #FCD299;"><?php echo getTotalLiabl($longTermLia, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></th>
 <th style = "background-color: #FCD299;"><?php echo getTotalLiabl($longTermLia, $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></th>
 <th style = "background-color: #FCD299;"><?php echo getTotalChangeLiabl($longTermLia, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></th>
 <th style = "background-color: #FCD299;"><?php echo getTotalChangePercentLiabl($longTermLia, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</th>
</tr>
<tr>
 <th style = "background-color: #000000;"></th>
 <th style = "background-color: #FCD299;">TOTAL LIABILITIES</th>
 <th style = "background-color: #FCD299;"><?php echo getTotalLiabilities($currentLia, $longTermLia, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></th>
 <th style = "background-color: #FCD299;"><?php echo getTotalLiabilities($currentLia, $longTermLia, $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></th>
 <th style = "background-color: #FCD299;"><?php echo getTotalLiabilitiesChange($currentLia, $longTermLia, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></th>
 <th style = "background-color: #FCD299;"><?php echo getTotalLiabilitiesChangePercent($currentLia, $longTermLia, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</th>
</tr>
<tr>
 <th style = "background-color: #000000;"></th>
 <th style = "background-color: #FFFFFF;">Partners Capital</th>
 <th style = "background-color: #000000;"></th>
 <th style = "background-color: #000000;"></th>
 <th style = "background-color: #000000;"></th>
 <th style = "background-color: #000000;"></th>
</tr>
<?php for ($x = 0; $x < count($capital); $x++) { ?>
 <tr>
 <td><?php echo gAcc($capital[$x], $mysqli); ?></td>
 <td><?php echo gDsc($capital[$x], $mysqli); ?></td>
 <td><?php echo getMoneyLiabl($capital[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></td>
 <td><?php echo getMoneyLiabl($capital[$x], $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo calcChangeLiabl($capital[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></td>
 <td><?php echo calcChangePercentLiabl($capital[$x], $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearDayOne); ?>%</td>
 </tr>
<?php } ?>
<tr>
 <th style = "background-color: #000000;"></th>
 <th style = "background-color: #FCD299;">TOTAL PARTNERS CAPITAL</th>
 <th style = "background-color: #FCD299;"><?php echo getTotalLiabl($capital, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></th>
 <th style = "background-color: #FCD299;"><?php echo getTotalLiabl($capital, $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></th>
 <th style = "background-color: #FCD299;"><?php echo getTotalChangeLiabl($capital, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></th>
 <th style = "background-color: #FCD299;"><?php echo getTotalChangePercentLiabl($capital, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</th>
</tr>
<tr>
 <th style = "background-color: #000000;"></th>
 <th style = "background-color: #FFD1DC;">TOTAL LIABILITIES & EQUITY</th>
 <th style = "background-color: #FFD1DC;"><?php echo getTotalLiabilitiesEq($currentLia, $longTermLia, $capital, $mysqli, $thisYearDayOne, $thisYearPeriodEnd); ?></th>
 <th style = "background-color: #FFD1DC;"><?php echo getTotalLiabilitiesEq($currentLia, $longTermLia, $capital, $mysqli, $lastYearDayOne, $lastYearPeriodEnd); ?></th>
 <th style = "background-color: #FFD1DC;"><?php echo getTotalLiabilitiesEqChange($currentLia, $longTermLia, $capital, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?></th>
 <th style = "background-color: #FFD1DC;"><?php echo getTotalLiabilitiesEqChangePercent($currentLia, $longTermLia, $capital, $mysqli, $thisYearDayOne, $thisYearPeriodEnd, $lastYearDayOne, $lastYearPeriodEnd); ?>%</th>
</tr>
</table>
<?php $mysqli->close(); ?>
<?php  $_SESSION = []; ?>
</body>
</html>
