<?php

class claseArchivo {

    protected $Arxiu;  // Punter a l'arxiu
    protected $Nom;    // El nom
    protected $Mode;   // El mode en que l'obrim
    public $Contingut;

    function __construct($pNom = 'classArxiu.php') {
        $this->Nom = $pNom;
    }

    /* para destruir la sala.....
      function __destruct () {
      fclose($this->Arxiu);
      echo unlink($this->Nom);
      echo "<br>--- Arxiu eliminat en tancar la sessi� ---";
      }
     */

    function Abrir($pMode = 'r+') {
        $this->Mode = $pMode;
        $this->Arxiu = fopen($this->Nom, $this->Mode);
        if (!$this->Arxiu) {
            echo "<br> No s'ha pogut obrir l'arxiu";
            return false;
        }
    }

    function Cerrar() {
        fclose($this->Arxiu); // echo "Arxiu Tancat";
    }

    function Leer() {
        rewind($this->Arxiu);
        echo "<hr>Contingut de l'arxiu: <b>" . $this->Nom . "</b><hr>";
        while (!feof($this->Arxiu)) {
            $this->Contingut = fgets($this->Arxiu);
            //$r = nl2br($this->Contingut);// esto añadia caracteres al string  NO UTILIZAR¡¡¡¡¡
            echo $this->Contingut . "<br>";
        }
    }

    function arrayLineas() {
        $this->Abrir("r");
        rewind($this->Arxiu);
        $a = array();
        while (!feof($this->Arxiu)) {
            $this->Contingut = fgets($this->Arxiu);
            //$r = nl2br($this->Contingut);// esto añadia caracteres al string  NO UTILIZAR¡¡¡¡¡
            $a[] = $this->Contingut;
        }
        return $a;
    }

    function leerLinea() {// lee una sola linea
        if (!feof($this->Arxiu)) {
            $this->Contingut = trim(fgets($this->Arxiu));
            return $this->Contingut;
        }
        else
            return false;
    }

    function Escribir($pCadena) {//escribe una linea
        fputs($this->Arxiu, trim($pCadena) . chr(13) . chr(10));
    }

    function Borrar() {//borra todo el contenido del archivo
        // Esborrem el contingut de l'arxiu
        $this->Arxiu = fopen($this->Nom, 'w+');
        // EL tanquem
        fclose($this->Arxiu);
    }

    function finArchivo() {
        if (feof($this->Arxiu))
            return true;
        else
            return false;
    }

}

//****************  clase para la gestion de salas con archivos txt ***********//

class gestCines extends claseArchivo {

    public $butacas = array();

    function __construct($pNom = 'classArxiu.php') {
        parent::__construct($pNom);
    }

    function iniciarSala() {//crea una sala si no existe y si existe la borra toda y pone a cero las butacas....
        $this->Abrir("w+");
        for ($i = 0; $i < 200; $i++) {
            $this->Escribir("0");
        }
        $this->Cerrar();
    }

    function compra() {// actualiza el estado de todas las butacas de una sala...
        $this->Abrir("w+");
        for ($i = 0; $i < count($this->butacas); $i++) {
            $this->Escribir($this->butacas[$i]);
        }
        $this->Cerrar();
    }

    function mostrarSala() {// muestra la sala por pantalla...
        $cont = 0;
        echo("<table><tr style='color:blue'><th></th>");
        for ($t = 1; $t <= 20; $t++) {
            echo("<th>b" . $t . "</th>");
        };
        echo("</tr>");
        for ($f = 0; $f < 10; $f++) {// de cero a 10 porque son las filas que queremos
            echo("<tr>");
            echo("<td style='color:blue'>f" . ($f + 1) . "</td>");
            for ($col = 0; $col < 20; $col++) {// de 0 a 20 porque son las columnas que queremos..
                if ($this->butacas[$cont] == 0) {// si la butaca esta vacia.....
                    echo("<td style='color:green'>");
                    echo("<form method='POST' id='$cont' name='but' action='' onmouseover='mostrarButaca(this)' onmouseout='ocultar()' style='margin:0'>");
                    echo(" <input type='hidden' name='datosb' value='f" . ($f + 1) . " - b" . ($col + 1) . "'/>");
                    echo(" <input type='hidden' name='buta' value='" . $cont . "'/>");
                    echo( "<input type='button' value='' style='background-image: url(\"./imagenes/libre.png\"); width: 30px; height: 31px;'/>");
                    echo("</form>");
                };
                if ($this->butacas[$cont] == 1) {// si la butaca esta ocupada....
                    echo("<td style='color:red'>");
                    echo("<form method='POST' name='but' action='' onmouseover='mostrarButaca(this)' onmouseout='ocultar()' style='margin:0'>");
                    echo(" <input type='hidden' name='datosb' value='f" . ($f + 1) . " - b" . ($col + 1) . "'/>");
                    echo( "<input type='button' name='" . $cont . "' style='background-image: url(\"./imagenes/ocupado.png\"); width: 30px; height: 31px;'/>");
                    echo("</form>");
                };
                $cont++;
                echo("</td>");
            };
            echo("</tr>");
        };

        echo("</table>");
    }

}

