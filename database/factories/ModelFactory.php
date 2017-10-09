<?php

use App\Buyer;
use App\Category;
use App\Product;
use App\Seller;
use App\Transaction;
use App\User;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'verified' => $verified = $faker->randomElement([User::VERIFIED_USER, User::UNVERIFIED_USER]),
        'verification_token' => $verified == User::VERIFIED_USER ? null : User::generateVerificationCode(),
        'admin' => $verified = $faker->randomElement([User::ADMIN_USER, User::REGULAR_USER]),
    ];
});

$factory->define(Category::class, function (Faker $faker) {

    return [
        'name' => $faker->word,
        'description' => $faker->paragraph(1),
    ];
});

$factory->define(Product::class, function (Faker $faker) {

    return [
        'name' => $faker->word,
        'description' => $faker->paragraph(1),
        'quantity' => $faker->numberBetween(10, 20),
        'status' => $faker->randomElement([Product::AVAILABLE_PRODUCT, Product::UNAVAILABLE_PRODUCT]),
        'image' => $faker->randomElement(['dau-truong-sinh-tu.jpg', 'bat-lua.jpg', 'hung-nhai.jpg']),
        'seller_id' => User::all()->random()->id,
        //User::inRandomOrder()->first()->id,
    ];
});

$factory->define(Transaction::class, function (Faker $faker) {

    $seller = Seller::has('products')->get()->random();
    $buyer = User::all()->except($seller->id)->random();

    return [
        'quantity' => $faker->numberBetween(1, 3),
        'buyer_id' => $buyer->id,
        'product_id' => $seller->products->random()->id,
    ];
});