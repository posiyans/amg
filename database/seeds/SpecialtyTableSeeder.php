<?php

use Illuminate\Database\Seeder;
use App\Specialty;

class SpecialtyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $specialty = new Specialty();
        $specialty->name = 'Сурдолог';
        $specialty->save();

        $specialty = new Specialty();
        $specialty->name = 'Оториноларинголог';
        $specialty->save();

        $specialty = new Specialty();
        $specialty->name = 'Психиатр';
        $specialty->save();

    }
}
