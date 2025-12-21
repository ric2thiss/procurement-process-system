# DOCUMENT TRACKING SYSTEM (DTS) - COMPREHENSIVE DOCUMENTATION

## 1. PURPOSE OF THE SYSTEM

The Procurement System is intended to digitally support, track, and document the existing manual procurement process of the institution—from request initiation to payment completion—while preserving the exact approval sequence, decision points, and document dependencies defined in the manual workflow.

### 1.1 Objectives

- **Digital Transformation**: Convert manual procurement processes into a streamlined digital workflow
- **Accountability**: Ensure full traceability of all procurement transactions
- **Transparency**: Provide real-time visibility into document status and progress
- **Compliance**: Maintain adherence to institutional procurement policies and procedures
- **Efficiency**: Reduce processing time and eliminate paper-based bottlenecks
- **Audit Trail**: Create comprehensive, immutable records for audit and compliance purposes

### 1.2 Scope

The system covers the complete procurement lifecycle:
- Supply request initiation
- Inventory management and availability checking
- Purchase request creation and approval
- Budget verification and obligation processing
- Procurement execution
- Payment and disbursement
- Document archiving and audit trail maintenance

---

## 2. SYSTEM OVERVIEW

The system operates as a document-driven, workflow-based platform where transactions move across offices following a fixed procedural flow. Each transaction is recorded and monitored through a central Document Tracking System, ensuring accountability, transparency, and traceability.

### 2.1 Architecture

The system follows a **workflow-based architecture** where:
- Documents flow through predefined stages based on institutional procedures
- Each stage requires specific actions from designated roles
- Status transitions are controlled and logged
- All actions are timestamped and attributed to specific users

### 2.2 Key Principles

1. **Document-Driven**: All processes are centered around official documents (PR, RIS, ORS, DV, etc.)
2. **Role-Based Access**: Users can only access and perform actions appropriate to their role
3. **Sequential Workflow**: Documents must pass through stages in the correct order
4. **Immutability**: Completed transactions cannot be modified, only archived
5. **Audit Compliance**: All actions are logged for audit purposes

### 2.3 System Benefits

- **Reduced Processing Time**: Automated routing eliminates manual document handoffs
- **Error Reduction**: Validation rules prevent common mistakes
- **Real-Time Visibility**: Stakeholders can track document status instantly
- **Centralized Storage**: All documents stored in one secure location
- **Automated Notifications**: Users notified of pending actions and status changes
- **Comprehensive Reporting**: Generate reports for management and audit purposes

---

## 3. SYSTEM MODULES OVERVIEW

The system is composed of the following core modules, each aligned with a specific role and responsibility:

### 3.1 Module List

1. **Teacher / End-User Module** - Request initiation and status monitoring
2. **Supply Office Module** - Inventory management and availability checking
3. **PPMP Management Module** - PPMP creation, management, and validation
4. **Purchase Request & PPMP Module** - PR creation and PPMP validation
5. **Principal / School Head Approval Module** - Executive approval authority
6. **Budgeting / Accounting Module** - Budget verification and ORS processing
7. **Procurement Office Module** - Procurement execution and PO management
8. **Bookkeeper Module** - Disbursement voucher preparation
9. **Payment & Disbursement Module** - Payment processing and cheque issuance
10. **Document Tracking & Audit Module** - System-wide tracking and audit trail

### 3.2 Module Interdependencies

Modules are interconnected through the document workflow:
- Each module receives documents from the previous stage
- Modules validate prerequisites before processing
- Documents are automatically routed to the next appropriate module
- All modules contribute to the central audit trail

---

## 4. DETAILED PROCESS FLOW BY MODULE

### 4.1 TEACHER / END-USER MODULE

#### Purpose

To initiate requests for equipment or supplies and monitor their progress throughout the procurement lifecycle.

#### User Capabilities

- Create and submit Supply Requests
- View request status and history
- Track document progress in real-time
- Receive notifications about status changes
- View issued items and completed transactions
- Access historical request records

#### Process Flow

1. **Request Initiation**
   - Teacher identifies the need for equipment or supplies
   - Teacher logs into the system
   - Teacher navigates to the Supply Request creation form

2. **Request Submission**
   - Teacher fills out the Supply Request form with:
     - Item description and specifications
     - Quantity required
     - Justification/purpose
     - Priority level (if applicable)
     - Expected delivery date (if applicable)
   - Teacher submits the Supply Request
   - System validates required fields
   - System generates a unique tracking ID

3. **System Processing**
   - The request is registered in the Document Tracking System
   - Initial status is set to "Submitted"
   - The request is automatically forwarded to the Supply Office
   - Teacher receives confirmation notification

4. **Status Monitoring**
   - Teacher can view the current status of the request
   - Teacher receives notifications for:
     - Status changes
     - Approval/rejection decisions
     - Item availability
     - Issuance completion
   - Teacher can view detailed transaction history

5. **Completion**
   - If item is available: Teacher receives notification of issuance
   - If procurement is required: Teacher can track through entire procurement process
   - Upon completion, transaction is archived

#### Data Requirements

- User identification (Teacher ID, Name, Department)
- Item details (Description, Quantity, Unit of Measure)
- Justification text
- Request date and time
- Priority classification (if applicable)

#### Notifications

- Confirmation of submission
- Status change alerts
- Approval/rejection notifications
- Issuance completion notices

---

### 4.2 SUPPLY OFFICE MODULE

#### Purpose

To validate item availability in inventory and determine whether procurement is required or items can be issued directly from stock.

#### User Capabilities

- Receive and review Supply Requests
- Check inventory availability
- Generate Requisition and Issue Slip (RIS)
- Issue items directly from inventory
- Create Purchase Requests for unavailable items
- Update inventory levels
- View inventory reports

#### Process Flow

