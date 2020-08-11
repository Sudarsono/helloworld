<?php

header('Content-Type: application/json');
$paths = explode('/', $_SERVER['REQUEST_URI']);

// http://192.168.64.2/helloworld/index.php/users
if("users" === $paths[3]) {
	$conn = connect();

	if("GET" === $_SERVER['REQUEST_METHOD']) {
		$records = select($conn, 'sudarsonosihotang');
		echo json_encode($records);
	} else if("PATCH" === $_SERVER['REQUEST_METHOD']) {
		$data = json_decode(file_get_contents('php://input'), true);
		update($conn, $data["id"], $data);
	} else if("POST" === $_SERVER['REQUEST_METHOD']) {
		$data = json_decode(file_get_contents('php://input'), true);
		insert($conn, $data);
	} else if("DELETE" === $_SERVER['REQUEST_METHOD']) {
		$data = json_decode(file_get_contents('php://input'), true);
		delete($conn, $data["id"]);
	}

	$conn->close();
}

function select($conn, $username) {
	$stmt = $conn->prepare("SELECT id, username, avatar_url FROM user WHERE username=?");
	$stmt->bind_param("s", $username);
	$stmt->execute();
	
	$result = $stmt->get_result();
	
	while ($row = $result->fetch_assoc()) {
		$records[] = $row;
	}

	$stmt->close();
	
	return $records;
}

function update($conn, $id, $data) {
	$stmt = $conn->prepare("UPDATE user SET avatar_url = ? WHERE id = ?");
	$stmt->bind_param("ss", $avatar, $id);

	$avatar = $data["avatar_url"];
	$stmt->execute();
	
	$stmt->close();
}

function delete($conn, $id) {
	$stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
	$stmt->bind_param("s", $id);

	$stmt->execute();
	
	$stmt->close();
}

function insert($conn, $data) {
	$stmt = $conn->prepare("INSERT INTO user (id, username, avatar_url) VALUES (?, ?, ?)");
	$stmt->bind_param("sss", $id, $username, $avatar);

	$id = uniqid('ID');
	$username = $data["username"];
	$avatar = $data["avatar_url"];

	$stmt->execute();
	$stmt->close();
}

function connect() {
	$servername = "192.168.1.7";
	$username = "potato";
	$password = "potato27";
	$dbname = "example";
	
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	
	// Check connection
	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error);
	}

	return $conn;
}
