# Database Schema Summary - Quick Reference

## Table Count by Category

- **User Management**: 4 tables (offices, roles, users, user_sessions)
- **Inventory**: 2 tables (inventory_items, inventory_movements)
- **Supply Requests**: 2 tables (supply_requests, supply_request_items)
- **PPMP**: 6 tables (ppmp, ppmp_items, ppmp_item_schedules, ppmp_amendments, app, app_ppmp)
- **Purchase Requests**: 2 tables (purchase_requests, purchase_request_items)
- **RIS**: 2 tables (ris, ris_items)
- **ORS**: 2 tables (ors, ors_checklist_items)
- **Budget**: 3 tables (budget_allocations, budget_reservations, budget_releases)
- **Suppliers**: 1 table (suppliers)
- **Procurement**: 6 tables (purchase_orders, purchase_order_items, quotations, procurement_checklists, inspection_acceptance_reports, iar_items)
- **Disbursement**: 3 tables (disbursement_vouchers, dv_items, dv_document_links)
- **Payment**: 2 tables (cheques, payments)
- **Tracking & Audit**: 4 tables (document_tracking, audit_logs, notifications, document_versions)

**Total: 39 tables**

## Document Flow Relationships

```
Supply Request
    ├─→ RIS (if item available)
    │   └─→ Accounting/Budget Office
    │
    └─→ PR (if item not available)
        ├─→ PPMP/APP Validation
        ├─→ Principal Approval
        ├─→ ORS (after approval)
        ├─→ PO (with ORS)
        │   ├─→ Quotations
        │   ├─→ Procurement Checklist
        │   └─→ IAR (after delivery)
        │
        └─→ DV (with PR, ORS, PO, IAR)
            ├─→ Budget Release
            ├─→ Cheque
            └─→ Payment
```

## Key Status Flows

### Supply Request Status Flow
```
Submitted → Available/Not Available → (If Available) Issued → Completed
                                      → (If Not Available) → PR Created → ...
```

### PR Status Flow
```
Draft → PPMP Validated → For Approval → Approved → Budget Cleared → ORS Created → 
Under Procurement → Completed
```

### PO Status Flow
```
Draft → Issued → Acknowledged → Delivered → Inspection → Accepted → (to DV)
```

### DV Status Flow
```
Draft → For Approval → Approved → Budget Released → Payment Processing → Paid
```

## Common Query Patterns

### Get Document with Related Data
```sql
-- Supply Request with items and requester
SELECT sr.*, u.first_name, u.last_name, 
       GROUP_CONCAT(sri.item_description) as items
FROM supply_requests sr
JOIN users u ON sr.requester_id = u.user_id
LEFT JOIN supply_request_items sri ON sr.supply_request_id = sri.supply_request_id
WHERE sr.supply_request_id = ?
GROUP BY sr.supply_request_id;
```

### Get Document Tracking History
```sql
SELECT dt.*, o.office_name, u.username
FROM document_tracking dt
LEFT JOIN offices o ON dt.current_office_id = o.office_id
LEFT JOIN users u ON dt.current_user_id = u.user_id
WHERE dt.document_type = 'PR' AND dt.document_id = ?
ORDER BY dt.tracked_at DESC;
```

### Get Budget Status
```sql
SELECT ba.*,
       COALESCE(SUM(br.reserved_amount), 0) as total_reserved,
       ba.allocated_amount - COALESCE(SUM(br.reserved_amount), 0) as available
FROM budget_allocations ba
LEFT JOIN budget_reservations br ON ba.allocation_id = br.allocation_id 
    AND br.status IN ('Reserved', 'Obligated')
WHERE ba.fiscal_year = YEAR(CURDATE())
GROUP BY ba.allocation_id;
```

## Important Notes

1. **Document Numbers**: All document types have unique number fields (pr_number, ris_number, ors_number, etc.)

2. **Tracking IDs**: Supply requests use tracking_id format: YYYY-SR-XXX

3. **Status Management**: Status fields use ENUM types for data integrity

4. **Timestamps**: Most tables have created_at and updated_at for audit purposes

5. **Soft Deletes**: Many tables use is_active flag instead of hard deletes

6. **Foreign Keys**: 
   - Most use ON DELETE RESTRICT (prevents deletion)
   - Some use ON DELETE SET NULL (for optional relationships)
   - Some use ON DELETE CASCADE (for dependent records)

7. **Indexes**: Key fields are indexed for performance:
   - Primary keys (automatic)
   - Foreign keys
   - Status fields
   - Date fields
   - Document numbers
   - Tracking IDs

## Data Integrity Rules

1. A user must have a role (required)
2. A PR must link to either a PPMP or APP
3. An ORS requires either a PR or RIS
4. A PO requires both PR and ORS
5. An IAR requires a PO
6. A DV requires an ORS
7. Budget reservations require a budget allocation
8. Payments require a DV

## Performance Considerations

1. Use indexes for frequently queried fields
2. Partition large tables (audit_logs, document_tracking) by date if needed
3. Archive old completed transactions
4. Regularly optimize tables
5. Use transactions for multi-table operations

## Security Features

1. Password hashing (store hashes, not plain text)
2. Audit logging (all changes tracked)
3. User sessions (track active sessions)
4. Role-based access (enforced at application level)
5. Foreign key constraints (data integrity)

