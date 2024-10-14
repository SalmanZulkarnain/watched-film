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

    if(isset($_POST['id'])) {
        $id = $_POST['id'];
        $judul = $_POST['judul'];
        $tanggal = $_POST['tanggal'];

        $current_film = $db->querySingle("SELECT gambar FROM films WHERE id = '$id'", true);
        $gambar = $current_film['gambar'];

        if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["gambar"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["gambar"]["tmp_name"]);
            if($check !== false) {
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            if ($_FILES["gambar"]["size"] > 500000) {
                echo "Sorry, your file is too large.";
                $uploadOk = 0;
            }

            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                    if(file_exists($current_film['gambar'])) {
                        unlink($current_film['gambar']);
                    }
                    $gambar = $target_file;
                } else {
                    echo "Sorry, there was an error uploading your file.";
                    return;
                }
            }
        }

        $db->query("UPDATE films SET judul = '$judul', gambar = '$gambar', tanggal = '$tanggal' WHERE id = '$id'");
        header('Location: index.php');
    }
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
