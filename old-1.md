# DOCUMENT TRACKING SYSTEM - PROCESS FLOW DOCUMENTATION

This document describes the detailed process flow for supply requests and purchase requests within the Document Tracking System. The process is divided into swimlanes representing different roles and departments.

**📋 Related Document**: See [MODULES_LIST.md](./MODULES_LIST.md) for a list of all integrated systems and their functionalities.

**Purpose**: This document explains **HOW** users execute tasks step-by-step using the systems documented in MODULES_LIST.md. MODULES_LIST.md shows **WHAT** systems exist; this document shows **HOW** they are used together in the actual workflow.

---

## PROCESS OVERVIEW

The supply and purchase request process follows a sequential workflow that involves multiple departments and approval stages. The process branches based on item availability and approval decisions at various checkpoints.

---

## PROCESS PARTICIPANTS (SWIMLANES)

1. **Requestor** - End Users, Teachers (All Departments)
2. **Supply Officer** - Supply Office Staff
3. **PPMP Manager** - PPMP Management Office Staff
4. **Budget Officer** - Budgeting/Accounting Office Staff
5. **Principal** - School Administration (Approval Authority)

---

## DETAILED PROCESS FLOW

### PHASE 1: SUPPLY REQUEST INITIATION

#### 1.1 Requestor Activities

**Step 1.1.1: Login**
- The Requestor logs into the Document Tracking System
- System authenticates user credentials
- User dashboard is displayed

**Step 1.1.2: Create Supply Request**
- Requestor initiates a new supply request
- System provides access to supply request creation form

**Step 1.1.3: Fill Out Request Form**
- Requestor completes the supply request form with:
  - Item description
  - Quantity
  - Unit of measurement
  - Justification/purpose
  - Expected delivery date (optional)
  - Other required fields
- Form validation is performed by the system

**Step 1.1.4: Submit Request Form**
- Requestor submits the completed supply request form
- System assigns a unique tracking ID to the request
- Request is routed to Supply Office for processing
- Requestor receives confirmation notification

---

### PHASE 2: INVENTORY AVAILABILITY CHECK

#### 2.1 Supply Officer Activities

**Step 2.1.1: Receive Supply Request**
- Supply Officer receives notification of new supply request
- Supply Officer accesses the request details in the system

**Step 2.1.2: Check Inventory Availability (Decision Point)**
- Supply Officer checks current inventory levels for the requested item
- System displays inventory status

**DECISION: Is Request Available?**

##### PATH A: Item Available (Yes)

**Step 2.1.3a: Create Requisition and Issue Slip (RIS)**
- Supply Officer generates Requisition and Issue Slip (RIS)
- System creates RIS document with:
  - Request details
  - Item information
  - Quantity to be issued
  - Issue date
- RIS is linked to the original supply request

**Step 2.1.4a: Issue Items**
- Supply Officer issues items directly from inventory
- System updates inventory levels
- Requestor is notified of item issuance
- Request status is updated to "Issued/Completed"

**END OF PROCESS FOR AVAILABLE ITEMS**

##### PATH B: Item Not Available (No)

**Step 2.1.3b: Route to PPMP Process**
- Supply Officer marks item as unavailable
- Request status is updated to "Requires PPMP"
- Request is routed to PPMP Manager for processing
- Requestor is notified that PPMP process is required
- Process continues to Phase 3

---

### PHASE 3: PPMP CREATION AND SUBMISSION

#### 3.1 PPMP Manager Activities

**Step 3.1.1: Receive Supply Request (Unavailable Item)**
- PPMP Manager receives notification of supply request requiring PPMP
- PPMP Manager accesses the supply request details

**Step 3.1.2: Create PPMP**
- PPMP Manager creates a Project Procurement Management Plan (PPMP) document
- PPMP is linked to the original supply request
- PPMP includes:
  - Item specifications
  - Estimated costs
  - Procurement method
  - Timeline
  - Budget requirements
  - Justification
