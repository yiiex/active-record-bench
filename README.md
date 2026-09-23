# Active Record Benchmark

Сравнение производительности 4 реализаций Active Record на одинаковой схеме и данных:

| ORM | Пакет |
|---|---|
| Eloquent | `illuminate/database` (Capsule, без Laravel) |
| Yii2 | `yiisoft/yii2` |
| Yiisoft | `yiisoft/active-record` (Yii3) |
| Yii1x | `yii1x/active-record` |

Драйвер: SQLite (файл `bench.sqlite`), PHP 8.4, инструмент замера — `phpbench`.

## Запуск

```bash
docker compose build
docker compose up -d

# установить зависимости
docker compose exec php composer install

# засеять БД (объёмы по умолчанию небольшие, масштабируются через SEED_*)
docker compose exec php php seed.php

# smoke-проверка: каждый ORM выполняет CRUD + все типы отношений
docker compose exec php php smoke.php

# бенчмарк
docker compose exec php vendor/bin/phpbench run --report=aggregate
```

Масштаб данных задаётся переменными окружения при сидинге:

```bash
docker compose exec -e SEED_CUSTOMERS=5000 -e SEED_PRODUCTS=10000 \
  -e SEED_ORDERS=150000 -e SEED_COMMENTS=250000 php php seed.php
```

## Структура

```
src/
  Schema/schema.sql      — общая схема (магазин: customers, orders, products, …)
  Schema/Seeder.php      — детерминированный сидинг (сырой PDO, батчами)
  Bootstrap/             — бутстрап каждого ORM (Eloquent/Yii2/Yiisoft/Yii1x)
  Models/{Eloquent,Yii2,Yiisoft,Yii1x}/ — эквивалентные модели (Customer, Order, Product, Category)
  Support/               — PSR-11 контейнер и утилиты
benchmarks/{Eloquent,Yii2,Yiisoft,Yii1x}/ — бенч-классы phpbench
smoke.php                — smoke-проверка всех ORM
seed.php                 — запуск сидера
```

## Примечания по методологии

- Каждый subject phpbench выполняется в отдельном процессе; бутстрап ORM происходит
  в конструкторе бенч-класса (до замера времени).
- opcache включён, JIT выключен (`docker/php/conf.d/zz-bench.ini`).
- **Пока НЕ выровнено**: кеширование схемы. Yiisoft кеширует схему по умолчанию,
  Eloquent — нет, Yii2/yii1x — выключено. Это нужно учесть на этапе «настоящих» замеров.
- Сценарии `insert` растят БД — для финального прогона нужен сброс/пересид между прогонами.
