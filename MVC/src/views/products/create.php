<h1>Создание товара</h1>
<form action="/products" method="post">
    <div class="form-group">
        <label for="name">Название</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="price">Цена</label>
        <input type="number" class="form-control" id="price" name="price" step="0.01" required>
    </div>
    <button type="submit" class="btn btn-primary">Создать</button>
</form>