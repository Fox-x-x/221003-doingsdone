<?php
require("functions.php");


$connect = mysqli_connect("localhost", "root", "root", "doingsdone");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $user = $_POST;
  $user["email"] = mysqli_real_escape_string($connect, $_POST["email"]);

  $required = ["email", "password"];
  $errors = [];

  // валидируем форму
  if (strlen($user["email"])) {
    $errors = validate_auth_form($connect, $required, $user);
  }


  // если ошибок нет, то записываем в сессию информацию о юзере
  if (empty($errors)) {

    $auth_user = get_auth_user($connect, $user);
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
  "user" => $user
]);

$layout = include_template("layout.php", [
  "content" => $content,
  "title" => $title
]);

echo $layout;

?>
