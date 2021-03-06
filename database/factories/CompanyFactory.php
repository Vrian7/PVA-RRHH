<?php

use Faker\Generator as Faker;

$factory->define(App\Company::class, function (Faker $faker) {
	$faker->addProvider(new \Faker\Provider\en_US\Company($faker));

	return [
		'name' => $faker->unique()->company,
		'shortened' => strtoupper($faker->unique()->firstname),
		'tax_number' => $faker->unique()->randomNumber(8, true) * pow(10, 6),
		'document_id' => App\Document::first()->id,
	];
});
