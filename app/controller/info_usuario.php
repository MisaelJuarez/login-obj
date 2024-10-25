<?php
session_start();
require_once '../config/conexion.php';

class Informacion extends Conexion {

    private $expresion = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

    public function comprobar_emails($datos,$email) {
        foreach($datos as $emails) {
            if ($emails['email'] == $email) {
                return true;
            } 
        }
    }

    public function obtener_informacion() {
        $consulta = $this->obtener_conexion()->prepare("SELECT * FROM t_usuarios WHERE id_usuario = :id_usuario");
        $consulta->bindParam(':id_usuario',$_SESSION['usuario']['id_usuario']);
        $consulta->execute();
        $datos = $consulta->fetch(PDO::FETCH_ASSOC);

        echo json_encode($datos);
    }

    public function actualizar_informacion() {
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $email = $_POST['email'];
        $pass= $_POST['pass'];

        if (empty($_POST['nombre']) && empty($_POST['apellido']) && empty($_POST['email']) && empty($_POST['pass'])) {
            echo json_encode([0,"Campos incompletos"]);
        } else if(is_numeric($nombre) || is_numeric($apellido)) {
            echo json_encode([0,"No puedes ingresar numeros en nombre y apellido"]);
        } else if (!preg_match($this->expresion,$_POST['email'])) {
            echo json_encode([0,"No cumples con las especificaciones de un correo"]);
        } else {

            if ($_SESSION['usuario']['email'] != $email || $_SESSION['usuario']['pass'] != $pass) {

                $usuariosEmail = $this->obtener_conexion()->prepare("SELECT email FROM t_usuarios WHERE id_usuario != :id_usuario");
                $usuariosEmail->bindParam(':id_usuario',$_SESSION['usuario']['id_usuario']);
                $usuariosEmail->execute();
                $emails = $usuariosEmail->fetchAll(PDO::FETCH_ASSOC);

                if ($this->comprobar_emails($emails,$email)) {
                    echo json_encode([0,"Ese correo ya existe, ingrese otro correo"]);
                } else {
                    $actualizacion = $this->obtener_conexion()->prepare("UPDATE t_usuarios 
                    SET nombre = :nombre, apellido = :apellido, email = :email, pass = :pass  
                    WHERE id_usuario = :id_usuario");

                    $actualizacion->bindParam(':nombre',$nombre);
                    $actualizacion->bindParam(':apellido',$apellido);
                    $actualizacion->bindParam(':email',$email);
                    $passw = password_hash($pass,PASSWORD_BCRYPT);
                    $actualizacion->bindParam(':pass',$passw);
                    $actualizacion->bindParam(':id_usuario',$_SESSION['usuario']['id_usuario']);
                    
                    $actualizacion->execute();

                    echo json_encode(["cerrar","Actualizacion correcta","Tu session se cerrara para que ingreses de nuevo tus datos"]);
                }
            } else {
                $actualizacion = $this->obtener_conexion()->prepare("UPDATE t_usuarios 
                    SET nombre = :nombre, apellido = :apellido, email = :email, pass = :pass  
                    WHERE id_usuario = :id_usuario");

                $actualizacion->bindParam(':nombre',$nombre);
                $actualizacion->bindParam(':apellido',$apellido);
                $actualizacion->bindParam(':email',$email);
                $actualizacion->bindParam(':pass',$pass);
                $actualizacion->bindParam(':id_usuario',$_SESSION['usuario']['id_usuario']);

                $actualizacion->execute();

                $consulta = $this->obtener_conexion()->prepare("SELECT * FROM t_usuarios WHERE id_usuario = :id_usuario");
                $consulta->bindParam(':id_usuario',$_SESSION['usuario']['id_usuario']);
                $consulta->execute();
                $datos = $consulta->fetch(PDO::FETCH_ASSOC);

                $_SESSION['usuario'] = $datos;
                echo json_encode([1,"Informacion actualizada correctamente"]);
            }

        }
    }
}

$consulta = new Informacion();
$metodo = $_POST['metodo'];
$consulta->$metodo();



?>