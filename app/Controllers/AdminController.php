<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{
    Article,
    Category,
    User
};

use Exception;

use Tischmann\Atlantis\{
    Alert,
    Breadcrumb,
    Controller,
    CSRF,
    Locale,
    Pagination,
    Request,
    Response,
    Sorting,
    Template,
    View
};

class AdminController extends Controller
{
    /**
     * Вывод главной страницы админпанели
     */
    public function index()
    {
        $this->checkAdmin();

        View::send(
            'admin/index',
            [
                'breadcrumbs' => [new Breadcrumb(Locale::get('dashboard'))],
            ]
        );
    }

    /**
     * Вывод списка локалей в админпанели
     */
    public function getLocales(Request $request): void
    {
        $this->checkAdmin();

        View::send(
            'admin/locales',
            [
                'breadcrumbs' => [
                    new Breadcrumb(
                        url: "/" . getenv('APP_LOCALE') . '/admin',
                        label: Locale::get('dashboard')
                    ),
                    new Breadcrumb(
                        label: Locale::get('locales')
                    ),
                ],
                'locales' => Locale::available(),
            ]
        );
    }

    /**
     * Форма добавления локали
     *
     * @param Request $request
     * 
     * @return void
     */
    public function newLocale(Request $request)
    {
        $this->checkAdmin();

        $this->getLocalesEditor();
    }

    /**
     * Вывод формы добавления/редактирования локали
     * 
     * @param Category $category Категория
     */
    public function getLocalesEditor(string $locale = '')
    {
        $this->checkAdmin();

        $title = Locale::get("locale_" . ($locale ? $locale : 'new'));

        $breadcrumbs = [
            new Breadcrumb(
                url: "/" . getenv('APP_LOCALE') . '/admin',
                label: Locale::get('dashboard')
            ),
            new Breadcrumb(
                url: "/" . getenv('APP_LOCALE') .  '/admin/locales',
                label: Locale::get('locales')
            ),
            new Breadcrumb($title)
        ];

        static::setTitle($title);

        View::send(
            'admin/locale',
            [
                'breadcrumbs' => $breadcrumbs,
                'locale' => $locale,
                'title' => $locale ? $title : '',
                'strings' => $locale ? Locale::getLocale($locale) : ['' => ''],

            ]
        );
    }

    /**
     * Добавление локали
     * 
     * @param Request $request Запрос
     * 
     * @throws Exception
     */
    public function addLocale(Request $request)
    {
        $this->checkAdmin();

        CSRF::verify($request);

        $request->validate([
            'title' => ['required', 'string'],
            'code' => ['required', 'string'],
            'keys' => ['required', 'array'],
            'values' => ['required', 'array'],
        ]);

        $code = $request->request('code');

        $title = $request->request('title');

        $keys = $request->request('keys');

        $values = $request->request('values');

        $file = <<<EOL
        <?php

        return [
            'locale_{$code}' => '$title',
        EOL;

        $file .= PHP_EOL;

        foreach ($keys as $key => $value) {
            if (empty($value)) continue;

            $file .= <<<EOL
                '{$value}' => '{$values[$key]}',
            EOL;

            $file .= PHP_EOL;
        }

        $file .= <<<EOL
        ];
        EOL;

        if (!is_dir(getenv('APP_ROOT') . '/lang')) {
            mkdir(getenv('APP_ROOT') . '/lang', 0755, true);
        }

        $result = file_put_contents(
            getenv('APP_ROOT') . '/lang/' . $code . '.php',
            $file
        );

        Response::redirect(
            '/' . getenv('APP_LOCALE') . '/admin/locales',
            new Alert(
                status: $result ? -1 : 0,
                message: Locale::get($result ? 'locale_saved' : 'locale_save_error')
            )
        );
    }

    /**
     * Вывод формы редактирования локали
     *
     * @param Request $request
     * 
     * @throws Exception
     */
    public function getLocale(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $code = $request->route('code');

        if (!Locale::exists($code)) {
            throw new Exception(Locale::get('locale_not_found'));
        }

        $this->getLocalesEditor($code);
    }

