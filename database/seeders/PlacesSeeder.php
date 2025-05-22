<?php

namespace Database\Seeders;

use App\Models\AcademicInfo;
use Illuminate\Database\Seeder;

class AcademicInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Notas de corte
        $cutOffMarks = [
            [
                'university_name' => 'Universidad de Valencia',
                'degree_name' => 'Medicina',
                'cut_off_mark' => 13.85,
                'year' => 2024,
                'type' => 'notas-corte',
            ],
            [
                'university_name' => 'Universidad de Valencia',
                'degree_name' => 'Psicología',
                'cut_off_mark' => 11.50,
                'year' => 2024,
                'type' => 'notas-corte',
            ],
            [
                'university_name' => 'Universidad Politécnica de Valencia',
                'degree_name' => 'Ingeniería Informática',
                'cut_off_mark' => 11.80,
                'year' => 2024,
                'type' => 'notas-corte',
            ],
            [
                'university_name' => 'Universidad Politécnica de Valencia',
                'degree_name' => 'Arquitectura',
                'cut_off_mark' => 12.20,
                'year' => 2024,
                'type' => 'notas-corte',
            ],
            [
                'university_name' => 'Universidad de Valencia',
                'degree_name' => 'Derecho',
                'cut_off_mark' => 10.95,
                'year' => 2024,
                'type' => 'notas-corte',
            ],
            [
                'university_name' => 'Universidad de Valencia',
                'degree_name' => 'Administración y Dirección de Empresas',
                'cut_off_mark' => 9.80,
                'year' => 2024,
                'type' => 'notas-corte',
            ],
        ];

        // Becas
        $scholarships = [
            [
                'university_name' => 'Generalitat Valenciana',
                'degree_name' => 'Todas las carreras',
                'scholarship_name' => 'Beca de Excelencia Académica',
                'scholarship_description' => 'Beca destinada a estudiantes con expediente académico sobresaliente. Cubre matrícula y gastos de manutención.',
                'application_deadline' => '2024-09-30',
                'year' => 2024,
                'type' => 'beca',
                'link' => 'https://www.gva.es/es/inicio/procedimientos?id_proc=19304',
            ],
            [
                'university_name' => 'Ministerio de Educación',
                'degree_name' => 'Todas las carreras',
                'scholarship_name' => 'Beca General del Estado',
                'scholarship_description' => 'Beca general para estudiantes universitarios con criterios económicos y académicos.',
                'application_deadline' => '2024-10-15',
                'year' => 2024,
                'type' => 'beca',
                'link' => 'https://www.educacionyfp.gob.es/servicios-al-ciudadano/catalogo/estudiantes/becas-ayudas/universidad/beca-general.html',
            ],
            [
                'university_name' => 'Universidad de Valencia',
                'degree_name' => 'Medicina',
                'scholarship_name' => 'Beca de Investigación Médica',
                'scholarship_description' => 'Beca específica para estudiantes de medicina interesados en investigación.',
                'application_deadline' => '2024-11-30',
                'year' => 2024,
                'type' => 'beca',
                'link' => 'https://www.uv.es/uvweb/universidad/es/estudios-grado/becas-ayudas-1285846094474.html',
            ],
            [
                'university_name' => 'Universidad Politécnica de Valencia',
                'degree_name' => 'Ingeniería',
                'scholarship_name' => 'Beca de Excelencia en Ingeniería',
                'scholarship_description' => 'Programa de becas para estudiantes destacados en carreras de ingeniería.',
                'application_deadline' => '2024-12-15',
                'year' => 2024,
                'type' => 'beca',
                'link' => 'https://www.upv.es/contenidos/ESTUDIA/',
            ],
            [
                'university_name' => 'Fundación La Caixa',
                'degree_name' => 'Todas las carreras',
                'scholarship_name' => 'Beca de Movilidad Internacional',
                'scholarship_description' => 'Programa de becas para realizar estudios en universidades extranjeras.',
                'application_deadline' => '2025-01-31',
                'year' => 2025,
                'type' => 'beca',
                'link' => 'https://fundacionlacaixa.org/es/becas-estudios-posgrado-extranjero',
            ],
        ];

        // Información general
        $generalInfo = [
            [
                'university_name' => 'Universidad de Valencia',
                'degree_name' => 'Información General',
                'year' => 2024,
                'type' => 'general',
                'scholarship_name' => 'Proceso de Admisión 2024',
                'scholarship_description' => 'Información sobre el proceso de admisión para el curso académico 2024-2025.',
                'link' => 'https://www.uv.es/uvweb/universidad/es/estudios-grado/admision-1285846094465.html',
            ],
            [
                'university_name' => 'Universidad Politécnica de Valencia',
                'degree_name' => 'Información General',
                'year' => 2024,
                'type' => 'general',
                'scholarship_name' => 'Calendario Académico 2024-2025',
                'scholarship_description' => 'Fechas importantes del calendario académico para el curso 2024-2025.',
                'link' => 'https://www.upv.es/entidades/SA/menu_990.html',
            ],
        ];

        // Insertar notas de corte
        foreach ($cutOffMarks as $cutOff) {
            AcademicInfo::firstOrCreate(
                [
                    'university_name' => $cutOff['university_name'],
                    'degree_name' => $cutOff['degree_name'],
                    'year' => $cutOff['year'],
                    'type' => $cutOff['type'],
                ],
                $cutOff
            );
        }

        // Insertar becas
        foreach ($scholarships as $scholarship) {
            AcademicInfo::firstOrCreate(
                [
                    'university_name' => $scholarship['university_name'],
                    'scholarship_name' => $scholarship['scholarship_name'],
                    'year' => $scholarship['year'],
                    'type' => $scholarship['type'],
                ],
                $scholarship
            );
        }

        // Insertar información general
        foreach ($generalInfo as $info) {
            AcademicInfo::firstOrCreate(
                [
                    'university_name' => $info['university_name'],
                    'scholarship_name' => $info['scholarship_name'],
                    'year' => $info['year'],
                    'type' => $info['type'],
                ],
                $info
            );
        }

        $this->command->info('Información académica creada exitosamente.');
    }
}