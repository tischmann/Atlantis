'use strict';

const $$ = (function () {
    let _eventHandlers = {};
    let fn = {};

    fn.on = function (node, event, handler, capture = false) {
        if (!(event in _eventHandlers)) {
            _eventHandlers[event] = [];
        }

        _eventHandlers[event].push({
            node: node,
            handler: handler,
            capture: capture
        });

        node.addEventListener(event, handler, capture);
    }

    fn.off = function (targetNode, targetEvent = null, targetHandler = null) {
        if (!targetHandler) {
            for (const [event, obj] of Object.entries(_eventHandlers)) {
                if (obj.node === targetNode) {
                    if (targetEvent === event || targetEvent === null) {
                        obj.node.removeEventListener(event, obj.handler, obj.capture);
                        _eventHandlers[event] = _eventHandlers[event].filter(
                            ({ node }) => node !== targetNode,
                        );
                    }
                }
            }
        } else {
            _eventHandlers[targetEvent].filter(({ node }) => node === targetNode)
                .forEach(({ node, handler, capture }, index) => {
                    if (handler == targetHandler) {
                        node.removeEventListener(targetEvent, targetHandler, capture);
                        _eventHandlers[targetEvent].splice(index, 1);
                    }
                });

            _eventHandlers[targetEvent] = _eventHandlers[targetEvent].filter(
                ({ node }) => node !== targetNode,
            );
        }
    }

    fn.ajax = function (options = {}) {
        options.method = options.method || 'GET';
        options.url = options.url || window.location;
        options.data = options.data || {};
        options.success = options.success || function () { };
        options.failure = options.failure || function () { };
        options.headers = options.headers || {};

        let init = {
            method: options.method,
            headers: options.headers
        };

        if (options.method.toUpperCase() == 'POST') {
            init.body = JSON.stringify(options.data);
        }

        fetch(options.url, init).then(response => response.json())
            .then(response => options.success(response))
            .catch(error => options.failure(error));
    }

    fn.cookie = function (name, value = undefined, options = {}) {
        if (value === undefined) {
            let matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));

            return matches ? decodeURIComponent(matches[1]) : undefined;
        } else {
            options = {
                path: '/',
                secure: true,
                samesite: 'strict',
                'max-age': 60 * 60 * 24 * 14,
                ...options
            };

            if (options.expires instanceof Date) {
                options.expires = options.expires.toUTCString();
            }

            let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

            for (let optionKey in options) {
                updatedCookie += "; " + optionKey;
                let optionValue = options[optionKey];
                if (optionValue !== true) {
                    updatedCookie += "=" + optionValue;
                }
            }

            document.cookie = updatedCookie;
        }
    }

    fn.datepicker = function (node, args = {}) {
        if ('_datepicker' in node) {
            return node._datepicker;
        }

        let locale = fn.cookie('language') || 'ru';

        const today = new Date();
        const current = new Date(Date.parse($$.cookie('date_of_flight')));

        let date = args.date || new Date();
        args.callback = args.callback || function () { };

        const container = fn.create('div', {
            class: 'volantis-datepicker-container'
        });
        const yearsWrapper = fn.create('div', {
            class: 'volantis-datepicker-years-wrapper'
        });
        const monthWrapper = fn.create('div');
        let years = undefined;
        let months = undefined;

        let monthTable;
        let days_labels = [];

        for (let i = 1; i <= 7; i++) {
            const d = new Date(2020, 10, i);
            days_labels.push(d.toLocaleString(locale, { weekday: 'short' }));
        }

        let months_labels = [];

        for (let i = 0; i <= 11; i++) {
            const d = new Date(2021, i, 1);
            months_labels.push(d.toLocaleString(locale, { month: 'long' }));
        }

        yearsWrapper.classList.add('volantis-datepicker-years-wrapper');

        function destroyYears() {
            if (years) {
                $$.off(years);
                years.remove();
                years = undefined;
            }
        }

        function destroyMonths() {
            if (months) {
                $$.off(months);
                months.remove();
                months = undefined;
            }
        }

        function getYears() {
            destroyYears();

            years = $$.create('select');

            for (let i = 2011; i <= today.getFullYear(); i++) {
                const newDate = new Date(i, date.getMonth(), date.getDate());
                const option = $$.create('option', {
                    value: newDate.toLocaleDateString('en-US')
                });

                if (i == current.getFullYear()) {
                    option.selected = 'selected';
                    option.classList.add('current');
                }

                option.innerHTML = newDate.toLocaleDateString(locale, { year: 'numeric' });
                years.append(option);
            }

            $$.on(years, 'change', refreshMonthTable);

            return years;
        }


        function getMonths() {
            destroyMonths();

            months = $$.create('select');

            for (let i = 0; i <= 11; i++) {
                const newDate = new Date(date.getFullYear(), i, date.getDate());
                const option = $$.create('option', {
                    value: newDate.toLocaleDateString('en-US')
                });

                if (i == current.getMonth()) {
                    option.selected = 'selected';
                    option.classList.add('current');
                }

                option.innerHTML = newDate.toLocaleDateString(locale, { month: 'long' });
                months.append(option);
            }

            $$.on(months, 'change', refreshMonthTable);

            return months;
        }

        function clickHandler(event) {
            event.stopPropagation();
        }

        function refreshMonthTable(event) {
            date = new Date(Date.parse(this.value));
            destroyTable();
            monthWrapper.append(getTable());
        }

        function getTable() {
            const year = date.getFullYear();
            const month = date.getMonth();

            const days_in_month = getDaysInMonth(month, year);
            const first_day_date = new Date(year, month, 1);
            const first_day_weekday = first_day_date.getDay();

            const prev_month = month == 0 ? 11 : month - 1;
            const prev_year = prev_month == 11 ? year - 1 : year;
            const prev_days = getDaysInMonth(prev_month, prev_year);

            function getDaysInMonth(month, year) {
                return new Date(year, month + 1, 0).getDate();
            }

            monthTable = document.createElement('table');

            const thead = document.createElement('thead');
            let tr = document.createElement('tr');

            tr.classList.add('week-days');

            for (let i = 1; i <= 7; i++) {
                const th = document.createElement('th');
                let ind = i < 7 ? i : 0;
                th.innerHTML = days_labels[ind];
                tr.append(th);
            }

            thead.append(tr);
            monthTable.append(thead);

            let w = 0;
            let n = 1;
            let c = 1;

            const tbody = document.createElement('tbody');

            for (let i = 1; i < 42; i++) {
                if (w == 0) {
                    tr = document.createElement('tr');
                    tr.classList.add('week');
                }

                const td = document.createElement('td');

                if (i < new Date(year, month, 1).getDay()) {
                    // previous month's day
                    td.innerHTML = prev_days - first_day_weekday + i + 1;
                    tr.append(td);
                } else if (c > days_in_month) {
                    // next month's day
                    td.innerHTML = n;
                    tr.append(td);
                    n++;
                } else {
                    // current month's day
                    const d = new Date(year, month, c);
                    td.innerHTML = c;

                    if (c == today.getDate() && today.getMonth() == month && today.getFullYear() == year) {
                        td.classList.add('today');
                    }

                    if (c == date.getDate() && current.getMonth() == month && current.getFullYear() == year) {
                        td.classList.add('current');
                    }

                    td.dataset.date = d.toLocaleDateString(locale);
                    fn.on(td, 'click', args.callback);
                    tr.append(td);
                    c++;
                }

                if (w == 6) {
                    tbody.append(tr);
                    w = 0;
                } else {
                    w++;
                }
            }

            monthTable.append(tbody);

            return monthTable;
        }

        function show(event) {
            event.stopPropagation();

            if (monthTable) {
                return false;
            }

            fn.on(document, 'click', hide);

            destroyYears();
            destroyMonths();

            yearsWrapper.append(getMonths());
            yearsWrapper.append(getYears());

            destroyTable();

            monthWrapper.append(getTable());

            document.body.append(container);

            const offset = node.getBoundingClientRect();

            container.style.top = offset.top + offset.height + 'px';
            container.style.left = offset.left + 'px';

            $$.on(container, 'click', clickHandler);
        }

        function destroyTable() {
            if (monthTable) {
                [].forEach.call(monthTable.querySelectorAll('tr td'), (td) => {
                    fn.off(td);
                });

                monthTable.remove();

                monthTable = null;
            }
        }

        function hide() {
            destroyTable();
            container.remove();
            fn.off(document, 'click', hide);
        }

        function destroy() {
            hide();
            fn.off(node);
            delete node._datepicker;
        }

        container.append(yearsWrapper);
        container.append(monthWrapper);

        fn.on(node, 'click', show);

        node._datepicker = {
            show: show,
            hide: hide,
            destroy: destroy
        };

        return node;
    }

    fn.draggable = function (draggable, options = {}) {
        const container = options.container || draggable.parentNode;
        const axis = options.axis || null;
        const drag = options.drag || function () { };
        const start = options.start || function () { };
        const stop = options.stop || function () { };

        let isMouseDown = false;
        let mouseX;
        let mouseY;
        let elmTop;
        let elmLeft;
        let diffX;
        let newElmTop;
        let newElmLeft;
        let diffY;
        let rightBarrier;
        let bottomBarrier;
        let containerWidth = container.clientWidth;
        let containerHeight = container.clientHeight;
        let elmWidth = draggable.clientWidth;
        let elmHeight = draggable.clientHeight;

        function mouseDown(e) {
            isMouseDown = true;

            mouseX = e.clientX;
            mouseY = e.clientY;

            elmTop = draggable.offsetTop;
            elmLeft = draggable.offsetLeft;

            diffX = mouseX - elmLeft;
            diffY = mouseY - elmTop;

            start();
        }

        function mouseUp() {
            isMouseDown = false;
            stop();
        }

        function mouseMove(e) {
            if (!isMouseDown) return false;

            var newMouseX = e.clientX;
            var newMouseY = e.clientY;

            newElmTop = newMouseY - diffY;
            newElmLeft = newMouseX - diffX;

            rightBarrier = containerWidth - elmWidth;
            bottomBarrier = containerHeight - elmHeight;

            if (newElmTop < 0) {
                newElmTop = 0;

            }

            if (newElmTop > bottomBarrier) {
                newElmTop = bottomBarrier;
            }

            if (newElmLeft < 0) {
                newElmLeft = 0;

            }

            if (newElmLeft > rightBarrier) {
                newElmLeft = rightBarrier;
            }

            if (axis == 'x') {
                draggable.style.left = newElmLeft + "px";
            } else if (axis == 'y') {
                draggable.style.top = newElmTop + "px";
            } else {
                draggable.style.top = newElmTop + "px";
                draggable.style.left = newElmLeft + "px";
            }

            drag();
        }

        fn.on(document, 'mousemove', mouseMove);
        fn.on(draggable, 'mouseup', mouseUp);
        fn.on(draggable, 'mousedown', mouseDown);
    }

    fn.data = function (node, key, value = undefined) {
        if (!node) {
            return null;
        }

        if (value !== undefined && !(key instanceof Object)) {
            node.setAttribute(`data-${key}`, value);
        } else if (key instanceof Object) {
            for (const [i, v] of Object.entries(key)) {
                node.setAttribute(`data-${i}`, v);
            }
        } else {
            return node.getAttribute(`data-${key}`);
        }
    }

    fn.index = function (el) {
        if (!el) return -1;

        let i = 0;

        do {
            i++;
        } while (el = el.previousElementSibling);

        return i;
    }

    fn.parent = function (node) {
        return node.parentNode;
    }

    fn.parents = function (node, parentSelector = 'body') {
        let parent = node;

        while (parent !== document.body) {
            parent = parent.parentNode;

            if (parent.parentNode.querySelector(parentSelector)) {
                break;
            }
        }

        if (parent === document.body) {
            return null;
        }

        return parent;
    }

    fn.height = function (node) {
        return parseFloat(getComputedStyle(node, null).height.replace("px", ""));
    }

    fn.width = function (node) {
        return parseFloat(getComputedStyle(node, null).width.replace("px", ""));
    }

    fn.offset = function (node) {
        return { left: node.offsetLeft, top: node.offsetTop };
    }

    fn.create = function (element, options = {}) {
        const node = document.createElement(element);

        for (let [key, obj] of Object.entries(options)) {
            switch (key) {
                case 'class':
                    if (!obj) continue;

                    if (!(obj instanceof Array)) {
                        obj = [obj];
                    }

                    obj.forEach(i => node.classList.add(i));
                    break;
                case 'style':
                    if (!obj) continue;
                    fn.css(node, obj);
                    break;
                default:
                    if (!(obj instanceof Array)) {
                        obj = [obj];
                    }

                    obj.forEach(i => node.setAttribute(key, i));
                    break;
            }
        }

        return node;
    }

    fn.css = function (node, attr = {}) {
        for (let [key, obj] of Object.entries(attr)) {
            if (!(obj instanceof Array)) {
                obj = [obj];
            }

            obj.forEach(i => node.style[key] = i);
        }
    }

    fn.find = function (parent, selector) {
        const found = parent.querySelectorAll(selector);
        return found ? Array.from(found) : [];
    }

    fn.unhighlight = function (node) {
        node.innerHTML = node.innerHTML.replace(/(<span class="highlight">|<\/span>)/igm, "");
    }

    fn.highlight = function (node, value, callback = function () { }) {
        value = value.replace(/\\/, '');

        if (!value) {
            return false;
        }

        const result = node.innerHTML.replace(
            new RegExp(`(${value})`, "gim"),
            function (match, p1) {
                callback(node);
                return `<span class="highlight">${p1}</span>`;
            }
        );

        node.innerHTML = result;
    }

    fn.count = function (parent, selector) {
        return parent.querySelectorAll(selector).length;
    }

    fn.exist = function (parent, selector) {
        return !!fn.count(parent, selector);
    }

    fn.trigger = function (el, eventName, data = {}) {
        if (window.CustomEvent && typeof window.CustomEvent === 'function') {
            var event = new CustomEvent(eventName, { detail: data });
        } else {
            var event = document.createEvent('CustomEvent');
            event.initCustomEvent(eventName, true, true, data);
        }

        return el.dispatchEvent(event);
    }

    fn.isEmpty = function (obj) {
        return Object.keys(obj).length === 0;
    }

    return fn;

}());