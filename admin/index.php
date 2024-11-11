<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login') {
  echo "<script>
            alert('Anda belum login');
            location.href='../index.php';
          </script>";
  exit("User belum login.");
}

$userid = $_SESSION['userid'];
include '../config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gallery Photo</title>
  <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container px-4 px-lg-5">
      <a class="navbar-brand" href="index.php">Galeri</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="home.php">Home</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">Add here</a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
              <li><a class="dropdown-item" href="album.php">Album</a></li>
              <li><a class="dropdown-item" href="foto.php">Photo</a></li>
            </ul>
          </li>
        </ul>
        <a href="../config/aksi_logout.php" class="btn btn-outline-primary">Keluar</a>
      </div>
    </div>
  </nav>

  <header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
      <div class="text-center text-white">
        <h1 class="display-4 fw-bolder">Selamat Datang Di Galeri Foto</h1>
        <p class="lead fw-normal text-white-50 mb-0">hai.</p>
      </div>
    </div>
  </header>

  <div class="container mt-2">
    <div class="row">
      <?php
      $query = mysqli_query($koneksi, "SELECT * FROM foto 
                                        INNER JOIN user ON foto.userid=user.userid 
                                        INNER JOIN album ON foto.albumid=album.albumid");
      while ($data = mysqli_fetch_array($query)) {
        $fotoid = $data['fotoid'];
        $ceksuka = mysqli_query($koneksi, "SELECT * FROM likefoto WHERE fotoid='$fotoid' AND userid='$userid'");
        $likeCount = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM likefoto WHERE fotoid='$fotoid'"));
        ?>
        <div class="col-md-3 mt-2">
          <a type="button" data-bs-toggle="modal" data-bs-target="#komentar<?php echo $fotoid ?>">
            <div class="card mb-2">
              <img style="height: 12rem;" src="../assets/img/<?php echo $data['lokasifile'] ?>" class="card-img-top"
                title="<?php echo htmlspecialchars($data['Judulfoto']) ?>" alt="Foto Album">
              <div class="card-footer text-center">
                <a href="../config/proses_like.php?fotoid=<?php echo $fotoid ?>" type="submit"
                  name="<?php echo (mysqli_num_rows($ceksuka) == 1) ? 'batalsuka' : 'suka'; ?>">
                  <i class="<?php echo (mysqli_num_rows($ceksuka) == 1) ? 'fa fa-heart' : 'fa-regular fa-heart'; ?>"></i>
                </a>
                <?php echo $likeCount . ' Suka'; ?>
                <a href="#" type="button" data-bs-toggle="modal" data-bs-target="#komentar<?php echo $fotoid ?>"><i
                    class="fa-regular fa-comment"></i></a>
                <?php
                $jmlkomen = mysqli_query($koneksi, "SELECT * FROM komentarfoto WHERE fotoid='$fotoid'");
                echo mysqli_num_rows($jmlkomen) . ' Komentar';
                ?>
              </div>
            </div>
          </a>

          <!-- Modal -->
          <div class="modal fade" id="komentar<?php echo $fotoid ?>" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
              <div class="modal-content">
                <div class="modal-body">
                  <div class="row">
                    <div class="col-md-8">
                      <img src="../assets/img/<?php echo $data['lokasifile'] ?>" class="card-img-top"
                        title="<?php echo htmlspecialchars($data['Judulfoto']) ?>" alt="Foto Album">
                    </div>
                    <div class="col-md-4">
                      <div class="m-2">
                        <div class="sticky-top">
                          <strong><?php echo htmlspecialchars($data['Judulfoto']) ?></strong><br>
                          <span class="badge bg-secondary"><?php echo htmlspecialchars($data['NamaLengkap']) ?></span>
                          <span class="badge bg-secondary"><?php echo $data['tanggalunggah'] ?></span>
                          <span class="badge bg-primary"><?php echo htmlspecialchars($data['namaalbum']) ?></span>
                        </div>
                        <hr>
                        <p align="left">
                          <?php echo htmlspecialchars($data['Deskripsifoto']) ?>
                          <hr>
                          <?php
                          $komentar = mysqli_query($koneksi, "SELECT * FROM komentarfoto 
                                                              INNER JOIN user ON komentarfoto.userid=user.userid 
                                                              WHERE komentarfoto.fotoid='$fotoid'");
                          while ($row = mysqli_fetch_array($komentar)) {
                            ?>
                          <p align="left">
                            <strong><?php echo htmlspecialchars($row['NamaLengkap']) ?></strong>
                            <?php echo htmlspecialchars($row['isikomentar']) ?>
                            <?php if ($row['userid'] == $userid) { ?>
                              <!-- Tampilkan tombol hapus jika pengguna adalah pemilik komentar -->
                              <a href="../config/proses_hapus_komentar.php?komentarid=<?php echo $row['komentarid'] ?>&fotoid=<?php echo $fotoid ?>"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')"
                                class="btn btn-sm btn-danger">Hapus</a>
                            <?php } ?>
                          </p>
                        <?php } ?>
                        <hr>
                        <div class="sticky-bottom">
                          <form action="../config/proses.komentar.php" method="POST">
                            <div class="input-group">
                              <input type="hidden" name="fotoid" value="<?php echo $fotoid ?>">
                              <input type="text" name="isikomentar" class="form-control" placeholder="Tambahkan Komentar"
                                required>
                              <div class="input-group-prepend">
                                <button type="submit" name="kirimkomentar" class="btn btn-outline-primary">Kirim</button>
                              </div>
                            </div>
                          </form>
                        </div>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>

  <!-- Include jQuery and Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
</body>

</html>