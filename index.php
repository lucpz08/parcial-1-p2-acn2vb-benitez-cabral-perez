<?php
require __DIR__ . '/data/items.php';
require __DIR__ . '/inc/functions.php';

// Parámetros GET
$search = isset($_GET['q']) ? $_GET['q'] : '';
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$tema = isset($_GET['tema']) && $_GET['tema'] === 'oscuro' ? 'oscuro' : 'claro';

$mensaje = "";
$tipoMensaje = "";

// Procesar eliminación de juego
if (isset($_GET['eliminar'])) {
    $idEliminar = intval($_GET['eliminar']);
    $juegoEliminado = null;

    // Buscar y eliminar el juego
    foreach ($items as $key => $item) {
        if ($item['id'] == $idEliminar) {
            $juegoEliminado = $item;

            // Eliminar imagen si no es placeholder
            if ($item['imagen'] !== 'assets/images/placeholder.jpg' && file_exists(__DIR__ . '/' . $item['imagen'])) {
                unlink(__DIR__ . '/' . $item['imagen']);
            }

            unset($items[$key]);
            break;
        }
    }

    if ($juegoEliminado) {
        // Reindexar array
        $items = array_values($items);

        // Guardar cambios
        $contenido = "<?php\n// Array asociativo con los ítems (GOTY 2025)\n\$items = " . var_export($items, true) . ";\n\n// Lista de categorías derivadas del array\n\$categorias = array_values(array_unique(array_map(function (\$i) {\n    return \$i['categoria'];\n}, \$items)));\n\n// Retornar el array para que funcione con include()\nreturn \$items;";

        if (file_put_contents(__DIR__ . '/data/items.php', $contenido)) {
            $mensaje = "🗑️ Juego eliminado: " . $juegoEliminado['titulo'];
            $tipoMensaje = "success";
            // Recargar datos
            $items = include(__DIR__ . '/data/items.php');
            $categorias = array_values(array_unique(array_map(function ($i) {
                return $i['categoria'];
            }, $items)));
        } else {
            $mensaje = "❌ Error al eliminar el juego.";
            $tipoMensaje = "error";
        }
    }
}

$results = filtrar_items($items, $search, $categoria);

?><!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GOTY 2025 - Lista</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body class="<?php echo $tema === 'oscuro' ? 'tema-oscuro' : ''; ?>">
    <div class="container">
        <div class="header">
            <h1>G.O.T.Y. 2025</h1>
            <div>
                <a href="index.php?tema=claro">Tema claro</a> | <a href="index.php?tema=oscuro">Tema oscuro</a>
                &nbsp;|&nbsp;<a href="sugerir.php?tema=<?php echo $tema; ?>">Sugerir un juego</a>
            </div>
        </div>

        <?php if ($mensaje): ?>
            <div class="mensaje <?php echo $tipoMensaje; ?>">
                <?php echo e($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario para buscar por categorias -->
        <form method="GET" class="search-form">
            <input type="text" name="q" placeholder="Buscar por nombre..." value="<?php echo e($search); ?>">

            <select name="categoria">
                <option value="">-- Todas las categorías --</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo e($cat); ?>" <?php echo $categoria === $cat ? 'selected' : ''; ?>>
                        <?php echo e($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="hidden" name="tema" value="<?php echo $tema; ?>">
            <button type="submit">Buscar / Filtrar</button>
        </form>

        <p><?php if ($search || $categoria) {
            echo count($results) . ' resultado(s)';
        } else {
            echo 'Mostrando todos los ítems (' . count($results) . ')';
        } ?>
        </p>

        <!-- Modelo de tarjeta para cada juego -->
        <div class="cards">
            <?php foreach ($results as $it): ?>
                <div class="card">
                    <a href="item.php?id=<?php echo $it['id']; ?>&tema=<?php echo $tema; ?>">
                        <img src="<?php echo e($it['imagen']); ?>" alt="<?php echo e($it['titulo']); ?>">
                    </a>
                    <div class="body">
                        <div class="cat"><?php echo e($it['categoria']); ?></div>
                        <h3 class="title"><?php echo e($it['titulo']); ?></h3>
                        <p><?php echo e($it['descripcion']); ?></p>
                        <button class="btn-delete"
                            onclick="confirmarEliminacion(<?php echo $it['id']; ?>, '<?php echo addslashes($it['titulo']); ?>')">
                            🗑️ Eliminar
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div id="modal-confirmacion" class="modal">
        <div class="modal-content">
            <h2>⚠️ Confirmar eliminación</h2>
            <p id="modal-mensaje">¿Estás seguro de que deseas eliminar este juego?</p>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
                <a id="btn-confirmar" href="#" class="btn-confirm">Eliminar</a>
            </div>
        </div>
    </div>

    <script>
        // Modal de confirmación
        const modal = document.getElementById('modal-confirmacion');
        const modalMensaje = document.getElementById('modal-mensaje');
        const btnConfirmar = document.getElementById('btn-confirmar');

        function confirmarEliminacion(id, titulo) {
            modalMensaje.textContent = `¿Estás seguro de que deseas eliminar "${titulo}"?`;

            // Construir URL manteniendo los parámetros de búsqueda
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('eliminar', id);
            btnConfirmar.href = `index.php?${urlParams.toString()}`;

            modal.style.display = 'flex';
        }

        function cerrarModal() {
            modal.style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function (event) {
            if (event.target == modal) {
                cerrarModal();
            }
        }

        // Cerrar modal con ESC
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                cerrarModal();
            }
        });
    </script>
</body>

</html>