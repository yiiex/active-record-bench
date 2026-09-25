PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS customers
(
    id            INTEGER PRIMARY KEY,
    name          TEXT    NOT NULL,
    email         TEXT    NOT NULL,
    phone         TEXT,
    country       TEXT,
    status        TEXT    NOT NULL DEFAULT 'active',
    is_verified   INTEGER NOT NULL DEFAULT 0,
    points        INTEGER NOT NULL DEFAULT 0,
    notes         TEXT,
    created_at    TEXT    NOT NULL,
    last_login_at TEXT
);

CREATE TABLE IF NOT EXISTS addresses
(
    id          INTEGER PRIMARY KEY,
    customer_id INTEGER NOT NULL,
    country     TEXT    NOT NULL,
    city        TEXT    NOT NULL,
    street      TEXT    NOT NULL,
    zip         TEXT    NOT NULL,
    phone       TEXT,
    is_default  INTEGER NOT NULL DEFAULT 0,
    FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS products
(
    id          INTEGER PRIMARY KEY,
    name        TEXT    NOT NULL,
    slug        TEXT    NOT NULL,
    sku         TEXT    NOT NULL,
    brand       TEXT,
    description TEXT,
    price       REAL    NOT NULL,
    cost_price  REAL,
    stock       INTEGER NOT NULL DEFAULT 0,
    weight      REAL,
    is_active   INTEGER NOT NULL DEFAULT 1,
    tax_rate    REAL    NOT NULL DEFAULT 0,
    discount    REAL    NOT NULL DEFAULT 0,
    rating      REAL    NOT NULL DEFAULT 0,
    views       INTEGER NOT NULL DEFAULT 0,
    sold_count  INTEGER NOT NULL DEFAULT 0,
    created_at  TEXT    NOT NULL,
    updated_at  TEXT    NOT NULL
);

CREATE TABLE IF NOT EXISTS categories
(
    id          INTEGER PRIMARY KEY,
    name        TEXT NOT NULL,
    slug        TEXT NOT NULL,
    description TEXT,
    parent_id   INTEGER,
    position    INTEGER NOT NULL DEFAULT 0,
    is_active   INTEGER NOT NULL DEFAULT 1,
    FOREIGN KEY (parent_id) REFERENCES categories (id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS orders
(
    id               INTEGER PRIMARY KEY,
    customer_id      INTEGER NOT NULL,
    number           TEXT    NOT NULL,
    status           TEXT    NOT NULL DEFAULT 'new',
    currency         TEXT    NOT NULL DEFAULT 'USD',
    total            REAL    NOT NULL DEFAULT 0,
    subtotal         REAL    NOT NULL DEFAULT 0,
    discount         REAL    NOT NULL DEFAULT 0,
    tax              REAL    NOT NULL DEFAULT 0,
    shipping_cost    REAL    NOT NULL DEFAULT 0,
    shipping_address TEXT,
    shipping_method  TEXT,
    note             TEXT,
    created_at       TEXT    NOT NULL,
    updated_at       TEXT    NOT NULL,
    paid_at          TEXT,
    FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items
(
    id         INTEGER PRIMARY KEY,
    order_id   INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity   INTEGER NOT NULL DEFAULT 1,
    price      REAL    NOT NULL,
    discount   REAL    NOT NULL DEFAULT 0,
    total      REAL    NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS payments
(
    id             INTEGER PRIMARY KEY,
    order_id       INTEGER NOT NULL,
    amount         REAL    NOT NULL,
    method         TEXT    NOT NULL,
    status         TEXT    NOT NULL DEFAULT 'pending',
    transaction_id TEXT,
    currency       TEXT    NOT NULL DEFAULT 'USD',
    paid_at        TEXT    NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS shipments
(
    id              INTEGER PRIMARY KEY,
    order_id        INTEGER NOT NULL,
    carrier         TEXT    NOT NULL,
    tracking_number TEXT    NOT NULL,
    shipped_at      TEXT    NOT NULL,
    delivered_at    TEXT,
    FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_events
(
    id         INTEGER PRIMARY KEY,
    order_id   INTEGER NOT NULL,
    type       TEXT    NOT NULL,
    note       TEXT,
    created_at TEXT    NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments
(
    id            INTEGER PRIMARY KEY,
    product_id    INTEGER NOT NULL,
    customer_id   INTEGER NOT NULL,
    body          TEXT    NOT NULL,
    rating        INTEGER NOT NULL DEFAULT 5,
    is_approved   INTEGER NOT NULL DEFAULT 1,
    helpful_count INTEGER NOT NULL DEFAULT 0,
    created_at    TEXT    NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS product_category
(
    product_id  INTEGER NOT NULL,
    category_id INTEGER NOT NULL,
    PRIMARY KEY (product_id, category_id),
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE
);

-- Write-only table for the insert benchmark, so that the tables used by the
-- read benchmarks keep their seeded size across a benchmark run.
CREATE TABLE IF NOT EXISTS bench_rows
(
    id         INTEGER PRIMARY KEY,
    name       TEXT NOT NULL,
    email      TEXT NOT NULL,
    phone      TEXT,
    country    TEXT,
    created_at TEXT NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_orders_customer     ON orders (customer_id);
-- NOTE: no index on orders(status): status is low-cardinality (4 values) and a
-- non-selective index makes SQLite pick a bad plan for ORDER BY total queries.
CREATE INDEX IF NOT EXISTS idx_orders_total        ON orders (total);
CREATE INDEX IF NOT EXISTS idx_order_items_order   ON order_items (order_id);
CREATE INDEX IF NOT EXISTS idx_order_items_product ON order_items (product_id);
CREATE INDEX IF NOT EXISTS idx_comments_product    ON comments (product_id);
CREATE INDEX IF NOT EXISTS idx_comments_customer   ON comments (customer_id);
CREATE INDEX IF NOT EXISTS idx_payments_order      ON payments (order_id);
CREATE INDEX IF NOT EXISTS idx_shipments_order     ON shipments (order_id);
CREATE INDEX IF NOT EXISTS idx_order_events_order  ON order_events (order_id);
