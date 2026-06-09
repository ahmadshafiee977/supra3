<?php
require 'db.php';
function getShortName($name)
{
    //$separators = [' BIN ', ' bin ', ' BINTI ', ' binti ', ' A/L ', ' a/l ', ' A/P ', ' a/p '];
    $pattern = '/\b(bin|binti|a\/l|a\/p)\b/i';

    $parts = preg_split($pattern, $name, 2);
    return $parts[0];
}
$query = "SELECT ad.*, ae.no_pendaftaran, jk.kerosakan,p.nama, pm.*, l.kritikal, pg.nama AS penugas FROM aduan ad 
                LEFT JOIN aset ae ON ae.id = ad.id_aset
                LEFT JOIN jenis_kerosakan jk ON jk.id = ad.id_jenis_kerosakan
                LEFT JOIN pengguna p ON p.id = ad.id_pengadu
                LEFT JOIN perincian_masalah pm ON pm.id = ad.id_perincian_masalah 
                LEFT JOIN lokasi l ON l.nama_lokasi = pm.lokasi_pengadu
                LEFT JOIN pengguna pg ON pg.id = ad.ditugaskan_kepada
                ORDER BY tarikh_dihantar DESC";
$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV Dashboard</title>
    <link rel="icon" href="../images/ict_logo.png" type="image/png">
    <!-- NProgress -->
    <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- Datatables -->
    <link href="../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- bootstrap-wysiwyg -->
    <link href="../vendors/google-code-prettify/bin/prettify.min.css" rel="stylesheet">
    <!-- select2 css -->
    <link href="../vendors/select2/dist/css/select2.css" rel="stylesheet">
    <!-- Custom styling plus plugins -->

    <link href="../build/css/custom.css" rel="stylesheet">
    <style>
        body {
            margin: 20px;
            overflow: hidden;

        }

        .content {
            min-height: 87vh;
        }
    </style>
</head>

<body>
    <h1>E-Aduan</h1>
    <div class="content">

        <table class="table table-striped table-bordered" id="datatable-fixed-header">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>No Pendaftaran Asset</th>
                    <th>Jenis kerosakan</th>
                    <th>Perincian Masalah</th>
                    <th>Pengadu</th>
                    <th>Penugas</th>
                    <th>Status</th>
                    <th>Tarikh Dihantar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $serial = 1;
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

                    <tr class="<?= $row_class ?>  <?= $row_kritikal ?>" style="font-weight: 550;">
                        <td><?= $serial++ ?></td>
                        <td><?= $row['no_pendaftaran']; ?></td>
                        <td><?= $row['kerosakan']; ?></td>
                        <td><?= $row['masalah']; ?></td>
                        <td><?= getShortName($row['nama']); ?> </td>
                        <td><?= getShortName($row['penugas']) ?></td>
                        <td><span class="badge badge-<?= $badge ?>"><?= $row['status'] ?></span></td>
                        <td><?= $row['tarikh_dihantar']; ?></td>

                    </tr>

                <?php } ?>

            </tbody>
        </table>
    </div>
    <div id="audioModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p>Please close this to enable audio.</p>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <audio src="../sound/doorbell.wav" id="notifSound"></audio>

    <footer>
        <div class="pull-right">
            COPYRIGHT © 2025 ICT Hospital Segamat
        </div>
        <div class="clearfix"></div>
    </footer>
    <!-- jQuery -->
    <script src="../vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="../vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../vendors/nprogress/nprogress.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="../vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="../vendors/iCheck/icheck.min.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="../vendors/moment/min/moment.min.js"></script>
    <script src="../vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap-wysiwyg -->
    <script src="../vendors/bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js"></script>
    <script src="../vendors/jquery.hotkeys/jquery.hotkeys.js"></script>
    <script src="../vendors/google-code-prettify/src/prettify.js"></script>
    <!-- jQuery Tags Input -->
    <script src="../vendors/jquery.tagsinput/src/jquery.tagsinput.js"></script>
    <!-- Switchery -->
    <script src="../vendors/switchery/dist/switchery.min.js"></script>
    <!-- Select2 -->
    <script src="../vendors/select2/dist/js/select2.full.min.js"></script>
    <!-- Parsley -->
    <script src="../vendors/parsleyjs/dist/parsley.min.js"></script>
    <!-- Autosize -->
    <script src="../vendors/autosize/dist/autosize.min.js"></script>
    <!-- jQuery autocomplete -->
    <script src="../vendors/devbridge-autocomplete/dist/jquery.autocomplete.min.js"></script>
    <!-- starrr -->
    <script src="../vendors/starrr/dist/starrr.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../build/js/custom.js"></script>
    <!-- iCheck -->
    <script src="../vendors/iCheck/icheck.min.js"></script>
    <!-- Datatables -->
    <script src="../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="../vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="../vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="../vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script src="../vendors/jszip/dist/jszip.min.js"></script>

    <script src="https://10.77.232.23:3000/socket.io/socket.io.js"></script>

    <script>
        $(document).ready(function() {
            if ($('#datatable-fixed-header').length > 0) {
                $('#datatable-fixed-header').DataTable().destroy();
                $('#datatable-fixed-header').DataTable({
                    language: {
                        search: 'Cari: ',
                        lengthMenu: 'Papar _MENU_ rekod',
                        zeroRecords: 'Tiada rekod dijumpai',
                        info: 'Menunjukkan _START_ hingga _END_ dari _TOTAL_ rekod',
                        infoEmpty: 'Tiada rekod untuk dipaparkan',
                        infoFiltered: '(ditapis daripada _MAX_ jumlah rekod)',
                        paginate: {
                            first: 'Pertama',
                            last: 'Terakhir',
                            next: 'Seterusnya',
                            previous: 'Sebelumnya'
                        }
                    },
                    "pageLength": 10
                });
            }
            $('#audioModal').modal('show');
        })

        let isAlarmPlaying = false;
        const audio = document.getElementById("notifSound");

        function stopAlarm() {
            if (isAlarmPlaying) {
                audio.pause();
                audio.currentTime = 0;
                isAlarmPlaying = false;
            }
            setTimeout(() => {
                $('.new-row').removeClass('new-row');
            }, 3000);
        }

        function renumberTable() {
            const table = document.getElementById("datatable-fixed-header");
            const rows = table.querySelectorAll("tbody tr");
            rows.forEach((row, index) => {
                row.cells[0].textContent = index + 1;
            })
        }

        const socket = io("https://10.77.232.23:3000", {
            auth: {
                token: "TV"
            }
        });
        socket.on("new_complaint", (data) => {
            audio.loop = true;
            audio.play();
            isAlarmPlaying = true;
            setTimeout(() => {
                stopAlarm();
            }, 3000);

            const table = $('#datatable-fixed-header').DataTable();
            const row = table.row.add([
                '0',
                data.no_pendaftaran,
                data.kerosakan,
                data.masalah,
                `${data.nama}`,
                '',
                `<span class="badge badge-baru">${data.status}</span>`,
                data.tarikh_dihantar
            ]).draw(false).node();

            renumberTable();
            requestAnimationFrame(() => {
                row.classList.add("new-row", "text-info");
            })



        })
    </script>
</body>

</html>