<h1>Список товаров</h1>
<a href="/products/create" class="btn btn-primary mb-3">Создать товар</a>
<table class="table">
    <thead>
    <tr>
        <th>Название</th>
        <th>Цена</th>
        <th>Действия</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $product): ?>
        <tr>
            <td><?= htmlspecialchars($product['name']) ?></td>
            <td><?= htmlspecialchars($product['price']) ?></td>
            <td>
                <form action="/products/delete" method="post">
                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>