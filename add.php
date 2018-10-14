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


$added_task["name"] = "";

// Проверяем отправку формы и валидируем, если отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST") {

   $added_task = $_POST;
   $added_task["name"] = mysqli_real_escape_string($connect, $_POST["name"]);
   $added_task["project"] = mysqli_real_escape_string($connect, $_POST["project"]);
   $added_task["date"] = mysqli_real_escape_string($connect, $_POST["date"]);

   $required = ["name", "project"];

   $errors = [];
   foreach ($required as $key) {
     if (empty($_POST[$key])) {
            $errors[$key] = "Это поле надо заполнить";
     }
   }


   // Проверяем был ли прикреплен файл
   $save_file = false;
   if (isset($_FILES["preview"]["name"]) && ($_FILES["preview"]["name"] != '')) {

     $file_name = $_FILES["preview"]["name"];
     $file_path = __DIR__ . "/";
     $file_url = $file_path . $file_name;

     $save_file = true;

   }

   // Если ошибок нет, то сохраняем
   if (empty($errors)) {

     // SQL запрос на добавление задачи в БД
     $added_name = $added_task["name"];
     if ($save_file) {
       $added_file = $file_path . $file_name;
     } else {
       $added_file = "";
     }

     $added_project = $added_task["project"];
     $now = date("Y-m-d H:i:s");
     $added_date = $added_task["date"];

     // если задача без даты, то приходится колхозить
     if ($added_date === "") {
       $added_date = "null";
       $insert_request = "INSERT INTO tasks
                           SET creation_date      = '$now',
                               date_of_completion = null,
                               status             = 0,
                               name               = '$added_name',
                               file               = '$added_file',
                               deadline           =  $added_date,
                               created_by_user    = '$user_id',
                               related_to_proj    = '$added_project'";

     } else {
       $insert_request = "INSERT INTO tasks
                           SET creation_date      = '$now',
                               date_of_completion = null,
                               status             = 0,
                               name               = '$added_name',
                               file               = '$added_file',
                               deadline           = '$added_date',
                               created_by_user    = '$user_id',
                               related_to_proj    = '$added_project'";
     }


     $insert_result = insert_into_db($connect, $insert_request);

     if ($insert_result) {

       if ($save_file) {
         // сохраняем файл
         move_uploaded_file($_FILES["preview"]["tmp_name"], $file_path . $file_name);
       }


       // переадресация на главную
       header("Location: /index.php");
     }

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
