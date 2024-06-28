<?php
include 'config.php';

$sql = "SELECT name, roll_no, department, hostel FROM trial_info";
$result = $conn->query($sql);

if ($result === FALSE) {
    die("Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information</title>
    
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('https://image.shutterstock.com/image-photo/diverse-group-students-collaborating-on-260nw-2458397457.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }
        .container {
            margin-top: 50px;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 10px;
        }
        .table-container {
            margin-top: 20px;
        }
        .button-container {
            margin-top: 20px;
            text-align: center;
        }
        .form-inline, .table-container, .button-container {
            color: #fff;
        }
        .table {
            background-color: rgba(255, 255, 255, 0.9);
        }
        .table th, .table td {
            color: #000;
        }
        .btn {
            margin-right: 10px;
        }
        .btn.add {
            background-color: #28a745;
            color: white;
        }
        .btn.update {
            background-color: #ffc107;
            color: black;
        }
        .btn.delete {
            background-color: #dc3545;
            color: white;
        }
        .btn.cancel {
            background-color: #6c757d;
            color: white;
        }
        .actions-cell {
            display: flex;
            gap: 5px;
        }
        .actions-cell .btn {
            flex: 1;
        }
        @media (max-width: 768px) {
            .form-inline label {
                margin-bottom: 10px;
            }
            .form-inline {
                flex-direction: column;
                align-items: flex-start;
                margin-top: 10px; /* Added margin top for the form inline */
            }
            .table-responsive {
                overflow-x: auto;
            }
            .form-inline .form-group {
                margin-bottom: 10px; /* Margin between search filter and input */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Student Information</h1>
        <div class="form-inline mt-3"> 
            <label for="search_column" class="mr-2">Search by:</label>
            <select id="search_column" class="form-control mr-2">
                <option value="name">Name</option>
                <option value="roll_no">Roll No.</option>
                <option value="department">Department</option>
                <option value="hostel">Hostel</option>
            </select>
            <input type="text" id="search_query" class="form-control" placeholder="Type to search..." />
        </div>
        <div class="table-container table-responsive">
            <form id="student_form" action="save_students.php" method="post">
                <table class="table table-striped table-bordered mt-3">
                    <thead class="thead-dark">
                        <tr>
                            <th>Name</th>
                            <th>Roll No.</th>
                            <th>Department</th>
                            <th>Hostel</th>
                            <th>Actions</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody id="student_table_body">
                        <?php
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr id='row-" . htmlspecialchars($row["roll_no"]) . "'>
                                        <td data-name='name'>" . htmlspecialchars($row["name"]) . "</td>
                                        <td data-name='roll_no'>" . htmlspecialchars($row["roll_no"]) . "</td>
                                        <td data-name='department'>" . htmlspecialchars($row["department"]) . "</td>
                                        <td data-name='hostel'>" . htmlspecialchars($row["hostel"]) . "</td>
                                        <td class='actions-cell'>
                                            <button type='button' class='btn update' onclick=\"enableEdit('" . htmlspecialchars($row['roll_no']) . "')\">Update</button>
                                        </td>
                                        <td>
                                            <button type='button' class='btn delete' onclick=\"confirmDelete('" . htmlspecialchars($row['name']) . "', '" . htmlspecialchars($row['roll_no']) . "')\">Delete</button>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No records found</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
                <div class="button-container">
                    <button id="add_student_btn" class="btn add" type="button" onclick="addNewStudent()">Add New Student</button>
                    <button id="save_students_btn" class="btn btn-primary" type="submit" style="display: none;">Save All Students</button>
                </div>
            </form>
        </div>
        <form id="delete_form" action="delete_student.php" method="post">
            <input type="hidden" id="delete_roll_no" name="delete_roll_no" value="">
        </form>
    </div>

    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        let newStudentCounter = 0;

        function enableEdit(rollNo) {.

            
            const row = document.getElementById('row-' + rollNo);
            const cells = row.getElementsByTagName('td');
            const originalValues = [];

            for (let i = 0; i < cells.length - 2; i++) {
                const cell = cells[i];
                const value = cell.innerText;
                originalValues.push(value);
                cell.innerHTML = `<input type="text" class="form-control" name="${cell.getAttribute('data-name')}" value="${value}" />`;
            }

            const actionCell = cells[cells.length - 2];
            actionCell.innerHTML = `<button type="button" class='btn btn-primary' onclick="saveEdit('${rollNo}')">Save</button>
                                    <button type="button" class='btn btn-secondary' onclick="cancelEdit('${rollNo}', ${JSON.stringify(originalValues)})">Cancel</button>
                                    <input type="hidden" name="old_roll_no" value="${rollNo}" />`;
        }

        function saveEdit(rollNo) {
            const row = document.getElementById('row-' + rollNo);
            const inputs = row.getElementsByTagName('input');
            const form = document.getElementById('student_form');

            let formData = new FormData();
            formData.append('old_roll_no', rollNo);

            for (let i = 0; i < inputs.length; i++) {
                const input = inputs[i];
                if (!input.value) {
                    alert('Please fill in all the fields');
                    return;
                }
                formData.append(input.name, input.value);
            }

            fetch('update_student.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();  
                } else {
                    alert(data.message);
                    cancelEdit(rollNo, data.originalValues); 
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function cancelEdit(rollNo, originalValues) {
            const row = document.getElementById('row-' + rollNo);
            const cells = row.getElementsByTagName('td');

            for (let i = 0; i < cells.length - 2; i++) {
                const cell = cells[i];
                cell.innerHTML = originalValues[i]; 
            }

            const actionCell = cells[cells.length - 2];
            actionCell.innerHTML = `<button type='button' class='btn update' onclick="enableEdit('${rollNo}')">Update</button>`;
        }

        function addNewStudent() {
            newStudentCounter++;
            const tableBody = document.querySelector('table tbody');
            const newRow = document.createElement('tr');
            newRow.id = `new-student-${newStudentCounter}`;
            newRow.innerHTML = `
                <td><input type="text" class="form-control" name="new_name[]" /></td>
                <td><input type="text" class="form-control" name="new_roll_no[]" /></td>
                <td><input type="text" class="form-control" name="new_department[]" /></td>
                <td><input type="text" class="form-control" name="new_hostel[]" /></td>
                <td class='actions-cell'>
                    <button type="button" class='btn cancel' onclick="cancelAdd(this)">Cancel</button>
                </td>
                <td></td>
            `;
            tableBody.appendChild(newRow);

            document.getElementById('save_students_btn').style.display = 'inline-block';
        }

        function cancelAdd(button) {
            const row = button.closest('tr');
            row.remove();

            const newRows = document.querySelectorAll('tr[id^="new-student-"]');
            if (newRows.length === 0) {
                document.getElementById('save_students_btn').style.display = 'none';
            }
        }

        function confirmDelete(name, rollNo) {
            if (confirm(`Are you sure you want to delete ${name} with roll no. ${rollNo}?`)) {
                document.getElementById('delete_roll_no').value = rollNo;
                document.getElementById('delete_form').submit();
            }
        }

        async function filterResults() {
            const searchColumn = document.getElementById('search_column').value;
            const searchQuery = document.getElementById('search_query').value;

            let url = 'search_students.php';

            
            if (searchQuery.trim() !== '') {
                url += `?search_column=${searchColumn}&search_query=${searchQuery}`;
            }

            try {
                const response = await fetch(url);
                const students = await response.json();

                const tableBody = document.getElementById('student_table_body');
                tableBody.innerHTML = '';

                if (students.length > 0) {
                    students.forEach(student => {
                        const row = document.createElement('tr');
                        row.id = `row-${student.roll_no}`;
                        row.innerHTML = `
                            <td data-name='name'>${student.name}</td>
                            <td data-name='roll_no'>${student.roll_no}</td>
                            <td data-name='department'>${student.department}</td>
                            <td data-name='hostel'>${student.hostel}</td>
                            <td class='actions-cell'>
                                <button type='button' class='btn update' onclick="enableEdit('${student.roll_no}')">Update</button>
                            </td>
                            <td>
                                <button type='button' class='btn delete' onclick="confirmDelete('${student.name}', '${student.roll_no}')">Delete</button>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                } else {
                    tableBody.innerHTML = '<tr><td colspan="6">No records found</td></tr>';
                }
            } catch (error) {
                console.error('Error fetching student data:', error);
            }
        }

        document.getElementById('search_query').addEventListener('input', filterResults);
    </script>
</body>
</html>
