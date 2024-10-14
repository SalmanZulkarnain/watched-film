<?php 

$db = new SQLite3('films.sqlite');

if(!$db) {
    echo $db->lastErrorMsg();
}

    $db->query("CREATE TABLE IF NOT EXISTS films
    (
    id INTEGER PRIMARY KEY,
    judul TEXT NOT NULL,
    gambar TEXT NOT NULL,
    tanggal DATETIME,
    status TEXT CHECK( status IN ('sudah', 'belum') ) DEFAULT 'belum'
    )");
 
// $db->query("DROP TABLE films");