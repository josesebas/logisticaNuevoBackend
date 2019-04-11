<?php 

	date_default_timezone_set("America/Mexico_City");
	require_once 'vendor/autoload.php';
	require("includes/class.phpmailer.php");
	require("includes/class.smtp.php");
    require('piramide-uploader/PiramideUploader.php');
    require('includes/SED.php');
    require('correo_operador.php');
	
	$app = new \Slim\Slim();

	/*$db = new PDO("mysql:host=localhost; dbname=gdlvanco_ventas; charset=utf8;", 'gdlvanco_gdlvan', 'ctWOV(Ww!-9(');
    $db2 = new PDO("mysql:host=localhost; dbname=gdlvanco_cotizador; charset=utf8;", 'gdlvanco_coti', 'U0kX[_=V&iM9');
    $db3 = new PDO("mysql:host=localhost; dbname=gdlvanco_logistica; charset=utf8;", 'gdlvanco_logisti', '}D7[H^(lwJ$@');*/
    $db_gdlvan = new PDO("mysql:host=localhost; dbname=gdlvanco_ventas; charset=utf8;", 'root', '');
    $db_cotizador = new PDO("mysql:host=localhost; dbname=gdlvanco_cotizador; charset=utf8;", 'root', '');
    $db_logistica = new PDO("mysql:host=localhost; dbname=gdlvanco_logistica; charset=utf8;", 'root', '');

	//Configuracion de cabeceras
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Allow: GET, POST, OPTIONS, PUT, DELETE");
	$method = $_SERVER['REQUEST_METHOD'];
	if($method == "OPTIONS") {
	    die();
	}
    $app->get('/verify', function() use($app, $db_gdlvan, $db_logistica){
        $json = file_get_contents('http://www.gmail.com/');
         echo json_encode($json);
    });
    $app->get('/llenar_dbventas', function() use($app, $db_gdlvan, $db_logistica){


        //agregar a viaje campo id_unidad
        //agregar a viaje campo id_operador
        //agregar a viaje campo iniciado
        //agregar a viaje campo cerrado
        //agregar a viaje campo terminado
        //agregar a viaje campo correo_enviado72
        //agregar a viaje campo correo_enviado24
        //agregar a viaje campo cliente_contactado
        //agregar a viaje campo cliente_contactado2
        //agregar a viaje campo firma_cliente
        //agregar a viaje campo cliente_verifico

        //agregar a movimientos campo viaje_div
        /*-----------------------------------------------------------------------------------------------
        

        Se le agrega el campo de viaje_div para poder ubicar si el id_viaje es del viaje original o si es de una division
        para cuando hagamos las consultas relacionadas hagamos
            SELECT * from tabla left join viajes_divididos left join viaje         si el campo viaje_div tiene (letra o id)
            SELECT * from tabla left join viaje                                 si el campo viaje_div no tiene (letra o id)


        -------------------------------------------------------------------------------------------------*/
        //agregar a encuestas campo viaje_div
        //agregar a firmas campo viaje_div
        //agregar a gastos campo viaje_div
        //agregar a mails_cupones campo viaje_div
        //agregar a reporte_viajes campo viaje div
        //agregar a rastreo campo viaje_div
        //agregar a movimientos campo viaje_div
        //agregar a solicitud gasolina campo viaje_div
        /*--------------------------------------------------------------------------------------------------


    
        AGREGAR VIAJES DIVIDIDOS A Ventas



        ----------------------------------------------------------------------------------------------------
        actualizar logistica
        */
        $sql = "SELECT * FROM viajes ORDER by id asc";
        $response=$db_logistica->prepare($sql);
        $response->execute();
        $viajes = $response->fetchAll(PDO::FETCH_ASSOC);
        //print_r($viajes);
        $i = 0;
        $tam = sizeof($viajes);
        //hacer explode y cechar los que son mayores son los que son viajes divididos sino son normales
        //agregar a tabla de viajes divididos solo su primer aparicion
        while ($i < $tam) {
            $contrato = explode('-',$viajes[$i]['contrato']);
             $select = "SELECT viaje.id from viaje LEFT JOIN contrato on contrato.id = viaje.id_contrato WHERE contrato.num_contrato like '".$contrato[0]."-".$contrato[1]."-".$contrato[2]."%' ";
                    $response = $db_gdlvan->prepare($select);
                    $response->execute();
                    $id_viaje_analizado = $response->fetch(PDO::FETCH_ASSOC);
            if(sizeof($contrato)>4){//valida si es una division
                echo "division";
                print_r($contrato);
                //buscar id en ventas con num_contrato en logistica
               
                echo $id_viaje_analizado['id'];
                //buscar divisiones
                $sql = "SELECT * from viajes_divididos LEFT JOIN viaje on viajes_divididos.id_viaje = viaje.id LEFT JOIN contrato on contrato.id = viaje.id_contrato WHERE contrato.num_contrato like '".$contrato[0]."-".$contrato[1]."-".$contrato[2]."-".$contrato[3]."%'";
                echo $sql;
                $response = $db_gdlvan->prepare($sql);
                $response->execute();
                $viajes_div = $response->fetchAll(PDO::FETCH_ASSOC);
                echo sizeof($viajes_div);
                //  insertar en ventas
                if (sizeof($viajes_div)==0) {
                    
                    $insert = "INSERT INTO viajes_divididos (
                                                        consecutivo,
                                                        id_unidad,
                                                        id_operador,
                                                        iniciado,
                                                        cerrado,
                                                        terminado,
                                                        cliente_contactado,
                                                        cliente_contactado2,
                                                        firma_div,
                                                        cliente_verifico,
                                                        id_viaje
                                                        )
                                            VALUES(
                                                    'A',
                                                    ".$viajes[$i]['unidad'].",
                                                    ".$viajes[$i]['operador'].",
                                                    ".$viajes[$i]['iniciado'].",
                                                    ".$viajes[$i]['cerrado'].",
                                                    ".$viajes[$i]['terminado'].",
                                                    ".$viajes[$i]['cliente_contactado'].",
                                                    ".$viajes[$i]['cliente_contactado2'].",
                                                    '".$viajes[$i]['firma_cliente']."',
                                                    '".$viajes[$i]['cliente_verifico']."',
                                                    ".$id_viaje_analizado['id']."
                                                )"; 
                    echo $insert;  
                    $response = $db_gdlvan->prepare($insert);
                    $response->execute();
                    $id_divido = $db_gdlvan->lastInsertId();

                    //actualizamos LOGISTICA  con el id de viajes_div
                    $updateLog = "UPDATE encuestas  set viaje_div = ".$id_divido." WHERE encuestas.viaje = ".$viajes[$i]['id'];
                    $response = $db_logistica->prepare($updateLog);
                    $response->execute();

                    $updateLog = "UPDATE firmas  set viaje_div = ".$id_divido." WHERE firmas.id_viaje = ".$viajes[$i]['id'];
                    $response = $db_logistica->prepare($updateLog);
                    $response->execute();

                    $updateLog = "UPDATE gastos  set viaje_div = ".$id_divido." WHERE gastos.id_viaje = ".$viajes[$i]['id'];
                    $response = $db_logistica->prepare($updateLog);
                    $response->execute();

                    $updateLog = "UPDATE mails_cupones  set viaje_div = ".$id_divido." WHERE mails_cupones.id_viajes = ".$viajes[$i]['id'];
                    $response = $db_logistica->prepare($updateLog);
                    $response->execute();

                    $updateLog = "UPDATE rastreo  set viaje_div = ".$id_divido." WHERE rastreo.id_viaje = ".$viajes[$i]['id'];
                    $response = $db_logistica->prepare($updateLog);
                    $response->execute();

                    $updateLog = "UPDATE reporte_viajes  set viaje_div = ".$id_divido." WHERE reporte_viajes.id_viaje = ".$viajes[$i]['id'];
                    $response = $db_logistica->prepare($updateLog);
                    $response->execute();

                    $updateLog = "UPDATE solicitud_gasolina  set viaje_div = ".$id_divido." WHERE solicitud_gasolina.id_viaje = ".$viajes[$i]['id'];
                    $response = $db_logistica->prepare($updateLog);
                    $response->execute();
                    echo "agregar y actualizar<br>";

                }
            echo "<br>";
            }else if(count($contrato)==4){
                //echo "bien<br>";
                $update = "UPDATE viaje LEFT JOIN contrato on contrato.id = viaje.id_contrato 
                            SET viaje.sucursal = ".$viajes[$i]['sucursal'].",
                                viaje.id_unidad = ".$viajes[$i]['unidad'].",
                                viaje.id_operador = ".$viajes[$i]['operador'].",
                                iniciado = ".$viajes[$i]['iniciado'].",
                                cerrado = ".$viajes[$i]['cerrado'].",
                                terminado = ".$viajes[$i]['terminado'].",
                                correo_enviado72 = ".$viajes[$i]['correo_enviado72'].",
                                correo_enviado24 = ".$viajes[$i]['correo_enviado24'].",
                                cliente_contactado = ".$viajes[$i]['cliente_contactado'].",
                                cliente_contactado2 = ".$viajes[$i]['cliente_contactado2'].",
                                firma_cliente = '".$viajes[$i]['firma_cliente']."',
                                cliente_verifico = '".$viajes[$i]['cliente_verifico']."' 
                            WHERE viaje.id = ".$id_viaje_analizado['id']."
                                ";
                #echo $update;
                //print_r($contrato);
                //echo "<br>";
                $response = $db_gdlvan->prepare($update);
                $response->execute();
                echo "actualizar viaje ".$id_viaje_analizado."<br>";
            }else if(coun($contrato)<4){
                echo "errores <br>";
            }
            
            $i++;
        }

        /*
        ----------------------------------------------------------------------------------------------------*/
        /*------------------------------------------------------------------------------------------------------
        ----------------                                                                        ----------------
        ----------------                                                                        ----------------
        ----------------                                                                        ----------------
        ---------------- importar exepto viajes y movimientos tablas y luego estas consultas    ----------------
        ----------------                                                                        ----------------
        ----------------                                                                        ----------------
        ----------------                                                                        ----------------
        ----------------                                                                        ----------------
        ----------------                                                                        ----------------
        ----------------------------------------------------------------------------------------------------------*/
        /*---------------------------------------ACTUALIZAR encuestas---------------------------------------------------*/
    });

    $app->get('/llenar_dbventas2', function() use($db_gdlvan, $db_logistica,$app){
        $sql = "SELECT encuestas.id, viajes.contrato, encuestas.viaje_div FROM encuestas LEFT JOIN viajes on viajes.id = encuestas.viaje";
        $response = $db_logistica->prepare($sql);
        $response->execute();
        $encuestas = $response->fetchAll(PDO::FETCH_ASSOC);
        $i = 0;
        $tam = sizeof($encuestas);
        while ($i<$tam) {
            //contrato
            $contrato = explode("-",$encuestas[$i]['contrato']);
            //print_r($contrato);
            $contrato =  $contrato[0]."-".$contrato[1];
            //buscar viaje en ventas
            $busqueda="";
            if ($encuestas[$i]['viaje_div']>0) {
                 $busqueda = "SELECT viajes_divididos.id, viajes_divididos.id_viaje from viajes_divididos where id = ".$encuestas[$i]['viaje_div'];
            }else{
                 $busqueda = "SELECT viaje.id from viaje LEFT JOIN contrato on contrato.id = viaje.id_contrato WHERE num_contrato like '".$contrato."%' ";

            }
            echo $busqueda."<br>";
            $response=$db_gdlvan->prepare($busqueda);
            $response->execute();
            $viaje = $response->fetch(PDO::FETCH_ASSOC);
            //echo $viaje['id'];
            //actualizar en encuestas ventas
            echo $encuestas[$i]['viaje_div']."<br>";

            print_r($viaje);
            $update="";
            if ($encuestas[$i]['viaje_div']>0) {
                echo "si division<br>";
                $update = "UPDATE encuestas set viaje = ".$viaje['id_viaje'].", viaje_div = ".$viaje['id']." WHERE id = ".$encuestas[$i]['id'];
            }else{
                echo "no division<br>";
                 $update = "UPDATE encuestas set viaje = ".$viaje['id']." WHERE id = ".$encuestas[$i]['id'];

            }
            $response=$db_gdlvan->prepare($update);
            $response->execute();
            echo $update."<br>";
            $i++;
        }
        /*---------------------------------------ACTUALIZAR firmas---------------------------------------------------*/
        $sql = "SELECT firmas.id, viajes.contrato, firmas.viaje_div FROM firmas LEFT JOIN viajes on viajes.id = firmas.id_viaje";
        $response=$db_logistica->prepare($sql);
        $response->execute();
        $firmas = $response->fetchAll(PDO::FETCH_ASSOC);
        $i=0;
        $tam = sizeof($firmas);
        while ($i<$tam) { 
            $contrato = explode("-",$firmas[$i]['contrato']);
            $contrato =  $contrato[0]."-".$contrato[1];       
           //    echo $contrato."<br>";
             $busqueda="";
            if ($firmas[$i]['viaje_div']>0) {
                 $busqueda = "SELECT viajes_divididos.id, viajes_divididos.id_viaje from viajes_divididos where id = ".$firmas[$i]['viaje_div'];
            }else{
                 $busqueda = "SELECT viaje.id from viaje LEFT JOIN contrato on contrato.id = viaje.id_contrato WHERE num_contrato like '".$contrato."%' ";

            }
            
            $response=$db_gdlvan->prepare($busqueda);
            $response->execute();
            $viaje = $response->fetch(PDO::FETCH_ASSOC);
            //echo $viaje['id'];
            //actualizar en encuestas ventas
            $update="";
            if ($encuestas[$i]['viaje_div']>0) {
                echo " si division<br>";
                $update = "UPDATE firmas set id_viaje = ".$viaje['id_viaje'].", viaje_div = ".$viaje['id']." WHERE id = ".$firmas[$i]['id'];
            }else{
                echo "no division<br>";
                 $update = "UPDATE firmas set id_viaje = ".$viaje['id']." WHERE id = ".$firmas[$i]['id'];

            }
            $response=$db_gdlvan->prepare($update);
            $response->execute();
            echo $update."<br>";
            $i++;
        }
        /*---------------------------------------ACTUALIZAR gastos---------------------------------------------------*/
        $sql = "SELECT gastos.id, viajes.contrato, gastos.viaje_div FROM gastos LEFT JOIN viajes on viajes.id = gastos.id_viaje";
        $response=$db_logistica->prepare($sql);
        $response->execute();
        $data = $response->fetchAll(PDO::FETCH_ASSOC);
        $i=0;
        $tam = sizeof($data);
        while ($i<$tam) { 
            $contrato = explode("-",$data[$i]['contrato']);
            $contrato =  $contrato[0]."-".$contrato[1];       
            //echo $contrato."<br>";
            $busqueda;
            if ($data[$i]['viaje_div']>0) {
                 $busqueda = "SELECT viajes_divididos.id, viajes_divididos.id_viaje from viajes_divididos where id = ".$data[$i]['viaje_div'];
            }else{
                 $busqueda = "SELECT viaje.id from viaje LEFT JOIN contrato on contrato.id = viaje.id_contrato WHERE num_contrato like '".$contrato."%' ";
            }
            
            $response=$db_gdlvan->prepare($busqueda);
            $response->execute();
            $viaje = $response->fetch(PDO::FETCH_ASSOC);
            //echo $viaje['id'];
            //actualizar en encuestas ventas
            $update="";
            if ($data[$i]['viaje_div']>0) {
                echo "si division<br>";
                $update = "UPDATE gastos set id_viaje = ".$viaje['id_viaje'].", viaje_div = ".$viaje['id']." WHERE id = ".$data[$i]['id'];
            }else{
                echo "no division<br>";
                 $update = "UPDATE gastos set id_viaje = ".$viaje['id']." WHERE id = ".$data[$i]['id'];

            }
            $response=$db_gdlvan->prepare($update);
            $response->execute();
            echo $update."<br>";
            $i++;
        }
        /*---------------------------------------ACTUALIZAR mails_cupones---------------------------------------------------*/
        $sql = "SELECT mails_cupones.id, viajes.contrato, mails_cupones.viaje_div FROM mails_cupones LEFT JOIN viajes on viajes.id = mails_cupones.id_viajes";
        $response=$db_logistica->prepare($sql);
        $response->execute();
        $data = $response->fetchAll(PDO::FETCH_ASSOC);
        $i=0;
        $tam = sizeof($data);

        while ($i<$tam) { 
            $contrato = explode("-",$data[$i]['contrato']);
            $contrato =  $contrato[0]."-".$contrato[1];       
            echo $contrato."<br>";
            $busqueda;
            if ($data[$i]['viaje_div']>0) {
                 $busqueda = "SELECT viajes_divididos.id, viajes_divididos.id_viaje from viajes_divididos where id = ".$data[$i]['viaje_div'];
            }else{
                 $busqueda = "SELECT viaje.id from viaje LEFT JOIN contrato on contrato.id = viaje.id_contrato WHERE num_contrato like '".$contrato."%' ";
            }
            $response=$db_gdlvan->prepare($busqueda);
            $response->execute();
            $viaje = $response->fetch(PDO::FETCH_ASSOC);
            //echo $viaje['id'];
            //actualizar en encuestas ventas
            $update="";
            if ($data[$i]['viaje_div']>0) {
                echo "si division<br>";
                $update = "UPDATE mails_cupones set id_viaje = ".$viaje['id_viaje'].", viaje_div = ".$viaje['id']." WHERE id = ".$data[$i]['id'];
            }else{
                echo "no division<br>";
                 $update = "UPDATE mails_cupones set id_viaje = ".$viaje['id']." WHERE id = ".$data[$i]['id'];

            }
            $response=$db_gdlvan->prepare($update);
            $response->execute();
            echo $update."<br>";
            
            $i++;
        }
        /*-----------------------------------ACTUALIZAR reporte_viajes-----------------------------------------------------------*/
        $sql = "SELECT reporte_viajes.id, viajes.contrato, reporte_viajes.viaje_div FROM reporte_viajes LEFT JOIN viajes on viajes.id = reporte_viajes.id_viaje";
        $response=$db_logistica->prepare($sql);
        $response->execute();
        $data = $response->fetchAll(PDO::FETCH_ASSOC);
        $i=0;
        $tam = sizeof($data);

        while ($i<$tam) { 
            $contrato = explode("-",$data[$i]['contrato']);
            $contrato =  $contrato[0]."-".$contrato[1];       
            //echo $contrato."<br>";
            $busqueda;
            if ($data[$i]['viaje_div']>0) {
                 $busqueda = "SELECT viajes_divididos.id, viajes_divididos.id_viaje from viajes_divididos where id = ".$data[$i]['viaje_div'];
            }else{
                 $busqueda = "SELECT viaje.id from viaje LEFT JOIN contrato on contrato.id = viaje.id_contrato WHERE num_contrato like '".$contrato."%' ";
            }
            $response=$db_gdlvan->prepare($busqueda);
            $response->execute();
            $viaje = $response->fetch(PDO::FETCH_ASSOC);
            //echo $viaje['id'];
            //actualizar en encuestas ventas
            $update="";
            if ($data[$i]['viaje_div']>0) {
                echo "si division<br>";
                $update = "UPDATE reporte_viajes set id_viaje = ".$viaje['id_viaje'].", viaje_div = ".$viaje['id']." WHERE id = ".$data[$i]['id'];
            }else{
                echo "no division<br>";
                 $update = "UPDATE reporte_viajes set id_viaje = ".$viaje['id']." WHERE id = ".$data[$i]['id'];

            }
            $response=$db_gdlvan->prepare($update);
            $response->execute();
            echo $update."<br>";
            
            $i++;
        }
        /*---------------------------------------ACTUALIZAR rastreo---------------------------------------------------*/
        $sql = "SELECT rastreo.id, viajes.contrato, rastreo.viaje_div FROM rastreo LEFT JOIN viajes on viajes.id = rastreo.id_viaje";
        $response=$db_logistica->prepare($sql);
        $response->execute();
        $data = $response->fetchAll(PDO::FETCH_ASSOC);
        $i=0;
        $tam = sizeof($data);

        while ($i<$tam) { 
            $contrato = explode("-",$data[$i]['contrato']);
            $contrato =  $contrato[0]."-".$contrato[1];       
            //echo $contrato."<br>";
            $busqueda;
            if ($data[$i]['viaje_div']>0) {
                 $busqueda = "SELECT viajes_divididos.id, viajes_divididos.id_viaje from viajes_divididos where id = ".$data[$i]['viaje_div'];
            }else{
                 $busqueda = "SELECT viaje.id from viaje LEFT JOIN contrato on contrato.id = viaje.id_contrato WHERE num_contrato like '".$contrato."%' ";
            }
            $response=$db_gdlvan->prepare($busqueda);
            $response->execute();
            $viaje = $response->fetch(PDO::FETCH_ASSOC);
            //echo $viaje['id'];
            //actualizar en encuestas ventas
            $update="";
            if ($data[$i]['viaje_div']>0) {
                echo "si division<br>";
                $update = "UPDATE rastreo set id_viaje = ".$viaje['id_viaje'].", viaje_div = ".$viaje['id']." WHERE id = ".$data[$i]['id'];
            }else{
                echo "no division<br>";
                 $update = "UPDATE rastreo set id_viaje = ".$viaje['id']." WHERE id = ".$data[$i]['id'];

            }
            $response=$db_gdlvan->prepare($update);
            $response->execute();
            echo $update."<br>";
            
            $i++;
        }
        /*---------------------------------------ACTUALIZAR solicitud_gasolina---------------------------------------------------*/
        $sql = "SELECT solicitud_gasolina.id, viajes.contrato, solicitud_gasolina.viaje_div FROM solicitud_gasolina LEFT JOIN viajes on viajes.id = solicitud_gasolina.id_viaje";
        $response=$db_logistica->prepare($sql);
        $response->execute();
        $data = $response->fetchAll(PDO::FETCH_ASSOC);
        $i=0;
        $tam = sizeof($data);

        while ($i<$tam) { 
            $contrato = explode("-",$data[$i]['contrato']);
            $contrato =  $contrato[0]."-".$contrato[1];       
            echo $contrato."<br>";
            $busqueda;
            if ($data[$i]['viaje_div']>0) {
                 $busqueda = "SELECT viajes_divididos.id, viajes_divididos.id_viaje from viajes_divididos where id = ".$data[$i]['viaje_div'];
            }else{
                 $busqueda = "SELECT viaje.id from viaje LEFT JOIN contrato on contrato.id = viaje.id_contrato WHERE num_contrato like '".$contrato."%' ";
            }
            $response=$db_gdlvan->prepare($busqueda);
            $response->execute();
            $viaje = $response->fetch(PDO::FETCH_ASSOC);
            //echo $viaje['id'];
            //actualizar en encuestas ventas
            $update="";
            if ($data[$i]['viaje_div']>0) {
                echo "si division<br>";
                $update = "UPDATE solicitud_gasolina set id_viaje = ".$viaje['id_viaje'].", viaje_div = ".$viaje['id']." WHERE id = ".$data[$i]['id'];
            }else{
                echo "no division<br>";
                 $update = "UPDATE solicitud_gasolina set id_viaje = ".$viaje['id']." WHERE id = ".$data[$i]['id'];

            }
            $response=$db_gdlvan->prepare($update);
            $response->execute();
            echo $update."<br>";
            
            $i++;
        }
    });
    $app->get('/insertar', function() use($app, $db_gdlvan){
        //insertar costo
        $sql ="INSERT INTO costos(precio,   num_boxlunch, costo_boxlunch, descuento, total_descuento, subtotal, iva, total, anticipo, restante, porcentaje_anticipo, iva_incluido) VALUES(100.00, 0,0.00, 0, 0.00, 100.00, 16.00, 116.00, 16.00, 116.00, 16,0)";
        echo $sql."<br>";
        $response=$db_gdlvan->prepare($sql);
        $response->execute();
        $id_insertado = $db_gdlvan->lastInsertId();
        $sql = "SELECT max(id) as maximo from cotizacion";
        $response=$db_gdlvan->prepare($sql);
        $response->execute();
        $maximo = $response->fetch(PDO::FETCH_ASSOC);
        //insertar cotizacion
        $sql = "INSERT INTO cotizacion(
                                    id_cliente,
                                    id_costo,
                                    fecha,
                                    num_cotizacion,
                                    cancelado,
                                    vigencia,
                                    dispositivo, 
                                    correo_cancelacion, 
                                    comentario_cliente, 
                                    whats1,
                                    whats2, 
                                    whats3, 
                                    cupon, 
                                    visto, 
                                    vistoWeb, 
                                    promocion 
                                    ) 
                            VALUES(
                                    5677,
                                    ".$id_insertado.",
                                    '2019-03-13',
                                    'GDLT-".$maximo['maximo']."-19-CPC',
                                    0,
                                    3,
                                    'P',
                                    0, 
                                    '', 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0,
                                    0, 
                                    0
                                    )";
       echo $sql."<br>";
        $response = $db_gdlvan->prepare($sql);
        $response->execute();
        $cot_insertada = $db_gdlvan->lastInsertId();
        echo "cotizacion ".$cot_insertada."<br>";
        //INSERtar contrato
         $sql = "SELECT max(id) as maximo from contrato";
        $response=$db_gdlvan->prepare($sql);
        $response->execute();
        $maximoCont = $response->fetch(PDO::FETCH_ASSOC);
        $sql = "INSERT into contrato(id_cliente, id_costo, fecha, num_contrato, asignado, autorizado, usuario, cancelado, comentario_cliente, visto, notificacion_vista,visto72, visto24, actualizacion, credito) 
        VALUES(5677, ".$id_insertado.", '219-03-13', 'GDL-".($maximoCont['maximo']+1)."-19-CPC',0,1,92,0,'',0, 0,0,0,0,0)";
        echo $sql."<br>";
        $response=$db_gdlvan->prepare($sql);
        $response->execute();
        $cont_insertada = $db_gdlvan->lastInsertId();
        //insertar viaje
        $sql = "INSERT INTO viaje(pasajeros, unidad, tipo_viaje, fecha_salida, fecha_regreso, lugar_salida1, lugar_destino1, lugar_salida2, lugar_destino2, nombre_responsable, cel_responsable, correo_responsable, id_cotizacion, id_contrato, sucursal, fecha_final, hora_salida, hora_regreso, hora_final, salida_exacta, destino_exacto, id_unidad, id_operador, iniciado, cerrado, terminado, correo_enviado72, correo_enviado24, cliente_contactado, cliente_contactado2, cliente_verifico, firma_cliente ) 
                VAlues(15, 'S TUR 16', 'Foraneo redondo s/movimientos', '2019-03-19', '2019-03-22', 546, 707, 707, 546, 'Jose', 3318549435, 'jose.sebas@alumnos.udg.mx', ".$cot_insertada.", ".$cont_insertada.", 1, '2019-03-22','07:00', '21:00', '20:00','','',0,0,0,0,0,0,0,0,0,'','')";
        echo $sql."<br>";
        $response=$db_gdlvan->prepare($sql);
        $response->execute();
        $viaje_insertado = $db_gdlvan->lastInsertId();
        echo $viaje_insertado;
    });

	$app->post("/logear", function() use($app, $db_gdlvan){
		$json = $app->request->post('log');
        $log = json_decode($json, true);
        $log['contrasena']=SED::encryption($log['contrasena']);
        $sql = 'SELECT * from users LEFT JOIN colaboradores ON id_colaborador = colaboradores.id WHERE baja=0 and pass = "'.$log['contrasena'].'" and email = "'.$log['usuario'].'"';
        $response = $db_gdlvan->prepare($sql);
        $response->execute();
        $respuesta = $response->fetch(PDO::FETCH_ASSOC);
        if($respuesta){
            $respuesta = array(
            'code'=>200,
            'status'=>'success',
            'usuario'=>array(
                        'email'=>$respuesta['email'],
                        'img_perfil'=>$respuesta['img_perfil'],
                        'nombre'=>$respuesta['nombre'],
                        'puesto'=>$respuesta['puesto'],
                        'siglas'=>$respuesta['siglas'],
                        'role'=>$respuesta['role'],
                        'sucursal'=>$respuesta['sucursal']
                    )

            ) ;
        }else{
            $respuesta = array(
            'code'=>404,
            'status'=>'error',
            'usuario'=>array()

        );
        }
		echo json_encode($respuesta);
	});
    $app->get('/getOperadores', function() use($app, $db_gdlvan){
        $sql = "SELECT * FROM colaboradores WHERE baja =0 and puesto ='operador'";
        $response = $db_gdlvan->prepare($sql);
        $response->execute();
        $operadores = $response->fetchAll(PDO::FETCH_ASSOC);
        $result = array(
            'code'=>200,
            'data'=>$operadores
        );

        echo json_encode($result);
    });
    $app->get('/getOperador/:id_unidad', function($id_unidad) use($app, $db_gdlvan){
        $sql = "SELECT id, nombre, apellidos FROM colaboradores WHERE baja =0 and puesto ='operador' and id_unidad =".$id_unidad;
        $response = $db_gdlvan->prepare($sql);
        $response->execute();
        $operador = $response->fetch(PDO::FETCH_ASSOC);
        $result=array();
        if (!$operador) {
            $result = array(
                'code'=>404,
                'data'=>$operador
            );
        }else{
            $result = array(
                'code'=>200,
                'data'=>$operador
            );    
        }
        

        echo json_encode($result);
    });
    $app->get('/getUnidades', function() use($app, $db_gdlvan){
        $sql = "SELECT * FROM unidades WHERE baja =0";
        $response = $db_gdlvan->prepare($sql);
        $response->execute();
        $operadores = $response->fetchAll(PDO::FETCH_ASSOC);
        $result = array(
            'code'=>200,
            'data'=>$operadores
        );

        echo json_encode($result);
    });
    $app->get('/getSucursales', function() use($app, $db_gdlvan){
        $sql = "SELECT * FROM sucursales WHERE baja =0";
        $response = $db_gdlvan->prepare($sql);
        $response->execute();
        $operadores = $response->fetchAll(PDO::FETCH_ASSOC);
        $result = array(
            'code'=>200,
            'data'=>$operadores
        );

        echo json_encode($result);
    });
    $app->post('/getMovimientos', function() use($app, $db_gdlvan){
        $json = $app->request->post('viaje');
        $viaje =json_decode($json,true);
        $inicio = $viaje['inicio'];
        $fin = $viaje['fin'];
        $id_viaje = $viaje['id_viaje'];
        $fecha_temp = date('Y-m-d',mktime(0, 0, 0, $inicio['month']  , $inicio['day'], $inicio['year']));
        $fecha_temp2 = date('Y-m-d',mktime(0, 0, 0, $fin['month']  , $fin['day'], $fin['year']));
        
        $sql = "SELECT descripcion, hora, fecha FROM  movimientos  WHERE fecha BETWEEN '".$fecha_temp."' and '".$fecha_temp2."' and id_viaje =".$id_viaje." ORDER BY id asc, fecha asc LIMIT 1 ";
        $response = $db_gdlvan->prepare($sql);
        $response->execute();
        $movimientos = $response->fetch(PDO::FETCH_ASSOC);
        $result = array ();
        if (count($movimientos)>0) {
            $resp = "";
            if ($movimientos['descripcion']) {
                $resp = $movimientos['descripcion'];
                if ($movimientos['hora']) {
                    $resp .= " a las ".$movimientos['hora'];
                }
                $resp .=" el día ".$movimientos['fecha'];
            }
            $result = array(
                'code'=>200,
                'otro'=>$movimientos,
                'data'=>$resp
            );  
        }else{
            $result = array(
                'code'=>404,
                'data'=>$movimientos
            );
        }
        echo json_encode($result);
    });
    $app->get('/correo', function() use($app, $db_gdlvan){
            
            $operador = array('nombre'=>'Jose');
            $cliente = "";
            $viaje = "";
            echo Correo_Operador::getCorreo($operador, $cliente, $viaje);
    });
    $app->post('/iniciarViaje', function() use($app, $db_gdlvan, $db_cotizador){
        $json = $app->request->post('viaje');
        $viaje_post =json_decode($json,true);
        $id_viaje = $viaje_post['id_viaje'];
        $num_contrato = $viaje_post['num_contrato'];
        $num_contrato = explode('-',$num_contrato);
        $sql  ="";
        $sql_correo="";
        $operador;
        $cliente;
        $viaje;
        if (count($num_contrato)>4) {
            $sql  = "UPDATE viajes_divididos set iniciado = 1 WHERE id_viaje = ".$id_viaje." and consecutivo = '".$num_contrato[4]."' ";
            $sql_correo = "SELECT viaje.nombre_responsable as nombre_cliente, viaje.cel_responsable as cel, colaboradores.nombre as nombre_operador,colaboradores.apellidos as apellidos_operador,colaboradores.tel1, colaboradores.correo1, viaje.pasajeros, unidades.num_economico, unidades.tipo_unidad, unidades.placas, viajes_divididos.tipo, viaje.tipo_viaje, viajes_divididos.fechaInicio as fecha1, viajes_divididos.fechaFin as fecha2, viajes_divididos.lugar_salida, viaje.lugar_salida1,viajes_divididos.salida_exacta, viajes_divididos.destino_exacto, viaje.lugar_destino1, viaje.lugar_salida2, viaje.lugar_destino2
                            FROM viajes_divididos 
                            LEFT JOIN viaje on viaje.id = viajes_divididos.id_viaje
                            LEFT JOIN contrato on contrato.id = viaje.id_contrato
                            LEFT JOIN clientes on clientes.id = contrato.id_cliente
                            LEFT JOIN colaboradores on colaboradores.id = viajes_divididos.id_operador
                            LEFT JOIN unidades on unidades.id = viajes_divididos.id_unidad
                             WHERE viajes_divididos.id_viaje = ".$id_viaje." and consecutivo ='".$num_contrato[4]."' and viajes_divididos.cancelado=0 ";
            $response = $db_gdlvan->prepare($sql_correo);
            $response->execute();
            $datos = $response->fetch(PDO::FETCH_ASSOC);
            if ($datos['tipo']=='ida_vuelta') {
                $datos['lugar_salida'] = $datos['lugar_salida2'];
                $datos['lugar_destino'] = $datos['lugar_destino20'];
            }
            if ($datos['tipo']=='falla') {
                $datos['lugar_salida'] = "";
                $datos['lugar_destino'] = "";
            }


        }else{
            $sql  = "UPDATE viaje set iniciado = 1 WHERE id = ".$id_viaje;
            $sql_correo = "SELECT viaje.nombre_responsable as nombre_cliente, viaje.cel_responsable  as cel, colaboradores.nombre as nombre_operador,colaboradores.apellidos as apellidos_operador,colaboradores.tel1, colaboradores.correo1, viaje.pasajeros, unidades.num_economico, unidades.tipo_unidad, unidades.placas, viaje.tipo_viaje, viaje.fecha_salida as fecha1, viaje.fecha_regreso as fecha2, viaje.lugar_salida1, viaje.lugar_destino1, viaje.salida_exacta, viaje.destino_exacto, viajes_divididos.id_viaje
                            FROM viaje
                            LEFT JOIN contrato on contrato.id = viaje.id_contrato
                            LEFT JOIN clientes on clientes.id = contrato.id_cliente
                            LEFT JOIN colaboradores on colaboradores.id = viaje.id_operador
                            LEFT JOIN unidades on unidades.id = viaje.id_unidad
                            LEFT JOIN viajes_divididos on viajes_divididos.id_viaje = viaje.id 
                             WHERE viaje.id = ".$id_viaje."  ";
            $response = $db_gdlvan->prepare($sql_correo);
            $datos;
            if ($response->execute()) {
                $datos = $response->fetch(PDO::FETCH_ASSOC);       
                $response = $db_gdlvan->prepare($sql);
                $response->execute();
            }

            if ($datos['id_viaje']) {
                $sql = "SELECT date_add(fechaInicio, INTERVAL -1 day) as fecha1 FROM viajes_divididos WHERE id_viaje = ".$id_viaje." ORDER BY fechaInicio asc ";
                $response = $db_gdlvan->prepare($sql);
                $response->execute();
                $dia_anterior = $response->fetch(PDO::FETCH_ASSOC);
                $fecha_entrada = strtotime($dia_anterior['fecha1']);
                $fecha_consulta = strtotime($datos['fecha1']); 
                if ($fecha_consulta <= $fecha_entrada) {
                    $datos['fecha2'] = $dia_anterior['fecha1'];
                }else if($fecha_consulta>$fecha_entrada){
                    $datos['fecha2'] =  $datos['fecha1'];
                }
            }
        
        }
        //





        ///char esta consulta de sql  cambiar nombre a sql donde traigo fechas
        $response = $db_gdlvan->prepare($sql);
        $response->execute();
        $movimientos = $response->fetch(PDO::FETCH_ASSOC);
        $result=array();
        if ($response->rowCount()) {
            //enviar correo
            
            $operador = array(
                'nombre'=>$datos['nombre_operador'],
                'apellidos'=>$datos['apellidos_operador'],
                'celular'=>$datos['tel1'],
                'correo'=>$datos['correo1']
            );
            $viaje=array(
                'pasajeros'=>$datos['pasajeros'],
                'tipo_unidad'=>$datos['tipo_unidad'],
                'num_economico'=>$datos['num_economico'],
                'placas'=>$datos['placas'],
                'tipo_viaje'=>$datos['tipo_viaje'],
                'fecha1'=>$datos['fecha1'],
                'fecha2'=>$datos['fecha2'],
                'lugar_salida'=>$datos['lugar_salida1'],
                'salida_exacta'=>$datos['salida_exacta'],
                'lugar_destino'=>$datos['lugar_destino1'],
                'destino_exacto'=>$datos['destino_exacto'],
                'num_contrato'=>$viaje_post['num_contrato']
            );
            $muni = "SELECT CONCAT('',municipio) as municipio from municipios WHERE id = ".$viaje['lugar_salida']." UNION ALL SELECT CONCAT('',municipio) as municipio from municipios WHERE id = ".$viaje['lugar_destino'];
                $response= $db_cotizador->prepare($muni);
                if ($response->execute()) {
                    $municipios = $response->fetchAll(PDO::FETCH_ASSOC);
                    $salida = explode(",",$municipios[0]['municipio']);
                    $destino = explode(",",$municipios[1]['municipio']);
                    $viaje['lugar_salida']= $salida[0].", ".$salida[1];        
                    $viaje['lugar_destino']=$destino[0].", ".$destino[1];
                }
            $cliente= array(
                'nombre'=>$datos['nombre_cliente'],
                'celular'=>$datos['cel']
            );
            /*$mail = new PHPMailer();
            $mail->CharSet = 'UTF-8';
            $mail->SMTPDebug = 0;
            //$mail->isSMTP(); 
            $mail->Host = 'mail.gdlvan.com.mx';
            $mail->SMTPAuth = true;
            $mail->Username = 'notificaciones@gdlvan.com.mx';
            $mail->Password = 'OmbI8+2$yDte';
            $mail->From ="notificaciones@gdlvan.com.mx";
            $mail->FromName='GDLvan S.A. de C.V.';
            $mail->Subject = "Nuevo viaje por iniciar.";
            $mail->Body = Correo_Operador::getCorreo($operador, $cliente, $viaje);
            $mail->IsHTML(true);
            $mail->AddAddress('jose.sebas@alumnos.udg.mx');
            $mail->Send();
            $mail->ClearAddresses();
            */
            $result = array(
                'code'=>200,
                'data'=>$sql,
                'operador'=>$operador,
                'viaje'=>$viaje,
                'cliente'=>$cliente,
                'count'=>$response->rowCount(),
                'sql'=>$sql_correo,
                'message'=>Correo_Operador::getCorreo($operador, $cliente, $viaje)
            );
        }else{
            $result = array(
                'code'=>400,
                'data'=>$sql
            );
        }
              
        
        echo json_encode($result);
    });
    $app->post('/asignarViaje', function() use($app, $db_gdlvan){
        $json = $app->request->post('asignar');
        $asignar = json_decode($json, true);
        $sql = "UPDATE viaje LEFT JOIN contrato on contrato.id = viaje.id_contrato SET viaje.id_unidad = ".$asignar['id_unidad'].", viaje.id_operador=".$asignar['id_operador'].", contrato.asignado =1 WHERE viaje.id =  ".$asignar['id_viaje'];
        $response=$db_gdlvan->prepare($sql);
        $response->execute();
        $result=array();
        if ($response->rowCount()) {
            $result = array(
            'code'=>200,
            'data'=>$response->rowCount(),
            'response'=>$response,
            'sql'=>$sql
            );    
        }else{
            $result = array(
            'code'=>500,
        'sql'=>$sql
            );
        }
        echo json_encode($result);
    });
    $app->post('/dividirViaje', function() use($app, $db_gdlvan ,$db_cotizador){
        $consecutivos = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S'];
        $json = $app->request->post('dividir');
        $dividir = json_decode($json, true);
        $data_principal = $dividir['data_principal'];
        $data_divisiones = $dividir['data_divisiones'];
        $viajes=array();
        $sql = "UPDATE viaje set viaje.id_operador = ".$data_principal['operador'].", viaje.id_unidad = ".$data_principal['unidad']." WHERE viaje.id = ".$data_principal['id_viaje'];
        $response=$db_gdlvan->prepare($sql);
        $response->execute();
        $sql_div="";
        if ($data_principal['tipo_division']=='dia') {
            for ($i=0; $i <sizeof($data_divisiones) ; $i++) { 
                $fecha_temp = date('Y-m-d',mktime(0, 0, 0, $data_divisiones[$i]['fechaInicial']['month']  , $data_divisiones[$i]['fechaInicial']['day'], $data_divisiones[$i]['fechaInicial']['year']));
                $fecha_temp2 = date('Y-m-d',mktime(0, 0, 0, $data_divisiones[$i]['fechaFinal']['month']  , $data_divisiones[$i]['fechaFinal']['day'], $data_divisiones[$i]['fechaFinal']['year']));
                $sql_div = "INSERT INTO viajes_divididos(consecutivo, fechaInicio, fechaFin, id_unidad, id_operador, iniciado, cerrado, terminado, cliente_contactado, cliente_contactado2, firma_div, cliente_verifico, id_viaje,tipo, salida_exacta, destino_exacto,  lugar_salida, hora_salida, lugar_regreso, hora_regreso,  cancelado)
                        VALUES('".$consecutivos[$i]."', '".$fecha_temp."', '".$fecha_temp2."', ".$data_divisiones[$i]['unidad'].", ".$data_divisiones[$i]['operador'].", 0,0,0,0,0,'','', ".$data_divisiones[$i]['id_viaje'].", '".$data_principal['tipo_division']."','".agregarAmperson($data_divisiones[$i]['salida_exacta'])."','".agregarAmperson($data_divisiones[$i]['destino_exacto'])."','','','','',0)";
                $response=$db_gdlvan->prepare($sql_div);
                $response->execute();
                $viajes[$i]['id_division'] = $db_gdlvan->lastInsertId();
                $viajes[$i]['consecutivo'] = $consecutivos[$i];
                $sql_mov = "UPDATE movimientos SET viaje_div = ".$viajes[$i]['id_division']." WHERE fecha BETWEEN '".$fecha_temp."' and '".$fecha_temp2."' and id_viaje =".$data_divisiones[$i]['id_viaje'];
                $response=$db_gdlvan->prepare($sql_mov);
                $response->execute();


            }
        }else{
            $sql_div = "INSERT INTO viajes_divididos(consecutivo, fechaInicio, fechaFin, id_unidad, id_operador, iniciado, cerrado, terminado, cliente_contactado, cliente_contactado2, firma_div, cliente_verifico, id_viaje, tipo, salida_exacta, destino_exacto, lugar_salida, hora_salida, lugar_regreso, hora_regreso, cancelado)
                        VALUES('A', '".$fecha_temp."', '".$fecha_temp2."', ".$data_divisiones[0]['unidad'].", ".$data_divisiones[0]['operador'].", 0,0,0,0,0,'','', ".$data_divisiones[0]['id_viaje'].", '".$data_principal['tipo_division']."', '".agregarAmperson($data_divisiones[0]['salida_exacta'])."','".agregarAmperson($data_divisiones[0]['destino_exacto'])."','','','','',0)";
            $response=$db_gdlvan->prepare($sql_div);
            $response->execute();
            $viajes[0]['id_division'] = $db_gdlvan->lastInsertId();
            $viajes[0]['consecutivo'] = $consecutivos[0];
            //echo $sql_div;
                
        }
        for($ind = 0; $ind < count($viajes); $ind++){
            $sql = "SELECT   viaje.id,
                            CONCAT(contrato.num_contrato,'-',viajes_divididos.consecutivo) as num_contrato,
                            clientes.nombre as nombre_cliente,
                            clientes.cel as celular_cliente,
                            viaje.lugar_salida1 as lugar_salida,
                            viaje.fecha_salida,
                            viaje.lugar_destino1 as lugar_destino,
                            viaje.fecha_regreso,
                            viaje.salida_exacta,
                            viaje.destino_exacto,
                            viaje.tipo_viaje,
                            costos.total,
                            costos.restante,
                            unidades.num_economico,
                            unidades.placas,
                            viaje.unidad,
                            viajes_divididos.id_unidad,
                            colaboradores.nombre as nombre_operador,
                            colaboradores.apellidos as apellido_operador,
                            viajes_divididos.id_operador,
                            contrato.cancelado,
                            viajes_divididos.iniciado,
                            viajes_divididos.id as dividido
                            from viajes_divididos 
                                left join viaje on viaje.id = viajes_divididos.id_viaje 
                                left join contrato on contrato.id = viaje.id_contrato 
                                left join costos on contrato.id_costo = costos.id  
                                left join unidades on unidades.id = viajes_divididos.id_unidad
                                left join colaboradores on colaboradores.id =viajes_divididos.id_operador
                                left join clientes on contrato.id_cliente = clientes.id
                                left join sucursales on viaje.sucursal = sucursales.id 
                            WHERE viajes_divididos.id = ".$viajes[$ind]['id_division']." and viajes_divididos.cancelado=0 ORDER by viajes_divididos.id";

            $response =$db_gdlvan->prepare($sql);
            $response->execute();
            $resp_temp = $response->fetch(PDO::FETCH_ASSOC);
            if ($resp_temp) {
                $muni = "SELECT CONCAT('',municipio) as municipio from municipios WHERE id = ".$resp_temp['lugar_salida']." UNION ALL SELECT CONCAT('',municipio) as municipio from municipios WHERE id = ".$resp_temp['lugar_destino'];
                $response= $db_cotizador->prepare($muni);
                $response->execute();
                $municipios = $response->fetchAll(PDO::FETCH_ASSOC);
                $salida = explode(",",$municipios[0]['municipio']);
                $destino = explode(",",$municipios[1]['municipio']);
                $resp_temp['lugar_salida']= $salida[0].", ".$salida[1];        
                $resp_temp['lugar_destino']=$destino[0].", ".$destino[1];
                //print_r( $municipios);
                //print_r($resp_temp);
                $viajes[$ind]=$resp_temp;
            }
            
        }
        
        $result=array();
        if (count($viajes)>0) {
            $result=array(
                'code'=>200,
                'data'=>$viajes

            );            
        }else{
            $result = array(
                'code'=>404,
                'data'=>$viajes

            );
        }
        echo json_encode($result);
    });
    $app->post('/editarDivisiones', function() use($app, $db_gdlvan, $db_cotizador){
        $json = $app->request->post('divisiones');
        $dividir = json_decode($json, true);
        $data_principal = $dividir['data_principal'];
        $data_divisiones = $dividir['data_divisiones'];
        $contador =0;
        $sql_insert_update=array();

        while ($contador < sizeof($data_divisiones)) {
            if ($data_divisiones[$contador]['division_cancelada']==0) {
                $sql ="";
                // para guardar las fechas en caso de que fuera por dias
                $fecha_temp = "";$fecha_temp2="";
//                if ($data_divisiones[$contador]['tipo_division']=='dia') {
                    $fecha_temp = date('Y-m-d',mktime(0, 0, 0, $data_divisiones[$contador]['fechaInicial']['month']  , $data_divisiones[$contador]['fechaInicial']['day'], $data_divisiones[$contador]['fechaInicial']['year']));
                    $fecha_temp2 = date('Y-m-d',mktime(0, 0, 0, $data_divisiones[$contador]['fechaFinal']['month']  , $data_divisiones[$contador]['fechaFinal']['day'], $data_divisiones[$contador]['fechaFinal']['year']));
  //              }
                // guardar division en caso de ser nueva
                if ($data_divisiones[$contador]['nuevo']==1) {  
                    $consecutivo = explode('-',$data_divisiones[$contador]['num_contrato']);
                    $consecutivo = $consecutivo[4];
                    $sql = "INSERT INTO viajes_divididos(consecutivo, fechaInicio, fechaFin, id_unidad, id_operador, iniciado, cerrado, terminado, cliente_contactado, cliente_contactado2, firma_div, cliente_verifico, id_viaje,tipo,salida_exacta,destino_exacto, lugar_salida, hora_salida, lugar_regreso, hora_regreso,  cancelado)
                            VALUES('".$consecutivo."', '".$fecha_temp."', '".$fecha_temp2."', ".$data_divisiones[$contador]['unidad'].", ".$data_divisiones[$contador]['operador'].", 0,0,0,0,0,'','', ".$data_divisiones[$contador]['id_viaje'].", '".$data_divisiones[$contador]['tipo_division']."','".agregarAmperson($data_divisiones[$contador]['salida_exacta'])."','".agregarAmperson($data_divisiones[$contador]['destino_exacto'])."','','','','', 0)";
                // actualizar division en caso de ya existir
                }else{

//                    if ($data_divisiones[$contador]['tipo_division']=='dia') {// si se actualizan los dias y todos los demas
                        $sql  = "UPDATE viajes_divididos SET fechaInicio='".$fecha_temp."', fechaFin='".$fecha_temp2."', id_unidad = ".$data_divisiones[$contador]['unidad'].", id_operador =".$data_divisiones[$contador]['operador']." , cancelado =".$data_divisiones[$contador]['eliminar'].", salida_exacta='".agregarAmperson($data_divisiones[$contador]['salida_exacta'])."',destino_exacto = '".agregarAmperson($data_divisiones[$contador]['destino_exacto'])."'  WHERE viajes_divididos.id = ".$data_divisiones[$contador]['id_dividido'];
                    /*}else{ // si se actualizan todos menos los dias
                        $sql  = "UPDATE viajes_divididos SET id_unidad = ".$data_divisiones[$contador]['unidad'].", id_operador =".$data_divisiones[$contador]['operador']." , cancelado =".$data_divisiones[$contador]['eliminar'].", lugar_salida='".agregarAmperson($data_divisiones[$contador]['salida_exacta'])."' WHERE viajes_divididos.id = ".$data_divisiones[$contador]['id_dividido'];    
                    }*/
                    
                }
                array_push($sql_insert_update,$sql);
                $response = $db_gdlvan->prepare($sql);
                $response->execute();
            }
            $contador++;
        }
         $sql = "SELECT   viaje.id,
                            CONCAT(contrato.num_contrato,'-',viajes_divididos.consecutivo) as num_contrato,
                            clientes.nombre as nombre_cliente,
                            clientes.cel as celular_cliente,
                            viaje.lugar_salida1 as lugar_salida,
                            viaje.fecha_salida,
                            viaje.lugar_destino1 as lugar_destino,
                            viaje.fecha_regreso,
                            viaje.tipo_viaje,
                            viaje.salida_exacta,
                            viaje.destino_exacto,
                            costos.total,
                            costos.restante,
                            unidades.num_economico,
                            viaje.unidad,
                            viajes_divididos.id_unidad,
                            colaboradores.nombre as nombre_operador,
                            colaboradores.apellidos as apellido_operador,
                            viajes_divididos.id_operador,
                            viajes_divididos.cancelado,
                            viajes_divididos.iniciado,
                            viajes_divididos.id as dividido,
                            viajes_divididos.fechaInicio,
                            viajes_divididos.fechaFin
                            from viajes_divididos 
                                left join viaje on viaje.id = viajes_divididos.id_viaje 
                                left join contrato on contrato.id = viaje.id_contrato 
                                left join costos on contrato.id_costo = costos.id  
                                left join unidades on unidades.id = viajes_divididos.id_unidad
                                left join colaboradores on colaboradores.id =viajes_divididos.id_operador
                                left join clientes on contrato.id_cliente = clientes.id
                                left join sucursales on viaje.sucursal = sucursales.id 
                            WHERE viajes_divididos.id_viaje = ? and viajes_divididos.cancelado=0 and viajes_divididos.iniciado=? ORDER by viajes_divididos.id asc";

            $response =$db_gdlvan->prepare($sql);
            $response->execute(array($data_principal['id_viaje'],0));
            $viajes= $response->fetchAll(PDO::FETCH_ASSOC);

            $response =$db_gdlvan->prepare($sql);
            $response->execute(array($data_principal['id_viaje'],1));
            $viajesIniciados= $response->fetchAll(PDO::FETCH_ASSOC);




            //ERROR
            //cuando llega a esta parte



            //TODOS LOS MOVIMIENTOS RELACIONARLOS SOLO CON EL PRINCIPAL
            $sql_mov = "UPDATE movimientos SET viaje_div = 0 WHERE id_viaje =".$data_principal['id_viaje'];
            $response=$db_gdlvan->prepare($sql_mov);
            $response->execute();
            for ($i=0; $i <sizeof($viajes) ; $i++) { 
                //CADA DIVISION ASIGNARLE A SUS MOVIMIENTOS
                $sql_mov = "UPDATE movimientos SET viaje_div = ".$viajes[$i]['dividido']." WHERE fecha BETWEEN '".$viajes[$i]['fechaInicio']."' and '".$viajes[$i]['fechaFin']."' and viajes_divididos.id =".$viajes[$i]['dividido'];
                $response=$db_gdlvan->prepare($sql_mov);
                $response->execute();


                $muni = "SELECT municipio from municipios WHERE id = ".$viajes[$i]['lugar_salida']." UNION ALL SELECT municipio from municipios WHERE id = ".$viajes[$i]['lugar_destino'];
                $response= $db_cotizador->prepare($muni);
                $response->execute();
                $municipios = $response->fetchAll(PDO::FETCH_ASSOC);
                $salida = explode(",",$municipios[0]['municipio']);
                $destino = explode(",",$municipios[1]['municipio']);
                $viajes[$i]['lugar_salida']= $salida[0].", ".$salida[1];        
                $viajes[$i]['lugar_destino']=$destino[0].", ".$destino[1];
            }
            for ($i=0; $i <sizeof($viajesIniciados) ; $i++) { 
                //CADA DIVISION ASIGNARLE A SUS MOVIMIENTOS
                $sql_mov = "UPDATE movimientos SET viaje_div = ".$viajesIniciados[$i]['dividido']." WHERE fecha BETWEEN '".$viajesIniciados[$i]['fechaInicio']."' and '".$viajesIniciados[$i]['fechaFin']."' and viajesIniciados_divididos.id =".$viajesIniciados[$i]['dividido'];
                $response=$db_gdlvan->prepare($sql_mov);
                $response->execute();
                $muni = "SELECT municipio from municipios WHERE id = ".$viajesIniciados[$i]['lugar_salida']." UNION ALL SELECT municipio from municipios WHERE id = ".$viajesIniciados[$i]['lugar_destino'];
                $response= $db_cotizador->prepare($muni);
                $response->execute();
                $municipios = $response->fetchAll(PDO::FETCH_ASSOC);
                $salida = explode(",",$municipios[0]['municipio']);
                $destino = explode(",",$municipios[1]['municipio']);
                $viajesIniciados[$i]['lugar_salida']= $salida[0].", ".$salida[1];        
                $viajesIniciados[$i]['lugar_destino']=$destino[0].", ".$destino[1];
            }
            $result=array(
                'code'=>200,
                'data'=>$viajes,
                'dataIniciados'=>$viajesIniciados,
                'sql'=>$sql_insert_update

            );            
        
        echo json_encode($result);

    });
    $app->post('/reasignarViaje', function() use($app, $db_gdlvan, $db_cotizador){
        $json = $app->request->post('reasignar');
        $reasignar = json_decode($json, true);
        $tipo = explode('-',$reasignar['num_contrato']);
        if (sizeof($tipo)==4) {
            $sql = "UPDATE viaje LEFT JOIN contrato on contrato.id = viaje.id_contrato SET viaje.id_unidad = ".$reasignar['id_unidad'].", viaje.id_operador=".$reasignar['id_operador'].", contrato.asignado =1 WHERE viaje.id =  ".$reasignar['id_viaje'];
        }else if(sizeof($tipo)==5){
            $sql = "UPDATE viajes_divididos LEFT JOIN viaje on viaje.id = viajes_divididos.id_viaje LEFT JOIN contrato on contrato.id = viaje.id_contrato SET viajes_divididos.id_unidad = ".$reasignar['id_unidad'].", viajes_divididos.id_operador=".$reasignar['id_operador']." WHERE viajes_divididos.id =  ".$reasignar['id_dividido'];
        }
        
        $response=$db_gdlvan->prepare($sql);
        $response->execute();
        $result=array();
        if ($response->rowCount()) {
            $result = array(
            'code'=>200,
            'data'=>$response->rowCount(),
            'response'=>$response,
            'sql'=>$sql
            );    
        }else{
            $result = array(
            'code'=>500,
            'sql'=>$sql
            );
        }
        echo json_encode($result);
        
    });
    $app->post('/getDivisiones', function() use($app, $db_gdlvan, $db_cotizador){
        $json = $app->request->post('viaje');
        $viaje = json_decode($json, true);
        $sql = "SELECT   viaje.id,
                            CONCAT(contrato.num_contrato,'-',viajes_divididos.consecutivo) as num_contrato,
                            viajes_divididos.consecutivo,
                            contrato.num_contrato as num_contrato_original,
                            clientes.nombre as nombre_cliente,
                            clientes.cel as celular_cliente,
                            viaje.lugar_salida1 as lugar_salida,
                            viaje.fecha_salida,
                            viaje.lugar_destino1 as lugar_destino,
                            viaje.fecha_regreso,
                            viaje.tipo_viaje,
                            costos.total,
                            costos.restante,
                            unidades.num_economico,
                            viaje.unidad,
                            viajes_divididos.id_unidad,
                            colaboradores.nombre as nombre_operador,
                            colaboradores.apellidos as apellido_operador,
                            viajes_divididos.id_operador,
                            contrato.cancelado,
                            viajes_divididos.id as dividido,
                            viajes_divididos.tipo,
                            viajes_divididos.fechaInicio,
                            viajes_divididos.fechaFin,
                            viajes_divididos.salida_exacta,
                            viajes_divididos.destino_exacto,
                            viajes_divididos.hora_salida,
                            viajes_divididos.lugar_regreso,
                            viajes_divididos.hora_regreso,
                            viajes_divididos.cancelado as division_cancelada,
                            CONCAT(0) as eliminar,
                            CONCAT(0) as nuevo
                            from viajes_divididos 
                                left join viaje on viaje.id = viajes_divididos.id_viaje 
                                left join contrato on contrato.id = viaje.id_contrato 
                                left join costos on contrato.id_costo = costos.id  
                                left join unidades on unidades.id = viajes_divididos.id_unidad
                                left join colaboradores on colaboradores.id =viajes_divididos.id_operador
                                left join clientes on contrato.id_cliente = clientes.id
                                left join sucursales on viaje.sucursal = sucursales.id 
                            WHERE viajes_divididos.id_viaje = ".$viaje['id_viaje']." ORDER by viajes_divididos.id asc";
        $response =$db_gdlvan->prepare($sql);
        $response->execute();
        $divisiones=$response->fetchAll(PDO::FETCH_ASSOC);
        $sql = "SELECT   count(viajes_divididos.id) as inicial_asignados
                            from viajes_divididos 
                                left join viaje on viaje.id = viajes_divididos.id_viaje 
                                left join contrato on contrato.id = viaje.id_contrato 
                                left join costos on contrato.id_costo = costos.id  
                                left join unidades on unidades.id = viajes_divididos.id_unidad
                                left join colaboradores on colaboradores.id =viajes_divididos.id_operador
                                left join clientes on contrato.id_cliente = clientes.id
                                left join sucursales on viaje.sucursal = sucursales.id 
                            WHERE viajes_divididos.id_viaje = ".$viaje['id_viaje']." and viajes_divididos.cancelado  =0 and viajes_divididos.iniciado=0";
        $response =$db_gdlvan->prepare($sql);
        $response->execute();
        $resp=$response->fetch(PDO::FETCH_ASSOC);
        $inicial_asignados = $resp['inicial_asignados'];
        $sql = "SELECT   count(viajes_divididos.id) as inicial_iniciados
                            from viajes_divididos 
                                left join viaje on viaje.id = viajes_divididos.id_viaje 
                                left join contrato on contrato.id = viaje.id_contrato 
                                left join costos on contrato.id_costo = costos.id  
                                left join unidades on unidades.id = viajes_divididos.id_unidad
                                left join colaboradores on colaboradores.id =viajes_divididos.id_operador
                                left join clientes on contrato.id_cliente = clientes.id
                                left join sucursales on viaje.sucursal = sucursales.id 
                            WHERE viajes_divididos.id_viaje = ".$viaje['id_viaje']." and viajes_divididos.cancelado  =0 and viajes_divididos.iniciado=1";
        $response =$db_gdlvan->prepare($sql);
        $response->execute();
        $resp=$response->fetch(PDO::FETCH_ASSOC);
        $inicial_iniciados = $resp['inicial_iniciados'];
        $sql = "SELECT      contrato.num_contrato,
                            unidades.num_economico,
                            viaje.id_unidad,
                            colaboradores.nombre as nombre_operador,
                            viaje.id_operador,
                            viaje.salida_exacta,
                            viaje.destino_exacto,
                            viaje.fecha_salida,
                            viaje.fecha_regreso,
                            viaje.hora_salida,
                            viaje.hora_regreso
                            from viaje
                                left join contrato on viaje.id_contrato = contrato.id
                                left join unidades on unidades.id = viaje.id_unidad
                                left join colaboradores on colaboradores.id =viaje.id_operador
                                left join movimientos on movimientos.id_viaje = viaje.id
                            WHERE viaje.id = ".$viaje['id_viaje']." order by movimientos.id asc";
        $response =$db_gdlvan->prepare($sql);
        $response->execute();
        $principal=$response->fetch(PDO::FETCH_ASSOC);
        $result = array(
            'code'=>200,
            'data'=>$divisiones,
            'principal'=>$principal,
            'inicial_iniciados'=>$inicial_iniciados,
            'inicial_asignados'=>$inicial_asignados
       );
       echo json_encode($result);  
    });
    
    $app->post('/getSeguimiento', function() use($app, $db_gdlvan, $db_cotizador){
        $json = $app->request->post('seguimiento');
        $seguimiento = json_decode($json, true);
        $where = "";
        $order ="  ORDER BY viaje.fecha_salida ASC ";
        //falta la sucursal
        if ($seguimiento['tab']=='x_asignar') {
            $where = " WHERE viaje.iniciado = 0 and id_contrato>0 and contrato.cancelado=0 and contrato.autorizado=1 and contrato.asignado = 0 ";
            
        }else if($seguimiento['tab']=='proximos'){
            $where = " WHERE id_contrato>0 and contrato.cancelado=0 and contrato.autorizado=1 and contrato.asignado = 1 and viaje.iniciado=0 ";
       
        }else if($seguimiento['tab']=='en_curso'){
            $where = " WHERE viaje.iniciado = 1 and viaje.cerrado=0 and viaje.terminado = 0 and id_contrato>0 and contrato.cancelado=0 and contrato.autorizado=1 and contrato.asignado = 1 ";
       
        }else if($seguimiento['tab']=='cerrados'){
            $where = " WHERE viaje.cerrado = 1 and viaje.terminado=0 and id_contrato>0 and contrato.cancelado=0 and contrato.autorizado=1 and contrato.asignado = 1 ";
            $order ="  ORDER BY viaje.fecha_salida ASC ";
        }else if($seguimiento['tab']=='completados'){
            $where = " WHERE viaje.terminado = 1 and id_contrato>0 and contrato.cancelado=0 and contrato.autorizado=1 and contrato.asignado = 1 ";
            $order ="  ORDER BY viaje.fecha_salida ASC";
        }
        $where2 = "";
        $sinSucursal = 0;
        $whereSucursal ="";
        if ($seguimiento['tipoFiltro']=='todos') {
        }else if ($seguimiento['tipoFiltro']=='operador') {

            $where2 = " and colaboradores.id =".$seguimiento['filtro']." ";
        }else if($seguimiento['tipoFiltro']=='unidad'){
            $where2 = " and unidades.id= ".$seguimiento['filtro']." ";
        }else if($seguimiento['tipoFiltro']=='cliente'){
            $where2 = " and clientes.nombre like '%".$seguimiento['filtro']."%' ";
        }else if($seguimiento['tipoFiltro']=='sucursal'){
            $where2 = " and viaje.sucursal= ".$seguimiento['filtro']." ";
            $sinSucursal  =1;
        }else if($seguimiento['tipoFiltro']=='fecha'){
            $fechas = explode('a', $seguimiento['filtro']);
            $where2 = " and date(viaje.fecha_salida) BETWEEN '".$fechas[0]."' and '".$fechas[1]."' ";
        }
        if ($sinSucursal==0) {
            $whereSucursal = " and viaje.sucursal= ".$seguimiento['sucursal']."  ";
        }
        $inicio = $seguimiento['cantidad']*($seguimiento['pagina']-1);

        $limit=" LIMIT ".$seguimiento['cantidad']." OFFSET ".$inicio." ";
        $sql = "SELECT  viaje.id,
                        contrato.num_contrato,
                        clientes.nombre as nombre_cliente, 
                        clientes.cel as celular_cliente, 
                        viaje.lugar_salida1 as lugar_salida,
                        viaje.fecha_salida,
                        viaje.lugar_destino1 as lugar_destino,
                        viaje.fecha_regreso,
                        viaje.tipo_viaje,
                        costos.total,
                        costos.restante,
                        unidades.num_economico,
                        unidades.placas as placas_unidad,
                        viaje.unidad,
                        viaje.id_unidad,
                        colaboradores.nombre as nombre_operador,
                        colaboradores.tel1 as telefono_operador,
                        colaboradores.apellidos as apellido_operador,
                        viaje.id_operador,
                        viaje.iniciado,
                        contrato.cancelado as cancelado,
                        viajes_divididos.id as dividido,
                        viaje.salida_exacta,
                        viaje.destino_exacto,
                        viaje.hora_salida,
                        viaje.hora_regreso
                        from viaje 
                            left join contrato on contrato.id = viaje.id_contrato 
                            left join costos on contrato.id_costo = costos.id  
                            left join unidades on unidades.id = viaje.id_unidad
                            left join colaboradores on colaboradores.id =viaje.id_operador
                            left join clientes on contrato.id_cliente = clientes.id
                            left join sucursales on viaje.sucursal = sucursales.id
                            left join viajes_divididos on viaje.id = viajes_divididos.id_viaje and viajes_divididos.cancelado = 0
                        ".$where." ".$where2.$whereSucursal." GROUP BY viaje.id";
        $whereUnion = "";
        if ($seguimiento['tab']=='x_asignar') {
            $whereUnion = " WHERE (viaje.id_unidad=0 OR viaje.id_operador=0) and viajes_divididos.iniciado = 0 and viajes_divididos.cerrado = 0 and viajes_divididos.cancelado = 0 and viajes_divididos.terminado = 0 and id_contrato>0 and contrato.cancelado=0 and contrato.autorizado=1 and asignado = 0 ";
            $order ="  ORDER BY fecha_salida desc";
        }else if($seguimiento['tab']=='proximos'){
            $whereUnion = " WHERE viaje.id_unidad>0 and viaje.id_operador>0 and id_contrato>0 and contrato.cancelado=0 and viajes_divididos.cancelado = 0 and contrato.autorizado=1 and contrato.asignado = 1 and viajes_divididos.cerrado=0 and viajes_divididos.iniciado=0 ";
            $order ="  ORDER BY fecha_salida asc, num_contrato asc";
        }else if($seguimiento['tab']=='en_curso'){
            $whereUnion = " WHERE viajes_divididos.iniciado = 1 and viajes_divididos.cerrado=0 and viajes_divididos.terminado = 0 and id_contrato>0 and viajes_divididos.cancelado = 0 and contrato.cancelado=0 and contrato.autorizado=1 and contrato.asignado = 1 ";
            $order ="  ORDER BY fecha_salida asc, num_contrato asc";
        }else if($seguimiento['tab']=='cerrados'){
            $whereUnion = " WHERE viajes_divididos.cerrado = 1 and viajes_divididos.terminado=0 and id_contrato>0 and contrato.cancelado=0 and viajes_divididos.cancelado = 0 and contrato.autorizado=1 and contrato.asignado = 1 ";
            $order ="  ORDER BY fecha_regreso, num_contrato asc ";
        }else if($seguimiento['tab']=='completados'){
            $whereUnion = " WHERE viajes_divididos.terminado = 1 and id_contrato>0 and contrato.cancelado=0 and viajes_divididos.cancelado = 0 and contrato.autorizado=1 and contrato.asignado = 1 ";
            $order ="  ORDER BY fecha_regreso desc, num_contrato asc ";
        }
        $sqlUnion="SELECT   viaje.id,
                            CONCAT(contrato.num_contrato,'-',viajes_divididos.consecutivo) as num_contrato,
                            clientes.nombre as nombre_cliente,
                            clientes.cel as celular_cliente,
                            viaje.lugar_salida1 as lugar_salida,
                            viaje.fecha_salida,
                            viaje.lugar_destino1 as lugar_destino,
                            viaje.fecha_regreso,
                            viaje.tipo_viaje,
                            costos.total,
                            costos.restante,
                            unidades.num_economico,
                            viaje.unidad,
                            viajes_divididos.id_unidad,
                            unidades.placas as placas_unidad,
                            colaboradores.nombre as nombre_operador,
                            colaboradores.tel1 as telefono_operador,
                            colaboradores.apellidos as apellido_operador,
                            viajes_divididos.id_operador,
                            viajes_divididos.iniciado,
                            viajes_divididos.cancelado as cancelado,
                            viajes_divididos.id as dividido,
                            viaje.salida_exacta,
                            viaje.destino_exacto,
                            viaje.hora_salida,
                            viaje.hora_regreso
                            from viajes_divididos 
                                left join viaje on viaje.id = viajes_divididos.id_viaje 
                                left join contrato on contrato.id = viaje.id_contrato 
                                left join costos on contrato.id_costo = costos.id  
                                left join unidades on unidades.id = viajes_divididos.id_unidad
                                left join colaboradores on colaboradores.id =viajes_divididos.id_operador
                                left join clientes on contrato.id_cliente = clientes.id
                                left join sucursales on viaje.sucursal = sucursales.id
                            ".$whereUnion." ".$where2.$whereSucursal;
        $sql = "SELECT * FROM(".$sql." UNION ALL ".$sqlUnion.") as temp ".$order;
        
        $response = $db_gdlvan->prepare($sql.$limit);
        $response->execute();
        $respuestas = $response->fetchAll(PDO::FETCH_ASSOC);
        $tam = count($respuestas);
        $ind = 0;
        while ($ind<$tam) {
            $muni = "SELECT municipio from municipios WHERE id = ".$respuestas[$ind]['lugar_salida']." UNION ALL SELECT municipio from municipios WHERE id = ".$respuestas[$ind]['lugar_destino'] ;
            $response= $db_cotizador->prepare($muni);
            $response->execute();
            $municipios = $response->fetchAll(PDO::FETCH_ASSOC);
            $salida = explode(",",$municipios[0]['municipio']);
            $destino = explode(",",$municipios[1]['municipio']);
            $respuestas[$ind]['lugar_salida']= $salida[0].", ".$salida[1];        
            $respuestas[$ind]['lugar_destino']=$destino[0].", ".$destino[1];
            $ind++;
        }
       /* 
            $sql = "SELECT count(*)as total  from viaje
                left join contrato on contrato.id = viaje.id_contrato 
                            left join costos on contrato.id_costo = costos.id  
                            left join unidades on unidades.id = viaje.id_unidad
                            left join colaboradores on colaboradores.id =viaje.id_operador
                            left join clientes on contrato.id_cliente = clientes.id
                            left join sucursales on sucursales.id = viaje.sucursal
                            ".$where." ".$where2.$whereSucursal;
        */
        $sqlcont  = "SELECT count(*) as total FROM(".$sql.") as temp";
        $response = $db_gdlvan->prepare($sqlcont);
        $response->execute();
        $totalidad = $response->fetch(PDO::FETCH_ASSOC);
        $total = $totalidad['total'];
        $result = array(
            'code'=>200,
            'status'=>'success',
            'sql'=>"SELECT * FROM(".$sql." UNION ALL ".$sqlUnion.") as temp ".$limit,
            'data'=>$respuestas,
            'total'=>$total,
            'cantidad'=>sizeof($respuestas)
        );
        echo json_encode($result);
    });



	$app->run();

    function agregarAmperson($cadena){
        return  str_replace('|amperson|', '&', $cadena);
    }
?>

