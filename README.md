# Active Record Benchmark

Performance comparison of four Active Record implementations on the same schema and data:

| ORM | Package |
|---|---|
| Eloquent | `illuminate/database` (Capsule, without Laravel) |
| Yii2 | `yiisoft/yii2` |
| Yiisoft | `yiisoft/active-record` (Yii3) |
| Yii1x | `yii1x/active-record` |

Driver: SQLite (`bench.sqlite`), PHP 8.4, measurement tool: `phpbench`.

## Getting started

```bash
docker compose build
docker compose up -d

# install dependencies
docker compose exec php composer install

# seed the database (data scale is controlled by SEED_* env vars)
docker compose exec php php seed.php

# benchmark (summary report with an ORM column; mem_peak + mem_final)
docker compose exec php vendor/bin/phpbench run --report=summary

# identical subjects side by side: one table per subject, all ORMs
docker compose exec php vendor/bin/phpbench run --report=bysubject

# a single ORM (groups: eloquent / yii2 / yiisoft / yii1x)
docker compose exec php vendor/bin/phpbench run --report=summary --group=eloquent

# a single scenario/class
docker compose exec php vendor/bin/phpbench run --report=summary --filter='benchFindAll'
```

### Reports and visualisation

```bash
# interactive HTML report with Plotly charts → .phpbench/html/index.html
docker compose exec php vendor/bin/phpbench run --report=overview --output=html

# machine-readable JSON (for your own visualisation)
docker compose exec php vendor/bin/phpbench run --report=summary --output=json > results.json

# CSV
docker compose exec php vendor/bin/phpbench run --report=summary --output=delimited > results.csv
```

`--output` (`-o`) selects the renderer: `console` (default), `html`, `json`, `delimited`.
Memory: `mem_peak` is the per-process peak, `mem_final` is the memory at the end (retained + bootstrap).

The custom report generators live in `phpbench.json`: `summary` (flat table with an ORM
column), `bysubject` (one table per subject, all ORMs) and `ormchart` (Plotly bar charts with
ORMs as series). `overview` is a built-in generator.

Ready-made reports are kept in `results/`:

- `overview.html` — overview
- `by-subject.html` — one table per subject
- `orm-compare.html` — Plotly charts (time and memory per ORM)
- `summary.json` — machine-readable data

Open them in a browser; the Plotly charts are loaded from a CDN.

### Reports without re-running

Results can be stored and rendered later:

```bash
# run and store
docker compose exec php vendor/bin/phpbench run --store --tag=full

# build any report from a stored run
docker compose exec php vendor/bin/phpbench report --ref=full --report=bysubject
docker compose exec php vendor/bin/phpbench report --ref=full --report=overview --output=html

# list stored runs
docker compose exec php vendor/bin/phpbench log
```

Data scale is controlled by environment variables at seed time. Defaults: customers 2500,
products 1000, categories 50, orders 25000; comments are generated as 5–15 per product.

```bash
docker compose exec -e SEED_CUSTOMERS=2500 -e SEED_PRODUCTS=1000 \
  -e SEED_CATEGORIES=50 -e SEED_ORDERS=25000 php php seed.php
```

## Tests

```bash
docker compose exec php vendor/bin/phpunit
```

Covers what is actually ours: the seeder (expected volumes, determinism, foreign keys)
and the ORM bootstraps (each connects and the models query). CI runs them on every push
and pull request to `master` (`.github/workflows/tests.yml`).

## Structure

```
src/
  Schema/schema.sql        — shared schema (shop: customers, orders, products, …)
  Schema/Seeder.php        — deterministic seeding (raw PDO, batched)
  Bootstrap/               — one bootstrap per ORM (Eloquent / Yii2 / Yiisoft / Yii1x)
  Models/{Eloquent,Yii2,Yiisoft,Yii1x}/ — equivalent models per ORM:
    Customer, Order, OrderItem, Product, Category,
    Address, Payment, Shipment, OrderEvent, BenchRow
    (+ CustomerQuery / ProductQuery for Yii2 and Yiisoft)
  Support/                 — PSR-11 container and helpers (Env)
benchmarks/
  Support/ProvidesParams.php — shared @ParamProviders (limit / loop count)
  {Eloquent,Yii2,Yiisoft,Yii1x}/
    *Benchmark.php         — abstract base (boot + #[Groups])
    CrudBench.php          — findByPk / insert / count
    FindBench.php          — findAll (limit 100…1000) / findAll by status
    RelationBench.php      — eager loading: belongsTo, manyMany, deep nested,
                             items via WHERE IN and via JOIN
    QueryBuilderBench.php  — complex nested query (build + execute)
    ScopeBench.php         — nested eager with/without scopes and via a raw where
benchmark_bootstrap.php    — phpbench runner bootstrap
seed.php                   — seeds the database
dump/                      — local-only scripts to dump SQL / inspect memory (gitignored)
phpbench.json              — phpbench config and custom report generators
results/                   — generated HTML/JSON reports
```

## Methodology notes

- Every phpbench subject runs in its own process; the ORM bootstrap happens in the
  benchmark class constructor, before timing starts.
- opcache is enabled, JIT is disabled (`docker/php/conf.d/zz-bench.ini`).
- Schema cache is aligned: Yiisoft's `SchemaCache` is explicitly disabled, Yii2/yii1x
  have it off, and Eloquent has no schema cache.
- `ANALYZE` runs after seeding so the SQLite query planner picks the right indexes
  (without statistics it chose a bad plan for the complex query).
- The `insert` scenarios write to a dedicated `bench_rows` table and never mutate the
  tables used by the read scenarios; the seeder clears `bench_rows` on start.
- Numbers vary by a few percent between runs while rankings stay stable, so treat the
  values in the docs as `mode` (KDE mode) from a single representative run.
