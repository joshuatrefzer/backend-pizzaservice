<?php 

require 'set_headers.php';
require 'db_connection.php';

$sql = "SELECT id, name, price, image_url FROM pizza_dough";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $pizza_doughs = [];

    while($row = $result->fetch_assoc()) {
        $pizza_doughs[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'image_url' => $row['image_url']
        ];
    }

    $data = [
        'status' => 'success',
        'data' => $pizza_doughs
    ];

    echo json_encode($data);

} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No pizza dough found'
    ]);
}

$conn->close();
?>
