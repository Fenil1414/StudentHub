
<!DOCTYPE html>
<html>
<head>
    <title>Student Management - Practical 13</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Student Management System</h1>
    <p>PHP JSON API and Fetch API CRUD Operations</p>

    <h2 id="formTitle">Add Student</h2>

    <form id="studentForm">
        <input type="hidden" id="studentId">

        <input type="text" id="name" placeholder="Student Name" required>

        <input type="email" id="email" placeholder="Email Address" required>

        <input type="text" id="course" placeholder="Course" required>

        <button type="submit" id="submitBtn">Add Student</button>
        <button type="button" id="cancelBtn" onclick="resetForm()" hidden>Cancel</button>
    </form>

    <p id="message"></p>

    <h2>Student Records</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="studentList"></tbody>
    </table>
</div>

<script>
const form = document.getElementById("studentForm");
const studentList = document.getElementById("studentList");
const message = document.getElementById("message");

async function loadStudents() {
    const response = await fetch("api.php");
    const students = await response.json();

    studentList.innerHTML = "";

    students.forEach(student => {
        const row = document.createElement("tr");

        [student.id, student.name, student.email, student.course].forEach(value => {
            const cell = document.createElement("td");
            cell.textContent = value;
            row.appendChild(cell);
        });

        const actions = document.createElement("td");

        const editButton = document.createElement("button");
        editButton.textContent = "Edit";
        editButton.onclick = () => editStudent(student);

        const deleteButton = document.createElement("button");
        deleteButton.textContent = "Delete";
        deleteButton.onclick = () => deleteStudent(student.id);

        actions.append(editButton, deleteButton);
        row.appendChild(actions);

        studentList.appendChild(row);
    });
}

form.addEventListener("submit", async function(event) {
    event.preventDefault();

    const id = document.getElementById("studentId").value;

    const student = {
        name: document.getElementById("name").value.trim(),
        email: document.getElementById("email").value.trim(),
        course: document.getElementById("course").value.trim()
    };

    const response = await fetch("api.php", {
        method: id ? "PUT" : "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(id ? {...student, id: id} : student)
    });

    const result = await response.json();

    message.textContent = result.message;

    if (response.ok) {
        resetForm();
        loadStudents();
    }
});

function editStudent(student) {
    document.getElementById("studentId").value = student.id;
    document.getElementById("name").value = student.name;
    document.getElementById("email").value = student.email;
    document.getElementById("course").value = student.course;

    document.getElementById("formTitle").textContent = "Edit Student";
    document.getElementById("submitBtn").textContent = "Update Student";
    document.getElementById("cancelBtn").hidden = false;
}

async function deleteStudent(id) {
    if (!confirm("Are you sure you want to delete this student?")) {
        return;
    }

    const response = await fetch("api.php", {
        method: "DELETE",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({id: id})
    });

    const result = await response.json();
    message.textContent = result.message;

    if (response.ok) {
        loadStudents();
    }
}

function resetForm() {
    form.reset();
    document.getElementById("studentId").value = "";
    document.getElementById("formTitle").textContent = "Add Student";
    document.getElementById("submitBtn").textContent = "Add Student";
    document.getElementById("cancelBtn").hidden = true;
}

loadStudents();
</script>

</body>
</html>
