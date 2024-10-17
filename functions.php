<?php

require 'config.php';

function insertFilm()
{
    global $db;

    $pesan = '';
    if (isset($_POST['submit'])) {
        $judul = $_POST['judul'];
        $tanggal = $_POST['tanggal'];

        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["gambar"]["name"]);

        $check = getimagesize($_FILES["gambar"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = $target_file;

                $stmt = $db->prepare("INSERT INTO films (judul, gambar, tanggal) VALUES (:judul, :gambar, :tanggal)");#
                $stmt->bindParam(':judul', $judul, SQLITE3_TEXT);
                $stmt->bindParam(':gambar', $gambar, SQLITE3_TEXT);
                $stmt->bindParam(':tanggal', $tanggal, SQLITE3_TEXT);

                if ($stmt->execute()) {
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

function viewFilm()
{
    global $db;

    $result = $db->query("SELECT * FROM films ORDER BY status ASC, tanggal DESC");
    $data = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $data[] = $row;
    }

    return $data;
}

function doneFilm()
{
    global $db;

    if (isset($_GET['done'])) {
        $status = 'belum';
        $id = $_GET['done'];

        if ($_GET['status'] == 'belum') {
            $status = 'sudah';
        } else {
            $status = 'belum';
        }
        $stmt = $db->prepare("UPDATE films SET status = :status WHERE id = :id");
        $stmt->bindParam(':id', $id, SQLITE3_INTEGER);
        $stmt->bindParam(':status', $status, SQLITE3_TEXT);

        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        }
    }
}
function ambilFilm()
{
    global $db;

    if (!isset($_GET['edit'])) {
        return null;
    }

    $id = $_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM films WHERE id = :id");
    $stmt->bindParam(':id', $id, SQLITE3_INTEGER);
    $ambil = $stmt->execute();

    return $ambil->fetchArray(SQLITE3_ASSOC);
}

function updateFilm()
{
    global $db;

    $pesan = '';
    if (isset($_POST['submit'])) {
        $id = $_POST['id'];
        $judul = $_POST['judul'];
        $tanggal = $_POST['tanggal'];
        
        // Ambil gambar lama dari database
        $stmt = $db->prepare("SELECT gambar FROM films WHERE id = :id");
        $stmt->bindParam(':id', $id, SQLITE3_INTEGER);
        $current_image = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        $gambar = $current_image['gambar'];

        // Cek apakah ada file gambar baru diupload
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["gambar"]["name"]);

            $check = getimagesize($_FILES["gambar"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                    // Hapus gambar lama jika ada
                    if (file_exists($gambar)) {
                        unlink($gambar);
                    }
                    $gambar = $target_file; // Update dengan gambar baru
                } else {
                    $pesan = "Gagal mengunggah gambar.";
                    return $pesan;
                }
            } else {
                $pesan = "File bukan gambar.";
                return $pesan;
            }
        } // Jika tidak ada gambar baru, gunakan gambar lama

        // Update data film
        $stmt = $db->prepare("UPDATE films SET judul = :judul, gambar = :gambar, tanggal = :tanggal WHERE id = :id");
        $stmt->bindParam(':id', $id, SQLITE3_INTEGER);
        $stmt->bindParam(':judul', $judul, SQLITE3_TEXT);
        $stmt->bindParam(':tanggal', $tanggal, SQLITE3_TEXT);
        $stmt->bindParam(':gambar', $gambar, SQLITE3_TEXT);

        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        } else {
            $pesan = "Gagal memperbarui data di database.";
        }
    }
    return $pesan;
}

function deleteFilm()
{
    global $db;

    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];

        $film = $db->querySingle("SELECT gambar FROM films WHERE id = :id", true);

        if ($film && file_exists($film['gambar'])) {
            unlink($film['gambar']);
        }

        $stmt = $db->prepare("DELETE FROM films WHERE id = :id");
        $stmt->bindParam(':id', $id, SQLITE3_INTEGER);

        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        }
    }
}
