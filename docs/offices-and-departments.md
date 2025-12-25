# OFFICES AND DEPARTMENTS DOCUMENTATION

This document provides a comprehensive list of all offices and departments involved in the Document Tracking System (DTS) procurement process.

## Overview

The procurement process involves multiple offices and departments, each with specific responsibilities aligned with the Philippine Government procurement process. This document outlines all distinct offices, their roles, and their involvement in the procurement workflow.

---

## Total Number of Offices: **8 Offices/Departments**

---

## 1. SUPPLY OFFICE

**Related Modules**: 
- Supply Office Module (`module/supply-office/`)

**Key Personnel**:
- Supply Office Staff

### Primary Responsibilities
- Inventory management and stock control
- Receive and process Supply Requests from teachers/end-users
- Check inventory availability for requested items
- Generate Requisition and Issue Slip (RIS) for available items
- Issue items directly from inventory to end-users
- Create Purchase Requests (PR) when items are not available in stock
- Update inventory levels after issuance
- Maintain inventory database and reports

### Process Steps Involved
- **STEP 1**: Receives Supply Request from Teacher/End-User
- **STEP 2**: Checks inventory availability
  - If available: Issues RIS and delivers items
  - If not available: Creates PR and forwards for PPMP processing

### Documents Created/Managed
- Requisition and Issue Slip (RIS)
- Purchase Request (PR) - when items unavailable

### Workflow Position
**Start of Process**: Receives initial Supply Requests

---

## 2. BUDGET OFFICE

**Related Modules**: 
- Budgeting / Accounting Module (`module/budgeting-accounting/`)

**Key Personnel**:
- Budget Officer
- Budget / Accounting Staff

### Primary Responsibilities
- Review PPMP submissions for budget availability (STEP 4)
- Verify available budget or ability to allocate funds
- Process budget reservations
- Manage budget allocations by category
- Process budget release for payment (STEP 11)
- Generate budget reports and analytics
- Track budget utilization and obligations

### Process Steps Involved
- **STEP 4**: Reviews PPMP submissions - checks if budget is available or can be allocated
  - If budget available: Forwards to BAC Secretariat for APP consolidation
  - If no budget: Tags as "Pending PPMP - Waiting for Budget"
- **STEP 11**: Processes budget release after DV is signed for payment

### Documents Reviewed/Managed
- PPMP submissions (STEP 4)
- Budget allocations
- Budget release documentation

### Workflow Position
**After PPMP Creation** (STEP 4) and **Before Payment** (STEP 11)

---

## 3. BAC SECRETARIAT (Bids and Awards Committee Secretariat)

**Related Modules**: 
- PPMP Management Module (`module/ppmp-management/`) - APP Consolidation section

**Key Personnel**:
- PPMP Manager (may handle BAC Secretariat functions)
- BAC Secretariat Staff

### Primary Responsibilities
- Consolidate PPMPs into Annual Procurement Plan (APP) - STEP 5
- Prepare draft APP for HoPE (Head of Procuring Entity) approval
- Oversee procurement compliance with RA 9184 (Government Procurement Reform Act)
- Prepare Recommendation for Award (STEP 8)
- Submit recommendations to HoPE for approval
- Ensure procurement activities comply with regulations

### Process Steps Involved
- **STEP 5**: Consolidates approved PPMPs into draft APP
- **STEP 8**: Prepares Recommendation for Award and submits to HoPE

### Documents Created/Managed
- Annual Procurement Plan (APP) - Consolidated from PPMPs (STEP 5)
- Recommendation for Award (STEP 8)

### Workflow Position
**After Budget Office Review** (STEP 5) and **During Procurement** (STEP 8)

**Note**: BAC Secretariat may be part of Procurement Office in some institutions but has distinct responsibilities for APP consolidation and procurement compliance.

---

## 4. ACCOUNTING OFFICE

**Related Modules**: 
- Budgeting / Accounting Module (`module/budgeting-accounting/`)
- Bookkeeper Module (`module/bookkeeper/`)
- Payment & Disbursement Module (`module/payment-disbursement/`)

**Key Personnel**:
- Budget / Accounting Staff
- Bookkeeper
- Payment / Disbursement Staff

### Primary Responsibilities
- Receive RIS from Supply Office (for items available in stock)
- Verify invoices and supporting documents from suppliers (STEP 11)
- Create Obligation Request Status (ORS) after Principal approval
- Create signed PR documents with budget confirmation
- Prepare Disbursement Vouchers (DV) - STEP 11
- Process payment cheques
- Manage financial records and reporting

