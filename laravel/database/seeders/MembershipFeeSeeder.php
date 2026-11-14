<?php

namespace Database\Seeders;

use App\Models\MembershipFee;
use Illuminate\Database\Seeder;

/** Fee table from sstn.sk/clenstvo (valid since 1 January 2012). */
class MembershipFeeSeeder extends Seeder
{
    public function run(): void
    {
        $fees = [
            ['Individuálne', 14, 1, true],
            ['Kolektívne do 50 zamestnancov', 35, 2, false],
            ['Kolektívne od 51 do 100 zamestnancov', 45, 3, false],
            ['Kolektívne nad 100 zamestnancov', 80, 5, false],
        ];

        foreach ($fees as $i => [$kind, $price, $votes, $featured]) {
            MembershipFee::updateOrCreate(['kind' => $kind], [
                'price' => $price, 'votes' => $votes, 'is_featured' => $featured, 'sort' => $i + 1,
            ]);
        }
    }
}
