<?php
require_once __DIR__ . '/../config/database.php';

class Console {
    private $collection;

    public function __construct() {
        $db = new Database();
        $this->collection = $db->getDatabase()->consoles;
    }

    public function addConsole($data) {
        return $this->collection->insertOne([
            'name' => $data['name'],
            'type' => $data['type'],
            'status' => 'available',
            'price_per_hour' => (float) $data['price_per_hour'],
            'accessories' => $data['accessories'] ?? [],
            'games' => $data['games'] ?? [],
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ]);
    }

    public function getAllConsoles() {
        return $this->collection->find()->toArray();
    }

    public function getConsoleById($id) {
        return $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
    }

    public function updateConsole($id, $data) {
        $data['updated_at'] = new MongoDB\BSON\UTCDateTime();
        return $this->collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => $data]
        );
    }

    public function deleteConsole($id) {
        return $this->collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
    }

    public function updateStatus($id, $status) {
        return $this->collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => [
                'status' => $status,
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ]]
        );
    }
} 