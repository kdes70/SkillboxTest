# Задание 2

Необходимо спроектировать таблицы в БД, для хранения данных проекта и подготовить некоторые SQL запросы.
В качестве БД нужно использовать реляционную SQL БД: MySQL.

## Описание

Есть `Фирмы`, `Товары`, `Категории товаров` и `Цвета`.
Для каждого элемента должно хранится как минимум одно текстовое поле `name` - название

### Требования

- Каждая фирма производит свои уникальные товары.
- Категории товаров едины для всего проекта и должны иметь древовидную структуру.
- Товар может находится в нескольких разделах одновременно.
- Товар может быть представлен в любых цветах.

### Sql запросы

Реализуйте следующие SQL запросы:

1. Выберите все фирмы (названия), у которых есть товары определенного цвета (по id)
2. Посчитайте количество товаров в определенной категории (по id) и определенного цвета (по id)
3. Выберите поддерево категорий, по id категории.

# Решение:

### Cтруктура таблиц

Таблица firms

```sql
CREATE TABLE firms
(
    id   INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL
);

```

Таблица categories

```sql
CREATE TABLE categories
(
    id   INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    lft  INT UNSIGNED NOT NULL,
    rgt  INT UNSIGNED NOT NULL,
    INDEX (lft),
    INDEX (rgt)
);

```

Таблица colors

```sql
CREATE TABLE colors
(
    id   INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL
);

```

Таблица products

```sql
CREATE TABLE products
(
    id      INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name    VARCHAR(255) NOT NULL,
    firm_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (firm_id) REFERENCES firms (id) ON DELETE CASCADE
);

```

Таблица product_categories

```sql
CREATE TABLE product_categories
(
    product_id  INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (product_id, category_id),
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE
);

```

Таблица product_colors

```sql
CREATE TABLE product_colors
(
    product_id INT UNSIGNED NOT NULL,
    color_id   INT UNSIGNED NOT NULL,
    PRIMARY KEY (product_id, color_id),
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
    FOREIGN KEY (color_id) REFERENCES colors (id) ON DELETE CASCADE
);

```

## SQL запросы:

1. Выбор всех фирм (названия), у которых есть товары определенного цвета (по color_id)

```sql
SELECT DISTINCT f.name
FROM firms f
         JOIN products p ON f.id = p.firm_id
         JOIN product_colors pc ON p.id = pc.product_id
WHERE pc.color_id = :color_id;

```

2. Подсчет количества товаров в определенной категории (по category_id) и определенного цвета (по color_id)

```sql
SELECT COUNT(DISTINCT p.id) AS product_count
FROM products p
         JOIN product_categories pc ON p.id = pc.product_id
         JOIN categories c ON pc.category_id = c.id
         JOIN categories parent ON parent.lft <= c.lft AND parent.rgt >= c.rgt
         JOIN product_colors pcl ON p.id = pcl.product_id
WHERE parent.id = :category_id
  AND pcl.color_id = :color_id;

```

3. Выбор поддерева категорий по category_id

```sql
SELECT child.id, child.name, child.lft, child.rgt
FROM categories AS parent
         JOIN categories AS child ON child.lft BETWEEN parent.lft AND parent.rgt
WHERE parent.id = :category_id
ORDER BY child.lft;

```

Для решения задачи использован подход с Nested Set Model она отлично подходит для запросов на чтение, 
таких как выбор поддерева категорий. Однако его сложность проявляется при операциях вставки и удаления, так как требуется пересчет значений lft и rgt.

