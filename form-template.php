<h2>
	Paso <span class="text-step">1</span> de 4
</h2>

<div class="progress-bar">
  <div class="progress-bar-inner" id="progress"></div>
</div>


<div class="caldera-form">

  <form id="caldera-form">
    <!-- Paso 1: Datos del consumidor -->
    <div class="step step-1 active">
      <h3>Paso 1: Datos del consumidor</h3>

      <div class="row">
        <div class="col-sm-12 col-md-6">
          <label>Nombres:* <input type="text" class="form-control" name="nombres" required></label>
        </div>
        <div class="col-sm-12 col-md-6">
          <label>Apellidos:* <input type="text" class="form-control" name="apellidos" required></label>
        </div>
        <div class="col-sm-12 col-md-6">
          <label>Tipo de documento:* 
            <select name="tipo_doc" class="form-control" required>
              <option value="">Selecciona</option>
              <option value="DNI">DNI</option>
              <option value="CE">CE</option>
            </select>
          </label>
        </div>
        <div class="col-sm-12 col-md-6">
          <label>Número de documento:* <input type="text" name="numero_doc" class="form-control" required></label>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12 col-md-6">
          <label>Correo electrónico:* <input type="email" name="correo" class="form-control" required></label>
        </div>
        <div class="col-sm-12 col-md-6">
          <label>Teléfono: <input type="text" name="telefono" class="form-control"></label>
        </div>
        <div class="col-sm-12 col-md-6">
          <label>Distrito:* <input type="text" name="distrito" class="form-control" required></label>
        </div>
        <div class="col-sm-12 col-md-6">
          <label>Dirección:* <input type="text" name="direccion" class="form-control" required></label>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12 col-md-6">
          <label>¿Es menor de edad?:*
            <input type="checkbox" name="menor_edad" class="form-control" value="1"> Sí
          </label>
        </div>
      </div>
    </div>

    <!-- Paso 2: Datos del bien contratado -->
    <div class="step step-2">
      <h3>Paso 2: Bien o servicio contratado</h3>

      <div class="row">
        <div class="col-sm-12 col-md-6">
          <label>Tipo:* 
            <select name="tipo_bien" class="form-control" required>
              <option value="">Selecciona</option>
              <option value="producto">Producto</option>
              <option value="servicio">Servicio</option>
            </select>
          </label>
        </div>
        <div class="col-sm-12 col-md-6">
          <label>Marca:*
			<select name="marca" class="form-control" required>
              <option value="">Selecciona</option>
              <option value="Shineray">Shineray</option>
              <option value="SWM">SWM</option>
              <option value="Soueast">Soueast</option>
              <option value="Faw">Faw</option>
              <option value="Zotye">Zotye</option>
            </select>
		   </label>
        </div>
        <div class="col-sm-12 col-md-6">
          <label>Establecimiento:* <input type="text" class="form-control" name="local" required></label>
        </div>
        <div class="col-sm-12 col-md-6">
          <label>Área:*
			<select name="area" class="form-control" required>
              <option value="">Selecciona</option>
              <option value="Comercial">Comercial</option>
              <option value="Postventa">Postventa</option>
              <option value="Venta de repuestos">Venta de repuestos</option>
            </select>	
		  </label>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12 col-md-6">
          <label>Tipo de moneda:*
            <select name="tipo_moneda" class="form-control" required>
              <option value="">Selecciona</option>
              <option value="PEN">Soles</option>
              <option value="USD">Dólares</option>
            </select>
          </label>
        </div>
        <div class="col-sm-12 col-md-6">
          <label>Monto reclamado:* <input type="number" name="monto" class="form-control" required step="0.01"></label>
        </div>
      </div>
    </div>

    <!-- Paso 3: Detalles del reclamo -->
    <div class="step step-3">
      <h3>Paso 3: Detalles del reclamo</h3>

      <div class="row">
        <div class="col-sm-12 col-md-6">
          <label>Tipo de reclamo:* 
            <select name="tipo_reclamo" class="form-control" required>
              <option value="">Selecciona</option>
              <option value="reclamo">Reclamo</option>
              <option value="queja">Queja</option>
            </select>
          </label>
        </div>
        <div class="col-sm-12 col-md-6">
          <label>Detalle del reclamo:* 
            <textarea name="detalle" class="form-control" maxlength="950" required></textarea>
          </label>
        </div>
        <div class="col-sm-12 col-md-6">
          <label>Pedido del consumidor:* 
            <textarea name="pedido" class="form-control" maxlength="950" required></textarea>
          </label>
        </div>
        <!--<div class="col-sm-12 col-md-6">
          <label>Fecha de respuesta esperada:* 
            <input type="date" class="form-control" name="fecha_respuesta" required>
          </label>
        </div>-->
      </div>
    </div>

    <!-- Paso 4: Confirmación -->
    <div class="step step-4">
      <h3>Paso 4: Confirmación</h3>
      <label>
        <input type="checkbox" required> Declaro que la información proporcionada es veraz.
      </label>
      <p>La empresa tiene un plazo de hasta 15 días hábiles para dar respuesta.</p>
    </div>

    <!-- Controles -->
    <div class="form-navigation">
      <button type="button" class="prev btn">Anterior</button>
      <button type="button" class="next btn">Siguiente</button>
      <button type="submit" class="submit btn">Enviar</button>
    </div>
  </form>
</div>

<script>

jQuery(document).ready(function($) {
  let current = 0;
  const steps = $('.step');
  const totalSteps = steps.length;

  function updateProgressBar(step) {
    const percent = ((step + 1) / totalSteps) * 100;
    $('#progress').css('width', `${percent}%`);
  }

  function showStep(index) {
	  $('.text-step').text(index+1);
    updateProgressBar(index);
    steps.removeClass('active').eq(index).addClass('active');
    $('.prev').toggle(index > 0).prop('disabled', index === 0);
    $('.next').toggle(index < totalSteps - 1);
    $('.submit').toggle(index === totalSteps - 1);
  }

  showStep(current);

  $('.next').click(() => {
    const currentStepFields = steps.eq(current).find('input, select, textarea').toArray();
    for (const field of currentStepFields) {
      if (!field.checkValidity()) {
        field.reportValidity();
        return;
      }
    }
    current++;
    showStep(current);
  });

  $('.prev').click(() => {
    if (current > 0) {
      current--;
      showStep(current);
    }
  });

  $('#caldera-form').submit(function(e) {
    e.preventDefault();
    $.post({
      url: caldera_form_ajax.ajax_url,
      data: $(this).serialize() + '&action=caldera_form_submit',
      success: function(res) {
        alert(res.data.message);
        $('#caldera-form')[0].reset();
        current = 0;
        showStep(current);
      }
    });
  });
});

</script>
