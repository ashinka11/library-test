<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once("includes/connection.php");

if (!isset($_SESSION["session_username"])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$current_username = $_SESSION['session_username'];

// Получаем ID пользователя
$user_result = mysqli_query($con, "SELECT id FROM users WHERE username = '$current_username'");
$user_row = mysqli_fetch_assoc($user_result);
$user_id = $user_row['id'];

// Получаем список книг
$query = mysqli_query($con, "SELECT id, title, text FROM books WHERE user_id = $user_id AND deleted_at IS NULL ORDER BY id DESC");

$books = [];
while ($book = mysqli_fetch_assoc($query)) {
    $books[] = $book;
}

// JSON
echo json_encode($books, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
