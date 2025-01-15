<?php 

require 'set_headers.php';
require 'db_connection.php';

$sql = "SELECT id , name, price, image_url  FROM ingredients";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $ingredients = [];

    while($row = $result->fetch_assoc()) {
        $ingredients[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'image_url' => $row['image_url'],
        ];
    }

    $data = [
        'status' => 'success',
        'data' => $ingredients
    ];

echo json_encode($data);

} else {
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Keine Pizzen gefunden'
    ]);
}

$conn->close();
?>