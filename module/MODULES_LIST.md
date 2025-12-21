# DOCUMENT TRACKING SYSTEM - MODULES LIST

Based on the comprehensive documentation, the system consists of the following modules:

## 1. Teacher / End-User Module
**Location**: `module/teacher/teacher_dashboard.html`
- **Purpose**: Request initiation and status monitoring
- **Key Features**:
  - Create and submit Supply Requests
  - View request status and history
  - Track document progress in real-time
  - Receive notifications about status changes
  - View issued items and completed transactions
  - Access historical request records

## 2. Supply Office Module
**Location**: `module/supply-office/supply_dashboard.html`
**Status**: ✅ Created
- **Purpose**: Inventory management and availability checking
- **Key Features**:
  - Receive and review Supply Requests
  - Check inventory availability
  - Generate Requisition and Issue Slip (RIS)
  - Issue items directly from inventory
  - Create Purchase Requests for unavailable items
  - Update inventory levels
  - View inventory reports and analytics

## 3. PPMP Management Module
**Location**: `module/ppmp-management/ppmp_dashboard.html`
**Status**: ✅ Created
- **Purpose**: PPMP creation, management, and validation
- **Key Features**:
  - Create and manage PPMP documents
  - Manage PPMP items and master data
  - View PPMP list and details
  - Handle PPMP amendments
  - Generate PPMP reports and analytics
  - Dashboard overview with statistics
  - Items management for PPMP entries

## 4. Purchase Request & PPMP Module
**Location**: `module/purchase-request-ppmp/pr_ppmp_dashboard.html`
**Status**: ✅ Created
- **Purpose**: PR creation and PPMP validation
- **Key Features**:
  - Create and manage Purchase Requests (PR)
  - Validate PPMP inclusion
  - Link PRs to PPMP items
  - View PPMP validation results
  - Track PR status
  - Manage pending PPMP requests
  - Generate PR and PPMP reports

## 5. Principal / School Head Approval Module
**Location**: `module/principal/principal_dashboard.html`
**Status**: ✅ Created
- **Purpose**: Executive approval authority
- **Key Features**:
  - Review pending Purchase Requests
  - Approve or reject PRs
  - Add remarks/comments to decisions
  - View PR history and details
  - Sign Disbursement Vouchers (DV)

## 6. Budgeting / Accounting Module
**Location**: `module/budgeting-accounting/budgeting_dashboard.html`
**Status**: ✅ Created
- **Purpose**: Budget verification and ORS processing
- **Key Features**:
  - Receive approved PRs and RIS
  - Check budget availability
  - Create and manage Obligation Request Status (ORS)
  - Process budget reservations
  - Manage budget allocations
  - Generate budget reports

## 7. Procurement Office Module
**Location**: `module/procurement-office/procurement_dashboard.html`
**Status**: ✅ Created
- **Purpose**: Procurement execution and PO management
- **Key Features**:
  - Receive PRs and ORS
  - Manage procurement checklists
  - Create and manage Purchase Orders (PO)
  - Execute procurement activities
  - Manage supplier relationships
  - Track procurement progress

## 8. Bookkeeper Module
**Location**: `module/bookkeeper/bookkeeper_dashboard.html`
**Status**: ✅ Created
- **Purpose**: Disbursement voucher preparation
- **Key Features**:
  - Receive completed procurement documents
  - Create and manage Disbursement Vouchers (DV)
  - Link documents (PR, ORS, PO, receipts)
  - Verify payment requirements
  - Forward DVs for approval

## 9. Payment & Disbursement Module
**Location**: `module/payment-disbursement/payment_dashboard.html`
**Status**: ✅ Created
- **Purpose**: Payment processing and cheque issuance
- **Key Features**:
  - Receive DVs for payment processing
  - Process budget release
  - Generate cheques
  - Manage cheque signatures
  - Track payment status
  - Record payment completion

## 10. Document Tracking & Audit Module
**Location**: `module/document-tracking-audit/audit_dashboard.html`
**Status**: ✅ Created
- **Purpose**: System-wide tracking and audit trail
- **Key Features**:
  - Assign unique tracking IDs
  - Log all actions and decisions
  - Record timestamps
  - Track responsible offices
  - Maintain immutable records
  - Generate audit reports

---

**Note**: All modules have been implemented with UI. The system is now complete with all 10 modules fully functional.