    /**
     * Редактирование локали
     *
     * @param Request $request
     * @return void
     */
    public function updateLocale(Request $request)
    {
        $this->checkAdmin();

        CSRF::verify($request);

        $request->validate([
            'keys' => ['required', 'array'],
            'values' => ['required', 'array'],
        ]);

        $code = $request->route('code');

        if (!Locale::exists($code)) {
            throw new Exception(Locale::get('locale_not_found'));
        }

        $keys = $request->request('keys');

        $values = $request->request('values');

        $file = <<<EOL
        <?php

        return [
        EOL;

        $file .= PHP_EOL;

        foreach ($keys as $key => $value) {
            if (empty($value)) continue;

            $file .= <<<EOL
                '{$value}' => '{$values[$key]}',
            EOL;

            $file .= PHP_EOL;
        }

        $file .= <<<EOL
        ];
        EOL;

        if (!is_dir(getenv('APP_ROOT') . '/lang')) {
            mkdir(getenv('APP_ROOT') . '/lang', 0755, true);
        }

        $result = file_put_contents(
            getenv('APP_ROOT') . '/lang/' . $code . '.php',
            $file
        );

        Response::redirect(
            '/' . getenv('APP_LOCALE') . '/admin/locales',
            new Alert(
                status: $result ? -1 : 0,
                message: Locale::get($result ? 'locale_saved' : 'locale_save_error')
            )
        );
    }

    /**
     * Удаление локали
     *
     * @param Request $request
     *
     */
    public function deleteLocale(Request $request)
    {
        $this->checkAdmin();

        CSRF::verify($request);

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $code = $request->route('code');

        if (!Locale::exists($code)) {
            throw new Exception(Locale::get('locale_not_found'));
        }

        $result = unlink(getenv('APP_ROOT') . '/lang/' . $code . '.php');

        Response::send(new Alert(
            status: intval($result),
            message: $result
                ? Locale::get('locale_deleted')
                : Locale::get('locale_delete_error')
        ));
    }

    /**
     * Вывод списка пользователь в админпанели
     */
    public function getUsers(Request $request): void
    {
        $this->checkAdmin();

        $query = User::query()->limit(Pagination::DEFAULT_LIMIT);

        View::send(
            'admin/users',
            [
                'breadcrumbs' => [
                    new Breadcrumb(
                        url: '/admin',
                        label: Locale::get('dashboard')
                    ),
                    new Breadcrumb(
                        label: Locale::get('users')
                    ),
                ],
                'users' => User::fill($query),
            ]
        );
    }

    /**
     * Вывод списка категорий в админпанели
     */
    public function getCategories(Request $request): void
    {
        $this->checkAdmin();

        $items = [];

        $query = Category::query()
            ->where('parent_id', null)
            ->order('position', 'ASC');

        foreach (Category::fill($query) as $category) {
            assert($category instanceof Category);

            if (!array_key_exists($category->locale, $items)) {
                $items[$category->locale] = [];
            }

            $items[$category->locale][] = $category;
        }

        View::send(
            'admin/categories',
            [
                'breadcrumbs' => [
                    new Breadcrumb(
                        url: '/admin',
                        label: Locale::get('dashboard')
                    ),
                    new Breadcrumb(
                        label: Locale::get('categories')
                    ),
                ],
                'items' => $items,
            ]
        );
    }

