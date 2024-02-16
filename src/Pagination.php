<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Класс пагинатора для постраничной навигации
 */
class Pagination
{
    public const DEFAULT_LIMIT = 10; // Количество элементов на странице по умолчанию

    public array $prev_pages = []; // Массив предыдущих от текущей страниц

    public array $next_pages = []; // Массив следующих от текущей страниц
    /**
     * Конструктор
     * 
     * @param int $total Общее количество элементов
     * @param int $page Текущая страница
     * @param int $limit Количество элементов на странице
     * @param int $first Номер первой страницы
     * @param int $prev Номер предыдущей страницы
     * @param int $next Номер следующей страницы
     * @param int $last Номер последней страницы
     * @param int $offset Смещение от начала списка
     * @param int $pages_prev_limit Количество страниц слева от текущей
     * @param int $pages_next_limit Количество страниц справа от текущей
     * 
     */
    public function __construct(
        public int $total = 0,
        public int $page = 1,
        public int $limit = 5,
        public int $first = 1,
        public int $prev = 1,
        public int $next = 1,
        public int $last = 1,
        public int $offset = 0,
        public int $pages_prev_limit = 3,
        public int $pages_next_limit = 3,
        public ?Query $query = null
    ) {
        $this->setTotal($this->total <= 0 ? 0 : $this->total)
            ->setPage($this->page <= 1 ? 1 : $this->page)
            ->setLimit($this->limit <= 0 ? static::DEFAULT_LIMIT : $this->limit)
            ->compute();

        if ($query) $this->query(query: $query);
    }

    /**
     * Установка общего количества элементов
     * 
     * @param int $total Общее количество элементов
     * 
     * @return self
     */
    public function setTotal(int $total): self
    {
        $this->total = abs($total);

        return $this;
    }

    /**
     * Установка текущей страницы
     * 
     * @param int $page Номер страницы
     * 
     * @return self
     */
    public function setPage(int $page): self
    {
        $this->page = abs($page) ?: 1;

        return $this;
    }

    /**
     * Установка количества элементов на странице
     * 
     * @param int $limit Количество элементов на странице
     * 
     * @return self
     */
    public function setLimit(int $limit): self
    {
        $this->limit = abs($limit) ?: $this->total;

        return $this;
    }

    /**
     * Установка номера первой страницы
     * 
     * @param int $first Номер первой страницы
     * 
     * @return self
     */
    public function setFirst(int $first): self
    {
        $this->first = abs($first) ?: 1;

        return $this;
    }

    /**
     * Установка номера предыдущей страницы
     * 
     * @param int $prev Номер предыдущей страницы
     * 
     * @return self
     */
    public function setPrev(int $prev): self
    {
        $this->prev = abs($prev) ?: 1;

        return $this;
    }

    /**
     * Установка номера следующей страницы
     * 
     * @param int $next Номер следующей страницы
     * 
     * @return self
     */
    public function setNext(int $next): self
    {
        $this->next = abs($next);

        return $this;
    }

    /**
     * Установка номера последней страницы
     * 
     * @param int $last Номер последней страницы
     * 
     * @return self
     */
    public function setLast(int $last): self
    {
        $this->last = abs($last);

        return $this;
    }

    /**
     * Установка смещения от начала списка
     * 
     * @param int $offset Смещение от начала списка
     * 
     * @return self
     */
    public function setOffset(int $offset): self
    {
        $this->offset = abs($offset);

        return $this;
    }

    /**
     * Сброс параметров пагинатора
     * 
     * @return self
     */
    public function reset(): self
    {
        $this->total = 0;
        $this->first = 1;
        $this->last = 1;
        $this->prev = 1;
        $this->next = 1;
        $this->offset = 0;
        $this->page = 1;
        $this->prev_pages = [];
        $this->next_pages = [];
        return $this;
    }

