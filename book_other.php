<?php
session_start();
require_once("includes/connection.php");

if (!isset($_SESSION["session_username"])) {
    header("location:login.php");
    exit();
}

$current_username = $_SESSION['session_username'];
$current_user_id = 0;

// Получаем ID текущего пользователя
$user_result = mysqli_query($con, "SELECT id FROM users WHERE username = '$current_username'");
if ($user_row = mysqli_fetch_assoc($user_result)) {
    $current_user_id = $user_row['id'];
}

$owner_id = isset($_GET['owner_id']) ? intval($_GET['owner_id']) : 0;
$owner_name = "";
$books = [];
$access_denied = false;

if ($owner_id > 0) {
    // Получаем имя владельца библиотеки
    $owner_result = mysqli_query($con, "SELECT username FROM users WHERE id = $owner_id");
    if ($owner_row = mysqli_fetch_assoc($owner_result)) {
        $owner_name = $owner_row['username'];
        
        // Проверяем, есть ли у нас доступ к библиотеке этого пользователя
        $access_check = mysqli_query($con, 
            "SELECT id FROM library_access 
             WHERE owner_id = $owner_id AND user_id = $current_user_id");
        
        if (mysqli_num_rows($access_check) > 0) {
            // Получаем книги владельца
            $books_query = mysqli_query($con, 
                "SELECT id, title FROM books 
                 WHERE user_id = $owner_id AND deleted_at IS NULL 
                 ORDER BY id DESC");
            while ($book = mysqli_fetch_assoc($books_query)) {
                $books[] = $book;
            }
        } else {
            $access_denied = true;
        }
    }
}

// Просмотр конкретной книги
$book_to_view = null;
if (isset($_GET['view']) && $owner_id > 0 && !$access_denied) {
    $book_id = intval($_GET['view']);
    $result = mysqli_query($con, 
        "SELECT title, text FROM books 
         WHERE id = $book_id AND user_id = $owner_id AND deleted_at IS NULL");
    $book_to_view = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Библиотека другого пользователя</title>
    <link href="style/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php if ($owner_id == 0): ?>
            <div class="error">
                <h2>Ошибка</h2>
                <p>Не указан пользователь. Вернитесь на главную страницу и выберите пользователя из списка "Доступ получен от".</p>
            </div>
        
        <?php elseif ($access_denied): ?>
            <div class="error">
                <h2>Доступ запрещен</h2>
                <p>У вас нет доступа к библиотеке пользователя <strong><?php echo htmlspecialchars($owner_name); ?></strong>.</p>
                <p>Попросите пользователя предоставить вам доступ к своей библиотеке.</p>
            </div>
        
        <?php elseif ($book_to_view): ?>
            <!-- Показать книгу -->
            <h1>Книга: <?php echo htmlspecialchars($book_to_view['title']); ?></h1>
            <p><strong>Владелец:</strong> <?php echo htmlspecialchars($owner_name); ?></p>
            <div class="book-text">
                <?php echo nl2br(htmlspecialchars($book_to_view['text'])); ?>
            </div>
            <a href="book_other.php?owner_id=<?php echo $owner_id; ?>" class="btn back-btn">К списку книг</a>
        
        <?php else: ?>
            <!-- Список книг другого пользователя -->
            <h1>Библиотека пользователя: <?php echo htmlspecialchars($owner_name); ?></h1>
            
            <div class="book-list">
                <h2>Книги (<?php echo count($books); ?>)</h2>
                
                <?php if (count($books) > 0): ?>
                    <?php foreach ($books as $book): ?>
                        <div class="book-item">
                            <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                            <div class="book-id">ID: <?php echo $book['id']; ?></div>
                            <div class="actions">
                                <a href="?owner_id=<?php echo $owner_id; ?>&view=<?php echo $book['id']; ?>" class="btn view-btn">Читать</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>У пользователя пока нет книг в библиотеке.</p>
                <?php endif; ?>
            </div>
            
            <a href="intropage.php" class="btn back-btn">Назад к списку пользователей</a>
        <?php endif; ?>
    </div>
</body>
</html>