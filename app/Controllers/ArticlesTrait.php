<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Article;

trait ArticlesTrait
{
    /**
     * Обработка статьи для вывода в представлении
     * 
     * @param Article $article Статья
     * @return Article Обработанная статья
     */
    protected function processArticle(Article $article): Article
    {
        $placeholder = "/images/placeholder.svg";

        $article->image = $article->image
            ? "/images/articles/{$article->id}/{$article->image}"
            : $placeholder;

        if (!is_file(getenv('APP_ROOT') . "/public{$article->image}")) {
            $article->image = $placeholder;
        }

        return $article;
    }
}
