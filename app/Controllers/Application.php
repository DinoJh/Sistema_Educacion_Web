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
        if ($this->session->login != md5("L0g¡NS!st3M4")) {
            echo "inactivo";
            exit(0);
        }
    }

public function index()
    {
        $roles  = General::getRoles($this->session->perf_ide);
        $roles2 = [];
        $modulos = [];

        // Siempre agregar Inicio como primer item especial
        $inicio = ['icono'=>'ti-home','modulo'=>'Inicio','rol'=>'Inicio','url'=>'/application','ide'=>0,'nombre'=>'Inicio','descripcion'=>'Pantalla principal','clase'=>'primary'];

        foreach ($roles as $reg) {
            // ── ADMIN: sin Mi Panel, sin Crear Curso, sin rutas de asesor ──
            if ($this->session->perf_ide == 3) {
                if (in_array($reg->role_url, [
                    '/cursos/crear','/mi-panel/cursos','/mi-panel/progreso','/application',
                    '/asesor/cursos','/asesor/grupos'
                ])) continue;
            }
            // ── PROFESOR: sin Mi Panel ──
            if ($this->session->perf_ide == 2) {
                if (in_array($reg->role_url, ['/mi-panel/cursos','/mi-panel/progreso'])) continue;
            }
            // ── ALUMNO: sin gestión ──
            if ($this->session->perf_ide == 1) {
                if (in_array($reg->role_url, [
                    '/cursos/crear','/lecciones','/usuarios/alumnos','/usuarios/profesores',
                    '/reportes/progreso','/categorias','/usuarios/asesores',
                    '/asesor/cursos','/asesor/grupos','/asesor/admin-grupos'
                ])) continue;
            }
            // ── ASESOR: solo sus rutas propias ──
            if ($this->session->perf_ide == 4) {
                if (!in_array($reg->role_url, ['/asesor/cursos','/asesor/grupos'])) continue;
            }

            if ($reg->role_url == '/application') continue; // Inicio se maneja aparte

            $modulos[$reg->modu_ide] = $reg->modu_nombre;
            $roles2[$reg->modu_ide][] = [
                'icono'       => $reg->modu_icono,
                'modulo'      => $reg->modu_nombre,
                'rol'         => $reg->role_nombre,
                'url'         => $reg->role_url,
                'ide'         => $reg->role_ide,
                'nombre'      => $reg->role_nombre,
                'descripcion' => $reg->role_descripcion,
                'clase'       => $reg->modu_clase,
            ];
        }

        // Agregar "Mi Perfil" como módulo especial visible para todos
        $modulos[99] = 'Mi Cuenta';
        $roles2[99][] = [
            'icono'       => 'ti-user',
            'modulo'      => 'Mi Cuenta',
            'rol'         => 'Mi Perfil',
            'url'         => '/mi-perfil',
            'ide'         => 99,
            'nombre'      => 'Mi Perfil',
            'descripcion' => 'Ver y editar tus datos de perfil',
            'clase'       => 'info',
        ];

        $data = [
            'system_name'     => 'CodePuno',
            'session'         => $this->session,
            'logo'            => 'logo-codepuno.svg',
            'roles2'          => $roles2,
            'contacto_datos'  => 'CodePuno',
            'contacto_celular'=> '',
            'contacto_email'  => 'contacto@codepuno.edu.pe',
            'base'            => base_url('public'),
        ];

        return view('sistema/vheader', $data)
             . view('sistema/vindex',  ['session' => $this->session])
             . view('sistema/vfooter')
             . view('sistema/vmenu',   $data);
    }

    public function accesos()
    {
        $head = [
            ['name'=>'ID',          'campo'=>'usua_ide',    'formato'=>'true','hidden'=>'false','width'=>'5%'],
            ['name'=>'Ap.Paterno',  'campo'=>'usua_paterno','formato'=>'true','hidden'=>'false','width'=>'15%'],
            ['name'=>'Ap.Materno',  'campo'=>'usua_materno','formato'=>'true','hidden'=>'false','width'=>'15%'],
            ['name'=>'Nombres',     'campo'=>'usua_nombres','formato'=>'true','hidden'=>'false','width'=>'15%'],
            ['name'=>'Usuario',     'campo'=>'usua_user',   'formato'=>'true','hidden'=>'false','width'=>'15%'],
            ['name'=>'Perfil',      'campo'=>'perf_nombre', 'formato'=>'true','hidden'=>'false','width'=>'15%'],
        ];
        $botonAsignaRol = "
        {
            name: 'Operaciones', width: '20%',
            formatter: (cell, row) => {
                return gridjs.h('button', {
                    className: 'btn btn-sm btn-primary',
                    onClick: function(){
                        openCargar();
                        param={ide:row.cells[0].data};
                        $.post('".base_url('/getroles')."',param,function(data){
                            data=JSON.parse(data);
                            $('#modalAsignaRoles').modal('show');
                            $('#accesoGetRoles').html(data.tabla);
                            $('#accesoIdUsuario').val(data.usuaIde);
                            closeCargar();
                        });
                    }
                }, 'Asignar Roles');
            }
        },";

        $db   = \Config\Database::connect();
        $data = $db->table('usuarios u')
            ->select('u.usua_ide, u.usua_paterno, u.usua_materno, u.usua_nombres, u.usua_user, p.perf_nombre')
            ->join('perfiles p','p.perf_ide=u.usua_perf_ide','left')
            ->where('u.usua_esta_ide',1)->where('u.usua_deleted_at IS NULL')
            ->orderBy('u.usua_paterno')->get()->getResult();

        echo Componente::Tabla('tabla_usuarios',$head,$data,5,'true','true','primary',[$botonAsignaRol],'');
        $body = "<input type='hidden' id='accesoIdUsuario'><div id='accesoGetRoles'></div>";
        echo Componente::Modal('modalAsignaRoles','ASIGNAR ROLES',$body,'','modal-xl');
    }

    public function getroles()
    {
        $head = [
            ['name'=>'ID',         'campo'=>'role_ide',       'formato'=>'true','hidden'=>'false','width'=>'5%'],
            ['name'=>'Modulo',     'campo'=>'modu_nombre',     'formato'=>'true','hidden'=>'false','width'=>'15%'],
            ['name'=>'Rol',        'campo'=>'role_nombre',     'formato'=>'true','hidden'=>'false','width'=>'15%'],
            ['name'=>'Descripción','campo'=>'role_descripcion','formato'=>'true','hidden'=>'false','width'=>'40%'],
            ['name'=>'Estado',     'campo'=>'estado',          'formato'=>"function(cell){return gridjs.html('<span class=\"badge bg-success\">'+cell+'</span>');}",'hidden'=>'false','width'=>'10%'],
        ];
        $data = General::getRolesAsignados($this->request->getPost('ide'));
        $boton = "
        {
            name:'Operaciones',width:'15%',
            formatter:(cell,row)=>{
                return gridjs.h('button',{
                    className:'btn btn-sm btn-primary',
                    onClick:function(){
                        openCargar();
                        param={usua_ide:$('#accesoIdUsuario').val(),role_ide:row.cells[0].data};
                        $.post('".base_url('/asignarol')."',param,function(data){
                            param2={ide:$('#accesoIdUsuario').val()};
                            $.post('".base_url('/getroles')."',param2,function(data){
                                data=JSON.parse(data);
                                $('#accesoGetRoles').html(data.tabla);
                                closeCargar();
                            });
                        });
                    }
                },'Asignar/Quitar');
            }
        },";
        $result = [
            'tabla'   => Componente::Tabla('tabla_roles_asignados',$head,$data,10,'false','true','primary',[$boton],''),
            'usuaIde' => $this->request->getPost('ide'),
        ];
        echo json_encode($result);
    }

    public function asignarol()
    {
        $usua_ide = $this->request->getPost('usua_ide');
        $role_ide = $this->request->getPost('role_ide');
        $db = \Config\Database::connect();
        // Obtener perf_ide del usuario
        $user = $db->table('usuarios')->select('usua_perf_ide')->where('usua_ide',$usua_ide)->get()->getRow();
        $perf_ide = $user ? $user->usua_perf_ide : $usua_ide;

        $acceso = General::getData('*','accesos',['acce_perf_ide'=>$perf_ide,'acce_role_ide'=>$role_ide,'acce_esta_ide'=>1],'');
        if (count($acceso) >= 1) {
            General::actualizar('accesos',['acce_perf_ide'=>$perf_ide,'acce_role_ide'=>$role_ide],['acce_esta_ide'=>2]);
        } else {
            General::insertar('accesos',['acce_perf_ide'=>$perf_ide,'acce_role_ide'=>$role_ide,'acce_esta_ide'=>1]);
        }
    }

    public function setpass()
    {
        $ante     = $this->request->getPost('anterior');
        $nueva    = $this->request->getPost('nueva');
        $repi     = $this->request->getPost('repite');
        $usua_ide = $this->session->usua_ide;
        $usua_user= $this->session->usuario;

        if (!$ante || !$nueva || !$repi) {
            $r = ['clase'=>'alert alert-danger','icono'=>'ti-close','mensaje'=>'Complete todos los campos.'];
        } elseif ($nueva != $repi) {
            $r = ['clase'=>'alert alert-danger','icono'=>'ti-alert','mensaje'=>'La nueva clave y la repetición no coinciden.'];
        } else {
            $res = General::actualizar('usuarios',['usua_ide'=>$usua_ide,'usua_user'=>$usua_user,'usua_pass'=>$ante],['usua_pass'=>$nueva]);
            if ($res == 0) $r = ['clase'=>'alert alert-warning','icono'=>'ti-alert','mensaje'=>'Clave anterior incorrecta.'];
            else $r = ['clase'=>'alert alert-success','icono'=>'ti-check','mensaje'=>'Clave cambiada exitosamente.'];
        }
        echo json_encode($r);
    }

    public function salir()
    {
        $this->session->destroy();
        return redirect()->to(base_url('/login'));
    }

    public function testing()
    {
        // Keeps session alive
    }
}
