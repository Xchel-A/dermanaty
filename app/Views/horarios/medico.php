<h3>👤 Detalle del medico</h3>
<pre style="background-color:#fff8dc; padding:10px; border:1px solid #ccc;">
<?php print_r($user); ?>
</pre>



<h3>📋 Lista de horarios (datos completos)</h3>
<pre style="background-color: #f4f4f4; padding: 10px; border: 1px solid #ccc;">
<?php print_r($horarios); ?>
</pre>

<?php if (!empty($horarios)): ?>
 <p>
        <a href="<?= base_url('horarios/' . $horarios[0]['medico_id'] . '/new') ?>" style="padding: 8px 12px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;">➕ Nuevo horario</a>
    </p>
    <p>
        <a href="<?= base_url('horarios/' . $horarios[0]['id'] . '/edit') ?>" style="padding: 8px 12px; background-color: #f0ad4e; color: white; text-decoration: none; border-radius: 4px;">✏️ Editar primer horario</a>
    </p>
<?php else: ?>
    <p>No hay horarios registrados.</p>
<?php endif; ?>
