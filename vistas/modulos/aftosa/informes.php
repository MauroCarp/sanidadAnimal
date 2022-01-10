<?php

if($_SESSION["perfil"] == "Especial"){

  echo '<script>

    window.location = "inicio";

  </script>';

  return;

}

?>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Informes

    </h1>

    <ol class="breadcrumb">
      
      <li><i class="fa fa-dashboard"></i> Aftosa</li>
      <li><a href="index.php?ruta=aftosa/informes">Informes</a></li>
      
    </ol>

  </section>

  <section class="content">

    <div class="box">

        <div class="box-body">

            <div class="box-body table-responsive no-padding">

            <div class="list-group" style="font-size:1.5em;">

            <?php
            
              foreach ($informes as $key => $value) {
                
                if ($value != 'Detalle Animales Vacunados por Vacunador' AND $value != 'Cronograma por Veterinario' AND $value != 'Cronograma Actual por Veterinario'){
                 
                  echo  "<a href='extensiones/fpdf/informesPdf.php?informe=informe".($key+1).".php' class='list-group-item list-group-item-action' target='_blank'>".($key+1)."- ".$value."</a>";

                  
                }else{

                  echo "<a href='#' class='list-group-item list-group-item-action' data-toggle='modal' data-target='.matricula-";

                  $tipo = ($value == 'Cronograma por Veterinario' OR $value == 'Cronograma Actual por Veterinario') ? 'cronograma' : 'informe';

                  echo "$tipo".($key+1)."'>".($key+1)."-  ".$value."</a>";

                }

              }
                
                ?>
                
            </div>



            </div>

        </div>

    </div>

  </section>

</div>