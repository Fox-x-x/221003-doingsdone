<?php
session_start();
require("functions.php");




if (!isset($_SESSION["user"])) {
    header('HTTP/1.0 403 Forbidden');
    exit();
}

$user_id = $_SESSION["user"]["id"];



// Подключаемся к БД
$connect = get_connect_db();

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
if (!empty($_POST)) {

   $added_task = $_POST;

   $errors = [];

   // валидируем форму, включая все защиты от инъекций и прочего
   $errors = validate_task_form($connect, $added_task, $user_id);


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

       // проверяем есть ли уже файл с таким именем
       while (file_exists($file_path . $file_name)) {
         $file_name = date("Y-m-d H:i:s").$file_name;
       }
       $added_file = $file_path . $file_name;

     } else {
       $added_file = "";
     }


     $added_project = $added_task["project"];
     $now = date("Y-m-d H:i:s");
     $added_date = $added_task["date"];
     // защищаемся от инъекций через имя файла
     $added_file_safe_name = mysqli_real_escape_string($connect, $added_file);
     $added_date = $added_task["date"] ? "'$added_date'" : 'null';
     
     $insert_request = "INSERT INTO tasks
                           SET creation_date      = '$now',
                               date_of_completion = null,
                               status             = 0,
                               name               = '$added_name',
                               file               = '$added_file_safe_name',
                               deadline           =  $added_date,
                               created_by_user    = '$user_id',
                               related_to_proj    = '$added_project'";



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
