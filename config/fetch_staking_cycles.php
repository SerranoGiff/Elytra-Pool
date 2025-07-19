<?php
require 'dbcon.php';

$query = "SELECT id, currency, min_amount, daily_percent FROM staking_cycles";
$result = $conn->query($query);

$cycles = [];
while ($row = $result->fetch_assoc()) {
    $cycles[] = $row;
}

echo json_encode($cycles);