//****************  clase principal conexion a base de datos  ***********//

class PdoConexion {

    protected $dbbase; // = "mysql";
    protected $servidor; // = "localhost";
    protected $usuario; // = "manuel";
    protected $pass; // = "12345";
    protected $db_name; // = "prueba";
    protected $cnx;
    private $resultado;

    //constructor.....
    public function __construct($dbase, $servidor, $usuario, $pass, $dbname) {
        $this->dbbase = $dbase;
        $this->servidor = $servidor;
        $this->usuario = $usuario;
        $this->pass = $pass;
        $this->db_name = $dbname;


        try {
            $this->cnx = new PDO($this->dbbase . ":host=" . $this->servidor . ";dbname=" . $this->db_name, $this->usuario, $this->pass, array(PDO::ATTR_PERSISTENT => true));
            return $this->cnx;
        } catch (PDOException $ex) {
            echo ("Conexion Error" . $ex->getMessage());
        }
    }

    //*****************funciones para la aplicacion cines... a partir de aqui funciones especificas para gestcine

    public function todosLosDatos() {//funcion que muestra todos los datos....
        try {
            $sql = "select * from usuarios;"; //consulta
            $this->resultado = $this->cnx->query($sql) or die($sql); //el resultado de la consulta se lo metemos al atributo
            $this->resultado->setFetchMode(PDO::FETCH_ASSOC); //para mostrar solo los resultados asociativos y no los numericos
            foreach ($this->resultado as $fila) {// recorremos el array resultante
                /* aqui si quisieramos sabiendo que este foreach nos devuelve un array con todos los campos
                 * es decir Array ( [usuario] => lito [pass] => 827ccb0eea8a706c4c34a16891f84e7b [estado] => A
                 *  [puntos] => 30 [regentrada] => 6 [regpalomitas]
                 *  => 12 [premios] => 18 [dni] => 234234234 [fecha] => 2012-11-23 ).
                 * si aqui quisieramos ver los puntos hariamos lo siguiente:
                 *     echo($fila[puntos]); 
                 */
                foreach ($fila as $key => $value) {

                    echo($key . "=" . $value . "<br>");
                    /*
                     * si quisieramos ver solo el usuario hariamos esto.... 
                     *  if ($key == "usuario") {
                      echo($key . "  = " . $value . "<br>"); // imprimimos
                      } */
                }
            }
        } catch (PDOException $ex) {
            echo ("Conexion Error" . $ex->getMessage());
        }
    }

    public function Validar($nombre, $pass) {// valida nombre y usuario devolviendo true si es correcto....
        try {
            $sql = "SELECT * FROM `usuarios` WHERE `usuario` = :nom and `pass`= :pass";
            $this->resultado = $this->cnx->prepare($sql); //preparamos la consulta....
            $this->resultado->execute(array(':nom' => $nombre, ':pass' => $pass)); //el resultado de la consulta se lo metemos al atributo
            $row = $this->resultado->fetch(PDO::FETCH_ASSOC); // metemos en row solo los resultados asociativos (nombre= manuel)
            if (!empty($row)) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $ex) {
            echo ("Conexion Error" . $ex->getMessage());
        }
    }

