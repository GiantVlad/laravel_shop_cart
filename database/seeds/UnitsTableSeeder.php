<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
        DB::table('units')->truncate();
        DB::table('units')->insert([
            [
                'name' => 'kg.',
                'code' => 'KG',
                'full_name' => 'kilogram',
                'decimal_place' => 3,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'gr.',
                'code' => 'GR',
                'full_name' => 'gram',
                'decimal_place' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [

                'name' => 'pack.',
                'code' => 'PK',
                'full_name' => 'package',
                'decimal_place' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'pcs.',
                'code' => 'PCS',
                'full_name' => 'pieces',
                'decimal_place' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
