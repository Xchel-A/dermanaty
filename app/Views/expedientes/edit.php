<h3>👤 Detalle del paciente</h3>
<pre style="background-color:#fff8dc; padding:10px; border:1px solid #ccc;">
<?php print_r($paciente); ?>
</pre>


<a href="<?= base_url('usuarios/' . $users[0]['id'] . '/edit') ?>" style="padding: 8px 12px; background-color: #f0ad4e; color: white; text-decoration: none; border-radius: 4px;">✏️ Editar primer usuario</a>