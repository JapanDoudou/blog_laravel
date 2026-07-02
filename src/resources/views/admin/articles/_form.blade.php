@php use App\Enums\ArticleStatus; @endphp

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

<div class="d-grid gap-4">
    <div>
        <label for="title" class="form-label">Titre</label>
        <input
            id="title"
            name="title"
            type="text"
            value="{{ old('title', $article->title) }}"
            class="form-control @error('title') is-invalid @enderror"
            required
        >
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="status" class="form-label">Statut</label>
        <select
            id="status"
            name="status"
            class="form-select @error('status') is-invalid @enderror"
            required
        >
            @foreach ($statuses as $status)
                <option
                    value="{{ $status->value }}"
                    @selected(old('status', $article->status?->value ?? ArticleStatus::Draft->value) === $status->value)
                >
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="content" class="form-label">Contenu</label>
        <textarea
            id="content"
            name="content"
            rows="12"
            class="form-control @error('content') is-invalid @enderror"
            data-rich-text-editor
            data-editor-placeholder="Écris ton article ici..."
        >{{ old('content', $article->content) }}</textarea>
        @error('content')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex flex-wrap gap-3 align-items-center">
        <button type="submit" class="btn btn-dark">Enregistrer</button>

        @if ($article->exists && $article->status === ArticleStatus::Published)
            <a href="{{ route('articles.show', $article->slug) }}" class="btn btn-outline-secondary">Voir la page publique</a>
        @endif
    </div>
</div>
