<?php
require_once './clases/clases.php';
session_start();
$pru = new PdoConexion("mysql", "localhost", "manuel", "12345", "gestcines2");

if (isset($_SESSION['nombre'])) {
    
    echo ("<script>
                            window.location='compra.php';
                            </script>");
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Formulario de acceso</title>
        <?php
        if (isset($_POST['1'])) {// opcion para formulario de entrada
            $nombre = trim($_POST['usuario']);
            $pass = md5(trim($_POST['pass']));
            if ($nombre != "" && $pass != "") {
                $resultado = $pru->Validar($nombre, $pass);
                if (!$resultado) {
                    echo("<script>alert('Usuario inexistente');</script>");
                } else {
                    $_SESSION['nombre'] = $nombre;
                    $_SESSION['pass'] = $pass;

                    //ahora comprobamos los regalos //
                    $entradas = $pru->getregentrada($_SESSION['nombre']);
                    $palo = $pru->getpalomitas($_SESSION['nombre']);
                    if ($entradas > 0 && $palo > 0) {
                        echo("<script> alert('Tiene " . $entradas . " entradas y " . $palo . " paquetes de palomitas de regalo');</script>");
                    } else if ($entradas > 0) {
                        echo("<script> alert('Tiene " . $entradas . " entradas  de regalo');</script>");
                    } else if ($palo > 0) {
                        echo("<script> alert('Tiene " . $palo . " paquetes de palomitas de regalo');</script>");
                    }

                    date_default_timezone_set('Europe/Madrid');
                    $diac = date("d");
                    $mc = date("m");
                    $yc = date("Y");
                    $fechab = $pru->getfecha($_SESSION['nombre']);
                    $mes = preg_split("/-/", $fechab);
                    $anop = $pru->getApremio($_SESSION['nombre']);
                    //echo("<script> alert('mes actual: ".$mc." año actual: ".$yc." año premio: ".$anop." mes cumple: ".$mes[1]."');</script>");
                    if ($mes[1] == $mc && $anop < $yc) {//si coinciden los meses y el año que se le dio el ultimo premio es menor que el actual
                        $pru->setEntrada($_SESSION['nombre'], "+", 1);
                        $pru->setPalomitas($_SESSION['nombre'], "+", 1);
                        $pru->setPremios($_SESSION['nombre'], "+", 2);
                        $pru->setanoPremio($_SESSION['nombre'], $yc);
                    }
                    $_SESSION['tiempo']=date("Hi");
                    echo ("<script>
                            window.location='compra.php';
                            </script>");
                }
            } else {
                echo("<script> alert('Tiene que completar todos los campos');</script>");
            }
        }
        if (isset($_POST['2'])) {
            $nombre = trim($_POST['usuario']);
            $pass = md5(trim($_POST['pass']));
            if ($nombre != "" && $pass != "") {
                $resultado = $pru->NoDuplicado($nombre);
                if (!$resultado) {
                    //$fecha = $_POST['ano'] . "-" . $_POST['mes'] . "-" . $_POST['dia'];
                    $fecha = $_POST['fechana'];
                    //echo("<script> alert('" . $fecha . "');</script>");
                    $dni = $_POST['dni'];
                    $sql = "INSERT INTO `usuarios`(`usuario`, `pass`,`dni`,`fecha`) VALUES ('" . $nombre . "','" . $pass . "','" . $dni . "','" . $fecha . "')";
                    echo("<script>alert('Usuario registrado');</script>");
                    $pru->hacerconsulta($sql);
                } else {
                    echo("<script>alert('Usuario existente,ingrese otro nombre de usuario');</script>");
                }
            } else {
                echo("<script> alert('Tiene que completar todos los campos');</script>");
            }
        }
        ?>
        <LINK REL=StyleSheet HREF="./css/estilo.css" TYPE="text/css" MEDIA=screen>
        <!-- para el datepicker -->
        <link rel="stylesheet" href="./js/themes/base/jquery.ui.all.css">
        <script src="./js/jquery-1.8.3.js"></script>
        <script src="./js/ui/jquery.ui.core.js"></script>
        <script src="./js/ui/jquery.ui.widget.js"></script>
        <script src="./js/ui/jquery.ui.datepicker.js"></script>
        <script src="./js/ui/i18n/jquery.ui.datepicker-es.js"></script>
        <script>
            /* $(function() {
                $.datepicker.setDefaults( $.datepicker.regional[ "" ] );
                $( "#datepicker" ).datepicker( $.datepicker.regional[ "es" ] );
                 $('#datepicker').datepicker('option', {dateFormat: 'yy-mm-dd'});
            });*/
            
            $(function(){
                $( "#datepicker" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '1940: 2013'
                }); 
                $('#datepicker').datepicker('option', {
                    dateFormat: 'yy-mm-dd'
                });
            });
	
            function fecha(valor){
                alert(valor);
            }
        </script>
    </head>
    <!--creado por Manuel Aguilar NAvarro -->
    <body>
        <?php
        setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp"); // establecemos la zona horaria
        echo strftime("%A %d de %B del %Y"); // mostramos el nombre del dia, el dia, "de", el nombre del mes ,"del", el año.
        ?>
        <div id="contenedor">
            <h1 style="margin: 0 auto;width: 50%;text-align: center;">FORMULARIO DE ACCESO</h1>
            <div id="formulario">
                <form method="POST" action="" id="formu">
                    <table id="login">
                        <tr>
                            <td>USUARIO:</td><td><input type="text" name="usuario"/></td>
                        </tr>
                        <tr>
                            <td>PASSWORD:</td><td><input type="password" name="pass"/></td>
                        </tr>
                        <tr>
                            <td><input type="submit" value="ENTRAR" name="1"/></td> 
                        </tr>
                    </table>
                </form>
            </div>
            <div id="formulario2">
                <form method="POST" action="" id="formu2">
                    <table id="login2">
                        <tr>
                            <td>USUARIO:</td><td><input type="text" name="usuario"/></td>
                        </tr>
                        <tr>
                            <td>DNI:</td><td><input type="text" name="dni"/></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Fecha Nacimiento: <input type="text" id="datepicker" name="fechana"/>
                                <!--Fecha Nacimiento:
                                <select name="dia">
                                <?php
                                /* date_default_timezone_set('Europe/Madrid');
                                  $diasmes = date("t");
                                  $diam = date("d");
                                  $mm = date("m");
                                  $yy = date("Y");
                                  for ($d = 1; $d <= $diasmes; $d++) {
                                  if ($d == $diam) {
                                  echo("<option value='" . $d . "' selected>" . $d . "</option>");
                                  } else {
                                  echo("<option value='" . $d . "'>" . $d . "</option>");
                                  }
                                  }
                                  ?>
                                  </select>
                                  <select name="mes">
                                  <?php
                                  for ($m = 1; $m <= 12; $m++) {
                                  if ($m == $mm) {
                                  echo("<option value='" . $m . "' selected>" . $m . "</option>");
                                  } else {
                                  echo("<option value='" . $m . "'>" . $m . "</option>");
                                  }
                                  }
                                  ?>
                                  </select>
                                  <select name="ano">
                                  <?php
                                  for ($a = 1950; $a <= 2050; $a++) {
                                  if ($a == $yy) {
                                  echo("<option value='" . $a . "' selected>" . $a . "</option>");
                                  } else {
                                  echo("<option value='" . $a . "'>" . $a . "</option>");
                                  }
                                  } */
                                ?>
                                </select>-->
                            </td>
                        </tr>
                        <tr>
                            <td>PASSWORD:</td><td><input type="password" name="pass"/></td>
                        </tr>
                        <tr>
                            <td><input type="submit" value="REGISTRAR" name="2"/></td>

                        </tr>
                    </table>
                </form>
                <script type="text/javascript">selectFecha(); </script>
            </div>
        </div><!-- fin  id contenedor -->
    </body>
</html>