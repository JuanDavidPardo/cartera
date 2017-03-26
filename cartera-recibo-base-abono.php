<?php
# Aqui detectamos el coelgio al que se conecto el usuario.
# por url digitada pordemos detectar a el colegio al que desea 
# conectarse el usuario y apartir de allí generamos todas las 
# coexiones y funciones necesarias para dicho colegio.

# Atrapamos la url (todo luego del dominio);
$url = $_SERVER['REQUEST_URI'];
# separamos la URL por "/" y de esta manera leeriamos el colegio que es la primera 
# palabra luego del dominio.
$school = explode("/", $url);
# el colegio es el numero 1 ya que 0 siempre estara vacio ya que se encuentra de la 
# la siguiente manera "/colegio/carpeta/" por tanto cero cerio null ya que a la izquierda
# del "/" no hay nada.
$school = $school[1];

# detecto el directorio raiz
$rootPath = $_SERVER['DOCUMENT_ROOT'];



/* LIBRERIAS PDF Y BARCODE
   ____________________________________________________
  | * Estas librerias incluyen el tipo de codigo que es|
  | GS1-128 y otras que nos permiten asignar           |
  | propiedades al codigo de barras                    |
  | * Tambien esta la libreria FPDF.PHP que nos permite|
  | crear una vista de PDF bajo codigo php.            |
  |____________________________________________________|

*/
require "$rootPath/$school/cartera/BARCODE/BCGFontFile.php";
require "$rootPath/$school/cartera/BARCODE/BCGColor.php";
require "$rootPath/$school/cartera/BARCODE/BCGDrawing.php";
require "$rootPath/$school/cartera/BARCODE/BCGgs1128.barcode.php";
// FIN LIBRERIAS PDF Y BARCODE--------------------------


include "$rootPath/$school/funciones.php";
include "$rootPath/class/StudentWallet.class.php";
include "$rootPath/$school/includes/sesion.inc";
header( 'Content-Type: text/html;charset=utf-8' ); 
 
login();
$id_ano = $_SESSION['ano']; 
$id     = $_SESSION['id']; 
$periodo = $_GET['periodo'];
$pago = $_GET['pago'];
$estudiante = $_GET['estudiante'];
$nombreCobro = $_GET['nombre'];
$NIT = "900 128 830-2";
$CONVENIO = "16110";
$CodeEntidad = "1516110600212";

$fecha = date ("d/m/Y",time ());


$Estudiante =  new StudentWallet($id, date("Y"), $nameDb, $usrDb, $passDb);

# Traigo todos pagos pendientes con becas y pagos adicionales.
$pendingPaymentsFull = $Estudiante->getPendingPaymentsFull();

# Atrap la referencia de pago.
$CodePago = $pendingPaymentsFull[0][PAY_REFERENCE];
setlocale(LC_MONETARY, 'en_US');	
function SinCeros($valor){
  
  $valor = substr($valor, 0, strlen($valor)-3);
  return $valor;
}


function fecha($fecha){
	
	$fecha = str_replace("-","",$fecha);
	return $fecha;
	
	}	

$cv_db = base_datos();
$linkd = conectar_db($cv_db);


$hoy = date("Y-m-d H:i:s");	  
//echo $hoy;
$mysql = "INSERT INTO  `pago-recibo-consulta`  (`id_est`, `id_pva`, `fec_con`) 
		   VALUES ('".$estudiante."', '".$pago."', '".$hoy."')";
mysql_query($mysql, $linkd);	// guardamos quienes han consultado (estudiante, pago y fecha)
?>
<?php ob_start(); ?>
<head>            
  <link href="../cartera/cartera-style.css" type="text/css" rel="stylesheet" />  
  <link href="../cartera/cartera-style-recibo.css" type="text/css" rel="stylesheet" />
  <META http-equiv=Content-Type content="text/html; charset=utf-8">           
