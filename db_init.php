<?php

require_once __DIR__ . '/config.php';
try {

    $db = new PDO('sqlite:database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //error mode to catch errors
    $db->exec("PRAGMA foreign_keys = ON;");


    //UUID format: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
    // $db->sqliteCreateFunction("uuid", function () {
    //     return sprintf(
    //         "%04x%04x-%04x-%04x-%04x-%04x%04x%04x",
    //         mt_rand(0, 0xffff),
    //         mt_rand(0, 0xffff),
    //         mt_rand(0, 0xffff),
    //         mt_rand(0, 0xffff) | 0x4000,
    //         mt_rand(0, 0xffff) | 0x8000,
    //         mt_rand(0, 0xffff),
    //         mt_rand(0, 0xffff),
    //         mt_rand(0, 0xffff)
    //     );
    // },);

    //User table
    $db->exec(
        "CREATE TABLE IF NOT EXISTS Users(
        id TEXT PRIMARY KEY,
        full_name TEXT,
        email TEXT UNIQUE NOT NULL,
        role TEXT NOT NULL CHECK (role IN ('user','company','admin')),
        password TEXT NOT NULL,
        company_id TEXT,
        balance REAL DEFAULT 1000,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
        FOREIGN KEY (company_id) REFERENCES Bus_Company(id) ON DELETE SET NULL);"
    );

    //Bus company
    $db->exec(
        "CREATE TABLE IF NOT EXISTS Bus_Company(
        id TEXT PRIMARY KEY,
        name TEXT UNIQUE NOT NULL,
        logo_path TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);"
    );

    //Trips
    $db->exec(
        "CREATE TABLE IF NOT EXISTS Trips(
        id TEXT PRIMARY KEY,
        company_id TEXT NOT NULL,
        destination_city TEXT NOT NULL,
        arrival_time DATETIME NOT NULL,
        departure_time DATETIME NOT NULL,
        departure_city TEXT NOT NULL,
        price INTEGER NOT NULL,
        capacity INTEGER NOT NULL,
        created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (company_id) REFERENCES Bus_Company(id) ON DELETE CASCADE);"
    );

    //Tickets
    $db->exec(
        "CREATE TABLE IF NOT EXISTS Tickets (
        id TEXT PRIMARY KEY,
        trip_id TEXT NOT NULL,
        user_id TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT 'active' CHECK (status IN ('active','canceled','expired')),
        total_price INTEGER NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (trip_id) REFERENCES Trips(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE);"
    );

    //Booked Seats
    $db->exec(
        "CREATE TABLE IF NOT EXISTS Booked_Seats(
        id TEXT PRIMARY KEY,
        ticket_id TEXT NOT NULL,
        seat_number INTEGER NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ticket_id) REFERENCES Tickets(id) ON DELETE CASCADE);"
    );

    //cOUPONS
    $db->exec(
        "CREATE TABLE IF NOT EXISTS Coupons(
        id TEXT PRIMARY KEY,
        code TEXT NOT NULL,
        discount REAL NOT NULL,
        company_id TEXT,
        usage_limit INTEGER NOT NULL,
        expire_date DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (company_id) REFERENCES Bus_Company(id) ON DELETE SET NULL);"
    );

    //User Coupons
    $db->exec(
        "CREATE TABLE IF NOT EXISTS User_Coupons (
        id TEXT PRIMARY KEY,
        coupon_id TEXT NOT NULL,
        user_id TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (coupon_id) REFERENCES Coupons(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE
        );"
    );

    echo "I hope it is done.";
} catch (PDOException $e) {
    echo "!!!! -> Error: " . $e->getMessage();
}
