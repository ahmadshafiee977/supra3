<div class="right_col">
  <div class="x_panel">
    <div class="x_title">
      <h2>SENARAI LOKASI</h2>
    </div>
    <br />
    <button
      class="btn btn-primary"
      data-toggle="modal"
      data-target="#borang-lokasi"
    >
      Tambah Lokasi
    </button>
    <div class="x_content">
      <table
        class="table table-striped table-bordered"
        id="datatable-fixed-header"
      >
        <thead>
          <tr>
            <th style="width: 10%">No.</th>
            <th>Lokasi</th>
            <th style="width: 10%">Tindakan</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td data-id="1">1</td>
            <td>
              <span class="display"> <?= $row['locationName'] ?> </span>
              <input
                type="text"
                class="form-control edit"
                value="<?= $row['locationName'] ?>"
                style="display: none; width: 95%"
              />
            </td>

            <td>
              <button class="d-inline-block btn btn-primary btn-sm btn-edit">
                <i class="bi bi-pencil"></i>
              </button>
              <button
                class="d-inline-block btn btn-success btn-sm btn-save"
                style="display: none"
                onclick="alert('Lokasi berjaya dikemaskini.')"
              >
                <i class="bi bi-save"></i>
              </button>
              <button
                class="d-inline-block btn btn-secondary btn-sm btn-close"
                style="display: none"
              >
                <i class="bi bi-x-square-fill"></i>
              </button>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="modal fade" id="borang-lokasi">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="" method="POST">
        <div class="modal-header">
          <button type="button" data-dismiss="modal" class="close modal-close">
            &times;
          </button>
          <h4 class="modal-title">Tambah Lokasi</h4>
        </div>
        <div class="modal-body">
          <div class="x_panel">
            <div class="col-md-12 col-sm-12">
              <div class="row">
                <label for="n-nama-lokasi">Nama Lokasi</label>
                <input
                  type="text"
                  id="n-nama-lokasi"
                  name="n-nama-lokasi"
                  class="form-control"
                  required
                />
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="submit"
            name="hantar-lokasi"
            class="btn btn-success"
            onclick="alert('Lokasi berjaya ditambah.')"
          >
            Hantar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  document.querySelectorAll(".btn-edit, .btn-close").forEach(function (button) {
    button.addEventListener("click", function () {
      var row = this.closest("tr");
      row.querySelectorAll(".display").forEach(function (span) {
        span.style.display = span.style.display === "none" ? "block" : "none";
      });
      row.querySelectorAll(".edit").forEach(function (input) {
        input.style.display =
          input.style.display === "block" ? "none" : "block";
      });
      row.querySelector(".btn-edit").style.display =
        row.querySelector(".btn-edit").style.display === "none"
          ? "inline-block"
          : "none";
      row.querySelector(".btn-save").style.display =
        row.querySelector(".btn-save").style.display === "inline-block"
          ? "none"
          : "inline-block";
      row.querySelector(".btn-close").style.display =
        row.querySelector(".btn-close").style.display === "inline-block"
          ? "none"
          : "inline-block";
    });

    document.querySelectorAll(".btn-save").forEach(function (button) {
      button.addEventListener("click", function () {
        var row = this.closest("tr");
        var id = row.querySelector("td:first-child").dataset.id;
        var nama = row.querySelector('input[type="text"]').value;
        var kritikal = row.querySelectorAll("select")[0].value;
        var aktif = row.querySelectorAll("select")[1].value;

        var form = document.createElement("form");
        form.method = "POST";
        form.action = "";

        var idInput = document.createElement("input");
        idInput.type = "hidden";
        idInput.name = "id-lokasi";
        idInput.value = id;
        form.appendChild(idInput);

        var namaInput = document.createElement("input");
        namaInput.type = "hidden";
        namaInput.name = "nama-lokasi";
        namaInput.value = nama;
        form.appendChild(namaInput);

        var kriInput = document.createElement("input");
        kriInput.type = "hidden";
        kriInput.name = "kritikal";
        kriInput.value = kritikal;
        form.appendChild(kriInput);

        var staInput = document.createElement("input");
        staInput.type = "hidden";
        staInput.name = "status";
        staInput.value = aktif;
        form.appendChild(staInput);

        var btnInput = document.createElement("input");
        btnInput.type = "hidden";
        btnInput.name = "edit-lokasi";
        form.appendChild(btnInput);

        document.body.appendChild(form);
        form.submit();
      });
    });
  });
</script>
<?php include('footer.php'); ?>
