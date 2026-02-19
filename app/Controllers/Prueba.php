<?php

namespace App\Controllers;


class Prueba extends BaseController
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
	public function getnombres()
    {
        echo "Dino Jhoel";
    }
    public function getapellidos()
    {
        echo "Condori Churata";
    }
    public function getdni()
    {
        echo "75921149";
    }
}
