<h3>📋 Lista de expedientes (datos completos)</h3>
<pre style="background-color: #f4f4f4; padding: 10px; border: 1px solid #ccc;">
<?php print_r($expedientes); ?>
</pre>

<?php if (!empty($expedientes)): ?>
    <p>
        <a href="<?= base_url('expedientes/' . $expedientes[0]['id'] . '/detalles') ?>" style="padding: 8px 12px; background-color:rgb(78, 181, 240); color: white; text-decoration: none; border-radius: 4px;">Detalle expediente</a>
    </p>
<?php else: ?>
    <p>No hay expedientes registrados.</p>
<?php endif; ?>
