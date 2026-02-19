<?php

namespace App\Models;

use CodeIgniter\Model;

class Generaldb extends Model
{
    /**
     * Select data from the table.
     *
     * @param string $table
     * @param mixed $where
     * @param mixed $join
     * @return array
     */
    public function selectData($table, $where = false, $join = false)
    {
        $builder = $this->db->table($table);

        if ($where !== false) {
            $builder->getWhere($where);
        }

        if ($join !== false) {
            $builder->join($join[0], $join[1], $join[2]);
        }

        return $builder->get()->getResult();
    }

    /**
     * Select specific data from the table.
     *
     * @param string|array $select
     * @param string $table
     * @param mixed $where
     * @param mixed $order_by
     * @param mixed $group_by
     * @param mixed $limit
     * @param mixed $join
     * @return array
     */
    public function selectSomeData($select, $table, $where = false, $order_by = false, $group_by = false, $limit = false, $join = false)
    {
        $builder = $this->db->table($table);

        $builder->select($select);

        if ($order_by !== false) {
            $builder->orderBy($order_by);
        }

        if ($limit !== false) {
            $builder->limit($limit);
        }

        if ($group_by !== false) {
            $builder->groupBy($group_by);
        }

        if ($join !== false) {
            $builder->join($join[0], $join[1], $join[2]);
        }

        if ($where !== false) {
            $builder->where($where);
        }

        return $builder->get()->getResult();
    }
    /**
     * Select specific data from the table.
     *
     * @param string|array $select
     * @param string $table
     * @param mixed $where
     * @param mixed $order_by
     * @param mixed $group_by
     * @param mixed $limit
     * @param mixed $join array()
     * @return array
     */
    public function selectSomeDataJoin($select, $table, $where = false, $order_by = false, $group_by = false, $limit = false, $join = false)
    {
        $builder = $this->db->table($table);

        $builder->select($select);

        if ($order_by !== false) {
            $builder->orderBy($order_by);
        }

        if ($limit !== false) {
            $builder->limit($limit);
        }

        if ($group_by !== false) {
            $builder->groupBy($group_by);
        }

        if ($join !== false) {
            foreach ($join as $j) {
                $builder->join($j[0], $j[1], $j[2]);
            }
        }

        if ($where !== false) {
            $builder->where($where);
        }
        return $builder->get()->getResult();
    }

    /**
     * Select specific data from the table.
     *
     * @param string|array $select
     * @param string $table
     * @param mixed $where
     * @param mixed $order_by
     * @param mixed $group_by
     * @param mixed $limit
     * @param mixed $join array()
     * @return array
     */
    public function selectDatatable($select, $table, $where = false, $order_by = false, $group_by = false, $limit = false, $join = false)
    {
        $builder = $this->db->table($table);

        $builder->select($select);

        if ($order_by !== false) {
            foreach ($order_by as $j) {
                $builder->orderBy($j[0], $j[1]);
            }
        }

        if ($limit !== false) {
            foreach ($limit as $j) {
                $builder->limit($j[0], $j[1]);
            }
        }

        if ($group_by !== false) {
            $builder->groupBy($group_by);
        }

        if ($join !== false) {
            foreach ($join as $j) {
                $builder->join($j[0], $j[1], $j[2]);
            }
        }

        if ($where !== false) {
            $builder->where($where);
        }

        return $builder->get()->getResult();
    }
    public function countAll($table, $where = false)
    {
        $builder = $this->db->table($table);


        if ($where !== false) {
            $builder->where($where);
        }

        return $builder->countAll();
    }



    /**
     * Insert data into the table.
     *
     * @param string $table
     * @param array $set
     * @return bool
     */
    public function insertData($table, $set)
    {
        $builder = $this->db->table($table);

        try {
            $builder->insert($set);
            $lastInsertId = $this->db->insertID();
            return $lastInsertId;
        } catch (\Exception $e) {
            // Handle the exception or log it
            return false;
        }
    }
    /**
     * InsertBatch data into the table.
     *
     * @param string $table
     * @param array $set
     * @return bool
     */
    public function insertDataBatch($table, $set)
    {
        $builder = $this->db->table($table);

        try {
            $builder->insertBatch($set);
            $rowsInsert = $this->db->affectedRows();
            return $rowsInsert;
        } catch (\Exception $e) {
            echo $e; // Handle the exception or log it
            return false;
        }
    }

    /**
     * Update data in the table based on the given condition.
     *
     * @param string $table
     * @param array $set
     * @param mixed $where
     * @return bool
     */
    public function updateData($table, $set, $where)
    {
        $builder = $this->db->table($table);

        try {
            $builder->where($where);
            $builder->set($set);
            return $builder->update();
            //return true;
        } catch (\Exception $e) {
            // Handle the exception or log it
            return false;
        }
    }

    /**
     * Delete data from the table based on the given condition.
     *
     * @param string $table
     * @param mixed $where
     * @return bool
     */
    public function deleteData($table, $where)
    {
        $builder = $this->db->table($table);
        $builder->where($where);

        try {
            $builder->delete();
            return true;
        } catch (\Exception $e) {
            // Handle the exception or log it
            //print_r($e);
            return false;
        }
    }
}
