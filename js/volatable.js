'use strict'

function volatable(self, params = {}) {
    if ('_volatable' in self) {
        return self._volatable;
    }

    let fn = {};

    params.route = params.route || $$.data(self, 'route');

    params.layout = parseInt(params.layout || $$.data(self, 'layout')
        || $$.data($$.parents(self, '[data-layout]'), 'layout'), 10);

    params.window = params.window || $$.data(self, 'window')
        || $$.data($$.parents(self, '[data-window]'), 'window');

    params.pager = params.pager || parseFloat($$.data(self, 'pager'));

    params.limit = params.limit || $$.data(self, 'limit');

    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

    let config = {
        route: params.route,
        layout: params.layout,
        window: params.window,
        columns: {},
        qfilter: {},
        pagerSearchTimeout: 1000,
        pager: {
            first: 1,
            last: 1,
            limit: params.limit || calculateRowLimit($$.height(self.parentNode)),
            next: 1,
            offset: 0,
            page: 1,
            prev: 1,
            total: 0
        },
        order: {},
        resizable: true,
        sortable: true,
        searchable: true,
        filterable: true,
        hasQuickFilter: true,
        hideSearch: true,
        hasPager: !!params.pager
    };

    const lang = {
        ru: {
            search: {
                title: 'Найдено совпадений'
            },
            filter: {
                title: 'Отфильтровано строк',
                clear: 'СБРОС',
                apply: 'ПРИМЕНИТЬ',
            },
            cancel: 'Отмена',
            row: {
                new: 'Новая запись',
                add: 'Добавить'
            }
        }
    };

    let resizeObserver = null;
    let resizeTimeout = null;
    let pagerSearchTimeout = null;

    const wrapper = $$.create('div', { class: 'volatable-wrapper' });
    const header = $$.create('div', { class: 'volatable-header' });
    const tbody = $$.create('tbody');
    const thead = $$.create('thead');
    const header_table = $$.create('table');
    let header_thead = $$.create('thead');
    const bar = $$.create('div', { class: 'volatable-bar' });
    const pager = $$.create('div', { class: 'volatable-pager' });
    const search = $$.create('tr', { class: ['search', 'hidden'] });
    const searchResult = $$.create('div', {
        class: ['volatable-search-result', 'hidden'],
        'data-title': lang.ru.search.title
    });
    const filterResult = $$.create('div', {
        class: ['volatable-filter-result', 'hidden'],
        'data-title': lang.ru.filter.title
    });
    const qfilter = $$.create('div', { class: 'volatable-qfilter' });
    let row_extras = undefined;
    let edit_cell = undefined;
    let edit_input = undefined;

    function calculateRowLimit(tableHeight) {
        return Math.floor(tableHeight / 24 - 3);
    }

    function ajax(options = {}) {
        options.action = options.action || null;
        options.data = options.data || {};
        options.success = options.success || function () { };
        options.failure = options.failure || function () { };

        options.data = {
            ...options.data,
            action: options.action,
            layout: config.layout,
            window: config.window
        };

        if (config.hasPager) {
            options.data.page = config.pager.page;
            options.data.limit = config.pager.limit;
        }

        $$.ajax({
            url: config.route,
            method: 'POST',
            headers: {
                'Content-Type': 'application/json;charset=utf-8',
                'X-CSRF-Token': CSRF_TOKEN
            },
            data: options.data,
            success: function (json) {
                if (!json.status) {
                    alert(json.message);
                    options.failure(json.message);
                } else {
                    options.success(json);
                }
            }
        });
    }

    function init(data) {
        config.pager = data.pager;
        config.qfilter = data.qfilter;

        initHeader(data.thead);
        initRows(data.tbody);

        self.parentNode.appendChild(wrapper);

        self.appendChild(tbody);
        wrapper.appendChild(self);
        wrapper.appendChild(header);

        $$.on(wrapper, 'scroll', wrapperScrollHandler);

        setTableWidth();

        if (config.resizable) {
            initResizers();
        }

        if (config.sortable) {
            initSorters();
            sort(true);
        }

        if (config.searchable) {
            initSearch();
        }

        if (config.filterable) {
            initFilter();
        }

        if (config.hasQuickFilter) {
            initQuickFilter();
        }

        initStatusBar();

        if (config.hasPager) {
            if (tbody.rows.length < calculatePagerLimit()) {
                self.style.height.style.height = 'auto';
            } else {
                self.style.height = '100%';
            }

            resizeObserver = new ResizeObserver(pagerResiseHandler);
            resizeObserver.observe(wrapper.parentNode);
        }
    }

    function searchKeyUpHandler() {
        config.columns[this.parentNode.cellIndex].search = this.value;
        searchValue();
    }

    function searchFocusHandler() {
        search.setAttribute('data-locked', 1);
    }

    function searchBlurHandler() {
        search.setAttribute('data-locked', 0);

        for (const args of Object.values(config.columns)) {
            if (args.search) {
                search.setAttribute('data-locked', 1);
                break;
            }
        };

        if (!parseFloat(search.getAttribute('data-locked'))) {
            search.classList.add('hidden');
            thead.rows[1].classList.add('hidden');
        }
    }

    function searchBarMouseEnter() {
        this.classList.remove('hidden');
        thead.rows[1].classList.remove('hidden');

        if (config.hasPager) {
            const limit = calculatePagerLimit();

            if (tbody.rows.length < limit) {
                self.style.height = 'auto';
            } else {
                self.style.height = $$.height(wrapper) + 'px';
            }
        }
    }

    function searchBarMouseLeave() {
        search.setAttribute('data-locked', 0);

        for (const args of Object.values(config.columns)) {
            if (args.search) {
                search.setAttribute('data-locked', 1);
            }
        }

        if (parseFloat(search.getAttribute('data-locked'))) {
            return false;
        }

        this.classList.add('hidden');
        thead.rows[1].classList.add('hidden');

        if (config.hasPager) {
            pagerTableAutosize();
        }
    }

    function initSearch() {
        const clone = search.cloneNode(true);

        Array.from(header_thead.rows[0].cells).forEach(function (cell) {
            const th = $$.create('th');
            const index = cell.cellIndex;

            clone.appendChild(th);

            if (!index) {
                th.classList.add('icon');
            } else if (config.columns[index].search !== undefined) {

                const input = $$.create('input', {
                    id: `search_${config.columns[index].column}_${config.window}`,
                    type: 'text',
                    spellcheck: false
                });

                th.appendChild(input);

                $$.on(input, 'keyup', searchKeyUpHandler);

                if (config.hideSearch) {
                    $$.on(input, 'focus', searchFocusHandler);
                    $$.on(input, 'blur', searchBlurHandler);
                }
            }

            searchResult.title = searchResult.title;
            search.appendChild(th);
        });

        if (config.hideSearch) {
            $$.on(search, 'mouseenter', searchBarMouseEnter);
            $$.on(search, 'mouseleave', searchBarMouseLeave);
        }

        bar.appendChild(searchResult);
        header_thead.appendChild(search);
        thead.appendChild(clone);
    }

    function searchValue() {
        clearFilter();
        resetSearchResult();
        removeSearchHighlights();

        if (config.hasPager) {
            clearTimeout(pagerSearchTimeout);
            pagerSearchTimeout = setTimeout(loadPager, config.pagerSearchTimeout);
        } else {
            highlightSearchResults();
        }

    }

    function highlightSearchResults() {
        let foundTotal = 0;
        let searched = false;
        let rows = Array.from(tbody.rows);

        rows.forEach(row => {
            row.classList.add('hidden');
            row.classList.remove('found');

            Array.from(row.cells).forEach(td => {
                td.classList.remove('found');
                $$.unhighlight(td);
            });
        });

        for (let [index, args] of Object.entries(config.columns)) {
            const val = args.search || '';
            let found = 0;

            if (val == '') continue;

            searched = true;

            if (foundTotal) {
                rows = rows.filter(row => {
                    return Array.from(row.classList).includes('found');
                });

                rows.forEach(row => {
                    row.classList.add('hidden');
                    row.classList.remove('found');
                });
            }

            rows.forEach(row => {
                $$.highlight(row.cells[index], val, function (node) {
                    row.classList.remove('hidden');
                    row.classList.add('found');
                    node.classList.add('found');
                    foundTotal++;
                    found++;
                });
            });

            if (!found && foundTotal) {
                return false;
            }
        }

        if (!searched) {
            resetQuickFilter();
        }

        if (!foundTotal) {
            return rows.forEach(row => row.classList.remove('hidden'));
        }

        setSearchResult(foundTotal);
    }

    function removeSearchHighlights() {
        Array.from($$.find(tbody, 'td.found')).forEach(td => {
            $$.unhighlight(td);
            td.parentNode.classList.remove('hiden', 'found');
            td.classList.remove('found');
        });
    }

    function setSearchResult(count) {
        searchResult.classList.remove('hidden');
        searchResult.innerHTML = count;
        searchResult.title = `${searchResult.getAttribute('data-title')}: ${count}`;
    }

    function resetSearch() {
        resetSearchResult();
        removeSearchHighlights();
        $$.find(search, 'input').forEach(input => input.value = '');
        search.classList.add('hidden');
        thead.rows[1].classList.add('hidden');

        for (const args of Object.values(config.columns)) {
            if (args.search) {
                delete args.search;
            }
        }
    }

    function resetSearchResult() {
        searchResult.innerHTML = ''
        searchResult.classList.add('hidden');
        searchResult.title = '';
    }

    function initFilter() {
        for (const index of Object.keys(config.columns)) {
            if (config.columns[index].filter) {
                $$.on(
                    header_thead.rows[0].cells[index],
                    'contextmenu',
                    filterContextmenuHandler
                );
            }
        }

        bar.appendChild(filterResult);
    }

    function filterContextmenuHandler(event) {
        event.preventDefault();
        event.stopPropagation();

        const current = $$.exist(header, `.volatable-filter[data-index="${this.cellIndex}"]`);

        hideFilter();

        if (!current) {
            const filter = $$.create('div', {
                class: ['volatable-filter', 'loader'],
                style: {
                    top: $$.height(header_thead.rows[0]) + 'px',
                    left: this.offsetLeft - 1 + 'px',
                    'min-width': $$.width(this) + 20 + 'px'
                },
                'data-index': this.cellIndex
            });

            if (config.hasPager) {
                ajax({
                    action: 'getfilter',
                    data: {
                        column: config.columns[this.cellIndex].column
                    },
                    success: (json) => {
                        filter.appendChild(getFilterItems(this, json.items));
                        filter.classList.remove('loader');
                        filter.appendChild(getFilterApply());
                        filter.appendChild(getFilterClear());
                    }
                });
            } else {
                filter.appendChild(getFilterItems(this));
                filter.appendChild(getFilterClear());
            }

            header.appendChild(filter);
        }
    }

    function clearFilterClickHandler() {
        clearFilter();

        if (config.hasQuickFilter) {
            resetQuickFilter();
        } else if (config.hasPager) {
            loadPager();
        }
    }

    function applyFilterClickHandler() {
        hideFilter();
        resetSearch();
        loadPager();
    }

    function getFilterClear() {
        const clear = $$.create('div', { class: 'clear' });
        clear.innerHTML = lang.ru.filter.clear;
        $$.on(clear, 'click', clearFilterClickHandler);
        return clear;
    }

    function getFilterApply() {
        const apply = $$.create('div', { class: 'apply' });
        apply.innerHTML = lang.ru.filter.apply;
        $$.on(apply, 'click', applyFilterClickHandler);
        return apply;
    }

    function getFilterItems(th, ajax = {}) {
        let items = {};
        const index = th.cellIndex;
        const list = $$.create('div', {
            class: 'volatable-filter-list',
            'data-index': index
        });

        if (config.hasPager) {
            for (const value of Object.values(ajax)) {
                for (const args of Object.values(value)) {
                    items[args.v] = initFilterItem(index, args.v, args.t);
                }
            }
        } else {
            Array.from(tbody.rows).forEach(function (tr) {
                const td = tr.cells[index];
                const text = td.title || td.textContent;

                if (!Object.keys(items).includes(text)) {
                    items[text] = initFilterItem(index, text);
                }
            });

            items = Object.keys(items)
                .sort()
                .reduce((r, k) => ((r[k] = items[k]), r), {});
        }

        for (const val of Object.values(items)) {
            list.appendChild(val);
        }

        return list;
    }

    function getFilterClear() {
        const clear = $$.create('div', { class: 'clear' });
        clear.innerHTML = lang.ru.filter.clear;

        $$.on(clear, 'click', filterClearHandler);

        return clear;
    }

    function filterClearHandler() {
        clearFilter();

        if (config.hasQuickFilter) {
            resetQuickFilter();
        } else if (config.hasPager) {
            loadPager();
        }
    }

    function initFilterItem(index, value, title = null) {
        const filter = config.columns[index].filter || [];

        const item = $$.create('div', {
            class: 'item'
        });

        if (title !== null) {
            title = title + '';
            item.title = title;

            if (filter.includes(title)) {
                item.classList.add('selected');
            }
        } else if (filter.includes(value)) {
            item.classList.add('selected');
        }

        item.innerHTML = value;

        $$.on(item, 'click', filterClickHandler);

        return item;
    }

    function filterClickHandler() {
        const index = this.parentNode.getAttribute('data-index');
        const text = this.title || this.textContent;

        if (Array.from(this.classList).includes('selected')) {
            config.columns[index].filter = config.columns[index].filter.filter((e) => {
                return e !== text;
            });
        } else {
            config.columns[index].filter.push(text);
        }

        this.classList.toggle('selected');

        if (!config.hasPager) {
            filterValues();
        }
    }

    function filterValues() {
        resetSearch();
        resetFilter();

        let hasFilter = false;

        for (const [index, args] of Object.entries(config.columns)) {
            if (args.filter.length) {
                const th = header_thead.rows[0].cells[index];

                th.classList.add('filter');
                hasFilter = true;

                Array.from(tbody.rows).forEach(function (tr) {
                    const td = tr.cells[index];
                    let text = td.title || td.textContent;

                    if (!args.filter.includes(text)) {
                        tr.classList.add(`filtered`);
                    }
                });
            }
        }

        const count = $$.count(tbody, `tr:not(.filtered)`);

        if (count && hasFilter) {
            setFilterResult(count);
        }
    }

    function setFilterResult(count) {
        filterResult.classList.remove('hidden');
        filterResult.innerHTML = `<span>${count}</span>`;
        filterResult.title = `${filterResult.getAttribute('data-title')}: ${count}`;
    }

    function clearFilter() {
        resetFilter();

        for (const args of Object.values(config.columns)) {
            args.filter = [];
        }

        hideFilter();
    }

    function resetFilter() {
        $$.find(tbody, 'tr.filtered')
            .forEach(el => el.classList.remove('filtered'));
        Array.from(header_thead.rows[0].cells)
            .forEach(el => el.classList.remove('filter'));

        resetFilterResult();
    }

    function resetFilterResult() {
        filterResult.innerHTML = '';
        filterResult.classList.add('hidden');
        filterResult.title = '';
    }

    function hideFilter() {
        $$.find(header, '.volatable-filter').forEach(el => {
            Array.from($$.find(el, '.item, .clear, .apply')).forEach(item => $$.off(item));
            el.remove();
        });
    }

    function calculatePagerLimit() {
        return calculateRowLimit($$.height(wrapper.parentNode));
    }

    function pagerResiseHandler() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function () {
            const limit = calculatePagerLimit();

            if (limit != config.pager.limit) {
                config.pager.limit = limit;
                loadPager();
            }
        }, 1000);
    }

    function pagerTableAutosize() {
        self.style.height = 'auto';

        console.log(tbody.rows.length, calculatePagerLimit());

        if (tbody.rows.length == calculatePagerLimit()) {
            self.style.height = '100%';
        }
    }

    function initStatusBar() {
        if (config.hasPager) {
            pager.append(
                $$.create('div', { class: ['button', 'first'] }),
                $$.create('div', { class: ['button', 'prev'] }),
                $$.create('div', { class: ['button', 'page'] }),
                $$.create('div', { class: ['button', 'next'] }),
                $$.create('div', { class: ['button', 'last'] }));
            bar.prepend(pager);
            initPager();
        }

        wrapper.parentNode.appendChild(bar);
    }

    function initSorters() {
        for (const [index, args] of Object.entries(config.columns)) {
            if (args.sort) {
                $$.on(header_thead.rows[0].cells[index], 'click', sorterClickHandler);
            }
        }
    }

    function sorterClickHandler() {
        const dir = config.columns[this.cellIndex].dir == 'asc' ? 'desc' : 'asc';

        for (const [index, args] of Object.entries(config.columns)) {
            if (args.sort) {
                config.columns[index].dir = null;
                header_thead.rows[0].cells[index].setAttribute('data-dir', null);
            }
        }

        config.columns[this.cellIndex].dir = dir;
        this.setAttribute('data-dir', dir);

        saveTableState();

        sort();
    }

    function sort(skip = false) {
        for (const [index, args] of Object.entries(config.columns)) {
            if (!args.sort || !args.dir) {
                continue;
            }

            if (config.hasPager && !skip) {
                return loadPager();
            } else {
                const dir = args.dir == 'asc';
                let tr = Array.prototype.slice.call(tbody.rows, 0);

                function convertDateTime(date) {
                    if (date.length <= 8) {
                        return `T${date}`;
                    } else {
                        return date.slice(0, 10) + 'T' + date.slice(12);
                    }
                }

                function getCellValue(row, index) {
                    let cell = row.cells[index];
                    const title = cell.title.trim();
                    return title != '' ? title : cell.textContent.trim();
                }

                switch (args.sort) {
                    case 'number':
                        tr = tr.sort(function (a, b) {
                            a = parseFloat(getCellValue(a, index));
                            b = parseFloat(getCellValue(b, index));
                            return dir && a > b ? 1 : -1;
                        });
                        break;
                    case 'datetime':
                        tr = tr.sort(function (a, b) {
                            a = new Date(convertDateTime(getCellValue(a, index)));
                            b = new Date(convertDateTime(getCellValue(b, index)));
                            return dir && a > b ? 1 : -1;
                        });
                        break;
                    default:
                        tr = tr.sort(function (a, b) {
                            return (dir ? 1 : -1) * getCellValue(a, index)
                                .localeCompare(getCellValue(b, index), 'en');
                        });
                        break;
                }

                tr.forEach(row => tbody.appendChild(row));
            }
        }
    }

    function initQuickFilter() {
        const list = $$.create('div', { class: 'list' });

        for (const [key, args] of Object.entries(config.qfilter)) {
            const item = $$.create('div', {
                class: 'item',
                'data-type': key,
                'data-css': args.css,
                title: `${args.title}:${args.count}`,
                'data-default': args.default ? 1 : 0
            });

            item.innerHTML = `${args.label}:${args.count}`;

            $$.on(item, 'click', quickFilterClickHandler);

            list.appendChild(item);
        }

        qfilter.appendChild(list);

        bar.appendChild(qfilter);

        resetQuickFilter();
    }

    function quickFilterClickHandler() {
        clearFilter();
        resetSearch();

        if (config.hasPager) {
            for (const key of Object.keys(config.qfilter)) {
                config.qfilter[key].selected = false;
            }

            config.qfilter[this.getAttribute('data-type')].selected = true;
            loadPager();
        } else {
            Array.from(tbody.rows).forEach(el => el.classList.add('hidden'));
            const css = this.getAttribute('data-css') || 'hidden';

            $$.find(tbody, `tr.${css}`).forEach(el => {
                el.classList.remove('hidden');
            });
        }
    }

    function resetQuickFilter() {
        if (config.hasQuickFilter) {
            const item = $$.find(qfilter, `.item[data-default="1"]`);

            if (item.length) {
                $$.trigger(item[0], 'click');
            }
        }
    }

    function wrapperScrollHandler() {
        header.style.top = `${this.scrollTop}px`;
    }

    function setTableWidth() {
        const width = getTableWidth();
        header_table.style.width = self.style.width = `${width}px`;
    }

    function getTableWidth() {
        let width = 0;
        Object.values(config.columns).forEach(i => width += i.width || 0);
        return width;
    }

    function resetTable() {
        Array.from(tbody.rows).forEach(tr => {
            Array.from(tr.cells).forEach(td => $$.off(td));
            $$.off(tr);
            tr.remove();
        });
    }

    function getOrder() {
        let order = {};

        for (const args of Object.values(config.columns)) {
            if (args.dir) {
                order[args.column] = args.dir;
            }
        }

        return order;
    }

    function getSearch() {
        let search = {};

        for (const args of Object.values(config.columns)) {
            if (args.search) {
                search[args.column] = args.search;
            }
        }

        return search;
    }

    function getFilter() {
        let filter = {};

        for (const args of Object.values(config.columns)) {
            if (args.filter.length) {
                filter[args.column] = args.filter;
            }
        }

        return filter;
    }

    function getQuickFilter() {
        let qfilter = null;

        for (const [key, args] of Object.entries(config.qfilter)) {
            if (args.default) {
                qfilter = key;
                break;
            }
        }

        for (const [key, args] of Object.entries(config.qfilter)) {
            if (args.selected) {
                qfilter = key;
                break;
            }
        }

        return qfilter;
    }

    function loadPager() {
        resetTable();
        const search = getSearch();
        const filter = getFilter();

        ajax({
            action: 'get',
            data: {
                pager: true,
                order: getOrder(),
                search: search,
                filter: filter,
                qfilter: getQuickFilter()
            },
            success: (json) => {
                config.pager = json.pager;

                initRows(json.tbody);

                self.style.height = $$.height(wrapper);

                pagerTableAutosize();

                initPager();

                if (!$$.isEmpty(search)) {
                    highlightSearchResults();
                    setSearchResult(json.pager.total);
                }

                if (!$$.isEmpty(filter)) {

                    for (const [key, args] of Object.entries(config.columns)) {
                        if (args.filter.length) {
                            header_thead.rows[0].cells[key].classList.add('filter');
                        }
                    }

                    setFilterResult(json.pager.total);
                }
            }
        });
    }

    function initPager() {
        $$.find(pager, '.button:not(.page)').forEach(el => {
            $$.off(el);
            el.removeAttribute('data-page');
            el.classList.add('disabled');
        });

        $$.find(pager, '.first').forEach(el => {
            el.title = config.pager.first
            if (config.pager.first < config.pager.page) {
                el.classList.remove('disabled');
                el.setAttribute('data-page', config.pager.first);
                $$.on(el, 'click', pagerClickHandler);
            }
        });

        $$.find(pager, '.prev').forEach(el => {
            el.title = config.pager.prev;

            if (config.pager.prev < config.pager.page) {
                el.classList.remove('disabled');
                el.setAttribute('data-page', config.pager.prev);
                $$.on(el, 'click', pagerClickHandler);
            }
        });


        $$.find(pager, '.page').forEach(el => el.innerHTML = config.pager.page);

        $$.find(pager, '.next').forEach(el => {
            el.title = config.pager.next;

            if (config.pager.next > config.pager.page) {
                el.classList.remove('disabled');
                el.setAttribute('data-page', config.pager.next);
                $$.on(el, 'click', pagerClickHandler);
            }
        });

        $$.find(pager, '.last').forEach(el => {
            el.title = config.pager.last;

            if (config.pager.last > config.pager.page) {
                el.classList.remove('disabled');
                el.setAttribute('data-page', config.pager.last);
                $$.on(el, 'click', pagerClickHandler);
            }
        });
    }

    function pagerClickHandler() {
        config.pager.page = parseInt(this.getAttribute('data-page'), 10);
        resetTable();
        loadPager();
    }

    function initHeader(data) {
        const tr = $$.create('tr');
        const th = $$.create('th', {
            class: 'resizable-false',
            add: 1
        });

        $$.on(th, 'click', addTableRowClickHandler);

        tr.appendChild(th);

        let index = 1;

        for (let args of Object.values(data)) {
            config.columns[index] = {
                column: args.column,
                name: args.name,
                title: args.title,
                width: args.width,
                edit: args.edit,
                filter: [],
                resize: !!args.resize || false,
                mask: args.mask || null,
                sort: args.sort || null,
                dir: args.dir || null,
            };

            const th = $$.create('th', {
                title: args.title + '',
                style: {
                    width: `${args.width}px`
                }
            });

            th.innerHTML = args.name;

            if (args.sort) {
                $$.data(th, 'dir', args.dir);
            }

            if (args.search) {
                config.columns[index].search = '';
            }

            tr.appendChild(th);

            index++;
        }

        thead.appendChild(tr);
        self.appendChild(thead);

        header_table.setAttribute('class', self.getAttribute('class'));
        header_thead = thead.cloneNode(true);
        header_table.appendChild(header_thead);
        header.appendChild(header_table);
    }

    function addTableRowClickHandler() {
        alert('addTableRowClickHandler');
    }

    function initRows(rows) {
        console.log(rows);
        for (const row of Object.values(rows)) {
            tbody.appendChild(initRow(row));
        }
    }

    function initRow(data) {
        const tr = $$.create('tr', {
            class: data.c || '',
            'data-id': data.i
        });

        const td = $$.create('td', {
            extra: 1
        });

        $$.on(td, 'click', rowExtrasClickHandler);

        tr.appendChild(td);

        for (const val of Object.values(data.r)) {
            tr.appendChild(initTableRowCell(val));
        };

        return tr;
    }

    function initTableRowCell(data) {
        const td = $$.create('td', {
            class: data.c || ''
        });

        td.innerHTML = data.v;

        $$.on(td, 'click', cellClickHandler);

        if (data.t !== undefined && data.t !== null) {
            td.setAttribute('title', data.t + '');
        } else {
            td.setAttribute('title', data.v + '');
        }

        return td;
    }

    function cellClickHandler(event) {
        cellDiscardHandler();

        edit_cell = this;

        const rowId = parseFloat(this.parentNode.getAttribute('data-id'));
        const column = config.columns[this.cellIndex];

        if (!column.edit) {
            return false;
        }

        ajax({
            action: 'getcell',
            data: {
                id: rowId,
                column: column.column
            },
            success: (json) => {
                switch (json.type) {
                    case 'select':
                        return getEditSelect(this, json, event);
                    default:
                        return getEditInput(this, json, event);
                }
            }
        });
    }

    function createEditInput(td, data) {
        const editWrapper = $$.create('div', {
            class: 'volatable-edit-wrapper'
        });

        const width = td.offsetWidth + 1;
        const height = td.offsetHeight + 1;

        const input = $$.create('textarea', {
            class: 'volatable-edit-input',
            rows: 1,
            'data-id': td.parentNode.getAttribute('data-id'),
            'data-column': config.columns[td.cellIndex].column,
            style: {
                'font-family': getComputedStyle(td)['font-family'],
                'font-weight': getComputedStyle(td)['font-weight'],
                'font-size': getComputedStyle(td)['font-size'],
                'line-height': getComputedStyle(td)['line-height'],
                padding: getComputedStyle(td)['padding'],
                'width': width + 'px',
                'max-width': width * 2 + 'px',
                'min-width': width + 'px',
                'height': height + 'px',
                'min-height': height + 'px',
                'max-height': '50vh'
            }
        });

        input.value = data.value;

        editWrapper.append(input);
        editWrapper.style.top = td.offsetTop + 'px';
        editWrapper.style.left = td.offsetLeft + 'px';

        wrapper.append(editWrapper);

        // input.on('keydown', function (event) {
        //     cellInputKeydownHelper(event, td, input);
        // }).focus();

        // if (config.columns[td.cellIndex].mask) {
        //     input.css('resize', 'none')
        //         .inputmask({
        //             regex: config.columns[td.cellIndex].mask,
        //             isComplete: function (buffer, opts) {
        //                 return new RegExp(opts.regex).test(buffer.join(''));
        //             },
        //             oncomplete: function () {
        //                 input.removeClass('invalid');
        //             }
        //         })
        //         .on('keyup', function () {
        //             if (!input.inputmask("isComplete")) {
        //                 input.addClass('invalid');
        //             }
        //         })
        //         .trigger('keyup');
        // }

        return input;
    }

    function getEditInput(td, data, event) {
        const input = createEditInput(td, data);
        input.setAttribute('data-default', td.textContent);
        input.select();
        //inpur.autosize()
    }

    function cellDiscardHandler() {
        if (edit_input) {
            $$.off(edit_input);
            edit_input.remove();
            edit_input = undefined;
            edit_cell = undefined;
        }
    }

    function rowExtrasClickHandler(event) {
        const rowId = $$.data(event.currentTarget.parentNode, 'id');

        if (row_extras) {
            const extrasId = $$.data(row_extras, 'id');

            $$.find(tbody, 'td[extra]').forEach(el => {
                el.classList.remove('active');
            });

            row_extras.remove();
            row_extras = undefined;

            if (rowId == extrasId) {
                return false;
            }
        }

        this.classList.add('active');

        row_extras = $$.create('div', {
            class: 'volatable-row-extras',
            'data-id': rowId
        });

        row_extras.style.top = `${this.offsetTop + $$.height(this) + 1}px`
        row_extras.left = `${this.offsetLeft}`;
        row_extras.innerHTML = 'extra content goes here...';

        wrapper.appendChild(row_extras);
    }

    function initResizers() {
        for (const [index, obj] of Object.entries(config.columns)) {
            if (obj.resize) {
                initResizer(index);
            }
        }
    }

    function initResizer(index) {
        const element = header_thead.rows[0].cells[index];

        const handler = $$.create('div', {
            class: 'volatable-resize-handler'
        });

        element.appendChild(handler);

        const handlerWidth = $$.width(handler);

        handler.style.top = 0;
        handler.style.left = `${element.offsetWidth - handlerWidth / 2}px`;

        let x = 0;
        let width = 0;
        let newWidth = 0;

        function clickHandler(event) {
            event.stopPropagation();
        }

        function mouseDownHandler(event) {
            x = event.clientX;
            width = element.offsetWidth;
            document.addEventListener('mousemove', mouseMoveHandler);
            document.addEventListener('mouseup', mouseUpHandler);
        };

        function mouseMoveHandler(event) {
            const dx = event.clientX - x;

            newWidth = width + dx;
            newWidth = newWidth < 23 ? 23 : newWidth;

            element.style.width = newWidth + 'px';
            thead.rows[0].cells[index].style.width = newWidth + 'px';
            handler.style.left = (newWidth - (handlerWidth / 2)) + 'px';
            config.columns[index].width = newWidth;

            setTableWidth();
        };

        function mouseUpHandler() {
            saveTableState();
            document.removeEventListener('mousemove', mouseMoveHandler);
            document.removeEventListener('mouseup', mouseUpHandler);
        };

        element.addEventListener('mousedown', mouseDownHandler);
        handler.addEventListener('click', clickHandler);
    }

    function saveTableState() {
        let columns = {};

        for (const args of Object.values(config.columns)) {
            columns[args.column] = {
                width: args.width
            };

            if (args.dir) {
                columns[args.column].dir = args.dir;
            }
        }

        ajax({
            action: 'savestate',
            data: { columns: columns }
        });
    }

    fn.destroy = function () {
        $$.off(wrapper);

        Array.from(header_thead.querySelectorAll('.volatable-resize-handler')).forEach(el => {
            $$.off(el);
        });

        Array.from(header_thead.querySelectorAll('th')).forEach(th => {
            $$.off(th);
        });

        Array.from(tbody.querySelectorAll('td')).forEach(td => {
            $$.off(td);
        });

        Array.from($$.find(header_thead.rows[0], 'input')).forEach(function (input) {
            $$.off(input);
        });

        $$.find(qfilter, '.item').forEach(el => $$.off(el));

        $$.off(search);
    }

    ajax({
        action: 'get',
        success: json => init(json)
    });

    self._volatable = fn;

    return fn;
}