    public function NoDuplicado($nombre) {// comprueba que no tenga dos iguales....
        try {
            $sql = "SELECT * FROM `usuarios` WHERE `usuario` = :nom";
            $this->resultado = $this->cnx->prepare($sql) or die($sql); //el resultado de la consulta se lo metemos al atributo
            $this->resultado->execute(array(':nom' => $nombre));
            $row = $this->resultado->fetch(PDO::FETCH_ASSOC); // metemos en row solo los resultados asociativos (nombre= manuel)
            if (!empty($row)) {// si hay algun resultado.....
                return true; // devuelve true si existe....
            } else {
                return false;
            }
        } catch (PDOException $ex) {
            echo ("Conexion Error" . $ex->getMessage());
        }
    }

    function verDatosUsuario($nombre) {// muestra todos los datos de un usuario en concreto....
        try {
            $sql = "SELECT * FROM `usuarios` WHERE `usuario` = :nom";
            $this->resultado = $this->cnx->prepare($sql) or die($sql);
            $this->resultado->execute(array(':nom' => $nombre)); // ejecutamos la consulta
            $row = $this->resultado->fetch(PDO::FETCH_ASSOC); // metemos en row solo los resultados asociativos (nombre= manuel)
            foreach ($row as $key => $value) {// recorremos el array resultante
                echo($key . "  = " . $value . "<br>"); // imprimimos
            }
            //print_r($row);
        } catch (PDOException $ex) {
            echo ("Conexion Error" . $ex->getMessage());
        }
    }

    function getpuntos($nombre) {// devuelve los puntos del usuario..
        try {
            $sql = "SELECT puntos FROM `usuarios` WHERE `usuario` = :nom";
            $this->resultado = $this->cnx->prepare($sql) or die($sql);
            $this->resultado->execute(array(':nom' => $nombre)); // ejecutamos la consulta
            $row = $this->resultado->fetch(PDO::FETCH_ASSOC); // metemos en row solo los resultados asociativos (nombre= manuel)
            foreach ($row as $key => $value) {// recorremos el array resultante
                return $value; // imprimimos
            }
        } catch (PDOException $ex) {
            echo ("Conexion Error" . $ex->getMessage());
        }
    }

    function getregentrada($nombre) {// devuelve entradas de regalo del usuario..
        try {
            $sql = "SELECT regentrada FROM `usuarios` WHERE `usuario` = :nom";
            $this->resultado = $this->cnx->prepare($sql) or die($sql);
            $this->resultado->execute(array(':nom' => $nombre)); // ejecutamos la consulta
            $row = $this->resultado->fetch(PDO::FETCH_ASSOC); // metemos en row solo los resultados asociativos (nombre= manuel)
            foreach ($row as $key => $value) {// recorremos el array resultante
                return $value; // imprimimos
            }
        } catch (PDOException $ex) {
            echo ("Conexion Error" . $ex->getMessage());
        }
    }

    function getpalomitas($nombre) {// devuelve palomitas de regalo del usuario..
        try {
            $sql = "SELECT regpalomitas FROM `usuarios` WHERE `usuario` = :nom";
            $this->resultado = $this->cnx->prepare($sql) or die($sql);
            $this->resultado->execute(array(':nom' => $nombre)); // ejecutamos la consulta
            $row = $this->resultado->fetch(PDO::FETCH_ASSOC); // metemos en row solo los resultados asociativos (nombre= manuel)
            foreach ($row as $key => $value) {// recorremos el array resultante
                return $value; // imprimimos
            }
        } catch (PDOException $ex) {
            echo ("Conexion Error" . $ex->getMessage());
        }
    }

    function getfecha($nombre) {// devuelve fecha de nacimiento  del usuario..
        try {
            $sql = "SELECT fecha FROM `usuarios` WHERE `usuario` = :nom";
            $this->resultado = $this->cnx->prepare($sql) or die($sql);
            $this->resultado->execute(array(':nom' => $nombre)); // ejecutamos la consulta
            $row = $this->resultado->fetch(PDO::FETCH_ASSOC); // metemos en row solo los resultados asociativos (nombre= manuel)
            foreach ($row as $key => $value) {// recorremos el array resultante
                return $value; // imprimimos
            }
        } catch (PDOException $ex) {
            echo ("Conexion Error" . $ex->getMessage());
        }
    }

