<?php namespace App\Models;
use CodeIgniter\Model;

class Usuarios extends Model
{
	protected $table      = 'usuarios';
    protected $primaryKey = 'usua_ide';

    protected $useAutoIncrement = true;

    protected $returnType     = 'object';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
    	'usua_ide', 
    	'usua_enti_ide',
    	'usua_paterno',
    	'usua_materno',
    	'usua_nombres',
    	'usua_user',
    	'usua_pass',
    	'usua_esta_ide'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'usua_created_at';
    protected $updatedField  = 'usua_updated_at';
    protected $deletedField  = 'usua_deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

	public function getData($campos,$tabla,$where=false,$order=false){

		/*$this->db->select($campos,false);
		$this->db->from($tabla);
		if($where!=false)
			$this->db->where($where);
		if($orden!=false)
			$this->db->order_by($orden);
		$query=$this->db->get();
		if($query->num_rows()>0){
			return $query->result();
		}
		return array();*/
	}
	public function ultimoId(){
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
