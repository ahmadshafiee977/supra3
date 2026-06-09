<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'icths85000@gmail.com';
$mail->Password = 'mlcg pryb useh mgem';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
$mail->setFrom('ahmadshafiee977@gmail.com', 'Demo');
$mail->setFrom('icths85000@gmail.com', 'ICT E-Aduan');
$mail->addAddress('icthsgmt@moh.gov.my');
$mail->addAddress('ahmadshafiee977@gmail.com');
$mail->Subject = 'Test Email 2|| Live Server ||' . date('d-m-Y H:i');
$mail->isHTML(true);
include('header.php');

$whereClause = '';
if (in_array($user_level, ['BIASA', 'PENYELARAS'])) {
    $whereClause = 'WHERE id_pengadu = ' . $user_id;
}

$query = "SELECT ad.*, ae.no_pendaftaran, jk.kerosakan,p.nama, pm.*, l.kritikal, pg.nama AS penugas FROM aduan ad 
                LEFT JOIN aset ae ON ae.id = ad.id_aset
                LEFT JOIN jenis_kerosakan jk ON jk.id = ad.id_jenis_kerosakan
                LEFT JOIN pengguna p ON p.id = ad.id_pengadu
                LEFT JOIN perincian_masalah pm ON pm.id = ad.id_perincian_masalah 
                LEFT JOIN lokasi l ON l.nama_lokasi = pm.lokasi_pengadu
                LEFT JOIN pengguna pg ON pg.id = ad.ditugaskan_kepada
                $whereClause
                ORDER BY tarikh_dihantar DESC";
$result = mysqli_query($con, $query);

$aset_query = "SELECT * FROM aset WHERE id_jabatan = $jabatan_id";
$aset_result = mysqli_query($con, $aset_query);

$jeke_query = "SELECT * FROM jenis_kerosakan WHERE status = 'AKTIF'";
$jeke_result = mysqli_query($con, $jeke_query);

$lokasi_query = "SELECT * FROM lokasi WHERE status = '1'";
$lokasi_result = mysqli_query($con, $lokasi_query);

