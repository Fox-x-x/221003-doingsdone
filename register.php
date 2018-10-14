<?php
require("functions.php");


// Проверяем отправку формы и валидируем, если отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $reg_user = $_POST;

  $required = ["email", "password", "name"];

  $errors = [];

  // проверяем email на валидность и записываем ошибку + проверяем на существование такоего email в базе
  $connect = mysqli_connect("localhost", "root", "root", "doingsdone");
  $errors = validate_reg_form($connect, $reg_user["email"], $required, $reg_user);


  // Если ошибок нет, то сохраняем и перенаправляем на главную
  if (empty($errors)) {

    $now = date("Y-m-d H:i:s");
    $user_email = $reg_user["email"];
    $passwordHash = password_hash($reg_user["password"], PASSWORD_DEFAULT);
    $user_name = $reg_user["name"];

    $request = "INSERT INTO users
                        SET reg_date      = '$now',
                            email         = '$user_email',
                            name          = '$user_name',
                            password      = '$passwordHash' ";

    $insert_result = insert_into_db($connect, $request);

    if ($insert_result) {
      header("Location: /index.php");
    }
  }


}


// подключаем шаблоны
$title = "Дела в порядке: регистрация/вход";

$layout = include_template("register.php", [
  "title" => $title,
  "errors" => $errors,
  "reg_user" => $reg_user
]);

echo $layout;

?>
