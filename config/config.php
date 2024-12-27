<?php
// config/config.php

// Налаштування підключення до бази даних
define('DB_HOST', 'localhost');         // Хост для бази даних (зазвичай localhost)
define('DB_USER', 'root');     // Ім'я користувача бази даних
define('DB_PASS', '');     // Пароль користувача бази даних
define('DB_NAME', 'solar_management');    // Назва бази даних

// Підключення до бази даних за допомогою MySQLi
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Перевірка підключення до бази даних
if ($connection->connect_error) {
  die("Помилка підключення до бази даних: " . $connection->connect_error);
}

// Налаштування для сесій
session_start();
//ini_set('session.cookie_lifetime', 86400); // Час життя сесії (24 години)

// Функція для захисту від SQL-ін'єкцій
function sanitize($data)
{
  global $connection;
  return $connection->real_escape_string(trim($data));
}

// Встановлення рівня помилок (для розробки, можна змінити в продакшн-режимі)
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
