<script>
			

</script>
<!--
	ESTE CODIGO NO SIRVE - NO SE ESTAN USANDO VISTAS, ELIMINAR MAS ADELANTE
-->

<?php
	use App\Libraries\Componente;
	$row = new Componente();
?>

<?php
	
	$head=array(
		array(
			"name"=>"ID",
			"campo"=>"usua_ide",
			"formato"=>"true",
			"hidden"=>"false"
		),
		array(
			"name"=>"Ap.Paterno",
			"campo"=>"usua_paterno",
			"formato"=>"true",
			"hidden"=>"false"
		),
		array(
			"name"=>"Ap.Materno",
			"campo"=>"usua_materno",
			"formato"=>"true",
			"hidden"=>"false"
		),
		array(
			"name"=>"Nombres",
			"campo"=>"usua_nombres",
			"formato"=>"true",
			"hidden"=>"false"
		),
		array(
			"name"=>"Usuario",
			"campo"=>"usua_user",
			"formato"=>"true",
			"hidden"=>"false"
		),
		array(
			"name"=>"Password",
			"campo"=>"usua_pass",
			"formato"=>"true",
			"hidden"=>"false"
		),
	);
	$botonAsignaRol="
	{
		name: 'Operaciones',
			formatter: (cell, row) => {
		    return gridjs.h('button', {
				className: 'btn btn-sm btn-primary',
			    onClick: function(){
			    	openCargar();
			    	param={
			    		ide:row.cells[0].data
			    	};
			    	$.post('".base_url("/getroles")."',param,function(data){
			    		$('#accessoGetRoles').html(data);
			    		$('#modalAsignaRoles').modal('show');
			    		closeCargar();
			    	});
				}
		  	}, 'Asignar Roles');
		}
	},
	";
	echo Componente::Tabla("tabla_usuarios",$head,$data,$l=5,$s="true",$p="true","primary",array($botonAsignaRol));
?>

<?php 
	$boton = Componente::Boton($id="guardar",$tipo="button",$clase="btn btn-sm btn-primary",$icono="ti-save",$txt="Guardar");
	/*$input = Componente::Input($id="input",$tipo="text",$clase="form-control form-control-xs",$val="123",$ph="valor");

	$row->agregar(Componente::Col("col-sm-6 form-floating",$input));
	$row->agregar(Componente::Col("col-sm-6",$boton));

	$body=$row->get($etiqueta="div",$clase="row",$propiedades="");*/
	$body="<div id='accessoGetRoles'></div>";

	echo Componente::Modal($id="modalAsignaRoles",$titulo="ASIGNAR ROLES",$body,$botonok=$boton,$size="modal-xl");
?>