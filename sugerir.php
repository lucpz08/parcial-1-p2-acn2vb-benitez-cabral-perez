<?php
require __DIR__ . '/data/items.php';
require __DIR__ . '/inc/functions.php';

$tema = isset($_GET['tema']) && $_GET['tema'] === 'oscuro' ? 'oscuro' : 'claro';
$mensaje = "";
$tipoMensaje = ""; // success o error

// Crear carpeta de uploads si no existe
$uploadDir = __DIR__ . '/assets/images/uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

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

// Validar y procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST["titulo"] ?? "");
    $categoria = trim($_POST["categoria"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    
    // Variable para la ruta de la imagen
    $imagenPath = 'assets/images/placeholder.jpg'; // Por defecto

    if ($titulo === "" || $categoria === "" || $descripcion === "") {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
        $tipoMensaje = "error";
    } else {
        // Procesar imagen si se subió
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $archivo = $_FILES['imagen'];
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            // Validar extensión
            if (in_array($extension, $extensionesPermitidas)) {
                // Validar tamaño (máximo 5MB)
                if ($archivo['size'] <= 5 * 1024 * 1024) {
                    // Generar nombre único
                    $nombreArchivo = uniqid('game_', true) . '.' . $extension;
                    $rutaDestino = $uploadDir . $nombreArchivo;
                    
                    // Mover archivo
                    if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                        $imagenPath = 'assets/images/uploads/' . $nombreArchivo;
                    } else {
                        $mensaje = "⚠️ Error al subir la imagen. Usando imagen por defecto.";
                        $tipoMensaje = "error";
                    }
                } else {
                    $mensaje = "⚠️ La imagen es demasiado grande (máx 5MB). Usando imagen por defecto.";
                    $tipoMensaje = "error";
                }
            } else {
                $mensaje = "⚠️ Formato de imagen no válido. Usando imagen por defecto.";
                $tipoMensaje = "error";
            }
        }

        // Si no hubo errores graves, agregar el juego
        if ($tipoMensaje !== "error" || $imagenPath !== 'assets/images/placeholder.jpg') {
            // Generar nuevo ID (máximo actual + 1)
            $maxId = 0;
            foreach ($items as $item) {
                if ($item['id'] > $maxId) {
                    $maxId = $item['id'];
                }
            }
            $nuevoId = $maxId + 1;

            // Crear nuevo juego con la estructura correcta
            $nuevoJuego = [
                'id' => $nuevoId,
                'titulo' => $titulo,
                'categoria' => $categoria,
                'descripcion' => $descripcion,
                'imagen' => $imagenPath
            ];

            // Agregar a la lista
            $items[] = $nuevoJuego;

            // Guardar cambios en items.php
            $contenido = "<?php\n// Array asociativo con los ítems (GOTY 2025)\n\$items = " . var_export($items, true) . ";\n\n// Lista de categorías derivadas del array\n\$categorias = array_values(array_unique(array_map(function (\$i) {\n    return \$i['categoria'];\n}, \$items)));\n\n// Retornar el array para que funcione con include()\nreturn \$items;";
            
            if (file_put_contents(__DIR__ . '/data/items.php', $contenido)) {
                $mensaje = "✅ Juego agregado con éxito: $titulo";
                $tipoMensaje = "success";
                // Recargar items y categorías
                $items = include(__DIR__ . '/data/items.php');
                $categorias = array_values(array_unique(array_map(function ($i) {
                    return $i['categoria'];
                }, $items)));
            } else {
                $mensaje = "❌ Error al guardar el juego. Verifica los permisos del archivo.";
                $tipoMensaje = "error";
            }
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sugerir Juego - GOTY 2025</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/sugerir.css">
</head>
<body class="<?php echo $tema === 'oscuro' ? 'tema-oscuro' : ''; ?>">
    <div class="container">
        <div class="header">
            <h1>Sugerir un nuevo juego</h1>
            <div>
                <a href="sugerir.php?tema=claro">Tema claro</a> | <a href="sugerir.php?tema=oscuro">Tema oscuro</a>
            </div>
        </div>

        <a href="index.php?tema=<?php echo $tema; ?>" class="back-link">← Volver al listado</a>

        <?php if ($mensaje): ?>
            <div class="mensaje <?php echo $tipoMensaje; ?>">
                <?php echo e($mensaje); ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="sugerir.php?tema=<?php echo $tema; ?>" class="suggestion-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="titulo">Título del juego:</label>
                    <input type="text" id="titulo" name="titulo" required placeholder="Ej: The Last of Us Part III">
                </div>

                <div class="form-group">
                    <label for="categoria">Categoría:</label>
                    <select id="categoria" name="categoria" required>
                        <option value="">-- Selecciona una categoría --</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo e($cat); ?>"><?php echo e($cat); ?></option>
                        <?php endforeach; ?>
                        <option value="Aventura">Aventura</option>
                        <option value="Estrategia">Estrategia</option>
                        <option value="Deportes">Deportes</option>
                        <option value="Horror">Horror</option>
                        <option value="Simulación">Simulación</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="4" required placeholder="Describe brevemente el juego..."></textarea>
                </div>

                <div class="form-group">
                    <label for="imagen">Imagen del juego:</label>
                    <div class="file-upload-wrapper">
                        <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" class="file-input">
                        <label for="imagen" class="file-label">
                            <span class="file-icon">📁</span>
                            <span class="file-text">Seleccionar imagen (opcional)</span>
                        </label>
                        <div class="file-info">
                            Formatos: JPG, PNG, GIF, WEBP • Máximo: 5MB
                        </div>
                    </div>
                    <div id="preview-container" class="preview-container" style="display: none;">
                        <img id="preview-image" src="" alt="Preview">
                    </div>
                </div>

                <button type="submit" class="btn-primary">Agregar Juego</button>
            </form>
        </div>

        <div class="games-list">
            <h2>Juegos actuales (<?php echo count($items); ?>)</h2>
            <div class="cards">
                <?php foreach ($items as $it): ?>
                    <div class="card">
                        <a href="item.php?id=<?php echo $it['id']; ?>&tema=<?php echo $tema; ?>">
                            <img src="<?php echo e($it['imagen']); ?>" alt="<?php echo e($it['titulo']); ?>">
                        </a>
                        <div class="body">
                            <div class="cat"><?php echo e($it['categoria']); ?></div>
                            <h3 class="title"><?php echo e($it['titulo']); ?></h3>
                            <p><?php echo e($it['descripcion']); ?></p>
                            <button class="btn-delete" onclick="confirmarEliminacion(<?php echo $it['id']; ?>, '<?php echo e($it['titulo']); ?>')">
                                🗑️ Eliminar
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
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
            btnConfirmar.href = `sugerir.php?eliminar=${id}&tema=<?php echo $tema; ?>`;
            modal.style.display = 'flex';
        }

        function cerrarModal() {
            modal.style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            if (event.target == modal) {
                cerrarModal();
            }
        }

        // Cerrar modal con ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                cerrarModal();
            }
        });

        // Preview de imagen antes de subir
        const inputImagen = document.getElementById('imagen');
        const previewContainer = document.getElementById('preview-container');
        const previewImage = document.getElementById('preview-image');
        const fileLabel = document.querySelector('.file-text');

        inputImagen.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Actualizar texto del label
                fileLabel.textContent = file.name;
                
                // Mostrar preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                fileLabel.textContent = 'Seleccionar imagen (opcional)';
                previewContainer.style.display = 'none';
            }
        });
    </script>
</body>
</html>