$today = date('Y-m-d');
$cuti_query = 'SELECT id FROM cuti WHERE tarikh_mula >= "' . $today . '" AND tarikh_akhir <= "' . $today . '" ';
$cuti_result = mysqli_query($con, $cuti_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hantar-aduan'])) {
    $id_npa = $_POST['no-pendaftaran-aset'];
    $id_jks = $_POST['jenis-kerosakan'];
    $pm = $_POST['perincian-masalah'];
    $lokasi = $_POST['lokasi'];
    $status = 'BARU';

    $con->begin_transaction();
    try {
        $masalah_query = "INSERT INTO perincian_masalah (masalah, jawatan_pengadu,jabatan_pengadu,lokasi_pengadu) VALUES (?,?,?,?)";
        $masalah_stmt = $con->prepare($masalah_query);
        $masalah_stmt->bind_param('ssss', $pm, $jawatan_pengguna, $jabatan_pengguna, $lokasi);
        $masalah_stmt->execute();
        $masalah_id = $masalah_stmt->insert_id;
        $now = new DateTime();
        $start = new DateTime('17:00');
        $end = new DateTime('08:00');
        $weekend = ($now->format('N') >= 6);
        $mail_jeke = [];
        foreach ($id_jks as $id_jk) {
            $insert_query = "INSERT INTO aduan (id_aset, id_jenis_kerosakan, id_perincian_masalah,id_pengadu,status) VALUES (?,?,?,?,?)";
            $insert_stmt = $con->prepare($insert_query);

            $mail_jeke_query = "SELECT kerosakan FROM jenis_kerosakan WHERE id = ?";
            $mail_jeke_stmt = $con->prepare($mail_jeke_query);
            $mail_jeke_stmt->bind_param('i', $id_jk);
            $mail_jeke_stmt->execute();
            $mail_jeke_result =  $mail_jeke_stmt->get_result();
            while ($mailJEKE = $mail_jeke_result->fetch_assoc()) {
                $mail_jeke[] = $mailJEKE['kerosakan'];
            }
            //$insert_stmt->bind_param("iisiis", $npa, $jk, $pm, $uid, $ji, $status);
            $insert_stmt->bind_param("iiiis", $id_npa, $id_jk, $masalah_id,  $user_id, $status);
            if (!$insert_stmt->execute()) {
                throw new Exception('Gagal memasukkan aduan' . $insert_stmt->error);
            }
            $latestId = $insert_stmt->insert_id;
            $latest_query =
                "SELECT ad.*, ae.no_pendaftaran, jk.kerosakan, pm.*,  pg.nama, pg.no_telefon, l.kritikal FROM aduan ad 
                LEFT JOIN perincian_masalah pm ON ad.id_perincian_masalah = pm.id
                LEFT JOIN aset ae ON ae.id = ad.id_aset
                LEFT JOIN jenis_kerosakan jk ON jk.id = ad.id_jenis_kerosakan
                LEFT JOIN lokasi l ON l.nama_lokasi = pm.lokasi_pengadu 
                LEFT JOIN pengguna pg ON pg.id = ad.id_pengadu
                WHERE ad.id = $latestId";
            $latest_result = mysqli_query($con, $latest_query);
            $data = mysqli_fetch_assoc($latest_result);

            $con->commit();
            $ch = curl_init("https://hsgmt23:3000/emit-complaint");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_exec($ch);
            curl_close($ch);
        }
        //email start
        if (mysqli_num_rows($cuti_result) > 0 || ($now >= $start || $now < $end) || $weekend) {

            $mail->Body = ' <table style="width:100%;height:auto;background-color:#edece8;">
                            <tr>
                                <td align="center">
                                    <table style="background:white;padding:5px;">
                                        <tr>
                                            <td align="center">
                                                <table style="width:300px;height:100%;background:white;">
                                                    <tr>
                                                        <td align="center">
                                                            <table style="background:' . ($data['kritikal'] == 0 ? '#0376c1' : '#e80202') . ' ;color:white;border-radius:10px 10px 0 0;width:100%;">
                                                                <tr>
                                                                    <td align="left" style="font-size:18px;padding-left:10px;">
                                                                        E-Aduan
                                                                    </td>
                                                                    <td align="right" style="font-size:12px;padding-right:10px;">
                                                                        ' . date('d-m-Y H:i') . '
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <br>
                                                            <table style="width:280px;border:1px solid #e0dce4;border-radius:10px;padding:10px;color:black;">
                                                                <tr>
                                                                    <td style="font-size:18px;">' . $data['nama'] . '</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-size:18px;">' . $data['lokasi_pengadu'] . ' / ' . $data['jabatan_pengadu'] . '</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-size:15px;">' . $data['no_telefon'] . '</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-size:15px;">' . implode(", ", $mail_jeke) . '</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-size:15px;">' . $data['masalah'] . '</td>
                                                                </tr>
                                                            </table>
                                                            <table>
                                                                <tr>
                                                                    <td style="font-size:10px;" align="center">
                                                                        COPYRIGHT © 2025 ICT Hospital Segamat
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    ';


            $mail->AltBody = 'Aduan Baru';
            $mail->send();
        }
        //email end
        echo '<script>alert("Aduan anda telah diterima dan akan diambil tindakan padu waktu bekerja oleh pegawai bertugas.");window.location.assign("aduan_dashboard.php");</script>;';
    } catch (Exception $e) {
        $con->rollback();
        echo '<script>alert("Aduan gagal dihantar: ' . $e->getMessage() . '");</script>';
    }
}
?>
<div class="right_col">
    <div class="x_panel">
        <div class="x_title">
            <h2>SENARAI ADUAN</h2>
            <div class="clearfix"></div>
        </div>
        <br>
        <button class="btn btn-primary" data-toggle="modal" data-target="#borang-aduan">Aduan Baru</button>
        <a class="btn btn-success" href="testphp.php">phpinfo</a>
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

                            if ($row['kritikal'] == '1' && $row['status'] == 'BARU') {
                                $row_kritikal = 'kritikal';
                            } else {
                                $row_kritikal = '';
                            }
                        ?>
                            <tr class="<?= $row_class ?> <?= $row_kritikal ?>" style="font-weight: 550;">
                                <td><?= $serialnumber++ ?></td>
                                <td><?= $row['no_pendaftaran']; ?></td>
                                <td><?= $row['kerosakan']; ?></td>
                                <td><?= $row['masalah']; ?></td>
                                <td><?= getShortName($row['nama']); ?> </td>
                                <td><?= getShortName($row['penugas']) ?></td>
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
<div class="modal fade" id="borang-aduan">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header">
                    <button type="button" data-dismiss="modal" class="close modal-close">&times;</button>
                    <h4 class="modal-title">Borang Aduan</h4>
                </div>
                <div class="modal-body">
                    <div class="x_panel">
                        <div class="col-md-12 col-sm-12">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <label for="lokasi">Lokasi</label>
                                    <select name="lokasi" id="lokasi" class="form-control" required>
                                        <option value="" selected disabled>--Pilih lokasi--</option>
                                        <?php while ($lokasi = mysqli_fetch_assoc($lokasi_result)) { ?>
                                            <option value="<?= $lokasi['nama_lokasi'] ?>"><?= $lokasi['nama_lokasi'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <label for="no-pendaftaran-aset">No Pendaftaran Aset</label>
                                    <select name="no-pendaftaran-aset" id="no-pendaftaran-aset" class="form-control" required>
                                        <?php
                                        while ($aset = mysqli_fetch_assoc($aset_result)) {
                                        ?>
                                            <option value="<?= $aset['id'] ?>"><?= $aset['no_pendaftaran'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <label for="jenis-kerosakan">Jenis Kerosakan</label>
                                    <select name="jenis-kerosakan[]" id="jenis-kerosakan" class="form-control" multiple required>
                                        <?php while ($jeke = mysqli_fetch_assoc($jeke_result)) { ?>
                                            <option value="<?= $jeke['id'] ?>"><?= $jeke['kerosakan']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <label for="perincian-masalah">Perincian Masalah</label>
                                    <textarea name="perincian-masalah" id="perincian-masalah" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
                <br><br>
                <div class="modal-footer">
                    <button type="submit" id="hantar-aduan" name="hantar-aduan" class="btn btn-success">Hantar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<audio src="../sound/loud_notification.mp3" id="notifSound"></audio>
<script>
    $('select.form-control').select2({
        width: '100%'
    })
    $('select[multiple].form-control').select2({
        allowClear: true,
        width: '100%'
    })
</script>
<?php
include('footer.php');
