<?php
session_start();
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: admin.html");
    exit;
}

require "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ai_name = $_POST['ai_name'] ?? '';
    $welcome_message = $_POST['welcome_message'] ?? '';

    $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    
    $stmt->bind_param("ss", $key, $val);

    $key = 'ai_name';
    $val = $ai_name;
    $stmt->execute();

    $key = 'welcome_message';
    $val = $welcome_message;
    $stmt->execute();

    $stmt->close();
    header("Location: dashboard.php?success=branding_updated");
    exit;
}