    function getpremios($nombre) {// devuelve premios acumulados del usuario..
        try {
            $sql = "SELECT premios FROM `usuarios` WHERE `usuario` = :nom";
            $this->resultado = $this->cnx->prepare($sql) or die($sql);
            $this->resultado->execute(array(':nom' => $nombre)); // ejecutamos la consulta
            $row = $this->resultado->fetch(PDO::FETCH_ASSOC); // metemos en row solo los resultados asociativos (nombre= manuel)
            foreach ($row as $key => $value) {// recorremos el array resultante
                return $value; // imprimimos
            }
        } catch (PDOException $ex) {
            echo ("Conexion Error" . $ex->getMessage());
        }
    }
    
      function getPeli($nombre) {// devuelve premios acumulados del usuario..
        try {
            $sql = "SELECT pelicula FROM `salas` WHERE `id` = :nom";
            $this->resultado = $this->cnx->prepare($sql) or die($sql);
            $this->resultado->execute(array(':nom' => $nombre)); // ejecutamos la consulta
            $row = $this->resultado->fetch(PDO::FETCH_ASSOC); // metemos en row solo los resultados asociativos (nombre= manuel)
            foreach ($row as $key => $value) {// recorremos el array resultante
                return $value; // imprimimos
            }
        } catch (PDOException $ex) {
            echo ("Conexion Error" . $ex->getMessage());
        }
    }

    function getApremio($nombre) {// devuelve premios acumulados del usuario..
        try {
            $sql = "SELECT anopremio FROM `usuarios` WHERE `usuario` = :nom";
            $this->resultado = $this->cnx->prepare($sql) or die($sql);
            $this->resultado->execute(array(':nom' => $nombre)); // ejecutamos la consulta
            $row = $this->resultado->fetch(PDO::FETCH_ASSOC); // metemos en row solo los resultados asociativos (nombre= manuel)
            foreach ($row as $key => $value) {// recorremos el array resultante
                return $value; // imprimimos
            }
        } catch (PDOException $ex) {
            echo ("Conexion Error" . $ex->getMessage());
        }
    }

    function getSalas() {// devuelve premios acumulados del usuario..
        try {
            $sql = "SELECT salas FROM `usuarios` WHERE `usuario` = :nom";
            $this->resultado = $this->cnx->prepare($sql) or die($sql);
            $this->resultado->execute(array(':nom' => "manuel")); // ejecutamos la consulta
            $row = $this->resultado->fetch(PDO::FETCH_ASSOC); // metemos en row solo los resultados asociativos (nombre= manuel)
            foreach ($row as $key => $value) {// recorremos el array resultante
                return $value; // imprimimos
            }
        } catch (PDOException $ex) {
            echo ("Conexion Error" . $ex->getMessage());
        }
    }

    function setSalas($salitas) {//aumenta o disminuye el numero de palomitas regaladas..
        $sql = "UPDATE `usuarios` SET `salas`= :salas";
        $this->resultado = $this->cnx->prepare($sql);
        $this->resultado->execute(array(':salas' => $salitas));
    }

    //UPDATE `usuarios` SET `salas`= 7
    function setEntrada($nombre, $operacion, $cantidad) {//aumenta o disminuye el numerto de entradas regaladas
        if ($operacion == '+') {
            $sql = "UPDATE `usuarios` SET `regentrada`=`regentrada` + :cant where `usuario`= :nom";
        } else {
            $sql = "UPDATE `usuarios` SET `regentrada`=`regentrada` - :cant where `usuario`= :nom";
        }
        $this->resultado = $this->cnx->prepare($sql);
        $this->resultado->execute(array(':nom' => $nombre, ':cant' => $cantidad));
    }

    function setPalomitas($nombre, $operacion, $cantidad) {//aumenta o disminuye el numero de palomitas regaladas..
        if ($operacion == '+') {
            $sql = "UPDATE `usuarios` SET `regpalomitas`=`regpalomitas` + :cant where `usuario`= :nom";
        } else {
            $sql = "UPDATE `usuarios` SET `regpalomitas`=`regpalomitas` - :cant where `usuario`= :nom";
        }
        $this->resultado = $this->cnx->prepare($sql);
        $this->resultado->execute(array(':nom' => $nombre, ':cant' => $cantidad));
    }