### Process Steps Involved
- **Receives RIS**: When items are available in stock
- **After Principal Approval**: Creates ORS (Obligation Request Status)
- **STEP 11**: Verifies supplier invoices and prepares DV
- **STEP 11**: Processes payment after DV approval

### Documents Created/Managed
- Obligation Request Status (ORS)
- Disbursement Voucher (DV) - STEP 11
- Payment cheques

### Workflow Position
**Throughout Process**: Receives RIS, creates ORS, prepares DV, processes payment

**Note**: In some institutions, Accounting Office and Budget Office may be separate; in others, they may be combined as "Budget/Accounting Office".

---

## 5. PRINCIPAL / SCHOOL HEAD OFFICE

**Related Modules**: 
- Principal Module (`module/principal/`)

**Key Personnel**:
- Principal / School Head

### Primary Responsibilities
- Review Purchase Requests for approval (STEP 7)
- Approve or reject PRs based on institutional priorities and policies
- Add remarks/comments to approval decisions
- Sign Disbursement Vouchers (DV) for payment authorization
- Delegate approval authority when necessary
- View approval reports and history

### Process Steps Involved
- **STEP 7**: Reviews and approves/rejects PRs
  - Reviews: Item necessity, budget impact, PPMP alignment, compliance
  - Decision: Approve (proceeds to procurement) or Reject (returns for correction)
- **STEP 11**: Signs DV for payment authorization

### Documents Reviewed/Approved
- Purchase Request (PR) - Approval/Rejection (STEP 7)
- Disbursement Voucher (DV) - Signature (STEP 11)

### Workflow Position
**After Budget Verification** (STEP 7) and **Before Payment** (STEP 11)

---

## 6. PROCUREMENT OFFICE

**Related Modules**: 
- Procurement Office Module (`module/procurement-office/`)

**Key Personnel**:
- Procurement Office Staff
- BAC Secretariat (may be part of this office)

### Primary Responsibilities
- Receive approved PRs and completed ORS
- Verify document completeness
- Select procurement method (Shopping/RFQ/Canvas/Bidding) - STEP 8
- Execute procurement activities:
  - Issue RFQ/Canvas/Invitation to Bid
  - Receive quotations/bids from suppliers
  - Evaluate quotations/bids
  - Prepare Abstract of Quotations / Bid Evaluation Report
  - Process Purchase Orders (PO)
- Coordinate delivery of items - STEP 9
- Handle Inspection and Acceptance (IAR) - STEP 10
- Manage supplier relationships
- Track procurement progress
- Generate procurement reports

### Process Steps Involved
- **STEP 8**: Executes procurement process
  - Selects procurement method
  - Issues RFQ/Canvas/Invitation to Bid
  - Receives and evaluates quotations/bids
  - Prepares Abstract of Quotations
  - Issues Purchase Order (PO) after HoPE approval
- **STEP 9**: Coordinates delivery of items
- **STEP 10**: Coordinates Inspection and Acceptance (IAR)

### Documents Created/Managed
- Procurement Checklist
- Purchase Order (PO) - STEP 8
- Inspection and Acceptance Report (IAR) - STEP 10

### Workflow Position
**After Principal Approval and ORS Completion** (STEP 8) through **Inspection** (STEP 10)

---

## 7. BOOKKEEPER OFFICE / UNIT

**Related Modules**: 
- Bookkeeper Module (`module/bookkeeper/`)

**Key Personnel**:
- Bookkeeper

### Primary Responsibilities
- Receive completed procurement documents (PR, ORS, PO, IAR, invoices)
- Verify document completeness for payment
- Create Disbursement Vouchers (DV) - STEP 11
- Link all related documents (PR, ORS, PO, IAR, supplier invoices)
- Verify payment requirements and amounts
- Forward DVs to School Head for signature
- Track DV status
- Generate payment reports

### Process Steps Involved
- **STEP 11**: Creates DV after receiving:
  - Approved PR
  - Completed ORS
  - PO and delivery documents
  - IAR (Inspection and Acceptance Report)
  - Supplier invoices
  - Other supporting documents

### Documents Created/Managed
- Disbursement Voucher (DV) - STEP 11

### Workflow Position
**After Inspection and Acceptance** (STEP 10) and **Before Payment** (STEP 11)

**Note**: Bookkeeper may be part of Accounting Office in some institutions but has distinct responsibilities for DV preparation.

---

## 8. INSPECTION AND ACCEPTANCE COMMITTEE (IAC)

**Related Modules**: 
- Procurement Office Module (`module/procurement-office/`) - Inspection & Acceptance section

**Key Personnel**:
- IAC Members (may include representatives from various offices)

### Primary Responsibilities
- Inspect delivered items from suppliers - STEP 10
- Verify quantity, quality, and condition of delivered items
- Check compliance with specifications
- Prepare Inspection and Acceptance Report (IAR)
- Accept or reject delivered items
- Coordinate with supplier for replacements if needed

