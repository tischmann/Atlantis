<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use App\Models\{User};

use BadMethodCallException;

use Exception;

class Controller
{
    public function __call($name, $arguments): mixed
    {
        throw new BadMethodCallException(
            Locale::get('method_not_found') . ": {$name}",
            404
        );
    }

    public static function setTitle(string $title): void
    {
        putenv('APP_TITLE=' . getenv('APP_TITLE') . " - " . $title);
    }

    protected function checkAdmin(): void
    {
        if (!User::current()->isAdmin()) {
            throw new Exception(Locale::get('access_denied'), 404);
        }
    }

    protected function sort(Query &$query, Request $request): Query
    {
        $sort = $request->request('sort') ?: 'id';

        $order = $request->request('order') ?: 'desc';

        return $query->order($sort, $order);
    }

    protected function search(
        Query &$query,
        Request $request,
        array $columns
    ): Query {
        $search = strip_tags(strval($request->request('search')));

        if ($search) {
            $query->where(function (&$nested) use ($columns, $search) {
                foreach ($columns as $column) {
                    $nested->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
