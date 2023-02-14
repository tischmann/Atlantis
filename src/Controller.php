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

    protected function getCsrfInput(): string
    {
        list($key, $value) = CSRF::set();

        return Template::make(
            'csrf',
            ['key' => $key, 'value' => $value]
        )->render();
    }

    protected function isAdmin(): bool
    {
        return User::current()->isAdmin();
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

    public static function renderBreadcrumbs(array $breadcrumbs): string
    {
        $items = '';

        foreach ($breadcrumbs as $breadcrumb) {

            $view = $breadcrumb->url
                ? 'breadcrumb-url'
                : 'breadcrumb-span';

            $items .= Template::make(
                template: $view,
                args: [
                    'label' => $breadcrumb->label,
                    'url' => $breadcrumb->url,
                ]
            )->render();
        }

        return Template::make('breadcrumbs', ['items' => $items])->render();
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

    protected function getLocalesOptions(string $locale): string
    {
        $locales = '';

        foreach (Locale::available() as $localeValue) {
            $locales  .= Template::make(
                'option',
                [
                    'value' => $localeValue,
                    'label' => Locale::get('locale_' . $localeValue),
                    'title' => Locale::get('locale_' . $localeValue),
                    'selected' => $localeValue === $locale ? 'selected' : ''
                ]
            )->render();
        }

        return $locales;
    }
}
