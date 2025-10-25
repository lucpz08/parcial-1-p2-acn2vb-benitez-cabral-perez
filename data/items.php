<?php
// Array asociativo con los ítems (GOTY 2025)
$items = array (
  0 => 
  array (
    'id' => 1,
    'titulo' => 'Astro Bot',
    'categoria' => 'Plataformas',
    'descripcion' => 'Un encantador juego de plataformas en 3D con creatividad y humor.',
    'imagen' => 'assets/images/astro.jpg',
  ),
  1 => 
  array (
    'id' => 4,
    'titulo' => 'Elden Ring: Shadow of the Erdtree',
    'categoria' => 'Acción/RPG',
    'descripcion' => 'Expansión del aclamado Elden Ring que amplía la historia y los desafíos.',
    'imagen' => 'assets/images/eldenring.jpg',
  ),
  2 => 
  array (
    'id' => 6,
    'titulo' => 'Metaphor: ReFantazio',
    'categoria' => 'RPG',
    'descripcion' => 'Nuevo JRPG de los creadores de Persona, con un mundo de fantasía único.',
    'imagen' => 'assets/images/metaphor.jpg',
  ),
  3 => 
  array (
    'id' => 7,
    'titulo' => 'Stellar Blade',
    'categoria' => 'Acción/Aventura',
    'descripcion' => 'Juego de acción futurista con combates estilizados y narrativa intensa.',
    'imagen' => 'assets/images/stellarblade.jpg',
  ),
  4 => 
  array (
    'id' => 8,
    'titulo' => 'Like a Dragon: Infinite Wealth',
    'categoria' => 'RPG',
    'descripcion' => 'Nueva entrega de la saga Yakuza con historia cargada de humor y drama.',
    'imagen' => 'assets/images/likeadragon.jpg',
  ),
  5 => 
  array (
    'id' => 12,
    'titulo' => 'Black Myth: Wukong',
    'categoria' => 'RPG',
    'descripcion' => 'Un RPG de acción ambientado en el mundo de la mitología china, basado en la novela clásica Viaje al Oeste. El juego te pone en la piel del "Predestinado", un monje que emprende un viaje lleno de peligros para descubrir la verdad detrás de una antigua leyenda. Se caracteriza por su sistema de combate ofensivo, la posibilidad de personalizar habilidades y hechizos, y su fidelidad visual inspirada en la novela.',
    'imagen' => 'assets/images/uploads/game_68fc2fc5e5b812.88811489.jpg',
  ),
  6 => 
  array (
    'id' => 13,
    'titulo' => 'Final fantasy vii Rebirth',
    'categoria' => 'Acción/RPG',
    'descripcion' => 'Es el segundo juego de la trilogía de remake de Final Fantasy VII, que continúa la historia de Cloud y sus compañeros tras escapar de Midgar en busca de Sefirot.',
    'imagen' => 'assets/images/uploads/game_68fc3013026022.26682839.jpg',
  ),
  7 => 
  array (
    'id' => 14,
    'titulo' => 'Balatro',
    'categoria' => 'Estrategia',
    'descripcion' => 'Es un videojuego de construcción de mazos roguelike inspirado en el póquer, donde los jugadores deben crear manos de póquer combinándolas con comodines y efectos para ganar puntuaciones.',
    'imagen' => 'assets/images/uploads/game_68fc3051a74229.93300605.jpg',
  ),
);

// Lista de categorías derivadas del array
$categorias = array_values(array_unique(array_map(function ($i) {
    return $i['categoria'];
}, $items)));

// Retornar el array para que funcione con include()
return $items;