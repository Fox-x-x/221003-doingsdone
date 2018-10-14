<?php
require("functions.php");





// Подключаемся к БД
$connect = mysqli_connect("localhost", "root", "root", "doingsdone");


session_start();

// показывать или нет выполненные задачи
if (isset($_GET["show_completed"])) {

  $show_complete_tasks = $_GET["show_completed"];
  
  // анти-инъекция
  settype($show_complete_tasks, 'integer');
  $_SESSION["user"]["show_completed"] = $_GET["show_completed"];

} else {
  if (isset($_SESSION["user"]["show_completed"])) {
    $show_complete_tasks = $_SESSION["user"]["show_completed"];
  } else {
    $show_complete_tasks = 1;
  }

}

if ($connect && isset($_SESSION["user"])) {

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


  /* проверяем установлен ли идентификатор запроса и показываем соответствующие задачи */
  if ( isset($_GET["id"]) && empty($_GET["date"]) ) {

    // Ищем проекты с полученным из $_GET id
    $project_id = $_GET["id"];
    // защищаемся от инъекций
    settype($project_id, 'integer');

    $request = "SELECT id, name FROM projects WHERE created_by_user = $user_id AND id = $project_id";
    $projects_test = sel_from_db_to_array($connect, $request);

    /* Если не нашли проекта с данным id, то вернем 404 ошибку */
    if (!$projects_test) {

      echo "нету нихрена";
      http_response_code(404);
      die();

    } else {

      /* В противном случае получаем список задач */
      $request = "SELECT * FROM tasks
      WHERE created_by_user = $user_id AND related_to_proj = $project_id
      ORDER BY creation_date DESC ";
      $tasks = sel_from_db_to_array($connect, $request);
    }

  /* проверяем установлен ли идентификатор для фильтрации задач */
  } else if ( !empty($_GET["date"]) ) {

      // если идентификатор проекта установлен, значит будем применять фильтр в рамках проекта,
      // если нет, то ко всему списку задач
      if ( isset($_GET["id"]) ) {
        $project_id = $_GET["id"];
        // защищаемся от инъекций
        settype($project_id, 'integer');

        $request = 'SELECT * FROM `tasks` WHERE `created_by_user` = '. $user_id .' AND `related_to_proj` = '.$project_id;
      } else {
        $request = 'SELECT * FROM `tasks` WHERE `created_by_user` = '. $user_id;
      }

      // защищаемся от инъекций
      $filter_date = mysqli_real_escape_string($connect, $_GET["date"]);




      if ( (strlen($filter_date)) && ($filter_date === "today") ) {

        $request.= ' AND `deadline` >= CURRENT_DATE AND `deadline` < date_add(CURRENT_DATE, INTERVAL 1 day)';
        // echo $request . "<br><br>";
        $tasks = sel_from_db_to_array($connect, $request);

      } else if ( (strlen($filter_date)) && ($filter_date === "tomorrow") ) {

        $request.= ' AND `deadline` >= date_add(CURRENT_DATE, INTERVAL 1 day) AND `deadline` < date_add(CURRENT_DATE, INTERVAL 2 day);';
        // echo $request . "<br><br>";
        $tasks = sel_from_db_to_array($connect, $request);

      } else if ( (strlen($filter_date)) && ($filter_date === "overdue") ) {

        $request.= ' AND `deadline` < CURRENT_DATE';
        // echo $request . "<br><br>";
        $tasks = sel_from_db_to_array($connect, $request);

      }




  } else {
      $tasks = $initial_tasks;
  }


  // Выполнение задачи
  if (isset($_GET["task_id"], $_GET["check"])) {

    // echo "Получили task_id и check<br>";

    $task_ids = array_column($tasks, "id");
    $task_id = $_GET["task_id"];
    $task_status = $_GET["check"];

    // echo "task_id = " . $task_id . "<br>";
    // echo "check = " . $task_status . "<br>";
    // var_dump($task_ids);
    // echo "<br><br>";

    // Защищаемся от SQL-инъекций
    settype($task_id, 'integer');
    settype($task_status, 'boolean');

    // Если task_id пуст, либо если по этому id у юзера не нашли ни одной задачи, то возвращаем 404
    if (!in_array($task_id, $task_ids) || empty($task_id)) {
        header("HTTP/1.1 404 Not Found");
        exit();
    }

    if ($task_status) {

      $sql = "UPDATE tasks
                 SET status = 1,
                     date_of_completion = NOW()
                 WHERE id = " . $task_id;

      $result_sql = insert_into_db($connect, $sql);

    } else {
      $sql = "UPDATE tasks
                 SET status = 0,
                     date_of_completion = NULL
                 WHERE id = " . $task_id;

      $result_sql = insert_into_db($connect, $sql);
    }

    if ($result_sql) {
      header("Location: /index.php");
      exit();
    }
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
