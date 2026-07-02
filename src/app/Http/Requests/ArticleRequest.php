<?php

namespace App\Http\Requests;

use App\Enums\ArticleStatus;
use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $article = $this->route('article');

        if ($article instanceof Article) {
            return $this->user()?->can('update', $article) ?? false;
        }

        return $this->user()?->can('create', Article::class) ?? false;
    }

    /**
     * @return array<string, array<int, string|Enum>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'status' => ['required', new Enum(ArticleStatus::class)],
        ];
    }
}
