<?php 
$db = new SQLite3('db_film.sqlite');

if(!$db) {
    echo $db->lastErrorMsg();
}

$db->query("CREATE TABLE IF NOT EXISTS films (
    id INTEGER PRIMARY KEY,
    judul TEXT NOT NULL,
    gambar TEXT NOT NULL,
    tanggal DATETIME,
    status TEXT CHECK( status IN ('sudah', 'belum') ) DEFAULT 'belum'
)");
