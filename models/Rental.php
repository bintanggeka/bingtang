<?php
require_once __DIR__ . '/../config/database.php';

class Rental {
    private $collection;

    public function __construct() {
        $db = new Database();
        $this->collection = $db->getDatabase()->rentals;
    }

    public function createRental($data) {
        return $this->collection->insertOne([
            'user_id' => new MongoDB\BSON\ObjectId($data['user_id']),
            'console_id' => new MongoDB\BSON\ObjectId($data['console_id']),
            'package_id' => new MongoDB\BSON\ObjectId($data['package_id']),
            'start_time' => new MongoDB\BSON\UTCDateTime(strtotime($data['start_time']) * 1000),
            'end_time' => new MongoDB\BSON\UTCDateTime(strtotime($data['end_time']) * 1000),
            'total_price' => (float) $data['total_price'],
            'status' => 'pending', // pending, active, completed, cancelled
            'accessories' => $data['accessories'] ?? [],
            'games' => $data['games'] ?? [],
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ]);
    }

    public function getRentalsByUser($userId) {
        return $this->collection->find([
            'user_id' => new MongoDB\BSON\ObjectId($userId)
        ])->toArray();
    }

    public function getAllRentals() {
        return $this->collection->find()->toArray();
    }

    public function updateRentalStatus($id, $status) {
        return $this->collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => [
                'status' => $status,
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ]]
        );
    }

    public function getRentalById($id) {
        return $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
    }

    public function getActiveRentals() {
        return $this->collection->find(['status' => 'active'])->toArray();
    }

    public function getRentalReport($startDate = null, $endDate = null) {
        $pipeline = [];
        
        // Add match stage if dates are provided
        if ($startDate && $endDate) {
            $pipeline[] = [
                '$match' => [
                    'created_at' => [
                        '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000),
                        '$lte' => new MongoDB\BSON\UTCDateTime(strtotime($endDate) * 1000)
                    ]
                ]
            ];
        }
        
        // Add group stage
        $pipeline[] = [
            '$group' => [
                '_id' => null,
                'total_income' => ['$sum' => '$total_price'],
                'total_rentals' => ['$sum' => 1]
            ]
        ];
        
        $result = $this->collection->aggregate($pipeline)->toArray();
        
        // Return default values if no results
        if (empty($result)) {
            return [[
                'total_income' => 0,
                'total_rentals' => 0
            ]];
        }
        
        return $result;
    }

    public function getMostRentedConsoles($startDate = null, $endDate = null) {
        $pipeline = [];
        
        // Add match stage if dates are provided
        if ($startDate && $endDate) {
            $pipeline[] = [
                '$match' => [
                    'created_at' => [
                        '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000),
                        '$lte' => new MongoDB\BSON\UTCDateTime(strtotime($endDate) * 1000)
                    ]
                ]
            ];
        }
        
        // Add group and sort stages
        $pipeline[] = [
            '$group' => [
                '_id' => '$console_id',
                'total_rentals' => ['$sum' => 1],
                'total_income' => ['$sum' => '$total_price']
            ]
        ];
        
        $pipeline[] = [
            '$sort' => ['total_rentals' => -1]
        ];
        
        $pipeline[] = [
            '$limit' => 5
        ];
        
        return $this->collection->aggregate($pipeline)->toArray();
    }

    public function getTopCustomers($startDate = null, $endDate = null) {
        $pipeline = [];
        
        // Add match stage if dates are provided
        if ($startDate && $endDate) {
            $pipeline[] = [
                '$match' => [
                    'created_at' => [
                        '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000),
                        '$lte' => new MongoDB\BSON\UTCDateTime(strtotime($endDate) * 1000)
                    ]
                ]
            ];
        }
        
        // Add group and sort stages
        $pipeline[] = [
            '$group' => [
                '_id' => '$user_id',
                'total_rentals' => ['$sum' => 1],
                'total_spent' => ['$sum' => '$total_price']
            ]
        ];
        
        $pipeline[] = [
            '$sort' => ['total_spent' => -1]
        ];
        
        $pipeline[] = [
            '$limit' => 5
        ];
        
        return $this->collection->aggregate($pipeline)->toArray();
    }
} 