# Database Structure & Schema Documentation

This document defines the logical order, structure, relationships, and application usage of all database tables in the application.

## 1. Foundational Tables
- `users`: User authentication, roles (`super_admin`, `coo`, `hod`, `project_manager`, `reception`, `others`), staff designations, and suspension status.
- `profiles`: Staff profile metadata, bio, avatar, and employee numbers.
- `donors` / Agencies: Financial sponsors and donor organization records.
- `contractors`: Registered contractors and engineers.
- `clusters`: Regional groupings and administrative clusters.
- `themes` & `subthemes`: Categorization hierarchy for foundation projects and aid programmes.

## 2. Core Entity Tables
- `applications`: Central intake repository for all submitted beneficiary applications.
- **Category Application Tables**: `education_center_applications`, `cultural_center_applications`, `drinking_water_group_applications`, `drinking_water_individual_applications`, `family_aid_applications`, `general_applications`, `hospital_clinics_applications`, `house_applications`, `orphan_care_applications`, `shops_others_applications`, `differently_abled_applications`.
- `projects`: Primary project master record linked to approved applications.
- **Category Project Tables**: `education_center_projects`, `cultural_center_projects`, `drinking_water_group_projects`, `drinking_water_individual_projects`, `family_aid_projects`, `general_projects`, `hospital_clinics_projects`, `house_projects`, `orphan_care_projects`, `shops_others_projects`, `differently_abled_projects`, `social_aid_projects`.

## 3. Transactional & Sub-Entity Tables
- `applicant_addresses`: Standardized physical and contact address details for applicants.
- `project_documents`: File attachments, location maps, certificates, and tax receipts.
- `project_photos`: Progress media, initial site photos, construction phase, and completion photos.
- `project_statuses`: Workflow stage history, Stage 1 to 6 approval states, HOD/COO approval remarks.
- `project_expenses`: Material and communication expenses recorded during construction.
- `project_inspections` & `project_site_studies`: Site evaluation and feasibility study records.
- `leave_types`: Configured leave categories (CL, SL, LSL, ML, MTL, PTL, PIL).
- `leave_balances`: User-specific leave allocations, used days, carried forward days, and balance days.
- `leave_requests`: Staff leave request submissions, dates, status (`Pending`, `Approved`, `Rejected`), and manager action tracking.

## 4. Audit & Queue Logs
- `leave_accrual_log`: Idempotent log tracking monthly leave accrual job runs.
- `notifications` & `notification_recipients`: System-wide notification dispatch records.
- `jobs`, `failed_jobs`, `cache`, `sessions`: Laravel core infrastructure tables.
