<?php

namespace App\Models;

use CodeIgniter\Model;

class Mgrilla extends Model
{
	public static function getCamposTabla($tabla)
	{
		$db = \Config\Database::connect();
		$sql = "SHOW COLUMNS FROM $tabla";
		$query = $db->query($sql);
		return $query->getResult();
	}
	public static function getTotal($tablaPrincipal, $tablasSecundarias, $where, $whereB)
	{
		$db = \Config\Database::connect();
		$builder = $db->table($tablaPrincipal);
		$builder->select("count(*) as total");
		/*if ($tablasSecundarias != false) {
			$builder->from($tablasSecundarias);
		}
		if (is_array($relaciones)) {
			foreach ($relaciones as $reg) {
				$builder->where($reg);
			}
		}*/
		if (is_array($tablasSecundarias)) {
			foreach ($tablasSecundarias as $reg) {
				//$builder->where($reg);
				$builder->join($reg["tabla"], $reg["relacion"], $reg["join"]);
			}
		}
		$builder->where($where);
		$builder->where($whereB);
		$query = $builder->get();
		return $query->getResult();
	}
	public static function getData($campos, $tablaPrincipal, $tablasSecundarias, $where, $whereB, $orden, $ini, $m)
	{
		/*echo "<pre>";
		print_r($campos);
		print_r($tablaPrincipal);
		print_r($tablasSecundarias);
		print_r($where);
		print_r($whereB);
		print_r($relaciones);
		print_r($orden);
		print_r($ini);
		print_r($m);
		echo "</pre>";*/
		$db = \Config\Database::connect();
		$builder = $db->table($tablaPrincipal);
		$builder->select($campos);
		/*if ($tablasSecundarias != false) {
			$builder->from($tablasSecundarias);
		}
		if (is_array($relaciones)) {
			foreach ($relaciones as $reg) {
				$builder->where($reg);
			}
		}*/
		if (is_array($tablasSecundarias)) {
			foreach ($tablasSecundarias as $reg) {
				//$builder->where($reg);
				$builder->join($reg["tabla"], $reg["relacion"], $reg["join"]);
			}
		}
		$builder->where($where);
		$builder->where($whereB);
		$builder->orderBy($orden);
		$builder->limit($m, $ini);
		$query = $builder->get();
		return $query->getResult();
	}
}
