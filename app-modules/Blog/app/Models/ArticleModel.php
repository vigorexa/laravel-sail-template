<?php

declare(strict_types=1);

namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AccessControl\Models\UserModel;
use Modules\Blog\Database\Factories\ArticleModelFactory;
use Modules\Blog\Policies\ArticleModelPolicy;

#[UsePolicy(ArticleModelPolicy::class)]
class ArticleModel extends Model
{
    /** @use HasFactory<ArticleModelFactory> */
    use HasFactory;
    use HasUuids;

    protected $table = 'blog_articles';

    protected $guarded = [];

    public function author(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    protected static function newFactory(): ArticleModelFactory
    {
        return ArticleModelFactory::new();
    }
}
