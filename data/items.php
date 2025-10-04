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
);

// Lista de categorías derivadas del array
$categorias = array_values(array_unique(array_map(function ($i) {
    return $i['categoria'];
}, $items)));

// Retornar el array para que funcione con include()
return $items;