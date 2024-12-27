<?php

class UserController
{
  private $connection;

  // Конструктор для ініціалізації з'єднання з базою даних
  public function __construct($connection)
  {
    $this->connection = $connection;
  }

  // Метод для отримання всіх користувачів з їх ролями
  public function getAllUsers()
  {
    $users = [];
    $stmt  = $this->connection->prepare("SELECT u.user_id, u.username, u.email, u.created_at, r.role_name
                                            FROM users u
                                            LEFT JOIN roles r ON u.role_id = r.role_id
                                            ORDER BY u.created_at DESC");

    if ($stmt->execute()) {
      $result = $stmt->get_result();
      while ($row = $result->fetch_assoc()) {
        $users[] = $row;
      }
    } else {
      echo "Помилка виконання запиту: " . $this->connection->error;
    }

    $stmt->close();
    return $users;
  }

  // Метод для отримання даних користувача за його ID
  public function getUserById($user_id)
  {
    $stmt = $this->connection->prepare("SELECT u.user_id, u.username, u.email, u.created_at, r.role_name, u.role_id
                                            FROM users u
                                            LEFT JOIN roles r ON u.role_id = r.role_id
                                            WHERE u.user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
      $result = $stmt->get_result();
      $user   = $result->fetch_assoc();
      $stmt->close();
      return $user;
    } else {
      echo "Помилка виконання запиту: " . $this->connection->error;
      return null;
    }
  }

  // Метод для додавання нового користувача
  public function addUser($username, $email, $password_hash, $role_id)
  {
    $stmt = $this->connection->prepare("INSERT INTO users (username, email, password_hash, role_id, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssi", $username, $email, $password_hash, $role_id);

    if ($stmt->execute()) {
      return true;
    } else {
      echo "Помилка додавання користувача: " . $this->connection->error;
      return false;
    }
  }

  // Метод для оновлення даних користувача
  public function updateUser($user_id, $username, $email, $role_id)
  {
    $stmt = $this->connection->prepare("UPDATE users SET username = ?, email = ?, role_id = ? WHERE user_id = ?");
    $stmt->bind_param("ssii", $username, $email, $role_id, $user_id);

    if ($stmt->execute()) {
      return true;
    } else {
      echo "Помилка оновлення користувача: " . $this->connection->error;
      return false;
    }
  }

  // Метод для зміни пароля користувача
  public function updateUserPassword($user_id, $new_password_hash)
  {
    $stmt = $this->connection->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
    $stmt->bind_param("si", $new_password_hash, $user_id);

    if ($stmt->execute()) {
      return true;
    } else {
      echo "Помилка оновлення пароля: " . $this->connection->error;
      return false;
    }
  }

  // Метод для видалення користувача за його ID
  public function deleteUser($user_id)
  {
    $stmt = $this->connection->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
      return true;
    } else {
      echo "Помилка видалення користувача: " . $this->connection->error;
      return false;
    }
  }

  // Метод для отримання всіх ролей
  public function getAllRoles()
  {
    $roles = [];
    $stmt  = $this->connection->prepare("SELECT * FROM roles");

    if ($stmt->execute()) {
      $result = $stmt->get_result();
      while ($row = $result->fetch_assoc()) {
        $roles[] = $row;
      }
    } else {
      echo "Помилка отримання ролей: " . $this->connection->error;
    }

    $stmt->close();
    return $roles;
  }
}
