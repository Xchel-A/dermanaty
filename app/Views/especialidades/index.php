<h3>📋 Lista de especialidades (datos completos)</h3>
<pre style="background-color: #f4f4f4; padding: 10px; border: 1px solid #ccc;">
<?php print_r($especialidades); ?>
</pre>

<?php if (!empty($especialidades)): ?>
 <p>
        <a href="<?= base_url('especialidades/new') ?>" style="padding: 8px 12px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;">➕ Nueva especialidad</a>
    </p>
    <p>
        <a href="<?= base_url('especialidades/' . $especialidades[0]['id'] . '/edit') ?>" style="padding: 8px 12px; background-color: #f0ad4e; color: white; text-decoration: none; border-radius: 4px;">✏️ Editar primer especialidad</a>
    </p>
<?php else: ?>
    <p>No hay especialidades registrados.</p>
<?php endif; ?>
