<?php 
include 'template/Header.php'; 
include 'template/Sidebar.php';
include_once("Database/koneksi.php");
?>

<?php

//autonumber
// $cariindex = mysqli_query($koneksi,"select max(no_pengembalian) from tb_pengembalian") or die(mysqli_error());
// $dataindex = mysqli_fetch_array($cariindex);
// if($dataindex){
//     $nilaiindex = substr($dataindex[0],3);
//     $index = (int) $nilaiindex;
//     $index = $index + 1;
//     $hasilindex = "401".str_pad($index,4,"0",STR_PAD_LEFT);

// } else {
//     $hasilindex = "4010001";}

?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      <small><b>Halaman Data Pengembalian</b></small>
    </h1>
  </section>
<section class="content">

<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <label>Daftar Peminjaman</label>
      </div>
      <div class="panel-body">
        <table style="table-layout:fixed" class="table table-striped table-bordered table-hover" id="anggota">
          <thead>
            <tr>
              <th align="center" width="20px">No. </th>
              <th align="center"><center>Nomor Peminjaman</center></th>
              <th align="center"><center>Nomor anggota</center></th>
              <th align="center"><center>Nama Peminjam</center> </th>
              <th align="center"><center>Tanggal Pinjam</center> </th>
              <th align="center"><center>Detail</center></th>
              <th align="center"><center>Proses</center></th>
              <!-- <th align="center"><center>Hilang</center></th> -->
            </tr>
          </thead>
          <tbody>
            <?php $no=1; ?>
            <?php $result = mysqli_query($koneksi, "SELECT * FROM tb_peminjaman,tb_anggota,detil_pinjam,tb_copybuku WHERE STATUS = '1' AND tgl_kbl is null AND detil_pinjam.no_copy = tb_copybuku.no_copy AND tb_peminjaman.no_peminjaman = detil_pinjam.no_peminjaman AND tb_anggota.no_anggota = tb_peminjaman.no_anggota GROUP BY tb_peminjaman.no_peminjaman order by tb_peminjaman.no_peminjaman desc"); ?>
            <?php while($data2 = mysqli_fetch_array($result)) { ?>
            <tr>
              <td><center><?php echo $no++ ?></center></td>
              <td><center><?php echo $data2['no_peminjaman'] ?></center></td>
              <td><center><?php echo $data2['no_anggota'] ?></center></td>
              <td><center><?php echo $data2['nama_anggota'] ?></center></td>
              <td><center><?php echo $data2['tgl_pinjam'] ?></center></td>
              <td><center><a data-toggle="modal" href="#detail<?php echo $data2['no_peminjaman'] ?>" class="btn btn-primary"><i class="fa fa-folder-open"></i></a></center></td>
              
              <td><center><a data-toggle="modal" href="#proses<?php echo $data2['no_peminjaman'] ?>" class="btn btn-success"><i class="fa fa-check"></i></a></center></td>

              <!-- <td><center><a href="detil_kembali.php?id=<?php echo $data2['no_peminjaman'] ?>" class="btn btn-success"><i class="fa fa-check"></i></a></center></td> -->
              
              <!-- <td><center><a href="#hilang<?php echo $data2['no_peminjaman'] ?>" data-toggle="modal" class="btn btn-danger"><i class="fa fa-warning "></i></a></center></td> -->
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
     </div>
    </div>
  </div>
</section>
</div>

<?php $result2 = mysqli_query($koneksi, "SELECT * FROM tb_peminjaman order by no_peminjaman desc"); ?>
<?php while($data3 = mysqli_fetch_array($result2)) { ?>
<div class="modal fade" id="detail<?php echo $data3['no_peminjaman'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Nomor Peminjaman <b><?php echo $data3['no_peminjaman'] ?></b></h4>
      </div>
      <div class="modal-body">
         <table style="table-layout:fixed" class="table table-striped table-bordered table-hover" id="anggota">
          <thead>
            <tr>
              <th align="center" width="20px">No. </th>
              <th align="center"><center>Nomor Copy Buku</center></th>
              <th align="center"><center>Judul Buku</center></th>
              <th align="center"><center>Tanggal Pinjam</center> </th>
              <th align="center"><center>Jumlah Pinjam</center></th>
            </tr>
          </thead>
          <tbody>
            <?php $no=1; ?>
            <?php $z = $data3['no_peminjaman'] ?>
            <?php $result4 = mysqli_query($koneksi, "SELECT * FROM detil_pinjam,tb_anggota,tb_peminjaman,tb_copybuku,tb_buku WHERE detil_pinjam.no_copy = tb_copybuku.no_copy and tb_buku.no_buku = tb_copybuku.no_buku and tb_peminjaman.no_peminjaman = detil_pinjam.no_peminjaman and tb_peminjaman.no_anggota = tb_anggota.no_anggota and tb_peminjaman.no_peminjaman = '$z' order by detil_pinjam.no_peminjaman asc"); ?>
            <?php while($data2 = mysqli_fetch_array($result4)) { ?>
            <tr>
              <td><center><?php echo $no++ ?></center></td>
              <td><center><?php echo $data2['no_copy'] ?></center></td>
              <td><center><?php echo $data2['judul_buku'] ?></center></td>
              <td><center><?php echo $data2['tgl_pinjam'] ?></center></td>
              <td><center><?php echo $data2['jml_pinjam'] ?></center></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
<?php } ?>

<!-- proses -->
<?php $result2 = mysqli_query($koneksi, "SELECT * FROM tb_peminjaman,tb_anggota,detil_pinjam,tb_copybuku WHERE STATUS = '1' AND detil_pinjam.no_copy = tb_copybuku.no_copy AND tb_peminjaman.no_peminjaman = detil_pinjam.no_peminjaman AND tb_anggota.no_anggota = tb_peminjaman.no_anggota GROUP BY tb_peminjaman.no_peminjaman order by tb_peminjaman.no_peminjaman desc"); ?>
<?php while($data3 = mysqli_fetch_array($result2)) { ?>
<div class="modal fade" id="proses<?php echo $data3['no_peminjaman'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Nomor Peminjaman <b><?php echo $data3['no_peminjaman'] ?></b></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="proses_transaksi/proses_pengembalian.php">
         <table style="table-layout:fixed" class="table table-striped table-bordered table-hover" id="anggota">
          <thead>
            <tr>
              <th align="center" width="20px">No. </th>
              <th align="center"><center>Nomor Copy Buku</center></th>
              <th align="center"><center>Judul Buku</center></th>
              <th align="center"><center>Tanggal Pinjam</center> </th>
              <th align="center"><center>Jumlah Kembali</center></th>
            </tr>
          </thead>
          <tbody>
            <?php $no=1; ?>
            <?php $no_cp=1; ?>
            <?php $jml_kem=1; ?>
            <?php $z = $data3['no_peminjaman'] ?>
            <?php $result4 = mysqli_query($koneksi, "SELECT * FROM detil_pinjam,tb_anggota,tb_peminjaman,tb_copybuku,tb_buku WHERE detil_pinjam.no_copy = tb_copybuku.no_copy and tb_buku.no_buku = tb_copybuku.no_buku and tb_peminjaman.no_peminjaman = detil_pinjam.no_peminjaman and tb_peminjaman.no_anggota = tb_anggota.no_anggota and tb_peminjaman.no_peminjaman = '$z' order by detil_pinjam.no_peminjaman asc"); ?>
            <?php while($data2 = mysqli_fetch_array($result4)) { ?>
            <tr>
              <td><center><?php echo $no++ ?></center></td>
              <td><center><?php echo $data2['no_copy'] ?></center></td>
              <td><center><?php echo $data2['judul_buku'] ?></center></td>
              <td><center><?php echo $data2['tgl_pinjam'] ?></center></td>
              <td><center>
                <input type="number" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" min="1" value="<?php echo $data2['jml_pinjam'] ?>" class="form-control"  name="jml_kembali<?php echo $jml_kem++ ?>">
                </center></td>
                <input type="hidden" name="no_copy<?php echo $no_cp++ ?>" value="<?php echo $data2['no_copy'] ?>">
                <input type="hidden" name="no_peminjaman" value="<?php echo $z ?>">
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Batal</button>
        <button type="submit" name="kembali" class="btn btn-success"><i class="fa fa-save"></i> Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
<?php } ?>


<?php $result2 = mysqli_query($koneksi, "SELECT * FROM tb_peminjaman order by no_peminjaman desc"); ?>
<?php while($data3 = mysqli_fetch_array($result2)) { ?>
<div class="modal fade" id="hilang<?php echo $data3['no_peminjaman'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Input Data Hilang</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="detil_hilang.php?id=<?php echo $data3['no_peminjaman'] ?>">
        <table style="table-layout:fixed" class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th align="center" width="50px">No. </th>
              <th align="center"><center>Nomor Copy</center></th>
              <th align="center"><center>Judul Buku </center></th>
              <th align="center"><center>Pengarang</center> </th>
              <th align="center"><center>Tanggal Pinjam</center> </th>
              <th align="center"><center>Jumlah Pinjam</center> </th>
              <th align="center" width="50px"><center>Pilih</center> </th>
            </tr>
          </thead>
          <tbody>
            <?php $keter=1; ?>
            <?php $nocop=1; ?>
            <?php $no=1; ?>
            <?php $r = $data3['no_peminjaman']; ?>
            <?php $result = mysqli_query($koneksi, "SELECT * FROM tb_copybuku a,tb_buku b,detil_pinjam c,tb_peminjaman d WHERE a.no_buku = b.no_buku and a.no_copy = c.no_copy and c.no_peminjaman = d.no_peminjaman and d.no_peminjaman = '$r'"); ?>
            <?php while($data2 = mysqli_fetch_array($result)) { ?>
            <tr>
              <td><center><?php echo $no++ ?></center></td>
              <td><center><?php echo $data2['no_copy'] ?></center></td>
              <td><center><?php echo $data2['judul_buku'] ?></center></td>
              <td><center><?php echo $data2['pengarang'] ?></center></td>
              <td><center><?php echo $data2['tgl_pinjam'] ?></center></td>
              <td><center><?php echo $data2['jml_pinjam'] ?></center></td>
              <td><center><input type="checkbox" name="pilih<?php echo $keter++; ?>" value="<?php echo $data2['no_copy'] ?>"></center></td>
              <input type="hidden" name="no_peminjaman" value="<?php echo $data2['no_peminjaman'] ?>">
            </tr>
            <?php } ?>
          </tbody>
        </table>

      </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Batal</button>
          <button type="submit" name="hilang" class="btn btn-success"><i class="fa fa-save"></i> Submit</button>
      </div>
      </form>
      </div>
    </div>
  </div>
</div>
<?php } ?>


<?php include "template/Footer.php"; ?>