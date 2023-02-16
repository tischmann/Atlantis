<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{Category};

use Exception;

use Tischmann\Atlantis\{
    Alert,
    Breadcrumb,
    Controller,
    CSRF,
    Locale,
    Request,
    Response,
    Template,
    View
};

class CategoriesController extends Controller
{
    public function index(Request $request): void
    {
        $this->checkAdmin();

        $categories = [];

        $query = Category::query()
            ->where('parent_id', null)
            ->order('position', 'ASC');

        foreach (Category::fill($query) as $category) {
            assert($category instanceof Category);

            if (!array_key_exists($category->locale, $categories)) {
                $categories[$category->locale] = [];
            }

            $categories[$category->locale][] = $category;
        }

        $localesItems = '';

        foreach ($categories as $locale => $cats) {
            $categoriesItems = '';

            foreach ($cats as $category) {
                $categoriesItems .= Template::make(
                    'admin/category-item',
                    [
                        'category_id' => $category->id,
                        'category_title' => $category->title
                    ]
                )->render();
            }

            $localesItems .= Template::make(
                'admin/category-locale',
                [
                    'csrf' => $this->getCsrfInput(),
                    'locale_title' => Locale::get('locale_' . $locale),
                    'items' => $categoriesItems,
                ]
            )->render();
        }

        Response::send(
            View::make(
                'admin/categories',
                [
                    'breadcrumbs' => AdminController::renderBreadcrumbs([
                        new Breadcrumb(
                            url: '/admin',
                            label: Locale::get('adminpanel')
                        ),
                        new Breadcrumb(
                            label: Locale::get('categories')
                        ),
                    ]),
                    'items' => $localesItems,
                    'app_title' => getenv('APP_TITLE') . " - " . Locale::get('categories'),
                ]
            )->render()
        );
    }