1. **Request Receipt**
   - Supply Office receives the Supply Request
   - Request appears in pending queue
   - Supply Office staff reviews request details

2. **Inventory Check**
   - Supply Office checks inventory availability
   - System displays current stock levels
   - Supply Office verifies:
     - Item availability
     - Quantity on hand
     - Item condition
     - Location/warehouse

3. **Decision Point: Is the equipment available?**

   **If AVAILABLE:**
   
   a. **RIS Preparation**
      - Supply Office prepares a Requisition and Issue Slip (RIS)
      - RIS includes:
        - Request details
        - Item specifications
        - Quantity to issue
        - Stock numbers
        - Unit prices
        - Total amount
      - RIS is generated in official format
   
   b. **Item Issuance**
      - Equipment is physically issued to the Teacher
      - Teacher acknowledges receipt (digital signature/confirmation)
      - Inventory levels are updated
      - Issue date and time are recorded
   
   c. **Forwarding to Accounting Office**
      - RIS is forwarded to the Accounting Office
      - Accounting Office receives RIS for budget verification and processing
      - Status is updated to "RIS Generated - For Accounting"
      - Supply Office receives confirmation
      - Process continues to Accounting Office for budget check

   **If NOT AVAILABLE:**
   
   a. **PR Processing Initiation**
      - Supply Office proceeds with Purchase Request (PR) processing
      - PR is created based on the Supply Request
      - PR includes all original request details
   
   b. **PPMP Validation**
      - Request is validated against the Project Procurement Management Plan (PPMP)
      - System checks if item is included in current PPMP
      - PPMP reference is attached to PR
   
   c. **Forwarding**
      - PR is forwarded to next stage (PPMP Module)
      - Status is updated to "Not Available - PR Created"
      - Supply Office receives confirmation

#### Inventory Management

- Real-time inventory tracking
- Stock level updates upon issuance
- Low stock alerts
- Inventory reports and analytics
- Stock movement history

#### Data Requirements

- Inventory database with current stock levels
- Item master data (descriptions, units, prices)
- Stock numbers and locations
- Issue history records

#### Notifications

- New request alerts
- Low stock warnings
- Issuance completion confirmations
- PR creation confirmations

---

### 4.3 PPMP MANAGEMENT MODULE

#### Purpose

To create, manage, and maintain the Project Procurement Management Plan (PPMP), which serves as the annual procurement blueprint for the institution. This module enables comprehensive PPMP administration including creation, item management, amendments, and reporting.

#### User Capabilities

- Create and manage PPMP documents for different fiscal years
- Add, edit, and manage PPMP items (descriptions, quantities, budgets, schedules)
- View PPMP list and detailed PPMP information
- Handle PPMP amendments and revisions
- Generate PPMP reports and analytics
- View dashboard overview with PPMP statistics
- Manage PPMP item master data
- Track PPMP utilization and budget allocation

#### Process Flow

1. **PPMP Creation**
   - User navigates to "Create New PPMP" section
   - User selects fiscal year for the PPMP
   - User enters PPMP header information:
     - End User/Unit
     - Project, Programs & Activities (PAPs)
     - Fund source (e.g., MOOE)
   - System generates new PPMP document
   - PPMP is saved with unique identifier

2. **PPMP Item Management**
   - User adds items to PPMP through "Items Management" section
   - For each item, user enters:
     - Item code
     - General description
     - Quantity/Size
     - Unit of measure
     - Unit price
     - Estimated budget
     - Mode of procurement
     - Monthly schedule (Jan-Dec)
   - Items are validated and saved
   - Budget totals are automatically calculated

3. **PPMP Viewing and Editing**
   - User can view all PPMPs in "PPMP List" section
   - User can open existing PPMP for viewing or editing
   - Changes are tracked and logged
   - PPMP can be printed or exported

4. **PPMP Amendments**
   - User navigates to "PPMP Amendments" section
   - User selects PPMP to amend
   - Amendments can include:
     - Adding new items
     - Modifying existing items
     - Adjusting budgets
     - Updating schedules
   - Amendment reason is recorded
   - Amendment history is maintained
   - Original PPMP is preserved for audit

5. **PPMP Reporting**
   - User accesses "Reports & Analytics" section
   - Available reports include:
     - PPMP summary reports
     - Budget utilization reports
     - Item procurement status
     - Schedule compliance reports
   - Reports can be filtered by year, unit, or item
   - Reports can be exported in various formats

6. **PPMP Validation for PRs**
   - When Purchase Requests are created, system validates against PPMP
   - System checks if requested items exist in PPMP
   - System verifies budget availability
   - System confirms schedule alignment
   - Validation results are recorded

#### PPMP Dashboard Features

- Overview statistics (total PPMPs, active items, budget utilization)
- Quick access to key functions
- Recent PPMP activity
- Pending amendments
- Budget status indicators

#### Data Requirements

- PPMP master data (fiscal year, unit, PAPs)
- PPMP items (codes, descriptions, quantities, prices)
- Budget allocations per item
- Monthly procurement schedules
- PPMP approval records
- Amendment history
- Historical PPMP data

#### Notifications

- PPMP creation confirmations
- Amendment approval requests
- Budget threshold alerts
- Schedule milestone reminders
- PPMP expiration warnings

---

### 4.4 PURCHASE REQUEST & PPMP MODULE

#### Purpose

To formalize procurement needs, ensure alignment with approved procurement plans, and validate that requests comply with institutional procurement policies.

#### User Capabilities

- Create and manage Purchase Requests (PR)
- Validate PPMP inclusion (references PPMP Management Module)
- Link PRs to PPMP items
- View PPMP validation results
- Track PR status

#### Process Flow

1. **PR Generation**
   - A Purchase Request (PR) is generated from Supply Request
   - PR includes:
     - Item details (description, quantity, specifications)
     - Justification and purpose
     - Estimated unit price and total amount
     - Required delivery date
     - Requestor information
     - Required signatures/approvals

