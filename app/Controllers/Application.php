<?php

namespace App\Controllers;

use App\Models\General;
use App\Libraries\Componente;

class Application extends BaseController
{
	public $session;
	public function __construct()
	{
		$this->session = \Config\Services::session();
		//parent::__construct();
		if ($this->session->login != md5("L0g¡NS!st3M4")) {
			echo "inactivo";
			exit(0);
			return;
		}
	}
	/************************************************************************
	public function index()
    {
    	$roles = General::getRoles($this->session->usua_ide);
        $data_header=array(
            "system_name"=>"NOMBRE DEL SISTEMA",
            "base"=>base_url("public"),
        );
        $data_menu=array(
        	"system_name"=>"NOMBRE DEL SISTEMA",
        	"base"=>base_url("public"),
        	"logo"=>"dota2.png",
        	"roles"=>$roles,
        	"session"=>$this->session,
        	"contacto_datos"=>"Ing. Víctor Hugo BEJAR GONZALES",
            "contacto_celular"=>"958273933",
            "contacto_email"=>"victor.bejar.g@gmail.com",
        );
        $data_index=array();
        $data_footer=array();
        //$vistas = view('sistema/vheader',$data_header).
        //		  view('sistema/vfooter',$data_footer).
        //		  view('sistema/vmenu',$data_menu).
        //		  view('sistema/vindex',$data_index);
      	echo view('sistema/vheader',$data_header);
        echo view('sistema/vfooter',$data_footer);
        echo view('sistema/vmenu',$data_menu);
        echo view('sistema/vindex',$data_index);

        //return $vistas;
    }
	 ************************************************************************/
	public function index()
	{
		#echo $this->session->perf_ide ;
		#print_r($this->session);
		$roles = General::getRoles($this->session->perf_ide);
		$roles2 = array();
		$modulos = array();
		foreach ($roles as $reg) {
			$modulos[$reg->modu_ide] = $reg->modu_nombre;
			$roles2[$reg->modu_ide][] = array(
				"icono" => $reg->modu_icono,
				"modulo" => $reg->modu_nombre,
				"rol" => $reg->role_nombre,
				"url" => $reg->role_url,
				"ide" => $reg->role_ide,
				"nombre" => $reg->role_nombre,
				"descripcion" => $reg->role_descripcion,
				"clase" => $reg->modu_clase,
			);
		}
		$data = array(
			"system_name" => "DRE PUNO",
			"session" => $this->session,
			"logo" => "drepuno.png",
			"roles2" => $roles2,
			"contacto_datos" => "Ing. Víctor Hugo BEJAR GONZALES",
			"contacto_celular" => "958273933",
			"contacto_email" => "victor.bejar.g@gmail.com",
			"base" => base_url("public")
		);
		$vistas = view('sistema/vheader', $data) .
			view('sistema/vindex') .
			view('sistema/vfooter') .
			view('sistema/vmenu');
		return $vistas;
	}
	public function accesos()
	{
		$head = array(
			array(
				"name" => "ID",
				"campo" => "usua_ide",
				"formato" => "true",
				"hidden" => "false",
				"width" => "5%",
			),
			array(
				"name" => "Ap.Paterno",
				"campo" => "usua_paterno",
				"formato" => "true",
				"hidden" => "false",
				"width" => "15%",
			),
			array(
				"name" => "Ap.Materno",
				"campo" => "usua_materno",
				"formato" => "true",
				"hidden" => "false",
				"width" => "15%",
			),
			array(
				"name" => "Nombres",
				"campo" => "usua_nombres",
				"formato" => "true",
				"hidden" => "false",
				"width" => "15%",
			),
			array(
				"name" => "Usuario",
				"campo" => "usua_user",
				"formato" => "true",
				"hidden" => "false",
				"width" => "15%",
			),
			array(
				"name" => "Password",
				"campo" => "usua_pass",
				"formato" => "true",
				"hidden" => "false",
				"width" => "15%",
			),
		);
		$botonAsignaRol = "
		{
			name: 'Operaciones',
			width: '20%',
			formatter: (cell, row) => {
			    return gridjs.h('button', {
					className: 'btn btn-sm btn-primary',
				    onClick: function(){
				    	openCargar();
				    	param={
				    		ide:row.cells[0].data
				    	};
				    	$.post('" . base_url("/getroles") . "',param,function(data){
				    		data=JSON.parse(data);
				    		$('#modalAsignaRoles').modal('show');
				    		$('#accesoGetRoles').html(data.tabla);
				    		$('#accesoIdUsuario').val(data.usuaIde);
				    		//$('#modalAsignaRoles').modal('show');
				    		closeCargar();
				    	});
					}
			  	}, 
			  	'Asignar Roles');
			}
		},
		";
		$data = General::getData(
			$campos = "*",
			$tabla = "usuarios",
			$where = array("usua_esta_ide" => 1),
			$order = "usua_ide"
		);

		echo Componente::Tabla(
			$id = "tabla_usuarios",
			$head,
			$data,
			$l = 5,
			$s = "true",
			$p = "true",
			$clase = "primary",
			array($botonAsignaRol),
			$js = ""
		);

		$body = "<input type='hidden' id='accesoIdUsuario'><div id='accesoGetRoles'></div>";
		echo Componente::Modal($id = "modalAsignaRoles", $titulo = "ASIGNAR ROLES", $body, $botonok = "", $size = "modal-xl");
	}

