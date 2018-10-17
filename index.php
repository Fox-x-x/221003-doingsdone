<?php
session_start();


require("functions.php");


// показывать или нет выполненные задачи
if (isset($_GET["show_completed"])) {

  $show_complete_tasks = $_GET["show_completed"];

  // анти-инъекция
  settype($show_complete_tasks, 'integer');
  $_SESSION["user"]["show_completed"] = $_GET["show_completed"];

} else {

    $show_complete_tasks = $_SESSION["user"]["show_completed"] ?? 1;

}

$title = "Дела в порядке";

// Подключаемся к БД
$connect = get_connect_db();

if (!$connect) {
   echo "Error!!! Unable to connect to DB.";
   exit;
}

// Если сессия пустая, то отправляем юзера на страницу логина/регистрации
if (empty($_SESSION['user'])) {

  $content = include_template("guest.php",[]);

  $layout = include_template("layout.php", [
    "content" => $content,
    "title" => $title,
    "projects" => [],
    "tasks" => [],
    "initial_tasks" => []
  ]);
  echo $layout;
  exit;
}



$user_id = $_SESSION["user"]["id"];

/* Получаем список проектов */
$request = "SELECT id, name FROM projects WHERE created_by_user = $user_id ";
$projects = sel_from_db_to_array($connect, $request);

/* Получаем список задач */
$request = "SELECT *
FROM tasks
WHERE created_by_user = $user_id
ORDER BY creation_date DESC ";
$tasks = sel_from_db_to_array($connect, $request);
$initial_tasks = $tasks;


// Если прилетел id проекта, значит кликнули по проектам и нужно показать соответствующие проекту задачи
$project_id = $_GET['id'] ?? null;
settype($project_id, 'integer');
if (!empty($project_id)) {
  $request = "SELECT id, name FROM projects WHERE created_by_user = $user_id AND id = $project_id";
  $project = sel_from_db_to_array($connect, $request)[0];
  if (!$project) {
    echo "нету нихрена";
    http_response_code(404);
    exit;
  }
}

$request = 'SELECT * FROM `tasks` WHERE `created_by_user` = '. $user_id;

if (!empty($project)) {
  $request .= ' AND `related_to_proj` = '. $project['id'];
}

/* проверяем установлен ли идентификатор для фильтрации задач */
if ( !empty($_GET["date"]) ) {
    // защищаемся от инъекций
    $filter_date = mysqli_real_escape_string($connect, $_GET["date"]);
    switch ($filter_date) {
        case 'today' : {
          $request.= ' AND `deadline` >= CURRENT_DATE AND `deadline` < date_add(CURRENT_DATE, INTERVAL 1 day)';
          break;
        }
        case 'tomorrow' : {
          $request.= ' AND `deadline` >= date_add(CURRENT_DATE, INTERVAL 1 day) AND `deadline` < date_add(CURRENT_DATE, INTERVAL 2 day);';
          break;
        }
        case 'overdue': {
            $request.= ' AND `deadline` < CURRENT_DATE';
            break;
        }
    }

}
$tasks = sel_from_db_to_array($connect, $request);

// Выполнение задачи
if (isset($_GET["task_id"], $_GET["check"])) {

  $task_ids = array_column($tasks, "id");
  $task_id = $_GET["task_id"];
  $task_status = $_GET["check"];

  // Защищаемся от SQL-инъекций
  settype($task_id, 'integer');
  settype($task_status, 'boolean');

  // Если task_id пуст, либо если по этому id у юзера не нашли ни одной задачи, то возвращаем 404
  if (!in_array($task_id, $task_ids) ) {
      header("HTTP/1.1 404 Not Found");
      exit();
  }

  $dateOfCompletion = $task_status ? 'NOW()' : 'NULL';
  $sql = "UPDATE tasks
             SET status = 1,
                 date_of_completion = $dateOfCompletion
             WHERE id = " . $task_id;
  $result_sql = insert_into_db($connect, $sql);

  exit();
}

$content = include_template("index.php", [
  "show_complete_tasks" => $show_complete_tasks,
  "tasks" => $tasks
]);

$layout = include_template("layout.php", [
  "content" => $content,
  "title" => $title,
  "projects" => $projects,
  "tasks" => $tasks,
  "initial_tasks" => $initial_tasks
]);

echo $layout;

?>
