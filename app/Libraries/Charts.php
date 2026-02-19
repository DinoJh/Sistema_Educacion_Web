<?php

namespace App\Libraries;

class Charts
{
    public function __construct() {}
    public static function barras($data, $label)
    {
        $data = array(
            "data" => $data,
            "label" => $label,
        );
        return view("charts/barras", $data);
    }
    public static function pie($ejeX, $ejeY)
    {
        $data = array(
            "ejex" => $ejeX,
            "ejey" => $ejeY
        );
        return view("charts/pie", $data);
    }
}
