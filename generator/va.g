<?= $this->insert('overall/header') ?>
<body>
  <?= $this->insert('overall/topnav') ?>
  <div id="content">

      <div class="container">
          <div class="col-md-12">
            <div class="box">
                <p class="text-center">
                    <img src="views/app/images/logo.png">
                </p>

                <h3>Vista de Ejemplo</h3>

                <form id="{{action}}_form" role="form">
                  <div class="alert hide" id="ajax_{{action}}"></div>
                  <div class="form-group">
                    <label class="cole">Ejemplo:</label>
                    <input type="text" class="form-control form-input" name="ejemplo" placeholder="Escribe algo..." />
                  </div>
                  <div class="form-group">
                    <button type="button" id="{{action}}" class="btn red  btn-block">Enviar</button>
                  </div>
                </form>
            </div>
          </div>
     </div>
     <?= $this->insert('overall/footer') ?>
       <script src="views/app/js/{{action}}.js"></script>
  </div>
</body>
</html>
