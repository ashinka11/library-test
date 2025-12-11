<?php
session_start();
require_once("includes/connection.php");

if (!isset($_SESSION["session_username"])) {
    header("location:login.php");
    exit();
}

$current_username = $_SESSION['session_username'];

// ID текущего пользователя
$user_result = mysqli_query($con, "SELECT id FROM users WHERE username = '$current_username'");
$user_row = mysqli_fetch_assoc($user_result);
$user_id = $user_row['id'];

$search_results = [];
$message = "";

// Поиск книги
if (isset($_POST['search_books'])) {
    $search_query = trim($_POST['search_query']);
    
    if (!empty($search_query)) {
        $encoded_query = urlencode($search_query);
        
        // Пробуем Google Books API
        $google_url = "https://www.googleapis.com/books/v1/volumes?q=" . $encoded_query;
        $google_response = @file_get_contents($google_url);
        
        if ($google_response) {
            $data = json_decode($google_response, true);
            
            if (isset($data['items'])) {
                foreach ($data['items'] as $item) {
                    $book_info = $item['volumeInfo'];
                    
                    $search_results[] = [
                        'source' => 'google',
                        'id' => $item['id'],
                        'title' => $book_info['title'] ?? 'Без названия',
                        'authors' => isset($book_info['authors']) ? implode(', ', $book_info['authors']) : 'Неизвестен',
                        'description' => $book_info['description'] ?? 'Нет описания',
                        'url' => $book_info['infoLink'] ?? '#'
                    ];
                }
            }
        }
        
        // Если Google не нашел, пробуем Mann-Ivanov-Ferber
        if (empty($search_results)) {
            $mif_url = "https://www.mann-ivanov-ferber.ru/book/search.ajax?q=" . $encoded_query;
            $mif_response = @file_get_contents($mif_url);
            
            if ($mif_response) {
                $data = json_decode($mif_response, true);
                
                if (isset($data['books']) && count($data['books']) > 0) {
                    foreach ($data['books'] as $book) {
                        $search_results[] = [
                            'source' => 'mif',
                            'id' => $book['id'] ?? rand(1000, 9999),
                            'title' => $book['title'] ?? 'Без названия',
                            'authors' => $book['author'] ?? 'Неизвестен',
                            'description' => $book['annotation'] ?? 'Нет описания',
                            'url' => $book['url'] ?? '#'
                        ];
                    }
                }
            }
        }
        
        if (empty($search_results)) {
            $message = "Книги по запросу '$search_query' не найдены";
        }
    } else {
        $message = "Введите поисковый запрос";
    }
}

// Сохранение найденной книги
if (isset($_POST['save_book'])) {
    $book_id = $_POST['book_id'];
    $title = mysqli_real_escape_string($con, $_POST['book_title']);
    $description = mysqli_real_escape_string($con, $_POST['book_description']);
    $url = mysqli_real_escape_string($con, $_POST['book_url']);
    
    // Используем описание или URL как текст книги
    $book_text = !empty($description) ? $description : "Ссылка на книгу: " . $url;
    
    // Сохраняем книгу в базу данных
    mysqli_query($con, 
        "INSERT INTO books (user_id, title, text) 
         VALUES ($user_id, '$title', '$book_text')");
    
    $message = "Книга '$title' сохранена в вашу библиотеку!";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Поиск книг</title>
    <link href="style/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <a href="intropage.php" class="btn back-btn">Назад в библиотеку</a>
        <h1>Поиск существующих книг</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'сохранена') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Форма поиска -->
        <div class="search-form">
            <h2>Поиск книг</h2>
            <form method="post">
                <div class="form-group">
                    <label>Введите название книги или автора:</label>
                    <input type="text" name="search_query" 
                           value="<?php echo isset($_POST['search_query']) ? htmlspecialchars($_POST['search_query']) : ''; ?>" 
                           placeholder="Например: Гарри Поттер" required>
                </div>
                <button type="submit" name="search_books" class="btn search-btn">Искать книги</button>
            </form>
        </div>
        
        <!-- Результаты поиска -->
        <?php if (!empty($search_results)): ?>
            <h2>Найдено книг: <?php echo count($search_results); ?></h2>
            
            <?php foreach ($search_results as $book): ?>
                <div class="book-item">
                    <div class="book-title">
                        <?php echo htmlspecialchars($book['title']); ?>
                        <span class="source-badge <?php echo $book['source'] == 'google' ? 'google-badge' : 'mif-badge'; ?>">
                            <?php echo $book['source'] == 'google' ? 'Google Books' : 'MIF'; ?>
                        </span>
                    </div>
                    
                    <div class="book-link-container">
                        <strong>Ссылка:</strong>
                        <a href="<?php echo htmlspecialchars($book['url']); ?>" target="_blank" class="book-url">
                            <?php echo htmlspecialchars($book['url']); ?>
                        </a>
                    </div>
                    
                    <?php if (!empty($book['description'])): ?>
                        <div class="book-description">
                            <strong>Описание:</strong> 
                            <?php 
                            $desc = $book['description'];
                            echo strlen($desc) > 200 ? htmlspecialchars(substr($desc, 0, 200)) . '...' : htmlspecialchars($desc);
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="book-info">
                        <strong>Ссылка:</strong> 
                        <a href="<?php echo htmlspecialchars($book['url']); ?>" target="_blank">
                            <?php echo htmlspecialchars($book['url']); ?>
                        </a>
                    </div>
                    
                    <!-- Форма для сохранения книги -->
                    <form method="post" style="margin-top: 10px;">
                        <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['id']); ?>">
                        <input type="hidden" name="book_title" value="<?php echo htmlspecialchars($book['title']); ?>">
                        <input type="hidden" name="book_description" value="<?php echo htmlspecialchars($book['description']); ?>">
                        <input type="hidden" name="book_url" value="<?php echo htmlspecialchars($book['url']); ?>">
                        
                        <button type="submit" name="save_book" class="btn save-btn">Сохранить в мою библиотеку</button>
                    </form>
                </div>
            <?php endforeach; ?>
            
        <?php elseif (isset($_POST['search_books'])): ?>
            <div class="message error">
                Ничего не найдено. Попробуйте другой запрос.
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px;">
            <a href="book_list.php" class="btn back-btn">Посмотреть мои книги</a>
        </div>
    </div>
</body>
</html>