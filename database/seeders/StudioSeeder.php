<?php
// database/seeders/StudioSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Studio;
use App\Models\Seat;

class StudioSeeder extends Seeder
{
    public function run()
    {
        // Studio 1 - 80 seats (8x10)
        $studio1 = Studio::create([
            'name' => 'Studio 1',
            'total_seats' => 80,
            'rows' => 8,
            'columns' => 10
        ]);

        $this->createSeats($studio1, 8, 10);

        // Studio 2 - 64 seats (8x8)
        $studio2 = Studio::create([
            'name' => 'Studio 2',
            'total_seats' => 64,
            'rows' => 8,
            'columns' => 8
        ]);

        $this->createSeats($studio2, 8, 8);

        // Studio 3 - 100 seats (10x10)
        $studio3 = Studio::create([
            'name' => 'Studio 3 - Sweetbox',
            'total_seats' => 100,
            'rows' => 10,
            'columns' => 10
        ]);

        $this->createSeats($studio3, 10, 10, true);
    }

    private function createSeats($studio, $rows, $columns, $hasSweetbox = false)
    {
        $rowsArray = range('A', chr(ord('A') + $rows - 1));

        foreach ($rowsArray as $row) {
            for ($number = 1; $number <= $columns; $number++) {
                $seatCode = $row . $number;

                // Determine seat type
                $type = 'regular';
                if ($hasSweetbox && $row >= 'H' && $number >= 4 && $number <= 7) {
                    $type = 'sweetbox';
                }

                Seat::create([
                    'studio_id' => $studio->id,
                    'row' => $row,
                    'number' => $number,
                    'seat_code' => $seatCode,
                    'type' => $type,
                    'is_available' => true
                ]);
            }
        }
    }
}
