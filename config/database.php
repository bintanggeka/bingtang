<?php
require_once __DIR__ . '/../vendor/autoload.php';

class Database {
    private $client;
    private $database;

    public function __construct() {
        try {
            // Koneksi ke MongoDB
            $this->client = new MongoDB\Client("mongodb://localhost:27017");
            $this->database = $this->client->rental_ps;
        } catch (Exception $e) {
            die("Error koneksi database: " . $e->getMessage());
        }
    }

    public function getDatabase() {
        return $this->database;
    }
} 