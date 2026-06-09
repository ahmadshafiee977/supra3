<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gentelella Alela! | </title>

    <!-- Bootstrap -->

  </head>

  <body class="nav-md footer_fixed">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="index.html" class="site_title"><i class="fa fa-paw"></i> <span>Gentelella Alela!</span></a>
            </div>

            <div class="clearfix"></div>



  
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            
            <!-- /menu footer buttons -->
          </div>
        </div>

        <!-- top navigation -->
     

        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                
        <!-- Subsystem Data Table -->
        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
              <div class="x_title">
                  <h2>SENARAI DATA SUBSISTEM</h2>
                  <div class="clearfix"></div>
              </div>
 
              <div class="clearfix"></div>

<div class="x_content">
  <table id="datatable-fixed-header" class="table table-striped table-bordered">

              <div class="x_content">

                  <!-- Add New Subsystem Form (Toggled by button click) -->
                  <button class="btn btn-dark mb-3 " style="margin-bottom: 20px;" id="toggleAddForm">Tambah Subsistem</button>

                  <div id="addForm" style="display:none; margin-bottom: 20px;">
                      <form method="POST" action="">
                          <div class="form-group">
                              <label for="CODESYSTEM">Kod Subsistem</label>
                              <input type="text" class="form-control" name="CODESYSTEM" required>
                          </div>
                          <div class="form-group">
                              <label for="DESCSYSTEM">Deskripsi</label>
                              <input type="text" class="form-control" name="DESCSYSTEM" required>
                          </div>
                          <div class="form-group">
                              <label for="STATUS">Status</label>
                              <select class="form-control" name="STATUS" required>
                                  <option value="Aktif">Aktif</option>
                                  <option value="Tidak Aktif">Tidak Aktif</option>
                              </select>
                          </div>
                          <button type="submit" class="btn btn-primary" name="add_subsystem">Hantar</button>
                      </form>
                  </div>

                  <!-- Subsystem Table -->
                          <thead>
                              <tr>
                                  <th>BIL</th>
                                  <th>KOD SUBSISTEM</th>
                                  <th>DESKRIPSI</th>
                                  <th>STATUS</th>
                                  <th>TINDAKAN</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php
                              $serialNumber = 1;  
                              // Loop through the results and display them in the table
                              while ($row = mysqli_fetch_assoc($result)) {
                              ?>
                                  <tr>
                                  <td><?php echo $serialNumber++; ?></td> 
                                      <th>
                                          <span class="display"><?php echo $row['CODESYSTEM']; ?></span>
                                          <input class="edit form-control" type="text" value="<?php echo $row['CODESYSTEM']; ?>" style="display: none;">
                                      </th>
                                      <th>
                                          <span class="display"><?php echo $row['DESCSYSTEM']; ?></span>
                                          <input class="edit form-control" type="text" value="<?php echo $row['DESCSYSTEM']; ?>" style="display: none;">
                                      </th>
                                      <th>
                                          <span class="display"><?php echo $row['STATUS']; ?></span>
                                          <select class="edit form-control" style="display: none;">
                                              <option value="Aktif" <?php echo $row['STATUS'] == 'Aktif' ? 'selected' : ''; ?>>Aktif</option>
                                              <option value="Tidak Aktif" <?php echo $row['STATUS'] == 'Tidak Aktif' ? 'selected' : ''; ?>>Tidak Aktif</option>
                                          </select>
                                      </th>
                                      <th>
  <button class="btn btn-primary btn-sm edit-btn">
      <i class="bi bi-pencil-square"></i>
  </button>
  <button class="btn btn-success btn-sm save-btn" style="display: none;">
      <i class="bi bi-save"></i>
  </button>
  <a href="delete_system.php?id=<?php echo $row['SystemID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Anda pasti ingin memadam record ini ?');">
      <i class="bi bi-trash"></i>
  </a>
                                          <input type="hidden" class="row-id" value="<?php echo $row['SystemID']; ?>" />
                                      </th>
                                  </tr>
                              <?php
                              }
                              ?>
                          </tbody>
                      
                   
                          </table>
            </div> <!-- /System Table -->
      </div> <!-- /x_panel -->
  </div> <!-- /col -->
</div> <!-- /right_col -->
</div> <!-- /col -->
</div> 
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- footer content -->
<footer>
  <div class="pull-right">
  COPYRIGHT @ 2025 ICT Hospital Segamat <a href="https://colorlib.com"></a>
  </div>
  <div class="clearfix"></div>
</footer>
<!-- /footer content -->
</div>
<!-- End of page content -->
</div>

<!-- Toggle form visibility -->
<script>
  document.getElementById("toggleAddForm").onclick = function() {
      var form = document.getElementById("addForm");
      if (form.style.display === "none") {
          form.style.display = "block";
      } else {
          form.style.display = "none";
      }
  }

  // Handle Edit and Save buttons
  document.querySelectorAll('.edit-btn').forEach(function(button) {
      button.addEventListener('click', function() {
          var row = this.closest('tr');
          row.querySelectorAll('.display').forEach(function(span) {
              span.style.display = 'none';
          });
          row.querySelectorAll('.edit').forEach(function(input) {
              input.style.display = 'block';
          });
          this.style.display = 'none';
          row.querySelector('.save-btn').style.display = 'inline-block';
      });
  });

  // Handle Save functionality with form submission
  document.querySelectorAll('.save-btn').forEach(function(button) {
      button.addEventListener('click', function() {
          var row = this.closest('tr');
          var id = row.querySelector('td:first-child').innerText;
          var code = row.querySelectorAll('input[type="text"]')[0].value;
          var desc = row.querySelectorAll('input[type="text"]')[1].value;
          var status = row.querySelector('select').value;

          // Create a form element and submit the data
          var form = document.createElement('form');
          form.method = 'POST';
          form.action = '';

          var idInput = document.createElement('input');
          idInput.type = 'hidden';
          idInput.name = 'id';
          idInput.value = id;
          form.appendChild(idInput);

          var codeInput = document.createElement('input');
          codeInput.type = 'hidden';
          codeInput.name = 'CODESYSTEM';
          codeInput.value = code;
          form.appendChild(codeInput);

          var descInput = document.createElement('input');
          descInput.type = 'hidden';
          descInput.name = 'DESCSYSTEM';
          descInput.value = desc;
          form.appendChild(descInput);

          var statusInput = document.createElement('input');
          statusInput.type = 'hidden';
          statusInput.name = 'STATUS';
          statusInput.value = status;
          form.appendChild(statusInput);

          var submitInput = document.createElement('input');
          submitInput.type = 'hidden';
          submitInput.name = 'edit_subsystem';
          form.appendChild(submitInput);

          document.body.appendChild(form);
          form.submit();
      });
  });
</script>

                <h3>Fixed Footer <small> Just add class <strong>footer_fixed</strong></small></h3>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="../vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="../vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../vendors/nprogress/nprogress.js"></script>
    <!-- jQuery custom content scroller -->
    <script src="../vendors/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="../build/js/custom.min.js"></script>
  </body>
</html>