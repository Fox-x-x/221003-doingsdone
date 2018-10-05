<?php
require("functions.php");

$user_id = 2;

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


$added_task["name"] = "";

// Проверяем отправку формы и валидируем, если отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST") {

   echo "sent<br>";
   $added_task = $_POST;

   echo "POST:<br>";
   var_dump($added_task);
   echo "<br>";

   $required = ["name", "project"];
   $dict = ["name" => "Название задачи", "project" => "Выберите категорию"];

   $errors = [];
   foreach ($required as $key) {
     if (empty($_POST[$key])) {
            $errors[$key] = "Это поле надо заполнить";
     }
   }

   echo "errors:<br>";
   var_dump($errors);

   // Если ошибок нет, то сохраняем
   if (!empty($errors)) {

   }

}


// Подключаем шаблон index и layout
$content = include_template("form-task.php", [
  "projects" => $projects,
  "tasks" => $tasks,
  "added_task" => $added_task,
  "errors" => $errors
]);

$title = "Добавить задачу";

$layout = include_template("layout.php", [
  "content" => $content,
  "title" => $title,
  "projects" => $projects,
  "tasks" => $tasks,
  "initial_tasks" => $initial_tasks
]);

echo $layout;








?>
