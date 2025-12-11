<?php 
require_once("includes/connection.php");

if (isset($_POST["register"])) {
    
    if (!empty($_POST['full_name']) && !empty($_POST['email']) && 
        !empty($_POST['username']) && !empty($_POST['password']) && 
        !empty($_POST['confirm_password'])) {
        
        $full_name = htmlspecialchars(trim($_POST['full_name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $username = htmlspecialchars(trim($_POST['username']));
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($password !== $confirm_password) {
            $message = "Пароли не совпадают!";
        } 
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Некорректный формат email!";
        }
        else {
            $query = mysqli_query($con, "SELECT * FROM users WHERE username='" . mysqli_real_escape_string($con, $username) . "'");
            
            if (mysqli_num_rows($query) == 0) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $full_name_escaped = mysqli_real_escape_string($con, $full_name);
                $email_escaped = mysqli_real_escape_string($con, $email);
                $username_escaped = mysqli_real_escape_string($con, $username);
                
                $sql = "INSERT INTO users(full_name, email, username, password) 
                        VALUES('$full_name_escaped', '$email_escaped', '$username_escaped', '$hashed_password')";
                
                $result = mysqli_query($con, $sql);
                
                if ($result) {
                    $_SESSION['session_username'] = $username;
                    $_SESSION['session_full_name'] = $full_name;
                    
                    header("Location: intropage.php");
                    exit();
                } else {
                    $message = "Ошибка при создании учетной записи: " . mysqli_error($con);
                }
            } else {
                $message = "Пользователь с таким логином уже существует!";
            }
        }
    } else {
        $message = "Все поля обязательны для заполнения!";
    }
}
?>

<?php if (isset($message) && !empty($message)) { 
    echo "<p class='error'>" . htmlspecialchars($message) . "</p>"; 
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<title>Регистрация</title>
<link href="style/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mregister">
        <div id="login">
            <h1>Регистрация</h1>
            <form action="register.php" id="registerform" method="post" name="registerform">
                <p>
                    <label for="full_name">Полное имя<br>
                        <input class="input" id="full_name" name="full_name" size="32" type="text" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>
                    </label>
                </p>
                <p>
                    <label for="email">E-mail<br>
                        <input class="input" id="email" name="email" size="32" type="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </label>
                </p>
                <p>
                    <label for="username">Имя пользователя<br>
                        <input class="input" id="username" name="username" size="20" type="text" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    </label>
                </p>
                <p>
                    <label for="password">Пароль<br>
                        <input class="input" id="password" name="password" size="32" type="password" required>
                    </label>
                </p>
                <p>
                    <label for="confirm_password">Подтверждение пароля<br>
                        <input class="input" id="confirm_password" name="confirm_password" size="32" type="password" required>
                    </label>
                </p>
                
                <p class="submit">
                    <input class="button" id="register" name="register" type="submit" value="Зарегистрироваться">
                </p>
                <p class="regtext">Уже зарегистрированы? <a href="login.php">Войти</a></p>
            </form>
        </div>
    </div>
    <footer></footer>
</body>
</html>