    function setPremios($nombre, $operacion, $cantidad) {//aumenta o disminuye el numero de palomitas regaladas..
        if ($operacion == '+') {
            $sql = "UPDATE `usuarios` SET `premios`=`premios` + :cant where `usuario`= :nom";
        } else {
            $sql = "UPDATE `usuarios` SET `premios`=`premios` - :cant where `usuario`= :nom";
        }
        $this->resultado = $this->cnx->prepare($sql);
        $this->resultado->execute(array(':nom' => $nombre, ':cant' => $cantidad));
    }

    function setPuntos($nombre, $operacion) {//aumenta o disminuye el numero de palomitas regaladas..
        if ($operacion == '+') {
            $sql = "UPDATE `usuarios` SET `puntos`=`puntos` + 10 where `usuario`= :nom";
        } else {
            $sql = "UPDATE `usuarios` SET `puntos` = 0 where `usuario`= :nom";
        }
        $this->resultado = $this->cnx->prepare($sql);
        $this->resultado->execute(array(':nom' => $nombre));
    }

    function setanoPremio($nombre, $ano) {//guardamos el ultimo año que se le da un premio por cumpleaños
        $sql = "UPDATE `usuarios` SET `anopremio`= :ano where `usuario`= :nom";
        $this->resultado = $this->cnx->prepare($sql);
        $this->resultado->execute(array(':ano' => $ano, ':nom' => $nombre));
    }

    /*
      function verConsulta($sql) {// muestra consulta que se le pasa por parametro......
      try {
      $this->resultado = $this->cnx->query($sql) or die($sql); // ejecutamos la consulta
      $row = $this->resultado->fetch(PDO::FETCH_ASSOC); // metemos en row solo los resultados asociativos (nombre= manuel)
      foreach ($row as $key => $value) {// recorremos el array resultante
      echo($key . "  = " . $value . "<br>"); // mostramos el resultado de la consulta....
      }
      //print_r($row);
      } catch (PDOException $ex) {
      echo ("Conexion Error" . $ex->getMessage());
      }
      }
     */

    function hacerconsulta($sql) {// para insertar o modificar.... no devuelve nada....
        try {
            $this->resultado = $this->cnx->query($sql) or die($sql); // ejecutamos la consulta
        } catch (PDOException $ex) {
            echo ("Conexion Error" . $ex->getMessage());
        }
    }

    function compra() {

        if ($this->getregentrada($_SESSION['nombre']) > 0) {// si hay alguna entrada de regalo
            $this->setentrada($_SESSION['nombre'], '-', 1); // restamos una entrada y no aumentamos los puntos
            echo("<script>alert('ENHORABUENA AQUI TIENES TU ENTRADA DE REGALO');</script>");
        } else {
            // insertamos 10 puntos al usuario
            $this->setPuntos($_SESSION['nombre'], '+');
            // averiguamos cuantos puntos tiene despues de la compra
            $punt = $this->getpuntos($_SESSION['nombre']);

            if ($punt == 100) {//si puntos es igual a 100
                $this->setPremios($_SESSION['nombre'], '+', 1); //aumentamos en 1 los premios recibidos
                $this->setentrada($_SESSION['nombre'], '+', 1); //sumamos una entrada
                $this->setPuntos($_SESSION['nombre'], '-');
            }
        }

        // ahora las palomitas //

        if ($this->getpalomitas($_SESSION['nombre']) > 0) {//si hay algun paquete de palomitas de regalo
            $this->setPalomitas($_SESSION['nombre'], '-', 1); //restamos un paquete de palomitas
            echo("<script>alert('ENHORABUENA AQUI TIENES TU PAQUETE DE PALOMITAS  DE REGALO');</script>");
        } else {
            if ($punt == 50) {
                $this->setPalomitas($_SESSION['nombre'], '+', 1); //aumentamos en 1 las palomitas
                $this->setPremios($_SESSION['nombre'], '+', 1); //aumentamos en 1 los premios recibidos
            }
            if ($punt == 100) {
                $this->setPalomitas($_SESSION['nombre'], '+', 1); //aumentamos en 1 las palomitas
                $this->setPremios($_SESSION['nombre'], '+', 1); //aumentamos en 1 los premios recibidos
            }
        }
    }

    function Desconexion() {
        $this->cnx = null;
    }

}

?>