</head>
<body style="background-color: #FFF; text-align:center;">

  <div class="recibo-ajustar"></div>  
  <div class="recibo-header" style="margin: 0 auto">
    <div class="recibo-escudo">
    <img src="../../imagenes_cv/escudo_bolivia.png" width="38" height="42"/>
    </div>
    <div class="recibo-colegio">
      <div>
          COLEGIO LICEO PSICOPEDAGOGICO BOLIVIA
      </div>
      <div>
          "Fortaleciendo los derechos humanos  con una Educación Integral y Autónoma"
      </div>
      <div>
          NIT: <?PHP echo " ".$NIT; ?>
      </div>
	<div>
          DIR.: Clle 81 # 106A - 10&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TEL.: 2274881
      </div>
    </div>
    
  </div><!-- FIN HEADER RECIBO -->
  <div class="recibo-cuerpo" style="margin: 0 auto;">
  <div class="recibo-fila">
     	<div class="recibo-caja-contenedor" style="dwidth: 496px;">
        	<div class="recibo-caja-arriba" style="display:inline-block; width: 494px; left: 0px; background-color:#FFF">
     <span style="width: 90px; display:inline-block; background-color: #E4E3E6; margin-bottom:1px;">N° CONVENIO</span>
    <span style="width: 60px; background:#FFF; display:inline-block; margin-bottom:1px;"> <?PHP echo " ".$CONVENIO; ?></span>
    <span style="width: 80px; display:inline-block; background-color: #E4E3E6; margin-bottom:1px;">N° RECIBO</span>
    <span style="width: 80px; background:#FFF; display:inline-block; margin-bottom:1px;"> <?php echo $pendingPaymentsFull[0][ID_PAY]; ?> </span>
    <span style="width: 80px; display:inline-block; background-color: #E4E3E6; margin-bottom:1px;">REF. PAGO </span>
    <span style="width: 80px; background:#FFF; display:inline-block; margin-bottom:1px;"> <?php echo $CodePago; ?> </span>
            </div>
        </div>
      </div>
  	<div class="recibo-fila">
          <div class="recibo-caja-contenedor" >
            <div class="recibo-caja-arriba" style="width: 496px;">
              <span style=" display: inline-block; width: 200px;">
                  NOMBRE ESTUDIANTE
              </span>
              <span style=" display: inline-block; width: 120px;">
                  IDENTIFICACION-CV
              </span>
              <div style=" display: inline-block; width: 50px;">
                  CURSO
              </div>
              <span style=" display: inline-block; width: 110px;">
                  PERIODO
              </span>
            </div>
            <div class="recibo-caja-abajo">
            	<span style=" display: inline-block; width: 200px;">
                	<?php echo ucwords(strtolower($Estudiante->lastName." ".$Estudiante->name)); ?>
                </span>
               	<span style=" display: inline-block; width: 120px;">
                	<?php echo $Estudiante->idCv; ?>
                </span>
              	<span style=" display: inline-block; width: 50px;">
                	<?php echo $Estudiante->getNameCurse(); ?>
                </span>
                <span style=" display: inline-block; width: 110px;">
                	<?php echo NombreMes($pendingPaymentsFull[0][PERIOD]); ?>
                </span>
            </div>
          </div>     
    </div>
   
    <div class="recibo-fila">
     <div class="recibo-caja-contenedor">
          	<div class="recibo-fila">
            	<div class="recibo-caja-arriba" style="width: 496px; margin: 0; display: block;">
                  <div>
                    <span style=" display: inline-block; width: 486px; text-align: center;">
                     CONCEPTOS LIQUIDADOS
                    </span
                  ></div>
                  <div>
                    <span style=" display: inline-block; width: 370px; font-size: 9px;">
                     DESCRIPCIÓN
                    </span>
                    <span style=" text-align: right;display: inline-block; width: 110px; font-size: 9px;">
                     VALOR
                    </span>
                  </div>
                </div>
            </div>

            <div class="recibo-fila">
            	<div class="recibo-caja-abajo" style="width: 496px; margin: 0; display: block;">
            	<?php  foreach ( $pendingPaymentsFull as $key ) : ?>
				  <div>
                    <span style=" display: inline-block; width: 370px; font-size: 9px;">

                     <?php echo "  ".strtoupper($key[DESCRIPTION]." - ".NombreMes($key[PERIOD])); ?>
                    </span>
                    <span style=" text-align: right;display: inline-block; width: 110px; font-size: 9px;">
                     <?php echo SinCeros(money_format('%+#10n', $key[REALVALUE])) ; ?>
                    </span>
                  </div>
                  <?php if( $key[ADDITIONALPAYMENTS] != 0) { ?>
                  <div>
                    <span style=" display: inline-block; width: 370px; font-size: 9px;">
                     <?php echo "  ".strtoupper($key[ADDITIONALPAYMENTS_DES]." - ".NombreMes($key[PERIOD])); ?>
                    </span>
                    <span style=" text-align: right;display: inline-block; width: 110px; font-size: 9px;">
                     <?php echo SinCeros(money_format('%+#10n',$key[ADDITIONALPAYMENTS])); ?>
                    </span>
                  </div> 
                  <?php } ?>
                  <?php if( $key[SCHOLARSHIP] != 0) { ?>
                  <div>
                    <span style=" display: inline-block; width: 370px; font-size: 9px;  color: #13BA0B;">
                     <?php echo "  ".strtoupper($key[SCHOLARSHIP_DES]." - ".NombreMes($key[PERIOD])); ?>
                    </span>
                    <span style=" text-align: right;display: inline-block; width: 110px; font-size: 9px; color: #13BA0B;">
                     <?php echo SinCeros(money_format('%+#10n',$key[SCHOLARSHIP])); ?>
                    </span>
                  </div> 
                  <?php } ?>

                <?php 
                	$PFinalValor = $PFinalValor + $key[REALVALUE] + $key[ADDITIONALPAYMENTS] + $key[SCHOLARSHIP]; 
                	if ($pago == $key[ID_PAY]) : break 1; endif;
                endforeach;
                ?>
                <?php 
                  if( $Estudiante->getPositiveBalance() != 0 ) : 
                  	$positiveBalance = $Estudiante->getPositiveBalance();
                  	$PFinalValor = $PFinalValor - $positiveBalance[0][VALUE];
                  ?>
                  <div>
                    <span style=" display: inline-block; width: 370px; font-size: 9px;  color: #13BA0B;">
                     <?php echo "SALDO POSITIVO"; ?>
                    </span>
                    <span style=" text-align: right;display: inline-block; width: 110px; font-size: 9px; color: #13BA0B;">
                     <?php echo SinCeros(money_format('%+#10n',$positiveBalance[0][VALUE]*(-1))); ?>
                    </span>
                  </div> 
                  <?php endif; ?>

                <?php
				$periodoExiste = array_search(date("m"), array_column($scholarShipMatrix, 'PERIOD'));

				?>
                <div style="height: 20px; font-size: 13px; font-weight:bold; width: 450px; text-align:right;">
                	<span>
                    	VALOR A PAGAR : <?php echo SinCeros(money_format('%+#10n',$PFinalValor)); ?>
                    </span>
                </div>
            </div>
      </div>
 
    </div>
    <div class="recibo-fila">
          <div class="recibo-caja-contenedor" >
            <div class="recibo-caja-arriba" style="width: 496px;">
              <span style=" display: inline-block; width: 200px;">
                  ENTIDAD BANCARIA
              </span>
            </div>
            <div class="recibo-caja-abajo">
            	<span style=" display: inline-block; width: 450px;">
                	Consigne en cualquier oficina de Banco Caja Social
                    <br>N° Cuenta Corriente 21500 278 292
                </span>
            </div>   	   
          </div>
    </div>

    <div class="recibo-fila" style=" font-size: 7px; color:#666; text-align:right;">
    	Copia Estudiante
    </div> 
  
  <!-- - - --- - - - -- - -- - - - -- - FIN PRIMER RECIBO - - -- - ---- - --- - - - -- -->
  <div class="recibo-division"></div>
  
   <div class="recibo-ajustar"></div> 
  <div class="recibo-header" style="margin: 0 auto">
    <div class="recibo-escudo">
    <img src="../../imagenes_cv/escudo_bolivia.png" width="38" height="42"/>
    </div>
    <div class="recibo-colegio">
      <div>
          COLEGIO LICEO PSICOPEDAGOGICO BOLIVIA
      </div>
      <div>
          "Fortaleciendo los derechos humanos  con una Educación Integral y Autónoma"
      </div>
      <div>
          NIT: <?PHP echo " ".$NIT; ?>
      </div>
	<div>
          DIR.: Clle 81 # 106A - 10&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TEL.: 2274881
      </div>
    </div>
    
  </div><!-- FIN HEADER RECIBO -->
  <div class="recibo-cuerpo" style="margin: 0 auto;">
  <div class="recibo-fila">
     	<div class="recibo-caja-contenedor" style="dwidth: 496px;">
        	<div class="recibo-caja-arriba" style="display:inline-block; width: 494px; left: 0px; background-color:#FFF">
     <span style="width: 90px; display:inline-block; background-color: #E4E3E6; margin-bottom:1px;">N° CONVENIO</span>
    <span style="width: 60px; background:#FFF; display:inline-block; margin-bottom:1px;"> <?PHP echo " ".$CONVENIO; ?></span>
    <span style="width: 80px; display:inline-block; background-color: #E4E3E6; margin-bottom:1px;">N° RECIBO</span>
    <span style="width: 80px; background:#FFF; display:inline-block; margin-bottom:1px;"> <?php echo $pendingPaymentsFull[0][ID_PAY]; ?> </span>
    <span style="width: 80px; display:inline-block; background-color: #E4E3E6; margin-bottom:1px;">REF. PAGO </span>
    <span style="width: 80px; background:#FFF; display:inline-block; margin-bottom:1px;"> <?php echo $CodePago; ?> </span>
            </div>
        </div>
      </div>
  	<div class="recibo-fila">
          <div class="recibo-caja-contenedor" >
            <div class="recibo-caja-arriba" style="width: 496px;">
              <span style=" display: inline-block; width: 200px;">
                  NOMBRE ESTUDIANTE
              </span>
              <span style=" display: inline-block; width: 120px;">
                  IDENTIFICACION-CV
              </span>
              <div style=" display: inline-block; width: 50px;">
                  CURSO
              </div>
              <span style=" display: inline-block; width: 110px;">
                  PERIODO
              </span>
            </div>
            <div class="recibo-caja-abajo">
            	<span style=" display: inline-block; width: 200px;">
                	<?php echo ucwords(strtolower($Estudiante->lastName." ".$Estudiante->name)); ?>
                </span>
               	<span style=" display: inline-block; width: 120px;">
                	<?php echo $Estudiante->idCv; ?>
                </span>
              	<span style=" display: inline-block; width: 50px;">
                	<?php echo $Estudiante->getNameCurse(); ?>
                </span>
                <span style=" display: inline-block; width: 110px;">
                	<?php echo NombreMes($pendingPaymentsFull[0][PERIOD]); ?>
                </span>
            </div>
          </div>     
    </div>
   
    <div class="recibo-fila">
     <div class="recibo-caja-contenedor">
          	<div class="recibo-fila">
            	<div class="recibo-caja-arriba" style="width: 496px; margin: 0; display: block;">
                  <div>
                    <span style=" display: inline-block; width: 486px; text-align: center;">
                     CONCEPTOS LIQUIDADOS
                    </span
                  ></div>
                  <div>
                    <span style=" display: inline-block; width: 370px; font-size: 9px;">
                     DESCRIPCIÓN
                    </span>
                    <span style=" text-align: right;display: inline-block; width: 110px; font-size: 9px;">
                     VALOR
                    </span>
                  </div>
                </div>
            </div>

            <div class="recibo-fila">
            	<div class="recibo-caja-abajo" style="width: 496px; margin: 0; display: block;">
            	<?php $PFinalValor = 0;
            		  $PFecha 	   = NULL;
            		  $SFecha 	   = NULL;
            		  $intereses   = NULL;         		  
            		    	
					  foreach ( $pendingPaymentsFull as $key ) : 
						# Buscamos el periodo para saber el interes y las fechas
					  	# si el no encuentra el periodo actual el primero mayor
					  	# ejemplo si busca junio y sigue con julio, etc.
						if ( 
							 ( 
							 	$periodo == $key[PERIOD] && fecha($key[DATE2]) >= date("Ymd") && is_null($intereses)
							 ) 
							 || 
							 ( 
							   is_null($intereses) && fecha($key[DATE2]) >= date("Ymd")
							 ) 
						   ) : 
							$PFecha 	= $key[DATE1];
							$SFecha 	= $key[DATE2];
            		  		$intereses  = $key[VALUE2] - $key[VALUE1]; 
						endif;
				?>
				  <div>
                    <span style=" display: inline-block; width: 370px; font-size: 9px;">
                     <?php echo $intereses." ".strtoupper($key[DESCRIPTION]." - ".NombreMes($key[PERIOD])); ?>
                    </span>
                    <span style=" text-align: right;display: inline-block; width: 110px; font-size: 9px;">
                     <?php echo SinCeros(money_format('%+#10n', $key[REALVALUE])) ; ?>
                    </span>
                  </div>
                  <?php if( $key[ADDITIONALPAYMENTS] != 0) : ?>
                  <div>
                    <span style=" display: inline-block; width: 370px; font-size: 9px;">
                     <?php echo "  ".strtoupper($key[ADDITIONALPAYMENTS_DES]." - ".NombreMes($key[PERIOD])); ?>
                    </span>
                    <span style=" text-align: right;display: inline-block; width: 110px; font-size: 9px;">
                     <?php echo SinCeros(money_format('%+#10n',$key[ADDITIONALPAYMENTS])); ?>
                    </span>
                  </div> 
                  <?php endif; ?>
                  <?php if( $key[SCHOLARSHIP] != 0) : ?>
                  <div>
                    <span style=" display: inline-block; width: 370px; font-size: 9px;  color: #13BA0B;">
                     <?php echo "  ".strtoupper($key[SCHOLARSHIP_DES]." - ".NombreMes($key[PERIOD])); ?>
                    </span>
                    <span style=" text-align: right;display: inline-block; width: 110px; font-size: 9px; color: #13BA0B;">
                     <?php echo SinCeros(money_format('%+#10n',$key[SCHOLARSHIP])); ?>
                    </span>
                  </div> 
                  <?php endif; ?>

                <?php 
                	$PFinalValor = $PFinalValor + $key[REALVALUE] + $key[ADDITIONALPAYMENTS] + $key[SCHOLARSHIP];
                	if ($pago == $key[ID_PAY]) : break 1; endif;
                endforeach;
                ?>
                  <?php 
                  if( $Estudiante->getPositiveBalance() != 0 ) : 
                  	$positiveBalance = $Estudiante->getPositiveBalance();
                  	$PFinalValor = $PFinalValor - $positiveBalance[0][VALUE];
                  ?>
                  <div>
                    <span style=" display: inline-block; width: 370px; font-size: 9px;  color: #13BA0B;">
                     <?php echo "SALDO POSITIVO"; ?>
                    </span>
                    <span style=" text-align: right;display: inline-block; width: 110px; font-size: 9px; color: #13BA0B;">
                     <?php echo SinCeros(money_format('%+#10n',$positiveBalance[0][VALUE]*(-1))); ?>
                    </span>
                  </div> 
                  <?php endif; ?>

                <?php
                $periodoExiste = array_search(date("m"), array_column($scholarShipMatrix, 'PERIOD'));

                # Si el periodo descargado ya paso y se va a pagar con mora buscamos la fecha del primer pago los pagos;
                $PFecha = is_null($PFecha) ? $pendingPaymentsFull[0][DATE2] : $PFecha; 
				?>
                
                <div style="height: 20px; font-size: 13px; font-weight:bold; width: 450px; text-align:right;">
                	<span>
                    	VALOR A PAGAR : <?php echo SinCeros(money_format('%+#10n',$PFinalValor)); ?>
                    </span>
                </div>
            </div>
      </div>
  </div>    
  <div class="recibo-fila">
   <div class="recibo-caja-contenedor" style="dwidth: 496px;">
  	<div class="recibo-caja-arriba" style="display:inline-block; width: 494px; left: 0px; background-color:#FFF">
      <span style="width: 140px; display:inline-block; background-color: #E4E3E6; margin-bottom:1px;">FECHA DE PAGO HASTA</span>
      <span style="width: 90px; background:#FFF; display:inline-block; margin-bottom:1px;"><?php echo $PFecha; ?></span>
      <span style="width: 90px; display:inline-block; background-color: #E4E3E6; margin-bottom:1px;">VALOR</span>
      <span style="width: 90px; background:#FFF; display:inline-block; margin-bottom:1px;">
          <?php echo SinCeros(money_format('%+#10n',$PFinalValor)); ?>
      </span>
    </div>
    <div>
            
            	<?php 
				
					/* PROPIEDADES CODIGO DE BARRAS
					   ____________________________________________________
					  | Aqui asiganmos las propiedades de codido de barras |
					  |____________________________________________________|
					*/
					$color_black = new BCGColor(0, 0, 0);
					$color_white = new BCGColor(255, 255, 255);
					$font = new BCGFontFile('gs1class/font/Arial.ttf', 16);
					 
					$code = new BCGcode128();
					$code->setScale(3);
					$code->setThickness(50);
					$code->setForegroundColor($color_black);
					$code->setBackgroundColor($color_white);
					$code->setFont($font);
					$code->setStart(NULL);
					$code->setLabel(NULL);
					
					// FIN DE PROPIEDADES CODIGO DE BARRAS ------------------
					
					
					/* GENRAMOS E IMPRIMIRMOS EL CODIGO DE BARRAS
					   ____________________________________________________
					  | Aqui Generamos el codigo de barras                 |
					  | * Guardamos en la variable $codigo  el codigo a    |
					  | impirmir, luego la conbertimos a codigo de barras  |
					  | con parse.										   |
					  | * Asignmos un espacio temporal para la imagen que  |
					  | sera el codigo de barras.                          |
					  | * creamo la funcion par ya que los codgidos deben  |
					  |  ser pares.                                        |
					  | * Debajo escribimos el codigo en numeros ejemplo:  |
					  | (415)234578812(8020)3234234....					   |								 
					  |____________________________________________________|
					*/
					
					$img1 = 0;
					// Valores contenidos en codigo de barras
					
					$CodeValor   = numeroDigitosPar($PFinalValor);
					$CodeFecha   = fecha($PFecha);
					
					$codigo = "~F1415".$CodeEntidad."~F18020".$CodePago."~F13900".$CodeValor."~F196".$CodeFecha;
					$code->parse($codigo);
					//echo $codigo;
					$img1++; //aumentamos el contador para imagen temporal 
					$drawing = new BCGDrawing('../administrador/cartera/BARCODE/temp/barcode'.$Cartera.'.png', $color_white); // Guarda la imagen en la 
					$imagen_codigo='../administrador/cartera/BARCODE/temp/barcode'.$Cartera.'.png';                     // carpeta(TEMP) que debe existir
					$drawing->setBarcode($code);
					$drawing->draw();
					$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
					$drawing->destroy();

				
				?>
               <img src="../administrador/cartera/BARCODE/temp/barcode<?php echo $Cartera; ?>.png" width="450px" height="60px" style="margin: 10px;"/>
              <span style="margin-top: -10px; font-size: 9px; font-weight: bold; display: block;">
			   <?php echo "(415)".$CodeEntidad."(8020)".$CodePago."(3900)".$CodeValor."(96)".$CodeFecha?>
               </span>
            
        </div>
      </div>
    </div>    
        <?php 
        	# Si no existe interes no necitamos imprimir un segundo codigo de barras.
        	if ( is_null($intereses) || $intereses == 0 ): $SFecha = $PFecha;  endif;
        ?>
			<div class="recibo-fila">
              <div class="recibo-caja-contenedor" style="dwidth: 496px;">
                <div class="recibo-caja-arriba" style="display:inline-block; width: 494px; left: 0px; background-color:#FFF">
            	  <span style="width: 140px; display:inline-block; background-color: #E4E3E6; margin-bottom:1px;">FECHA DE PAGO DESDE</span>
            	  <span style="width: 90px; background:#FFF; display:inline-block; margin-bottom:1px;"><?php echo $SFecha; ?></span>
            	  <span style="width: 90px; display:inline-block; background-color: #E4E3E6; margin-bottom:1px;">VALOR</span>
            	  <span style="width: 90px; background:#FFF; display:inline-block; margin-bottom:1px;">
                	<?php echo SinCeros(money_format('%+#10n',($PFinalValor + $intereses))); ?>
            	  </span>
                    </div>
                   <?php 
        		   
        		   /* PROPIEDADES CODIGO DE BARRAS
        			   ____________________________________________________
        			  | Aqui asiganmos las propiedades de codido de barras |
        			  |____________________________________________________|
        			*/
        			$color_black = new BCGColor(0, 0, 0);
        			$color_white = new BCGColor(255, 255, 255);
        			$font = new BCGFontFile('gs1class/font/Arial.ttf', 16);
        			 
        			$Scode = new BCGcode128();
        			$Scode->setScale(3);
        			$Scode->setThickness(50);
        			$Scode->setForegroundColor($color_black);
        			$Scode->setBackgroundColor($color_white);
        			$Scode->setFont($font);
        			$Scode->setStart(NULL);
        			$Scode->setLabel(NULL);
        			
        			// FIN DE PROPIEDADES CODIGO DE BARRAS ------------------
        		   
        		    $img2 = 1000;
        		   // Valores contenidos en codigo de barras
        					
        					$SCodeValor   = numeroDigitosPar($PFinalValor + $intereses);
        					$SCodeFecha   = fecha($SFecha);
        					
        					$Scodigo = "~F1415".$CodeEntidad."~F18020".$CodePago."~F13900".$SCodeValor."~F196".$SCodeFecha;
        					$Scode->parse($Scodigo);
        					$img2++; //aumentamos el contador para imagen temporal 
        					$Sdrawing = new BCGDrawing('../administrador/cartera/BARCODE/temp/barcode'.$Cartera.'2.png', $color_white); // Guarda la imagen en la 
        					$Simagen_codigo='../administrador/cartera/BARCODE/temp/barcode'.$Cartera.'2.png';                   // carpeta(TEMP) que debe existir
        					$Sdrawing->setBarcode($Scode);
        					$Sdrawing->draw();
        					$Sdrawing->finish(BCGDrawing::IMG_FORMAT_PNG);
        					$drawing->destroy();
        
        		   ?>
                   <img src="../administrador/cartera/BARCODE/temp/barcode<?php echo $Cartera; ?>2.png" width="450px" height="60px" style="margin: 10px;"/>
                   <span style="margin-top: -10px; font-size: 9px; font-weight: bold; display: block;">
        			   <?php echo "(415)".$CodeEntidad."(8020)".$CodePago."(3900)".$SCodeValor."(96)".$SCodeFecha?>
                       </span>
                </div>
              </div>          	
        
       
      
    <div class="recibo-fila">
          <div class="recibo-caja-contenedor" >
            <div class="recibo-caja-arriba" style="width: 496px;">
              <span style=" display: inline-block; width: 200px;">
                  ENTIDAD BANCARIA
              </span>
            </div>
            <div class="recibo-caja-abajo">
            	<span style=" display: inline-block; width: 450px;">
                	Consigne en cualquier oficina de Banco Caja Social
                    <br>N° Cuenta Corriente 21500 278 292
                </span>
            </div>   	   
          </div>
    </div>
    <div class="recibo-fila" style=" font-size: 7px; color:#666; text-align:right;">
    	Copia Banco
    </div>
  
</body>
<?php
	  
	   	   
	   
require_once("../cartera/DOMPDF/dompdf_config.inc.php");
$dompdf = new DOMPDF();
$dompdf->set_paper('letter', 'portrait');
$dompdf->load_html(ob_get_clean());
$dompdf->render();
header('Content-type: application/pdf');
//$filename = "RECIBO-".mes($periodo)."-".$nombre.'.pdf';   | estos codigo guardan una copia en el server.
//file_put_contents($filename, $pdf);                       |
 echo $dompdf->output();
?>
