<?php
require_once '../config/conexion.php';

class Productos extends Conexion {
    public function obtener_datos() {
        $consulta = $this->obtener_conexion()->prepare("SELECT * FROM t_productos");
        $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $this->cerrar_conexion();
        echo json_encode($datos);
    }

    public function registrar_producto() {
        if (isset($_POST['nombre_p']) && !empty($_POST['nombre_p']) && 
            isset($_POST['precio_p']) && !empty($_POST['precio_p']) && 
            isset($_POST['cantidad_p']) && !empty($_POST['cantidad_p'])) {

            $nombreProducto = $_POST['nombre_p'];
            $precioProducto = $_POST['precio_p'];
            $cantidadProducto = $_POST['cantidad_p'];

            if (is_numeric($precioProducto) && is_numeric($cantidadProducto)) {

                $insercion = $this->obtener_conexion()->prepare("INSERT INTO t_productos (producto,precio,cantidad) 
                                                VALUES(:producto,:precio,:cantidad)");
    
                $insercion->bindParam(':producto',$nombreProducto);
                $insercion->bindParam(':precio',$precioProducto);
                $insercion->bindParam(':cantidad',$cantidadProducto);
    
                $insercion->execute();
        
                if ($insercion) {
                    echo json_encode([1,"Producto registrado"]);
                } else {
                    echo json_encode([0,"Producto NO registrado"]);
                }
            } else {
                echo json_encode([0,"Solo datos numericos en precio y cantidad"]);
            }

        } else {
            echo json_encode([0,"No puedes dejar campos vacios"]);
        }
    }

    public function editar_producto() {
        if (!empty($_POST['nombre_p']) && !empty($_POST['precio_p']) && !empty($_POST['cantidad_p'])) {
            $id_producto = $_POST['idInput'];
            $nombreproducto = $_POST['nombre_p'];
            $precioProducto = $_POST['precio_p'];
            $cantidadProducto = $_POST['cantidad_p'];
        
            if (is_numeric($precioProducto) && is_numeric($cantidadProducto)) {
                $actualizacion = $this->obtener_conexion()->prepare("UPDATE t_productos 
                SET producto = :producto, precio = :precio, cantidad = :cantidad  
                WHERE id_producto = :id_producto");
            
                $producto = $nombreproducto;
                $precio = $precioProducto;
                $cantidad = $cantidadProducto;
                $id = $id_producto;
            
                $actualizacion->bindParam(':producto',$producto);
                $actualizacion->bindParam(':precio',$precio);
                $actualizacion->bindParam(':cantidad',$cantidad);
                $actualizacion->bindParam(':id_producto',$id);
            
                $actualizacion->execute();
            
                if ($actualizacion) {
                    echo json_encode([1,"Producto actualizado correctamente"]);
                } else {
                    echo json_encode([0,"Producto NO actualizado correctamente"]);
                }
            } else {
                echo json_encode([0,"Solo datos numericos en precio y cantidad"]);
            }
        } else {
            echo json_encode([0,"Datos incompletos"]);
        }
    }

    public function eliminar_producto(){
        $id_producto = $_POST['idInput'];

        $eliminar = $this->obtener_conexion()->prepare("DELETE FROM t_productos WHERE id_producto = :id_producto");
        $id = $id_producto;
        $eliminar->bindParam(':id_producto',$id);
        $eliminar->execute();

        if ($eliminar) {
            echo json_encode([1,'Producto eliminado correctamente']);
        } else {
            echo json_encode([0,'Producto NO eliminado correctamente']);
        }
    }
}

$consulta = new Productos();
$metodo = $_POST['metodo'];
$consulta->$metodo();

?>