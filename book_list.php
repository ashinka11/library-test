<?php
session_start();
require_once("includes/connection.php");

if (!isset($_SESSION["session_username"])) {
    header("location:login.php");
    exit();
}

$current_username = $_SESSION['session_username'];

// Получаем ID пользователя
$user_result = mysqli_query($con, "SELECT id FROM users WHERE username = '$current_username'");
$user_row = mysqli_fetch_assoc($user_result);
$user_id = $user_row['id'];

$message = "";
$books = [];

// Получить список книг
$query = mysqli_query($con, "SELECT id, title FROM books WHERE user_id = $user_id AND deleted_at IS NULL ORDER BY id DESC");
while ($book = mysqli_fetch_assoc($query)) {
    $books[] = $book;
}

// Создать книгу
if (isset($_POST['create_book'])) {
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $text = trim($_POST['text']);

    // Если текст в textarea пустой — пытаемся взять из файла
    if ($text === "" && isset($_FILES['book_file']) && $_FILES['book_file']['error'] === UPLOAD_ERR_OK) {

        $ext = strtolower(pathinfo($_FILES['book_file']['name'], PATHINFO_EXTENSION));

        if ($ext === "txt") {
            $file_content = file_get_contents($_FILES['book_file']['tmp_name']);
            if ($file_content !== false) {
                $text = $file_content;
            }
        }
    }
    $text = mysqli_real_escape_string($con, $text);

    if (!empty($title)) {
        mysqli_query($con, "INSERT INTO books (user_id, title, text) VALUES ($user_id, '$title', '$text')");
        $message = "Книга создана!";
        header("Location: book_list.php");
        exit();
    } else {
        $message = "Введите название книги";
    }
}


// Сохранить книгу
if (isset($_POST['save_book'])) {
    $book_id = intval($_POST['book_id']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $text = mysqli_real_escape_string($con, $_POST['text']);
    
    mysqli_query($con, "UPDATE books SET title = '$title', text = '$text' WHERE id = $book_id AND user_id = $user_id");
    $message = "Книга сохранена!";
    header("Location: book_list.php");
    exit();
}

// Удалить книгу
if (isset($_GET['delete'])) {
    $book_id = intval($_GET['delete']);
    mysqli_query($con, "UPDATE books SET deleted_at = NOW() WHERE id = $book_id AND user_id = $user_id");
    $message = "Книга удалена!";
    header("Location: book_list.php");
    exit();
}

// Открыть книгу
$book_to_view = null;
if (isset($_GET['view'])) {
    $book_id = intval($_GET['view']);
    $result = mysqli_query($con, "SELECT title, text FROM books WHERE id = $book_id AND user_id = $user_id");
    $book_to_view = mysqli_fetch_assoc($result);
}

// Редактирование книги
$book_to_edit = null;
if (isset($_GET['edit'])) {
    $book_id = intval($_GET['edit']);
    $result = mysqli_query($con, "SELECT id, title, text FROM books WHERE id = $book_id AND user_id = $user_id");
    $book_to_edit = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Мои книги</title>
    <link href="style/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <a href="intropage.php" class="btn back-btn">← Назад</a>
        <h1>Мои книги</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <!-- Показать книгу -->
        <?php if ($book_to_view): ?>
            <div class="book-text">
                <h2><?php echo htmlspecialchars($book_to_view['title']); ?></h2>
                <hr>
                <p><?php echo nl2br(htmlspecialchars($book_to_view['text'])); ?></p>
            </div>
            <a href="book_list.php" class="btn back-btn">Закрыть</a>
        
        <!-- Редактировать книгу -->
        <?php elseif ($book_to_edit): ?>
            <h2>Редактировать книгу</h2>
            <form method="post">
                <input type="hidden" name="book_id" value="<?php echo $book_to_edit['id']; ?>">
                <div class="form-group">
                    <label>Название книги:</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($book_to_edit['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Текст книги:</label>
                    <textarea name="text"><?php echo htmlspecialchars($book_to_edit['text']); ?></textarea>
                </div>
                <button type="submit" name="save_book" class="btn edit-btn">Сохранить</button>
                <a href="book_list.php" class="btn back-btn">Отмена</a>
            </form>
        
        <?php else: ?>
            <!-- Форма создания книги -->
            <h2>Создать новую книгу</h2>
            <form method="post">
                <div class="form-group">
                    <label>Название книги:</label>
                    <input type="text" name="title" required>
                </div>
                <div class="form-group">
                    <label>Текст книги:</label>
                    <textarea name="text" placeholder="Введите текст книги здесь..."></textarea>

                    <label>Загрузить текстовый файл (.txt):</label>
                    <input type="file" name="book_file" accept=".txt">
                </div>
                <button type="submit" name="create_book" class="btn" style="background: #e67e22;">Создать книгу</button>
            </form>
            
            <!-- Список книг -->
            <div class="book-list">
                <h2>Мои книги (<?php echo count($books); ?>)</h2>
                <?php if (count($books) > 0): ?>
                    <?php foreach ($books as $book): ?>
                        <div class="book-item">
                            <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                            <div class="book-id">ID: <?php echo $book['id']; ?></div>
                            <div class="actions">
                                <a href="?view=<?php echo $book['id']; ?>" class="btn view-btn">Открыть</a>
                                <a href="?edit=<?php echo $book['id']; ?>" class="btn edit-btn">Редактировать</a>
                                <a href="?delete=<?php echo $book['id']; ?>" class="btn delete-btn" 
                                   onclick="return confirm('Удалить книгу?')">Удалить</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>У вас пока нет книг. Создайте первую!</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>