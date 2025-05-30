<h3>👤 Detalle del paciente</h3>
<pre style="background-color:#fff8dc; padding:10px; border:1px solid #ccc;">
<?php print_r($paciente); ?>
</pre>


<h3>👤 Expedientes del paciente</h3>
<pre style="background-color:#fff8dc; padding:10px; border:1px solid #ccc;">
<?php print_r($expedientes); ?>
</pre>

<a href="<?= base_url('expedientes/' . $expedientes[0]['id'] . '/detalles') ?>" style="padding: 8px 12px; background-color: #f0ad4e; color: white; text-decoration: none; border-radius: 4px;">Ver expediente</a>