	public function getroles()
	{
		$head = array(
			array(
				"name" => "ID",
				"campo" => "role_ide",
				"formato" => "true",
				"hidden" => "false",
				"width" => "5%",
			),
			array(
				"name" => "Modulo",
				"campo" => "modu_nombre",
				"formato" => "true",
				"hidden" => "false",
				"width" => "15%",
			),
			array(
				"name" => "Rol",
				"campo" => "role_nombre",
				"formato" => "true",
				"hidden" => "false",
				"width" => "15%",
			),
			array(
				"name" => "Descripción",
				"campo" => "role_descripcion",
				"formato" => "true",
				"hidden" => "false",
				"width" => "40%",
			),
			array(
				"name" => "Estado",
				"campo" => "estado",
				"formato" => "
					function(cell){
						return gridjs.html('<span class=\"badge bg-success\">'+cell+'</span>');
					}
				",
				"hidden" => "false",
				"width" => "10%",
			)
		);
		$data = General::getRolesAsignados($this->request->getPost('ide'));
		$botonAsignaRol = "
		{
			name: 'Operaciones',
			width: '15%',
			formatter: (cell, row) => {
			    return gridjs.h('button', {
					className: 'btn btn-sm btn-primary',
				    onClick: function(){
				    	openCargar();
				    	//$('#modalAsignaRoles').modal('hide');
				    	param={
				    		usua_ide:$('#accesoIdUsuario').val(),
				    		role_ide:row.cells[0].data	
				    	};
				    	$.post('" . base_url("/asignarol") . "',param,function(data){
				    		param2={
					    		ide:$('#accesoIdUsuario').val(),
					    	};
					    	$.post('" . base_url("/getroles") . "',param2,function(data){
					    		data=JSON.parse(data);
					    		$('#accesoGetRoles').html(data.tabla);
					    		//$('#accesoIdUsuario').val(data.usuaIde);
					    		//$('#modalAsignaRoles').modal('show');
					    		closeCargar();
					    	});
				    	});
					}
			  	}, 
				'Asignar/Quitar');
			}
		},
		";
		$result = array(
			"tabla" => Componente::Tabla(
				$id = "tabla_roles_asignados",
				$head,
				$data,
				$l = 10,
				$s = "false",
				$p = "true",
				$clase = "primary",
				$botones = array($botonAsignaRol),
				$js = ""
			),
			"usuaIde" => $this->request->getPost('ide')
		);
		echo json_encode($result);
	}
	public function asignarol()
	{
		$usua_ide = $this->request->getPost('usua_ide');
		$role_ide = $this->request->getPost('role_ide');
		$acceso = General::getData(
			$campos = "*",
			$tabla = "accesos a",
			$where = array(
				"a.acce_usua_ide" => $usua_ide,
				"a.acce_role_ide" => $role_ide,
				"a.acce_esta_ide" => 1,
			),
			$order = ""
		);
		if (count($acceso) == 1) {
			$data = array(
				"a.acce_esta_ide" => 2,
			);
			General::actualizar("accesos a", $where, $data);
		} else {
			$data = array(
				"acce_usua_ide" => $usua_ide,
				"acce_role_ide" => $role_ide,
				"acce_esta_ide" => 1,
			);
			General::insertar("accesos", $data);
		}
	}
	public function setpass()
	{
		$ante = $this->request->getPost('anterior');
		$nueva = $this->request->getPost('nueva');
		$repi = $this->request->getPost('repite');
		$usua_ide = $this->session->usua_ide;
		$usua_user = $this->session->usuario;

		if ($ante == "" or $nueva == "" or $repi == "") {
			$clase = "alert alert-danger";
			$icono = "ti-close";
			$mensaje = "Complete todos los campos antes de continuar";
		} else if ($nueva == $repi) {
			$where = array(
				"usua_ide" => $usua_ide,
				"usua_user" => $usua_user,
				"usua_pass" => $ante
			);
			$data = array(
				"usua_pass" => $nueva
			);
			$r = General::actualizar("usuarios", $where, $data);
			if ($r == 0) {
				$clase = "alert alert-warning";
				$icono = "ti-alert";
				$mensaje = "No se pudo cambiar la clave, intentelo nuevamente";
			} else {
				$clase = "alert alert-success";
				$icono = "ti-check";
				$mensaje = "Se realizo el cambio de clave exitosamente";
			}
		} else {
			$clase = "alert alert-danger";
			$icono = "ti-alert";
			$mensaje = "La nueva clave y la clave que se repite tienen que ser las mismas, verifique para continuar";
		}
		$result = array(
			"clase" => $clase,
			"mensaje" => $mensaje,
			"icono" => $icono
		);
		echo json_encode($result);
	}
	public function salir()
	{
		$this->session->destroy();
		return redirect()->to(base_url('/login'));
	}
	public function testing()
	{
	}

	public function nuevo()
	{
		echo 'asdasd';
	}
}
