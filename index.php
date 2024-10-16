<?php

require 'db.php';
require 'functions.php';

$pesan = '';

$films = viewFilm();

doneFilm();

deleteFilm();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'tambah':
                $pesan = insertFilm();
                break;
            case 'update':
                updateFilm();
                break;
        }
    }
}

$film_edit = null;
if (isset($_GET['edit'])) {
    $film_edit = ambilFilm();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WATCHED FILM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
    <div class="container">


        <div class="modal-container">
            <!-- TAMBAH -->
            <input type="checkbox" id="modal-tambah" class="modal-tambah">
            <label class="modal-btn" for="modal-tambah">TAMBAH</label>
            <div class="modal">
                <div class="modal-content">
                    <h2>Isi Film</h2>
                    <form action="index.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="tambah">
                        <?php if ($film_edit): ?>
                            <input type="hidden" name="id">
                        <?php endif; ?>

                        <div class="input-group">
                            <input type="text" name="judul" placeholder="Masukkan nama film" autofocus>
                        </div>
                        <div class="input-group">
                            <input type="file" name="gambar" accept="image/*">
                        </div>
                        <div class="input-group">
                            <input type="date" name="tanggal" placeholder="Masukkan tanggal film">
                        </div>
                        <div class="input-group">
                            <input type="submit" name="submit" value="Tambah">
                        </div>
                    </form>
                    <label class="modal-close" for="modal-tambah">Tutup</label>
                </div>
            </div>

            <!-- EDIT  -->
            <input type="checkbox" id="modal-update" class="modal-update" <?php if (isset($_GET['edit'])) echo 'checked'; ?>>
            <div class="modal">
                <div class="modal-content">
                    <h2>Edit Film</h2>
                    <form action="index.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <?php if ($film_edit): ?>
                            <input type="hidden" name="id" value="<?php echo isset($film_edit['id']) ? $film_edit['id'] : ''; ?>">
                        <?php endif; ?>

                        <div class="input-group">
                            <input type="text" name="judul" value="<?php echo isset($film_edit['judul']) ? $film_edit['judul'] : ''; ?>" autofocus placeholder="Masukkan nama film">
                        </div>
                        <div class="input-group">
                            <input type="file" name="gambar" accept="image/*">
                        </div>
                        <div class="input-group">
                            <input type="date" name="tanggal" value="<?php echo isset($film_edit['tanggal']) ? $film_edit['tanggal'] : ''; ?>" placeholder="Masukkan tanggal film">
                        </div>
                        <div class="input-group">
                            <input type="submit" name="submit" value="Update">
                        </div>
                    </form>
                    <label class="modal-close" for="modal-update">Tutup</label>
                </div>
            </div>
            <?php if (!empty($pesan)): ?>
                <p style="color: red; margin-bottom: 20px;"><?php echo $pesan; ?></p>
            <?php endif; ?>
        </div>
        
        <div class="card-container">
            <?php foreach ($films as $key => $film) {
                $formattedDate = date('d/m/Y', strtotime($film['tanggal'])); ?>
                <div class="card">
                    <div class="image-container">
                        <input type="checkbox"
                            id="check_<?php echo $film['id']; ?>"
                            class="status-checkbox"
                            onclick="window.location.href = 'index.php?done=<?php echo $film['id']; ?>&status=<?php echo $film['status']; ?>'"
                            <?php echo $film['status'] == 'sudah' ? 'checked' : ''; ?>>
                        <label for="check_<?php echo $film['id']; ?>">
                            <i class="fa-solid fa-square-check check-icon"></i>
                            <i class="fa-solid fa-circle-xmark uncheck-icon"></i>
                        </label>
                        <img src="<?php echo $film['gambar']; ?>" alt="<?php echo $film['judul']; ?>">
                    </div>
                    <div class="film-container">
                        <h3><?php echo $film['judul']; ?></h3>
                        <h5><?php echo $formattedDate; ?></h5>
                        <div class="action-btn">
                            <a href="index.php?edit=<?php echo $film['id']; ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="index.php?delete=<?php echo $film['id']; ?>" onclick="return confirm('Apakah anda yakin ingin menghapus?')"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>

</html>