<?php
include_once("../Database/koneksi.php");

//insert
if(isset($_POST['submit'])) {
  $no_pengembalian = $_POST['no_pengembalian'];
  $no_peminjaman = $_POST['no_peminjaman'];
  $tgl_kembali = $_POST['tgl_kembali'];
    
  $date = date('Y-m-d');
  if($tgl_kembali < $date){
    echo $s = "<script type='text/javascript'>
            alert ('Tanggal Tidak Valid !');
            window.location.replace('http://localhost/SistemPerpustakaan/pengembalian.php');
          </script>";
    return $s;
  }

  $result = mysqli_query($koneksi, "INSERT INTO tb_pengembalian(no_pengembalian,no_peminjaman,tgl_kembali) VALUES('$no_pengembalian','$no_peminjaman','$tgl_kembali')");

  if($result){
    // echo "<script type='text/javascript'>
    //         alert ('Data Berhasil Disimpan !');
    //         window.location.replace('http://localhost/SistemPerpustakaan/pengembalian.php');
    //       </script>"; 
    header("location:../pengembalian.php.");
  } else {
    // echo "<script type='text/javascript'>
    //         alert ('Data Gagal Disimpan !');
    //         window.location.replace('http://localhost/SistemPerpustakaan/pengembalian.php');
    //       </script>";
    header("location:../pengembalian.php.");
  }
}


//pengembalian
if(isset($_POST['kembali'])) {
  $no_peminjaman = $_POST['no_peminjaman'];
  $tanggal = date('Y-m-d');

  $result4 = mysqli_query($koneksi, "SELECT tb_anggota.no_anggota FROM detil_pinjam,tb_anggota,tb_peminjaman,tb_copybuku,tb_buku WHERE detil_pinjam.no_copy = tb_copybuku.no_copy and tb_buku.no_buku = tb_copybuku.no_buku and tb_peminjaman.no_peminjaman = detil_pinjam.no_peminjaman and tb_peminjaman.no_anggota = tb_anggota.no_anggota and status = '1' AND tb_peminjaman.no_peminjaman = '$no_peminjaman' order by detil_pinjam.no_peminjaman asc");

  $baris = mysqli_num_rows($result4);

  $co_cp = "no_copy";
  $no_copy = array();
  for($i=1; $i<=$baris; $i++){
      $no_copy[] = $_POST[$co_cp.$i];
  }

  $jml_kem = "jml_kembali";
  $jml_kembali = array();
  for($i=1; $i<=$baris; $i++){
      $jml_kembali[] = $_POST[$jml_kem.$i];
  }


  //update stok
  $q = 0;
  $m = 0;
  for($i=1; $i<=$baris; $i++){
    $copy = $no_copy[$q++];
    $jmKbml = $jml_kembali[$m++];
    $result2 = mysqli_query($koneksi, "SELECT jml_pinjam FROM detil_pinjam WHERE no_copy = '$copy' AND no_peminjaman = '$no_peminjaman'");

    $jumlah = mysqli_fetch_assoc($result2);

    if($jumlah['jml_pinjam'] == $jmKbml){
      
      $result5 = mysqli_query($koneksi, "UPDATE detil_pinjam SET tgl_kbl = '$tanggal' WHERE no_copy = '$copy' and no_peminjaman = '$no_peminjaman'");
      $result6 = mysqli_query($koneksi, "UPDATE tb_copybuku SET status = '0' WHERE no_copy = '$copy'");      
      header("location:../pengembalian.php");
    } else {

      // $total = $jumlah['jml_pinjam']-$jmKbml;
      $result5 = mysqli_query($koneksi, "UPDATE detil_pinjam SET jml_pinjam = '$jmKbml' WHERE no_copy = '$copy' and no_peminjaman = '$no_peminjaman'");
      // $result5 = mysqli_query($koneksi, "UPDATE detil_pinjam SET tgl_kbl = '$tanggal' WHERE no_copy = '$copy' and no_peminjaman = '$no_peminjaman'");
      $result3 = mysqli_query($koneksi, "UPDATE tb_copybuku SET status = '2' WHERE no_copy = '$copy'");
      header("location:../pengembalian.php");
    }

    
  }

 
}