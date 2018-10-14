<?php
require("functions.php");


// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

// Подключаемся к БД
$connect = mysqli_connect("localhost", "root", "root", "doingsdone");


session_start();

if ($connect && isset($_SESSION["user"])) {

  $user_id = $_SESSION["user"]["id"];

  /* Получаем список проектов */
  $request = "SELECT id, name FROM projects WHERE created_by_user = $user_id";
  $projects = sel_from_db_to_array($connect, $request);

  /* Получаем список задач */
  $request = "SELECT id, creation_date, status, name, deadline, created_by_user, related_to_proj
  FROM tasks
  WHERE created_by_user = $user_id";
  $tasks = sel_from_db_to_array($connect, $request);
  $initial_tasks = $tasks;


  /* проверяем установлен ли идентификатор запроса и показываем соответствующие задачи */
  if (isset($_GET["id"])) {

    // Ищем проекты с полученным из $_GET id
    // Нужно будет потом защититься от инъекций (!)
    $project_id = $_GET["id"];
    $request = "SELECT id, name FROM projects WHERE created_by_user = $user_id AND id = $project_id";
    $projects_test = sel_from_db_to_array($connect, $request);

    /* Если не нашли проекта с данным id, то вернем 404 ошибку */
    if (!$projects_test) {

      echo "нету нихрена";
      http_response_code(404);
      die();

    } else {

      /* В противном случае получаем список задач */
      $request = "SELECT id, creation_date, status, name, deadline, created_by_user, related_to_proj
      FROM tasks
      WHERE created_by_user = $user_id AND related_to_proj = $project_id";
      $tasks = sel_from_db_to_array($connect, $request);
    }

  } else {

    $tasks = $initial_tasks;

  }
}



// Подключаем шаблон index и layout
if (isset($_SESSION["user"])) {
  $content = include_template("index.php", [
    "show_complete_tasks" => $show_complete_tasks,
    "tasks" => $tasks
  ]);
} else {
  $content = include_template("guest.php",[]);
}

$title = "Дела в порядке";

$layout = include_template("layout.php", [
  "content" => $content,
  "title" => $title,
  "projects" => $projects,
  "tasks" => $tasks,
  "initial_tasks" => $initial_tasks
]);

echo $layout;

?>
