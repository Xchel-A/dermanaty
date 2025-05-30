<h3>📋 Lista de pacientes (datos completos)</h3>
<pre style="background-color: #f4f4f4; padding: 10px; border: 1px solid #ccc;">
<?php print_r($pacientes); ?>
</pre>

<?php if (!empty($pacientes)): ?>
    <p>
        <a  href="<?= base_url('pacientes/' . $pacientes[0]['id']. '/new') ?>" style="padding: 8px 12px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;">➕ Nuevo paciente</a>
    </p>
    <p>
        <a href="<?= base_url('pacientes/' . $pacientes[0]['id'] . '/edit') ?>" style="padding: 8px 12px; background-color: #f0ad4e; color: white; text-decoration: none; border-radius: 4px;">Detalle primer paciente</a>
    </p>
<?php else: ?>
    <p>No hay pacientes registrados.</p>
<?php endif; ?>
