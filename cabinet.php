<?php
      header('Content-Type: text/html; charset = utf-8');
      error_reporting(E_ALL);
      mb_internal_encoding("UTF-8");
      require_once 'scripts/functions.php'; //грузим функции (вывод уведомлений)
      require_once 'scripts/db.php'; //подключение к базе данных
      require_once 'vendor/autoload.php';//загрузка библиотек (composer)
?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <title>Какой-то сервис</title>
    <link rel="stylesheet" type="text/css" href="style.css">
  </head>
  <body>
    <h3>Личный кабинет</h3>
    <h4>Информация о пользователе по Email:</h4>
    <form method="POST">
      <label for="email">Email:</label>
      <input id="email" name="email" type="email">
      <input type="submit" name="submit" value="Далее">
    </form>
<?php
      if (isset($_POST['submit'])) {
        $email_from_input = (isset($_POST['email'])) ? htmlspecialchars(trim($_POST['email'])) : ''; //берем почту с фронта

        $stmt = $connect->prepare("SELECT * FROM users WHERE email = ?"); //ищем в базе совпадения по email
        $stmt->execute([$email_from_input]);
        while ($row = $stmt->fetch(PDO::FETCH_LAZY)) {
            echo 'id: '.$row->id.'<br>'.//выводим данные по найденному email
                 'Имя: '.$row->name.'<br>'.
                 'Почта: '.$row->email.'<br>'.
                 'Телефон: '.$row->phone;
        }
      }
?>
    <h4>Информация об организации по ИНН:</h4>
    <form method="POST">
      <label for="inn">ИНН:</label>
      <input id="inn" name="inn" type="number">
      <input type="submit" name="submit2" value="Далее">
    </form>
  </body>
</html>

<?php //запрос в dadata.ru (поиск по ИНН)
  if (isset($_POST['submit2'])) {
        $query = (isset($_POST['inn'])) ? htmlspecialchars(trim($_POST['inn'])) : ''; //телефон из формы авторизации
        $token = $stringToken;
        $dadata = new \Dadata\DadataClient($token, null);
        $result = $dadata->findById("party", $query, 1);
        echo $result[0]['value'].'<br>'.$result[0]['data']['address']['value'];
      }
?>
