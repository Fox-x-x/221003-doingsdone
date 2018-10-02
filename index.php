<?php
require("functions.php");


// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
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



// Подключаем шаблон index и layout
$content = include_template("index.php", [
  "show_complete_tasks" => $show_complete_tasks,
  "tasks"=>$tasks
]);

$title = "Дела в порядке";

$layout = include_template("layout.php", [
  "content" => $content,
  "title" => $title,
  "projects" => $projects,
  "tasks" => $tasks
]);

print($layout);

?>
