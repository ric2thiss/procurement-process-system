# USER ROLES AND USERS DOCUMENTATION

This document provides a comprehensive list of all user roles in the Document Tracking System (DTS).

## Overview

The system implements role-based access control (RBAC) to ensure users can only access and perform actions appropriate to their role. Each role has specific responsibilities and permissions aligned with the Philippine Government procurement process.

---

## Total Number of Roles: **11 Roles**

---

## 1. TEACHER / END-USER

**Module Location**: `module/teacher/teacher_dashboard.html`

### Purpose
To initiate requests for equipment or supplies and monitor their progress throughout the procurement lifecycle.

### Key Responsibilities
- Create and submit Supply Requests
- View own request status and history
- Track document progress in real-time
- Receive notifications about status changes
- View issued items and completed transactions
- Access historical request records

### Key Actions
- Submit Supply Requests with item details, quantity, and justification
- Monitor request status throughout the procurement process
- Receive notifications for status updates
- Acknowledge receipt of issued items

### Documents Created/Managed
- **Supply Request**: Initial request for equipment or supplies

---

## 2. SUPPLY OFFICE STAFF

**Module Location**: `module/supply-office/supply_dashboard.html`

### Purpose
To validate item availability in inventory and determine whether procurement is required or items can be issued directly from stock.

### Key Responsibilities
- Receive and review Supply Requests
- Check inventory availability
- Generate Requisition and Issue Slip (RIS)
- Issue items directly from inventory
- Create Purchase Requests for unavailable items
- Update inventory levels
- View inventory reports and analytics

### Key Actions
- Check inventory stock levels
- Generate RIS for available items
- Create PR when items are not available
- Manage inventory database
- Update stock levels after issuance

### Documents Created/Managed
- **Requisition and Issue Slip (RIS)**: Document for issuing items from inventory
- **Purchase Request (PR)**: Created when items are not available in stock

---

## 3. PPMP MANAGER

**Module Location**: `module/ppmp-management/ppmp_dashboard.html`

### Purpose
To create, manage, and maintain the Project Procurement Management Plan (PPMP), which serves as the annual procurement blueprint for the institution.

### Key Responsibilities
- Create and manage PPMP documents for different fiscal years
- Add, edit, and manage PPMP items (descriptions, quantities, budgets, schedules)
- View PPMP list and detailed PPMP information
- Handle PPMP amendments and revisions
- Consolidate PPMPs into Annual Procurement Plan (APP) - STEP 5
- Generate PPMP reports and analytics
- Manage PPMP item master data
- Track PPMP utilization and budget allocation

### Key Actions
- Create annual PPMP documents
- Add items to PPMP with budget allocations
- Handle PPMP amendments for unplanned items
- Consolidate PPMPs into APP for HoPE approval
- Generate PPMP reports

### Documents Created/Managed
- **Project Procurement Management Plan (PPMP)**: Annual procurement plan
- **Annual Procurement Plan (APP)**: Consolidated PPMPs (STEP 5)

---

## 4. PURCHASE REQUEST & PPMP MANAGER

**Module Location**: `module/purchase-request-ppmp/pr_ppmp_dashboard.html`

### Purpose
To formalize procurement needs, ensure alignment with approved procurement plans, and validate that requests comply with institutional procurement policies.

### Key Responsibilities
- Create and manage Purchase Requests (PR)
- Validate PPMP inclusion
- Link PRs to PPMP items
- Reference approved APP in PR creation (STEP 6)
- View PPMP validation results
- Track PR status
- Manage pending PPMP requests
- Generate PR and PPMP reports

### Key Actions
- Create PR from Supply Requests
- Validate PR items against existing PPMP
- Link PR to approved APP
- Track PR validation status

### Documents Created/Managed
- **Purchase Request (PR)**: Formal request for procurement (STEP 6)

---

## 5. PRINCIPAL / SCHOOL HEAD

**Module Location**: `module/principal/principal_dashboard.html`

### Purpose
To provide executive approval authority for procurement requests, ensuring that all purchases align with institutional priorities and policies.

### Key Responsibilities
- Review pending Purchase Requests
- Approve or reject PRs (STEP 7)
- Add remarks/comments to decisions
- View PR history and details
- Sign Disbursement Vouchers (DV)
- Delegate approval authority (if applicable)
- View approval reports

### Key Actions
- Review PR details, budget impact, and justification
- Approve or reject PRs with remarks
- Sign DVs for payment authorization
- Delegate approval authority temporarily

### Documents Reviewed/Approved
- **Purchase Request (PR)**: Approval/rejection decision (STEP 7)
- **Disbursement Voucher (DV)**: Signature for payment authorization

---

## 6. BUDGET / ACCOUNTING STAFF

**Module Location**: `module/budgeting-accounting/budgeting_dashboard.html`

### Purpose
To verify budget availability, process obligations, and ensure financial compliance before procurement execution.

### Key Responsibilities
- Review PPMP submissions and verify budget availability (STEP 4)
- Receive approved PRs and RIS
- Check budget availability for procurement
- Create and manage Obligation Request Status (ORS)
- Process budget reservations
- Manage budget allocations
- Generate budget reports
- Track obligations and commitments

### Key Actions
- Review PPMP and check budget availability (STEP 4)
- Verify budget for PRs
- Reserve budget for procurement
- Create ORS after Principal approval
- Complete ORS checklist
- Manage budget allocations and reports

### Documents Created/Managed
- **Obligation Request Status (ORS)**: Document for budget obligation
- **PPMP Budget Review**: Review PPMP submissions (STEP 4)

---

## 7. PROCUREMENT OFFICE STAFF

