<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //--------------------Seeding roles table-------------------------------
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Administrator'],
            ['id' => 2, 'name' => 'Standard User'],
        ]);
        
        //--------------------Seeding categories table--------------------------
        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'Books', 'description' => 'Books coming from anywhere'],
            ['id' => 2, 'name' => 'Cars', 'description' => 'Available cars'],
            ['id' => 3, 'name' => 'Toys', 'description' => 'Different kinds of toys'],
            ['id' => 4, 'name' => 'Office', 'description' => 'Useful office stuff'],
            ['id' => 5, 'name' => 'Other', 'description' => 'Articles whitout specific category'],
        ]);
        
        //------------------Seeding product conditions table------------------------
        DB::table('product_conditions')->insert([
            ['id' => 1, 'name' => 'New'],
            ['id' => 2, 'name' => 'Used'],
            ['id' => 3, 'name' => 'Broken'],
        ]);
        
        //------------------Seeding user status table------------------------
        DB::table('user_status')->insert([
            ['id' => 1, 'name' => 'Active'],
            ['id' => 2, 'name' => 'Blocked'],
        ]);
        
        //------------------Seeding users table------------------------
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Alcides',
                'email' => 'alcides8107@gmail.com',
                'password' => '$2y$10$.5SCX7scnd160ykxoOZnHuNtB1SRsFiPJqOCaNC0PXZ.Z.a4BydGC',
                'remember_token' => NULL,
                'api_token' => str_random(60),
                'created_at' => '2016-02-26 22:56:14',
                'updated_at' => '2016-02-26 22:56:14',
                'role_id' => 1,
                'status_id' => 1
            ],
        ]);
        
        //------------------Seeding products table------------------------
        DB::table('products')->insert([
            ['id' => 1, 'name' => 'Lamborghini Diablo', 'description' => 'This is my old supercar', 'quantity' => 1, 'category_id' => 2, 'condition_id' => 2, 'user_id' => 1],
            ['id' => 2, 'name' => 'Lamborghini Murcielago', 'description' => 'This is my new supercar', 'quantity' => 1, 'category_id' => 2, 'condition_id' => 1, 'user_id' => 1],
            ['id' => 3, 'name' => 'Buzz Lightyear', 'description' => 'One of my favorite childhood toys', 'quantity' => 3, 'category_id' => 3, 'condition_id' => 2, 'user_id' => 1],
            ['id' => 4, 'name' => 'The old man and the sea', 'description' => 'One of my favorite books', 'quantity' => 5, 'category_id' => 1, 'condition_id' => 2, 'user_id' => 1],
            ['id' => 5, 'name' => 'Golf stick #3', 'description' => 'This golf sticks needs some repairing', 'quantity' => 1, 'category_id' => 5, 'condition_id' => 3, 'user_id' => 1],
        ]);
    }
}
