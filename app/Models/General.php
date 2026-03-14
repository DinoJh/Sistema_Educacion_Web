<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\Componente;

class General extends Model
{
	public static function getRoles($perf_ide)
	{
		$db = \Config\Database::connect();
		$builder = $db->table('accesos a');
		$builder->select("
			m.modu_ide,
			r.role_ide,
			m.modu_orden,
			r.role_orden,
			m.modu_icono,
			m.modu_nombre,
			r.role_nombre,
			r.role_url,
			r.role_descripcion,
			m.modu_clase
		");
		$builder->from("
			roles r,
			modulos m,
			perfiles p
		");

		$builder->where("a.acce_perf_ide", $perf_ide);
		$builder->where("a.acce_perf_ide = p.perf_ide");
		$builder->where("a.acce_role_ide = r.role_ide");
		$builder->where("r.role_modu_ide = m.modu_ide");
		$builder->where("r.role_esta_nombre = 'ACTIVO'");
		$builder->where("a.acce_esta_ide = 1");
		$builder->orderBy("m.modu_orden, r.role_orden");

		$query = $builder->get();
		return $query->getResult();
	}

	public static function getRolesAsignados($usua_ide)
	{
		$db = \Config\Database::connect();
		$builder = $db->table('accesos a');
		$builder->select("
			if(a.acce_ide is NULL,'','ASIGNADO') as estado,
			a.acce_ide,
			r.role_ide,
			a.acce_perf_ide,
			m.modu_nombre,
			r.role_nombre,
			r.role_descripcion
		");
		$builder->join(
			"roles r",
			"a.acce_role_ide = r.role_ide AND a.acce_perf_ide = $usua_ide AND a.acce_esta_ide = 1",
			"right"
		);
		$builder->join("modulos m", "r.role_modu_ide = m.modu_ide", "right");
		$builder->orderBy("m.modu_orden,r.role_orden");

		$query = $builder->get();
		return $query->getResult();
	}
	public static function getData($campos, $tablaPrincipal, $where, $order, $groupBy = false, $join = false)
	{
		$db = \Config\Database::connect();
		$builder = $db->table($tablaPrincipal);
		$builder->select($campos);
		$builder->where($where);
		if ($groupBy != false) {
			$builder->groupBy($groupBy);
		}
		$builder->orderBy($order);
		if ($join != false) {
			$query = $builder->join($join[0], $join[1], $join[2]);
		}

		$query = $builder->get();
		return $query->getResult();
	}

	public static function getData2($campos, $tablaPrincipal, $tablasSecundarias, $where, $order)
	{
		$db = \Config\Database::connect();
		$builder = $db->table($tablaPrincipal);
		$builder->select($campos);
		if ($tablasSecundarias != false) {
			$builder->from($tablasSecundarias);
		}
		foreach ($where as $w) {
			$builder->where($w);
		}
		$builder->orderBy($order);
		$query = $builder->get();
		return $query->getResult();
	}
	// se añade la funcion Join
	public static function getDataJoin($campos, $tablaPrincipal, $joins, $where, $order)
	{
		$db = \Config\Database::connect();
		$builder = $db->table($tablaPrincipal);
		$builder->select($campos);
		foreach ($joins as $j) {
			$builder->join($j['tabla'], $j['on']);
		}
		foreach ($where as $w) {
			$builder->where($w);
		}
		$builder->orderBy($order);
		$query = $builder->get();
		return $query->getResult();
	}

	public static function actualizar($tabla, $where, $data)
	{
		$db = \Config\Database::connect();
		$builder = $db->table($tabla);
		$builder->where($where);
		$builder->update($data);
		return $db->affectedRows();
	}
	public static function insertar($tabla, $data)
	{
		$db = \Config\Database::connect();
		$builder = $db->table($tabla);
		$builder->insert($data);
		return $db->affectedRows();
	}
	public function eliminar($tabla, $where)
	{
		/*$this->db->delete($tabla,$where);
		return $this->db->affected_rows();*/
	}

	public function ultimoId()
	{
		/*$this->db->select("
			last_insert_id() as id
		",false);
		$query=$this->db->get();
		if($query->num_rows()>0){
			$result=$query->result();
			return $result[0]->id;
		}
		return false;*/
	}
}
