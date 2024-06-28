<?php
include 'config.php';

if (!empty($_POST['delete_roll_no'])) {
    $rollNo = $_POST['delete_roll_no'];

    $sql = "DELETE FROM trial_info WHERE roll_no='$rollNo'";
    if ($conn->query($sql) === FALSE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
header("Location: index.php");
exit();
?>
