-- ============================================================================
-- DOCUMENT TRACKING SYSTEM (DTS) - DATABASE SCHEMA
-- Magallanes National High School
-- Database: MySQL/MariaDB
-- ============================================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS dts_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dts_db;

-- ============================================================================
-- 1. USER MANAGEMENT AND AUTHENTICATION TABLES
-- ============================================================================

-- Offices/Departments
CREATE TABLE offices (
    office_id INT AUTO_INCREMENT PRIMARY KEY,
    office_code VARCHAR(50) UNIQUE NOT NULL,
    office_name VARCHAR(255) NOT NULL,
    office_type ENUM('Supply Office', 'Budget Office', 'Accounting Office', 'Procurement Office', 'Principal Office', 'Bookkeeper Office', 'Payment Office', 'BAC Secretariat', 'IAC', 'Other') NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_office_code (office_code),
    INDEX idx_office_type (office_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles
CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_code VARCHAR(50) UNIQUE NOT NULL,
    role_name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role_code (role_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    employee_id VARCHAR(50),
    role_id INT NOT NULL,
    office_id INT,
    department VARCHAR(255),
    position VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE RESTRICT,
    FOREIGN KEY (office_id) REFERENCES offices(office_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_username (username),
    INDEX idx_role_id (role_id),
    INDEX idx_office_id (office_id),
    INDEX idx_employee_id (employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Sessions (Optional - for session management)
CREATE TABLE user_sessions (
    session_id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. INVENTORY MANAGEMENT TABLES
-- ============================================================================

-- Inventory Items (Master Data)
CREATE TABLE inventory_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_code VARCHAR(100) UNIQUE NOT NULL,
    item_description TEXT NOT NULL,
    category VARCHAR(255),
    unit_of_measure VARCHAR(50) NOT NULL,
    standard_unit_price DECIMAL(15, 2),
    reorder_level INT DEFAULT 0,
    reorder_quantity INT DEFAULT 0,
    stock_on_hand INT DEFAULT 0,
    location VARCHAR(255),
    notes TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_item_code (item_code),
    INDEX idx_category (category),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inventory Movements (Transaction History)
CREATE TABLE inventory_movements (
    movement_id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    movement_type ENUM('IN', 'OUT', 'ADJUSTMENT', 'RETURN') NOT NULL,
    quantity INT NOT NULL,
    reference_type ENUM('RIS', 'PR', 'PO', 'ADJUSTMENT', 'RETURN') NULL,
    reference_id INT NULL,
    stock_before INT NOT NULL,
    stock_after INT NOT NULL,
    unit_price DECIMAL(15, 2),
    notes TEXT,
    movement_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT NOT NULL,
    FOREIGN KEY (item_id) REFERENCES inventory_items(item_id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_item_id (item_id),
    INDEX idx_movement_type (movement_type),
    INDEX idx_movement_date (movement_date),
    INDEX idx_reference (reference_type, reference_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. SUPPLY REQUEST TABLES
-- ============================================================================

-- Supply Requests
CREATE TABLE supply_requests (
    supply_request_id INT AUTO_INCREMENT PRIMARY KEY,
    tracking_id VARCHAR(50) UNIQUE NOT NULL,
    requester_id INT NOT NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    priority ENUM('Normal', 'High', 'Urgent') DEFAULT 'Normal',
    justification TEXT NOT NULL,
    expected_delivery_date DATE,
    status ENUM('Submitted', 'Available', 'Not Available', 'Pending PPMP', 'For Approval', 'Approved', 'Rejected', 'Pending Budget', 'Under Procurement', 'DV Processing', 'Paid', 'Completed') DEFAULT 'Submitted',
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (requester_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_tracking_id (tracking_id),
    INDEX idx_requester_id (requester_id),
    INDEX idx_status (status),
    INDEX idx_request_date (request_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Supply Request Items
CREATE TABLE supply_request_items (
    request_item_id INT AUTO_INCREMENT PRIMARY KEY,
    supply_request_id INT NOT NULL,
    item_description TEXT NOT NULL,
    quantity INT NOT NULL,
    unit_of_measure VARCHAR(50) NOT NULL,
    specifications TEXT,
    FOREIGN KEY (supply_request_id) REFERENCES supply_requests(supply_request_id) ON DELETE CASCADE,
    INDEX idx_supply_request_id (supply_request_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. PPMP (PROJECT PROCUREMENT MANAGEMENT PLAN) TABLES
-- ============================================================================

-- PPMP Master
CREATE TABLE ppmp (
    ppmp_id INT AUTO_INCREMENT PRIMARY KEY,
    ppmp_number VARCHAR(100) UNIQUE NOT NULL,
    fiscal_year YEAR NOT NULL,
    end_user_unit VARCHAR(255),
    project_programs_activities TEXT,
    fund_source VARCHAR(255),
    total_budget DECIMAL(15, 2) DEFAULT 0.00,
    status ENUM('Draft', 'Submitted', 'Approved', 'Active', 'Amended', 'Closed') DEFAULT 'Draft',
    approval_date DATE,
    approved_by INT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_ppmp_number (ppmp_number),
    INDEX idx_fiscal_year (fiscal_year),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- PPMP Items
CREATE TABLE ppmp_items (
    ppmp_item_id INT AUTO_INCREMENT PRIMARY KEY,
    ppmp_id INT NOT NULL,
    item_code VARCHAR(100),
    general_description TEXT NOT NULL,
    quantity_size VARCHAR(255),
    unit_of_measure VARCHAR(50) NOT NULL,
    unit_price DECIMAL(15, 2) NOT NULL,
    estimated_budget DECIMAL(15, 2) NOT NULL,
    mode_of_procurement VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ppmp_id) REFERENCES ppmp(ppmp_id) ON DELETE CASCADE,
    INDEX idx_ppmp_id (ppmp_id),
    INDEX idx_item_code (item_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- PPMP Item Schedules (Monthly Schedule)
CREATE TABLE ppmp_item_schedules (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    ppmp_item_id INT NOT NULL,
    month_number TINYINT NOT NULL CHECK (month_number >= 1 AND month_number <= 12),
    planned_quantity INT DEFAULT 0,
    planned_amount DECIMAL(15, 2) DEFAULT 0.00,
    FOREIGN KEY (ppmp_item_id) REFERENCES ppmp_items(ppmp_item_id) ON DELETE CASCADE,
    UNIQUE KEY unique_ppmp_item_month (ppmp_item_id, month_number),
    INDEX idx_ppmp_item_id (ppmp_item_id),
    INDEX idx_month_number (month_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- PPMP Amendments
CREATE TABLE ppmp_amendments (
    amendment_id INT AUTO_INCREMENT PRIMARY KEY,
    ppmp_id INT NOT NULL,
    amendment_number VARCHAR(100) NOT NULL,
    amendment_date DATE NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('Draft', 'Submitted', 'Approved', 'Rejected') DEFAULT 'Draft',
    approved_by INT,
    approval_date DATE,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ppmp_id) REFERENCES ppmp(ppmp_id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_ppmp_id (ppmp_id),
    INDEX idx_amendment_number (amendment_number),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Annual Procurement Plan (APP) - Consolidated PPMPs
CREATE TABLE app (
    app_id INT AUTO_INCREMENT PRIMARY KEY,
    app_number VARCHAR(100) UNIQUE NOT NULL,
    fiscal_year YEAR NOT NULL,
    total_budget DECIMAL(15, 2) DEFAULT 0.00,
    status ENUM('Draft', 'Submitted', 'Approved', 'Active') DEFAULT 'Draft',
    approval_date DATE,
    approved_by INT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_app_number (app_number),
    INDEX idx_fiscal_year (fiscal_year),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- APP-PPMP Relationship
CREATE TABLE app_ppmp (
    app_ppmp_id INT AUTO_INCREMENT PRIMARY KEY,
    app_id INT NOT NULL,
    ppmp_id INT NOT NULL,
    FOREIGN KEY (app_id) REFERENCES app(app_id) ON DELETE CASCADE,
    FOREIGN KEY (ppmp_id) REFERENCES ppmp(ppmp_id) ON DELETE CASCADE,
    UNIQUE KEY unique_app_ppmp (app_id, ppmp_id),
    INDEX idx_app_id (app_id),
    INDEX idx_ppmp_id (ppmp_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. PURCHASE REQUEST (PR) TABLES
-- ============================================================================

-- Purchase Requests
CREATE TABLE purchase_requests (
    pr_id INT AUTO_INCREMENT PRIMARY KEY,
    pr_number VARCHAR(100) UNIQUE NOT NULL,
    tracking_id VARCHAR(50),
    supply_request_id INT,
    ppmp_id INT,
    app_id INT,
    requestor_id INT NOT NULL,
    purpose TEXT NOT NULL,
    total_amount DECIMAL(15, 2) DEFAULT 0.00,
    required_delivery_date DATE,
    status ENUM('Draft', 'PPMP Validated', 'For Approval', 'Approved', 'Rejected', 'Pending Budget', 'Budget Cleared', 'ORS Created', 'Under Procurement', 'Completed', 'Cancelled') DEFAULT 'Draft',
    ppmp_validation_status ENUM('Pending', 'Validated', 'Not In PPMP', 'Pending Amendment') DEFAULT 'Pending',
    ppmp_validation_notes TEXT,
    rejection_reason TEXT,
    rejected_by INT,
    rejected_at TIMESTAMP NULL,
    approved_by INT,
    approved_at TIMESTAMP NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supply_request_id) REFERENCES supply_requests(supply_request_id) ON DELETE SET NULL,
    FOREIGN KEY (ppmp_id) REFERENCES ppmp(ppmp_id) ON DELETE SET NULL,
    FOREIGN KEY (app_id) REFERENCES app(app_id) ON DELETE SET NULL,
    FOREIGN KEY (requestor_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (rejected_by) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_pr_number (pr_number),
    INDEX idx_tracking_id (tracking_id),
    INDEX idx_supply_request_id (supply_request_id),
    INDEX idx_status (status),
    INDEX idx_ppmp_validation_status (ppmp_validation_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Purchase Request Items
CREATE TABLE purchase_request_items (
    pr_item_id INT AUTO_INCREMENT PRIMARY KEY,
    pr_id INT NOT NULL,
    item_description TEXT NOT NULL,
    quantity INT NOT NULL,
    unit_of_measure VARCHAR(50) NOT NULL,
    unit_price DECIMAL(15, 2) NOT NULL,
    total_amount DECIMAL(15, 2) NOT NULL,
    specifications TEXT,
    ppmp_item_id INT,
    FOREIGN KEY (pr_id) REFERENCES purchase_requests(pr_id) ON DELETE CASCADE,
    FOREIGN KEY (ppmp_item_id) REFERENCES ppmp_items(ppmp_item_id) ON DELETE SET NULL,
    INDEX idx_pr_id (pr_id),
    INDEX idx_ppmp_item_id (ppmp_item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. REQUISITION AND ISSUE SLIP (RIS) TABLES
-- ============================================================================

-- Requisition and Issue Slips
CREATE TABLE ris (
    ris_id INT AUTO_INCREMENT PRIMARY KEY,
    ris_number VARCHAR(100) UNIQUE NOT NULL,
    supply_request_id INT,
    requester_id INT NOT NULL,
    issued_to_id INT NOT NULL,
    issue_date DATE NOT NULL,
    total_amount DECIMAL(15, 2) DEFAULT 0.00,
    status ENUM('Draft', 'Generated', 'Issued', 'Received', 'Forwarded to Accounting', 'Completed') DEFAULT 'Draft',
    acknowledged_at TIMESTAMP NULL,
    forwarded_to_accounting_at TIMESTAMP NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supply_request_id) REFERENCES supply_requests(supply_request_id) ON DELETE SET NULL,
    FOREIGN KEY (requester_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (issued_to_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_ris_number (ris_number),
    INDEX idx_supply_request_id (supply_request_id),
    INDEX idx_status (status),
    INDEX idx_issue_date (issue_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- RIS Items
CREATE TABLE ris_items (
    ris_item_id INT AUTO_INCREMENT PRIMARY KEY,
    ris_id INT NOT NULL,
    inventory_item_id INT,
    stock_number VARCHAR(100),
    item_description TEXT NOT NULL,
    quantity INT NOT NULL,
    unit_of_measure VARCHAR(50) NOT NULL,
    unit_price DECIMAL(15, 2) NOT NULL,
    total_amount DECIMAL(15, 2) NOT NULL,
    FOREIGN KEY (ris_id) REFERENCES ris(ris_id) ON DELETE CASCADE,
    FOREIGN KEY (inventory_item_id) REFERENCES inventory_items(item_id) ON DELETE SET NULL,
    INDEX idx_ris_id (ris_id),
    INDEX idx_inventory_item_id (inventory_item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. OBLIGATION REQUEST STATUS (ORS) TABLES
-- ============================================================================

-- Obligation Request Status
CREATE TABLE ors (
    ors_id INT AUTO_INCREMENT PRIMARY KEY,
    ors_number VARCHAR(100) UNIQUE NOT NULL,
    pr_id INT,
    ris_id INT,
    budget_allocation_id INT,
    total_amount DECIMAL(15, 2) NOT NULL,
    fund_source VARCHAR(255),
    status ENUM('Draft', 'In Progress', 'Completed', 'Forwarded to Procurement') DEFAULT 'Draft',
    checklist_completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL,
    forwarded_to_procurement_at TIMESTAMP NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pr_id) REFERENCES purchase_requests(pr_id) ON DELETE SET NULL,
    FOREIGN KEY (ris_id) REFERENCES ris(ris_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_ors_number (ors_number),
    INDEX idx_pr_id (pr_id),
    INDEX idx_ris_id (ris_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ORS Checklist Items
CREATE TABLE ors_checklist_items (
    checklist_item_id INT AUTO_INCREMENT PRIMARY KEY,
    ors_id INT NOT NULL,
    checklist_item VARCHAR(255) NOT NULL,
    is_checked BOOLEAN DEFAULT FALSE,
    checked_at TIMESTAMP NULL,
    checked_by INT,
    notes TEXT,
    FOREIGN KEY (ors_id) REFERENCES ors(ors_id) ON DELETE CASCADE,
    FOREIGN KEY (checked_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_ors_id (ors_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8. BUDGET MANAGEMENT TABLES
-- ============================================================================

-- Budget Allocations
CREATE TABLE budget_allocations (
    allocation_id INT AUTO_INCREMENT PRIMARY KEY,
    fiscal_year YEAR NOT NULL,
    fund_source VARCHAR(255) NOT NULL,
    category VARCHAR(255),
    allocated_amount DECIMAL(15, 2) NOT NULL,
    obligated_amount DECIMAL(15, 2) DEFAULT 0.00,
    available_amount DECIMAL(15, 2) GENERATED ALWAYS AS (allocated_amount - obligated_amount) STORED,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_fiscal_year (fiscal_year),
    INDEX idx_fund_source (fund_source)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Budget Reservations
CREATE TABLE budget_reservations (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    allocation_id INT NOT NULL,
    reference_type ENUM('PR', 'RIS', 'ORS') NOT NULL,
    reference_id INT NOT NULL,
    reserved_amount DECIMAL(15, 2) NOT NULL,
    status ENUM('Reserved', 'Obligated', 'Released', 'Cancelled') DEFAULT 'Reserved',
    reserved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    obligated_at TIMESTAMP NULL,
    released_at TIMESTAMP NULL,
    reserved_by INT NOT NULL,
    FOREIGN KEY (allocation_id) REFERENCES budget_allocations(allocation_id) ON DELETE RESTRICT,
    FOREIGN KEY (reserved_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_allocation_id (allocation_id),
    INDEX idx_reference (reference_type, reference_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Budget Releases
CREATE TABLE budget_releases (
    release_id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    dv_id INT,
    release_amount DECIMAL(15, 2) NOT NULL,
    release_date DATE NOT NULL,
    released_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reservation_id) REFERENCES budget_reservations(reservation_id) ON DELETE RESTRICT,
    FOREIGN KEY (released_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_reservation_id (reservation_id),
    INDEX idx_dv_id (dv_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 9. SUPPLIER MANAGEMENT TABLES
-- ============================================================================

-- Suppliers
CREATE TABLE suppliers (
    supplier_id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_code VARCHAR(100) UNIQUE NOT NULL,
    supplier_name VARCHAR(255) NOT NULL,
    business_name VARCHAR(255),
    tax_id_number VARCHAR(100),
    contact_person VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_supplier_code (supplier_code),
    INDEX idx_supplier_name (supplier_name),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 10. PROCUREMENT TABLES
-- ============================================================================

-- Purchase Orders
CREATE TABLE purchase_orders (
    po_id INT AUTO_INCREMENT PRIMARY KEY,
    po_number VARCHAR(100) UNIQUE NOT NULL,
    pr_id INT NOT NULL,
    ors_id INT NOT NULL,
    supplier_id INT NOT NULL,
    procurement_method ENUM('Shopping', 'RFQ', 'Canvas', 'Competitive Bidding', 'Negotiated Procurement', 'Small Value Procurement') NOT NULL,
    total_amount DECIMAL(15, 2) NOT NULL,
    delivery_date DATE,
    delivery_address TEXT,
    payment_terms TEXT,
    status ENUM('Draft', 'Issued', 'Acknowledged', 'Delivered', 'Inspection', 'Accepted', 'Rejected', 'Cancelled') DEFAULT 'Draft',
    issued_date DATE,
    acknowledged_date DATE,
    delivered_date DATE,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pr_id) REFERENCES purchase_requests(pr_id) ON DELETE RESTRICT,
    FOREIGN KEY (ors_id) REFERENCES ors(ors_id) ON DELETE RESTRICT,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_po_number (po_number),
    INDEX idx_pr_id (pr_id),
    INDEX idx_ors_id (ors_id),
    INDEX idx_supplier_id (supplier_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Purchase Order Items
CREATE TABLE purchase_order_items (
    po_item_id INT AUTO_INCREMENT PRIMARY KEY,
    po_id INT NOT NULL,
    pr_item_id INT,
    item_description TEXT NOT NULL,
    quantity INT NOT NULL,
    unit_of_measure VARCHAR(50) NOT NULL,
    unit_price DECIMAL(15, 2) NOT NULL,
    total_amount DECIMAL(15, 2) NOT NULL,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(po_id) ON DELETE CASCADE,
    FOREIGN KEY (pr_item_id) REFERENCES purchase_request_items(pr_item_id) ON DELETE SET NULL,
    INDEX idx_po_id (po_id),
    INDEX idx_pr_item_id (pr_item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Quotations
CREATE TABLE quotations (
    quotation_id INT AUTO_INCREMENT PRIMARY KEY,
    po_id INT,
    supplier_id INT NOT NULL,
    quotation_number VARCHAR(100),
    quotation_date DATE,
    total_amount DECIMAL(15, 2) NOT NULL,
    validity_date DATE,
    file_path VARCHAR(500),
    is_selected BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(po_id) ON DELETE SET NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE RESTRICT,
    INDEX idx_po_id (po_id),
    INDEX idx_supplier_id (supplier_id),
    INDEX idx_is_selected (is_selected)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Procurement Checklists
CREATE TABLE procurement_checklists (
    checklist_id INT AUTO_INCREMENT PRIMARY KEY,
    po_id INT NOT NULL,
    checklist_item VARCHAR(255) NOT NULL,
    is_checked BOOLEAN DEFAULT FALSE,
    checked_at TIMESTAMP NULL,
    checked_by INT,
    notes TEXT,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(po_id) ON DELETE CASCADE,
    FOREIGN KEY (checked_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_po_id (po_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inspection and Acceptance Reports (IAR)
CREATE TABLE inspection_acceptance_reports (
    iar_id INT AUTO_INCREMENT PRIMARY KEY,
    iar_number VARCHAR(100) UNIQUE NOT NULL,
    po_id INT NOT NULL,
    inspection_date DATE NOT NULL,
    inspection_committee TEXT,
    overall_status ENUM('Accepted', 'Partially Accepted', 'Rejected') NOT NULL,
    remarks TEXT,
    accepted_date DATE,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(po_id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_iar_number (iar_number),
    INDEX idx_po_id (po_id),
    INDEX idx_inspection_date (inspection_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- IAR Items
CREATE TABLE iar_items (
    iar_item_id INT AUTO_INCREMENT PRIMARY KEY,
    iar_id INT NOT NULL,
    po_item_id INT NOT NULL,
    quantity_inspected INT NOT NULL,
    quantity_accepted INT NOT NULL,
    quantity_rejected INT DEFAULT 0,
    acceptance_status ENUM('Accepted', 'Partially Accepted', 'Rejected') NOT NULL,
    remarks TEXT,
    FOREIGN KEY (iar_id) REFERENCES inspection_acceptance_reports(iar_id) ON DELETE CASCADE,
    FOREIGN KEY (po_item_id) REFERENCES purchase_order_items(po_item_id) ON DELETE RESTRICT,
    INDEX idx_iar_id (iar_id),
    INDEX idx_po_item_id (po_item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 11. DISBURSEMENT VOUCHER (DV) TABLES
-- ============================================================================

-- Disbursement Vouchers
CREATE TABLE disbursement_vouchers (
    dv_id INT AUTO_INCREMENT PRIMARY KEY,
    dv_number VARCHAR(100) UNIQUE NOT NULL,
    po_id INT,
    ors_id INT NOT NULL,
    supplier_id INT NOT NULL,
    total_amount DECIMAL(15, 2) NOT NULL,
    payment_mode ENUM('Cheque', 'Cash', 'Bank Transfer', 'Other') DEFAULT 'Cheque',
    status ENUM('Draft', 'For Approval', 'Approved', 'Budget Released', 'Payment Processing', 'Paid', 'Cancelled') DEFAULT 'Draft',
    submitted_for_approval_at TIMESTAMP NULL,
    approved_at TIMESTAMP NULL,
    approved_by INT,
    budget_released_at TIMESTAMP NULL,
    payment_processed_at TIMESTAMP NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(po_id) ON DELETE SET NULL,
    FOREIGN KEY (ors_id) REFERENCES ors(ors_id) ON DELETE RESTRICT,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE RESTRICT,
    FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_dv_number (dv_number),
    INDEX idx_po_id (po_id),
    INDEX idx_ors_id (ors_id),
    INDEX idx_supplier_id (supplier_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DV Items
CREATE TABLE dv_items (
    dv_item_id INT AUTO_INCREMENT PRIMARY KEY,
    dv_id INT NOT NULL,
    item_description TEXT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(15, 2) NOT NULL,
    total_amount DECIMAL(15, 2) NOT NULL,
    FOREIGN KEY (dv_id) REFERENCES disbursement_vouchers(dv_id) ON DELETE CASCADE,
    INDEX idx_dv_id (dv_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DV Document Links (Links DV to related documents)
CREATE TABLE dv_document_links (
    link_id INT AUTO_INCREMENT PRIMARY KEY,
    dv_id INT NOT NULL,
    document_type ENUM('PR', 'ORS', 'PO', 'IAR', 'Invoice', 'Other') NOT NULL,
    document_id INT NOT NULL,
    document_number VARCHAR(100),
    FOREIGN KEY (dv_id) REFERENCES disbursement_vouchers(dv_id) ON DELETE CASCADE,
    INDEX idx_dv_id (dv_id),
    INDEX idx_document (document_type, document_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 12. PAYMENT TABLES
-- ============================================================================

-- Cheques
CREATE TABLE cheques (
    cheque_id INT AUTO_INCREMENT PRIMARY KEY,
    cheque_number VARCHAR(100) UNIQUE NOT NULL,
    dv_id INT NOT NULL,
    payee_name VARCHAR(255) NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    cheque_date DATE NOT NULL,
    description TEXT,
    status ENUM('Generated', 'Signed', 'Issued', 'Cleared', 'Cancelled') DEFAULT 'Generated',
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    signed_at TIMESTAMP NULL,
    issued_at TIMESTAMP NULL,
    cleared_at TIMESTAMP NULL,
    issued_to VARCHAR(255),
    generated_by INT NOT NULL,
    FOREIGN KEY (dv_id) REFERENCES disbursement_vouchers(dv_id) ON DELETE RESTRICT,
    FOREIGN KEY (generated_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_cheque_number (cheque_number),
    INDEX idx_dv_id (dv_id),
    INDEX idx_status (status),
    INDEX idx_cheque_date (cheque_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    payment_number VARCHAR(100) UNIQUE NOT NULL,
    dv_id INT NOT NULL,
    cheque_id INT,
    payment_amount DECIMAL(15, 2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_mode ENUM('Cheque', 'Cash', 'Bank Transfer', 'Other') NOT NULL,
    status ENUM('Pending', 'Completed', 'Cancelled') DEFAULT 'Pending',
    payment_reference VARCHAR(255),
    completed_at TIMESTAMP NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dv_id) REFERENCES disbursement_vouchers(dv_id) ON DELETE RESTRICT,
    FOREIGN KEY (cheque_id) REFERENCES cheques(cheque_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_payment_number (payment_number),
    INDEX idx_dv_id (dv_id),
    INDEX idx_cheque_id (cheque_id),
    INDEX idx_status (status),
    INDEX idx_payment_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 13. DOCUMENT TRACKING AND AUDIT TABLES
-- ============================================================================

-- Document Tracking
CREATE TABLE document_tracking (
    tracking_id INT AUTO_INCREMENT PRIMARY KEY,
    document_type ENUM('Supply Request', 'PR', 'RIS', 'ORS', 'PO', 'DV', 'PPMP', 'APP', 'IAR', 'Payment') NOT NULL,
    document_id INT NOT NULL,
    document_number VARCHAR(100),
    current_status VARCHAR(100) NOT NULL,
    current_office_id INT,
    current_user_id INT,
    previous_status VARCHAR(100),
    remarks TEXT,
    tracked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tracked_by INT,
    FOREIGN KEY (current_office_id) REFERENCES offices(office_id) ON DELETE SET NULL,
    FOREIGN KEY (current_user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (tracked_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_document (document_type, document_id),
    INDEX idx_document_number (document_number),
    INDEX idx_current_status (current_status),
    INDEX idx_tracked_at (tracked_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit Logs
CREATE TABLE audit_logs (
    log_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    action_type VARCHAR(100) NOT NULL,
    table_name VARCHAR(100) NOT NULL,
    record_id INT NOT NULL,
    user_id INT,
    action_description TEXT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action_type (action_type),
    INDEX idx_table_record (table_name, record_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    notification_type VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    related_document_type VARCHAR(100),
    related_document_id INT,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at),
    INDEX idx_notification_type (notification_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Document Versions (for version history)
CREATE TABLE document_versions (
    version_id INT AUTO_INCREMENT PRIMARY KEY,
    document_type VARCHAR(100) NOT NULL,
    document_id INT NOT NULL,
    version_number INT NOT NULL,
    version_data JSON,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_document (document_type, document_id),
    INDEX idx_version_number (version_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 14. ADD FOREIGN KEY FOR BUDGET_RELEASES.dv_id
-- ============================================================================

ALTER TABLE budget_releases
ADD CONSTRAINT fk_budget_releases_dv
FOREIGN KEY (dv_id) REFERENCES disbursement_vouchers(dv_id) ON DELETE SET NULL;

-- ============================================================================
-- END OF SCHEMA
-- ============================================================================

