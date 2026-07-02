<?php
$db_host = 'localhost';
$db_user = 'apipp';
$db_pass = '13211321';
$db_name = 'apipp';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check for user with ID or userCode 340867 (from logs)
$search = '340867';
$sql = "SELECT * FROM users WHERE id = '$search' OR userCode = '$search' OR aasUserCode = '$search' OR userCode LIKE '%$search%'";
echo "Searching for user: $search\n";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "\nUser Found:\n";
        print_r($row);
        
        // Also check the agent for this user
        $agentCode = $row['agentCode'];
        $sqlAgent = "SELECT * FROM agents WHERE agentCode = '$agentCode'";
        $resultAgent = $conn->query($sqlAgent);
        echo "\nAgent Info:\n";
        while($rowAgent = $resultAgent->fetch_assoc()) {
            print_r($rowAgent);
        }
    }
} else {
    echo "0 results for $search\n";
    
    // List last 3 users to find a valid one
    echo "\nLast 3 users:\n";
    $sqlLast = "SELECT * FROM users ORDER BY id DESC LIMIT 3";
    $resultLast = $conn->query($sqlLast);
    while($rowLast = $resultLast->fetch_assoc()) {
        print_r($rowLast);
    }
}

$conn->close();
?>
