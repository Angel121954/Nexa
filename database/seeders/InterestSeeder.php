<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InterestSeeder extends Seeder
{
    public function run(): void
    {
        $interests = [
            // Arte y creatividad
            'Arte', 'Fotografía', 'Diseño', 'Música', 'Cine', 'Teatro', 'Escritura', 'Pintura',
            // Tecnología
            'Tecnología', 'Programación', 'Gaming', 'Inteligencia Artificial', 'Startups',
            // Deportes y bienestar
            'Fitness', 'Yoga', 'Fútbol', 'Natación', 'Ciclismo', 'Senderismo', 'Baile',
            // Gastronomía
            'Cocina', 'Café', 'Vinos', 'Comida saludable', 'Repostería',
            // Viajes y aventura
            'Viajes', 'Aventura', 'Naturaleza', 'Camping', 'Fotografía de viajes',
            // Cultura y aprendizaje
            'Lectura', 'Historia', 'Idiomas', 'Psicología', 'Filosofía', 'Ciencia',
            // Social
            'Emprendimiento', 'Networking', 'Voluntariado', 'Moda', 'Animales',
            // Entretenimiento
            'Series', 'Podcasts', 'Conciertos', 'Festivales',
        ];

        $rows = array_map(fn($name) => ['name' => $name], $interests);

        foreach ($rows as $row) {
            DB::table('interests')->updateOrInsert(['name' => $row['name']], $row);
        }
    }
}
