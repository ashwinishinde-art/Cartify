<?php
// Products/save-shipping-session.php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['checkout_shipping'] = [
        'name' => $_POST['name'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address']
    ];
    echo json_encode(['status' => 'success']);
}