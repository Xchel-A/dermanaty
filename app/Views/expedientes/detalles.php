<h3>📋 Expediente (datos completos)</h3>
<pre style="background-color: #f4f4f4; padding: 10px; border: 1px solid #ccc;">
<?php print_r($expediente); ?>
</pre>


<h3>📋 Consultas (datos completos)</h3>
<pre style="background-color:rgb(216, 102, 102); padding: 10px; border: 1px solid #ccc;">
<?php print_r($consultas); ?>
</pre>

<p>
        <a href="<?= base_url('consultas/' . $expediente['id'] . '/detalles') ?>" style="padding: 8px 12px; background-color:rgb(78, 181, 240); color: white; text-decoration: none; border-radius: 4px;">Detalle consulta</a>
    </p>