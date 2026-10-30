
<?php
header("Content-Type: application/json");

require "db.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method == "GET") {
    $result = $conn->query("SELECT * FROM pr13_students ORDER BY id DESC");

    $students = [];

    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    echo json_encode($students);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if ($method == "POST") {
    $name = trim($data["name"] ?? "");
    $email = trim($data["email"] ?? "");
    $course = trim($data["course"] ?? "");

    if ($name == "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || $course == "") {
        http_response_code(400);
        echo json_encode(["message" => "Please enter valid student details."]);
        exit;
    }

    $stmt = $conn->prepare(
        "INSERT INTO pr13_students (name, email, course) VALUES (?, ?, ?)"
    );

    $stmt->bind_param("sss", $name, $email, $course);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Student added successfully."]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Unable to add student."]);
    }

    exit;
}

if ($method == "PUT") {
    $id = intval($data["id"] ?? 0);
    $name = trim($data["name"] ?? "");
    $email = trim($data["email"] ?? "");
    $course = trim($data["course"] ?? "");

    if ($id <= 0 || $name == "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || $course == "") {
        http_response_code(400);
        echo json_encode(["message" => "Invalid student details."]);
        exit;
    }

    $stmt = $conn->prepare(
        "UPDATE pr13_students SET name=?, email=?, course=? WHERE id=?"
    );

    $stmt->bind_param("sssi", $name, $email, $course, $id);
    $stmt->execute();

    echo json_encode(["message" => "Student updated successfully."]);
    exit;
}

if ($method == "DELETE") {
    $id = intval($data["id"] ?? 0);

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid student ID."]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM pr13_students WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo json_encode(["message" => "Student deleted successfully."]);
    exit;
}

http_response_code(405);
echo json_encode(["message" => "Method not allowed."]);
?>
