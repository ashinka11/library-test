<?php 
session_start();
require_once("includes/connection.php");

if (isset($_SESSION["session_username"])) {
    header("Location: intropage.php");
    exit();
}

$message = "";

if (isset($_POST["login"])) {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = htmlspecialchars(trim($_POST['username']));
        $password = $_POST['password'];

        $username_escaped = mysqli_real_escape_string($con, $username);
        // Запрос на поиск юзера
        $query = mysqli_query($con, "SELECT * FROM users WHERE username='$username_escaped'");
        
        if (mysqli_num_rows($query) != 0) {
            // Пользователь найден - проверяем пароль
            $row = mysqli_fetch_assoc($query);
            $dbpassword = $row['password'];
            
            if (password_verify($password, $dbpassword)) {
                $_SESSION['session_username'] = $row['username'];
                $_SESSION['session_full_name'] = $row['full_name'];
                header("Location: intropage.php");
                exit();
            } else {
                $message = "Неверный пароль!";
            }
        } else {
            // Неизвестный пользователь - добавление в бд
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password) VALUES ('$username_escaped', '$hashed_password')";
            
            if (mysqli_query($con, $sql)) {
                $_SESSION['session_username'] = $username;
                $_SESSION['session_full_name'] = $username;
                header("Location: intropage.php");
                exit();
            } else {
                $message = "Ошибка создания пользователя";
            }
        }
    } else {
        $message = "Заполните все поля";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Вход</title>
<link href="style/style.css" rel="stylesheet">
</head> 
<body>
    <div class="container mlogin">
        <?php if (!empty($message)): ?>
            <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        
        <div id="login">
            <h1>Вход</h1>
            <form action="" method="post">
                <p>
                    <label>Имя пользователя<br>
                        <input class="input" name="username" type="text" required autofocus>
                    </label>
                </p>
                <p>
                    <label>Пароль<br>
                        <input class="input" name="password" type="password" required>
                    </label>
                </p>
                <p class="submit">
                    <input class="button" name="login" type="submit" value="Войти">
                </p>
            </form>
        </div>
        <p class="regtext">Еще нет аккаунта? <a href="register.php">Регистрация</a></p>
    </div>
</body>
</html>