    /**
     * Вывод формы редактирования категории
     *
     * @param Request $request
     * 
     * @throws Exception
     */
    public function getCategory(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'id' => ['required'],
        ]);

        $id = intval($request->route('id'));

        $category = Category::find($id);

        assert($category instanceof Category);

        if (!$category->id) {
            throw new Exception(Locale::get('category_not_found'));
        }

        $this->getCategoryEditor($category);
    }

    /**
     * Форма добавления категории
     *
     * @param Request $request
     * 
     * @return void
     */
    public function newCategory(Request $request)
    {
        $this->checkAdmin();

        $this->getCategoryEditor();
    }

    /**
     * Вывод формы добавления/редактирования категории
     * 
     * @param Category $category Категория
     */
    public function getCategoryEditor(Category $category = new Category())
    {
        $this->checkAdmin();

        $parentBreadcrumbs = [];

        if ($category->parent_id) {
            $parent = Category::find($category->parent_id);

            assert($parent instanceof Category);

            while (true) {
                $parentBreadcrumbs[] = new Breadcrumb(
                    $parent->title,
                    '/category/edit/' . $parent->id
                );

                $parent = Category::find($parent->parent_id);

                if (!$parent->id) break;
            }
        }

        $parentBreadcrumbs = array_reverse($parentBreadcrumbs);

        $breadcrumbs = [
            new Breadcrumb(
                url: '/admin',
                label: Locale::get('dashboard')
            ),
            new Breadcrumb(
                url: '/admin/categories',
                label: Locale::get('categories')
            ),
            ...$parentBreadcrumbs
        ];

        if ($category->id) {
            $breadcrumbs[] = new Breadcrumb($category->title);

            static::setTitle($category->title);
        } else {
            $breadcrumbs[] = new Breadcrumb(Locale::get('category_new'));
        }

        View::send(
            'admin/category',
            [
                'breadcrumbs' => $breadcrumbs,
                'category' => $category,

            ]
        );
    }

    /**
     * Вывод списка статей в админпанели
     */
    public function getArticles(Request $request): void
    {
        $this->checkAdmin();

        $query = Article::query()->limit(Pagination::DEFAULT_LIMIT);

        $sort = $request->request('sort') ?: 'id';

        $order = $request->request('order') ?: 'desc';

        $query->order($sort, $order);

        View::send(
            'admin/articles',
            [
                'breadcrumbs' => [
                    new Breadcrumb(
                        url: '/admin',
                        label: Locale::get('dashboard')
                    ),
                    new Breadcrumb(
                        label: Locale::get('articles')
                    ),
                ],
                'articles' => Article::fill($query),
                'sortings' => [
                    new Sorting(),
                    new Sorting('title', 'asc'),
                    new Sorting('title', 'desc'),
                    new Sorting('created_at', 'asc'),
                    new Sorting('created_at', 'desc'),
                    new Sorting('updated_at', 'asc'),
                    new Sorting('updated_at', 'desc'),
                    new Sorting('visible', 'asc'),
                    new Sorting('visible', 'desc'),
                ]
            ]
        );
    }

    /**
     * Динамическая подгрузка статей в админпанели
     */
    public function fetchArticles(Request $request): void
    {
        ArticlesController::fetchArticles(
            $request,
            'admin/articles-item'
        );
    }

    /**
     * Динамическая подгрузка статей в админпанели
     */
    public function fetchUsers(Request $request): void
    {
        $pagination = new Pagination();

        $html = '';

        $page = 1;

        $total = 0;

        $limit = intval($request->request('limit') ?? Pagination::DEFAULT_LIMIT);

        $query = User::query();

        $sort = $request->request('sort') ?: 'id';

        $order = $request->request('order') ?: 'desc';

        $query->order($sort, $order);

        $total = $query->count();

        if ($total > $limit) {
            $page = intval($request->request('page') ?? 1);

            $offset = ($page - 1) * $limit;

            if ($limit) $query->limit($limit);

            if ($offset) $query->offset($offset);

            foreach (User::fill($query) as $user) {
                $html .= Template::html(
                    'admin/user-item',
                    [
                        'user' => $user,
                    ]
                );
            }
        }

        $pagination = new Pagination(
            total: $total,
            page: $page,
            limit: $limit
        );

        Response::json([
            'status' => 1,
            'html' => $html,
            ...get_object_vars($pagination)
        ]);
    }

    /**
     * Вывод формы редактирования пользователя
     *
     * @param Request $request
     * 
     * @throws Exception
     */
    public function editUser(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'id' => ['required'],
        ]);

        $id = intval($request->route('id'));

        $user = User::find($id);

        assert($user instanceof User);

        if (!$user->id) {
            throw new Exception(Locale::get('user_not_found'));
        }

        $this->getUserEditor($user);
    }

    /**
     * Вывод формы добавления/редактирования пользователя
     * 
     * @param Category $category Категория
     */
    public function getUserEditor(User $user = new User())
    {
        $this->checkAdmin();

        static::setTitle($user->id ? $user->login : Locale::get('user_new'));

        View::send(
            'admin/user',
            [
                'breadcrumbs' => [
                    new Breadcrumb(
                        url: '/admin',
                        label: Locale::get('dashboard')
                    ),
                    new Breadcrumb(
                        url: '/admin/users',
                        label: Locale::get('users')
                    ),
                    new Breadcrumb($user->id ? $user->login : Locale::get('user_new'))
                ],
                'user' => $user,

            ]
        );
    }
}