2. **PPMP Validation**
   - PR is checked for PPMP inclusion
   - System searches PPMP database for matching items
   - Validation checks:
     - Item code/description match
     - Quantity within PPMP limits
     - Budget availability in PPMP
     - Procurement schedule alignment

3. **Decision Point: Is the request included in the PPMP?**

   **If INCLUDED:**
   
   a. **PPMP Linking**
      - PR is linked to PPMP item
      - PPMP reference number is assigned
      - Budget allocation is reserved
      - Procurement schedule is confirmed
   
   b. **Forwarding to Accounting Office**
      - PR is forwarded to the Accounting / Budget Office
      - Accounting Office will check PPMP and budget availability
      - Document status is updated to "PPMP Validated - For Budget Check"
      - Accounting Office is notified
   
   c. **Processing Continues**
      - PR proceeds to Accounting Office for budget verification

   **If NOT INCLUDED:**
   
   a. **Tagging**
      - PR is tagged as "Pending PPMP"
      - Reason for exclusion is recorded
      - PPMP amendment may be required
   
   b. **Waiting List**
      - Request is placed on a waiting list for the next budget year
      - Request is queued for PPMP inclusion review
      - Priority is assigned based on institutional criteria
   
   c. **Notification**
      - Teacher is notified of the status
      - Supply Office is informed
      - Requestor may be asked to provide additional justification
   
   d. **Suspension**
      - Process is suspended
      - PR remains in "Pending PPMP" status
      - Can be reactivated when PPMP is updated

#### PPMP Integration

- References PPMP data from PPMP Management Module
- Validates PR items against existing PPMP entries
- Checks budget availability from PPMP
- Verifies procurement schedule alignment
- Links PRs to specific PPMP items

#### Data Requirements

- PPMP master data (items, codes, descriptions)
- Budget allocations per PPMP item
- Procurement schedules (monthly/quarterly)
- PPMP approval records
- Historical PPMP data

#### Notifications

- PPMP validation results
- PPMP inclusion/exclusion notifications
- Approval routing notifications
- PPMP amendment alerts

---

### 4.5 PRINCIPAL / SCHOOL HEAD APPROVAL MODULE

#### Purpose

To provide executive approval authority for procurement requests, ensuring that all purchases align with institutional priorities and policies.

#### User Capabilities

- Review pending Purchase Requests
- Approve or reject PRs
- Add remarks/comments to decisions
- View PR history and details
- Access approval reports
- Delegate approval authority (if applicable)

#### Process Flow

1. **PR Receipt**
   - Principal / School Head receives the Purchase Request
   - PR appears in approval queue
   - System displays:
     - Complete PR details
     - Item specifications
     - Justification
     - Estimated costs
     - PPMP reference
     - Previous approval history

2. **Review Process**
   - Principal / School Head reviews the Purchase Request
   - Reviews:
     - Item necessity and justification
     - Budget impact
     - PPMP alignment
     - Compliance with policies
     - Timing and urgency

3. **Action Options**
   - Action options are limited to:
     - **Approve**: Authorize the procurement
     - **Reject**: Deny the request with remarks
   - (Approval is equivalent to an official signature)

4. **Decision Point: Is the PR approved?**

   **If REJECTED:**
   
   a. **Rejection Processing**
      - PR is returned with remarks
      - Rejection reason is recorded
      - Remarks are mandatory for rejection
      - Rejection timestamp is logged
   
   b. **Status Update**
      - Status is updated to "Rejected"
      - PR is marked as final (no further processing)
   
   c. **Notification**
      - Teacher and originating offices are notified
      - Rejection reason is communicated
      - Requestor may submit new request with corrections
   
   d. **Process Termination**
      - Process ends
      - Transaction is archived with rejection status

   **If APPROVED:**
   
   a. **Digital Signature**
      - PR is digitally signed by Principal / School Head
      - Signature includes:
        - Approver name and title
        - Approval date and time
        - Digital signature hash
      - Signature is immutable and auditable
   
   b. **Forwarding**
      - PR is forwarded back to the Accounting / Budget Office for ORS preparation
      - Approval notification is sent
      - PR status is updated to "Approved by Principal"
   
   c. **Processing Continues**
      - Accounting Office prepares ORS based on approved PR
      - After ORS completion, PR and ORS are forwarded to Procurement Office

#### Approval Workflow

- Sequential approval (if multiple approvers required)
- Approval delegation (temporary authority transfer)
- Approval history tracking
- Approval deadline management
- Reminder notifications for pending approvals

#### Data Requirements

- Approver identification and credentials
- Approval decision (approve/reject)
- Approval remarks/comments
- Approval timestamp
- Digital signature data

#### Notifications

- New PR pending approval alerts
- Approval deadline reminders
- Approval completion notifications
- Rejection notifications to requestor

---

### 4.6 BUDGETING / ACCOUNTING MODULE

#### Purpose

To verify budget availability, process obligations, and ensure financial compliance before procurement execution.

#### User Capabilities

- Receive approved PRs
- Check budget availability
- Create and manage Obligation Request Status (ORS)
- Process budget reservations
- Manage budget allocations
- Generate budget reports
- Track obligations and commitments

#### Process Flow

1. **Document Receipt**
   - Accounting Office receives documents from two paths:
     - **Path 1**: RIS (Requisition and Issue Slip) from Supply Office (when items are available)
     - **Path 2**: PR with PPMP reference from PPMP Module (when items are not available)
   - Documents appear in accounting queue
   - System displays:
     - Document details (RIS or PR)
     - Item details and amounts
     - PPMP budget allocation (for PRs)
     - Current budget status
     - Previous obligations

