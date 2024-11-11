<?php
session_start();
include 'koneksi.php';

if (isset($_POST['tambah'])) {
    $judulfoto = $_POST['Judulfoto'];
    $deskripsifoto = $_POST['Deskripsifoto'];
    $tanggalunggah = date('y-m-d');
    $albumid = $_POST['albumid'];
    $userid = $_SESSION['userid'];

    // Ambil nama file dan ekstensi file
    $foto = $_FILES['lokasifile']['name'];
    $tmp = $_FILES['lokasifile']['tmp_name'];
    $lokasi = '../assets/img/';
    $namafoto = rand() . '-' . $foto;

    // Mendapatkan ekstensi file
    $ekstensi = strtolower(pathinfo($foto, PATHINFO_EXTENSION));

    // Daftar ekstensi file yang diizinkan
    $ekstensi_valid = ['jpg', 'jpeg', 'png', 'gif'];

    // Cek apakah ekstensi file valid
    if (in_array($ekstensi, $ekstensi_valid)) {
        // Pindahkan file ke folder yang ditentukan
        move_uploaded_file($tmp, $lokasi . $namafoto);

        // Simpan data ke database
        $sql = mysqli_query($koneksi, "INSERT INTO foto VALUES('', '$judulfoto','$deskripsifoto','$tanggalunggah','$namafoto','$albumid','$userid')");

        echo "<script>
        alert('Data berhasil disimpan!');
        location.href='../admin/foto.php'
        </script>";
    } else {
        echo "<script>
        alert('Hanya file gambar yang diperbolehkan untuk di-upload.');
        location.href='../admin/foto.php'
        </script>";
    }
}

if (isset($_POST['edit'])) {
    $fotoid = $_POST['fotoid'];
    $judulfoto = $_POST['Judulfoto'];
    $deskripsifoto = $_POST['Deskripsifoto'];
    $tanggalunggah = date('y-m-d');
    $albumid = $_POST['albumid'];
    $userid = $_SESSION['userid'];

    // Ambil nama file dan ekstensi file
    // $foto = $_FILES['lokasifile']['name'];
    // $tmp = $_FILES['lokasifile']['tmp_name'];
    // $lokasi = '../assets/img/';
    // $namafoto = rand() . '-' . $foto;

    // Mendapatkan ekstensi file
    $ekstensi = strtolower(pathinfo($foto, PATHINFO_EXTENSION));

    // Daftar ekstensi file yang diizinkan
    $ekstensi_valid = ['jpg', 'jpeg', 'png', 'gif'];

    if ($foto == null) {
        // Update tanpa mengubah gambar (foto lama tetap digunakan)
        $sql = mysqli_query($koneksi, "UPDATE foto SET judulfoto='$judulfoto', deskripsifoto='$deskripsifoto', tanggalunggah='$tanggalunggah', albumid='$albumid' WHERE fotoid='$fotoid'");
        echo "<script>
        alert('Data berhasil diperbarui!');
        location.href='../admin/foto.php'
        </script>";
    } else {
        // Cek apakah ekstensi file valid
        if (in_array($ekstensi, $ekstensi_valid)) {
            // Ambil data file lama
            $query = mysqli_query($koneksi, "SELECT * FROM foto WHERE fotoid='$fotoid'");
            $data = mysqli_fetch_array($query);
            // Hapus file lama jika ada
            if (is_file('../assets/img/' . $data['lokasifile'])) {
                unlink('../assets/img/' . $data['lokasifile']);
            }

            // Pindahkan file yang baru di-upload
            move_uploaded_file($tmp, $lokasi . $namafoto);

            // Update data ke database dengan mengganti foto
            $sql = mysqli_query($koneksi, "UPDATE foto SET judulfoto='$judulfoto', deskripsifoto='$deskripsifoto', tanggalunggah='$tanggalunggah', lokasifile='$namafoto', albumid='$albumid' WHERE fotoid='$fotoid'");

            echo "<script>
            alert('Data berhasil diperbarui!');
            location.href='../admin/foto.php'
            </script>";
        } else {
            echo "<script>
            alert('Hanya file gambar yang diperbolehkan untuk di-upload');
            location.href='../admin/foto.php'
            </script>";
        }
    }
}

if (isset($_POST['hapus'])) {
    $fotoid = $_POST['fotoid'];
    $query = mysqli_query($koneksi, "SELECT * FROM foto WHERE fotoid='$fotoid'");
    $data = mysqli_fetch_array($query);
    if (is_file('../assets/img/' . $data['lokasifile'])) {
        unlink('../assets/img/' . $data['lokasifile']);
    }

    $sql = mysqli_query($koneksi, "DELETE FROM foto WHERE fotoid='$fotoid'");
    echo "<script>
    alert('Data berhasil dihapus!');
    location.href='../admin/foto.php'
    </script>";
}
?>
