<div></div><h1>Medico Dashboard</h1>

<a href="<?= site_url('expedientes/' . $medicoId . '/medico') ?>" class="btn btn-primary">Expedientes</a>


<a href="<?= site_url('usuarios/create') ?>" class="btn btn-primary">Agenda</a>

<a href="<?= site_url('usuarios/create') ?>" class="btn btn-primary">Perfil</a>

<h1>Datos sesion</h1>
<?php
echo '<pre>';
print_r(session()->get());
echo '</pre>';


?>
