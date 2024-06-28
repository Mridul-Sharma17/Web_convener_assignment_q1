<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $roll_no = $_POST['roll_no'];
    $department = $_POST['department'];
    $hostel = $_POST['hostel'];
    $old_roll_no = $_POST['old_roll_no'];

    if ($name && $roll_no && $department && $hostel) {
        $stmt = $conn->prepare("UPDATE trial_info SET name=?, roll_no=?, department=?, hostel=? WHERE roll_no=?");
        $stmt->bind_param("sssss", $name, $roll_no, $department, $hostel, $old_roll_no);

        if ($stmt->execute()) {
            $response = [
                'success' => true,
                'message' => 'Student updated successfully.'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Error updating student: ' . $stmt->error
            ];
        }
        $stmt->close();
    } else {
        $response = [
            'success' => false,
            'message' => 'All fields are required.'
        ];
    }
} else {
    $response = [
        'success' => false,
        'message' => 'Invalid request method.'
    ];
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
