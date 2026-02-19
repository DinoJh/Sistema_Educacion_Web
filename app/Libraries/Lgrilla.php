<?php

namespace App\Libraries;

use App\Models\Mgrilla;

class Lgrilla
{
    private $tablaPrincipal;
    private $tablasSecundarias;
    private $campos;
    private $where;
    private $orden;
    private $data;
    private $listaMostrar;
    private $tituloTabla;
    private $widthTabla;
    private $columnas;
    private $url;
    private $componentes;
    public function __construct()
    {
        $this->tablaPrincipal = "";
        $this->tablasSecundarias = array();
        $this->campos = array();
        $this->where = array();
        $this->orden = "";
        $this->data = array();
        $this->listaMostrar = array(10, 20, 50, 100);
        $this->columnas = array();
        $this->widthTabla = "100%";
        $this->tituloTabla = "";
        $this->componentes = array();
    }
    public function setTablaPrincipal($tabla)
    {
        $this->tablaPrincipal = $tabla;
    }
    public function setTablaSecundaria($tabla, $relacion, $join)
    {
        $this->tablasSecundarias[] = array(
            "tabla" => $tabla,
            "relacion" => $relacion,
            "join" => $join
        );
    }
    public function getCamposTablaPrincipal()
    {
        $tmpCampos = Mgrilla::getCamposTabla($this->tablaPrincipal);
        foreach ($tmpCampos as $reg) {
            $type = $this->evaluar($reg->Type);
            $this->campos[$reg->Field] = array(
                "Key" => $reg->Key,
                "Field" => $reg->Field,
                "Type" => $reg->Type,
                "Type1" => $type,
                "Visible" => false,
                "Edit" => true,
                "Label" => "",
                "Width" => "",
                "Format" => "no"
            );
        }
    }
    public function getCamposTablasSecundarias()
    {
        foreach ($this->tablasSecundarias as $reg) {
            $tmpCampos = Mgrilla::getCamposTabla($reg["tabla"]);
            foreach ($tmpCampos as $reg) {
                $type = $this->evaluar($reg->Type);
                $this->campos[$reg->Field] = array(
                    "Key" => $reg->Key,
                    "Field" => $reg->Field,
                    "Type" => $reg->Type,
                    "Type1" => $type,
                    "Visible" => false,
                    "Edit" => true,
                    "Label" => "",
                    "Width" => "",
                    "Format" => "no"
                );
            }
        }
    }
    public function getCampos()
    {
        $this->getCamposTablaPrincipal();
        $this->getCamposTablasSecundarias();
        /*echo "<pre>";
        print_r($this->campos);
        echo "</pre>";*/
    }
    public function evaluar($entrada)
    {
        $entrada = explode("(", $entrada);
        $tipo = $entrada[0];
        $param = array();
        if (count($entrada) > 1) {
            $param = explode(")", $entrada[1]);
            $param = explode(",", $param[0]);
            foreach ($param as &$reg) {
                $reg = str_replace("'", "", $reg);
            }
        }
        $result = array(
            "tipo" => $tipo,
            "param" => $param,
        );
        return $result;
    }
    public function setWhere($where)
    {
        $this->where = $where;
    }
    public function setOrden($orden)
    {
        $this->orden = $orden;
    }
    public function setListaMostrar($lista)
    {
        $this->listaMostrar = $lista;
    }
    public function setTituloTabla($titulo)
    {
        $this->tituloTabla = $titulo;
    }
    public function setWidthTabla($width)
    {
        $this->widthTabla = $width;
    }
    public function setURL($url)
    {
        $this->url = base_url($url);
    }
    public function addComponente($componente)
    {
        $this->componentes[] = $componente;
    }
    public function setColumna($campo, $label, $width, $edit, $format = "no")
    {
        $this->campos[$campo]["Label"] = $label;
        $this->campos[$campo]["Visible"] = true;
        $this->campos[$campo]["Edit"] = $edit;
        $this->campos[$campo]["Width"] = $width;
        $this->campos[$campo]["Format"] = $format;
        $this->columnas[] = $campo;
    }
    public function getData($m, $p, $b)
    {
        /*$m=$this->request->getGet('m');
        $p=$this->request->getGet('p');
        $b=$this->request->getGet('b');*/
        $m = intval($m);
        $p = intval($p);
        $campos = array();
        foreach ($this->campos as $reg) {
            if ($reg["Visible"] == true) {
                $campos[] = $reg["Field"];
            }
        }
        $camposV = implode(",", $campos);
        $whereB = array();
        foreach ($campos as $reg) {
            $whereB[] = "$reg like '%$b%'";
        }
        $whereB = implode(" OR ", $whereB);
        $whereB = "(" . $whereB . ")";

        $relaciones = false;
        $secundarias = false;
        if (count($this->tablasSecundarias) > 0) {
            $relaciones = array();
            $secundarias = array();
            $joins = array();
            foreach ($this->tablasSecundarias as $reg) {
                $relaciones[] = $reg["relacion"];
                $secundarias[] = $reg["tabla"];
                $joins[] = $reg["join"];
            }
            $secundarias = implode(",", $secundarias);
        }
        $total = Mgrilla::getTotal(
            $this->tablaPrincipal,
            $this->tablasSecundarias,
            $this->where,
            $whereB
        );
        $total = $total[0]->total;
        if ($p == 0) {
            $p = 1;
        }
        $pgs = intval($total / $m);
        if ($total % $m > 0) {
            $pgs++;
        }
        if ($p > $pgs) {
            $p = 1;
        }
        $ini = $m * ($p - 1);
        $fin = $m * $p;

        $this->data = Mgrilla::getData(
            $camposV,
            $this->tablaPrincipal,
            $this->tablasSecundarias,
            $this->where,
            $whereB,
            $this->orden,
            $ini,
            $m
        );
        $campos = array();
        foreach ($this->columnas as $reg) {
            $campos[$reg] = $this->campos[$reg];
        }

        $result = array(
            "data" => $this->data,
            "m" => $m,
            "total" => $total,
            "ini" => $ini,
            "fin" => $fin,
            "pag" => $p,
            "pgs" => $pgs,
            "listaMostrar" => $this->listaMostrar,
            "campos" => $campos,
            "widthTabla" => $this->widthTabla,
            "tituloTabla" => $this->tituloTabla,
            "url" => $this->url,
            "componentes" => $this->componentes
        );
        return $result;
    }
    public function mostrarGrilla($ide)
    {
        $m = $this->listaMostrar[0];
        $p = 1;
        $b = "";
        $data = $this->getData($m, $p, $b);
        $data["grilla_ide"] = $ide;
        echo view("grilla/vgrilla", $data);

        /*echo "<pre>";
        print_r($data);
        echo "</pre>";*/
    }
    public function mostrarDataGrilla($m, $p, $b, $ide)
    {
        $data = $this->getData($m, $p, $b);
        $data["grilla_ide"] = $ide;
        echo view("grilla/vdatagrilla", $data);

        /*echo "<pre>";
        print_r($data);
        echo "</pre>";*/
    }
}
