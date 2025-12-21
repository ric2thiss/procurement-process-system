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
**Status**: Not yet created
- **Purpose**: Inventory management and availability checking
- **Key Features**:
  - Receive and review Supply Requests
  - Check inventory availability
  - Generate Requisition and Issue Slip (RIS)
  - Issue items directly from inventory
  - Create Purchase Requests for unavailable items
  - Update inventory levels

## 3. Purchase Request & PPMP Module
**Status**: Not yet created
- **Purpose**: PR creation and PPMP validation
- **Key Features**:
  - Create and manage Purchase Requests (PR)
  - Validate PPMP inclusion
  - Link PRs to PPMP items
  - Manage PPMP master data
  - View PPMP reports

## 4. Principal / School Head Approval Module
**Status**: Not yet created
- **Purpose**: Executive approval authority
- **Key Features**:
  - Review pending Purchase Requests
  - Approve or reject PRs
  - Add remarks/comments to decisions
  - View PR history and details
  - Sign Disbursement Vouchers (DV)

## 5. Budgeting / Accounting Module
**Status**: Not yet created
- **Purpose**: Budget verification and ORS processing
- **Key Features**:
  - Receive approved PRs and RIS
  - Check budget availability
  - Create and manage Obligation Request Status (ORS)
  - Process budget reservations
  - Manage budget allocations
  - Generate budget reports

## 6. Procurement Office Module
**Status**: Not yet created
- **Purpose**: Procurement execution and PO management
- **Key Features**:
  - Receive PRs and ORS
  - Manage procurement checklists
  - Create and manage Purchase Orders (PO)
  - Execute procurement activities
  - Manage supplier relationships
  - Track procurement progress

## 7. Bookkeeper Module
**Status**: Not yet created
- **Purpose**: Disbursement voucher preparation
- **Key Features**:
  - Receive completed procurement documents
  - Create and manage Disbursement Vouchers (DV)
  - Link documents (PR, ORS, PO, receipts)
  - Verify payment requirements
  - Forward DVs for approval

## 8. Payment & Disbursement Module
**Status**: Not yet created
- **Purpose**: Payment processing and cheque issuance
- **Key Features**:
  - Receive DVs for payment processing
  - Process budget release
  - Generate cheques
  - Manage cheque signatures
  - Track payment status
  - Record payment completion

## 9. Document Tracking & Audit Module
**Status**: Not yet created
- **Purpose**: System-wide tracking and audit trail
- **Key Features**:
  - Assign unique tracking IDs
  - Log all actions and decisions
  - Record timestamps
  - Track responsible offices
  - Maintain immutable records
  - Generate audit reports

---

**Note**: Currently, only the Teacher / End-User Module has been implemented with UI. Other modules are pending implementation.

