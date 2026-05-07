-- ============================================================
--  Feature 6: Membership Price History
--  Run ONCE in phpMyAdmin on the gym_management database
-- ============================================================

USE gym_management;

-- ── Step 1: Price history table ──────────────────────────────
CREATE TABLE IF NOT EXISTS membership_type_prices (
    price_id       INT PRIMARY KEY AUTO_INCREMENT,
    type_id        INT            NOT NULL,
    amount         DECIMAL(10,2)  NOT NULL,
    effective_from TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (type_id) REFERENCES membership_types(type_id) ON DELETE CASCADE
);

-- ── Step 2: Index for fast latest-price lookups ──────────────
CREATE INDEX IF NOT EXISTS idx_plan_date
    ON membership_type_prices (type_id, effective_from);

-- ── Step 3: Seed current prices for existing plans ───────────
--  (trigger only fires for future changes, so we seed manually)
INSERT INTO membership_type_prices (type_id, amount, effective_from)
SELECT type_id, amount, NOW()
FROM membership_types
WHERE type_id NOT IN (SELECT DISTINCT type_id FROM membership_type_prices);

-- ── Step 4: Triggers ─────────────────────────────────────────
DROP TRIGGER IF EXISTS after_plan_insert;
DROP TRIGGER IF EXISTS after_plan_price_update;

DELIMITER //

-- Fires when a brand-new plan is created → records the initial price
CREATE TRIGGER after_plan_insert
AFTER INSERT ON membership_types
FOR EACH ROW
BEGIN
    INSERT INTO membership_type_prices (type_id, amount, effective_from)
    VALUES (NEW.type_id, NEW.amount, NOW());
END //

-- Fires when a plan is edited → only records if the AMOUNT changed
CREATE TRIGGER after_plan_price_update
AFTER UPDATE ON membership_types
FOR EACH ROW
BEGIN
    IF OLD.amount <> NEW.amount THEN
        INSERT INTO membership_type_prices (type_id, amount, effective_from)
        VALUES (NEW.type_id, NEW.amount, NOW());
    END IF;
END //

DELIMITER ;

-- ── Step 5: View — current price per plan (latest record) ────
CREATE OR REPLACE VIEW vw_membership_current_price AS
SELECT
    mt.type_id,
    mt.type_name,
    mt.duration_months,
    mtp.amount         AS current_amount,
    mtp.effective_from AS price_since
FROM membership_types mt
JOIN membership_type_prices mtp ON mt.type_id = mtp.type_id
WHERE mtp.price_id = (
    SELECT price_id
    FROM membership_type_prices
    WHERE type_id = mt.type_id
    ORDER BY effective_from DESC
    LIMIT 1
);

-- ── Verify ───────────────────────────────────────────────────
SELECT * FROM vw_membership_current_price;
SHOW TRIGGERS FROM gym_management LIKE '%plan%';
