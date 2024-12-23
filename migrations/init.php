<?php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    // Koneksi ke MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");
    
    // Buat database rental_ps
    $database = $client->rental_ps;
    
    // Buat collections
    $database->createCollection('users');
    $database->createCollection('consoles');
    $database->createCollection('packages');
    $database->createCollection('rentals');
    
    // Buat indexes
    $database->users->createIndex(['email' => 1], ['unique' => true]);
    $database->rentals->createIndex(['user_id' => 1]);
    $database->rentals->createIndex(['console_id' => 1]);
    $database->rentals->createIndex(['package_id' => 1]);
    $database->rentals->createIndex(['status' => 1]);
    $database->rentals->createIndex(['created_at' => 1]);
    
    // Buat admin default
    $database->users->insertOne([
        'name' => 'Admin',
        'email' => 'admin@rentalps.com',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'phone' => '08123456789',
        'address' => 'Jl. Admin No. 1',
        'role' => 'admin',
        'is_active' => true,
        'created_at' => new MongoDB\BSON\UTCDateTime(),
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ]);
    
    // Buat paket default
    $packages = [
        [
            'name' => 'Paket 2 Jam',
            'duration' => 2,
            'price' => 30000,
            'description' => 'Paket rental PS selama 2 jam',
            'is_active' => true,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ],
        [
            'name' => 'Paket 4 Jam',
            'duration' => 4,
            'price' => 50000,
            'description' => 'Paket rental PS selama 4 jam',
            'is_active' => true,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ],
        [
            'name' => 'Paket 8 Jam',
            'duration' => 8,
            'price' => 90000,
            'description' => 'Paket rental PS selama 8 jam',
            'is_active' => true,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ]
    ];
    
    $database->packages->insertMany($packages);
    
    // Buat konsol default
    $consoles = [
        [
            'name' => 'PlayStation 4 Slim',
            'type' => 'PS4',
            'status' => 'available',
            'price_per_hour' => 15000,
            'accessories' => ['Stick 1', 'Stick 2', 'HDMI Cable'],
            'games' => ['FIFA 24', 'GTA V', 'God of War'],
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ],
        [
            'name' => 'PlayStation 4 Pro',
            'type' => 'PS4',
            'status' => 'available',
            'price_per_hour' => 20000,
            'accessories' => ['Stick 1', 'Stick 2', 'HDMI Cable'],
            'games' => ['FIFA 24', 'GTA V', 'God of War', 'Spider-Man'],
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ],
        [
            'name' => 'PlayStation 5',
            'type' => 'PS5',
            'status' => 'available',
            'price_per_hour' => 25000,
            'accessories' => ['DualSense 1', 'DualSense 2', 'HDMI Cable'],
            'games' => ['FIFA 24', 'Spider-Man 2', 'God of War Ragnarök'],
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ]
    ];
    
    $database->consoles->insertMany($consoles);
    
    echo "Migrasi berhasil dilakukan!\n";
    echo "Email admin: admin@rentalps.com\n";
    echo "Password admin: admin123\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 