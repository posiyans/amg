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
        //

        \App\User::create([
            'surname' => 'Иванов',
            'name' => 'Иван',
            'patronymic' => 'Иванович',
            'date' => '1981-04-12',
            'city' => 'Санкт-Петербург',
            'role' => 1,
            'specialty_id' => 1,
            'email' => 'test1@ya.ru',
            'password' => Hash::make('12345678'),
        ]);
        \App\User::create([
            'surname' => 'Петров',
            'name' => 'Виктор',
            'patronymic' => 'Иванович',
            'date' => '1971-11-12',
            'city' => 'Санкт-Петербург',
            'role' => 1,
            'specialty_id' => null,
            'email' => 'test2@ya.ru',
            'password' => Hash::make('12345678'),
        ]);
        \App\User::create([
            'surname' => 'Вихарев',
            'name' => 'Алексей',
            'patronymic' => 'Петрович',
            'date' => '1971-11-12',
            'city' => 'Санкт-Петербург',
            'role' => 2,
            'specialty_id' => '1',
            'email' => 'test3@ya.ru',
            'password' => Hash::make('12345678'),
        ]);
        \App\User::create([
            'surname' => 'Носовв',
            'name' => 'Андрей',
            'patronymic' => 'Петрович',
            'date' => '1971-11-12',
            'city' => 'Санкт-Петербург',
            'role' => 2,
            'specialty_id' => '2',
            'email' => 'test4@ya.ru',
            'password' => Hash::make('12345678'),
        ]);

    }


}