2. **PPMP and Budget Verification**
   - For PRs: Accounting Office checks PPMP from Supply Office
   - Budget availability is verified
   - System verifies:
     - Available budget for item category
     - PPMP budget allocation status (for PRs)
     - Existing obligations and commitments
     - Budget period validity
     - Fund source availability

3. **Decision Point: Is there available budget?**

   **If NO AVAILABLE BUDGET:**
   
   a. **Tagging**
      - PR is tagged as "Pending Budget"
      - Budget shortfall amount is recorded
      - Reason for unavailability is documented
   
   b. **Waiting List**
      - Request is placed on a waiting list for the next budget cycle
      - Priority is assigned based on:
        - Request date
        - Item urgency
        - Institutional priorities
      - Request is queued for budget allocation review
   
   c. **Status Recording**
      - Status is recorded in the Document Tracking System
      - Budget office notes are added
      - Stakeholders are notified
   
   d. **Suspension**
      - Process is suspended
      - PR remains in "Pending Budget" status
      - Can be reactivated when budget becomes available

   **If BUDGET IS AVAILABLE:**
   
   a. **Budget Reservation**
      - Budget is reserved for the procurement
      - Budget allocation is recorded
      - Available budget is reduced
      - Reservation is linked to PR or RIS
   
   b. **PR Creation and Signature (for PRs)**
      - For Purchase Requests: Accounting Office creates PR with signature
      - PR is prepared with all required details
      - PR includes budget confirmation
      - PR is digitally signed by Accounting Office
      - PR status is updated to "Budget Cleared - For Principal Approval"
   
   c. **Forwarding to Principal**
      - Signed PR is forwarded to Principal / School Head for approval
      - Principal receives PR with budget confirmation
      - Accounting Office receives confirmation
      - Status is updated to "For Principal Approval"
   
   d. **ORS Preparation (After Principal Approval)**
      - After Principal approves PR, Accounting Office prepares the Obligation Request Status (ORS)
      - ORS includes:
        - PR reference
        - Item details and amounts
        - Budget allocation information
        - Fund source
        - Obligation details
        - Required checklist items
   
   e. **ORS Checklist Completion**
      - ORS checklist is completed
      - Required documents are verified:
        - Approved PR
        - PPMP reference
        - Budget allocation confirmation
        - Other required attachments
      - Checklist items are marked complete
      - ORS is finalized
   
   f. **Forwarding to Procurement**
      - PR and completed ORS are forwarded to the Procurement Office
      - Status is updated to "Budget Cleared - ORS Completed"
      - Procurement Office is notified

#### Budget Management

- Real-time budget tracking
- Budget allocation management
- Obligation and commitment tracking
- Budget period management
- Fund source management
- Budget reports and analytics

#### Data Requirements

- Budget master data (allocations, categories)
- Current budget balances
- Obligation records
- Commitment records
- Fund source information
- Budget period definitions

#### Notifications

- New PR for budget verification alerts
- Budget availability status
- ORS completion notifications
- Budget shortfall alerts
- Budget allocation confirmations

---

### 4.7 PROCUREMENT OFFICE MODULE

#### Purpose

To manage procurement execution based on approved and budget-cleared requests, ensuring compliance with procurement regulations and policies.

#### User Capabilities

- Receive approved PRs and ORS
- Manage procurement checklists
- Create and manage Purchase Orders (PO)
- Execute procurement activities
- Manage supplier relationships
- Track procurement progress
- Generate procurement reports

#### Process Flow

1. **Document Receipt**
   - Procurement Office receives:
     - Approved PR
     - Completed ORS
     - All supporting documents
   - Documents appear in procurement queue

2. **Document Verification**
   - PPMP reference is confirmed
   - All prerequisites are verified:
     - Approved PR
     - Completed ORS
     - Budget clearance
     - Required attachments

3. **Procurement Checklist Preparation**
   - Procurement Checklist is prepared
   - Checklist includes:
     - Document completeness verification
     - Procurement method determination
     - Supplier identification requirements
     - Bidding/quotation requirements
     - Compliance checks
   - Checklist items are completed systematically

4. **Purchase Order (PO) Processing**
   - Purchase Order (PO) checklist is processed
   - PO creation requirements:
     - Supplier selection/identification
     - Price quotations/bids
     - Terms and conditions
     - Delivery requirements
     - Payment terms
   - PO is generated and prepared

5. **Procurement Activities**
   - Procurement activities are carried out:
     - Supplier communication
     - Quotation/bid collection
     - Supplier evaluation
     - PO issuance
     - Delivery coordination
     - Receipt verification
   - All activities are logged

6. **Status Update**
   - Request status is updated to "Under Procurement"
   - Progress milestones are recorded
   - Stakeholders are updated on progress

7. **Completion**
   - Upon delivery and acceptance:
     - Delivery is confirmed
     - Items are received and verified
     - Acceptance documentation is completed
     - Status is updated to "Procurement Completed"
     - Documents are forwarded to Bookkeeper

#### Procurement Methods

- Small Value Procurement (SVP)
- Shopping
- Competitive Bidding
- Negotiated Procurement
- Other methods as per regulations

#### Data Requirements

- Supplier master data
- Quotation/bid records
- PO records
- Delivery and receipt records
- Procurement method classifications

#### Notifications

- New PR/ORS receipt alerts
- Procurement milestone updates
- PO issuance notifications
- Delivery status updates
- Procurement completion alerts

---

### 4.8 BOOKKEEPER MODULE

#### Purpose

To prepare disbursement documentation for payment, ensuring all requirements are met before payment processing.

#### User Capabilities

- Receive completed procurement documents
- Create and manage Disbursement Vouchers (DV)
- Link documents (PR, ORS, PO, receipts)
- Verify payment requirements
- Forward DVs for approval
- Track DV status
- Generate payment reports

#### Process Flow

