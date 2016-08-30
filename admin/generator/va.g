<?= $this->insert('overall/header') ?>
<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-footer-fixed">

  <?= $this->insert('overall/topnav') ?>

  <div class="page-container">

    <?= $this->insert('overall/menu') ?>
      <div class="page-content-wrapper">

        <div class="page-content">

          <?= $this->insert('overall/pagebar',array('this_page' => 'Vista',
          'desc' => 'Pequeña descripción, si no se quiere poner nada pasar null')) ?>

          <div class="row">
            <form role="form" id="{{action}}_form" >

            <div class="col-sm-12">

              <div class="alert hide" id="ajax_{{action}}"></div>

              <div class="portlet box blue ">
                  <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-gears"></i> Formulario
                    </div>
                  </div>

                  <div class="portlet-body form">
                    <div class="form-horizontal form-bordered form-label-stripped">
                    <div class="form-body">

                      <div class="form-group">
                          <label class="control-label col-md-3">Ejemplo</label>
                          <div class="col-md-9">
                              <input type="text" name="ejemplo" class="form-control" placeholder="Ejemplo">
                              <span class="help-block"> Ayudita texto </span>
                          </div>
                      </div>

                      <div class="form-group">
                          <label class="control-label col-md-3">Checkbox</label>
                          <div class="col-md-9">
                            <div class="icheck-list">
                              <label><?= Bootstrap::checkbox('le_nombre','value',false,'icheck') ?> Checkbox </label>
                            </div>
                          </div>
                      </div>

                      <div class="form-group">
                          <label class="control-label col-md-3">Radios</label>
                          <div class="col-md-9">
                            <div class="icheck-list">
                              <label><?= Bootstrap::radio('le_radio','value',false,'icheck') ?> Sí</label>
                              <label><?= Bootstrap::radio('le_radio','value',true,'icheck') ?> No</label>
                            </div>
                          </div>
                      </div>

                      <div class="form-group">
                          <label class="control-label col-md-3">Ejemplo Select</label>
                          <div class="col-md-9">
                              <?= Bootstrap::basic_select('le_select', array('1' => 'uno', '2' => 'dos')) ?>
                          </div>
                      </div>

                    </div>
                    <div class="form-actions">
                      <div class="row">
                          <div class="col-md-offset-3 col-md-9">
                              <button type="button" id="{{action}}" class="btn green">
                                  <i class="fa fa-check"></i> Enviar</button>
                              <a href="" class="btn default">Cancelar</a>
                          </div>
                      </div>
                    </div>

                  </div>

                  </div>
              </div>

            </div>


            </form>
          </div>


          <div class="clearfix"></div>

        </div>
    </div>
  </div>
<?= $this->insert('overall/footer') ?>
<script src="views/backend/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
<script src="views/backend/pages/scripts/form-icheck.min.js" type="text/javascript"></script>
<script src="views/app/js/{{action}}.js"></script>
</body>
</html>