- System supports both:
  - Annual PPMP (planned purchases)
  - Per-request PPMP (ad-hoc purchases)

**Step 3.1.3: Submit PPMP for Approval**
- PPMP Manager completes and submits PPMP for approval
- PPMP status is updated to "Pending Budget Review"
- PPMP is routed to Budget Officer
- System generates notification to Budget Officer
- PPMP Manager receives confirmation notification

---

### PHASE 4: BUDGET REVIEW AND ALLOCATION

#### 4.1 Budget Officer Activities

**Step 4.1.1: Receive PPMP**
- Budget Officer receives notification of submitted PPMP
- Budget Officer accesses PPMP details in the system

**Step 4.1.2: Review Budget Availability (Decision Point)**
- Budget Officer reviews current budget status
- Budget Officer checks if sufficient funds are available
- System displays budget information and allocations

**DECISION: Is There Available Budget? Can Allocate Budget?**

##### PATH A: Budget Available (Yes)

**Step 4.1.3a: Allocate Budget**
- Budget Officer confirms budget allocation for the PPMP
- System reserves budget allocation
- Budget allocation is linked to the PPMP
- PPMP status is updated to "Budget Allocated - Pending Principal Approval"
- Process continues to Phase 5 (Principal Approval)

**Step 4.1.4a: Notify Stakeholders**
- System generates notifications to:
  - PPMP Manager: Budget allocation confirmed
  - Requestor: PPMP proceeding to Principal approval
  - Principal: PPMP pending approval

##### PATH B: Budget Not Available (No)

**Step 4.1.3b: Pending PPMP - Waiting List**
- Budget Officer determines budget is not available
- PPMP status is updated to "Pending - Waiting List for Next Year Budget"
- PPMP is added to waiting list for next fiscal year
- System records pending status with reason

**Step 4.1.4b: Notify Stakeholders (Pending Status)**
- System generates notifications to:
  - PPMP Manager: PPMP added to waiting list
  - Requestor: PPMP pending due to budget constraints
  - Principal: PPMP pending due to budget constraints

**END OF PROCESS FOR UNAVAILABLE BUDGET**

---

### PHASE 5: PRINCIPAL APPROVAL - PPMP

#### 5.1 Principal Activities

**Step 5.1.1: View Pending PPMP**
- Principal receives notification of PPMP requiring approval
- Principal accesses PPMP details including:
  - Item specifications
  - Budget allocation status
  - Justification
  - Requestor information
- Principal reviews all relevant documentation

**Step 5.1.2: Approve/Reject PPMP (Decision Point)**

**DECISION: Is PPMP Approved?**

##### PATH A: PPMP Approved (Yes)

**Step 5.1.3a: Approve PPMP**
- Principal approves the PPMP
- Principal may add remarks or comments
- System records approval with:
  - Approval date and time
  - Principal signature/authorization
  - Approval remarks
- PPMP status is updated to "Approved"
- Approved PPMP is linked to the supply request

**Step 5.1.4a: Notify Stakeholders (Approval)**
- System generates notifications to:
  - PPMP Manager: PPMP approved by Principal
  - Requestor: PPMP approved - can proceed with Purchase Request
  - Budget Officer: PPMP approved

**Step 5.1.5a: Enable Purchase Request Creation**
- System enables Purchase Request creation for the Requestor
- Process continues to Phase 6

##### PATH B: PPMP Rejected (No)

**Step 5.1.3b: Reject PPMP**
- Principal rejects the PPMP
- Principal provides rejection reason/remarks
- System records rejection with:
  - Rejection date and time
  - Principal remarks
  - Rejection reason
- PPMP status is updated to "Rejected"

**Step 5.1.4b: Pending PPMP - Waiting List**
- Rejected PPMP may be placed on waiting list for next year budget
- System updates status to "Pending - Waiting List for Next Year Budget"