    /**
     * Пересчёт параметров пагинатора
     * 
     * @return self
     */
    public function compute(): self
    {
        if ($this->total === 0) return $this->reset();

        $last = $this->limit ? intval(ceil($this->total / $this->limit)) : 1;

        $this->setLast($last);

        $this->setPage($this->page > $this->last ? $this->last : $this->page);

        $next = $this->page + 1 > $this->last ? $this->last : $this->page + 1;

        $this->setPrev($this->page - 1 < 1 ? 1 : $this->page - 1)
            ->setNext($next)
            ->setOffset(max(intval(($this->page - 1) * $this->limit),  0));

        $this->prev_pages = $this->computePrevPages();

        $this->next_pages = $this->computeNextPages();

        return $this;
    }

    /**
     * Получение количества элементов на уменьшение
     * 
     * @return array
     */
    protected function computePrevPages(): array
    {
        $prev_pages = [];

        $page = $this->page - 1;

        while (true) {
            if ($page < 1) break;

            if (count($prev_pages) === $this->pages_prev_limit) break;

            $prev_pages[] = $page--;
        }

        return array_reverse($prev_pages);
    }

    /**
     * Получение количества элементов на увеличение
     * 
     * @return array
     */
    protected function computeNextPages(): array
    {
        $next_pages = [];

        $page = $this->page + 1;

        while (true) {
            if ($page > $this->last) break;

            if (count($next_pages) === $this->pages_next_limit) break;

            $next_pages[] = $page++;
        }

        return $next_pages;
    }

    /**
     * Получение строки запроса на уменьшение
     * 
     * @return string Строка запроса 
     */
    public function getPrevQuery(): string
    {
        $request = new Request();

        $data = $request->get();

        $data['page'] = $this->prev;

        return http_build_query($data);
    }

    /**
     * Получение строки запроса на увеличение
     * 
     * @return string Строка запроса 
     */
    public function getNextQuery(): string
    {
        $request = new Request();

        $data = $request->get();

        $data['page'] = $this->next;

        return http_build_query($data);
    }

    /**
     * Получение строки запроса на первую страницу
     * 
     * @return string Строка запроса 
     */
    public function getFirstQuery(): string
    {
        $request = new Request();

        $data = $request->get();

        $data['page'] = $this->first;

        return http_build_query($data);
    }

    /**
     * Получение строки запроса на последнюю страницу
     * 
     * @return string Строка запроса 
     */
    public function getLastQuery(): string
    {
        $request = new Request();

        $data = $request->get();

        $data['page'] = $this->last;

        return http_build_query($data);
    }

    /**
     * Получение строки запроса на указанную страницу
     * 
     * @param int $page Номер страницы
     * 
     * @return string Строка запроса 
     */
    public function getPageQuery(int $page): string
    {
        $request = new Request();

        $data = $request->get();

        $data['page'] = $page;

        return http_build_query($data);
    }

    /**
     * Получение запроса на текущую страницу
     * 
     * @param Query Запрос 
     * @param int|null $page Номер страницы
     * @param int|null $limit Количество элементов на странице
     * 
     * @return Query Запрос 
     */
    public function query(
        Query &$query,
        ?int $page = null,
        ?int $limit = null
    ): Query {
        $request = Request::instance();

        $page ??= 1;

        if ($request->request('page') !== null) {
            $page = intval($request->request('page'));
        }

        $limit ??= static::DEFAULT_LIMIT;

        if ($request->request('limit') !== null) {
            $limit = intval($request->request('limit'));
        }

        $offset = 0;

        $total = $query->count();

        $max_pages = intval(ceil($total / $limit));

        if ($page < 1) $page = 1;
        else if ($page > $max_pages) $page = $max_pages;

        $offset = ($page - 1) * $limit;

        $query->limit($limit)->offset($offset);

        $this->setTotal($total)
            ->setPage($page)
            ->setLimit($limit)
            ->compute();

        return $query;
    }
}