### Process Steps Involved
- **STEP 10**: Inspection and Acceptance
  - Verifies quantity matches PO
  - Checks quality and condition
  - Assesses compliance with specifications
  - Prepares IAR (Inspection and Acceptance Report)
  - Makes acceptance/rejection decision

### Documents Created/Managed
- Inspection and Acceptance Report (IAR) - STEP 10

### Workflow Position
**After Delivery** (STEP 9) and **Before Payment Processing** (STEP 11)

**Note**: IAC is typically a committee composed of members from various offices, but it functions as a distinct unit in the process.

---

## OFFICE WORKFLOW SUMMARY

### Sequential Flow Through Offices:

1. **Supply Office** → Receives Supply Request
2. **Supply Office** → Checks Inventory
   - If available: Issues RIS → **Accounting Office** → Process complete
   - If not available: Creates PR → 
3. **PPMP Management** → Create/Update PPMP →
4. **Budget Office** → Review PPMP (STEP 4) →
5. **BAC Secretariat** → Consolidate into APP (STEP 5) →
6. **PR & PPMP Module** → Create PR referencing APP (STEP 6) →
7. **Budget/Accounting Office** → Budget verification →
8. **Principal Office** → PR Approval (STEP 7) →
9. **Accounting Office** → Create ORS →
10. **Procurement Office** → Execute procurement (STEP 8) →
11. **Procurement Office** → Delivery (STEP 9) →
12. **IAC** → Inspection and Acceptance (STEP 10) →
13. **Bookkeeper** → Create DV (STEP 11) →
14. **Principal Office** → Sign DV →
15. **Budget Office** → Budget release →
16. **Accounting/Payment Office** → Process payment (STEP 11) → **Complete**

---

## OFFICE SUMMARY TABLE

| # | Office/Department | Key Personnel | Primary Responsibility | Process Steps |
|---|-------------------|---------------|------------------------|---------------|
| 1 | Supply Office | Supply Office Staff | Inventory management & RIS issuance | STEP 1, 2 |
| 2 | Budget Office | Budget Officer, Budget Staff | PPMP review & budget management | STEP 4, 11 |
| 3 | BAC Secretariat | PPMP Manager, BAC Staff | APP consolidation & compliance | STEP 5, 8 |
| 4 | Accounting Office | Accounting Staff, Bookkeeper, Payment Staff | Financial processing & payment | Throughout |
| 5 | Principal / School Head Office | Principal / School Head | Executive approval & DV signing | STEP 7, 11 |
| 6 | Procurement Office | Procurement Staff | Procurement execution | STEP 8, 9, 10 |
| 7 | Bookkeeper Office/Unit | Bookkeeper | DV preparation | STEP 11 |
| 8 | Inspection and Acceptance Committee (IAC) | IAC Members | Item inspection & acceptance | STEP 10 |

---

## NOTES

1. **Office Combinations**: In some institutions, certain offices may be combined:
   - Budget Office and Accounting Office may be one "Budget/Accounting Office"
   - Bookkeeper may be part of Accounting Office
   - BAC Secretariat may be part of Procurement Office

2. **Multiple Functions**: Some offices participate in multiple steps:
   - Accounting Office: Receives RIS, creates ORS, prepares DV, processes payment
   - Budget Office: Reviews PPMP (STEP 4) and releases budget for payment (STEP 11)
   - Principal Office: Approves PR (STEP 7) and signs DV (STEP 11)

3. **Committee vs Office**: IAC is a committee but functions as a distinct unit in the workflow with specific responsibilities.

4. **External Entities**: Suppliers are external entities but interact with Procurement Office throughout the procurement process.

---

## OFFICE INTERACTIONS

### Document Flow Between Offices:

```
Teacher/End-User
    ↓ (Supply Request)
Supply Office
    ├→ (RIS) → Accounting Office (if available)
    └→ (PR) → Budget Office → BAC Secretariat → PR Module
                        ↓
                Principal Office (Approval)
                        ↓
                Accounting Office (ORS)
                        ↓
                Procurement Office → IAC
                        ↓
                Bookkeeper (DV)
                        ↓
                Principal Office (DV Sign)
                        ↓
                Budget Office (Budget Release)
                        ↓
                Accounting/Payment Office (Payment)
```

---

**Last Updated**: Based on system implementation and `documentation-2.md`
**Reference Documents**: 
- `documentation-2.md` - Final Procurement Process Sequence
- `documentation.md` - Section 4: Detailed Process Flow by Module
- `roles-and-users.md` - User Roles Documentation