**Module Location**: `module/procurement-office/procurement_dashboard.html`

### Purpose
To manage procurement execution based on approved and budget-cleared requests, ensuring compliance with procurement regulations and policies.

### Key Responsibilities
- Receive approved PRs and ORS
- Manage procurement checklists
- Select procurement method (Shopping/RFQ/Canvas/Bidding) - STEP 8
- Create and manage Purchase Orders (PO)
- Execute procurement activities (Canvas, Abstract, Award)
- Handle Inspection and Acceptance (IAR) - STEP 10
- Manage supplier relationships
- Track procurement progress
- Generate procurement reports

### Key Actions
- Verify document completeness
- Select procurement method
- Issue RFQ/Canvas/Invitation to Bid
- Evaluate quotations/bids
- Prepare Abstract of Quotations
- Process Purchase Orders
- Coordinate delivery and inspection
- Prepare Inspection and Acceptance Report (IAR)

### Documents Created/Managed
- **Procurement Checklist**: Checklist for procurement compliance
- **Purchase Order (PO)**: Issued to suppliers (STEP 8)
- **Inspection and Acceptance Report (IAR)**: Inspection results (STEP 10)

---

## 8. BOOKKEEPER

**Module Location**: `module/bookkeeper/bookkeeper_dashboard.html`

### Purpose
To prepare disbursement documentation for payment, ensuring all requirements are met before payment processing.

### Key Responsibilities
- Receive completed procurement documents (PR, ORS, PO, IAR)
- Create and manage Disbursement Vouchers (DV)
- Link documents (PR, ORS, PO, receipts, invoices)
- Verify payment requirements
- Forward DVs for approval
- Track DV status
- Generate payment reports

### Key Actions
- Verify document completeness
- Create DV linking all procurement documents
- Verify payment requirements and amounts
- Forward DV to School Head for signature

### Documents Created/Managed
- **Disbursement Voucher (DV)**: Document for payment authorization (STEP 11)

---

## 9. PAYMENT / DISBURSEMENT STAFF

**Module Location**: `module/payment-disbursement/payment_dashboard.html`

### Purpose
To complete payment to the supplier, ensuring proper authorization and documentation before fund release.

### Key Responsibilities
- Receive DVs for payment processing
- Process budget release
- Generate cheques
- Manage cheque signatures
- Track payment status
- Record payment completion
- Generate payment reports

### Key Actions
- Process budget release for signed DVs
- Generate payment cheques
- Manage cheque signature routing
- Issue cheques to suppliers
- Record payment completion

### Documents Created/Managed
- **Cheque**: Payment instrument issued to suppliers (STEP 11)

---

## 10. SYSTEM ADMINISTRATOR

**Module Location**: `module/admin/admin_dashboard.html`

### Purpose
To manage system configuration, users, roles, and system maintenance.

### Key Responsibilities
- Full system access
- User management (create, edit, deactivate users)
- Role management and permissions
- System configuration and settings
- Audit log access
- System maintenance and troubleshooting
- Data backup and recovery

### Key Actions
- Manage user accounts and roles
- Configure system settings
- Access audit logs
- Perform system maintenance
- Manage system security

### Special Permissions
- Full access to all modules
- User and role management
- System configuration
- Audit log access

---

## 11. AUDITOR / READ-ONLY USER

**Module Location**: `module/document-tracking-audit/audit_dashboard.html`

### Purpose
To provide audit trail access and read-only document viewing for compliance and audit purposes.

### Key Responsibilities
- View all documents (read-only)
- Access audit logs and transaction history
- Generate audit reports
- Export data for audit purposes
- Track document versions and changes
- Monitor system activity

### Key Actions
- View all procurement documents
- Access complete audit trail
- Generate audit reports
- Export transaction data
- Track document history

### Special Permissions
- Read-only access to all documents
- Audit log access
- Report generation
- Data export
- **No modification permissions**

---

## ROLE SUMMARY TABLE

| # | Role | Module | Primary Function |
|---|------|--------|------------------|
| 1 | Teacher / End-User | Teacher | Request initiation |
| 2 | Supply Office Staff | Supply Office | Inventory management |
| 3 | PPMP Manager | PPMP Management | PPMP & APP management |
| 4 | PR & PPMP Manager | Purchase Request & PPMP | PR creation & validation |
| 5 | Principal / School Head | Principal | Executive approval |
| 6 | Budget / Accounting Staff | Budgeting / Accounting | Budget verification & ORS |
| 7 | Procurement Office Staff | Procurement Office | Procurement execution |
| 8 | Bookkeeper | Bookkeeper | DV preparation |
| 9 | Payment / Disbursement Staff | Payment & Disbursement | Payment processing |
| 10 | System Administrator | Admin | System management |
| 11 | Auditor / Read-Only User | Document Tracking & Audit | Audit & compliance |

---

## NOTES

1. **Role Overlap**: PPMP Manager (#3) and PR & PPMP Manager (#4) may be the same person/team in some institutions, but they have distinct responsibilities in the system.

2. **Office Affiliations**: Some roles belong to the same office but have different responsibilities:
   - Budget/Accounting Staff and Bookkeeper may both be in Accounting Office
   - Payment/Disbursement Staff may also be part of Accounting/Budget Office

3. **Process Steps**: Each role participates in specific steps of the procurement process as outlined in `documentation-2.md`.

4. **Permissions**: Detailed permission matrix is available in `documentation.md` Section 7.2.

---

**Last Updated**: Based on system implementation as of current date
**Reference Documents**: 
- `documentation.md` - Section 7: User Roles and Permissions
- `documentation-2.md` - Final Procurement Process Sequence

