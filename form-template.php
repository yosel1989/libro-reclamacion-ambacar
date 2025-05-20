<div class="caldera-form">
  <form id="caldera-form">
    <!-- Paso 1: Datos del consumidor -->
    <div class="step step-1 active">
      <h3>Paso 1: Datos del consumidor</h3>
      <label>Nombres:* <input type="text" name="nombres" required></label>
      <label>Apellidos:* <input type="text" name="apellidos" required></label>
      <label>Tipo de documento:* 
        <select name="tipo_doc" required>
          <option value="">Selecciona</option>
          <option value="DNI">DNI</option>
          <option value="CE">CE</option>
        </select>
      </label>
      <label>Número de documento:* <input type="text" name="numero_doc" required></label>
      <label>Correo electrónico:* <input type="email" name="correo" required></label>
      <label>Teléfono: <input type="text" name="telefono"></label>
      <label>Distrito:* <input type="text" name="distrito" required></label>
      <label>Dirección:* <input type="text" name="direccion" required></label>
      <label>¿Es menor de edad?:*
        <input type="checkbox" name="menor_edad" value="1"> Sí
      </label>
    </div>

    <!-- Paso 2: Datos del bien contratado -->
    <div class="step step-2">
      <h3>Paso 2: Bien o servicio contratado</h3>
      <label>Tipo:* 
        <select name="tipo_bien" required>
          <option value="">Selecciona</option>
          <option value="producto">Producto</option>
          <option value="servicio">Servicio</option>
        </select>
      </label>
      <label>Marca:* <input type="text" name="marca" required></label>
      <label>Establecimiento:* <input type="text" name="local" required></label>
      <label>Área:* <input type="text" name="area" required></label>
      <label>Tipo de moneda:*
        <select name="tipo_moneda" required>
          <option value="">Selecciona</option>
          <option value="PEN">Soles</option>
          <option value="USD">Dólares</option>
        </select>
      </label>
      <label>Monto reclamado:* <input type="number" name="monto" required step="0.01"></label>
    </div>

    <!-- Paso 3: Detalles del reclamo -->
    <div class="step step-3">
      <h3>Paso 3: Detalles del reclamo</h3>
      <label>Tipo de reclamo:* 
        <select name="tipo_reclamo" required>
          <option value="">Selecciona</option>
          <option value="reclamo">Reclamo</option>
          <option value="queja">Queja</option>
        </select>
      </label>
      <label>Detalle del reclamo:* 
        <textarea name="detalle" maxlength="950" required></textarea>
      </label>
      <label>Pedido del consumidor:* 
        <textarea name="pedido" maxlength="950" required></textarea>
      </label>
      <label>Fecha de respuesta esperada:* 
        <input type="date" name="fecha_respuesta" required>
      </label>
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

  function showStep(index) {
    steps.removeClass('active').eq(index).addClass('active');
    $('.prev').toggle(index > 0);
    $('.next').toggle(index < totalSteps - 1);
    $('.submit').toggle(index === totalSteps - 1);
  }

  showStep(current);

  $('.next').click(() => {
    if ($('#caldera-form')[0].checkValidity()) {
      current++;
      showStep(current);
    } else {
      $('#caldera-form')[0].reportValidity();
    }
  });

  $('.prev').click(() => {
    current--;
    showStep(current);
  });

  $('#caldera-form').submit(function(e) {
    e.preventDefault();
    $.post({
      url: caldera_form_ajax.ajax_url,
      data: $(this).serialize() + '&action=caldera_form_submit',
      success: function(res) {
        alert(res.data);
        $('#caldera-form')[0].reset();
        current = 0;
        showStep(current);
      }
    });
  });
});
</script>
