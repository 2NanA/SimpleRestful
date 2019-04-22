<?php
namespace Restful\Core;

use Restful\Core\Pdoutil as Pdoutil;

class User
{
    public $id;
    public $userName;
    public $password;
    public $nullIfDeleted;
    public $department;
    public $email;
    public $createdBy;
    public $createdDate;
    public $lastModifiedBy;
    public $lastModifiedDate;

    protected $objectRelationMapping = [
        'id' => 'id',
        'Username' => 'userName',
        'Password' => 'password',
        'NullIfDeleted' => 'nullIfDeleted',
        'Department' => 'department',
        'Email' => 'email',
        'CreatedBy' => 'createdBy',
        'CreatedDate' => 'createdDate',
        'LastModifiedBy' => 'lastModifiedBy',
        'LastModifiedDate' => 'lastModifiedDate',
    ];
    
    protected $table = 'users';

    private $pdo;

    public function __construct($id = null)
    {
        $this->pdo = Pdoutil::getInstance();
        $this->find($id);
    }

    public function create($post)
    {
        foreach ($post as $key => $value) {
            if (!array_key_exists($key, $this->objectRelationMapping)) {
                throw new \Exception("The field {$key} is not existed in DB");
            }
        }
        $inserted = $this->pdo->insert($this->table, $post);
        return $inserted;
    }

    public function find($id)
    {
        if (!$id) {
            return false;
        }

        $result = $this->pdo->select("select * from users where id = :id", Pdoutil::SELECT_MODE_ONE, [':id' => $id]);
        
        if (!$result) {
            return false;
        }
        foreach ($result as $key => $value) {
            $property = $this->objectRelationMapping[$key];
            $this->$property = $value;
        }
        return true;
    }

    public function findAll()
    {
        $ids = $this->pdo->select("select id from users", Pdoutil::SELECT_MODE_ALL);
        return $ids;
    }

    public function delete($id)
    {
        $deleted = $this->pdo->delete($this->table, $id);
        return $deleted;
    }

    public function patch($data)
    {
        $updated = $this->pdo->update($this->table, $data, $this->id);
        return $updated;
    }

    
    public function put($data)
    {
        $putData = [];
        foreach ($this->objectRelationMapping as $key => $value) {
            $putData[$key] = empty($data[$key]) ? null :$data[$key];
        }
        unset($putData['id']);

        $updated = $this->pdo->update($this->table, $putData, $this->id);
        return $updated;
    }

    public function getArrayByORMapping($userId)
    {
        $return = [];
        $user = $this->find($userId);

        if ($user) {
            foreach ($this->objectRelationMapping as $key => $value) {
                $return[$key] = $this->$value;
            }
            return $return;
        } else {
            return false;
        }
    }
}
