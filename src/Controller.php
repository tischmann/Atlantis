<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use App\Models\User;
use BadMethodCallException;

class Controller
{
    public function __call($name, $arguments): mixed
    {
        throw new BadMethodCallException("Method {$name} not found", 404);
    }

    protected function isAdmin(): bool
    {
        return User::current()->isAdmin();
    }

    public static function setTitle(string $title): void
    {
        putenv('APP_TITLE=' . getenv('APP_TITLE') . " - " . $title);
    }

    protected function checkAdmin(): void
    {
        if (User::current()->role !== User::ROLE_ADMIN) {
            throw new AccessDeniedException();
        }
    }

    protected function getAlert(bool $wipe = true): ?Alert
    {
        if (Session::has('alert')) {
            $alert = Session::get('alert');
            if ($wipe) Session::delete('alert');
            return $alert;
        }

        return new Alert(status: -1, message: 'Everything is fine');
    }

    protected function getAdminMenu(): string
    {
        return $this->isAdmin()
            ? Template::make(
                'admin',
                [
                    'menu' =>  Template::make('admin/menu')->render()
                ]
            )->render()
            : '';
    }

    public static function getLocalesOptions(string $locale): string
    {
        $locales = '';

        foreach (Locale::available() as $value) {
            $locales  .= Template::html(
                'option',
                [
                    'value' => $value,
                    'label' => Locale::get('locale_' . $value),
                    'title' => Locale::get('locale_' . $value),
                    'selected' => $value === $locale ? 'selected' : ''
                ]
            );
        }

        return $locales;
    }
}
