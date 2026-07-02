<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): ViewContract
    {
        return view('home', [
            'articles' => Article::query()
                ->published()
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->paginate(9),
        ]);
    }
}
