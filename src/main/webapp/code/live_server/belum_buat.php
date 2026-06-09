<?php
include 'header.php';
?>

<div class="right_col">
    <div class="x_panel">
        <div class="x_title">
            <h2>Nota</h2>
        </div>
        <div class="x_content">
            <h4>Lepas tukar server atau IP nanti, kena tukar code sikit</h4>
            <h4>Dekat page bawah ni</h4>
            <ul>
                <li>header.php</li>
                <li>aduan_dashboard.php</li>
                <li>TV_dashboard.php</li>
            </ul>
            <h4>Semua hsgmt23 tukar jadi hsgmt20, server 20 kan? ha hsgmt20, kalau bukan ikut lah nama host untuk server tu</h4>
            <h4>tak silap header.php ada 2 je hsgmt23, aduan_dashboard.php ada 1 je </h4>
            <h4>TV_dashboard.php tukar ip dari .23 ke .20, sebab TV tak ada setting hostname</h4>
            ni command pm2
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Command</th>
                        <th>Function</th>
                        <th>Expected Output</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>npm install pm2</td>
                        <td>suruh node js untuk install pm2</td>
                        <td>entahlah, tak screenshot pulak haritu</td>
                    </tr>
                    <tr>
                        <td>pm2 start server.js --name E-Aduan --log-date-format "YYYY-MM-DD HH:mm:ss"</td>
                        <td>Untuk start file tu, kalau kat e-aduan untuk act as listener</td>
                        <td>tengok gambar kat bawah</td>
                    </tr>
                    <tr>
                        <td colspan="3"><img src="../images/pm2_start.png" style="width:100%" alt=""></td>
                    </tr>
                    <tr>
                        <td>pm2 list</td>
                        <td>display semua saved process</td>
                        <td>output sama macam table yang kat atas</td>
                    </tr>
                    <tr>
                        <td>pm2 start 0 atau pm2 start all</td>
                        <td>0 tu id processs yang kat table bila run pm2 list, in case process tu status 'stopped' etc, start balik guna command tu, should be no issue la, sebab dah set auto start upon boot</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight:bold;color:red">
                            <h4>In case somehow pm2 punya setting hilang ke apa, pastikan pm2 dah install, pm2 install via node js, pape google/chat gpt</h4>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=3>
                            <h4>
                                cd var/www/e-ict/complaint-notifier <br>
                                pm2 start server.js --name E-Aduan --log-date-format "YYYY-MM-DD HH:mm:ss" <br>
                                pm2 save <br>
                                pm2 startup <br>
                            </h4>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

    </div>
    <div class="x_panel">
        <div class="x_title">
            <h2>Pending task, goodluck intern&sup2; sekalian</h2>
        </div>
        <div class="x_content">
            <table class="table table-striped table-bordered" style="color:black;">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kerja</th>
                        <th>Detail</th>
                        <th>Location/ Page</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1.</td>
                        <td>Page laporan</td>
                        <td>buat page laporan, kena ada filter macam laporan kat sehati, detail filter tanya Puan Saripah/Encik Afiq. layout refer page lain, ikut sebijik melainkan ada keperluan untuk modify </td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>2.</td>
                        <td>Bahagian penyerahan/penerimaan barang kepada kontraktor luar</td>
                        <td>Form untuk tu dah ada, database pun dah ada, cuma php code untuk masukkan data untuk penyerahan/penerimaan je belum ada, dekat page nya aku dah comment mana kena modify code nya. pastu kena clarify jugak siapa yang boleh tekan button serah/terima barang tu, search perkataan 'intern', nanti jumpa lah comment nya </td>
                        <td> code/maklumat_aduan.php</td>
                    </tr>
                    <tr>
                        <td>3.</td>
                        <td>tukar text</td>
                        <td>tukar semua 'Aksi' ke 'Tindakan', tukar kat semua table,aku malas</td>
                        <td>semua page yang ada table</td>
                    </tr>
                    <tr>
                        <td>4.</td>
                        <td>check user level lain punya access/functionality</td>
                        <td>Check semua user level selain Superadmin punya functionality, pastikan semua function, pastu pastikan ada beberapa page yang certain level je boleh access, detail tanya sv</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>5.</td>
                        <td>User manual</td>
                        <td>Buat user manual, system flow tanya sv, untuk user level biasa, penyelaras, & admin, diorang boleh tengok user manual role diorang je, superadmin boleh tengok semua user manual, buat macam sehati punya</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>6.</td>
                        <td>Local testing</td>
                        <td>Ada beberapa benda yang akan fail kalau test dalam local device, sebab ada benda yang kena install, banyak jugaklah, aku rasa lagi senang korang code je dalam local device, tapi testing kat server. <br>Tambah Aduan jadi, masuk je kat database, tapi loading separuh jalan fail, alert takkan keluar, </td>
                        <td>code/aduan_dashboard.php</td>
                    </tr>
                    <tr>
                        <td>7.</td>
                        <td>TV dashboard</td>
                        <td>cantikkan je, jangan kacau coding javascript, tambah boleh, tapi jangan kacau yang sedia ada, kalau nak kacau buat backup dulu, kalau ada penambahan column baru adjust code js (same goes to aduan_dashboard & aduan_minidashboard) </td>
                        <td>code/TV_dashboard.php</td>
                    </tr>
                    <tr>
                        <td>8.</td>
                        <td>tukar logo</td>
                        <td>tukar/tambah logo, self explanatory tbh</td>
                        <td>index.php <br> code/header.php </td>
                    </tr>
                    <tr>
                        <td>9.</td>
                        <td>ayat profesional</td>
                        <td>ayat dekat dua dua slider tu sangatlah pasar, sila upgrade ke ayat professional, </td>
                        <td>code/profil_pengguna.php</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="color:blueviolet" align="center">kalau tak melibatkan data yang sensitive, tanya je gpt</td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
</div>
<?php
include 'footer.php';
?>