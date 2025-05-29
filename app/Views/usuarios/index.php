<h3>📋 Lista de usuarios (datos completos)</h3>
<pre style="background-color: #f4f4f4; padding: 10px; border: 1px solid #ccc;">
<?php print_r($users); ?>
</pre>

<?php if (!empty($users)): ?>
    <p>
        <a href="<?= base_url('usuarios/new') ?>" style="padding: 8px 12px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;">➕ Nuevo usuario</a>
    </p>
    <p>
        <a href="<?= base_url('usuarios/' . $users[0]['id'] . '/edit') ?>" style="padding: 8px 12px; background-color: #f0ad4e; color: white; text-decoration: none; border-radius: 4px;">✏️ Editar primer usuario</a>
    </p>
<?php else: ?>
    <p>No hay usuarios registrados.</p>
<?php endif; ?>
