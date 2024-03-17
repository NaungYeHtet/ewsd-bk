<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AcademicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dates = [
            // [
            //     'name' => '2020-2021',
            //     'start_date' => '2020-02-01',
            //     'closure_date' => '2020-06-01',
            //     'final_closure_date' => '2021-01-31',
            // ],
            // [
            //     'name' => '2021-2022',
            //     'start_date' => '2021-02-01',
            //     'closure_date' => '2021-06-01',
            //     'final_closure_date' => '2022-01-31',
            // ],
            [
                'name' => '2022-2023',
                'start_date' => '2022-02-01',
                'closure_date' => '2022-06-01',
                'final_closure_date' => '2023-01-31',
            ],
            [
                'name' => '2023-2024',
                'start_date' => '2023-02-01',
                'closure_date' => '2023-06-01',
                'final_closure_date' => '2024-01-31',
            ],
            [
                'name' => '2024-2025',
                'start_date' => '2024-02-01',
                'closure_date' => '2024-06-01',
                'final_closure_date' => '2025-01-31',
            ],
        ];

        foreach ($dates as $date) {
            \App\Models\Academic::create($date);
        }
    }
}
