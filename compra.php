<?php
require_once './clases/clases.php';
session_start();
// instanciamos un objeto de la clase cine para la conexion y gestion de la base de datos...
$pru = new PdoConexion("mysql", "localhost", "manuel", "12345", "gestcines2");

// si no existe la variable de sesion "nombre" lo manda al formulario de acceso
if (!isset($_SESSION['nombre'])) {
    echo ("<script>
                            window.location='index.php';
                            </script>");
}
// si se ha seleccionado una sala se crea la variable de sesion sala para crear el objeto archivo...
if (isset($_POST['sala'])) {
    $_SESSION['sala']="sala".$_POST['sala'].".txt";
}

/* * ******************************************************************************IMPORTANTE PARA EXAMEN** */
// si no existe la variable de sesion sala la crea y por defecto le da el valor de la sala 1
if (!isset($_SESSION['sala'])) {
    $_SESSION['sala'] = "sala1.txt";
}
//echo("<script>alert('se va a entrar en la sala ".$_SESSION['sala']."');</script>");
// instanciamos un objeto de la clase gestcines para la conexion y gestion de la base de datos...
$archivo = new gestCines($_SESSION['sala']); // creamos la clase archivo para control salas ...
// si no existe el archivo txt de la sala correspondiente se crea y se pone sus butacas a 0
if (!is_file($_SESSION['sala'])) {
    $archivo->iniciarSala();
}
$archivo->butacas = $archivo->arrayLineas();

/* * ********si se ha pulsado el boton de compra realizamos la compra...****************** */


if (isset($_POST['genera'])) {
    $pp=$pru->getSalas();
    for ($w=$pp;$w>0;$w--){
    $_SESSION['sala'] = "sala".$w.".txt";
    $archivo = new gestCines($_SESSION['sala']);
    $archivo->iniciarSala();
    }
}

if (isset($_POST['nsalas'])){
    $pru->setSalas($_POST['nsalas']);
}

//*******validar la compra *************//

if (isset($_POST['buta'])) {
    $archivo->butacas = $archivo->arrayLineas();
    if ($archivo->butacas[$_POST['buta']] == 0) {// si la posicion del array es igual a cero...
        $archivo->butacas[$_POST['buta']] = 1; //ponemos a uno la posicion del array...
        $archivo->compra(); // ejecutamos la funcion compra que realiza la compra...
        $pru->compra(); // efectuamos las operaciones de compra....
    } else {
        unset($_POST['buta']);
        echo("<script>alert('Butaca no disponible');</script>");
        echo ("<script>
                            window.location='compra.php';
                            </script>");
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <script type="text/javascript" src="./js/funciones.js"></script>
        <LINK REL=StyleSheet HREF="./css/estilo.css" TYPE="text/css" MEDIA=screen>
    </head>
    <body>
        <div id="contenedor">
            <div id="admin" style="text-align: center">
                <?php
            if ($_SESSION['nombre'] == "manuel") {
                echo("<form id='gsalas' method='POST' action='compra.php'>
                <input type='hidden' name='genera' />
                <input type='submit' value='INICIAR SALAS' />
            </form>"); 
            }
            ?>
            </div>
            <div id="salan">
                <?php
                if ($_SESSION['nombre']=="manuel"){
                        echo("<form id='numsalas' method='POST' action='compra.php'>
                Numero de salas:<input type='txt' name='nsalas' /><br>
                <input type='submit' value='CREAR SALAS' />
            </form>");
                }
                ?>
            </div>
            <h1> GESTION DE SALAS </h1>
            
            <!--/********** AQUI CREARE ESTE FORMULARIO CON PHP Y CON LA VARIABLE FECHA ACTUAL ***********/-->
            <div id="selec">
                
            <form id="salas" name="salas" method="POST" action="">
                <select name="sala" onchange="salas.submit()">
                    <?php
                    $nums=$pru->getSalas();
                      echo("<option value='0'>Seleccione una sala</option>");
                    for ($s=1;$s<=$nums;$s++){
                        $peli=$pru->getPeli($s);
                    echo("<option value='".$s."'>".$peli."</option>");
                    }
                    ?>
                    </select>
            </form>
            </div>
            <h2> DISPONIBILIDAD DE LA SALA <?PHP
            if (isset($_SESSION['sala'])){
                $sak=substr($_SESSION['sala'], 5,1);
                if ($sak=="."){
                $sak=substr($_SESSION['sala'], 4,1);
                }else{
                     $sak=substr($_SESSION['sala'], 4,2);
                }
            }
            echo($sak);
            ?></h2>
            <div id="butacas" style="width: 710px; margin: 0 auto;">
                <?php
                $archivo->mostrarSala();
                ?>
            </div>

            <div id="dat"  style="overflow: hidden;  width: 160px; height:60px; display: none; position: absolute; z-index: 10; top: 15%;left: 65%; ">
                <input id="dat2" type="text" value="34" style="opacity: 0.8;"/>
            </div>
        </div>
    </body>
</html>
