<?php
/**
 * Seed Inventory Items
 * Document Tracking System - Magallanes National High School
 * 
 * This script seeds 5 sample school supply items into the inventory_items table
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getDBConnection();
    
    // Check if items already exist
    $checkSql = "SELECT COUNT(*) FROM inventory_items WHERE item_code IN ('ITEM-001', 'ITEM-002', 'ITEM-003', 'ITEM-004', 'ITEM-005')";
    $existingCount = $pdo->query($checkSql)->fetchColumn();
    
    if ($existingCount > 0) {
        echo "Some inventory items already exist. Skipping insertion.\n";
        echo "To re-seed, delete existing items first or modify the item codes.\n";
        exit;
    }
    
    // Insert 5 School Supply Items
    $items = [
        [
            'item_code' => 'ITEM-001',
            'item_description' => 'A4 Bond Paper (Ream)',
            'category' => 'Office Supplies',
            'unit_of_measure' => 'Ream',
            'standard_unit_price' => 250.00,
            'reorder_level' => 10,
            'reorder_quantity' => 20,
            'stock_on_hand' => 25,
            'location' => 'Warehouse A, Shelf 1'
        ],
        [
            'item_code' => 'ITEM-002',
            'item_description' => 'Whiteboard Markers (Set of 10)',
            'category' => 'Teaching Materials',
            'unit_of_measure' => 'Set',
            'standard_unit_price' => 150.00,
            'reorder_level' => 5,
            'reorder_quantity' => 10,
            'stock_on_hand' => 8,
            'location' => 'Warehouse A, Shelf 3'
        ],
        [
            'item_code' => 'ITEM-003',
            'item_description' => 'Ballpoint Pens (Box of 12)',
            'category' => 'Office Supplies',
            'unit_of_measure' => 'Box',
            'standard_unit_price' => 120.00,
            'reorder_level' => 10,
            'reorder_quantity' => 15,
            'stock_on_hand' => 12,
            'location' => 'Warehouse B, Shelf 2'
        ],
        [
            'item_code' => 'ITEM-004',
            'item_description' => 'Notebooks (Spiral, 100 pages)',
            'category' => 'Office Supplies',
            'unit_of_measure' => 'Piece',
            'standard_unit_price' => 45.00,
            'reorder_level' => 20,
            'reorder_quantity' => 30,
            'stock_on_hand' => 15,
            'location' => 'Warehouse B, Shelf 1'
        ],
        [
            'item_code' => 'ITEM-005',
            'item_description' => 'Pencils (Box of 12)',
            'category' => 'Office Supplies',
            'unit_of_measure' => 'Box',
            'standard_unit_price' => 80.00,
            'reorder_level' => 15,
            'reorder_quantity' => 20,
            'stock_on_hand' => 5,
            'location' => 'Warehouse A, Shelf 2'
        ]
    ];
    
    $sql = "INSERT INTO inventory_items (
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
            ) VALUES (
                :item_code,
                :item_description,
                :category,
                :unit_of_measure,
                :standard_unit_price,
                :reorder_level,
                :reorder_quantity,
                :stock_on_hand,
                :location,
                1,
                NOW()
            )";
    
    $stmt = $pdo->prepare($sql);
    $inserted = 0;
    
    foreach ($items as $item) {
        try {
            $stmt->execute($item);
            $inserted++;
            echo "✓ Inserted: {$item['item_code']} - {$item['item_description']}\n";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                echo "⚠ Skipped (already exists): {$item['item_code']} - {$item['item_description']}\n";
            } else {
                echo "✗ Error inserting {$item['item_code']}: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n";
    echo "Successfully inserted {$inserted} inventory items.\n";
    
    // Display summary
    echo "\n=== Inventory Summary ===\n";
    $summarySql = "SELECT 
                        item_code,
                        item_description,
                        stock_on_hand,
                        reorder_level,
                        CASE 
                            WHEN stock_on_hand = 0 THEN 'Out of Stock'
                            WHEN stock_on_hand <= reorder_level THEN 'Low Stock'
                            ELSE 'In Stock'
                        END as stock_status
                    FROM inventory_items
                    WHERE item_code IN ('ITEM-001', 'ITEM-002', 'ITEM-003', 'ITEM-004', 'ITEM-005')
                    ORDER BY item_code";
    
    $summaryStmt = $pdo->query($summarySql);
    $results = $summaryStmt->fetchAll(PDO::FETCH_ASSOC);
    
    printf("%-12s %-40s %-15s %-15s %-15s\n", "Item Code", "Description", "Stock", "Reorder Level", "Status");
    echo str_repeat("-", 100) . "\n";
    
    foreach ($results as $row) {
        printf("%-12s %-40s %-15s %-15s %-15s\n", 
            $row['item_code'],
            substr($row['item_description'], 0, 38),
            $row['stock_on_hand'],
            $row['reorder_level'],
            $row['stock_status']
        );
    }
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

