<?php
require("functions.php");


$connect = get_connect_db();

if (!empty($_POST)) {

  $data = $_POST;
  $data["email"] = mysqli_real_escape_string($connect, $data["email"]);

  $required = ["email", "password"];
  $errors = [];

  // валидируем форму
  if (strlen($data["email"])) {
    $errors = validate_auth_form($connect, $required, $data);
  }


  // если ошибок нет, то записываем в сессию информацию о юзере
  if (empty($errors)) {

    $auth_user = get_auth_user($connect, $data);
    session_start();
    $_SESSION["user"] = $auth_user;
    header("Location: /");
    exit();
  }


}



// подключаем шаблоны
$title = "Дела в порядке: вход";

$content = include_template("auth.php", [
  "errors" => $errors,
  "user" => $data
]);

$layout = include_template("layout.php", [
  "content" => $content,
  "title" => $title
]);

echo $layout;

?>
