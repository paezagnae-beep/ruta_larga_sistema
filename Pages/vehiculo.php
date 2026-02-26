<?php
class Conexion {
    private $servidor = "localhost";
    private $usuario = "root";
    private $clave = "";
    private $bd = "proyecto";
    protected $conexion;

    public function __construct() {
        $this->conectar();
    }

    protected function conectar() {
        $this->conexion = new mysqli($this->servidor, $this->usuario, $this->clave, $this->bd);
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
    }
}

class Cliente extends Conexion {
    private $id;
    private $rif;
    private $nombre;
    private $telefono;

    public function __construct() { parent::__construct(); }

    public function setId($v) { $this->id = $v; }
    public function setRif($v) { $this->rif = $v; }
    public function setNombre($v) { $this->nombre = $v; }
    public function setTelefono($v) { $this->telefono = $v; }

    public function listar() {
        return $this->conexion->query("SELECT * FROM clientes");
    }

    public function insertar() {
        $sql = "INSERT INTO clientes (RIF_cedula, nombre, telefono) VALUES ('$this->rif', '$this->nombre', '$this->telefono')";
        return $this->conexion->query($sql);
    }

    // MÉTODO PARA ACTUALIZAR
    public function modificar() {
        $sql = "UPDATE clientes SET RIF_cedula='$this->rif', nombre='$this->nombre', telefono='$this->telefono' WHERE ID_cliente=$this->id";
        return $this->conexion->query($sql);
    }

    public function eliminar($id_recibido) {
        return $this->conexion->query("DELETE FROM clientes WHERE ID_cliente = $id_recibido");
    }
}

$cliente = new Cliente();

// PROCESAR REGISTRO
if (isset($_POST['registrar'])) {
    $cliente->setRif($_POST['RIF_cedula']);
    $cliente->setNombre($_POST['nombre']);
    $cliente->setTelefono($_POST['telefono']);
    $cliente->insertar();
    header("Location: " . $_SERVER['PHP_SELF']);
}

// PROCESAR EDICIÓN
if (isset($_POST['editar'])) {
    $cliente->setId($_POST['ID_cliente']);
    $cliente->setRif($_POST['RIF_cedula']);
    $cliente->setNombre($_POST['nombre']);
    $cliente->setTelefono($_POST['telefono']);
    $cliente->modificar();
    header("Location: " . $_SERVER['PHP_SELF']);
}

// PROCESAR ELIMINACIÓN
if (isset($_GET['delete'])) {
    $cliente->eliminar($_GET['delete']);
    header("Location: " . $_SERVER['PHP_SELF']);
}

$result = $cliente->listar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Clientes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
</head>
<body>

<div class="container mt-4">
    <h3 class="text-center mb-4">Listado de Clientes</h3>
    
    <div class="mb-3 d-flex justify-content-between">
        <a href="menu.php" class="text-blue-600 hover:underline">← Volver al Menú</a>
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalRegistro">
            + Nuevo Cliente
        </button>
    </div>

    <table id="example" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>RIF / Cédula</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($fila = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $fila['ID_cliente'] ?></td>
                <td><?= $fila['RIF_cedula'] ?></td>
                <td><?= $fila['nombre'] ?></td>
                <td><?= $fila['telefono'] ?></td>
                <td>
                    <button class="btn btn-info btn-sm btnEditar" 
                            data-id="<?= $fila['ID_cliente'] ?>"
                            data-rif="<?= $fila['RIF_cedula'] ?>"
                            data-nombre="<?= $fila['nombre'] ?>"
                            data-tel="<?= $fila['telefono'] ?>"
                            data-toggle="modal" data-target="#modalEditar">Editar</button>

                    <a href="?delete=<?= $fila['ID_cliente'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Desea eliminar este registro?')">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalRegistro" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5>Registrar Cliente</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <form action="" method="POST">
          <div class="modal-body">
              <div class="form-group"><label>RIF / Cédula</label><input type="text" name="RIF_cedula" class="form-control" required></div>
              <div class="form-group"><label>Nombre</label><input type="text" name="nombre" class="form-control" required></div>
              <div class="form-group"><label>Teléfono</label><input type="text" name="telefono" class="form-control" required></div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button type="submit" name="registrar" class="btn btn-primary">Guardar Datos</button></div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5>Editar Cliente</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <form action="" method="POST">
          <div class="modal-body">
              <input type="hidden" name="ID_cliente" id="edit_id">
              <div class="form-group"><label>RIF / Cédula</label><input type="text" name="RIF_cedula" id="edit_rif" class="form-control" required></div>
              <div class="form-group"><label>Nombre</label><input type="text" name="nombre" id="edit_nombre" class="form-control" required></div>
              <div class="form-group"><label>Teléfono</label><input type="text" name="telefono" id="edit_telefono" class="form-control" required></div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button type="submit" name="editar" class="btn btn-info">Actualizar Datos</button></div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    $('#example').DataTable({
        language: { "sSearch": "Buscar:", "sLengthMenu": "Mostrar _MENU_ registros", "oPaginate": { "sNext": "Sig", "sPrevious": "Ant" } }
    });

    // SCRIPT PARA PASAR DATOS AL MODAL DE EDICIÓN
    $('.btnEditar').on('click', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_rif').val($(this).data('rif'));
        $('#edit_nombre').val($(this).data('nombre'));
        $('#edit_telefono').val($(this).data('tel'));
    });
});
</script>
</body>
</html>