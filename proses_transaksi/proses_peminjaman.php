<?php
include_once("../Database/koneksi.php");
//insert detil
if(isset($_POST['detil'])) {
  $no_copy = $_POST['no_copy'];
  $jml_pinjam = $_POST['jml_pinjam'];

  $cari = mysqli_query($koneksi, "SELECT no_copy,jml_pinjam from detil_pinjam WHERE no_copy = '$no_copy' and no_peminjaman is null");
  $nomor_copy = mysqli_fetch_assoc($cari);


  $cari = mysqli_query($koneksi, "SELECT no_copy from detil_pinjam WHERE no_peminjaman is null AND tgl_kbl is null");
  $barisT = mysqli_num_rows($cari);

  if($no_copy == $nomor_copy['no_copy']){
    $total = $jml_pinjam+$nomor_copy['jml_pinjam'];
    $result = mysqli_query($koneksi, "UPDATE detil_pinjam set jml_pinjam = '$total' WHERE no_copy = '$no_copy' and no_peminjaman is null");
  } else {

    if($barisT >= 3) {
      echo $s = "<script type='text/javascript'>
            alert ('Jumlah Peminjaman Melebihi 3 Buku !');
            window.location.replace('http://localhost/SistemPerpustakaan/peminjaman.php');
          </script>";
      return $s;
    }

    $result = mysqli_query($koneksi, "INSERT INTO detil_pinjam(no_copy,jml_pinjam) VALUES('$no_copy','$jml_pinjam')");
  }
  if($result){
    // echo "<script type='text/javascript'>
    //         window.location.replace('http://localhost/SistemPerpustakaan/peminjaman.php');
    //       </script>"; 
    header("location:../peminjaman.php");
  } else {
    // echo "<script type='text/javascript'>
    //         window.location.replace('http://localhost/SistemPerpustakaan/peminjaman.php');
    //       </script>";
    header("location:../peminjaman.php");
  }

}

//hapus detil
if(isset($_POST['hapusDetil'])) {
  $no_copy = $_POST['no_copy'];

  $result = mysqli_query($koneksi, "DELETE FROM detil_pinjam WHERE no_copy = $no_copy AND no_peminjaman is null");
  if($result){
    // echo "<script type='text/javascript'>
    //         window.location.replace('http://localhost/SistemPerpustakaan/peminjaman.php');
    //       </script>"; 
    header("location:../peminjaman.php");
  } else {
    // echo "<script type='text/javascript'>
    //         window.location.replace('http://localhost/SistemPerpustakaan/peminjaman.php');
    //       </script>";
    header("location:../peminjaman.php");
  }

}

//hapus semua detil
if(isset($_POST['hapusSemua'])) {
  $result = mysqli_query($koneksi, "DELETE FROM detil_pinjam WHERE no_peminjaman is null");
  if($result){
    // echo "<script type='text/javascript'>
    //         window.location.replace('http://localhost/SistemPerpustakaan/peminjaman.php');
    //       </script>";
    header("location:../peminjaman.php");
  }

}

//insert
if(isset($_POST['submit'])) {
  $no_anggota = $_POST['no_anggota'];
  $no_peminjaman = $_POST['no_peminjaman'];
  $tgl_pinjam = $_POST['tgl_pinjam'];
  $tglhrs_kbl = $_POST['tglhrs_kbl'];

  $result = mysqli_query($koneksi, "SELECT COUNT(*) as baris FROM detil_pinjam WHERE no_peminjaman is null");
  $baris = mysqli_fetch_assoc($result);

  $date = date('Y-m-d');
  if($tgl_pinjam < $date){
    echo $s = "<script type='text/javascript'>
            alert ('Tanggal Tidak Valid !');
            window.location.replace('http://localhost/SistemPerpustakaan/peminjaman.php');
          </script>";
  return $s;
  }

  $result = mysqli_query($koneksi, "SELECT no_anggota FROM tb_anggota WHERE no_anggota = '$no_anggota'");
  $anggota = mysqli_fetch_assoc($result);

  if($anggota['no_anggota'] == null){
    echo $s = "<script type='text/javascript'>
            alert ('Data Tidak Ditemukan !');
            window.location.replace('http://localhost/SistemPerpustakaan/peminjaman.php');
          </script>";
  return $s;
  }

  //cek anggota yang minjem lagi
  $result = mysqli_query($koneksi, "SELECT tb_anggota.no_anggota FROM tb_peminjaman,tb_anggota,detil_pinjam,tb_copybuku WHERE tb_peminjaman.no_anggota = tb_anggota.no_anggota AND detil_pinjam.no_peminjaman = tb_peminjaman.no_peminjaman AND tb_copybuku.no_copy = detil_pinjam.no_copy AND (STATUS = '1'  OR STATUS = '2') AND tgl_kbl is null AND tb_anggota.no_anggota = '$no_anggota'");
  $anggotaT = mysqli_fetch_assoc($result);

  if($anggotaT['no_anggota'] != null){
    echo $s = "<script type='text/javascript'>
            alert ('Anggota Sudah Meminjam Buku !');
            window.location.replace('http://localhost/SistemPerpustakaan/peminjaman.php');
          </script>";
  return $s;
  }

  $result = mysqli_query($koneksi, "SELECT * FROM detil_pinjam WHERE no_peminjaman is null");

  $result5 = mysqli_query($koneksi, "INSERT INTO tb_peminjaman(no_peminjaman,no_anggota,tgl_pinjam,tglhrs_kbl) VALUES('$no_peminjaman','$no_anggota','$tgl_pinjam','$tglhrs_kbl')");

  $tampung = array();
  while($row = mysqli_fetch_array($result)){
    $tampung[] = $row['no_copy'];
  }

  for($i=0; $i<$baris['baris']; $i++){
    $tes = $tampung[$i]; 
    $update = mysqli_query($koneksi, "UPDATE detil_pinjam set no_peminjaman = '$no_peminjaman' WHERE no_copy = '$tes' and no_peminjaman is null"); 
    $update = mysqli_query($koneksi, "UPDATE tb_copybuku set status = '1' WHERE no_copy = '$tes' "); 
  }
    
  if($result5 && $update){
    // echo "<script type='text/javascript'>
    //         alert ('Data Berhasil Disimpan !');
    //         window.location.replace('http://localhost/SistemPerpustakaan/peminjaman.php');
    //       </script>";
    header("location:../peminjaman.php"); 
  } else {
    // echo "<script type='text/javascript'>
    //         alert ('Data Gagal Disimpan !');
    //         window.location.replace('http://localhost/SistemPerpustakaan/peminjaman.php');
    //       </script>";
    header("location:../peminjaman.php");
  }
}

//update
if(isset($_POST['update'])) {
  $no_buku = $_POST['no_buku'];
  $copy_buku = $_POST['no_copy'];
  
  $result = mysqli_query($koneksi, "UPDATE tb_copybuku SET no_buku = '$no_buku' WHERE no_copy = $copy_buku");
    
  if($result){
  // echo "<script type='text/javascript'>
  //           alert ('Data Berhasil Disimpan !');
  //           window.location.replace('http://localhost/SistemPerpustakaan/peminjaman.php');
  //         </script>"; 
    header("location:../peminjaman.php");
  } else {
    // echo "<script type='text/javascript'>
    //         alert ('Data Gagal Disimpan !');
    //         window.location.replace('http://localhost/SistemPerpustakaan/peminjaman.php');
    //       </script>";
    header("location:../peminjaman.php");
  }
}