<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Ventas de Productos', 'type' => 'income', 'classification' => 'none'],
            ['name' => 'Prestación de Servicios', 'type' => 'income', 'classification' => 'none'],
            ['name' => 'Cobro de Deudores', 'type' => 'income', 'classification' => 'none'],
            ['name' => 'Alquiler Oficina', 'type' => 'expense', 'classification' => 'fixed'],
            ['name' => 'Sueldos y Cargas Sociales', 'type' => 'expense', 'classification' => 'fixed'],
            ['name' => 'Internet y Servicios', 'type' => 'expense', 'classification' => 'fixed'],
            ['name' => 'Publicidad (Ads)', 'type' => 'expense', 'classification' => 'variable'],
            ['name' => 'Insumos y Papelería', 'type' => 'expense', 'classification' => 'variable'],
            ['name' => 'Mantenimiento y Reparaciones', 'type' => 'expense', 'classification' => 'variable'],
            ['name' => 'Comisiones Bancarias', 'type' => 'expense', 'classification' => 'variable'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
