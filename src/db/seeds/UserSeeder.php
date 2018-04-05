<?php
use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $data = [];
        for ($i = 0; $i < 100; $i++) {
            $data[] = [
                'u_screen_name' => $faker->userName,
                'u_full_name' => $faker->userName,
                'u_email' => $faker->email,
                'u_password' => sha1($faker->password),
                'u_status' => 1,
                'u_is_verified' => 1,
                'u_verify_type' => 3,
                'u_groupid' => 'guest',
                'u_date_created' => time()
            ];
        }

        $this->insert('user', $data);
    }
}
