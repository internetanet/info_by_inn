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
      <h3>Регистрация</h3>
      <a href="auth.php">вход</a>

      <form method="POST">
        <label for="name">Имя:</label>
        <input id="name" name="name" type="text">
        <label for="email">Email:</label>
        <input id="email" name="email" type="text">
        <label for="phone">Телефон:</label>
        <input id="phone" name="phone" type="text">
        <input type="submit" name="submit" value="Далее">
      </form>

    </body>
  </html>';
if (isset($_GET['message'])) {
    $param = $_GET['param'];
    $message = $_GET['message'];

    notification($message, $param);
  }
if (isset($_POST['submit'])) {

  if(empty($_POST['name']) || empty($_POST['email']) || empty($_POST['phone'])) { //Регистрация. Если поля пустые - возвращаемся
    
    $param = 2;
    $message = 'Пожалуйста, заполните все поля!';
    notification($message, $param);
    echo $page;
  }
  else{ //Регистрируем пользователя

    $name = (isset($_POST['name'])) ? htmlspecialchars(trim($_POST['name'])) : ''; //имя
    $email = (isset($_POST['email'])) ? htmlspecialchars(trim($_POST['email'])) : ''; //почта
    $phone = (isset($_POST['phone'])) ? htmlspecialchars(trim($_POST['phone'])) : ''; //телефон
    $userpass = generate_pass(6); //сгенерировали пароль для входа

        $stmt = $connect->prepare('SELECT phone FROM users WHERE phone = :phone');
        $stmt->execute(array(':phone' => $phone));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $from_base_phone = $row['phone']; //пароль из базы

        if ($phone !== $from_base_phone) { //сравниваем номера телефонов
              $stmt = $connect->prepare("INSERT INTO users (name, email, phone, userpass) VALUES (:name, :email, :phone, :userpass)"); //Запись в базу нового пользователя
              $stmt->bindParam(':name', $name);
              $stmt->bindParam(':email', $email);
              $stmt->bindParam(':phone', $phone);
              $stmt->bindParam(':userpass', $userpass);
              $stmt->execute();

          ///////////////////////////////////Отправка СМС
              $login = 'development-w@ya.ru';  
              $key   = 'PQx9oyV3HlL6NVsUeoeLRXW681K';  
              $phone = (isset($_POST['phone'])) ? htmlspecialchars(trim($_POST['phone'])) : ''; //телефон 
              $text  = 'Ваш пароль: ' . $userpass;// сгенерированый пароль
               
              $phone = preg_replace('/[^0-9]/', '', $phone);
               
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HEADER, 0);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
              curl_setopt($ch, CURLOPT_USERPWD, $login . ':' . $key);
              curl_setopt($ch, CURLOPT_URL, 'https://gate.smsaero.ru/v2/sms/send?number=' . $phone . '&text=' . $text . '&sign=SMS Aero');
              $res = curl_exec($ch);
              curl_close($ch);
                  $new_url = 'auth.php?param=1&message=Вы зарегистрированы! Код для входа отправлен на ваш номер телефона.';

                  header('Location: '.$new_url);
        }
        else{
          $new_url = 'index.php?param=0&message=Ошибка! Вы уже зарегистрированы.';
          header('Location: '.$new_url);
        }
  }
}
else{
  echo $page;
}
?>
