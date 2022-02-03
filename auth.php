<?php
  header('Content-Type: text/html; charset = utf-8');
  error_reporting(E_ALL);
  mb_internal_encoding("UTF-8");

  include 'scripts/functions.php'; //грузим функции (вывод уведомлений)
  include 'scripts/db.php'; //подключение к базе данных
  $page = '<!DOCTYPE html>
            <html lang="ru">
              <head>
                <meta charset="utf-8">
                <title>Какой-то сервис</title>
                <link rel="stylesheet" type="text/css" href="style.css">
              </head>
              <body>
                <h3>Авторизация</h3>
                <a href="index.php">зарегистрироваться</a>
                <form method="POST" action="auth.php">
                  <label for="phone">Телефон:</label>
                  <input id="phone" name="phone" type="text">
                  <label for="pass">Код:</label>
                  <input id="pass" name="pass" type="password">
                  <input type="submit" name="submit" value="Далее">
                </form>

              </body>
            </html>';
  if (isset($_GET['message'])) {
    $param = $_GET['param'];
    $message = $_GET['message'];

    notification($message, $param);
    echo $page;
  }
  else{
    echo $page;
  }

  if (isset($_POST['submit'])) { //авторизация
    if(!empty($_POST['phone']) || !empty($_POST['pass'])) {
      $auth_phone = (isset($_POST['phone'])) ? htmlspecialchars(trim($_POST['phone'])) : ''; //телефон из формы авторизации
      $auth_pass = (isset($_POST['pass'])) ? htmlspecialchars(trim($_POST['pass'])) : ''; //пароль из формы авторизации

      $stmt = $connect->prepare('SELECT userpass FROM users WHERE phone = :phone');
      $stmt->execute(array(':phone' => $auth_phone));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $userpass = $row['userpass']; //пароль из базы

      if ($auth_pass == $userpass) { //сравниваем пароли
          header('Location: cabinet.php');
      }
      else{
        $new_url = 'auth.php?param=0&message=Ошибка!';
        header('Location: '.$new_url);
      }
    }
    else{
        $new_url = 'auth.php?param=0&message=Ошибка!';
        header('Location: '.$new_url);
      }
  }
  
?>