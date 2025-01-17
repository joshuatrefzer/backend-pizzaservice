<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:4200'); // Erlaubt das Frontend
header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); // Erlaubte HTTP-Methoden
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Erlaubte Header
header('Access-Control-Allow-Credentials: true'); // Falls Cookies oder Auth verwendet werden

// Preflight-Anfragen (OPTIONS) behandeln
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // Kein Inhalt, aber erfolgreich
    exit;
}