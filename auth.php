<?php
require("functions.php");


$connect = get_connect_db();

if (!empty($_POST)) {

  $data = $_POST;
  $required = ["email", "password"];
  $errors = [];

  if ( !empty($data["email"]) ) {
    $data["email"] = mysqli_real_escape_string($connect, $data["email"]);
  } else {
    $errors["email"] = "Это поле нужно заполнить";
  }

  if ( empty($data["password"]) ) {
    $errors["password"] = "Это поле нужно заполнить";
  }


  // валидируем форму
  if (strlen($data["email"])) {
    $errors = validate_auth_form($connect, $required, $data);
  } else {
    $errors["email"] = "Это поле нужно заполнить";
  }


  // если ошибок нет, то записываем в сессию информацию о юзере
  if (empty($errors)) {

    $auth_user = get_auth_user($connect, $data);
    if ($auth_user) {
      session_start();
      $_SESSION["user"] = $auth_user;
      header("Location: /");
      exit();
    } else {
      header("Location: /");
      exit();
    }

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
