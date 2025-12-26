# Document Tracking System (DTS) - Database Schema Documentation

## Overview

This database schema supports the complete Document Tracking System for Magallanes National High School's procurement process. The schema is designed for MySQL/MariaDB and follows best practices for data integrity, relationships, and performance.

## Database Information

- **Database Name**: `dts_db`
- **Character Set**: utf8mb4
- **Collation**: utf8mb4_unicode_ci
- **Engine**: InnoDB (for foreign key support and transactions)

## Installation

1. Ensure MySQL/MariaDB is installed and running
2. Execute the schema file:
   ```bash
   mysql -u root -p < database/dts_schema.sql
   ```
   Or import via phpMyAdmin or MySQL Workbench

## Schema Structure

The database is organized into the following main sections:

### 1. User Management and Authentication
- **offices**: Organizational offices/departments
- **roles**: User roles (Teacher, Supply Office, PPMP Manager, etc.)
- **users**: System users with authentication
- **user_sessions**: Active user sessions (optional)

### 2. Inventory Management
- **inventory_items**: Master inventory item catalog
- **inventory_movements**: Stock movement transaction history

### 3. Supply Requests
- **supply_requests**: Teacher/end-user supply requests
- **supply_request_items**: Items within each supply request

### 4. PPMP (Project Procurement Management Plan)
- **ppmp**: PPMP master documents
- **ppmp_items**: Items within each PPMP
- **ppmp_item_schedules**: Monthly procurement schedules for PPMP items
- **ppmp_amendments**: PPMP amendment history
- **app**: Annual Procurement Plan (consolidated PPMPs)
- **app_ppmp**: Relationship between APP and PPMP

### 5. Purchase Requests (PR)
- **purchase_requests**: Purchase request documents
- **purchase_request_items**: Items within each PR

### 6. Requisition and Issue Slip (RIS)
- **ris**: Requisition and Issue Slip documents
- **ris_items**: Items issued in each RIS

### 7. Obligation Request Status (ORS)
- **ors**: ORS documents
- **ors_checklist_items**: ORS checklist completion tracking

### 8. Budget Management
- **budget_allocations**: Budget allocation master data
- **budget_reservations**: Budget reservations for documents
- **budget_releases**: Budget release transactions

### 9. Supplier Management
- **suppliers**: Supplier master data

### 10. Procurement
- **purchase_orders**: Purchase order documents
- **purchase_order_items**: Items in each PO
- **quotations**: Supplier quotations
- **procurement_checklists**: Procurement process checklists
- **inspection_acceptance_reports**: IAR documents
- **iar_items**: Items inspected in IAR

### 11. Disbursement Vouchers (DV)
- **disbursement_vouchers**: DV documents
- **dv_items**: Items in each DV
- **dv_document_links**: Links DV to related documents (PR, ORS, PO, IAR)

### 12. Payment
- **cheques**: Cheque records
- **payments**: Payment transaction records

### 13. Document Tracking and Audit
- **document_tracking**: Document status tracking
- **audit_logs**: Comprehensive audit trail
- **notifications**: System notifications
- **document_versions**: Document version history

## Key Relationships

### Document Flow
1. **Supply Request** → Can create → **RIS** (if available) OR **PR** (if not available)
2. **PR** → Links to → **PPMP** / **APP**
3. **PR** → After approval → **ORS**
4. **PR + ORS** → **PO**
5. **PO** → After delivery → **IAR**
6. **PR + ORS + PO + IAR** → **DV**
7. **DV** → After approval → **Cheque** / **Payment**

### User-Role-Office Hierarchy
- Users belong to **one role** and optionally **one office**
- Roles define permissions and access levels
- Offices represent organizational units

### Budget Flow
- **Budget Allocations** → **Budget Reservations** (when PR/ORS created)
- **Budget Reservations** → **Budget Releases** (when DV approved)

## Indexes

