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
if ($_SERVER["REQUEST_METHOD"] == "POST") {

 echo "sent";
 $added_task = $_POST;

}


// Подключаем шаблон index и layout
$content = include_template("form-task.php", [
  "projects" => $projects,
  "tasks" => $tasks,
  "added_task" => $added_task
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