**Step 5.1.5b: Notify Stakeholders (Rejection)**
- System generates notifications to:
  - PPMP Manager: PPMP rejected by Principal
  - Requestor: PPMP rejected - see remarks
  - Budget Officer: PPMP rejected

**END OF PROCESS FOR REJECTED PPMP**

---

### PHASE 6: PURCHASE REQUEST CREATION

#### 6.1 Requestor Activities (After PPMP Approval)

**Step 6.1.1: Receive PPMP Approval Notification**
- Requestor receives notification that PPMP has been approved
- Requestor can now proceed with Purchase Request creation

**Step 6.1.2: Create Purchase Request**
- Requestor accesses Purchase Request creation form from their dashboard (Supply Request Management System)
- **Note**: PR creation functionality is integrated into the requestor's account/dashboard - accessible to all requestors from any department
- System pre-fills information from:
  - Original supply request
  - Approved PPMP
- Requestor completes Purchase Request form with:
  - Vendor/supplier information (if known)
  - Item specifications
  - Quantity
  - Estimated unit cost
  - Total estimated cost
  - Delivery requirements
  - Other procurement details
- System validates PPMP linkage and ensures PPMP is approved

**Step 6.1.3: Submit Purchase Request**
- Requestor submits the Purchase Request
- System assigns unique Purchase Request tracking number
- Purchase Request is linked to:
  - Original supply request
  - Approved PPMP
  - Budget allocation
- Purchase Request status is updated to "Pending Principal Approval"
- System routes Purchase Request to Principal
- Requestor receives submission confirmation

---

### PHASE 7: PRINCIPAL APPROVAL - PURCHASE REQUEST

#### 7.1 Principal Activities

**Step 7.1.1: Receive Purchase Request**
- Principal receives notification of Purchase Request requiring approval
- Principal accesses Purchase Request details including:
  - Original supply request information
  - Approved PPMP details
  - Budget allocation status
  - Item specifications
  - Estimated costs
  - Requestor information

**Step 7.1.2: Review Purchase Request**
- Principal reviews Purchase Request against approved PPMP
- Principal verifies compliance with procurement policies
- Principal may request additional information if needed

**Step 7.1.3: Approve/Reject Purchase Request (Decision Point)**

**DECISION: Is Purchase Request Approved?**

##### PATH A: Purchase Request Approved (Yes)

**Step 7.1.4a: Approve Purchase Request**
- Principal approves the Purchase Request
- Principal may add remarks or conditions
- System records approval with:
  - Approval date and time
  - Principal signature/authorization
  - Approval remarks
- Purchase Request status is updated to "Approved - Forward to Procurement"

**Step 7.1.5a: Forward to Procurement Office**
- System routes approved Purchase Request to Procurement Office
- Purchase Request status is updated to "In Procurement"
- Principal receives confirmation of forwarding

**Step 7.1.6a: Notify Stakeholders (Approval)**
- System generates notifications to:
  - Requestor: Purchase Request approved and forwarded to Procurement
  - PPMP Manager: Purchase Request approved
  - Budget Officer: Purchase Request approved
  - Procurement Office: New Purchase Request received

**PROCESS CONTINUES TO PROCUREMENT PHASE**
*(Note: Procurement phase documentation is in development)*

##### PATH B: Purchase Request Rejected (No)

**Step 7.1.4b: Reject Purchase Request**
- Principal rejects the Purchase Request
- Principal provides rejection reason/remarks
- System records rejection with:
  - Rejection date and time
  - Principal remarks
  - Rejection reason
- Purchase Request status is updated to "Rejected"

**Step 7.1.5b: Notify Stakeholders (Rejection)**
- System generates notifications to:
  - Requestor: Purchase Request rejected - see remarks
  - PPMP Manager: Purchase Request rejected
  - Budget Officer: Purchase Request rejected

**END OF PROCESS FOR REJECTED PURCHASE REQUEST**

---

## PROCESS SUMMARY - DECISION POINTS

### Key Decision Points and Outcomes:

