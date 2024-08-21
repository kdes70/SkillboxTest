# Курс 3. Модуль 5

Доработаем разработанный ранее проект на laravel.

## Доработка механизма управления статьями

Сделайте из Статей - полноценный ресурс, добавьте возможность изменения и удаления статьи, а также реализуйте
всплывающие уведомления, что изменения прошли успешно.

Также к статьям реализуйте функционал тегов и облака тегов для статей на сайте. К статье можно создать и привязать любое
количество тегов, в боковой колонке сайта должно отображаться облако тегов и при переходе на тег - нужно отобразить
список статей, привязанных к этому тегу

## Реализуйте авторизацию

Авторизация, Регистрация, Восстановление пароля.

## Уровни доступа

Реализуйте Ограничение на создание и изменение статей, теперь только авторизованный пользователь может добавлять статьи
на сайт, и только пользователь написавший статью может ее изменить или удалить.

## Уведомления

При Создании/Изменении/Удалении статьи отправляйте почтовое уведомление администратору сайта (email указывается в
конфигурации). В уведомлении должна быть указана статья, описание событие (создана, изменена, удалена) и ссылка на эту
статью, если она не удалена.



# Заключение

По итогу проверки практического задания можно считать выполненным не в полной мере, так как В методе `update` также следует добавить проверку прав доступа с помощью authorize.
Eсть несколько советов по улучшению качества кода

## Основные цели рефакторинга:

1. Улучшение читаемости и поддерживаемости кода.
2. Разделение ответственности.
3. Использование возможностей Laravel для упрощения кода.

- laravel/check_tasks/home5/app/Http/Controllers/PostController@store
  Пример:

```php
public function store(Request $request)
{
    $attributes = $this->validatePost();
    $attributes['author_id'] = auth()->id();

    $post = Post::create($attributes);

    $this->attachTagsToPost($post, $request->input('tags'));

    return redirect()->route('posts.index');
}

private function attachTagsToPost(Post $post, string $tags)
{
    $tagsToAttach = collect(explode(',', $tags))->keyBy(function($item) {
        return $item;
    });

    if ($tagsToAttach->isNotEmpty()) {
        $tagsToAttach->each(function($tagName) use ($post) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $post->tags()->attach($tag);
        });
    }
}
```

### Что было сделано:

1. Выделение отдельного метода для прикрепления тегов:
    - Создан приватный метод attachTagsToPost, который занимается прикреплением тегов к посту. Это делает основной метод
      store более чистым и понятным.

2. Использование метода isNotEmpty:
    - Вместо проверки if ($tagsToAttach) используется isNotEmpty(), что делает код более выразительным.
      Использование метода each:

3. Вместо цикла foreach используется метод each коллекции, что делает код более лаконичным.
    - Теперь метод store выглядит более структурированным и легко читаемым


- laravel/check_tasks/home5/app/Http/Controllers/PostController@update
  Пример:

```php
public function update(Post $post)
{
    $attributes = $this->validatePost(false);
    $post->update($attributes);

    $this->syncTags($post, request('tags'));

    return redirect()->route('posts.index');
}

private function syncTags(Post $post, string $tags)
{
    $currentTags = $post->tags->keyBy('name');
    $newTags = collect(explode(',', $tags))->keyBy(function ($item) {
        return $item;
    });

    $tagsToAttach = $newTags->diffKeys($currentTags);
    $tagsToDetach = $currentTags->diffKeys($newTags);

    $this->attachTagsToPost($post, $tagsToAttach);
    $this->detachTags($post, $tagsToDetach);
}

private function detachTags(Post $post, $tagsToDetach)
{
    $tagsToDetach->each(function ($tag) use ($post) {
        $post->tags()->detach($tag);
    });
}
```

### Что было сделано:

1. Выделение отдельного метода для синхронизации тегов:
    - Создан приватный метод syncTags, который занимается синхронизацией тегов поста.

2. Выделение отдельных методов для прикрепления и открепления тегов:
    - Созданы приватные методы attachTags и detachTags, которые занимаются прикреплением и откреплением тегов
      соответственно.
3. Использование методов коллекций:
    - Методы diffKeys и each используются для определения и обработки тегов, которые нужно прикрепить или открепить.

### Заключение:
Рефакторинг позволил сделать код более структурированным, читаемым и поддерживаемым. Разделение логики на отдельные
методы и использование возможностей Laravel для работы с коллекциями улучшили качество кода и облегчили его дальнейшее
сопровождение и модификацию.