<?php

declare(strict_types=1);

namespace Modules\Blog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\AccessControl\Models\UserModel;
use Modules\Blog\Models\ArticleModel;

/** @extends Factory<ArticleModel> */
class ArticleModelFactory extends Factory
{
    protected $model = ArticleModel::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'text' => fake()->paragraphs(asText: true),
            'user_id' => UserModel::factory(),
        ];
    }
}
