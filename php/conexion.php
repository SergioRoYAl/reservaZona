<?php

@include("imports.php");

$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if (isset($_POST['option'])) {
    switch ($_POST['option']) {
        case "INSERT":

            if ($_POST['tipo'] == 'isleta') {

                $sql = "INSERT INTO isleta (id, nombre, height, width, x, y, id_zona, prefijo) VALUES (:id, :nombre, :height, :width, :x, :y, :id_zona, :prefijo)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':x', $_POST['x']); 
                $stmt->bindParam(':y', $_POST['y']);
                $stmt->bindParam(':nombre', $_POST['nombre']);
                $stmt->bindParam(':height', $_POST['height']);
                $stmt->bindParam(':width', $_POST['width']);
                $stmt->bindParam(':id', $_POST['id']);
                $stmt->bindParam(':id_zona', $_POST['id_zona']);
                $stmt->bindParam(':prefijo', $_POST['prefijo']);
                $stmt->execute();

                echo "Almacenado correctamente en la base de datos.";
            } else if($_POST['tipo'] == 'zona'){
                $id = $_POST['id'];
                $nombre = $_POST['nombre'];
                
                $sql = "INSERT INTO zona (id, nombre) VALUES (:id, :nombre)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':id', $id);
                $stmt->execute();

            } else {
                echo "Error: Altura y anchura no proporcionadas en la solicitud POST.";
            }
            break;

        case "UPDATE":
            if (isset($_POST['height']) && isset($_POST['width']) && isset($_POST['id'])) {
                $tipo = $_POST['tipo'];
                $height = $_POST['height'];
                $width = $_POST['width'];
                $id = $_POST['id'];
                $sql = "UPDATE $tipo SET height = :height, width = :width where id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':height', $height);
                $stmt->bindParam(':width', $width);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                echo "Coordenadas actualizadas correctamente correctamente en la base de datos.";
            } else {
                echo "Error: Altura y anchura no proporcionadas en la solicitud POST.";
            }
            break;
    }
} else if (isset($_GET['option'])) {
    switch ($_GET['option']) {
        case "LASTID":
            try {

                $tipo = $_GET['tipo'];
                $sql = "SELECT * FROM $tipo where id = (SELECT MAX(id) FROM $tipo)";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) == 0) {
                    echo json_encode(array("id" => 0));
                } else {
                    echo json_encode($result);
                }
            } catch (PDOException $e) {
                echo "Error de conexión: " . $e->getMessage();
            }
            break;
        case "GETALL":
            if($_GET['tipo'] == 'isleta'){
                $sql = "SELECT * FROM isleta";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($result);
            } else if($_GET['tipo'] == 'zona'){
                $sql = "SELECT * FROM zona";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($result);
            }
            break;
    }
}