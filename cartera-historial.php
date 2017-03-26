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
// include "$rootPath/$school/funciones.php";
include "$rootPath/$school/funciones.php";
include "$rootPath/class/StudentWallet.class.php";
include "$rootPath/$school/includes/sesion.inc";

login();

$id = $_SESSION['id'];
$perfil = $_SESSION['perfil'];
$id_ano = $_SESSION['ano'];



if ($perfil != '8'){
	//header("Location: ../error.php");
	//exit();
}

$Estudiante =  new StudentWallet($id, date("Y"), $nameDb, $usrDb, $passDb);
	
?>   

 

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../cartera/cartera-style.css" type="text/css" rel="stylesheet" />
<title>Impresion de Recibos . . .</title>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="../cartera/cartera-scripts.js"></script>
 <style>
  .custom-combobox {
    position: relative;
    display: inline-block;
	font-size: 12px;

  }
  .custom-combobox-toggle {
    position: absolute;
    top: 0;
    bottom: 0;
    margin-left: -1px;
    padding: 0;
    /* support: IE7 */
    *height: 1.7em;
    *top: 0.1em;
  }
  .custom-combobox-input {
    margin: 0;
    padding: 0.3em;
	width: 300px;
  }
   .ui-autocomplete {
    max-height: 350px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
	font-size: 12px;
	text-align: left;
  }
  /* IE 6 doesn't support max-height
   * we use height instead, but this forces the menu to always be this tall
   */
  * html .ui-autocomplete {
    height: 300px;
  }
  </style>
</head>

<body>
	<header class="bolivia">
    	Liceo psicopedagogico bolivia - Cartera
    </header>
    <article style="background:#FFF">
    	<a href="cartera.html"><h1 class="left"> <p style="margin: 5px;">Impresion de Recibos . . . </p></h1></a>
        <br />
        <br />
        
        <h2>Bienvenido al módulo de impresión  de recibos...!</h2>
                     <br />
                     
            <section>
            <h2>Datos estudiante</h2>
        	<div class="filaTitulo">
                <span class="nombre">
                	<?php echo $Estudiante->name." ".$Estudiante->lastName; ?>
                </span>
            </div>
            <div class="filaHTabla">
                <span class="nombre" style="width: 200px;display: inline-block;">
                	Curso
                </span>
                <span class="nombre" style="width: 200px;display: inline-block;">
                	ID comunidad
                </span>
                <span class="nombre" style=" display: inline-block;">
                	ID cartera
                </span>
                
            </div>
        	<div class="filaSimple" style="text-align:left;">
                <span style="width: 200px; display: inline-block; text-align:left">
                	<?php echo $Estudiante->getNameCurse(); ?>
                </span>
                <span style="width: 200px; display: inline-block; text-align:left">
                	<?php echo $Estudiante->idCv; ?>
                </span>
                <span style=" display: inline-block; text-align:left"> 
                <?php echo $Estudiante->getIdWallet(); ?>
                </span>
            </div>
         
            <div class="ajustar"></div>
            <br /><br /><br />
            <h2>Cobros Pendientes</h2>
             <div class="filaHTabla" style="background-color: #999;">
                <span class="nombre" style="width: 200px;display: inline-block;"> NOMBRE </span>
                <span class="nombre" style="width: 100px;display: inline-block;"> PERIODO </span>
                <span class="nombre" style="display: inline-block;"> IMPRIMIR </span>
            </div>

            <?php 
            foreach ( $Estudiante->getPendingPayments() as $key ) {            		
             ?>	
            <div class="filaHTabla" style="background-color: #FFF; color: #666666;">

                <span class="nombre" style="width: 200px;display: inline-block;"><?php echo $key[PAY_NAME]; ?></span>
                <span class="nombre" style="width: 100px;display: inline-block;"><?php echo NombreMes($key[PERIOD]); ?></span>
                <span class="nombre" style="width: 100px;display: inline-block; color: #817BC1;">
                          <img class="evento" src="../../../imagenes_cv/cartera-imprimir.png" width="30" height="30" 
                          alt="editar" 
                          onclick="imprimir('<?php echo $key[PERIOD] ;?>', '<?php echo $key[ID_PAY];?>', '<?php echo $key[PAY_NAME];?>')" />
                       
                </span>
            </div>
            <?php 
       		 }
       		 ?>

            
            
          <div class="ajustar"></div>
          <input type="hidden" name="estudiante" value="<?php echo $estudiante; ?>" />
          
            <script type="text/javascript">
			function imprimir(periodo, pago, nombre){
				
				
					var UrlEditar = 
		"cartera-recibo-base-test.php?periodo="+periodo+"&pago="+pago+"&nombre="+nombre+"&estudiante=<?php echo $id?>";
					
						
						window.open(UrlEditar, 'width=900,height=600');
					}//fin eliminar
			</script>
          
          
            <script type="text/javascript" src="../cartera/cartera-scripts.js"></script>
		<div class="ajustar"></div>
            <br /><br /><br />
		
		
            
            </section>
            <br /><br />
            <div id="resultado" class="resultado"></div>
            <div class="ajustar"></div>
     
           <br />
           <div class="ajustar"></div> 
    </article>
    <footer>
    	 comunidades virtuales online 
    </footer>
</body>
</html>