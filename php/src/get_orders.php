<?php

header('Content-Type: application/json');
require 'db_connection.php';

$sql = "
    SELECT 
        o.id AS order_id,
        o.user_id,
        o.order_date,
        o.total_price,
        oi.id AS order_item_id,
        oi.dough_id,
        oi.extra_wish,
        pd.name AS dough_name,
        pd.price AS dough_price,
        GROUP_CONCAT(DISTINCT CONCAT(i.name, '|', i.price, '|', i.image_url) ORDER BY i.id SEPARATOR ',') AS toppings
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN pizza_dough pd ON oi.dough_id = pd.id
    LEFT JOIN order_item_toppings oit ON oi.id = oit.order_item_id
    LEFT JOIN ingredients i ON oit.ingredient_id = i.id
    GROUP BY o.id, oi.id
    ORDER BY o.order_date DESC
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $toppings = [];
        if (!empty($row['toppings'])) {
            $topping_entries = explode(',', $row['toppings']);
            foreach ($topping_entries as $entry) {
                list($name, $price, $image_url) = explode('|', $entry);
                $toppings[] = [
                    'name' => $name,
                    'price' => (int) $price,
                    'image_url' => $image_url
                ];
            }
        }

        $orders[$row['order_id']]['order_id'] = $row['order_id'];
        $orders[$row['order_id']]['user_id'] = $row['user_id'];
        $orders[$row['order_id']]['order_date'] = $row['order_date'];
        $orders[$row['order_id']]['total_price'] = (int) $row['total_price'];
        $orders[$row['order_id']]['items'][] = [
            'order_item_id' => $row['order_item_id'],
            'dough' => [
                'id' => $row['dough_id'],
                'name' => $row['dough_name'],
                'price' => (int) $row['dough_price']
            ],
            'extra_wish' => $row['extra_wish'],
            'toppings' => $toppings
        ];
    }

    $orders = array_values($orders);

    echo json_encode([
        'status' => 'success',
        'data' => $orders
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Keine Bestellungen gefunden.'
    ]);
}

$conn->close();
?>
