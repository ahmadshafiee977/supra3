<?php
include('header.php');
$query = "SELECT ad.*, ae.no_pendaftaran, jk.kerosakan,p.nama, pm.*, l.kritikal, pg.nama AS penugas FROM aduan ad 
                LEFT JOIN aset ae ON ae.id = ad.id_aset
                LEFT JOIN jenis_kerosakan jk ON jk.id = ad.id_jenis_kerosakan
                LEFT JOIN pengguna p ON p.id = ad.id_pengadu
                LEFT JOIN perincian_masalah pm ON pm.id = ad.id_perincian_masalah 
                LEFT JOIN lokasi l ON l.nama_lokasi = pm.lokasi_pengadu
                LEFT JOIN pengguna pg ON pg.id = ad.ditugaskan_kepada
                WHERE ad.status <> 'SELESAI'
                ORDER BY tarikh_dihantar DESC";
$result = mysqli_query($con, $query);

$aset_query = "SELECT * FROM aset WHERE id_jabatan = $jabatan_id";
$aset_result = mysqli_query($con, $aset_query);

$jeke_query = "SELECT * FROM jenis_kerosakan WHERE status = 'AKTIF'";
$jeke_result = mysqli_query($con, $jeke_query);
?>
<div class="right_col">
    <div class="x_panel">
        <div class="x_title">
            <h2>ADUAN BELUM SELESAI</h2>
        </div>
        <div class="x_content">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <table class="table table-striped table-bordered" id="datatable-fixed-header">
                    <thead>
                        <th>No.</th>
                        <th>No Pendaftaran Asset</th>
                        <th>Jenis kerosakan</th>
                        <th>Perincian Masalah</th>
                        <th>Pengadu</th>
                        <th>Penugas</th>
                        <th>Status</th>
                        <th>Tarikh Dihantar</th>
                        <th>Aksi</th>
                    </thead>
                    <tbody>
                        <?php
                        $serialnumber = 1;
                        $today = new DateTime();
                        while ($row = mysqli_fetch_assoc($result)) {
                            $row_class = '';
                            $badge = '';
                            switch ($row['status']) {
                                case 'BARU':
                                case 'DITUGASKAN':
                                    $row_class = 'text-info';
                                    $badge = 'baru';
                                    break;
                                case 'DALAM PROSES':
                                    $row_class = 'text-warning';
                                    $badge = 'proses';
                                    break;
                                case 'SELESAI':
                                    $row_class = 'text-success';
                                    $badge = 'selesai';
                                    break;
                            }
                            $sdate = new DateTime($row['tarikh_dihantar']);
                            $diff = $today->diff($sdate);
                            if ($diff->days > 1 && $row['status'] != 'SELESAI') {
                                $row_class = 'text-danger';
                            }
                        ?>
                            <tr class="<?= $row_class ?>" style="font-weight: 550;">
                                <td><?= $serialnumber++ ?></td>
                                <td><?= $row['no_pendaftaran']; ?></td>
                                <td><?= $row['kerosakan']; ?></td>
                                <td><?= $row['masalah']; ?></td>
                                <td><?= getShortName($row['nama']); ?> </td>
                                <td><?= getShortName($row['penugas']) ?> </td>
                                <td>
                                    <?php
                                    if ($row['status'] == 'DITUGASKAN') {
                                        $status = in_array($user_level, ['ADMIN', 'SUPERADMIN']) ? $row['status'] : 'BARU';
                                    } else {
                                        $status = $row['status'];
                                    }
                                    ?>

                                    <span class="badge badge-<?= $badge ?>"><?= $status ?></span>
                                </td>
                                <td><?= $row['tarikh_dihantar']; ?></td>
                                <td>
                                    <?php if ($row['status'] == 'BARU' && $user_level == 'SUPERADMIN'): ?>
                                        <a class="btn btn-primary btn-sm" href="maklumat_aduan.php?id=<?= $row['id'] ?>" title="Tugaskan Aduan"><i class="bi bi-person-add"></i></a>
                                    <?php elseif (in_array($row['status'], ['DITUGASKAN', 'DALAM PROSES']) && in_array($user_level, ['ADMIN', 'SUPERADMIN'])): ?>
                                        <a href="maklumat_aduan.php?id=<?= $row['id']; ?>" class="btn btn-info btn-sm" title="Maklumat Aduan"><i class="bi bi-eye"></i></a>
                                    <?php elseif ($row['status'] == 'SELESAI'): ?>
                                        <a href="cetak_aduan.php?id=<?= $row['id']; ?>" class="btn btn-dark btn-sm" title="Cetak Aduan"><i class="bi bi-printer-fill"></i></a>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
include('footer.php');
?>