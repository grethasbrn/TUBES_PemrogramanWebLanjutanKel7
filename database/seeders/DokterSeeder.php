<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DokterSeeder extends Seeder
{
    /**
     * Buat akun dokter untuk setiap poli.
     * Jalankan: php artisan db:seed --class=DokterSeeder
     */
    public function run(): void
    {
        $dokters = [
            ['name' => 'Dr. Umum',          'email' => 'dokter.umum@pharmbee.com',          'poli' => 'Umum'],
            ['name' => 'Dr. Anak',          'email' => 'dokter.anak@pharmbee.com',           'poli' => 'Anak'],
            ['name' => 'Dr. Penyakit Dalam','email' => 'dokter.penyakitdalam@pharmbee.com',  'poli' => 'Penyakit Dalam'],
            ['name' => 'Dr. Bedah',         'email' => 'dokter.bedah@pharmbee.com',          'poli' => 'Bedah'],
            ['name' => 'Dr. Gigi',          'email' => 'dokter.gigi@pharmbee.com',           'poli' => 'Gigi'],
            ['name' => 'Dr. Kebidanan',     'email' => 'dokter.kebidanan@pharmbee.com',      'poli' => 'Kebidanan'],
            ['name' => 'Dr. Mata',          'email' => 'dokter.mata@pharmbee.com',           'poli' => 'Mata'],
            ['name' => 'Dr. UGD',           'email' => 'dokter.ugd@pharmbee.com',            'poli' => 'UGD'],
        ];

        foreach ($dokters as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('password'),
                    'role'     => 'dokter',
                    'poli'     => $data['poli'],
                ]
            );
        }

        $this->command->info('✅ ' . count($dokters) . ' akun dokter berhasil dibuat.');
    }
}