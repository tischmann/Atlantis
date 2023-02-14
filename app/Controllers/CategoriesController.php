<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{Category};

use Exception;

use Tischmann\Atlantis\{
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
                            label: Locale::get('adminpanel_categories')
                        ),
                    ]),
                    'items' => $localesItems
                ]
            )->render()
        );
    }

    public function getCategory(Request $request): void
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

        $childrenID = array_keys($category->getAllChildren());

        $parents = Category::fill(
            Category::query()
                ->where('id', '!()', [
                    $category->id,
                    ...$childrenID
                ])
                ->order('position', 'ASC')
        );

        $parents = [new Category(), ...$parents];

        $parents_options = '';

        foreach ($parents as $parent) {
            assert($parent instanceof Category);

            $parents_options .= Template::make(
                'option',
                [
                    'value' => $parent->id,
                    'title' => $parent->title,
                    'label' => $parent->title,
                    'selected' => $parent->id === $category->parent_id
                        ? 'selected' : ''
                ]
            )->render();
        }

        $breadcrumbs = [
            new Breadcrumb(
                url: '/admin',
                label: Locale::get('adminpanel')
            ),
            new Breadcrumb(
                url: '/categories',
                label: Locale::get('adminpanel_categories')
            ),
        ];

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

        $breadcrumbs = array_merge($breadcrumbs, array_reverse($parentBreadcrumbs));

        $breadcrumbs[] = new Breadcrumb(label: $category->title);

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

        Response::send(
            View::make(
                'admin/category',
                [
                    'csrf' => $this->getCsrfInput(),
                    'breadcrumbs' => AdminController::renderBreadcrumbs($breadcrumbs),
                    'category_id' => $category->id,
                    'category_title' => $category->title,
                    'category_slug' => $category->slug,
                    'locales_options' => $this->getLocalesOptions($category->locale),
                    'parents_options' => $parents_options,
                    'category_children' => $category_children,
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

        Response::redirect('/' . getenv('APP_LOCALE') . '/categories');
    }

    public function updateCategory(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'id' => ['required'],
            'title' => ['required'],
            'slug' => ['required'],
            'parent_id' => ['required'],
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

        $parent_id = intval($request->request('parent_id'));

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

        $category->parent_id = $parent_id;

        if (!$category->save()) {
            throw new Exception("Category ID:{$id} not saved");
        }

        Response::redirect('/' . getenv('APP_LOCALE') . '/categories');
    }
}
