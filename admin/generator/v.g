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
            <div class="col-sm-12">
              <div class="portlet box green">
                  <div class="portlet-title">
                      <div class="caption">
                          <i class="fa fa-globe"></i> Texto título aquí </div>
                      <div class="tools"> </div>
                  </div>
                  <div class="portlet-body">
                      <table id="generica" class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%">
                          <thead>
                              <tr>
                                  <th class="all">Etiqueta</th>
                                  <th class="all">Etiqueta 2</th>
                                <!--
                                  <th class="min-phone-l">Last name</th>
                                  <th class="min-tablet">Position</th>
                                  <th class="none">Start date</th>
                                  <th class="desktop">Salary</th>
                                -->
                              </tr>
                          </thead>
                          <tbody>
                            <?php foreach( array(array('example'),array('example2'))  as $x ): ?>
                              <tr>
                                  <td style="vertical-align: middle">Valor <?= $x[0] ?></td>
                                  <td style="vertical-align: middle">valor <?= $x[0] ?></td>
                              </tr>
                            <?php endforeach ?>
                          </tbody>
                      </table>
                  </div>
              </div>

            </div>
            <div class="col-sm-12">
              <a href="<?= $_GET['c'] ?>/crear" class="btn btn-primary"><i class="fa fa-plus"></i> Crear nuevo</a>
            </div>
          </div>


          <div class="clearfix"></div>

        </div>
    </div>
  </div>
<?= $this->insert('overall/footer') ?>
<script src="views/backend/pages/scripts/table-datatables-responsive.js" type="text/javascript"></script>
</body>
</html>
