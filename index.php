<?php
require("functions.php");

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
$projects = ["Входящие", "Учеба", "Работа", "Домашние дела", "Авто"];

$tasks = [
   [
    "task" => "Собеседование в IT компании",
    "date" => "01.12.2018",
    "project" => $projects[2],
    "done" => false
  ],
  [
    "task" => "Выполнить тестовое задание",
    "date" => "20.09.2018",
    "project" => $projects[2],
    "done" => false
  ],
  [
    "task" => "Сделать задание первого раздела",
    "date" => "21.12.2018",
    "project" => $projects[1],
    "done" => true
  ],
  [
    "task" => "Встреча с другом",
    "date" => "22.12.2018",
    "project" => $projects[0],
    "done" => false
  ],
  [
    "task" => "Купить корм для кота",
    "date" => "",
    "project" => $projects[3],
    "done" => false
  ],
  [
    "task" => "Заказать пиццу",
    "date" => "",
    "project" => $projects[3],
    "done" => false
  ]
];

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
