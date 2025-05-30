<h3>👤 Detalle del usuario</h3>
<pre style="background-color:#fff8dc; padding:10px; border:1px solid #ccc;">
<?php print_r($user); ?>
</pre>

<h3>📋 Lista de roles para select</h3>
<pre style="background-color:#f0f8ff; padding:10px; border:1px solid #ccc;">
<?php print_r($roles); ?>
</pre>

<h3>📂 Lista de especialidades para medicos</h3>
<pre style="background-color:rgb(255, 230, 255); padding: 10px; border: 1px solidrgb(255, 179, 249);">
<?php print_r($especialidades); ?>
</pre>



<?php if ($user['role_id'] == 2):
    // Si es medico entonces mostramos ver horario
    ?>
    <p>
        <a href="<?= base_url('horarios/' . $user['id'] . '/medico') ?>"
            style="padding: 8px 12px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;">➕
            Ver mis horarios</a>
    </p>
<?php else: ?>
    <p>No aplica el horario.</p>
<?php endif; ?>