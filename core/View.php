<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

final class View
{
    protected Template $template;

    public function __construct(public string $view, public array $args = [])
    {
        $user = Auth::user();

        $this->template = new Template($view, $args);

        Template::ifDirective('auth', function (...$args) use ($user) {
            return $user->exists();
        });

        Template::ifDirective('admin', function (...$args) use ($user) {
            return $user->isAdmin();
        });

        Template::directive('referrer', function (...$args) {
            return $_SERVER['HTTP_REFERER'] ?? '/';
        });

        Template::directive('nonce', function (...$args) {
            return getenv('APP_NONCE');
        });

        Template::directive('title', function (...$args) {
            return getenv('APP_TITLE');
        });

        Template::directive('date', function (...$args) {
            return gmdate(...$args);
        });

        $uniqid = uniqid();

        Template::directive('uniqid', function (...$args) use ($uniqid) {
            return $uniqid;
        });
    }

    public static function make(string $view, array $args = []): View
    {
        return new static($view, $args);
    }

    public function render(): string
    {
        return $this->template->render();
    }
}
