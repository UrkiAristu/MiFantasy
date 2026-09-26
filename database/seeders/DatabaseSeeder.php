<?php

namespace Database\Seeders;

use App\Models\Alineacion;
use App\Models\Equipo;
use App\Models\Estadistica;
use App\Models\Jornada;
use App\Models\Jugador;
use App\Models\Liguilla;
use App\Models\Partido;
use App\Models\Plantilla;
use App\Models\Torneo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // -------------------------------------------------------------
        // 1. USUARIOS (1 Administrador y 4 Usuarios estándar)
        // -------------------------------------------------------------
        $this->command->info('Creando usuarios...');

        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.es'],
            [
                'name' => 'Administrador MiFantasy',
                'password' => Hash::make('password'),
                'admin' => true,
                'active' => true,
                'email_verified_at' => now(),
            ]
        );

        $usuariosEstandar = [
            ['name' => 'Carlos Fantasy', 'email' => 'carlos@mifantasy.com', 'equipo' => 'Galácticos FC'],
            ['name' => 'Laura Champions', 'email' => 'laura@mifantasy.com', 'equipo' => 'Tiki-Taka Stars'],
            ['name' => 'Mikel DreamTeam', 'email' => 'mikel@mifantasy.com', 'equipo' => 'Rojillos United'],
            ['name' => 'Elena FC', 'email' => 'elena@mifantasy.com', 'equipo' => 'Furia Verde'],
        ];

        $users = collect([$admin]);

        foreach ($usuariosEstandar as $datos) {
            $user = User::firstOrCreate(
                ['email' => $datos['email']],
                [
                    'name' => $datos['name'],
                    'password' => Hash::make('password'),
                    'admin' => false,
                    'active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $users->push($user);
        }

        // -------------------------------------------------------------
        // 2. EQUIPOS (20 Equipos reales de la Liga)
        // -------------------------------------------------------------
        $this->command->info('Creando 20 equipos...');

        $equiposNombres = [
            'Real Madrid', 'FC Barcelona', 'Atlético de Madrid', 'Athletic Club', 'Real Sociedad',
            'Real Betis', 'Villarreal CF', 'Sevilla FC', 'CA Osasuna', 'Valencia CF',
            'Girona FC', 'Celta de Vigo', 'RCD Mallorca', 'Rayo Vallecano', 'Getafe CF',
            'UD Las Palmas', 'Deportivo Alavés', 'CD Leganés', 'Real Valladolid', 'RCD Espanyol'
        ];

        $equipos = collect();
        foreach ($equiposNombres as $nombre) {
            $equipo = Equipo::firstOrCreate(
                ['nombre' => $nombre],
                ['logo' => '/assets/media/images/default-team.png']
            );
            $equipos->push($equipo);
        }

        // -------------------------------------------------------------
        // 3. JUGADORES (120+ jugadores reales distribuidos entre los 20 equipos)
        // -------------------------------------------------------------
        $this->command->info('Creando jugadores y asignándolos a equipos...');

        $plantillasEquipos = [
            'Real Madrid' => [
                ['nombre' => 'Thibaut', 'apellido1' => 'Courtois', 'posicion' => 'Portero'],
                ['nombre' => 'Antonio', 'apellido1' => 'Rüdiger', 'posicion' => 'Defensa'],
                ['nombre' => 'Dani', 'apellido1' => 'Carvajal', 'posicion' => 'Defensa'],
                ['nombre' => 'Jude', 'apellido1' => 'Bellingham', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Federico', 'apellido1' => 'Valverde', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Vinicius', 'apellido1' => 'Junior', 'posicion' => 'Delantero'],
                ['nombre' => 'Kylian', 'apellido1' => 'Mbappé', 'posicion' => 'Delantero'],
            ],
            'FC Barcelona' => [
                ['nombre' => 'Marc-André', 'apellido1' => 'ter Stegen', 'posicion' => 'Portero'],
                ['nombre' => 'Pau', 'apellido1' => 'Cubarsí', 'posicion' => 'Defensa'],
                ['nombre' => 'Jules', 'apellido1' => 'Koundé', 'posicion' => 'Defensa'],
                ['nombre' => 'Pedro', 'apellido1' => 'González', 'apellido2' => 'Pedri', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Gavi', 'apellido1' => 'Páez', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Lamine', 'apellido1' => 'Yamal', 'posicion' => 'Delantero'],
                ['nombre' => 'Robert', 'apellido1' => 'Lewandowski', 'posicion' => 'Delantero'],
            ],
            'Atlético de Madrid' => [
                ['nombre' => 'Jan', 'apellido1' => 'Oblak', 'posicion' => 'Portero'],
                ['nombre' => 'Robin', 'apellido1' => 'Le Normand', 'posicion' => 'Defensa'],
                ['nombre' => 'José María', 'apellido1' => 'Giménez', 'posicion' => 'Defensa'],
                ['nombre' => 'Koke', 'apellido1' => 'Resurrección', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Rodrigo', 'apellido1' => 'De Paul', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Antoine', 'apellido1' => 'Griezmann', 'posicion' => 'Delantero'],
                ['nombre' => 'Julián', 'apellido1' => 'Álvarez', 'posicion' => 'Delantero'],
            ],
            'Athletic Club' => [
                ['nombre' => 'Unai', 'apellido1' => 'Simón', 'posicion' => 'Portero'],
                ['nombre' => 'Dani', 'apellido1' => 'Vivian', 'posicion' => 'Defensa'],
                ['nombre' => 'Yeray', 'apellido1' => 'Álvarez', 'posicion' => 'Defensa'],
                ['nombre' => 'Oihan', 'apellido1' => 'Sancet', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Mikel', 'apellido1' => 'Jauregizar', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Nico', 'apellido1' => 'Williams', 'posicion' => 'Delantero'],
                ['nombre' => 'Iñaki', 'apellido1' => 'Williams', 'posicion' => 'Delantero'],
            ],
            'Real Sociedad' => [
                ['nombre' => 'Álex', 'apellido1' => 'Remiro', 'posicion' => 'Portero'],
                ['nombre' => 'Igor', 'apellido1' => 'Zubeldia', 'posicion' => 'Defensa'],
                ['nombre' => 'Jon', 'apellido1' => 'Aramburu', 'posicion' => 'Defensa'],
                ['nombre' => 'Martín', 'apellido1' => 'Zubimendi', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Brais', 'apellido1' => 'Méndez', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Takefusa', 'apellido1' => 'Kubo', 'posicion' => 'Delantero'],
                ['nombre' => 'Mikel', 'apellido1' => 'Oyarzabal', 'posicion' => 'Delantero'],
            ],
            'Real Betis' => [
                ['nombre' => 'Rui', 'apellido1' => 'Silva', 'posicion' => 'Portero'],
                ['nombre' => 'Diego', 'apellido1' => 'Llorente', 'posicion' => 'Defensa'],
                ['nombre' => 'Marc', 'apellido1' => 'Bartra', 'posicion' => 'Defensa'],
                ['nombre' => 'Isco', 'apellido1' => 'Alarcón', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Pablo', 'apellido1' => 'Fornals', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Giovani', 'apellido1' => 'Lo Celso', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Vitor', 'apellido1' => 'Roque', 'posicion' => 'Delantero'],
            ],
            'Villarreal CF' => [
                ['nombre' => 'Diego', 'apellido1' => 'Conde', 'posicion' => 'Portero'],
                ['nombre' => 'Raúl', 'apellido1' => 'Albiol', 'posicion' => 'Defensa'],
                ['nombre' => 'Sergi', 'apellido1' => 'Cardona', 'posicion' => 'Defensa'],
                ['nombre' => 'Dani', 'apellido1' => 'Parejo', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Álex', 'apellido1' => 'Baena', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Gerard', 'apellido1' => 'Moreno', 'posicion' => 'Delantero'],
                ['nombre' => 'Ayoze', 'apellido1' => 'Pérez', 'posicion' => 'Delantero'],
            ],
            'Sevilla FC' => [
                ['nombre' => 'Ørjan', 'apellido1' => 'Nyland', 'posicion' => 'Portero'],
                ['nombre' => 'Loïc', 'apellido1' => 'Badé', 'posicion' => 'Defensa'],
                ['nombre' => 'Adrià', 'apellido1' => 'Pedrosa', 'posicion' => 'Defensa'],
                ['nombre' => 'Nemanja', 'apellido1' => 'Gudelj', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Djibril', 'apellido1' => 'Sow', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Dodi', 'apellido1' => 'Lukebakio', 'posicion' => 'Delantero'],
                ['nombre' => 'Isaac', 'apellido1' => 'Romero', 'posicion' => 'Delantero'],
            ],
            'CA Osasuna' => [
                ['nombre' => 'Sergio', 'apellido1' => 'Herrera', 'posicion' => 'Portero'],
                ['nombre' => 'Alejandro', 'apellido1' => 'Catena', 'posicion' => 'Defensa'],
                ['nombre' => 'Jesús', 'apellido1' => 'Areso', 'posicion' => 'Defensa'],
                ['nombre' => 'Lucas', 'apellido1' => 'Torró', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Jon', 'apellido1' => 'Moncayola', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Bryan', 'apellido1' => 'Zaragoza', 'posicion' => 'Delantero'],
                ['nombre' => 'Ante', 'apellido1' => 'Budimir', 'posicion' => 'Delantero'],
            ],
            'Valencia CF' => [
                ['nombre' => 'Giorgi', 'apellido1' => 'Mamardashvili', 'posicion' => 'Portero'],
                ['nombre' => 'Cristhian', 'apellido1' => 'Mosquera', 'posicion' => 'Defensa'],
                ['nombre' => 'José Luis', 'apellido1' => 'Gayà', 'posicion' => 'Defensa'],
                ['nombre' => 'Pepelu', 'apellido1' => 'García', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Javi', 'apellido1' => 'Guerra', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Diego', 'apellido1' => 'López', 'posicion' => 'Delantero'],
                ['nombre' => 'Hugo', 'apellido1' => 'Duro', 'posicion' => 'Delantero'],
            ],
            'Girona FC' => [
                ['nombre' => 'Paulo', 'apellido1' => 'Gazzaniga', 'posicion' => 'Portero'],
                ['nombre' => 'David', 'apellido1' => 'López', 'posicion' => 'Defensa'],
                ['nombre' => 'Daley', 'apellido1' => 'Blind', 'posicion' => 'Defensa'],
                ['nombre' => 'Yangel', 'apellido1' => 'Herrera', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Iván', 'apellido1' => 'Martín', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Viktor', 'apellido1' => 'Tsygankov', 'posicion' => 'Delantero'],
                ['nombre' => 'Abel', 'apellido1' => 'Ruiz', 'posicion' => 'Delantero'],
            ],
            'Celta de Vigo' => [
                ['nombre' => 'Vicente', 'apellido1' => 'Guaita', 'posicion' => 'Portero'],
                ['nombre' => 'Carl', 'apellido1' => 'Starfelt', 'posicion' => 'Defensa'],
                ['nombre' => 'Óscar', 'apellido1' => 'Mingueza', 'posicion' => 'Defensa'],
                ['nombre' => 'Fran', 'apellido1' => 'Beltrán', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Hugo', 'apellido1' => 'Sotelo', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Iago', 'apellido1' => 'Aspas', 'posicion' => 'Delantero'],
                ['nombre' => 'Borja', 'apellido1' => 'Iglesias', 'posicion' => 'Delantero'],
            ],
            'RCD Mallorca' => [
                ['nombre' => 'Dominik', 'apellido1' => 'Greif', 'posicion' => 'Portero'],
                ['nombre' => 'Antonio', 'apellido1' => 'Raíllo', 'posicion' => 'Defensa'],
                ['nombre' => 'Pablo', 'apellido1' => 'Maffeo', 'posicion' => 'Defensa'],
                ['nombre' => 'Samu', 'apellido1' => 'Costa', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Sergi', 'apellido1' => 'Darder', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Dani', 'apellido1' => 'Rodríguez', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Vedat', 'apellido1' => 'Muriqi', 'posicion' => 'Delantero'],
            ],
            'Rayo Vallecano' => [
                ['nombre' => 'Augusto', 'apellido1' => 'Batalla', 'posicion' => 'Portero'],
                ['nombre' => 'Florian', 'apellido1' => 'Lejeune', 'posicion' => 'Defensa'],
                ['nombre' => 'Abdul', 'apellido1' => 'Mumin', 'posicion' => 'Defensa'],
                ['nombre' => 'Óscar', 'apellido1' => 'Valentín', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Unai', 'apellido1' => 'López', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Isi', 'apellido1' => 'Palazón', 'posicion' => 'Delantero'],
                ['nombre' => 'Sergio', 'apellido1' => 'Camello', 'posicion' => 'Delantero'],
            ],
            'Getafe CF' => [
                ['nombre' => 'David', 'apellido1' => 'Soria', 'posicion' => 'Portero'],
                ['nombre' => 'Dakonam', 'apellido1' => 'Djené', 'posicion' => 'Defensa'],
                ['nombre' => 'Omar', 'apellido1' => 'Alderete', 'posicion' => 'Defensa'],
                ['nombre' => 'Mauro', 'apellido1' => 'Arambarri', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Luis', 'apellido1' => 'Milla', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Carles', 'apellido1' => 'Pérez', 'posicion' => 'Delantero'],
                ['nombre' => 'Borja', 'apellido1' => 'Mayoral', 'posicion' => 'Delantero'],
            ],
            'UD Las Palmas' => [
                ['nombre' => 'Jasper', 'apellido1' => 'Cillessen', 'posicion' => 'Portero'],
                ['nombre' => 'Álex', 'apellido1' => 'Suárez', 'posicion' => 'Defensa'],
                ['nombre' => 'Mika', 'apellido1' => 'Mármol', 'posicion' => 'Defensa'],
                ['nombre' => 'Kirian', 'apellido1' => 'Rodríguez', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Javi', 'apellido1' => 'Muñoz', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Alberto', 'apellido1' => 'Moleiro', 'posicion' => 'Delantero'],
                ['nombre' => 'Sandro', 'apellido1' => 'Ramírez', 'posicion' => 'Delantero'],
            ],
            'Deportivo Alavés' => [
                ['nombre' => 'Antonio', 'apellido1' => 'Sivera', 'posicion' => 'Portero'],
                ['nombre' => 'Abdel', 'apellido1' => 'Abqar', 'posicion' => 'Defensa'],
                ['nombre' => 'Nahuel', 'apellido1' => 'Tenaglia', 'posicion' => 'Defensa'],
                ['nombre' => 'Ander', 'apellido1' => 'Guevara', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Jon', 'apellido1' => 'Guridi', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Carlos', 'apellido1' => 'Vicente', 'posicion' => 'Delantero'],
                ['nombre' => 'Kike', 'apellido1' => 'García', 'posicion' => 'Delantero'],
            ],
            'CD Leganés' => [
                ['nombre' => 'Marko', 'apellido1' => 'Dmitrović', 'posicion' => 'Portero'],
                ['nombre' => 'Sergio', 'apellido1' => 'González', 'posicion' => 'Defensa'],
                ['nombre' => 'Jorge', 'apellido1' => 'Sáenz', 'posicion' => 'Defensa'],
                ['nombre' => 'Yvan', 'apellido1' => 'Neyou', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Seydouba', 'apellido1' => 'Cissé', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Juan', 'apellido1' => 'Cruz', 'posicion' => 'Delantero'],
                ['nombre' => 'Miguel', 'apellido1' => 'de la Fuente', 'posicion' => 'Delantero'],
            ],
            'Real Valladolid' => [
                ['nombre' => 'Karl', 'apellido1' => 'Hein', 'posicion' => 'Portero'],
                ['nombre' => 'Javi', 'apellido1' => 'Sánchez', 'posicion' => 'Defensa'],
                ['nombre' => 'Lucas', 'apellido1' => 'Rosa', 'posicion' => 'Defensa'],
                ['nombre' => 'Kike', 'apellido1' => 'Pérez', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Mario', 'apellido1' => 'Martín', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Raúl', 'apellido1' => 'Moro', 'posicion' => 'Delantero'],
                ['nombre' => 'Mamadou', 'apellido1' => 'Sylla', 'posicion' => 'Delantero'],
            ],
            'RCD Espanyol' => [
                ['nombre' => 'Joan', 'apellido1' => 'García', 'posicion' => 'Portero'],
                ['nombre' => 'Leandro', 'apellido1' => 'Cabrera', 'posicion' => 'Defensa'],
                ['nombre' => 'Marash', 'apellido1' => 'Kumbulla', 'posicion' => 'Defensa'],
                ['nombre' => 'Alex', 'apellido1' => 'Král', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Pol', 'apellido1' => 'Lozano', 'posicion' => 'Centrocampista'],
                ['nombre' => 'Javi', 'apellido1' => 'Puado', 'posicion' => 'Delantero'],
                ['nombre' => 'Alejo', 'apellido1' => 'Véliz', 'posicion' => 'Delantero'],
            ],
        ];

        $todosLosJugadores = collect();
        $equipoJugadorPivots = [];

        foreach ($plantillasEquipos as $equipoNombre => $jugadoresList) {
            $equipo = $equipos->firstWhere('nombre', $equipoNombre);

            foreach ($jugadoresList as $datosJugador) {
                $jugador = Jugador::create([
                    'nombre' => $datosJugador['nombre'],
                    'apellido1' => $datosJugador['apellido1'],
                    'apellido2' => $datosJugador['apellido2'] ?? null,
                    'fecha_nacimiento' => fake()->dateTimeBetween('-36 years', '-19 years')->format('Y-m-d'),
                    'posicion' => $datosJugador['posicion'],
                    'foto' => '/assets/media/images/default-player.png',
                ]);

                $todosLosJugadores->push($jugador);

                $equipoJugadorPivots[] = [
                    'equipo_id' => $equipo->id,
                    'jugador_id' => $jugador->id,
                    'fecha_union' => now()->subYears(rand(1, 4))->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('equipo_jugador')->insert($equipoJugadorPivots);

        // -------------------------------------------------------------
        // 4. TORNEO ACTIVO (LaLiga EA Sports Fantasy 2026)
        // -------------------------------------------------------------
        $this->command->info('Creando Torneo y vinculando equipos y jugadores...');

        $torneo = Torneo::create([
            'nombre' => 'LaLiga EA Sports Fantasy 2026',
            'fecha_inicio' => now()->subWeeks(4)->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(8)->format('Y-m-d'),
            'descripcion' => 'Torneo oficial de fútbol fantasy de LaLiga 2025/2026 en formato 5 contra 5.',
            'logo' => '/assets/media/images/default-tournament.png',
            'estado' => 'activo',
            'modalidad' => 'sala',
            'usa_posiciones' => true,
        ]);

        // Vincular los 20 equipos al torneo
        $equipoTorneoPivots = [];
        foreach ($equipos as $equipo) {
            $equipoTorneoPivots[] = [
                'equipo_id' => $equipo->id,
                'torneo_id' => $torneo->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('equipo_torneo')->insert($equipoTorneoPivots);

        // Vincular todos los jugadores a equipo_jugador_torneo
        $equipoJugadorTorneoPivots = [];
        foreach ($equipoJugadorPivots as $pivot) {
            $equipoJugadorTorneoPivots[] = [
                'jugador_id' => $pivot['jugador_id'],
                'equipo_id' => $pivot['equipo_id'],
                'torneo_id' => $torneo->id,
                'goles' => 0,
                'asistencias' => 0,
                'puntos' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('equipo_jugador_torneo')->insert($equipoJugadorTorneoPivots);

        // -------------------------------------------------------------
        // 5. JORNADAS Y PARTIDOS (4 Jornadas: 2 Jugadas, 1 Activa, 1 Futura)
        // -------------------------------------------------------------
        $this->command->info('Creando 4 Jornadas y 40 Partidos...');

        $jornada1 = Jornada::create([
            'torneo_id' => $torneo->id,
            'nombre' => 'Jornada 1',
            'orden' => 1,
            'fecha_inicio' => now()->subWeeks(3)->format('Y-m-d'),
            'fecha_fin' => now()->subWeeks(3)->addDays(3)->format('Y-m-d'),
            'fecha_cierre_alineaciones' => now()->subWeeks(3)->subHours(2)->format('Y-m-d H:i:s'),
            'alineaciones_congeladas' => true,
        ]);

        $jornada2 = Jornada::create([
            'torneo_id' => $torneo->id,
            'nombre' => 'Jornada 2',
            'orden' => 2,
            'fecha_inicio' => now()->subWeeks(2)->format('Y-m-d'),
            'fecha_fin' => now()->subWeeks(2)->addDays(3)->format('Y-m-d'),
            'fecha_cierre_alineaciones' => now()->subWeeks(2)->subHours(2)->format('Y-m-d H:i:s'),
            'alineaciones_congeladas' => true,
        ]);

        $jornada3 = Jornada::create([
            'torneo_id' => $torneo->id,
            'nombre' => 'Jornada 3 (Actual)',
            'orden' => 3,
            'fecha_inicio' => now()->subDays(1)->format('Y-m-d'),
            'fecha_fin' => now()->addDays(2)->format('Y-m-d'),
            'fecha_cierre_alineaciones' => now()->addHours(8)->format('Y-m-d H:i:s'),
            'alineaciones_congeladas' => false,
        ]);

        $jornada4 = Jornada::create([
            'torneo_id' => $torneo->id,
            'nombre' => 'Jornada 4',
            'orden' => 4,
            'fecha_inicio' => now()->addWeeks(1)->format('Y-m-d'),
            'fecha_fin' => now()->addWeeks(1)->addDays(3)->format('Y-m-d'),
            'fecha_cierre_alineaciones' => now()->addWeeks(1)->subHours(2)->format('Y-m-d H:i:s'),
            'alineaciones_congeladas' => false,
        ]);

        // Crear partidos de Jornada 1 (10 partidos jugados)
        $partidosJornada1 = [
            ['local' => 'Real Madrid', 'visitante' => 'CA Osasuna', 'gl' => 3, 'gv' => 1],
            ['local' => 'FC Barcelona', 'visitante' => 'Valencia CF', 'gl' => 2, 'gv' => 0],
            ['local' => 'Atlético de Madrid', 'visitante' => 'Girona FC', 'gl' => 2, 'gv' => 2],
            ['local' => 'Athletic Club', 'visitante' => 'Sevilla FC', 'gl' => 1, 'gv' => 0],
            ['local' => 'Real Sociedad', 'visitante' => 'Real Betis', 'gl' => 1, 'gv' => 1],
            ['local' => 'Villarreal CF', 'visitante' => 'Celta de Vigo', 'gl' => 4, 'gv' => 3],
            ['local' => 'RCD Mallorca', 'visitante' => 'Rayo Vallecano', 'gl' => 0, 'gv' => 1],
            ['local' => 'Getafe CF', 'visitante' => 'Deportivo Alavés', 'gl' => 2, 'gv' => 0],
            ['local' => 'UD Las Palmas', 'visitante' => 'CD Leganés', 'gl' => 1, 'gv' => 1],
            ['local' => 'Real Valladolid', 'visitante' => 'RCD Espanyol', 'gl' => 1, 'gv' => 0],
        ];

        $partidosJornada1Models = collect();
        foreach ($partidosJornada1 as $pj) {
            $eqLocal = $equipos->firstWhere('nombre', $pj['local']);
            $eqVisitante = $equipos->firstWhere('nombre', $pj['visitante']);

            $partido = Partido::create([
                'jornada_id' => $jornada1->id,
                'equipo_local_id' => $eqLocal->id,
                'equipo_visitante_id' => $eqVisitante->id,
                'fecha_partido' => Carbon::parse($jornada1->fecha_inicio)->addHours(rand(12, 21)),
                'goles_local' => $pj['gl'],
                'goles_visitante' => $pj['gv'],
                'estado' => 'jugado',
                'eventos' => [],
            ]);
            $partidosJornada1Models->push($partido);
        }

        // Crear partidos de Jornada 2 (10 partidos jugados)
        $partidosJornada2 = [
            ['local' => 'CA Osasuna', 'visitante' => 'FC Barcelona', 'gl' => 2, 'gv' => 4],
            ['local' => 'Valencia CF', 'visitante' => 'Real Madrid', 'gl' => 1, 'gv' => 2],
            ['local' => 'Sevilla FC', 'visitante' => 'Atlético de Madrid', 'gl' => 0, 'gv' => 1],
            ['local' => 'Girona FC', 'visitante' => 'Athletic Club', 'gl' => 2, 'gv' => 1],
            ['local' => 'Real Betis', 'visitante' => 'Villarreal CF', 'gl' => 1, 'gv' => 2],
            ['local' => 'Celta de Vigo', 'visitante' => 'Real Sociedad', 'gl' => 2, 'gv' => 2],
            ['local' => 'Rayo Vallecano', 'visitante' => 'Getafe CF', 'gl' => 0, 'gv' => 0],
            ['local' => 'Deportivo Alavés', 'visitante' => 'UD Las Palmas', 'gl' => 2, 'gv' => 0],
            ['local' => 'CD Leganés', 'visitante' => 'Real Valladolid', 'gl' => 3, 'gv' => 1],
            ['local' => 'RCD Espanyol', 'visitante' => 'RCD Mallorca', 'gl' => 2, 'gv' => 1],
        ];

        $partidosJornada2Models = collect();
        foreach ($partidosJornada2 as $pj) {
            $eqLocal = $equipos->firstWhere('nombre', $pj['local']);
            $eqVisitante = $equipos->firstWhere('nombre', $pj['visitante']);

            $partido = Partido::create([
                'jornada_id' => $jornada2->id,
                'equipo_local_id' => $eqLocal->id,
                'equipo_visitante_id' => $eqVisitante->id,
                'fecha_partido' => Carbon::parse($jornada2->fecha_inicio)->addHours(rand(12, 21)),
                'goles_local' => $pj['gl'],
                'goles_visitante' => $pj['gv'],
                'estado' => 'jugado',
                'eventos' => [],
            ]);
            $partidosJornada2Models->push($partido);
        }

        // Crear partidos programados para Jornadas 3 y 4
        for ($i = 0; $i < 10; $i++) {
            $eqLocal = $equipos[$i];
            $eqVisitante = $equipos[19 - $i];

            Partido::create([
                'jornada_id' => $jornada3->id,
                'equipo_local_id' => $eqLocal->id,
                'equipo_visitante_id' => $eqVisitante->id,
                'fecha_partido' => Carbon::parse($jornada3->fecha_inicio)->addHours(14 + ($i % 6)),
                'estado' => 'programado',
            ]);

            Partido::create([
                'jornada_id' => $jornada4->id,
                'equipo_local_id' => $eqVisitante->id,
                'equipo_visitante_id' => $eqLocal->id,
                'fecha_partido' => Carbon::parse($jornada4->fecha_inicio)->addHours(14 + ($i % 6)),
                'estado' => 'programado',
            ]);
        }

        // -------------------------------------------------------------
        // 6. EVENTOS Y ESTADÍSTICAS REALISTAS PARA JORNADAS 1 Y 2
        // -------------------------------------------------------------
        $this->command->info('Generando eventos y estadísticas de partidos...');

        $todosLosPartidosJugados = $partidosJornada1Models->concat($partidosJornada2Models);

        foreach ($todosLosPartidosJugados as $partido) {
            $eventos = [];
            $jugadoresLocal = $partido->equipoLocal->jugadoresEnTorneo($torneo->id);
            $jugadoresVisitante = $partido->equipoVisitante->jugadoresEnTorneo($torneo->id);

            // Goles del equipo local
            for ($g = 0; $g < $partido->goles_local; $g++) {
                $goleador = $jugadoresLocal->whereIn('posicion', ['Delantero', 'Centrocampista'])->random() ?? $jugadoresLocal->random();
                $eventos[] = [
                    'tipo' => 'Gol',
                    'jugador_id' => $goleador->id,
                    'minuto' => rand(5, 88),
                ];

                // 60% probabilidad de asistencia
                if (rand(1, 10) <= 6) {
                    $asistente = $jugadoresLocal->where('id', '!=', $goleador->id)->random();
                    if ($asistente) {
                        $eventos[] = [
                            'tipo' => 'Asistencia',
                            'jugador_id' => $asistente->id,
                            'minuto' => rand(5, 88),
                        ];
                    }
                }
            }

            // Goles del equipo visitante
            for ($g = 0; $g < $partido->goles_visitante; $g++) {
                $goleador = $jugadoresVisitante->whereIn('posicion', ['Delantero', 'Centrocampista'])->random() ?? $jugadoresVisitante->random();
                $eventos[] = [
                    'tipo' => 'Gol',
                    'jugador_id' => $goleador->id,
                    'minuto' => rand(5, 88),
                ];

                if (rand(1, 10) <= 6) {
                    $asistente = $jugadoresVisitante->where('id', '!=', $goleador->id)->random();
                    if ($asistente) {
                        $eventos[] = [
                            'tipo' => 'Asistencia',
                            'jugador_id' => $asistente->id,
                            'minuto' => rand(5, 88),
                        ];
                    }
                }
            }

            // Paradas de porteros
            $porteroLocal = $jugadoresLocal->firstWhere('posicion', 'Portero');
            if ($porteroLocal) {
                for ($p = 0; $p < rand(1, 4); $p++) {
                    $eventos[] = ['tipo' => 'Parada', 'jugador_id' => $porteroLocal->id, 'minuto' => rand(1, 90)];
                }
            }
            $porteroVisitante = $jugadoresVisitante->firstWhere('posicion', 'Portero');
            if ($porteroVisitante) {
                for ($p = 0; $p < rand(1, 4); $p++) {
                    $eventos[] = ['tipo' => 'Parada', 'jugador_id' => $porteroVisitante->id, 'minuto' => rand(1, 90)];
                }
            }

            // Tarjetas y faltas
            if (rand(1, 10) <= 7) {
                $jugAmarilla = $jugadoresLocal->random();
                $eventos[] = ['tipo' => 'Tarjeta Amarilla', 'jugador_id' => $jugAmarilla->id, 'minuto' => rand(10, 85)];
            }
            if (rand(1, 10) <= 6) {
                $jugAmarilla2 = $jugadoresVisitante->random();
                $eventos[] = ['tipo' => 'Tarjeta Amarilla', 'jugador_id' => $jugAmarilla2->id, 'minuto' => rand(10, 85)];
            }

            $partido->eventos = $eventos;
            $partido->save();

            // Calcular estadísticas del partido
            $partido->actualizarEstadisticas();
        }

        // -------------------------------------------------------------
        // 7. LIGUILLA Y ENROLAMIENTO DE USUARIOS
        // -------------------------------------------------------------
        $this->command->info('Creando Liguilla y enrolando a los 5 usuarios...');

        $liguilla = Liguilla::create([
            'nombre' => 'Superliga MiFantasy 2026',
            'torneo_id' => $torneo->id,
            'max_usuarios' => 10,
            'codigo_unico' => 'MIFANTASY26',
            'creador_id' => $admin->id,
            'estado' => 'activa',
        ]);

        $nombresEquiposLiguilla = [
            $admin->id => 'Directiva Stars',
            $users[1]->id => 'Galácticos FC',
            $users[2]->id => 'Tiki-Taka Stars',
            $users[3]->id => 'Rojillos United',
            $users[4]->id => 'Furia Verde',
        ];

        foreach ($users as $user) {
            DB::table('liguilla_usuario')->insert([
                'liguilla_id' => $liguilla->id,
                'user_id' => $user->id,
                'nombre_equipo' => $nombresEquiposLiguilla[$user->id] ?? ('Equipo ' . $user->name),
                'puesto' => null,
                'puntos' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -------------------------------------------------------------
        // 8. PLANTILLAS Y DRAFT DE JUGADORES (8 jugadores por usuario)
        // -------------------------------------------------------------
        $this->command->info('Generando plantillas y alineaciones (Base, J1, J2, J3)...');

        $porterosDisponibles = $todosLosJugadores->where('posicion', 'Portero')->values();
        $defensasDisponibles = $todosLosJugadores->where('posicion', 'Defensa')->values();
        $mediosDisponibles = $todosLosJugadores->where('posicion', 'Centrocampista')->values();
        $delanterosDisponibles = $todosLosJugadores->where('posicion', 'Delantero')->values();

        $jugadoresPlantillaInserts = [];
        $alineacionesAInsertar = [];
        $alineacionJugadorInserts = [];

        foreach ($users as $index => $user) {
            $plantilla = Plantilla::create([
                'user_id' => $user->id,
                'liguilla_id' => $liguilla->id,
            ]);

            // Asignar 8 jugadores únicos a la plantilla:
            // 1 Portero, 3 Defensas, 3 Medios, 1 Delantero
            $portero = $porterosDisponibles[$index];
            $defensas = $defensasDisponibles->slice($index * 3, 3)->values();
            $medios = $mediosDisponibles->slice($index * 3, 3)->values();
            $delantero = $delanterosDisponibles[$index];

            $jugadoresUsuario = collect([$portero])
                ->concat($defensas)
                ->concat($medios)
                ->concat([$delantero]);

            foreach ($jugadoresUsuario as $jug) {
                $jugadoresPlantillaInserts[] = [
                    'plantilla_id' => $plantilla->id,
                    'jugador_id' => $jug->id,
                    'posicion' => $jug->posicion,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // 5 Titulares para las alineaciones (1 Portero, 1 Defensa, 2 Medios, 1 Delantero)
            $titulares = [
                $portero,
                $defensas->first(),
                $medios[0],
                $medios[1],
                $delantero,
            ];

            // 1) Alineación Base (jornada_id = null)
            $alineacionBase = Alineacion::create([
                'user_id' => $user->id,
                'liguilla_id' => $liguilla->id,
                'jornada_id' => null,
                'formacion' => '1-2-1',
            ]);
            foreach ($titulares as $titular) {
                $alineacionJugadorInserts[] = [
                    'alineacion_id' => $alineacionBase->id,
                    'jugador_id' => $titular->id,
                    'puntos' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // 2) Alineación Jornada 1
            $alineacionJ1 = Alineacion::create([
                'user_id' => $user->id,
                'liguilla_id' => $liguilla->id,
                'jornada_id' => $jornada1->id,
                'formacion' => '1-2-1',
            ]);
            foreach ($titulares as $titular) {
                $alineacionJugadorInserts[] = [
                    'alineacion_id' => $alineacionJ1->id,
                    'jugador_id' => $titular->id,
                    'puntos' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // 3) Alineación Jornada 2
            $alineacionJ2 = Alineacion::create([
                'user_id' => $user->id,
                'liguilla_id' => $liguilla->id,
                'jornada_id' => $jornada2->id,
                'formacion' => '1-2-1',
            ]);
            foreach ($titulares as $titular) {
                $alineacionJugadorInserts[] = [
                    'alineacion_id' => $alineacionJ2->id,
                    'jugador_id' => $titular->id,
                    'puntos' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // 4) Alineación Jornada 3 (Actual)
            $alineacionJ3 = Alineacion::create([
                'user_id' => $user->id,
                'liguilla_id' => $liguilla->id,
                'jornada_id' => $jornada3->id,
                'formacion' => '1-2-1',
            ]);
            foreach ($titulares as $titular) {
                $alineacionJugadorInserts[] = [
                    'alineacion_id' => $alineacionJ3->id,
                    'jugador_id' => $titular->id,
                    'puntos' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('jugador_plantilla')->insert($jugadoresPlantillaInserts);
        DB::table('alineacion_jugador')->insert($alineacionJugadorInserts);

        // -------------------------------------------------------------
        // 9. CÁLCULO Y VOLCADO DE PUNTOS PARA ALINEACIONES Y CLASIFICACIÓN
        // -------------------------------------------------------------
        $this->command->info('Calculando puntos de jornadas y clasificación general...');

        // Puntos Jornada 1
        $puntosJornada1 = DB::table('estadisticas as e')
            ->join('partidos as p', 'p.id', '=', 'e.partido_id')
            ->select('e.jugador_id', DB::raw('SUM(e.puntos) as total_puntos'))
            ->where('p.jornada_id', $jornada1->id)
            ->groupBy('e.jugador_id')
            ->pluck('total_puntos', 'jugador_id');

        $alineacionesJ1Ids = Alineacion::where('liguilla_id', $liguilla->id)
            ->where('jornada_id', $jornada1->id)
            ->pluck('id');

        foreach ($puntosJornada1 as $jugadorId => $pts) {
            DB::table('alineacion_jugador')
                ->whereIn('alineacion_id', $alineacionesJ1Ids)
                ->where('jugador_id', $jugadorId)
                ->update(['puntos' => $pts]);
        }

        // Puntos Jornada 2
        $puntosJornada2 = DB::table('estadisticas as e')
            ->join('partidos as p', 'p.id', '=', 'e.partido_id')
            ->select('e.jugador_id', DB::raw('SUM(e.puntos) as total_puntos'))
            ->where('p.jornada_id', $jornada2->id)
            ->groupBy('e.jugador_id')
            ->pluck('total_puntos', 'jugador_id');

        $alineacionesJ2Ids = Alineacion::where('liguilla_id', $liguilla->id)
            ->where('jornada_id', $jornada2->id)
            ->pluck('id');

        foreach ($puntosJornada2 as $jugadorId => $pts) {
            DB::table('alineacion_jugador')
                ->whereIn('alineacion_id', $alineacionesJ2Ids)
                ->where('jugador_id', $jugadorId)
                ->update(['puntos' => $pts]);
        }

        // Recalcular puntos acumulados en equipo_jugador_torneo
        $puntosPorJugadorTorneo = DB::table('estadisticas as e')
            ->join('partidos as p', 'p.id', '=', 'e.partido_id')
            ->join('jornadas as j', 'j.id', '=', 'p.jornada_id')
            ->select(
                'e.jugador_id',
                DB::raw('SUM(e.goles) as total_goles'),
                DB::raw('SUM(e.asistencias) as total_asistencias'),
                DB::raw('SUM(e.puntos) as total_puntos')
            )
            ->where('j.torneo_id', $torneo->id)
            ->groupBy('e.jugador_id')
            ->get();

        foreach ($puntosPorJugadorTorneo as $statsJug) {
            DB::table('equipo_jugador_torneo')
                ->where('torneo_id', $torneo->id)
                ->where('jugador_id', $statsJug->jugador_id)
                ->update([
                    'goles' => $statsJug->total_goles,
                    'asistencias' => $statsJug->total_asistencias,
                    'puntos' => $statsJug->total_puntos,
                ]);
        }

        // Recalcular puntos globales de cada usuario en liguilla_usuario
        $puntosUsuarios = DB::table('alineaciones as a')
            ->join('alineacion_jugador as aj', 'aj.alineacion_id', '=', 'a.id')
            ->select('a.user_id', DB::raw('SUM(aj.puntos) as total_puntos'))
            ->where('a.liguilla_id', $liguilla->id)
            ->whereNotNull('a.jornada_id')
            ->groupBy('a.user_id')
            ->orderByDesc('total_puntos')
            ->get();

        foreach ($puntosUsuarios as $posicion => $pu) {
            DB::table('liguilla_usuario')
                ->where('liguilla_id', $liguilla->id)
                ->where('user_id', $pu->user_id)
                ->update([
                    'puntos' => $pu->total_puntos,
                    'puesto' => $posicion + 1,
                ]);
        }

        $this->command->info('¡Base de datos de MiFantasy poblada con éxito con datos realistas y coherentes!');
    }
}