The schema includes comprehensive indexes for:
- Primary keys (automatic)
- Foreign keys (for join performance)
- Status fields (for filtering)
- Date fields (for date range queries)
- Document numbers (for lookups)
- Tracking IDs (for document tracking)

## Data Types

- **INT**: Integer values (IDs, quantities, years)
- **DECIMAL(15,2)**: Monetary amounts with 2 decimal precision
- **VARCHAR**: Variable-length strings
- **TEXT**: Large text fields
- **DATE**: Date values (without time)
- **TIMESTAMP**: Date and time values (with timezone)
- **ENUM**: Enumerated values (statuses, types)
- **JSON**: JSON data (for audit logs, document versions)
- **BOOLEAN**: Boolean values (true/false)

## Status Enumerations

### Supply Request Status
- Submitted, Available, Not Available, Pending PPMP, For Approval, Approved, Rejected, Pending Budget, Under Procurement, DV Processing, Paid, Completed

### PR Status
- Draft, PPMP Validated, For Approval, Approved, Rejected, Pending Budget, Budget Cleared, ORS Created, Under Procurement, Completed, Cancelled

### PO Status
- Draft, Issued, Acknowledged, Delivered, Inspection, Accepted, Rejected, Cancelled

### DV Status
- Draft, For Approval, Approved, Budget Released, Payment Processing, Paid, Cancelled

## Audit Trail

All critical operations are logged in the **audit_logs** table with:
- Action type and description
- Table name and record ID
- User who performed the action
- Old and new values (JSON format)
- IP address and user agent
- Timestamp

## Security Considerations

1. **Password Storage**: Passwords should be hashed using bcrypt/argon2 (not stored in plain text)
2. **Foreign Keys**: Enforce referential integrity
3. **Indexes**: Optimize query performance
4. **Transactions**: Use transactions for multi-table operations
5. **Audit Logging**: All changes are tracked in audit_logs

## Sample Queries

### Get all pending supply requests
```sql
SELECT sr.*, u.first_name, u.last_name
FROM supply_requests sr
JOIN users u ON sr.requester_id = u.user_id
WHERE sr.status = 'Submitted'
ORDER BY sr.request_date DESC;
```

### Get PR with related documents
```sql
SELECT pr.*, ppmp.ppmp_number, ors.ors_number, po.po_number
FROM purchase_requests pr
LEFT JOIN ppmp ON pr.ppmp_id = ppmp.ppmp_id
LEFT JOIN ors ON ors.pr_id = pr.pr_id
LEFT JOIN purchase_orders po ON po.pr_id = pr.pr_id
WHERE pr.pr_id = ?;
```

### Get audit trail for a document
```sql
SELECT al.*, u.username, u.first_name, u.last_name
FROM audit_logs al
LEFT JOIN users u ON al.user_id = u.user_id
WHERE al.table_name = 'purchase_requests' AND al.record_id = ?
ORDER BY al.created_at DESC;
```

## Maintenance

### Regular Maintenance Tasks
1. **Backup**: Regular database backups
2. **Optimization**: Run `OPTIMIZE TABLE` periodically
3. **Index Analysis**: Monitor query performance and add indexes as needed
4. **Archive Old Data**: Archive completed transactions older than retention period
5. **Clean Sessions**: Clean expired sessions from user_sessions table

### Data Retention
- Consider archiving completed transactions after a retention period
- Maintain audit logs according to institutional policy
- Archive document versions periodically

## Notes

- All timestamps use TIMESTAMP type with automatic CURRENT_TIMESTAMP
- Foreign keys use ON DELETE RESTRICT for most critical relationships
- Status fields use ENUM for data integrity
- Document numbers should be unique (enforced by UNIQUE constraints)
- Tracking IDs are unique identifiers for document tracking

## Support

For questions or issues with the database schema, please contact the system administrator.

---

**Last Updated**: January 2025
**Version**: 1.0

