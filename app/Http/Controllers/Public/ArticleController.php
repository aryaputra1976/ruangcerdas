<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::query()
            ->published()
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('public.articles.index', compact('articles'));
    }

    public function show(Article $article)
    {
        abort_unless($article->is_published && $article->published_at && $article->published_at->lte(now()), 404);

        return view('public.articles.show', compact('article'));
    }
}
