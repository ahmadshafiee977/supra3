<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require('db.php');
// Start session to retrieve user level
if (session_status() == PHP_SESSION_NONE) {
  session_name("ADUANICT");
  session_start();
  $role = $_SESSION['user_level'];
  session_write_close();

  if (in_array($role, ['ADMIN', 'SUPERADMIN'])) {
    ini_set('session.gc_maxlifetime', 43200);
    ini_set('session.cookie_lifetime', 43200);
  }
}
session_name('ADUANICT');
session_start();

if (!isset($_SESSION['user_level'])) {
  echo "<script>alert('Sila log masuk.');</script>"; // clear all session and redirect to login page
  session_unset();
  session_destroy();
  echo "<script> window.location.href='index.php'</script>";
  exit;
}

$user_id = (int) $_SESSION['user_id'];
$user_level = $_SESSION['user_level'];
$jabatan_pengguna =  $_SESSION['jabatan_pengguna'];
$jabatan_id = (int)$_SESSION['jabatan_id'];
$jawatan_pengguna =  $_SESSION['jawatan_pengguna'];



function getShortName($name)
{
  //$separators = [' BIN ', ' bin ', ' BINTI ', ' binti ', ' A/L ', ' a/l ', ' A/P ', ' a/p '];
  $pattern = '/\b(bin|binti|a\/l|a\/p)\b/i';

  $parts = preg_split($pattern, $name, 2);
  return $parts[0];
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!-- Meta, title, CSS, favicons, etc. -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>ICT</title>
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

</head>

<body class="nav-md">
  <div class="container body">
    <div class="main_container hidden-print">
      <div class="col-md-3 left_col hidden-print" id="main_header">
        <div class="left_col scroll-view">
          <div class="navbar nav_title hidden-print" style="border: 0;">
            <div class="rowMenu logo_border">
              <a href="#" class="site_title"><span class="rockwell-font">ICT </span></a>
              <img src="../images/ict_logo.png" width="50" height="50" style="margin-top:5px;">
            </div>
          </div>
          <link href="https://fonts.googleapis.com/css2?family=Rock+Salt&display=swap" rel="stylesheet">
          <link href="https://fonts.cdnfonts.com/css/open-sauce-one" rel="stylesheet">
          <button class="btn btn-stick btn-warning" style="display:none;">Aduan</button>

          <style>
            .logo_border {
              margin-top: 3px;
              border: 1px solid white;
              border-radius: 10px;
              padding-bottom: 2px;
            }

            .rockwell-font {
              font-family: 'Open Sauce One';
              font-size: 36px;
              -webkit-text-stroke-width: 2px;
              -webkit-text-stroke-color: black;
              /*
              font-family: 'Rockwell';
              
              /*background: linear-gradient(90deg, rgba(58, 111, 180, 1) 0%, rgba(253, 29, 29, 1) 100%, rgba(252, 176, 69, 1) 100%); */
              background: white;
              /*background: linear-gradient(90deg, rgba(0, 0, 254, 1) 0%, rgba(255, 49, 49, 1) 100%);*/
              background-clip: text;
              -webkit-background-clip: text;
              -webkit-text-fill-color: transparent;
              font-weight: bold !important;

            }

            .nav.child_menu li a {
              font-size: 15px;
              /* Besar font */
              font-weight: normal;
              /* Membuat tulisan tebal */
            }

            .rowMenu {
              display: flex;
              flex-direction: row;
              gap: 0;
              justify-content: flex-start;
              padding-right: 10px;
            }

            .sidebar-menu .strong {
              font-weight: normal !important;

            }

            .nav.child_menu>li>a strong,
            .nav.side-menu>li>a strong {
              font-weight: normal !important;

            }

            h1,
            h2,
            h3 {
              font-size: 3rem !important
            }


            .username-background {

              border-radius: 10px;
              object-position: center center;
            }

            .username_frame {
              border: 2px solid #ddd;
              border-radius: 10px;
              /*
              background-color: rgb(7, 101, 109);
              */
              /*
              background: #272727;*/
              backdrop-filter: blur(3px);
              border: 2px solid rgba(255, 255, 255, 0.18);
              padding: 10px;
              word-wrap: break-word;
              overflow-wrap: break-word;
              text-align: center;
              max-width: 300px;
              margin: 10px auto;
              /*box-shadow: 0px 4px 6px rgba(255, 250, 250, 0);
              gray but look like glowing light rgba(227, 228, 237, 0.37)*/
              box-shadow: 0px 0px 30px rgba(227, 228, 237, 0.37);
            }

            #main_header {
              min-height: 100vh;
            }

            .x_title {
              display: flex;
              justify-content: center;
              /* Membuat teks rata tengah secara horizontal */
              align-items: center;
              /* Membuat teks rata tengah secara vertikal */
              height: 80%;
              /* Memastikan tinggi penuh digunakan */
            }

            .x_panel h2 {
              font-size: 4rem;
              /* Mengatur ukuran font menjadi 3rem */
              font-weight: bold;
              /* Membuat tulisan menjadi tebal */
              text-align: left;
              /* Membuat tulisan rata tengah */
            }

            .x_subtitle {
              display: flex;
              justify-content: flex-start;
              background-color: #c8dcf4;
              margin: 0;
              padding: 0;
              border: 1px solid black;
              border-collapse: collapse;
            }

            .x_subtitle h2 {
              text-align: left;
              font-weight: bold;
              color: black !important;
              font-size: 1.5em !important;
              margin: 2px !important;
              padding: 0 !important;
            }

            .col-md-6 {
              margin-bottom: 5px;

            }

            .left_col {
              position: relative;
            }

            .btn-stick {
              display: flex;
              align-items: center;
              justify-content: center;
              position: absolute;
              top: 100px;
              right: -52px;
              writing-mode: vertical-lr;
              text-orientation: sideways-right;
              text-align: center;
              width: 40px;
              height: 100px;
              font-weight: bold;
              font-size: 20px;
              margin: 0 auto;
              transform: rotate(180deg);
              transform-origin: center;
            }
          </style>


          <div class="clearfix"></div>
          <div class="clearfix"></div>
          <div class="clearfix"></div>
          <?php

          $usernameWords = explode(" ", $_SESSION['user_name']);

          // Format the username for display
          if (count($usernameWords) > 3) {
            // Show the first three words on the first line and the rest on the second line
            $usernameDisplay = implode(" ", array_slice($usernameWords, 0, 3)) . "<br>" .
              implode(" ", array_slice($usernameWords, 3));
          } else {
            // Show all words if there are 3 or fewer
            $usernameDisplay = implode(" ", $usernameWords);
          }
          ?>
          <div class="username-background hidden-print">
            <div class="hidden-print username_frame ">
              <h7 class="name-text">
                <?php echo $usernameDisplay; ?>
              </h7>
            </div>
          </div>
          <!-- sidebar menu -->
          <div id="sidebar-menu" class="main_menu_side hidden-print main_menu" style="font-weight:normal; color:black !important;">
            <div class="menu_section">
              <ul class="nav side-menu">
                <!-- Home menu item -->
                <li><a><i class="fa fa-home"></i> <strong>ADUAN</strong> <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
                    <?php if (in_array($user_level, ['ADMIN', 'SUPERADMIN'])): ?>
                      <li><a href="aduan_minidashboard.php"><strong>!Selesai</strong></a></li>
                    <?php endif ?>
                    <li><a href="aduan_dashboard.php"><strong>Senarai Aduan</strong></a></li>
                  </ul>
                </li>
                <?php
                if (in_array($user_level, ['ADMIN', 'SUPERADMIN'])):
                ?>
                  <li><a><i class="fa fa-cogs"></i><strong>TETAPAN</strong><span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="kerosakan_dashboard.php"><strong>Jenis Kerosakan</strong></a></li>
                      <li><a href="kategori_dashboard.php"><strong>Kategori</strong></a></li>
                      <li><a href="aset_dashboard.php"><strong>Aset</strong></a></li>
                      <li><a href="jawatan_dashboard.php"><strong>Jawatan</strong></a></li>
                      <li><a href="jabatan_dashboard.php"><strong>Jabatan</strong></a></li>
                      <li><a href="lokasi_dashboard.php"><strong>Lokasi</strong></a></li>
                      <li><a href="cuti_dashboard.php"><strong>Cuti Umum</strong></a></li>
                    </ul>
                  </li>
                <?php endif ?>

                <?php if ($user_level == 'SUPERADMIN'): ?>
                  <li><a href="pengguna_dashboard.php"><i class="fa fa-user"></i><strong>TETAPAN PENGGUNA</strong></a></li>
                <?php endif ?>
                <?php if ($user_level == 'PENYELARAS'): ?>
                  <li>
                    <a href="aset_dashboard.php"><i class="fa fa-laptop"></i><strong>ASET</strong></a>
                  </li>
                <?php endif ?>
                <li>
                  <a href="manual_pengguna.php"><i class="fa fa-book"></i><strong>MANUAL PENGGUNA</strong></a>
                </li>
                <li><a href="belum_buat.php"><i class="fa fa-list"></i><strong style="color:red"><b>MEH TENGOK</b></strong></a></li>
              </ul>
            </div>
          </div>
          <!-- /sidebar menu -->

          <!-- /menu footer buttons -->
          <div class="sidebar-footer hidden-small">
            <!-- Settings Button -->

            <!-- Fullscreen Button -->
            <a data-toggle="tooltip" data-placement="top" title="FullScreen" id="fullscreen-btn">
              <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
            </a>

          </div>

          <!-- Add this JavaScript to handle fullscreen functionality -->
          <script>
            function getShortName(name) {
              // Create a regex similar to your PHP pattern
              const pattern = /\b(bin|binti|a\/l|a\/p)\b/i;

              // Split based on the pattern (only first 2 parts like in PHP)
              const parts = name.split(pattern);

              // Return the first part (before bin/binti/a/l/a/p)
              return parts[0].trim();
            }

            // Check if the page should load in fullscreen based on localStorage
            if (localStorage.getItem('isFullscreen') === 'true') {
              goFullscreen();
            }

            // Fullscreen toggle functionality
            document.getElementById('fullscreen-btn').addEventListener('click', function() {
              if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
                // Request fullscreen
                goFullscreen();
                localStorage.setItem('isFullscreen', 'true'); // Save fullscreen state
              } else {
                // Exit fullscreen if already in fullscreen
                exitFullscreen();
                localStorage.setItem('isFullscreen', 'false'); // Save fullscreen state
              }
            });

            // Function to request fullscreen
            function goFullscreen() {
              if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
              } else if (document.documentElement.mozRequestFullScreen) { // Firefox
                document.documentElement.mozRequestFullScreen();
              } else if (document.documentElement.webkitRequestFullscreen) { // Chrome, Safari, Opera
                document.documentElement.webkitRequestFullscreen();
              } else if (document.documentElement.msRequestFullscreen) { // IE/Edge
                document.documentElement.msRequestFullscreen();
              }
            }

            // Function to exit fullscreen
            function exitFullscreen() {
              if (document.exitFullscreen) {
                document.exitFullscreen();
              } else if (document.mozCancelFullScreen) { // Firefox
                document.mozCancelFullScreen();
              } else if (document.webkitExitFullscreen) { // Chrome, Safari, Opera
                document.webkitExitFullscreen();
              } else if (document.msExitFullscreen) { // IE/Edge
                document.msExitFullscreen();
              }
            }

            // Optional: Add a keydown event listener to handle the Esc key if desired.
            document.addEventListener('keydown', function(event) {
              if (event.key === 'Escape') {
                exitFullscreen();
                localStorage.setItem('isFullscreen', 'false'); // Save fullscreen state
              }
            });
          </script>

          <!-- /menu footer buttons -->
        </div>
      </div>


      <!-- top navigation -->
      <div class="top_nav hidden-print">
        <div class="nav_menu">
          <nav>
            <div class="nav toggle">
              <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
              <li class="">
                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                  <span class="fa fa-angle-down fa-2x" id="arrow-logo"></span>
                </a>
                <ul class="dropdown-menu dropdown-usermenu pull-right">
                  <li><a href="profil_pengguna.php"><i class="fa fa-user pull-right"></i> Profil</a></li>
                  <li><a href="../index.php?logout=true"><i class="fa fa-sign-out pull-right"></i>Log Keluar</a></li>
                </ul>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
    <!-- /top navigation -->




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
    <script src="../vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="../vendors/pdfmake/build/vfs_fonts.js"></script>
    <!--export excel -->
    <script src="../vendors/exportxlxs/dist/xlsx.full.min.js"></script>
    <!-- report html to pdf  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <!-- report html table to pdf using jsPDF - autotable  -->
    <script src="../vendors/exceljs/dist/exceljs.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <script src="../vendors/echarts/dist/echarts.js"></script>






    <?php
    ini_set('display_errors', 0); // Hide errors on the page
    ini_set('log_errors', 1);     // Log errors (optional)
    error_reporting(E_ALL);

    ?>

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
        } else if ($('#datatable-buttons').length > 0) {
          $('#datatable-buttons').DataTable().destroy();
          $('#datatable-buttons').DataTable({
            language: {
              search: 'Cari: ',
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
            lengthChange: false
          });
        }
      })
    </script>

    <script>
      function formatDate(input) {
        if (input.value) {
          const [year, month, day] = input.value.split("-");
          input.type = "text";
          input.value = `${day}-${month}-${year}`;
        } else {
          input.type = "text";
        }
      }

      function textToDate(text) {
        if (text.value) {
          const [day, month, year] = text.value.split("-");
          text.type = "date";
          text.value = `${year}-${month}-${day}`;
        } else {
          text.type = "date";
        }
      }
    </script>



    <script>
      function adjustHeaderHeight() {
        const header = document.getElementById('main_header');
        const pageHeight = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight, header.scrollHeight);
        header.style.height = pageHeight + 'px';
      }

      window.addEventListener('load', adjustHeaderHeight);
      window.addEventListener('resize', adjustHeaderHeight);
    </script>
    <audio src="../sound/doorbell.wav" id="notifSound"></audio>

    <script src="https://hsgmt23:3000/socket.io/socket.io.js"></script>
    <script>
      const currentPath = window.location.pathname;
      const isAduanPage = currentPath.endsWith('aduan_dashboard.php') || currentPath.endsWith('aduan_minidashboard.php');

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

      window.onload = function() {
        if (Notification.permission !== "granted") {
          Notification.requestPermission();
        }
      }
      const socket = io("https://hsgmt23:3000", {
        auth: {
          token: "<?= $_SESSION['user_level'] ?>"
        }
      });

      socket.on("new_complaint", (data) => {
        audio.loop = true;
        audio.play();
        isAlarmPlaying = true;

        if (isAduanPage) {
          const table = $('#datatable-fixed-header').DataTable();
          const row = table.row.add([
            '0',
            data.no_pendaftaran,
            data.kerosakan,
            data.masalah,
            getShortName(data.nama),
            '',
            `<span class="badge badge-baru">${data.status}</span>`,
            data.tarikh_dihantar,
            `<a class="btn btn-primary btn-sm" href="maklumat_aduan.php?id=${data.id}"><i class="bi bi-person-add"></i></a>`
          ]).draw(false).node();
          renumberTable();
          requestAnimationFrame(() => {
            row.classList.add("new-row", "text-info");
            alert("Class added");
          })


        }
        if (Notification.permission == "granted") {
          const notification = new Notification("Aduan Baru!!", {
            tag: "Aduan baru",
            body: "Ada aduan baru."
          });
          notification.onclick = () => {
            window.focus();
            stopAlarm();
          }
        }
      })

      window.addEventListener("focus", stopAlarm);
      window.addEventListener("click", stopAlarm);
    </script>