1. **Item Availability Check (Supply Officer)**
   - **Available**: Process ends after RIS creation and item issuance
   - **Not Available**: Proceeds to PPMP creation process

2. **Budget Availability Check (Budget Officer)**
   - **Budget Available**: Proceeds to Principal approval
   - **Budget Not Available**: PPMP placed on waiting list for next year budget

3. **PPMP Approval (Principal)**
   - **Approved**: Enables Purchase Request creation
   - **Rejected**: PPMP placed on waiting list or process ends

4. **Purchase Request Approval (Principal)**
   - **Approved**: Forwarded to Procurement Office (continues to procurement phase)
   - **Rejected**: Process ends, Purchase Request rejected

---

## NOTIFICATION SYSTEM

Throughout the process, the system generates automated notifications to relevant stakeholders at key milestones:

- **Request Submission**: Notifies Supply Officer
- **Inventory Check Results**: Notifies Requestor
- **PPMP Submission**: Notifies Budget Officer
- **Budget Allocation**: Notifies PPMP Manager, Requestor, Principal
- **Budget Unavailable**: Notifies PPMP Manager, Requestor, Principal
- **PPMP Approval**: Notifies PPMP Manager, Requestor, Budget Officer
- **PPMP Rejection**: Notifies PPMP Manager, Requestor, Budget Officer
- **Purchase Request Submission**: Notifies Principal
- **Purchase Request Approval**: Notifies Requestor, PPMP Manager, Budget Officer, Procurement Office
- **Purchase Request Rejection**: Notifies Requestor, PPMP Manager, Budget Officer

---

## PROCESS END POINTS

The process can end at several points:

1. **Item Available**: After RIS creation and item issuance (Successful completion)
2. **Budget Unavailable**: PPMP placed on waiting list (Pending status)
3. **PPMP Rejected**: PPMP rejected by Principal (Terminated)
4. **Purchase Request Rejected**: Purchase Request rejected by Principal (Terminated)
5. **Forwarded to Procurement**: Purchase Request approved and forwarded (Continues to procurement phase)

---

## NEXT PHASE: PROCUREMENT PROCESS

**Note**: After Purchase Request is forwarded to Procurement Office, the process continues with:

1. Procurement Office activities (procurement execution)
2. Purchase Order (PO) creation
3. Supplier management
4. Delivery and receipt
5. Disbursement Voucher (DV) preparation
6. Payment processing

*Detailed documentation for the procurement phase and subsequent processes is to be developed separately.*

---

---

## HOW THIS PROCESS FLOW RELATES TO THE SYSTEMS

This process flow document describes **HOW** users interact with the systems listed in [MODULES_LIST.md](./MODULES_LIST.md). The relationship is:

- **MODULES_LIST.md** = **WHAT** systems are available and what functionalities they provide
- **PROCESS_FLOW.md** = **HOW** users use those systems step-by-step in the actual workflow

### System-to-Process Mapping:

1. **Supply Request Management System** → Used in Phase 1 (Requestor creates supply request) and Phase 6 (Requestor creates PR after PPMP approval - PR creation is integrated into requestor dashboard)
2. **Supply Inventory Management System** → Used in Phase 2 (Supply Officer checks inventory)
3. **PPMP Management System** → Used in Phase 3 (PPMP Manager creates PPMP)
4. **Budgeting & Accounting System** → Used in Phase 4 (Budget Officer reviews budget)
5. **Approval & Authorization System** → Used in Phases 5 & 7 (Principal approves PPMP and PR)
6. **Purchase Request Management & Administration System** → Used by PR Office staff for administrative oversight (not used by requestors in Phase 6, as PR creation is integrated into Supply Request Management System)
7. **Procurement Management System** → Used after Phase 7 (When PR is forwarded to Procurement)

---

## RELATED DOCUMENTATION

- [MODULES_LIST.md](./MODULES_LIST.md) - List of integrated systems and their functions
- System User Manuals (per system)
- System API Documentation
- Audit Trail Documentation

