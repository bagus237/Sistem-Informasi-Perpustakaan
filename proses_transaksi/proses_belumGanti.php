<?php

include_once("../Database/koneksi.php");

//insert tb_ganti
if(isset($_POST['gantiBuku'])) {

  $no_peminjaman = $_POST['no_peminjaman'];
  $tanggal = date('Y-m-d');

  $result4 = mysqli_query($koneksi, "SELECT tb_anggota.no_anggota FROM detil_pinjam,tb_anggota,tb_peminjaman,tb_copybuku,tb_buku WHERE detil_pinjam.no_copy = tb_copybuku.no_copy and tb_buku.no_buku = tb_copybuku.no_buku and tb_peminjaman.no_peminjaman = detil_pinjam.no_peminjaman and tb_peminjaman.no_anggota = tb_anggota.no_anggota and status = '2' AND tb_peminjaman.no_peminjaman = '$no_peminjaman' order by detil_pinjam.no_peminjaman asc");

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

  $ket = "ket";
  $keterangan = array();
  for($i=1; $i<=$baris; $i++){
      $keterangan[] = $_POST[$ket.$i];
  }

  $q = 0;
  $m = 0;
  $n = 0;
  for($i=1; $i<=$baris; $i++){
    $copy = $no_copy[$q++];
    $jmKbml = $jml_kembali[$m++];
    $keterangans = $keterangan[$n++];
    $result2 = mysqli_query($koneksi, "SELECT jml_pinjam FROM detil_pinjam WHERE no_copy = '$copy' AND no_peminjaman = '$no_peminjaman'");

    //autonumber
    $cariindex = mysqli_query($koneksi,"select max(no_ganti) from tb_ganti") or die(mysqli_error());
    $dataindex = mysqli_fetch_array($cariindex);
    if($dataindex){
        $nilaiindex = substr($dataindex[0],3);
        $index = (int) $nilaiindex;
        $index = $index + 1;
        $hasilindex = "701".str_pad($index,4,"0",STR_PAD_LEFT);

    } else {
        $hasilindex = "7010001";
    }

    $jumlah = mysqli_fetch_assoc($result2);
    $total = $jumlah['jml_pinjam']+$jmKbml;
    $result = mysqli_query($koneksi, "INSERT INTO tb_ganti(no_ganti,tgl_ganti,no_copy,keterangan) VALUES('$hasilindex','$tanggal','$copy','$keterangans')");

    $result5 = mysqli_query($koneksi, "UPDATE detil_pinjam SET jml_pinjam = '$total' WHERE no_copy = '$copy' AND no_peminjaman = '$no_peminjaman'");

    $result9 = mysqli_query($koneksi, "UPDATE detil_pinjam SET tgl_kbl = '$tanggal' WHERE no_copy = '$copy' AND no_peminjaman = '$no_peminjaman'");

    $result6 = mysqli_query($koneksi, "UPDATE tb_copybuku SET status = '0' WHERE no_copy = '$copy'");
    header("location:../belumGanti.php");

  }


}




//insert
if(isset($_POST['ganti'])) {
  $no_ganti = $_POST['no_ganti'];
  $no_hilang = $_POST['no_hilang'];
  $tgl_ganti = $_POST['tgl_ganti'];
  $no_hilang = $_POST['no_hilang'];
  $no_pengembalian = $_POST['no_pengembalian'];
  $no_peminjaman = $_POST['no_peminjaman'];

  $date = date('Y-m-d');
  if($tgl_ganti < $date){
    echo $s = "<script type='text/javascript'>
            alert ('Tanggal Tidak Valid !');
            window.location.replace('http://localhost/SistemPerpustakaan/belumGanti.php');
          </script>";
    return $s;
  }

  
  $result3 = mysqli_query($koneksi, "SELECT * FROM detil_kembali,tb_pengembalian,tb_peminjaman,tb_hilang WHERE detil_kembali.no_pengembalian = tb_pengembalian.no_pengembalian AND tb_peminjaman.no_peminjaman = tb_pengembalian.no_peminjaman AND tb_hilang.no_peminjaman = tb_peminjaman.no_peminjaman AND tb_hilang.no_hilang = '$no_hilang' and ket != '' ");

  $result4 = mysqli_query($koneksi, "SELECT jml_hilang FROM detil_hilang WHERE no_hilang = '$no_hilang'");
  $baris = mysqli_num_rows($result4);

  $result7 = mysqli_query($koneksi, "SELECT * FROM tb_pengembalian,tb_peminjaman,tb_hilang,detil_kembali WHERE tb_peminjaman.no_peminjaman = tb_pengembalian.no_peminjaman AND tb_hilang.no_peminjaman = tb_peminjaman.no_peminjaman AND detil_kembali.no_pengembalian = tb_pengembalian.no_pengembalian AND tb_hilang.no_hilang = '$no_hilang' and ket != '' ");

  $tampung = array();
  while ($jml = mysqli_fetch_assoc($result3)) {
    $tampung[] = $jml['jml_kembali'];
  }

  $tampung2 = array();
  while ($jml_hlg = mysqli_fetch_assoc($result4)) {
    $tampung2[] = $jml_hlg['jml_hilang'];
  }

  $array = array();
    while ($data6 = mysqli_fetch_array($result7)){
      $array[] = $data6['no_copy'];
  }


  $m=0;
  $q=0;
  $a=0;
  for($i=0; $i<$baris; $i++){
    $nocop = $array[$a++];
    $data = $tampung2[$m++]+$tampung[$q++];
    $update = mysqli_query($koneksi, "UPDATE detil_kembali SET jml_kembali = '$data' WHERE no_pengembalian = '$no_pengembalian' AND no_copy = '$nocop'");
  }  

  $result = mysqli_query($koneksi, "INSERT INTO tb_gantibuku(no_ganti,no_hilang,tgl_ganti) VALUES('$no_ganti','$no_hilang','$tgl_ganti')");

  $status = '1';
  $result4 = mysqli_query($koneksi, "UPDATE tb_hilang SET status = '$status' WHERE no_hilang = '$no_hilang'");
  $result4 = mysqli_query($koneksi, "UPDATE tb_peminjaman SET status = '$status' WHERE no_peminjaman = '$no_peminjaman'");

  if($result){
    // echo "<script type='text/javascript'>
    //         alert ('Data Berhasil Disimpan !');
    //         window.location.replace('http://localhost/SistemPerpustakaan/belumGanti.php');
    //       </script>";
    header("location:../belumGanti.php"); 
  } else {
    // echo "<script type='text/javascript'>
    //         alert ('Data Gagal Disimpan !');
    //         window.location.replace('http://localhost/SistemPerpustakaan/belumGanti.php');
    //       </script>";
    header("location:../belumGanti.php");
  }
}