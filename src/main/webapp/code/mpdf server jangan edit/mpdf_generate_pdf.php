<?php

require_once __DIR__ . '/../vendor/autoload.php';
require('db.php');

$aid = $_GET['id'];
$query = "SELECT ad.*, pga.nama AS nama_pengadu, pga.no_telefon, jb.nama_jabatan, jw.nama_jawatan AS jawatan_pengadu, ae.no_pendaftaran, jk.kerosakan, pm.masalah, pgp.nama AS nama_petugas, jwp.nama_jawatan AS jawatan_penugas FROM aduan ad
    INNER JOIN pengguna pga ON ad.id_pengadu = pga.id
    INNER JOIN jabatan jb ON jb.id = ad.id_jabatan
    INNER JOIN jawatan jw ON jw.id = ad.id_jawatan
    INNER JOIN aset ae ON ad.id_aset = ae.id
    INNER JOIN jenis_kerosakan jk ON jk.id = ad.id_jenis_kerosakan
    INNER JOIN perincian_masalah pm ON pm.id = ad.id_perincian_masalah
    INNER JOIN pengguna pgp ON ad.ditugaskan_kepada  = pgp.id
    INNER JOIN jawatan jwp ON pgp.id_jawatan = jwp.id
    WHERE ad.id = ?
";
$query_stmt = $con->prepare($query);
$query_stmt->bind_param('i', $aid);
if ($query_stmt->execute()) {
    $aduan_result = $query_stmt->get_result();
    $aduan = $aduan_result->fetch_assoc();
    list($tarikh_aduan, $masa_aduan) = explode(' ', $aduan['tarikh_dihantar']);
    list($tarikh_diterima, $masa_diterima) = explode(' ', $aduan['tarikh_diterima']);
    list($tarikh_selesai, $masa_selesai) = explode(' ', $aduan['tarikh_selesai']);
}

$jeke = [
    'Komputer',
    'Pencetak',
    'Rangkaian/Internet',
    'Antivirus',
    'CCTV',
    'EZ-AttendanceUPA',
    'iPortal Sistem HS',
    'Microsoft Office',
    'Search Engine',
    'Faceprint',
    'GPKI',
    'ePRS',
    'OTIS',
    'LabiS',
    'UPIS',
    'iServe',
    'DBS',
    'iCC',
    'iDOC',
    'iCar',
    'iBooking',
    'eHadir',
    'CNP',
    'iQMS',
    'SMRP',
    'PHIS',
    'MySPA',
    'HRMIS',
    'myCPD',
    'iGFMAS',
    'SPIKPA',
    'ePerolehan',
    'eProfile',
    'eLaporan',
    'IRIS',
    'SeHATI',
    'MPIS'
];

use Mpdf\Mpdf;

