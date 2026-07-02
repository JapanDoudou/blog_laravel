<?php

namespace App\Enums;

enum ArticleStatus: string
{
    case Draft = 'draft';
    case Published = 'published';

    // Nope, we don't need to implement the label method in the enum itself.
    // Instead, we must use Laravel's localization features to provide translations for the enum values.
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Published => 'Publié',
        };
    }
}
