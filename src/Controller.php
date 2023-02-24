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
        $search = strval($request->request('search'));

        $search = strip_tags($search);

        if (mb_strlen($search) == 0) return $query;

        if ($search) {
            $query->where(function (&$nested) use ($columns, $search) {
                foreach ($columns as $column) {
                    $nested->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
        }

        return $query;
    }

    /**
     * Динамическая подгрузка
     */
    public function fetch(
        Request $request,
        Query $query,
        callable $callback,
        int $limit
    ): void {
        $html = '';

        $page = intval($request->request('page') ?? 1);

        $next = intval($request->request('next') ?? 1);

        $last = intval($request->request('last') ?? 1);

        $total = 0;

        $limit = intval($request->request('limit') ?? $limit);

        $total = $query->count();

        $pagination = new Pagination(
            total: $total,
            page: $next,
            limit: $limit
        );

        if ($page < $last) {
            $offset = $pagination->offset;

            if ($limit) $query->limit($limit);

            if ($offset) $query->offset($offset);

            $html .= $callback($query);
        }

        Response::json([
            'status' => 1,
            'html' => $html,
            ...get_object_vars($pagination)
        ]);
    }
}
