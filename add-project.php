<?php
require("functions.php");


session_start();
if (!isset($_SESSION["user"])) {
    header('HTTP/1.0 403 Forbidden');
    exit();
}

$user_id = $_SESSION["user"]["id"];

// Подключаемся к БД
$connect = mysqli_connect("localhost", "root", "root", "doingsdone");

/* Получаем список проектов */
$request = "SELECT id, name FROM projects WHERE created_by_user = $user_id";
$projects = sel_from_db_to_array($connect, $request);

/* Получаем список задач */
$request = "SELECT id, creation_date, status, name, deadline, created_by_user, related_to_proj
FROM tasks
WHERE created_by_user = $user_id";
$tasks = sel_from_db_to_array($connect, $request);
$initial_tasks = $tasks;


$added_project["name"] = "";

// Проверяем отправку формы и валидируем, если отправлена
if ($_SERVER["REQUEST_METHOD"] === "POST") {

   $added_project = $_POST;

   $added_project["name"] = mysqli_real_escape_string($connect, $_POST["name"]);

   $required = ["name"];

   $errors = [];

   if (strlen($added_project["name"])) {
     $errors = validate_project_form($connect, $added_project, $user_id);
   }


   if ( empty($errors) ) {

     // формируем запрос на добавление в БД
     $project_name = $added_project["name"];
     $insert_request = "INSERT INTO projects SET name = '$project_name', created_by_user = '$user_id' ";
     $insert_result = insert_into_db($connect, $insert_request);

     // если запрос выполнился, идем на главную
     if ($insert_result) {

       header("Location: /index.php");
     }
   }

   

}


// Подключаем шаблон index и layout
$content = include_template("add-project.php", [
  "added_project" => $added_project,
  "errors" => $errors
]);

$title = "Добавить проект";

$layout = include_template("layout.php", [
  "content" => $content,
  "title" => $title,
  "projects" => $projects,
  "tasks" => $tasks,
  "initial_tasks" => $initial_tasks
]);

echo $layout;



?>
