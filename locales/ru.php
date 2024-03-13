<?php

return [
    // Общие
    'access_denied' => 'Доступ запрещен',
    'back' => 'Назад',
    'add' => 'Добавить',
    'actions' => 'Действия',
    'attention' => 'Внимание',
    'warning' => 'Предупреждение',
    'edit' => 'Изменить',
    'delete' => 'Удалить',
    'upload' => 'Загрузить',
    'close' => 'Закрыть',
    'save' => 'Сохранить',
    'error' => 'Ошибка',
    'read' => 'Читать',
    'show' => 'Показать',
    'json_error' => 'Ошибка JSON',
    'not_found' => 'Запрашиваемый ресурс не найден',
    'route_not_found' => 'Маршрут не найден',
    'template_not_found' => 'Шаблон не найден',
    'variable_required' => 'Переменная обязательна',
    'invalid_type' => 'Неверный тип',
    'confirm_delete' => 'Вы уверены, что хотите удалить?',
    'confirm_delete_category' => 'Вы уверены, что хотите удалить категорию и все дочерние категории? Это действие нельзя отменить!',
    'error_code' => 'Код ошибки',
    'response_code' => 'Код ответа',
    'csrf_failed' => 'CSRF токен не прошел проверку',
    'bad_request' => 'Неверный запрос',
    'upload_error' => 'Ошибка загрузки',
    'pagination_first' => 'Первая страница',
    'pagination_prev' => 'Предыдущая страница',
    'pagination_next' => 'Следующая страница',
    'pagination_last' => 'Последняя страница',
    'pagination_page' => 'Страница',
    'temp_file_not_moved' => 'Временный файл не перемещен',
    'temp_file_not_found' => 'Временный файл не найден',
    'order' => 'Сортировка',
    'locale' => 'Локаль',
    'direction' => 'Порядок сортировки',
    'direction_asc' => 'По возрастанию',
    'direction_desc' => 'По убыванию',
    'sort_up' => 'Поместить выше',
    'sort_down' => 'Поместить ниже',
    'not_saved' => 'Не сохранено',
    'saved' => 'Сохранено',
    'field_required' => 'Поле обязательно',
    'deleted' => 'Удалено',
    'not_deleted' => 'Не удалено',
    'print_page' => 'Версия для печати',
    'updated_at' => 'Изменено',
    'articles_by_tag' => 'Статьи по тегу',
    // Ошибки Json Web Token
    'jwt_missing_public_key' => 'Отсутствует публичный ключ',
    'jwt_wrong_segment_amount' => 'Неверное количество сегментов',
    'jwt_bad_header' => 'Неверный заголовок',
    'jwt_bad_payload' => 'Неверное тело',
    'jwt_algorithm_not_supported' => 'Алгоритм не поддерживается',
    'jwt_algorithm_not_allowed' => 'Алгоритм не разрешен',
    'jwt_key_id_invalid' => 'Неверный идентификатор ключа',
    'jwt_key_id_missing' => 'Отсутствует идентификатор ключа',
    'jwt_token_expired' => 'Истёк срок действия токена',
    'jwt_token_not_yet_valid' => 'Токен еще не действителен',
    'jwt_ssl_unable_to_sign' => 'Не удалось подписать токен',
    'jwt_null_result' => 'Пустой результат',
    // Авторизация
    'signin_login' => 'Логин',
    'signin_password' => 'Пароль',
    'signin_submit' => 'Войти',
    'signout_submit' => 'Выйти',
    // Пользователи
    'user_not_found' => 'Пользователь не найден',
    'user_save_error' => 'Ошибка сохранения пользователя',
    'user_delete_error' => 'Ошибка удаления пользователя',
    'user_last_admin' => 'Последний администратор не может быть удален',
    'user_login_format' => 'Логин должен содержать не менее 3 символов и состоять из латинских букв и цифр а также символов _ и -',
    'user_login_exists' => 'Пользователь с таким логином уже существует',
    'user_name_format' => 'Имя должно содержать не менее 3 символов',
    'user_password_complexity' => 'Пароль должен содержать не менее 8 символов, включая цифры, строчные и прописные буквы',
    'user_passwords_not_match' => 'Пароли не совпадают',
    'user_new' => 'Новый пользователь',
    'user_update' => 'Изменение пользователя',
    'users_list' => 'Список пользователей',
    'user_name' => 'Имя',
    'user_login' => 'Логин',
    'user_password' => 'Пароль',
    'user_password_repeat' => 'Повторите пароль',
    'user_password_mismatch' => 'Пароли не совпадают',
    'user_status' => 'Статус',
    'user_status_active' => 'Активен',
    'user_status_inactive' => 'Неактивен',
    'user_role' => 'Роль',
    'user_role_guest' => 'Гость',
    'user_role_user' => 'Пользователь',
    'user_role_admin' => 'Администратор',
    'user_role_moderator' => 'Модератор',
    'user_role_author' => 'Автор',
    'user_remarks' => 'Примечание',
    'user_order_created_at' => 'Дата создания',
    'user_order_login' => 'Логин',
    'user_order_name' => 'Имя',
    'user_order_role' => 'Роль',
    // Даты
    'year_ago' => 'год назад',
    'years_ago' => 'лет назад',
    'years_ago_2_4' => 'года назад',
    'month_ago' => 'месяц назад',
    'months_ago' => 'месяцев назад',
    'months_ago_2_4' => 'месяца назад',
    'day_ago' => 'день назад',
    'days_ago' => 'дней назад',
    'days_ago_2_4' => 'дня назад',
    'hour_ago' => 'час назад',
    'hours_ago' => 'часов назад',
    'hours_ago_2_4' => 'часа назад',
    'minute_ago' => 'минута назад',
    'minutes_ago' => 'минут назад',
    'minutes_ago_2_4' => 'минуты назад',
    'second_ago' => 'секунда назад',
    'seconds_ago' => 'секунд назад',
    'seconds_ago_2_4' => 'секунды назад',
    // Локали
    'locale_' => 'Все',
    'locale_ru' => 'Русский',
    // Категории
    'catgories_list' => 'Список категорий',
    'category_new' => 'Новая категория',
    'category_edit' => 'Редактирование категории',
    'category_title' => 'Заголовок',
    'category_slug' => 'URL',
    'category_locale' => 'Локаль',
    'category_children' => 'Дочерние категории',
    'category_actions' => 'Действия',
    'category_parent_id' => 'Родительская категория',
    'category_not_found' => 'Категория не найдена',
    // Статьи
    'article_edit' => 'Редактирование статьи',
    'article_new' => 'Новая статья',
    'articles_list' => 'Список статей',
    'article_title' => 'Заголовок',
    'article_text' => 'Текст статьи',
    'article_short_text' => 'Краткий текст статьи',
    'article_author' => 'Автор',
    'article_category' => 'Категория',
    'article_category_all' => 'Все',
    'article_image' => 'Изображение',
    'article_image_size' => 'Пропорции',
    'article_gallery' => 'Галерея',
    'article_locale' => 'Локаль',
    'article_visible' => 'Видимость',
    'article_visible_all' => 'Все',
    'article_visible_visible' => 'Видно',
    'article_visible_invisible' => 'Скрыто',
    'article_fixed' => 'Закрепление',
    'article_fixed_all' => 'Все',
    'article_fixed_on' => 'Да',
    'article_fixed_off' => 'Нет',
    'article_moderated' => 'Модерация',
    'article_moderated_all' => 'Все',
    'article_moderated_yes' => 'Да',
    'article_moderated_no' => 'Нет',
    'article_tags' => 'Теги',
    'article_created_at' => 'Создано',
    'article_updated_at' => 'Изменено',
    'article_not_found' => 'Статья не найдена',
    'article_generate_tags' => 'Сгенерировать',
    'article_attachement' => 'Вложения',
    'article_created_at' => 'Дата и время создания',
    'article_video' => 'Видео',
    'article_created_at_invalid' => 'Неверная дата и время создания',
    'articles_not_found' => 'Статьи не найдены',
    'article_order' => 'Сортировка',
    'article_order_created_at' => 'Дата создания',
    'article_order_title' => 'Заголовок',
    'article_order_visible' => 'Видимость',
    'article_order_fixed' => 'Закрепление',
    'article_direction' => 'Порядок сортировки',
    'article_direction_asc' => 'По возрастанию',
    'article_direction_desc' => 'По убыванию',
];
