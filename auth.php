<?php
require("functions.php");


$connect = mysqli_connect("localhost", "root", "root", "doingsdone");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $user = $_POST;

  $required = ["email", "password"];
  $errors = [];

  // валидируем форму
  $errors = validate_auth_form($connect, $required, $user);

  // если ошибок нет, то записываем в сессию информацию о юзере
  if (empty($errors)) {

    $auth_user = get_auth_user($connect, $user);
    session_start();
    $_SESSION["user"] = $auth_user;
    echo "session<br>";
    var_dump($_SESSION["user"]);
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
  // "projects" => $projects,
  // "tasks" => $tasks,
  // "initial_tasks" => $initial_tasks
]);

echo $layout;

?>