1. **Document Receipt**
   - Bookkeeper receives completed procurement documents:
     - Approved PR
     - Completed ORS
     - PO and delivery documents
     - Receipt and acceptance documents
     - Supplier invoices
     - Other supporting documents

2. **Document Verification**
   - All documents are verified for completeness
   - Payment requirements are checked:
     - Complete documentation
     - Proper approvals
     - Budget clearance
     - Delivery confirmation
     - Invoice accuracy

3. **DV Generation**
   - Disbursement Voucher (DV) is generated
   - DV includes:
     - Payment details
     - Supplier information
     - Item details and amounts
     - Document references
     - Payment terms
     - Required signatures

4. **Document Linking**
   - DV is linked to:
     - PR (Purchase Request)
     - ORS (Obligation Request Status)
     - PO (Purchase Order)
     - Delivery receipts
     - Supplier invoices
     - Other supporting documents
   - All links are verified

5. **DV Finalization**
   - DV is reviewed for accuracy
   - All amounts are verified
   - Required attachments are confirmed
   - DV is finalized and prepared

6. **Forwarding**
   - DV is forwarded to the School Head for signature
   - Status is updated to "DV for Approval"
   - School Head is notified
   - Payment processing is initiated

#### DV Requirements

- Complete documentation package
- Accurate amount calculations
- Proper document linking
- Required approvals
- Compliance with payment policies

#### Data Requirements

- DV master data
- Payment details
- Supplier payment information
- Document linkage records
- Payment history

#### Notifications

- New documents for DV creation alerts
- DV completion notifications
- DV approval status updates
- Payment processing alerts

---

### 4.9 PAYMENT & DISBURSEMENT MODULE

#### Purpose

To complete payment to the supplier, ensuring proper authorization and documentation before fund release.

#### User Capabilities

- Receive DVs for payment processing
- Process budget release
- Generate cheques
- Manage cheque signatures
- Track payment status
- Record payment completion
- Generate payment reports

#### Process Flow

1. **DV Review and Signature**
   - School Head reviews the Disbursement Voucher
   - School Head verifies:
     - Payment accuracy
     - Document completeness
     - Compliance with policies
   - School Head signs the DV (digital signature)
   - Signature is recorded and timestamped

2. **Budget Release**
   - Signed DV is forwarded to the Budget Office
   - Budget Office processes budget release
   - Reserved budget is converted to obligation
   - Budget is released for payment
   - Release is recorded in system

3. **Cheque Generation**
   - Cheque is generated
   - Cheque includes:
     - Payee (supplier) information
     - Payment amount
     - Payment description
     - Reference numbers (PR, ORS, DV)
     - Cheque number
   - Cheque details are recorded

4. **Cheque Preparation**
   - Cheque is prepared for signature
   - Required signatories are identified
   - Cheque is routed for signature
   - Signatures are obtained and recorded

5. **Cheque Issuance**
   - Cheque is issued to the Supplier
   - Issuance is recorded:
     - Issue date and time
     - Issued to (supplier)
     - Cheque number
     - Amount
   - Supplier acknowledges receipt (if applicable)

6. **Payment Completion**
   - Payment is marked as completed
   - Status is updated to "Paid"
   - All related documents are finalized
   - Transaction is archived
   - Stakeholders are notified

#### Payment Processing

- Cheque generation and numbering
- Signature workflow management
- Payment tracking
- Payment reconciliation
- Bank reconciliation support

#### Data Requirements

- Cheque master data
- Payment records
- Signature records
- Supplier payment information
- Bank account details
- Payment history

#### Notifications

- DV receipt for payment alerts
- Cheque generation notifications
- Payment completion alerts
- Payment status updates

---

### 4.10 DOCUMENT TRACKING & AUDIT MODULE

#### Purpose

To ensure full traceability and accountability across the entire procurement process, supporting audit requirements and compliance.

#### Key Functions

1. **Unique Tracking ID Assignment**
   - Assigns a unique tracking ID per transaction
   - Tracking ID format: [YEAR]-[TYPE]-[SEQUENCE]
   - ID is used throughout the document lifecycle
   - ID enables cross-document referencing

2. **Action Logging**
   - Logs every action, decision, and approval
   - Records include:
     - Action type (create, update, approve, reject, forward)
     - User who performed action
     - Timestamp
     - Document affected
     - Action details
     - Previous and new status

3. **Timestamp Recording**
   - Records timestamps for all events:
     - Document creation
     - Status changes
     - Approvals/rejections
     - Forwarding actions
     - Completion events
   - Timestamps are server-synchronized and immutable

4. **Responsible Office Tracking**
   - Records responsible offices for each stage
   - Tracks office assignments
   - Records office actions and decisions
   - Maintains office accountability

5. **Immutable Records**
   - Maintains immutable records for completed transactions
   - Completed transactions cannot be modified
   - Historical records are preserved
   - Audit trail is permanent

6. **Audit Support**
   - Supports audit and compliance requirements
   - Provides audit reports
   - Enables audit trail queries
   - Supports compliance verification

#### Audit Trail Features

- Complete transaction history
- User action logs
- Document version history
- Status change history
- Approval/rejection history
- Time-based queries
- User-based queries
- Document-based queries

#### Reporting Capabilities

- Transaction status reports
- Processing time reports
- Approval/rejection reports
- Budget utilization reports
- Procurement activity reports
- Audit trail reports
- Custom report generation

#### Data Retention

- Active transactions: Real-time access
- Completed transactions: Archived with full history
- Retention period: As per institutional policy
- Backup and recovery: Regular backups maintained

---

## 5. SYSTEM STATUS FLOW

The system uses a comprehensive status flow to track document progress through the procurement lifecycle. Statuses are sequential and represent specific stages in the process.

### 5.1 Status Definitions

