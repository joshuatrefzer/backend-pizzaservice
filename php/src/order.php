<?php

header('Content-Type: application/json');
require 'set_headers.php';
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $user_id = $input['user_id'] ?? null;
    $pizzas = $input['pizzas'] ?? []; 
    $total_price = 0;

    if (!$user_id || empty($pizzas)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Ungültige Eingabedaten. Benutzer-ID und Pizzen sind erforderlich.'
        ]);
        exit;
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
        $stmt->bind_param("id", $user_id, $total_price);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        foreach ($pizzas as $pizza) {
            $dough_name = $pizza['dough'] ?? null;
            $extra_wish = $pizza['extraWish'] ?? '';
            $toppings = $pizza['toppings'] ?? [];

            if (!$dough_name) {
                throw new Exception("Ungültiger Teigname.");
            }

            $stmt = $conn->prepare("SELECT id, price FROM pizza_dough WHERE name = ?");
            $stmt->bind_param("s", $dough_name);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $dough_data = $result->fetch_assoc();
                $dough_id = $dough_data['id'];
                $dough_price = $dough_data['price'];
            } else {
                throw new Exception("Teig '$dough_name' nicht gefunden.");
            }

            $stmt = $conn->prepare("INSERT INTO order_items (order_id, dough_id, extra_wish) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $order_id, $dough_id, $extra_wish);
            $stmt->execute();
            $order_item_id = $stmt->insert_id;

            foreach ($toppings as $topping) {
                $ingredient_id = $topping['id'] ?? null;
                $selected = $topping['selected'] ?? false;

                if (!$ingredient_id || !$selected) {
                    continue;
                }

                $stmt = $conn->prepare("SELECT price FROM ingredients WHERE id = ?");
                $stmt->bind_param("i", $ingredient_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $ingredient_price = $result->fetch_assoc()['price'];
                } else {
                    throw new Exception("Zutat mit ID $ingredient_id nicht gefunden.");
                }

                $stmt = $conn->prepare("INSERT INTO order_item_toppings (order_item_id, ingredient_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $order_item_id, $ingredient_id);
                $stmt->execute();

                $total_price += $ingredient_price;
            }

            $total_price += $dough_price; 
        }

        $stmt = $conn->prepare("UPDATE orders SET total_price = ? WHERE id = ?");
        $stmt->bind_param("di", $total_price, $order_id);
        $stmt->execute();

        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Bestellung erfolgreich aufgegeben.',
            'order_id' => $order_id,
            'total_price' => $total_price
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    } finally {
        $stmt->close();
        $conn->close();
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Ungültige Anfrage. Nur POST-Anfragen sind erlaubt.'
    ]);
}
?>
