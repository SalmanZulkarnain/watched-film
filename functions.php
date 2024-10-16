<?php

require 'db.php';

function insertFilm() {
    global $db;
    
    $pesan = '';
    if(isset($_POST['submit'])) {
        $judul = $_POST['judul'];
        $tanggal = $_POST['tanggal'];

        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["gambar"]["name"]);

        $check = getimagesize($_FILES["gambar"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = $target_file;
            
                $query = "INSERT INTO films (judul, gambar, tanggal) VALUES ('$judul', '$gambar', '$tanggal')";
                
                if ($db->query($query)) {
                    header('Location: index.php');
                    exit;
                } else {
                    $pesan = "Gagal menyimpan data ke database.";
                }
            } else {
                $pesan = "Maaf, terjadi kesalahan saat mengupload file.";
            }
        } else {
            $pesan = "File bukan gambar.";
        }
    }
    return $pesan;
}

function viewFilm() {
    global $db;
    
    $result = $db->query("SELECT * FROM films ORDER BY status ASC, tanggal DESC");
    $data = [];
    while($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $data[] = $row;
    }

    return $data;
}

function doneFilm(){
    global $db;

    if(isset($_GET['done'])) {
        $status = 'belum';
        $id = $_GET['done'];
        
        if($_GET['status'] == 'belum') {
            $status = 'sudah';
        } else {
            $status = 'belum';
        }
        $db->query("UPDATE films SET status = '$status' WHERE id = '$id'");
        header('Location: index.php');
        exit;
    }
}
function ambilFilm() {
    global $db;

    if (!isset($_GET['edit'])) {
        return null; 
    }

    $id = $_GET['edit'];
    $ambil = $db->query("SELECT * FROM films WHERE id = '$id'");
    
    return $ambil->fetchArray(SQLITE3_ASSOC);
}   

function updateFilm() {
    global $db;
    
    $pesan = '';
    if(isset($_POST['submit'])) {
        $id = $_POST['id'];
        $judul = $_POST['judul'];
        $tanggal = $_POST['tanggal'];

        // Ambil data film yang ada
        $current_film = $db->querySingle("SELECT gambar FROM films WHERE id = '$id'", true);
        $gambar = $current_film['gambar'];

        // Cek apakah ada file gambar baru diupload
        if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["gambar"]["name"]);

            $check = getimagesize($_FILES["gambar"]["tmp_name"]);
            if($check !== false) {
                if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                    // Hapus gambar lama jika ada
                    if(file_exists($current_film['gambar'])) {
                        unlink($current_film['gambar']);
                    }
                    $gambar = $target_file;
                } else {
                    $pesan = "Maaf, terjadi kesalahan saat mengupload file.";
                    return $pesan;
                }
            } else {
                $pesan = "File bukan gambar.";
                return $pesan;
            }
        }

        // Update data film
        $query = "UPDATE films SET judul = '$judul', gambar = '$gambar', tanggal = '$tanggal' WHERE id = '$id'";
        
        if ($db->query($query)) {
            header('Location: index.php');
            exit;
        } else {
            $pesan = "Gagal memperbarui data di database.";
        }
    }
    return $pesan;
}

function deleteFilm() {
    global $db;

    if(isset($_GET['delete'])) {
        $id = $_GET['delete'];
    
        $film = $db->querySingle("SELECT gambar FROM films WHERE id = '$id'", true);
        
        if($film && file_exists($film['gambar'])) {
            unlink($film['gambar']);
        }

        $db->query("DELETE FROM films WHERE id = '$id'");
        
        header('Location: index.php');
        exit;
    }
}