1. **Submitted**
   - Initial status when Supply Request is created
   - Request has been submitted by Teacher
   - Awaiting Supply Office review

2. **Available (Issued)**
   - Item is available in inventory
   - RIS has been prepared
   - Item has been issued to Teacher
   - Transaction is complete

3. **Not Available**
   - Item is not available in inventory
   - Purchase Request has been created
   - Awaiting PPMP validation

4. **Pending PPMP**
   - PR is not included in current PPMP
   - Request is on waiting list
   - Awaiting PPMP inclusion or amendment
   - Process is suspended

5. **For Approval**
   - PR is included in PPMP
   - PR is forwarded to Principal/School Head
   - Awaiting approval decision

6. **Approved**
   - PR has been approved by Principal/School Head
   - PR is forwarded to Budget Office
   - Awaiting budget verification

7. **Rejected**
   - PR has been rejected by Principal/School Head
   - Rejection reason has been recorded
   - Process is terminated
   - Requestor has been notified

8. **Pending Budget**
   - PR is approved but budget is not available
   - Request is on budget waiting list
   - Awaiting budget allocation
   - Process is suspended

9. **Under Procurement**
   - Budget has been cleared
   - ORS has been completed
   - Procurement activities are in progress
   - Awaiting delivery and acceptance

10. **DV Processing**
    - Procurement is completed
    - DV has been created
    - Awaiting School Head signature
    - Payment processing initiated

11. **Paid**
    - DV has been signed
    - Cheque has been issued
    - Payment has been completed
    - Awaiting final archiving

12. **Completed**
    - Payment has been completed
    - All documents have been finalized
    - Transaction has been archived
    - Process is fully complete

### 5.2 Status Transition Rules

- Statuses must follow the defined sequence
- Skipping statuses is not allowed
- Reversing statuses requires special authorization
- Status changes are logged and auditable
- Each status has specific prerequisites

### 5.3 Status Flow Diagram

```
Submitted
    ↓
[Inventory Check - Supply Office]
    ├─→ Available → RIS Generated → Accounting Office → [Budget Check] → [END for RIS]
    └─→ Not Available → PR Created
            ↓
        [PPMP Check - PPMP Module]
            ├─→ Pending PPMP → [SUSPENDED]
            └─→ PPMP Validated → Accounting Office
                    ↓
                [Budget Check - Accounting Office]
                    ├─→ Pending Budget → [SUSPENDED]
                    └─→ Budget Available → PR with Signature Created
                            ↓
                        [Principal Approval]
                            ├─→ Rejected → [END]
                            └─→ Approved → Accounting Office (ORS Preparation)
                                    ↓
                                ORS Completed → Procurement Office
                                    ↓
                                [Procurement Checklist]
                                    ↓
                                Under Procurement
                                    ↓
                                [Delivery & Acceptance]
                                    ↓
                                DV Processing (Parallel Track)
                                    ↓
                                [School Head Signature]
                                    ↓
                                Budget Office (Budget Release)
                                    ↓
                                Paid
                                    ↓
                                Completed → [END]
```

---

## 6. DOCUMENTS MANAGED BY THE SYSTEM

The system manages various official documents throughout the procurement process. Each document has specific requirements, formats, and purposes.

### 6.1 Document List

1. **Supply Request**
   - **Purpose**: Initial request for equipment or supplies
   - **Created By**: Teacher/End-User
   - **Contains**: Item description, quantity, justification, requestor info
   - **Format**: Digital form with required fields
   - **Status**: Submitted → Processed

2. **Requisition and Issue Slip (RIS)**
   - **Purpose**: Document for issuing items from inventory
   - **Created By**: Supply Office
   - **Contains**: Item details, quantities, stock numbers, prices, issue information
   - **Format**: Official RIS form (printable PDF/HTML)
   - **Status**: Generated when items are available

3. **Purchase Request (PR)**
   - **Purpose**: Formal request for procurement
   - **Created By**: Supply Office (from Supply Request)
   - **Contains**: Item specifications, quantities, estimated costs, justification, approvals
   - **Format**: Official PR form with digital signatures
   - **Status**: Created → Approved/Rejected → Processed

4. **Project Procurement Management Plan (PPMP)**
   - **Purpose**: Annual procurement plan
   - **Created By**: Procurement/Budget Office
   - **Contains**: Planned items, quantities, budgets, schedules, procurement methods
   - **Format**: Official PPMP form with monthly schedule
   - **Status**: Annual document, referenced throughout year

5. **Obligation Request Status (ORS)**
   - **Purpose**: Document for budget obligation
   - **Created By**: Budget/Accounting Office
   - **Contains**: PR reference, budget allocation, obligation details, checklist
   - **Format**: Official ORS form
   - **Status**: Created → Completed → Processed

6. **Procurement Checklist**
   - **Purpose**: Checklist for procurement compliance
   - **Created By**: Procurement Office
   - **Contains**: Required documents, compliance items, verification checklist
   - **Format**: Digital checklist form
   - **Status**: In Progress → Completed

7. **Purchase Order (PO) Checklist**
   - **Purpose**: Checklist for PO creation
   - **Created By**: Procurement Office
   - **Contains**: Supplier info, quotations, terms, delivery requirements
   - **Format**: Digital checklist form
   - **Status**: In Progress → Completed

8. **Disbursement Voucher (DV)**
   - **Purpose**: Document for payment authorization
   - **Created By**: Bookkeeper
   - **Contains**: Payment details, supplier info, document references, signatures
   - **Format**: Official DV form with signatures
   - **Status**: Created → Approved → Processed

9. **Cheque**
   - **Purpose**: Payment instrument
   - **Created By**: Payment/Disbursement Module
   - **Contains**: Payee, amount, description, cheque number, signatures
   - **Format**: Official cheque format
   - **Status**: Generated → Signed → Issued

