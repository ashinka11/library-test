<?php
session_start();
require_once("includes/connection.php");

if (!isset($_SESSION["session_username"])) {
    header("location:login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'] ?? null;
$current_username = $_SESSION['session_username'];

// Получаем список всех пользователей
$users_query = mysqli_query($con, "SELECT id, username FROM users ORDER BY username");
$all_users = [];
while ($user = mysqli_fetch_assoc($users_query)) {
    $all_users[] = $user;
}

// Получаем пользователей, которым мы дали доступ
$access_given_query = mysqli_query($con, 
    "SELECT u.id, u.username FROM library_access la 
     JOIN users u ON la.user_id = u.id 
     WHERE la.owner_id = (SELECT id FROM users WHERE username = '$current_username') 
     ORDER BY u.username");
$access_given = [];
while ($user = mysqli_fetch_assoc($access_given_query)) {
    $access_given[] = $user;
}

// Получаем пользователей, которые дали нам доступ
$access_received_query = mysqli_query($con, 
    "SELECT u.id, u.username FROM library_access la 
     JOIN users u ON la.owner_id = u.id 
     WHERE la.user_id = (SELECT id FROM users WHERE username = '$current_username') 
     ORDER BY u.username");
$access_received = [];
while ($user = mysqli_fetch_assoc($access_received_query)) {
    $access_received[] = $user;
}

// Обработка предоставления доступа
if (isset($_POST['grant_access']) && isset($_POST['user_id'])) {
    $user_id_to_grant = intval($_POST['user_id']);
    
    // Получаем ID текущего пользователя
    $current_user_result = mysqli_query($con, "SELECT id FROM users WHERE username = '$current_username'");
    $current_user_row = mysqli_fetch_assoc($current_user_result);
    $current_user_id = $current_user_row['id'];
    
    // Проверяем, не давали ли уже доступ
    $check_query = mysqli_query($con, 
        "SELECT id FROM library_access WHERE owner_id = $current_user_id AND user_id = $user_id_to_grant");
    
    if (mysqli_num_rows($check_query) == 0) {
        mysqli_query($con, 
            "INSERT INTO library_access (owner_id, user_id) VALUES ($current_user_id, $user_id_to_grant)");
        header("Location: intropage.php");
        exit();
    }
}

// Обработка удаления доступа
if (isset($_GET['revoke']) && is_numeric($_GET['revoke'])) {
    $user_id_to_revoke = intval($_GET['revoke']);
    
    $current_user_result = mysqli_query($con, "SELECT id FROM users WHERE username = '$current_username'");
    $current_user_row = mysqli_fetch_assoc($current_user_result);
    $current_user_id = $current_user_row['id'];
    
    mysqli_query($con, 
        "DELETE FROM library_access WHERE owner_id = $current_user_id AND user_id = $user_id_to_revoke");
    header("Location: intropage.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Библиотека - Главная</title>
    <link href="style/style.css" rel="stylesheet">
</head>
<body>
    <div id="welcome">
        <h2>Список участников</h2>
        
        <div class="nav-links">
            <a href="book_list.php">Мои книги</a>
            <a href="search_book.php">Поиск книг</a>
            <a href="logout.php">Выйти</a>
        </div>
        
        <div class="users-container">
            <!-- Список всех пользователей -->
            <div class="users-list">
                <h3>Все зарегистрированные участники</h3>
                <?php if (count($all_users) > 0): ?>
                    <?php foreach ($all_users as $user): ?>
                        <?php if ($user['username'] != $current_username): ?>
                            <div class="user-item">
                                <div>
                                    <strong>ID: <?php echo $user['id']; ?></strong> - 
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </div>
                                <form method="post" class="grant-form" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="grant_access" class="grant-btn">
                                        Дать доступ
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Нет других пользователей</p>
                <?php endif; ?>
            </div>
            
            <!-- Пользователи, которым мы дали доступ -->
            <div class="users-list">
                <h3>Доступ предоставлен</h3>
                <?php if (count($access_given) > 0): ?>
                    <?php foreach ($access_given as $user): ?>
                        <div class="user-item">
                            <div>
                                <strong>ID: <?php echo $user['id']; ?></strong> - 
                                <?php echo htmlspecialchars($user['username']); ?>
                            </div>
                            <a href="?revoke=<?php echo $user['id']; ?>" class="revoke-btn" 
                               onclick="return confirm('Отозвать доступ у <?php echo htmlspecialchars($user['username']); ?>?')">
                                Отозвать
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Вы не предоставили доступ никому</p>
                <?php endif; ?>
            </div>
            
            <!-- Пользователи, которые дали нам доступ -->
            <div class="users-list">
                <h3>Доступ получен от</h3>
                <?php if (count($access_received) > 0): ?>
                    <?php foreach ($access_received as $user): ?>
                        <div class="user-item">
                            <div>
                                <strong>ID: <?php echo $user['id']; ?></strong> - 
                                <?php echo htmlspecialchars($user['username']); ?>
                            </div>
                            <a href="book_other.php?owner_id=<?php echo $user['id']; ?>" 
                               style="color: #e67e22; text-decoration: none;">Просмотреть книги</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Вам не предоставили доступ к библиотекам</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
