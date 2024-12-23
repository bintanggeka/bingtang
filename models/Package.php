<?php
require_once __DIR__ . '/../config/database.php';

class Package {
    private $collection;

    public function __construct() {
        $db = new Database();
        $this->collection = $db->getDatabase()->packages;
    }

    public function createPackage($data) {
        return $this->collection->insertOne([
            'name' => $data['name'],
            'duration' => (int) $data['duration'], // dalam jam
            'price' => (float) $data['price'],
            'description' => $data['description'] ?? '',
            'is_active' => true,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ]);
    }

    public function getAllPackages() {
        return $this->collection->find(['is_active' => true])->toArray();
    }

    public function getPackageById($id) {
        return $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
    }

    public function updatePackage($id, $data) {
        $data['updated_at'] = new MongoDB\BSON\UTCDateTime();
        return $this->collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => $data]
        );
    }

    public function deletePackage($id) {
        return $this->collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => [
                'is_active' => false,
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ]]
        );
    }
} 