10. **Document Tracking Log**
    - **Purpose**: Audit trail of all document actions
    - **Created By**: System (automatic)
    - **Contains**: Action history, timestamps, users, status changes
    - **Format**: Digital log (queryable database)
    - **Status**: Continuously updated

### 6.2 Document Relationships

Documents are interconnected:
- Supply Request → PR
- PR → ORS
- PR → PPMP (reference)
- PR + ORS → Procurement Checklist
- PR + ORS + PO → DV
- DV → Cheque
- All documents → Tracking Log

### 6.3 Document Storage

- **Active Documents**: Stored in active database
- **Completed Documents**: Archived in document repository
- **Backup**: Regular backups maintained
- **Retention**: Per institutional policy
- **Access Control**: Role-based access to documents

### 6.4 Document Formats

- **Digital Forms**: Interactive web forms
- **Printable Forms**: PDF/HTML formats for printing
- **Digital Signatures**: Cryptographic signatures for approvals
- **Attachments**: Support for file attachments (images, PDFs, etc.)

---

## 7. USER ROLES AND PERMISSIONS

The system implements role-based access control (RBAC) to ensure users can only access and perform actions appropriate to their role.

### 7.1 Role Definitions

1. **Teacher / End-User**
   - Create and submit Supply Requests
   - View own request status
   - Receive notifications
   - View issued items
   - Access own request history

2. **Supply Office Staff**
   - Receive Supply Requests
   - Check inventory
   - Create RIS
   - Issue items
   - Create PRs
   - Manage inventory
   - View inventory reports

3. **PPMP Manager**
   - Create and manage PPMP documents
   - Add and manage PPMP items
   - Handle PPMP amendments
   - Generate PPMP reports and analytics
   - View PPMP dashboard and statistics
   - Manage PPMP master data

4. **Purchase Request & PPMP Manager**
   - Create and manage PRs
   - Validate PPMP inclusion (references PPMP Management Module)
   - View PPMP validation results
   - Link PRs to PPMP items
   - Track PR status

5. **Principal / School Head**
   - Review PRs
   - Approve/reject PRs
   - Add approval remarks
   - View approval reports
   - Sign DVs
   - Delegate approval authority (if applicable)

6. **Budget / Accounting Staff**
   - Receive approved PRs
   - Check budget availability
   - Create and manage ORS
   - Process budget reservations
   - Manage budget allocations
   - Generate budget reports

7. **Procurement Office Staff**
   - Receive PRs and ORS
   - Manage procurement checklists
   - Create and manage POs
   - Execute procurement activities
   - Manage suppliers
   - Track procurement progress

8. **Bookkeeper**
   - Receive procurement documents
   - Create and manage DVs
   - Link documents
   - Verify payment requirements
   - Forward DVs for approval
   - Generate payment reports

9. **Payment / Disbursement Staff**
   - Receive DVs
   - Process budget release
   - Generate cheques
   - Manage cheque signatures
   - Track payment status
   - Record payment completion

10. **System Administrator**
   - Full system access
   - User management
   - Role management
   - System configuration
   - Audit log access
   - System maintenance

11. **Auditor / Read-Only User**
    - View all documents (read-only)
    - Access audit logs
    - Generate reports
    - Export data
    - No modification permissions

### 7.2 Permission Matrix

| Action | Teacher | Supply | PPMP Mgr | PR/PPMP | Principal | Budget | Procurement | Bookkeeper | Payment | Admin |
|--------|---------|--------|----------|---------|-----------|--------|--------------|------------|---------|-------|
| Create Supply Request | ✓ | - | - | - | - | - | - | - | - | ✓ |
| View Own Requests | ✓ | - | - | - | - | - | - | - | - | ✓ |
| Check Inventory | - | ✓ | - | - | - | - | - | - | - | ✓ |
| Create RIS | - | ✓ | - | - | - | - | - | - | - | ✓ |
| Create PPMP | - | - | ✓ | - | - | - | - | - | - | ✓ |
| Manage PPMP Items | - | - | ✓ | - | - | - | - | - | - | ✓ |
| Handle PPMP Amendments | - | - | ✓ | - | - | - | - | - | - | ✓ |
| View PPMP Reports | - | - | ✓ | ✓ | - | - | - | - | - | ✓ |
| Create PR | - | ✓ | - | ✓ | - | - | - | - | - | ✓ |
| Validate PPMP Inclusion | - | - | - | ✓ | - | - | - | - | - | ✓ |
| Approve PR | - | - | - | - | ✓ | - | - | - | - | ✓ |
| Check Budget | - | - | - | - | - | ✓ | - | - | - | ✓ |
| Create ORS | - | - | - | - | - | ✓ | - | - | - | ✓ |
| Execute Procurement | - | - | - | - | - | - | ✓ | - | - | ✓ |
| Create DV | - | - | - | - | - | - | - | ✓ | - | ✓ |
| Process Payment | - | - | - | - | - | - | - | - | ✓ | ✓ |
| View All Documents | - | - | - | - | - | - | - | - | - | ✓ |
| System Configuration | - | - | - | - | - | - | - | - | - | ✓ |

### 7.3 Access Control

- **Authentication**: Required for all system access
- **Authorization**: Role-based permissions enforced
- **Session Management**: Secure session handling
- **Audit Logging**: All access attempts logged

---

## 8. NOTIFICATIONS SYSTEM

The system provides automated notifications to keep users informed about document status changes and pending actions.

### 8.1 Notification Types

1. **Status Change Notifications**
   - Sent when document status changes
   - Includes previous and new status
   - Includes document reference
   - Sent to relevant stakeholders

2. **Approval Request Notifications**
   - Sent when approval is required
   - Includes document details
   - Includes approval deadline (if applicable)
   - Sent to approver

