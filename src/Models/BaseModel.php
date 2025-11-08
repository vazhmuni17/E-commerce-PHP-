<?php
namespace Src\Models;

use Config\Database;

abstract class BaseModel {
    protected $db;
    protected $table;
    protected $fillable = [];
    
    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }
    
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function findAll($limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table}";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
            if ($offset) {
                $sql .= " OFFSET " . (int)$offset;
            }
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function create($data) {
        $fields = [];
        $values = [];
        $bindings = [];
        
        foreach ($this->fillable as $field) {
            if (isset($data[$field])) {
                $fields[] = $field;
                $values[] = ":{$field}";
                $bindings[$field] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $values) . ")";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $fields = [];
        $bindings = ['id' => $id];
        
        foreach ($this->fillable as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = :{$field}";
                $bindings[$field] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($bindings);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    public function where($column, $operator, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }
}