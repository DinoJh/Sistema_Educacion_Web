<?php
namespace App\Libraries;

class Funciones{
    public function __construct(){
    
    }
    public static function get_mes($mes){
        $meces=array(
            1=>"ENERO",
            2=>"FEBRERO",
            3=>"MARZO",
            4=>"ABRIL",
            5=>"MAYO",
            6=>"JUNIO",
            7=>"JULIO",
            8=>"AGOSTO",
            9=>"SETIEMBRE",
            10=>"OCTUBRE",
            11=>"NOVIEMBRE",
            12=>"DICIEMBRE",
            13=>"ERROR"
        );
        if($mes<=0 or $mes>=13)
            $mes=13;
        return $meces[$mes];
    }
    public static function get_fecha_letras($fecha){
        $tmp=explode("-",$fecha);
        return $tmp[2]." de ".strtolower(Funciones::get_mes((int)$tmp[1]))." de ".$tmp[0];
    }
    public function get_fecha_formato($fecha,$formato="COMUN"){
        $tmp=explode("-",$fecha);
        if($formato=="COMUN")
        return $tmp[2]."/".$tmp[1]."/".$tmp[0];
    }
    public function get_dias_del_mes($anio,$mes){
        $dia=0;
        if($anio%4==0){
            $dia=1;
        }
        $dias_mes=array(
            "1"=>31,
            "2"=>28+$dia,
            "3"=>31,
            "4"=>30,
            "5"=>31,
            "6"=>30,
            "7"=>31,
            "8"=>31,
            "9"=>30,
            "10"=>31,
            "11"=>30,
            "12"=>31,
        );
        return $anio."-".$mes."-".$dias_mes[$mes];
    }
    public function get_moneda($cantidad){
        return number_format($cantidad, 2, ".", "");
    }
    public static function get_ahora(){
        date_default_timezone_set('America/Lima');
        return date('Y-m-d H:i:s');
    }
    public static function get_ahora_fecha(){
        date_default_timezone_set('America/Lima');
        return date('Y-m-d');
    }
    public static function get_ahora_hora(){
        date_default_timezone_set('America/Lima');
        return date('H:i:s');
    }

    public static function enviarEmail($asunto,$email,$pagina){

        $to = $email;
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Torneos Peru4k <torneos@peru4k.com.pe>' . "\r\n";
        return mail($to,$asunto,$pagina,$headers);
        
    }

    public static function encodeX($num){
        return $num*751+9562;
    }

    public static function decodeX($num){
        return (intval($num)-9562)/751;
    }
}