3. **Completion Notifications**
   - Sent when process stages complete
   - Includes completion details
   - Sent to requestor and relevant offices

4. **Rejection Notifications**
   - Sent when requests are rejected
   - Includes rejection reason
   - Sent to requestor

5. **Reminder Notifications**
   - Sent for pending actions
   - Includes action required
   - Includes deadline (if applicable)

### 8.2 Notification Channels

- **In-System Notifications**: Displayed in user dashboard
- **Email Notifications**: Sent to user email (if configured)
- **SMS Notifications**: Sent via SMS (if configured)

### 8.3 Notification Preferences

Users can configure:
- Notification types to receive
- Notification channels
- Frequency of reminders
- Quiet hours

---

## 9. REPORTING AND ANALYTICS

The system provides comprehensive reporting capabilities for management, audit, and operational purposes.

### 9.1 Report Types

1. **Transaction Status Reports**
   - Current status of all transactions
   - Filterable by status, date, office, user
   - Exportable formats

2. **Processing Time Reports**
   - Time taken for each stage
   - Average processing times
   - Bottleneck identification

3. **Approval/Rejection Reports**
   - Approval rates
   - Rejection reasons
   - Approval timelines

4. **Budget Utilization Reports**
   - Budget allocation status
   - Obligation tracking
   - Available budget

5. **Procurement Activity Reports**
   - Procurement activities by period
   - Supplier performance
   - Item procurement history

6. **Audit Trail Reports**
   - Complete action history
   - User activity logs
   - Document change history

### 9.2 Report Features

- **Filtering**: Multiple filter options
- **Sorting**: Customizable sorting
- **Export**: PDF, Excel, CSV formats
- **Scheduling**: Automated report generation
- **Customization**: Custom report builder

---

## 10. SYSTEM REQUIREMENTS

### 10.1 Technical Requirements

**Server Requirements:**
- Web server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB 10.3 or higher
- Minimum 4GB RAM
- Minimum 50GB storage
- SSL certificate (for production)

**Client Requirements:**
- Modern web browser (Chrome, Firefox, Edge, Safari)
- JavaScript enabled
- Internet connection
- Minimum screen resolution: 1024x768

### 10.2 Software Dependencies

- PHP extensions: PDO, MySQLi, GD, JSON, MBString
- Web server modules: mod_rewrite (Apache)
- Database: MySQL/MariaDB

### 10.3 Security Requirements

- HTTPS encryption (production)
- Password encryption (bcrypt/argon2)
- Session security
- SQL injection prevention
- XSS protection
- CSRF protection
- Role-based access control
- Audit logging

---

## 11. ERROR HANDLING AND EXCEPTIONS

### 11.1 Error Types

1. **Validation Errors**
   - Missing required fields
   - Invalid data formats
   - Business rule violations
   - User-friendly error messages

2. **System Errors**
   - Database connection errors
   - Server errors
   - File system errors
   - Logged for administrator review

3. **Workflow Errors**
   - Invalid status transitions
   - Missing prerequisites
   - Authorization failures
   - Documented and reported

### 11.2 Exception Handling

- **User-Friendly Messages**: Clear error messages for users
- **Error Logging**: Detailed logs for administrators
- **Recovery Procedures**: Documented recovery processes
- **Support Contact**: Support information provided

---

## 12. DATA SECURITY AND PRIVACY

### 12.1 Data Protection

- **Encryption**: Sensitive data encrypted at rest and in transit
- **Access Control**: Role-based access to data
- **Audit Trail**: All data access logged
- **Backup**: Regular secure backups

### 12.2 Privacy

- **User Data**: Protected per privacy policy
- **Document Access**: Restricted to authorized users
- **Data Retention**: Per institutional policy
- **Data Deletion**: Secure deletion procedures

---

## 13. SYSTEM MAINTENANCE

### 13.1 Regular Maintenance

- **Database Optimization**: Regular optimization
- **Backup**: Daily automated backups
- **Log Rotation**: Log file management
- **Security Updates**: Regular security patches
- **Performance Monitoring**: System performance tracking

### 13.2 Support and Troubleshooting

- **User Support**: Help desk support
- **Documentation**: User guides and manuals
- **Training**: User training programs
- **Issue Tracking**: Issue reporting system

---

## 14. END OF PROCESS

The procurement process concludes when:

1. **Payment Completion**
   - Payment has been issued to the supplier
   - Cheque has been delivered
   - Payment is recorded in system

2. **Document Finalization**
   - All related documents have been logged
   - All documents have been finalized
   - All approvals are complete

3. **Archiving**
   - Transaction has been archived
   - All documents stored in archive
   - Audit trail is complete

4. **Notification**
   - All stakeholders notified of completion
   - Requestor receives completion confirmation
   - Final status update recorded

### 14.1 Process Completion Criteria

- ✓ Payment issued and recorded
- ✓ All documents finalized
- ✓ All approvals complete
- ✓ Transaction archived
- ✓ Audit trail complete
- ✓ Stakeholders notified

### 14.2 Post-Completion

- Transaction is read-only
- Documents are archived
- Available for audit and reporting
- Cannot be modified
- Historical reference maintained

---

## 15. APPENDICES

### 15.1 Glossary

- **PR**: Purchase Request
- **RIS**: Requisition and Issue Slip
- **PPMP**: Project Procurement Management Plan
- **ORS**: Obligation Request Status
- **PO**: Purchase Order
- **DV**: Disbursement Voucher
- **DTS**: Document Tracking System

### 15.2 Document Version History

- **Version 1.0**: Initial comprehensive documentation
- **Date**: [Current Date]
- **Author**: System Documentation Team

### 15.3 Contact Information

For system support, training, or questions:
- **System Administrator**: [Contact Information]
- **Help Desk**: [Contact Information]
- **Technical Support**: [Contact Information]

---

**END OF DOCUMENTATION**
