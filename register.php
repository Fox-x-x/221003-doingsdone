<?php
require("functions.php");


// Проверяем отправку формы и валидируем, если отправлена
if (!empty($_POST)) {

  $reg_user = $_POST;

  $required = ["email", "password", "name"];

  $errors = [];

  // проверяем email на валидность и записываем ошибку + проверяем на существование такоего email в базе
  $connect = get_connect_db();

  // защищаемся от SQL-инъекций
  $reg_user_safe["name"] = mysqli_real_escape_string($connect, $reg_user["name"]);
  $reg_user_safe["email"] = mysqli_real_escape_string($connect, $reg_user["email"]);

  // если что-то еще осталось от email, то валидируем его
  if ( (!empty($reg_user_safe["email"])) ) {
    $errors = validate_reg_form($connect, $reg_user_safe["email"]);
  }

  // проверяем все ли поля заполнены
  foreach ($required as $key) {
    if ( empty($reg_user[$key]) ) {
        $errors[$key] = "Это поле надо заполнить";
    }
  }


  // Если ошибок нет, то сохраняем и перенаправляем на главную
  if (empty($errors)) {

    $now = date("Y-m-d H:i:s");
    $user_email = $reg_user_safe["email"];
    $passwordHash = password_hash($reg_user["password"], PASSWORD_DEFAULT);
    $user_name = $reg_user_safe["name"];

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
