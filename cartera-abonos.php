<?php 
include('../../includes/sesion.inc');
include('../../funciones.php');
login();

if($_SESSION['cartera'] != 'yes'){
  header("Location: ../index.php");
  exit();
  }
$id = $_SESSION['id'];
$perfil = $_SESSION['perfil'];
$id_ano = $_SESSION['ano'];



if ($perfil != '1'){
  header("Location: ../../error.php");
  exit();
}

$cv_db = base_datos();
$linkd = conectar_db($cv_db);

	$QueryEstudiante = mysql_query("select * from estudiante_".$id_ano." 
									WHERE cadel <> '1' 
									order by  apellido, nombre",$linkd);
	
?>    

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="cartera-style.css" type="text/css" rel="stylesheet" />
<title>Becas</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
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
<script>
  (function( $ ) {
    $.widget( "custom.combobox", {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
 
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
      },
 
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";
 
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( value )
          .attr( "title", "" )
          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          })
          .tooltip({
            tooltipClass: "ui-state-highlight"
          });
 
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
			 
			 //Al hacer una seleccion ejecuta el evento ajax 
			 // que lee los datos desde el server
			 
			
			 
			 $.ajaxSetup ({
						cache: false
					});
					
					var ajax_load = "<img class='loading' src='../../../imagenes_cv/cargando.gif' alt='loading...' />";
					
					//	load() functions
					var loadUrl = "cartera-becas-datos.php";
				
				   
					
						$("#resultado")
							.html(ajax_load)
							//el segundo parametro especificamos la variables y sus respectivos valores
							.load(loadUrl, {estudiante: $('#estudiante').val() });// fin de la funcion ajax
										
							this._trigger( "select", event, {
              item: ui.item.option
            });
          },
 
          autocompletechange: "_removeIfInvalid"
        });
      },
 
      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;
 
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Ver todos los Estudiantes" )
          .tooltip()
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();
 
            // Close if already visible
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
          return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });
 
        // Found a match, nothing to do
        if ( valid ) {
          
		  return;
        }
 
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title"," No hemos encontrado al Estudiante" + "\"" + value + "\"")
          .tooltip( "open" );
        this.element.val( "" );
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
        }, 2500 );
        this.input.data( "ui-autocomplete" ).term = "";
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
  })( jQuery );
 
  $(function() {
    $( "#estudiante" ).combobox();
    $( "#toggle" ).click(function() {
      $( "#estudiante" ).toggle();
    });
  });

  </script>
</head>

<body>
	<header class="bolivia">
    	Liceo psicopedagogico bolivia - Cartera
    </header>
    <article style="background:#FFF">
    <form action="" method="POST">
    	<a href="cartera.php"><h1 class="left"> <p style="margin: 5px;">Asignar becas . . . </p></h1></a>
        <br />
        <br />
            <section>
            	
                <div class="ui-widget">
                    <label for="estudiante"> Estudiante </label>
                    <Select name="estudiante" id="estudiante">
                    <option value="" selected="selected"></option>
                        <?php while($row = mysql_fetch_array($QueryEstudiante)){ 
                                    $QueryEstado = mysql_query("select estado from usuario where id = '".$row['id']."'",$linkd);
                                    $RunEstado = mysql_fetch_assoc($QueryEstado);
                                    if ( $RunEstado['estado'] != 'Retirado' && $row['apellido'] != "" ){
                        ?>
                        <option value="<?php echo $row['id']?>"><?php echo $row['apellido']." ".$row['nombre']; ?> </option>
                        <?php 
                            }
                        } 
                        ?>
                    </Select>
                </div>
            </section> 
            <br /><br />
            <div id="resultado" class="resultado"></div>
            <div class="ajustar"></div>
           </form>
           <br />
           <div class="ajustar"></div> 
    </article>
    <footer>
    	 comunidades virtuales online 
    </footer>
</body>
</html>