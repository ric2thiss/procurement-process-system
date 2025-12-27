-- ============================================================================
-- SEED INVENTORY ITEMS
-- Document Tracking System - Magallanes National High School
-- 
-- This script seeds 5 sample school supply items into the inventory_items table
-- ============================================================================

USE dts_db;

-- Insert 5 School Supply Items
INSERT INTO inventory_items (
    item_code,
    item_description,
    category,
    unit_of_measure,
    standard_unit_price,
    reorder_level,
    reorder_quantity,
    stock_on_hand,
    location,
    is_active,
    created_at
) VALUES
(
    'ITEM-001',
    'A4 Bond Paper (Ream)',
    'Office Supplies',
    'Ream',
    250.00,
    10,
    20,
    25,
    'Warehouse A, Shelf 1',
    TRUE,
    NOW()
),
(
    'ITEM-002',
    'Whiteboard Markers (Set of 10)',
    'Teaching Materials',
    'Set',
    150.00,
    5,
    10,
    8,
    'Warehouse A, Shelf 3',
    TRUE,
    NOW()
),
(
    'ITEM-003',
    'Ballpoint Pens (Box of 12)',
    'Office Supplies',
    'Box',
    120.00,
    10,
    15,
    12,
    'Warehouse B, Shelf 2',
    TRUE,
    NOW()
),
(
    'ITEM-004',
    'Notebooks (Spiral, 100 pages)',
    'Office Supplies',
    'Piece',
    45.00,
    20,
    30,
    15,
    'Warehouse B, Shelf 1',
    TRUE,
    NOW()
),
(
    'ITEM-005',
    'Pencils (Box of 12)',
    'Office Supplies',
    'Box',
    80.00,
    15,
    20,
    5,
    'Warehouse A, Shelf 2',
    TRUE,
    NOW()
);

-- Verify the inserted data
SELECT 
    item_id,
    item_code,
    item_description,
    category,
    unit_of_measure,
    stock_on_hand,
    reorder_level,
    location,
    CASE 
        WHEN stock_on_hand = 0 THEN 'Out of Stock'
        WHEN stock_on_hand <= reorder_level THEN 'Low Stock'
        ELSE 'In Stock'
    END as stock_status
FROM inventory_items
WHERE item_code IN ('ITEM-001', 'ITEM-002', 'ITEM-003', 'ITEM-004', 'ITEM-005')
ORDER BY item_code;

