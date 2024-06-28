<?php
include 'config.php';

$searchColumn = $_GET['search_column'] ?? null;
$searchQuery = $_GET['search_query'] ?? null;

if ($searchColumn && $searchQuery) {
    $stmt = $conn->prepare("SELECT name, roll_no, department, hostel FROM trial_info WHERE $searchColumn LIKE ?");
    $searchQuery = "%" . $searchQuery . "%";
    $stmt->bind_param("s", $searchQuery);
} else {
    $stmt = $conn->prepare("SELECT name, roll_no, department, hostel FROM trial_info");
}

$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode($students);

$stmt->close();
$conn->close();
?>
