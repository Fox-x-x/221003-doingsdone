<?php

function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!file_exists($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require_once $name;

    $result = ob_get_clean();

return $result;
};


function countTasks($tasksArray, $projectName) {
  $tasksCounter = 0;
  foreach ($tasksArray as $task) {
    if ($task["project"] === $projectName) {
      $tasksCounter++;
    }
  };

  return $tasksCounter;
};

function isImportant($task) {
  // date_default_timezone_set("Europe/Kaliningrad");

  // $endDate = strtotime($task["date"]);
  // $now = time();
  //
  // $diff = $endDate - $now;
  // $hoursLeft = floor($diff / 3600);
  echo $task["date"];

  // if ($hoursLeft <= 24) {
  //   return false;
  // } else {
  //   return true;
  // }
};

?>
