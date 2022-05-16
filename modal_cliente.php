<?php
/* Modal clientes  */
?>
<!-- Modal cliente-->

      <div class="modal-header">
        <h4 class="modal-title" id="myModalCliente">Agregar cliente</h4>
      </div>
      <div class="modal-body">
      <div class="wrapper wrapper-content  animated fadeInRight">

        <div class="row">
              <div class="col-md-6">
                                <div class="form-group">
                                    <label><h4 class='text-navy'>Nombres </h4></label>
                                   </div>
              </div>
              <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" id="nombress" name="nombress" value=''  class="form-control">
                                </div>
              </div>

        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label><h4 class='text-success'>DUI</h4></label>
                          </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <input type="text" id="duii" name="duii" value='' placeholder="DUI" class="form-control">
                          </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label><h4 class='text-success'>Teléfonos</h4></label>
                          </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <input type="text" id="tel1" name="tel1" value='' placeholder="TELEFONO 1" class="form-control">
                          </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <input type="text" id="tel2" name="tel2" value='' placeholder="TELEFONO 2" class="form-control">
            </div>
          </div>
        </div>
      </div>
    </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-primary" id="btnAddClient"><i class="fa fa-user"></i> Guardar</button>
      <button type="button" class="btn btn-warning" id="btnEsc">Salir</button>
  </div>
