<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $people = [
            // name, email, gender, birth_date, city, bio, interests (nombres)
            ['Valentina Torres',  'valen@demo.com',    'female',     '1999-03-14', 'Bogotá',     'Amante del café, los libros y los atardeceres. Busco conversaciones que inspiren.',         ['Café','Lectura','Fotografía','Arte','Viajes']],
            ['Mateo Ramírez',     'mateo@demo.com',    'male',       '1997-07-22', 'Medellín',   'Ingeniero de día, músico de noche. Fan del fútbol y las salidas al campo.',                ['Música','Fútbol','Tecnología','Camping','Gaming']],
            ['Isabella Gómez',    'isa@demo.com',      'female',     '2000-11-05', 'Cali',       'Bailarina y cocinera entusiasta. Me encanta explorar restaurantes nuevos.',                 ['Baile','Cocina','Fitness','Música','Viajes']],
            ['Sebastián Castro',  'seba@demo.com',     'male',       '1996-04-18', 'Barranquilla','Diseñador gráfico, viajero compulsivo. Vivo con mi cámara en la mano.',                  ['Diseño','Fotografía','Viajes','Arte','Conciertos']],
            ['Camila Herrera',    'cami@demo.com',     'female',     '2001-08-30', 'Bogotá',     'Estudiante de psicología. Me apasiona el bienestar mental y el yoga.',                    ['Yoga','Psicología','Lectura','Fitness','Naturaleza']],
            ['Daniel Moreno',     'dani@demo.com',     'male',       '1998-01-09', 'Cartagena',  'Chef aficionado y amante del mar. Fin de semana perfecto = playa + buena música.',        ['Cocina','Aventura','Música','Natación','Senderismo']],
            ['Mariana Díaz',      'mari@demo.com',     'female',     '1995-06-25', 'Medellín',   'Emprendedora en el mundo de la moda. Busco personas con metas claras.',                  ['Moda','Emprendimiento','Networking','Viajes','Fotografía']],
            ['Andrés Vargas',     'andres@demo.com',   'male',       '1999-12-03', 'Bogotá',     'Desarrollador de software. Geek sin vergüenza, curioso por naturaleza.',                  ['Programación','Gaming','Tecnología','Ciencia','Podcasts']],
            ['Luciana Pérez',     'luci@demo.com',     'female',     '2000-05-17', 'Cúcuta',     'Comunicadora social. Me muevo entre festivales, libros y cafés con wifi.',                ['Escritura','Arte','Festivales','Café','Historia']],
            ['Felipe Sánchez',    'feli@demo.com',     'male',       '1997-09-28', 'Manizales',  'Ciclista de montaña y amante del café (somos paisas). Vivo la vida al máximo.',          ['Ciclismo','Café','Senderismo','Naturaleza','Aventura']],
            ['Sofía Reyes',       'sofi@demo.com',     'female',     '1998-02-11', 'Bogotá',     'Bióloga marina, activista ambiental. Si amas los animales, somos compatibles.',           ['Naturaleza','Animales','Ciencia','Senderismo','Fotografía de viajes']],
            ['Juan Martínez',     'juan@demo.com',     'male',       '2001-10-22', 'Pereira',    'Estudiante de derecho y gamer empedernido. Debate o partida, tú eliges.',                 ['Gaming','Derecho','Filosofía','Series','Tecnología']],
            ['Gabriela López',    'gaby@demo.com',     'female',     '1996-07-08', 'Bogotá',     'Nutricionista. La comida saludable puede ser deliciosa, te lo demuestro.',                ['Comida saludable','Fitness','Yoga','Cocina','Bienestar']],
            ['Ricardo Torres',    'ricky@demo.com',    'male',       '1994-03-30', 'Barranquilla','Músico y productor. El carnaval corre por mis venas, la música por todo lo demás.',     ['Música','Baile','Conciertos','Arte','Fotografía']],
            ['Natalia Guzmán',    'nati@demo.com',     'female',     '2002-01-19', 'Medellín',   'Artista plástica. Pinto, esculpo y veo el mundo diferente. Busco mi musa.',              ['Arte','Pintura','Museos','Café','Fotografía']],
            ['Miguel Ángel Ruiz', 'miguel@demo.com',   'male',       '1999-11-14', 'Bogotá',     'Fotógrafo de bodas y viajero. Tengo pasaporte sellado en 20 países.',                    ['Fotografía','Viajes','Aventura','Fotografía de viajes','Naturaleza']],
            ['Alejandra Mora',    'ale@demo.com',      'female',     '1997-04-06', 'Cali',       'Instructora de fitness y nutrición. El deporte es mi terapia.',                          ['Fitness','Yoga','Natación','Comida saludable','Baile']],
            ['Tomás Quintero',    'tomas@demo.com',    'male',       '2000-08-12', 'Bogotá',     'Emprendedor tech. Construyo startups de día y leo filosofía de noche.',                  ['Emprendimiento','Tecnología','Programación','Filosofía','Startups']],
            ['Daniela Ríos',      'dani.rios@demo.com','female',     '1998-06-01', 'Armenia',    'Barista certificada y sommelier de café. Ven, te cuento el mundo en una taza.',          ['Café','Viajes','Escritura','Fotografía','Historia']],
            ['Santiago Mejía',    'santi@demo.com',    'male',       '1995-12-25', 'Medellín',   'Ingeniero ambiental. Amo la montaña, el silencio y la gente auténtica.',                 ['Naturaleza','Senderismo','Camping','Ciclismo','Aventura']],
        ];

        $allInterests = DB::table('interests')->pluck('id', 'name');

        foreach ($people as $person) {
            [$name, $email, $gender, $birth, $city, $bio, $interestNames] = $person;

            // Evitar duplicados
            if (DB::table('users')->where('email', $email)->exists()) {
                continue;
            }

            $initial = strtoupper(substr($name, 0, 1));
            $colors   = ['E8375A', 'F59E0B', '6366F1', '10B981', '3B82F6', 'EC4899', '8B5CF6', 'EF4444'];
            $color    = $colors[array_rand($colors)];
            $avatar   = "https://ui-avatars.com/api/?name=".urlencode($name)."&background={$color}&color=fff&size=300&bold=true";

            // Crear usuario (solo columnas que existen en users tras la migración)
            $userId = DB::table('users')->insertGetId([
                'name'              => $name,
                'email'             => $email,
                'password'          => Hash::make('password'),
                'avatar'            => $avatar,
                'email_verified_at' => now(),
                'created_at'        => now()->subDays(rand(1, 60)),
                'updated_at'        => now(),
            ]);

            // Crear perfil completo
            DB::table('profiles')->insert([
                'user_id'           => $userId,
                'bio'               => $bio,
                'city'              => $city,
                'birth_date'        => $birth,
                'gender'            => $gender,
                'profile_completed' => true,
                'onboarding_step'   => 5,
                'looking_for'       => json_encode(['friendship', 'relationship']),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // Asignar intereses
            foreach ($interestNames as $iName) {
                if (isset($allInterests[$iName])) {
                    DB::table('interest_user')->insertOrIgnore([
                        'user_id'     => $userId,
                        'interest_id' => $allInterests[$iName],
                    ]);
                }
            }
        }

        $this->command->info('✅ 20 usuarios de prueba creados con perfiles completos.');
    }
}
