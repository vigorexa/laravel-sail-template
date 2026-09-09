<?php

declare(strict_types=1);

namespace Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Modules\Blog\Models\ArticleModel;

class BlogController extends Controller
{
    public function index(): View
    {
        $articles = ArticleModel::query()
            ->with('author')
            ->latest()
            ->paginate(10);

        return view('blog::index', [
            'articles' => $articles,
        ]);
    }
}
