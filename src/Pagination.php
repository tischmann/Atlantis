<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Класс пагинатора 
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
class Pagination
{
    public const DEFAULT_LIMIT = 5; // Количество элементов на странице по умолчанию

    public const PAGES_LIMIT = 3; // Количество страниц слева и справа от текущей

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
     * 
     */
    public function __construct(
        public int $total = 0,
        public int $page = 1,
        public int $limit = 0,
        public int $first = 1,
        public int $prev = 1,
        public int $next = 1,
        public int $last = 1,
        public int $offset = 0,
    ) {
        $this->setTotal($this->total <= 0 ? 0 : $this->total)
            ->setPage($this->page <= 1 ? 1 : $this->page)
            ->setLimit($this->limit <= 0 ? static::DEFAULT_LIMIT : $this->limit)
            ->compute();
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
     * Сброс значений по умолчанию
     * 
     * @return self
     */
    public function reset(): self
    {
        return $this->setTotal(0)
            ->setPage(1)
            ->setLimit(self::DEFAULT_LIMIT)
            ->setFirst(1)
            ->setPrev(1)
            ->setNext(1)
            ->setLast(1)
            ->setOffset(0);
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

        $this->prev_pages = [];

        $this->next_pages = [];

        $page = $this->page - 1;

        while (true) {
            if ($page < 1) break;

            if (count($this->prev_pages) === static::PAGES_LIMIT) break;

            $this->prev_pages[] = $page--;
        }

        $this->prev_pages = array_reverse($this->prev_pages);

        $page = $this->page + 1;

        while (true) {
            if ($page > $this->last) break;

            if (count($this->next_pages) === static::PAGES_LIMIT) break;

            $this->next_pages[] = $page++;
        }

        return $this;
    }

    public function getPrevQuery(): string
    {
        $request = new Request();

        $data = $request->get();

        $data['page'] = $this->prev;

        return http_build_query($data);
    }

    public function getNextQuery(): string
    {
        $request = new Request();

        $data = $request->get();

        $data['page'] = $this->next;

        return http_build_query($data);
    }

    public function getFirstQuery(): string
    {
        $request = new Request();

        $data = $request->get();

        $data['page'] = $this->first;

        return http_build_query($data);
    }

    public function getLastQuery(): string
    {
        $request = new Request();

        $data = $request->get();

        $data['page'] = $this->last;

        return http_build_query($data);
    }

    public function getPageQuery(int $page): string
    {
        $request = new Request();

        $data = $request->get();

        $data['page'] = $page;

        return http_build_query($data);
    }
}
