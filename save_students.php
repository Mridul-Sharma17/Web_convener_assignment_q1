<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['new_name'], $_POST['new_roll_no'], $_POST['new_department'], $_POST['new_hostel'])) {
        $new_names = $_POST['new_name'];
        $new_roll_nos = $_POST['new_roll_no'];
        $new_departments = $_POST['new_department'];
        $new_hostels = $_POST['new_hostel'];

        for ($i = 0; $i < count($new_names); $i++) {
            $name = $new_names[$i];
            $roll_no = $new_roll_nos[$i];
            $department = $new_departments[$i];
            $hostel = $new_hostels[$i];

            if ($name && $roll_no && $department && $hostel) {
                $stmt = $conn->prepare("INSERT INTO trial_info (name, roll_no, department, hostel) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $roll_no, $department, $hostel);

                if (!$stmt->execute()) {
                    die("Error: " . $stmt->error);
                }
                $stmt->close();
            } else {
                die("All fields are required.");
            }
        }
    }
}

$conn->close();

header("Location: index.php");
exit();
?>
