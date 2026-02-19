<?php

namespace App\Controllers;

use App\Models\Usuarios;
use App\Models\Generaldb;
use App\Libraries\Funciones;


class Login extends BaseController
{
    public function index()
    {
        $data = array(
            "system_name" => "MONITOREO REGIONAL",
            "error" => $this->request->getVar('error'),
            "fondo" => "fondo-dre-puno2.jpg",
            "logo" => "logo-drepuno.gif",
            "sigla" => ""
        );
        return view('login/vlogin', $data);
    }
    public function verificar()
    {
        $where = array(
            "usua_user" => $this->request->getPost('user'),
            "usua_pass" => $this->request->getPost('pass'),
            "usua_esta_ide" => 1,
        );
        $objeto = new Usuarios();
        //$usuarios = $objeto->where($where)->find();
        $usuarios = $objeto->where($where)->get()->getResult();
        if (count($usuarios) == 1) {
            #print_r($usuarios) ; 
            #return;

            $fecha = Funciones::get_ahora_fecha();
            $data_session = array(
                "login" => md5("L0g¡NS!st3M4"),
                "perf_ide" => $usuarios[0]->usua_perf_ide,
                "usua_ide" => $usuarios[0]->usua_ide,
                //"iiee_ide" => $usuarios[0]->usua_iiee_ide,
                //"dres_ide" => $usuarios[0]->usua_dres_ide,
                //"ugel_ide" => $usuarios[0]->usua_ugel_ide,
                //"enti_ide"=>$usuarios[0]->usua_enti_ide,
                //"unor_ide" => $usuarios[0]->usua_unor_ide,
                "datos" => $usuarios[0]->usua_nombres . ", " . $usuarios[0]->usua_paterno . " " . $usuarios[0]->usua_materno,
                "usuario" => $usuarios[0]->usua_user,
                "siglas" => "MONITOREO REGIONAL",
                "icono" => "ti-bar-chart-alt",
                "ini_fecha" => Funciones::get_fecha_letras($fecha),
                "ini_hora" => Funciones::get_ahora_hora()
            );

            $login = array(
                "logi_usua_ide" => $usuarios[0]->usua_ide,
                "logi_user" => $usuarios[0]->usua_user,
                "logi_pass" => $usuarios[0]->usua_pass,
                "logi_accedio" => "SI",
                "logi_datos" => $data_session["datos"],
                "logi_create_at" => Funciones::get_ahora(),
            );
            $general = new Generaldb();
            $general->insertData("logins", $login);

            $session = \Config\Services::session();
            $session->set($data_session);
            return redirect()->to(base_url('/application'));
        } else {
            $login = array(
                "logi_usua_ide" => NULL,
                "logi_user" => $this->request->getPost('user'),
                "logi_pass" => $this->request->getPost('pass'),
                "logi_accedio" => "NO",
                "logi_datos" => "",
                "logi_create_at" => Funciones::get_ahora(),
            );
            $general = new Generaldb();
            $general->insertData("logins", $login);

            return redirect()->to(base_url('/login?error=true'));
        }
        //return 0;
    }
}
