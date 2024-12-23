<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $collection;

    public function __construct() {
        $db = new Database();
        $this->collection = $db->getDatabase()->users;
    }

    public function createUser($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        return $this->collection->insertOne([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'phone' => $data['phone'],
            'address' => $data['address'],
            'role' => $data['role'] ?? 'customer', // admin atau customer
            'is_active' => true,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ]);
    }

    public function getUserByEmail($email) {
        return $this->collection->findOne(['email' => $email]);
    }

    public function getUserById($id) {
        return $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
    }

    public function getAllUsers() {
        return $this->collection->find()->toArray();
    }

    public function updateUser($id, $data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $data['updated_at'] = new MongoDB\BSON\UTCDateTime();
        
        return $this->collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => $data]
        );
    }

    public function deleteUser($id) {
        return $this->collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => [
                'is_active' => false,
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ]]
        );
    }

    public function verifyPassword($email, $password) {
        $user = $this->getUserByEmail($email);
        if (!$user) return false;
        
        return password_verify($password, $user->password);
    }
} 