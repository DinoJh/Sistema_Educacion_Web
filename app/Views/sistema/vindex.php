<?php
use App\Libraries\Componente;

?>


<?php foreach ($roles2 as $reg): ?>
	<div class="row mb-3">
	<?php foreach ($reg as $r): ?>
		<div class="col-sm-4 mb-1">
			<?php
				echo Componente::Rol(
					$modulo=$r["modulo"],
					$nombre=$r["nombre"],
					$descripcion=$r["descripcion"],
					$clase=$r["clase"],
					$icono=$r["icono"],
					$url=$r["url"]
				);
			?>
		</div>
	<?php endforeach ?>
	</div>
<?php endforeach ?>