$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => [250, 350],
    'tempDir' => __DIR__ . '/../tmp',
    'debug' => true
]);
$html = '
<style>
     
        .pull-right {
            color: black;
            float: right;
        }

        .outshell {
            border: 1px solid black;
            padding: 0 20px 20px 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            overflow:wrap;
        }

        img {
            height: 70px;
        }

        .bt {
            border-top: 1px solid black;
        }

        .br {
            border-right: 1px solid black;
        }

        .bb {
            border-bottom: 1px solid black; 
        }

        .bl {
            border-left: 1px solid black;
        }

        td {
            padding: 4px 8px;
        }

        .labelbox {
            width: 25%;
        }

        .tickbox {
            width: 4%;
        }

        .cop {
            text-decoration: overline dotted black;
            display: flex;
            gap: 1px;
            justify-content: center;
            position: relative;
        text-underline-offset: 4px;
        }

        .cop span {
            font-size: 12px;

        }
            .tac{
            text-align:center
}
    </style>
    <body>
    <table>
        <tr>
            <td align="right">HS(034)ICT/001-2010 <br>(Pindaan 2025)</td>
        </tr>
    </table>
    <div class="outshell">
        <table>
            <tr>
                <td align="center"></td>
            </tr>
            <tr >
                <td align="center">UNIT TEKNOLOGI MAKLUMAT (ICT) <br>HOSPITAL SEGAMAT</td>
            </tr>
        </table>
        <table>
            <tr>
                <td colspan=6 align="center" class="bt bl br">BORANG ADUAN ICT</td>
            </tr>
            <tr class="bai">
                <td class="bl bt" style="width:18%;">Nama Pengadu</td>
                <td class="bt" style="width:1%">:</td>
                <td class="bt br" style="width:48.5%">' . $aduan['nama_pengadu'] . '</td>
                <td class="bt" style="width:15.5%;">Tarikh Aduan</td>
                <td class="bt" style="width:1%">:</td>
                <td class="bt br" style="width:16%">' . date('d-m-Y', strtotime($tarikh_aduan)) . '</td>
            </tr>
            <tr class="bai">
                <td class="bl bt">Lokasi/Jabatan</td>
                <td class="bt">:</td>
                <td class="bt br">' . $aduan['nama_jabatan'] . '</td>
                <td class="bt">Masa Aduan</td>
                <td class="bt">:</td>
                <td class="bt br">' . date('H:i', strtotime($masa_aduan)) . '</td>
            </tr>
            <tr class="bai">
                <td class="bl bt">No Pendaftaran Aset</td>
                <td class="bt">:</td>
                <td class="bt br">' . $aduan['no_pendaftaran'] . '</td>
                <td class="bt">No Telefon</td>
                <td class="bt">:</td>
                <td class="bt br">' . $aduan['no_telefon'] . '</td>
            </tr>
        </table>
        <table>
            <tr>
                <td rowspan=14 class="bt bl br" style="width:19%; vertical-align:top;">Jenis Kerosakan <br> (Sila Tandakan /)</td>
                <td rowspan=3 class="bt br labelbox">Komputer (PC/Laptop)<br> - Tetikus (Mouse) <br> - Papan Kekunci (Keyboard) <br> - Monitor</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'Komputer') ? '/' : '') . '</td>
                <td class="bt br labelbox">ePRS</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'ePRS') ? '/' : '') . '</td>
                <td class="bt br labelbox">SMRP</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'SMRP') ? '/' : '') . '</td>
            </tr>
            <tr>
                <td class="bt br tickbox"></td>
                <td class="bt br labelbox">OTIS</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'OTIS') ? '/' : '') . '</td>
                <td class="bt br labelbox">PHIS</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'PHIS') ? '/' : '') . '</td>
            </tr>
            <tr>
                <td class="bt br tickbox"></td>
                <td class="bt br labelbox">LabIS</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'LabIS') ? '/' : '') . '</td>
                <td class="bt br labelbox">MySPA</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'MySPA') ? '/' : '') . '</td>
            </tr>
            <tr>
                <td class="bt br labelbox">Pencetak</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'Pencetak') ? '/' : '') . '</td>
                <td class="bt br labelbox">HRMIS</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'HRMIS') ? '/' : '') . '</td>
                <td class="bt br labelbox">UPIS</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'UPIS') ? '/' : '') . '</td>
            </tr>
            <tr>
                <td class="bt br labelbox">Rangkaitan/<i>Internet</i></td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'Rangkaian/Internet') ? '/' : '') . '</td>
                <td class="bt br labelbox">iServe</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'iServe') ? '/' : '') . '</td>
                <td class="bt br labelbox">myCPD</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'myCPD') ? '/' : '') . '</td>
            </tr>
            <tr>
                <td class="bt br labelbox"><i>Antivirus</i></td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'Antivirus') ? '/' : '') . '</td>
                <td class="bt br labelbox">DBS</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'DBS') ? '/' : '') . '</td>
                <td class="bt br labelbox">iGFMAS</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'iGFMAS') ? '/' : '') . '</td>
            </tr>
            <tr>
                <td class="bt br labelbox">CCTV</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'CCTV') ? '/' : '') . '</td>
                <td class="bt br labelbox">iCC</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'iCC') ? '/' : '') . '</td>
                <td class="bt br labelbox">SPIKPA</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'SPIKPA') ? '/' : '') . '</td>
            </tr>
            <tr>
                <td class="bt br labelbox">EZ-Attendance (UPA)</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'EZ-Attendance(UPA)') ? '/' : '') . '</td>
                <td class="bt br labelbox">iDoc</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'iDoc') ? '/' : '') . '</td>
                <td class="bt br labelbox">ePerolehan</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'ePerolehan') ? '/' : '') . '</td>
            </tr>
            <tr>
                <td class="bt br labelbox">iPortal Sistem HS</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'iPortal Sistem HS') ? '/' : '') . '</td>
                <td class="bt br labelbox">iCar</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'iCar') ? '/' : '') . '</td>
                <td class="bt br labelbox">eProfile</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'eProfile') ? '/' : '') . '</td>
            </tr>
            <tr>
                <td class="bt br labelbox"><i>Microsoft Office</i></td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'Microsoft Office') ? '/' : '') . '</td>
                <td class="bt br labelbox">iBooking</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'iBooking') ? '/' : '') . '</td>
                <td class="bt br labelbox">eLaporan</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'eLaporan') ? '/' : '') . '</td>
            </tr>
            <tr>
                <td class="bt br labelbox"><i>Search Engine (Google/Firefox)</i></td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'Search Engine') ? '/' : '') . '</td>
                <td class="bt br labelbox">eHadir</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'eHadir') ? '/' : '') . '</td>
                <td class="bt br labelbox">IRIS</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'IRIS') ? '/' : '') . '</td>
            </tr>
            <tr>
                <td class="bt br labelbox">Faceprint</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'Faceprint') ? '/' : '') . '</td>
                <td class="bt br labelbox">CNP</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'CNP') ? '/' : '') . '</td>
                <td class="bt br labelbox">SMAI</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'SMAI') ? '/' : '') . '</td>
            </tr>
            <tr>
                <td class="bt br labelbox">GPKI</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'GPKI') ? '/' : '') . '</td>
                <td class="bt br labelbox">iQMS</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'iQMS') ? '/' : '') . '</td>
                <td class="bt br labelbox">MPIS</td>
                <td class="bt br tickbox">' . (($aduan['kerosakan'] == 'MPIS') ? '/' : '') . '</td>
            </tr>
            <tr>
                <td class="bt br labelbox">Lain-lain (Nyatakan) : </td>
                <td colspan="5" class="bt br" style="width:50%;">' . ((!in_array($aduan['kerosakan'], $jeke)) ? $aduan['kerosakan'] : '') . '</td>
            </tr>
        </table>
        <table>
            <tr>
                <td align="center" class="bt br bl">PERINCIAN MASALAH</td>
            </tr>
            <tr>
                <td class="bt br bl">' . $aduan['masalah'] . '</td>
            </tr>
            <tr>
                <td align="center" class="bt br bl">PENGESAHAN TINDAKAN AWAL (DI ISI OLEH PENGADU*)</td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="bt br bl" style="width:25%;" rowspan=3>Tarikh:' . date('d-m-Y', strtotime($tarikh_diterima)) . '</td>
                <td class="bt br" style="width:25%;" rowspan=3>Masa:' . date('H:i', strtotime($masa_diterima)) . '</td>
                <td class="bt br" style="width:50%" align="center">' . $aduan['nama_pengadu'] . '</td>
            </tr>
            <tr>
                <td class="br" align="center">' . $aduan['jawatan_pengadu'] . '</td>
            </tr>
            <tr>
                <td class="br" align="center">
                    <div class="cop">
                        <span>Tandatangan dan Cop Rasmi</span>
                    </div>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td colspan="6" class="bt br bl" align="center">UNTUK KEGUNAAN ICT</td>
            </tr>
            <tr>
                <td class="bt bl" style="width:20%;">Tarikh Terima Aduan</td>
                <td class="bt" style="width:1%;">:</td>
                <td class="bt br" style="width:31%;">' . date('d-m-Y', strtotime($tarikh_diterima)) . '</td>
                <td class="bt" style="width:33%;">Masa Terima Aduan</td>
                <td class="bt" style="width:1%;">:</td>
                <td class="bt br" style="width:14%;">' . date('H:i', strtotime($masa_diterima)) . '</td>
            </tr>
            <tr>
                <td class="bt bl">No Tag Vendor</td>
                <td class="bt">:</td>
                <td class="bt br">' . $aduan['no_tag_vendor'] . '</td>
                <td class="bt">No Siri Perkakasan Gantian</td>
                <td class="bt">:</td>
                <td class="bt br">' . $aduan['no_siri_perkakasan_gantian'] . '</td>
            </tr>
            <tr>
                <td colspan=2 rowspan=3 class="bt bl br" style="vertical-align:middle;">Penyelesaian</td>
                <td colspan="4" class="bt br">' . $aduan['penyelesaian'] . '</td>
            </tr>
            <tr>
                <td colspan=4 class="br">&nbsp;</td>
            </tr>
            <tr>
                <td colspan=4 class="br">&nbsp;</td>
            </tr>
        </table>
        <table>
            <tr>
                <td align="center" class="bt br bl" colspan=2>PENGESAHAN PENYELESAIAN</td>
            </tr>
            <tr>
                <td align="center" class="bt br bl" style="width:50%;">PENGADU</td>
                <td align="center" class="bt br" style="width:50%;">KAKITANGAN ICT</td>
            </tr>
            <tr>
                <td class="bt br bl" align="center">' . $aduan['nama_pengadu'] . '</td>
                <td class="bt br" align="center">' . $aduan['nama_petugas'] . '</td>
            </tr>
            <tr>
                <td class=" br bl" align="center">' . $aduan['jawatan_pengadu'] . '</td>
                <td class=" br" align="center">' . $aduan['jawatan_penugas'] . '</td>
            </tr>
            <tr>
                <td class="bl br" align="center">
                    <div class="cop">
                        <span>Tandatangan dan Cop Rasmi</span>
                    </div>
                </td>
                <td class="br" align="center">
                    <div class="cop">
                        <span>Tandatangan dan Cop Rasmi</span>
                    </div>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="bl bt br" style="width:20%">Tarikh</td>
                <td class="bt br" style="width:30%">' . date('d-m-Y', strtotime($tarikh_selesai)) . '</td>
                <td class="bt br" style="width:20%">Tarikh</td>
                <td class="bt br" style="width:30%">' . date('d-m-Y', strtotime($tarikh_selesai)) . '</td>
            </tr>
            <tr>
                <td class="bl bt br bb">Masa</td>
                <td class="bt br bb">' . date('H:i', strtotime($masa_selesai)) . '</td>
                <td class="bt br bb">Masa</td>
                <td class="bt br bb">' . date('H:i', strtotime($masa_selesai)) . '</td>
            </tr>
        </table>
    </div>
    <table>
        <tr>
            <td align="center" style="font-size:14px;">** Sebarang pertanyaan boleh berhubung dengan pegawai ditalian 07-9433333 (samb. : 248/352/375/376)</td>
        </tr>
    </table>
    </body>
';
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($html);
if (ob_get_length()) {
    ob_end_clean();
}
$mpdf->Output('borang_aduan.pdf', 'D');