    public function addCategory(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'title' => ['required'],
            'slug' => ['required'],
            'locale' => ['required'],
        ]);

        CSRF::verify($request);

        $category = new Category();

        $category->title = $request->request('title');

        if (!strlen($category->title)) {
            throw new Exception('Title can not be empty');
        }

        $category->slug = $request->request('slug');

        if (!strlen($category->slug)) {
            throw new Exception('Slug can not be empty');
        }

        $category->locale = $request->request('locale');

        $parent_id = $request->request('parent_id');

        $category->parent_id = $parent_id ? intval($parent_id) : null;

        if (!$category->save()) {
            throw new Exception('Category not added');
        }

        Response::redirect(
            url: '/' . getenv('APP_LOCALE') . '/admin/categories',
            alert: new Alert(
                status: 1,
                message: Locale::get('category_added')
            )
        );
    }

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
            throw new Exception("Category ID:{$id} not found");
        }

        $this->getCategoryEditor($request, $category);
    }

    public function newCategory(Request $request)
    {
        $this->checkAdmin();

        $this->getCategoryEditor($request, new Category());
    }

    protected function getCategoryEditor(Request $request, Category $category)
    {
        $delete_button = '';

        $breadcrumbs = [
            new Breadcrumb(
                url: '/admin',
                label: Locale::get('adminpanel')
            ),
            new Breadcrumb(
                url: '/admin/categories',
                label: Locale::get('categories')
            ),
            ...$this->getParentBreadcrumbs($category)
        ];

        if ($category->id) {
            $delete_button = Template::make(
                template: 'admin/delete-button',
                args: ['href' => "/delete/category/{$category->id}"]
            )->render();

            $breadcrumbs[] = new Breadcrumb(label: $category->title);
        } else {
            $breadcrumbs[] = new Breadcrumb(label: Locale::get('category_new'));
        }

        Response::send(
            View::make(
                'admin/category',
                [
                    'csrf' => $this->getCsrfInput(),
                    'breadcrumbs' => AdminController::renderBreadcrumbs($breadcrumbs),
                    'category_id' => $category->id,
                    'category_title' => $category->title,
                    'category_slug' => $category->slug,
                    'delete_button' => $delete_button,
                    'locales_options' => $this->getLocalesOptions($category->locale),
                    'parents_options' => $this->getParentsOptions($category),
                    'category_children' => $this->getChildrenCategories($category),
                    'app_title' => getenv('APP_TITLE') . " - "
                        . ($category->id
                            ? $category->title
                            : Locale::get('category_new')),
                ]
            )->render()
        );
    }

    public function orderCategories(Request $request)
    {
        $this->checkAdmin();

        CSRF::verify($request);

        $categories = $request->request('categories');

        foreach ($categories as $position => $id) {
            $category = Category::find($id);

            assert($category instanceof Category);

            if (!$category->id) {
                throw new Exception("Category ID:{$id} not found");
            }

            $category->position = $position + 1;

            if (!$category->save()) {
                throw new Exception("Category ID:{$id} not saved");
            }
        }

        Response::redirect(
            url: '/' . getenv('APP_LOCALE') . '/admin/categories',
            alert: new Alert(
                status: 1,
                message: Locale::get('category_ordered')
            )
        );
    }

    public function updateCategory(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'id' => ['required'],
            'title' => ['required'],
            'slug' => ['required'],
            'locale' => ['required'],
        ]);

        CSRF::verify($request);

        $id = intval($request->route('id'));

        $category = Category::find($id);

        assert($category instanceof Category);

        if (!$category->id) {
            throw new Exception("Category ID:{$id} not found");
        }

        $title = strval($request->request('title'));

        $slug = strval($request->request('slug'));

        $locale = strval($request->request('locale'));

        $parent_id = $request->request('parent_id');

        $children = $request->request('children');

        if (is_array($children)) {
            foreach ($children as $position => $childId) {
                $child = Category::find($childId);

                assert($child instanceof Category);

                if (!$child->id) {
                    throw new Exception("Category ID:{$childId} not found");
                }

                $child->position = $position + 1;

                if (!$child->save()) {
                    throw new Exception("Category ID:{$childId} not saved");
                }
            }
        }

        $category->title = $title;

        $category->slug = $slug;

        $category->locale = $locale;

        $category->parent_id = $parent_id ? intval($parent_id) : null;

        if (!$category->save()) {
            throw new Exception("Category ID:{$id} not saved");
        }

        Response::redirect(
            url: '/' . getenv('APP_LOCALE') . '/admin/categories',
            alert: new Alert(
                status: 1,
                message: Locale::get('category_saved')
            )
        );
    }

    public function confirmDeleteCategory(Request $request)
    {
        $this->checkAdmin();

        $id = intval($request->route('id'));

        $category = Category::find($id);

        assert($category instanceof Category);

        if (!$category->id) {
            throw new Exception("Category ID:{$id} not found");
        }

        Response::send(View::make('admin/confirmation', [
            'csrf' => $this->getCsrfInput(),
            'back_url'  => '/admin/categories',
            'message' => Locale::get('category_delete_confirm')
                . " {$category->title}? "
                . Locale::get('category_children_will_be_deleted') . "!",
            'form_action' => "/category/delete/{$category->id}",
            'form_method' => 'POST',
            'app_title' => getenv('APP_TITLE') . " - "
                . Locale::get('category_delete_title'),
        ])->render());
    }

    public function deleteCategory(Request $request)
    {
        $this->checkAdmin();

        CSRF::verify($request);

        $id = intval($request->route('id'));

        $category = Category::find($id);

        assert($category instanceof Category);

        if (!$category->id) {
            throw new Exception("Category ID:{$id} not found");
        }

        foreach ($category->getAllChildren() as $child) {
            assert($child instanceof Category);

            if (!$child->id) continue;

            if (!$child->delete()) {
                throw new Exception("Category ID:{$child->id} not deleted");
            }
        }

        if (!$category->delete()) {
            Response::redirect(
                url: '/' . getenv('APP_LOCALE') . '/admin/categories',
                alert: new Alert(
                    status: 1,
                    message: Locale::get('category_delete_error')
                )
            );

            exit;
        }

        Response::redirect(
            url: '/' . getenv('APP_LOCALE') . '/admin/categories',
            alert: new Alert(
                status: 1,
                message: Locale::get('category_deleted')
            )
        );
    }

    public static function getParentsOptions(Category $category): string
    {
        $childrenID = array_keys($category->getAllChildren());

        $parents = Category::fill(
            Category::query()
                ->where('id', '!()', [
                    $category->id,
                    ...$childrenID
                ])
                ->order('title', 'ASC')
        );

        $parents = [new Category(), ...$parents];

        $parents_options = '';

        foreach ($parents as $parent) {
            assert($parent instanceof Category);

            $parents_options .= Template::make(
                'option',
                [
                    'value' => $parent->id ? $parent->id : '',
                    'title' => $parent->title,
                    'label' => $parent->title,
                    'selected' => $parent->id === $category->parent_id
                        ? 'selected' : ''
                ]
            )->render();
        }

        return $parents_options;
    }

    public static function getChildrenCategories(Category $category): string
    {
        $category_children = '';

        if ($category->children) {
            $categoryChilds = '';

            foreach ($category->children as $child) {
                assert($child instanceof Category);

                $categoryChilds .= Template::make(
                    template: 'admin/category-item',
                    args: [
                        'category_id' => $child->id,
                        'category_title' => $child->title,
                    ]
                )->render();
            }

            $category_children = Template::make(
                template: 'admin/category-children',
                args: [
                    'childs' => $categoryChilds
                ]
            )->render();
        }

        return $category_children;
    }

    public static function getParentBreadcrumbs(Category $category): array
    {
        $parentBreadcrumbs = [];

        if ($category->parent_id) {
            $parent = Category::find($category->parent_id);

            assert($parent instanceof Category);

            while (true) {
                $parentBreadcrumbs[] = new Breadcrumb(
                    url: '/category/edit/' . $parent->id,
                    label: $parent->title
                );

                $parent = Category::find($parent->parent_id);

                if (!$parent->id) break;
            }
        }

        return array_reverse($parentBreadcrumbs);
    }
}
