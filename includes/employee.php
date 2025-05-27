
<?php

function getAllEmployees($db) {
    $sql = "SELECT employees.id AS employee_id, employees.first_name, employees.last_name, employees.id_department, departments.name
            FROM employees
            LEFT JOIN departments ON employees.id_department = departments.id";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getEmployeeById($db, $id) {
    $sql = "SELECT id, first_name, last_name, id_department FROM employees WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createEmployee($db, $first_name, $last_name, $id_department) {
    $sql = "INSERT INTO employees (first_name, last_name, id_department) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    return $stmt->execute([$first_name, $last_name, $id_department]);
}

function updateEmployee($db, $id, $first_name, $last_name, $id_department) {
    $sql = "UPDATE employees SET first_name = ?, last_name = ?, id_department = ? WHERE id = ?";
    $stmt = $db->prepare($sql);
    return $stmt->execute([$first_name, $last_name, $id_department, $id]);
}

function deleteEmployee($db, $id) {
    $sql = "DELETE FROM employees WHERE id = ?";
    $stmt = $db->prepare($sql);
    return $stmt->execute([$id]);
}