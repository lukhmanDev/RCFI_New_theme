# Database Audit Report (Phase 1)

Generated on: 2026-08-07 11:54:27

> [!IMPORTANT]
> **Phase 1 Audit Report**: This document lists all tables, columns, unused fields, duplicate columns, and risk classifications. **No database columns have been dropped or modified.** Review the proposed findings before approving Phase 2 removal.

## Executive Summary
- **Total Database Tables Audited**: 122
- **Total Database Columns Audited**: 1921
- **Candidate Unused Columns Flagged**: 263
- **Key Duplicate Columns Flagged for Consolidation**: 6

## 1. Duplicate Columns & Source of Truth Audit

| Table | Column | Duplicate Of (Source of Truth) | Risk Level | Action Proposed |
|---|---|---|---|---|
| `applications` | `applicant_email` | `users.email` | Medium | Replace with `$application->user->email` relationship |
| `applications` | `category` | Separate Category Tables | High | Deprecated legacy column, drop after model verification |
| `projects` | `category` | Separate Project Category Tables | High | Deprecated legacy column, drop after model verification |
| `projects` | `department_name` | `departments.name` | Medium | Consolidate to `department_id` relationship |
| `social_aid_projects` | `locality` | `applicant_addresses.locality` | Medium | Consolidate to `applicant_addresses` relation |
| `social_aid_funds` | `project_id` | `projects.id` | Low | Already deprecated in migration `2026_07_30_130000` |

## 2. Table-by-Table Column Audit

### Table: `finport.agencies`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `is_active` | `Keep` | Low | 14 | Referenced in 14 files: LeaveApiController.php, LeaveRequestController.php, AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php, LeaveAdminDashboard.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport.cache`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `key` | `Keep` | Low | 52 | Referenced in 52 files: AdminController.php, ApplicationController.php, ProjectController.php, HasCategoryMeta.php, HasProjectColumns.php |
| `value` | `Keep` | Low | 72 | Referenced in 72 files: AdminController.php, ApplicationController.php, LeaveRequestController.php, ProjectController.php, User.php |
| `expiration` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `finport.cache_locks`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `key` | `Keep` | Low | 52 | Referenced in 52 files: AdminController.php, ApplicationController.php, ProjectController.php, HasCategoryMeta.php, HasProjectColumns.php |
| `owner` | `Keep` | Low | 8 | Referenced in 8 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, ProjectDocument.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `expiration` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `finport.districts`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `state_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport.failed_jobs`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `uuid` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `connection` | `Keep` | Low | 3 | Referenced in 3 files: ProjectDocument.php, drinking_water_group.blade.php, admin.blade.php |
| `queue` | `Keep` | Low | 2 | Referenced in 2 files: leave-admin-dashboard.blade.php, role-dashboard.blade.php |
| `payload` | `Keep` | Low | 16 | Referenced in 16 files: EntityChanged.php, ProjectUpdated.php, ListensForEntityChanges.php, RoleDashboard.php, BroadcastsChanges.php |
| `exception` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `failed_at` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `finport.jobs`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `queue` | `Keep` | Low | 2 | Referenced in 2 files: leave-admin-dashboard.blade.php, role-dashboard.blade.php |
| `payload` | `Keep` | Low | 16 | Referenced in 16 files: EntityChanged.php, ProjectUpdated.php, ListensForEntityChanges.php, RoleDashboard.php, BroadcastsChanges.php |
| `attempts` | `Keep` | Low | 1 | Referenced in 1 files: ApplicationApprovalPermissionsTest.php |
| `reserved_at` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `available_at` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport.job_batches`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `total_jobs` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `pending_jobs` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `failed_jobs` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `failed_job_ids` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `options` | `Keep` | Low | 25 | Referenced in 25 files: ApplicationController.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `cancelled_at` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `finished_at` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `finport.migrations`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `migration` | `Keep` | Low | 1 | Referenced in 1 files: HasProjectColumns.php |
| `batch` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `finport.od_projects`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `bank_name` | `Keep` | Low | 5 | Referenced in 5 files: ProfileController.php, UserController.php, profile.blade.php, users.blade.php, UserManagementTest.php |
| `project_number` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `project_year` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `rcfi_project_number` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `theme_id` | `Keep` | Low | 15 | Referenced in 15 files: ThemeController.php, Subtheme.php, Theme.php, themes.blade.php, cultural_center.blade.php |
| `subtheme_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `activity` | `Keep` | Low | 24 | Referenced in 24 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `state_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `district_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `village` | `Keep` | Low | 53 | Referenced in 53 files: AdminController.php, ApplicationController.php, ClusterController.php, ProjectController.php, Cluster.php |
| `pincode` | `Keep` | Low | 16 | Referenced in 16 files: HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `sanctioned_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `leverage_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `passed_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `agency` | `Keep` | Low | 51 | Referenced in 51 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php |
| `currency` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `sanctioned_amount_inr` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `excess_paid` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `deduction` | `Keep` | Low | 17 | Referenced in 17 files: AdminController.php, ProjectController.php, ProjectCompletionDetail.php, HasProjectColumns.php, cultural_center.blade.php |
| `community_contribution` | `Keep` | Low | 28 | Referenced in 28 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `project_name` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `sponsor_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `leverage` | `Keep` | Low | 2 | Referenced in 2 files: AdminController.php, single_project.blade.php |
| `grant_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `grant_amount_foreign` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `donor_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `donor_amount_foreign` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `finport.password_reset_tokens`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `email` | `Keep` | Low | 36 | Referenced in 36 files: AdminController.php, ApplicationController.php, AuthController.php, ContractorController.php, DonorController.php |
| `token` | `Keep` | Low | 34 | Referenced in 34 files: AuthController.php, User.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport.projects`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `bank_name` | `Keep` | Low | 5 | Referenced in 5 files: ProfileController.php, UserController.php, profile.blade.php, users.blade.php, UserManagementTest.php |
| `project_number` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `project_year` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `rcfi_project_number` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `agency` | `Keep` | Low | 51 | Referenced in 51 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php |
| `theme_id` | `Keep` | Low | 15 | Referenced in 15 files: ThemeController.php, Subtheme.php, Theme.php, themes.blade.php, cultural_center.blade.php |
| `subtheme_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `activity` | `Keep` | Low | 24 | Referenced in 24 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `state_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `district_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `village` | `Keep` | Low | 53 | Referenced in 53 files: AdminController.php, ApplicationController.php, ClusterController.php, ProjectController.php, Cluster.php |
| `pincode` | `Keep` | Low | 16 | Referenced in 16 files: HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `sanctioned_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `currency` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `sanctioned_amount_inr` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `passed_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `leverage` | `Keep` | Low | 2 | Referenced in 2 files: AdminController.php, single_project.blade.php |
| `excess_paid` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `deduction` | `Keep` | Low | 17 | Referenced in 17 files: AdminController.php, ProjectController.php, ProjectCompletionDetail.php, HasProjectColumns.php, cultural_center.blade.php |
| `community_contribution` | `Keep` | Low | 28 | Referenced in 28 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `project_name` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `sponsor_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `leverage_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `grant_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `grant_amount_foreign` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `donor_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `donor_amount_foreign` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `finport.project_payments`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `receipt_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `date_received` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `amount_foreign` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `amount_inr` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_type` | `Keep` | Low | 39 | Referenced in 39 files: AdminController.php, ProjectController.php, CulturalCenterApplication.php, EducationCenterApplication.php, ProjectCommunityContribution.php |

### Table: `finport.project_transactions`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `mode_of_payment` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `batch_no` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `paying_now` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `cheque_no` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `transaction_date` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `bank_name` | `Keep` | Low | 5 | Referenced in 5 files: ProfileController.php, UserController.php, profile.blade.php, users.blade.php, UserManagementTest.php |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport.receipts`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `receipt_number` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `date_received` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `agency` | `Keep` | Low | 51 | Referenced in 51 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php |
| `mode_of_payment` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `amount_inr` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `currency` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `amount_foreign` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport.sessions`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `user_id` | `Keep` | Low | 36 | Referenced in 36 files: LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php, LeaveRequestRejected.php, LeaveRequestSubmitted.php |
| `ip_address` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `user_agent` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `payload` | `Keep` | Low | 16 | Referenced in 16 files: EntityChanged.php, ProjectUpdated.php, ListensForEntityChanges.php, RoleDashboard.php, BroadcastsChanges.php |
| `last_activity` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `finport.states`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport.subthemes`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `theme_id` | `Keep` | Low | 15 | Referenced in 15 files: ThemeController.php, Subtheme.php, Theme.php, themes.blade.php, cultural_center.blade.php |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport.themes`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport.transaction_details`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `transaction_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `project_type` | `Keep` | Low | 39 | Referenced in 39 files: AdminController.php, ProjectController.php, CulturalCenterApplication.php, EducationCenterApplication.php, ProjectCommunityContribution.php |
| `project_number` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `amount` | `Keep` | Low | 55 | Referenced in 55 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |

### Table: `finport.users`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `email` | `Keep` | Low | 36 | Referenced in 36 files: AdminController.php, ApplicationController.php, AuthController.php, ContractorController.php, DonorController.php |
| `password` | `Keep` | Low | 19 | Referenced in 19 files: AuthController.php, ProfileController.php, UserController.php, User.php, profile.blade.php |
| `role` | `Keep` | Low | 53 | Referenced in 53 files: ApplicationCreated.php, ApplicationController.php, AuthController.php, ClusterController.php, LeaveAdminController.php |
| `remember_token` | `Keep` | Low | 2 | Referenced in 2 files: User.php, UserFactory.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport_new.agencies`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `is_active` | `Keep` | Low | 14 | Referenced in 14 files: LeaveApiController.php, LeaveRequestController.php, AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php, LeaveAdminDashboard.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport_new.cache`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `key` | `Keep` | Low | 52 | Referenced in 52 files: AdminController.php, ApplicationController.php, ProjectController.php, HasCategoryMeta.php, HasProjectColumns.php |
| `value` | `Keep` | Low | 72 | Referenced in 72 files: AdminController.php, ApplicationController.php, LeaveRequestController.php, ProjectController.php, User.php |
| `expiration` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `finport_new.cache_locks`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `key` | `Keep` | Low | 52 | Referenced in 52 files: AdminController.php, ApplicationController.php, ProjectController.php, HasCategoryMeta.php, HasProjectColumns.php |
| `owner` | `Keep` | Low | 8 | Referenced in 8 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, ProjectDocument.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `expiration` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `finport_new.districts`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `state_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport_new.failed_jobs`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `uuid` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `connection` | `Keep` | Low | 3 | Referenced in 3 files: ProjectDocument.php, drinking_water_group.blade.php, admin.blade.php |
| `queue` | `Keep` | Low | 2 | Referenced in 2 files: leave-admin-dashboard.blade.php, role-dashboard.blade.php |
| `payload` | `Keep` | Low | 16 | Referenced in 16 files: EntityChanged.php, ProjectUpdated.php, ListensForEntityChanges.php, RoleDashboard.php, BroadcastsChanges.php |
| `exception` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `failed_at` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `finport_new.jobs`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `queue` | `Keep` | Low | 2 | Referenced in 2 files: leave-admin-dashboard.blade.php, role-dashboard.blade.php |
| `payload` | `Keep` | Low | 16 | Referenced in 16 files: EntityChanged.php, ProjectUpdated.php, ListensForEntityChanges.php, RoleDashboard.php, BroadcastsChanges.php |
| `attempts` | `Keep` | Low | 1 | Referenced in 1 files: ApplicationApprovalPermissionsTest.php |
| `reserved_at` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `available_at` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport_new.job_batches`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `total_jobs` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `pending_jobs` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `failed_jobs` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `failed_job_ids` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `options` | `Keep` | Low | 25 | Referenced in 25 files: ApplicationController.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `cancelled_at` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `finished_at` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `finport_new.landassets`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `file_no` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `document_no` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `thanda_per` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `address` | `Keep` | Low | 40 | Referenced in 40 files: AdminController.php, ApplicationController.php, ContractorController.php, ProfileController.php, ProjectController.php |
| `building` | `Keep` | Low | 24 | Referenced in 24 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, HouseApplication.php, ShopOtherApplication.php |
| `hector` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `are` | `Keep` | Low | 108 | Referenced in 108 files: AdminController.php, ApplicationController.php, LeaveRequestController.php, ProfileController.php, ProjectController.php |
| `sq_ft` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport_new.migrations`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `migration` | `Keep` | Low | 1 | Referenced in 1 files: HasProjectColumns.php |
| `batch` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `finport_new.od_projects`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `bank_name` | `Keep` | Low | 5 | Referenced in 5 files: ProfileController.php, UserController.php, profile.blade.php, users.blade.php, UserManagementTest.php |
| `project_number` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `project_year` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `rcfi_project_number` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `theme_id` | `Keep` | Low | 15 | Referenced in 15 files: ThemeController.php, Subtheme.php, Theme.php, themes.blade.php, cultural_center.blade.php |
| `subtheme_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `activity` | `Keep` | Low | 24 | Referenced in 24 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `state_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `district_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `village` | `Keep` | Low | 53 | Referenced in 53 files: AdminController.php, ApplicationController.php, ClusterController.php, ProjectController.php, Cluster.php |
| `pincode` | `Keep` | Low | 16 | Referenced in 16 files: HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `sanctioned_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `leverage_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `passed_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `agency` | `Keep` | Low | 51 | Referenced in 51 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php |
| `currency` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `sanctioned_amount_inr` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `excess_paid` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `deduction` | `Keep` | Low | 17 | Referenced in 17 files: AdminController.php, ProjectController.php, ProjectCompletionDetail.php, HasProjectColumns.php, cultural_center.blade.php |
| `community_contribution` | `Keep` | Low | 28 | Referenced in 28 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `project_name` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `sponsor_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `leverage` | `Keep` | Low | 2 | Referenced in 2 files: AdminController.php, single_project.blade.php |
| `grant_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `grant_amount_foreign` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `donor_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `donor_amount_foreign` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `finport_new.password_reset_tokens`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `email` | `Keep` | Low | 36 | Referenced in 36 files: AdminController.php, ApplicationController.php, AuthController.php, ContractorController.php, DonorController.php |
| `token` | `Keep` | Low | 34 | Referenced in 34 files: AuthController.php, User.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport_new.projects`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `bank_name` | `Keep` | Low | 5 | Referenced in 5 files: ProfileController.php, UserController.php, profile.blade.php, users.blade.php, UserManagementTest.php |
| `project_number` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `project_year` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `rcfi_project_number` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `agency` | `Keep` | Low | 51 | Referenced in 51 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php |
| `theme_id` | `Keep` | Low | 15 | Referenced in 15 files: ThemeController.php, Subtheme.php, Theme.php, themes.blade.php, cultural_center.blade.php |
| `subtheme_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `activity` | `Keep` | Low | 24 | Referenced in 24 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `state_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `district_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `village` | `Keep` | Low | 53 | Referenced in 53 files: AdminController.php, ApplicationController.php, ClusterController.php, ProjectController.php, Cluster.php |
| `pincode` | `Keep` | Low | 16 | Referenced in 16 files: HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `sanctioned_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `currency` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `sanctioned_amount_inr` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `passed_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `leverage` | `Keep` | Low | 2 | Referenced in 2 files: AdminController.php, single_project.blade.php |
| `excess_paid` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `deduction` | `Keep` | Low | 17 | Referenced in 17 files: AdminController.php, ProjectController.php, ProjectCompletionDetail.php, HasProjectColumns.php, cultural_center.blade.php |
| `community_contribution` | `Keep` | Low | 28 | Referenced in 28 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `project_name` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `sponsor_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `leverage_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `grant_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `grant_amount_foreign` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `donor_amount` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `donor_amount_foreign` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `finport_new.project_payments`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `receipt_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `date_received` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `amount_foreign` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `amount_inr` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_type` | `Keep` | Low | 39 | Referenced in 39 files: AdminController.php, ProjectController.php, CulturalCenterApplication.php, EducationCenterApplication.php, ProjectCommunityContribution.php |

### Table: `finport_new.project_transactions`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `mode_of_payment` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `batch_no` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `paying_now` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `cheque_no` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `transaction_date` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `bank_name` | `Keep` | Low | 5 | Referenced in 5 files: ProfileController.php, UserController.php, profile.blade.php, users.blade.php, UserManagementTest.php |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport_new.receipts`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `receipt_number` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `date_received` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `agency` | `Keep` | Low | 51 | Referenced in 51 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php |
| `mode_of_payment` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `amount_inr` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `currency` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `amount_foreign` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport_new.sessions`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `user_id` | `Keep` | Low | 36 | Referenced in 36 files: LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php, LeaveRequestRejected.php, LeaveRequestSubmitted.php |
| `ip_address` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `user_agent` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `payload` | `Keep` | Low | 16 | Referenced in 16 files: EntityChanged.php, ProjectUpdated.php, ListensForEntityChanges.php, RoleDashboard.php, BroadcastsChanges.php |
| `last_activity` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `finport_new.states`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport_new.subthemes`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `theme_id` | `Keep` | Low | 15 | Referenced in 15 files: ThemeController.php, Subtheme.php, Theme.php, themes.blade.php, cultural_center.blade.php |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport_new.themes`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport_new.transaction_details`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `transaction_id` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `project_type` | `Keep` | Low | 39 | Referenced in 39 files: AdminController.php, ProjectController.php, CulturalCenterApplication.php, EducationCenterApplication.php, ProjectCommunityContribution.php |
| `project_number` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `amount` | `Keep` | Low | 55 | Referenced in 55 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |

### Table: `finport_new.users`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `email` | `Keep` | Low | 36 | Referenced in 36 files: AdminController.php, ApplicationController.php, AuthController.php, ContractorController.php, DonorController.php |
| `password` | `Keep` | Low | 19 | Referenced in 19 files: AuthController.php, ProfileController.php, UserController.php, User.php, profile.blade.php |
| `role` | `Keep` | Low | 53 | Referenced in 53 files: ApplicationCreated.php, ApplicationController.php, AuthController.php, ClusterController.php, LeaveAdminController.php |
| `remember_token` | `Keep` | Low | 2 | Referenced in 2 files: User.php, UserFactory.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `finport_new.vehicle_assets`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `file_no` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `rc_no` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `engine_no` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `chassis_no` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `makers_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `model_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `fuel_type` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `date_of_purchase` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `date_of_sale` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `ownership` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `previous_owner_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `previous_owner_contact_no` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `previous_owner_remark` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `insurance_start_date` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `insurance_end_date` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `pollution_start_date` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `pollution_end_date` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `fitness_start_date` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `fitness_end_date` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `tax_valid_upto` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `phpmyadmin.pma__bookmark`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `dbase` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `user` | `Keep` | Low | 110 | Referenced in 110 files: LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php, LeaveRequestRejected.php, LeaveRequestSubmitted.php |
| `label` | `Keep` | Low | 56 | Referenced in 56 files: AdminController.php, clusters.blade.php, contractors.blade.php, donors.blade.php, profile.blade.php |
| `query` | `Keep` | Low | 61 | Referenced in 61 files: AdminController.php, LeaveApiController.php, ApplicationController.php, LeaveRequestController.php, ProjectController.php |

### Table: `phpmyadmin.pma__central_columns`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `db_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `col_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `col_type` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `col_length` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `col_collation` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `col_isNull` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `col_extra` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `col_default` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `phpmyadmin.pma__column_info`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `db_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `table_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `column_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `comment` | `Keep` | Low | 1 | Referenced in 1 files: console.php |
| `mimetype` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `transformation` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `transformation_options` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `input_transformation` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `input_transformation_options` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `phpmyadmin.pma__designer_settings`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `username` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `settings_data` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `phpmyadmin.pma__export_templates`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `username` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `export_type` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `template_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `template_data` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `phpmyadmin.pma__favorite`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `username` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `tables` | `Keep` | Low | 3 | Referenced in 3 files: HasCategoryMeta.php, admin.blade.php, project_pdf.blade.php |

### Table: `phpmyadmin.pma__history`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `username` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `db` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, User.php, clusters.blade.php, leave_requests.blade.php, social_aid_funds.blade.php |
| `table` | `Keep` | Low | 118 | Referenced in 118 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `timevalue` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `sqlquery` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `phpmyadmin.pma__navigationhiding`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `username` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `item_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `item_type` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `db_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `table_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `phpmyadmin.pma__pdf_pages`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `db_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `page_nr` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `page_descr` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `phpmyadmin.pma__recent`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `username` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `tables` | `Keep` | Low | 3 | Referenced in 3 files: HasCategoryMeta.php, admin.blade.php, project_pdf.blade.php |

### Table: `phpmyadmin.pma__relation`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `master_db` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `master_table` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `master_field` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `foreign_db` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `foreign_table` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `foreign_field` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `phpmyadmin.pma__savedsearches`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `username` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `db_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `search_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `search_data` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `phpmyadmin.pma__table_coords`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `db_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `table_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `pdf_page_number` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `x` | `Keep` | Low | 195 | Referenced in 195 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `y` | `Keep` | Low | 194 | Referenced in 194 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |

### Table: `phpmyadmin.pma__table_info`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `db_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `table_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `display_field` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `phpmyadmin.pma__table_uiprefs`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `username` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `db_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `table_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `prefs` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `last_update` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `phpmyadmin.pma__tracking`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `db_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `table_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `version` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `date_created` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `date_updated` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `schema_snapshot` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `schema_sql` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `data_sql` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `tracking` | `Keep` | Low | 1 | Referenced in 1 files: welcome.blade.php |
| `tracking_active` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `phpmyadmin.pma__userconfig`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `username` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `timevalue` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `config_data` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `phpmyadmin.pma__usergroups`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `usergroup` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `tab` | `Keep` | Low | 143 | Referenced in 143 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, LeaveAdminDashboard.php |
| `allowed` | `Keep` | Low | 18 | Referenced in 18 files: ProjectController.php, profile.blade.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |

### Table: `phpmyadmin.pma__users`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `username` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `usergroup` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `rcfi.applicant_addresses`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `addressable_type` | `Keep` | Low | 1 | Referenced in 1 files: ApplicationAddressTest.php |
| `addressable_id` | `Keep` | Low | 1 | Referenced in 1 files: ApplicationAddressTest.php |
| `house_name` | `Keep` | Low | 27 | Referenced in 27 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `post_office` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, FamilyAidApplication.php, HasCategoryMeta.php |
| `village` | `Keep` | Low | 53 | Referenced in 53 files: AdminController.php, ApplicationController.php, ClusterController.php, ProjectController.php, Cluster.php |
| `panchayat` | `Keep` | Low | 48 | Referenced in 48 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `district` | `Keep` | Low | 69 | Referenced in 69 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `state` | `Keep` | Low | 70 | Referenced in 70 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `pin_code` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php, CulturalCenterApplication.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `contact_number_1` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `contact_number_2` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |

### Table: `rcfi.applications`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `applicant_name` | `Keep` | Low | 66 | Referenced in 66 files: ApplicationCreated.php, AdminController.php, ApplicationController.php, ProjectController.php, ListensForEntityChanges.php |
| `amount_requested` | `Keep` | Low | 43 | Referenced in 43 files: ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `rejected_reason` | `Keep` | Low | 20 | Referenced in 20 files: ApplicationController.php, HasCategoryMeta.php, base_table.blade.php, cultural_center.blade.php, differently_abled.blade.php |
| `contact_email` | `Keep` | Low | 5 | Referenced in 5 files: AdminController.php, ApplicationController.php, ProjectController.php, base_table.blade.php, ApplicationAddressTest.php |
| `details` | `Keep` | Low | 76 | Referenced in 76 files: ApplicationController.php, ContractorController.php, DonorController.php, ProfileController.php, ProjectController.php |
| `meta` | `Keep` | Low | 72 | Referenced in 72 files: ApplicationController.php, ProjectController.php, Application.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.cache`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `key` | `Keep` | Low | 52 | Referenced in 52 files: AdminController.php, ApplicationController.php, ProjectController.php, HasCategoryMeta.php, HasProjectColumns.php |
| `value` | `Keep` | Low | 72 | Referenced in 72 files: AdminController.php, ApplicationController.php, LeaveRequestController.php, ProjectController.php, User.php |
| `expiration` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `rcfi.cache_locks`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `key` | `Keep` | Low | 52 | Referenced in 52 files: AdminController.php, ApplicationController.php, ProjectController.php, HasCategoryMeta.php, HasProjectColumns.php |
| `owner` | `Keep` | Low | 8 | Referenced in 8 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, ProjectDocument.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `expiration` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `rcfi.clusters`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `code` | `Keep` | Low | 94 | Referenced in 94 files: AdminController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php, ProfileController.php |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `institution_name` | `Keep` | Low | 3 | Referenced in 3 files: ClusterController.php, Cluster.php, clusters.blade.php |
| `head_of_institution` | `Keep` | Low | 3 | Referenced in 3 files: ClusterController.php, Cluster.php, clusters.blade.php |
| `head_contact_number` | `Keep` | Low | 3 | Referenced in 3 files: ClusterController.php, Cluster.php, clusters.blade.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `po` | `Keep` | Low | 136 | Referenced in 136 files: AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php, ClusterController.php |
| `village` | `Keep` | Low | 53 | Referenced in 53 files: AdminController.php, ApplicationController.php, ClusterController.php, ProjectController.php, Cluster.php |
| `panjayath` | `Keep` | Low | 4 | Referenced in 4 files: ClusterController.php, Cluster.php, clusters.blade.php, social_aid_project_pdf.blade.php |
| `dist` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `state` | `Keep` | Low | 70 | Referenced in 70 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `contact_no` | `Keep` | Low | 3 | Referenced in 3 files: ClusterController.php, Cluster.php, clusters.blade.php |
| `cordinator_name` | `Keep` | Low | 3 | Referenced in 3 files: ClusterController.php, Cluster.php, clusters.blade.php |
| `cordinator_contact_number` | `Keep` | Low | 3 | Referenced in 3 files: ClusterController.php, Cluster.php, clusters.blade.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.contractors`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `phone` | `Keep` | Low | 52 | Referenced in 52 files: AdminController.php, ContractorController.php, DonorController.php, ProjectController.php, Contractor.php |
| `company_name` | `Keep` | Low | 16 | Referenced in 16 files: ContractorController.php, Contractor.php, ProjectContractor.php, HasProjectColumns.php, contractors.blade.php |
| `address` | `Keep` | Low | 40 | Referenced in 40 files: AdminController.php, ApplicationController.php, ContractorController.php, ProfileController.php, ProjectController.php |
| `email` | `Keep` | Low | 36 | Referenced in 36 files: AdminController.php, ApplicationController.php, AuthController.php, ContractorController.php, DonorController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.cultural_center_applications`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `applicant_name` | `Keep` | Low | 66 | Referenced in 66 files: ApplicationCreated.php, AdminController.php, ApplicationController.php, ProjectController.php, ListensForEntityChanges.php |
| `committee_name` | `Keep` | Low | 29 | Referenced in 29 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `reg_number` | `Keep` | Low | 28 | Referenced in 28 files: AdminController.php, ApplicationController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php |
| `year` | `Keep` | Low | 65 | Referenced in 65 files: AdminController.php, LeaveApiController.php, LeaveRequestController.php, AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php |
| `submitted_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `received_support_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `financial_support_purpose` | `Keep` | Low | 6 | Referenced in 6 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php |
| `mahallu_name` | `Keep` | Low | 22 | Referenced in 22 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_pin_code` | `Keep` | Low | 26 | Referenced in 26 files: ApplicationController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_village` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_post` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayath` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_district` | `Keep` | Low | 44 | Referenced in 44 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_state` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `families_in_mahallu` | `Keep` | Low | 21 | Referenced in 21 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `site_has_building` | `Keep` | Low | 19 | Referenced in 19 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `status_of_current_building` | `Keep` | Low | 19 | Referenced in 19 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `status_of_current_building_other` | `Keep` | Low | 12 | Referenced in 12 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `cultural_center_nearby` | `Keep` | Low | 5 | Referenced in 5 files: CulturalCenterApplication.php, cultural_center.blade.php, education_center.blade.php |
| `distance_cultural_centre` | `Keep` | Low | 12 | Referenced in 12 files: CulturalCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php, hospital_clinics.blade.php |
| `benefited_households` | `Keep` | Low | 3 | Referenced in 3 files: CulturalCenterApplication.php, cultural_center.blade.php |
| `project_type` | `Keep` | Low | 39 | Referenced in 39 files: AdminController.php, ProjectController.php, CulturalCenterApplication.php, EducationCenterApplication.php, ProjectCommunityContribution.php |
| `requirement` | `Keep` | Low | 17 | Referenced in 17 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, cultural_center.blade.php, education_center.blade.php |
| `building_area_sq` | `Keep` | Low | 23 | Referenced in 23 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, HouseApplication.php, ShopOtherApplication.php |
| `land_area_sq` | `Keep` | Low | 14 | Referenced in 14 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `amount_requested` | `Keep` | Low | 43 | Referenced in 43 files: ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `legal_approvals_status` | `Keep` | Low | 24 | Referenced in 24 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, HouseApplication.php, ShopOtherApplication.php |
| `permitted_type` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, hospital_clinics.blade.php |
| `area` | `Keep` | Low | 54 | Referenced in 54 files: CulturalCenterApplication.php, EducationCenterApplication.php, FamilyAidApplication.php, HospitalClinicApplication.php, HouseApplication.php |
| `recommendation_name` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization_other` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_phone` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_position` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `rejected_reason` | `Keep` | Low | 20 | Referenced in 20 files: ApplicationController.php, HasCategoryMeta.php, base_table.blade.php, cultural_center.blade.php, differently_abled.blade.php |
| `cluster_id` | `Keep` | Low | 15 | Referenced in 15 files: ApplicationController.php, Cluster.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `agency_number` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `agency_name` | `Keep` | Low | 24 | Referenced in 24 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, projects_list.blade.php, social_aid_project_detals.blade.php |
| `application_date` | `Keep` | Low | 10 | Referenced in 10 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, differently_abled.blade.php, drinking_water_group.blade.php |
| `whatsapp_number` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, social_aid_project_detals.blade.php, address_form_fields.blade.php, orphan_care.blade.php, social_aid_project_pdf.blade.php |
| `current_beneficiaries` | `Keep` | Low | 4 | Referenced in 4 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `contact_email` | `Keep` | Low | 5 | Referenced in 5 files: AdminController.php, ApplicationController.php, ProjectController.php, base_table.blade.php, ApplicationAddressTest.php |
| `additional_note` | `Keep` | Low | 11 | Referenced in 11 files: ApplicationController.php, ProjectController.php, HasCategoryMeta.php, social_aid_project_detals.blade.php, cultural_center.blade.php |
| `locality_place` | `Keep` | Low | 23 | Referenced in 23 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `rooms` | `Keep` | Low | 14 | Referenced in 14 files: EducationCenterApplication.php, ShopOtherApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `num_beneficiaries` | `Keep` | Low | 2 | Referenced in 2 files: cultural_center.blade.php |
| `recommender_name` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org_other` | `Keep` | Low | 19 | Referenced in 19 files: HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `recommender_phone` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_position` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `locality_panchayat` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `pin_code` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php, CulturalCenterApplication.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `village` | `Keep` | Low | 53 | Referenced in 53 files: AdminController.php, ApplicationController.php, ClusterController.php, ProjectController.php, Cluster.php |
| `post_office` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, FamilyAidApplication.php, HasCategoryMeta.php |
| `panchayat` | `Keep` | Low | 48 | Referenced in 48 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `district` | `Keep` | Low | 69 | Referenced in 69 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `state` | `Keep` | Low | 70 | Referenced in 70 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `contact_number_1` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `contact_number_2` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |

### Table: `rcfi.cultural_center_projects`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_name` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `sponsor` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `project_spec` | `Keep` | Low | 26 | Referenced in 26 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `agency_project_no` | `Keep` | Low | 38 | Referenced in 38 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php, DifferentlyAbledProject.php |
| `donor_id` | `Keep` | Low | 25 | Referenced in 25 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `project_manager_id` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `engineer_id` | `Keep` | Low | 29 | Referenced in 29 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `application_id` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `available_budget` | `Keep` | Low | 27 | Referenced in 27 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `type_of_project` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `theme` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, ThemeController.php, Subtheme.php, Theme.php |
| `subtheme` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, ProjectController.php, ThemeController.php, Subtheme.php, Theme.php |
| `activity` | `Keep` | Low | 24 | Referenced in 24 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `stage` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php, DifferentlyAbledProject.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `completion_details` | `Keep` | Low | 26 | Referenced in 26 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `community_contributions` | `Keep` | Low | 24 | Referenced in 24 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `materials` | `Keep` | Low | 13 | Referenced in 13 files: ProjectController.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `expenses` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.differently_abled_applications`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `amount_requested` | `Keep` | Low | 43 | Referenced in 43 files: ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `applicant_name` | `Keep` | Low | 66 | Referenced in 66 files: ApplicationCreated.php, AdminController.php, ApplicationController.php, ProjectController.php, ListensForEntityChanges.php |
| `father_name` | `Keep` | Low | 30 | Referenced in 30 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `fathers_father` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, FamilyAidApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `mother_name` | `Keep` | Low | 29 | Referenced in 29 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `gender` | `Keep` | Low | 29 | Referenced in 29 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `aadhar_number` | `Keep` | Low | 16 | Referenced in 16 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `dob` | `Keep` | Low | 16 | Referenced in 16 files: ProjectController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `age` | `Keep` | Low | 141 | Referenced in 141 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php |
| `marital_status` | `Keep` | Low | 13 | Referenced in 13 files: ProfileController.php, UserController.php, DifferentlyAbledApplication.php, LeaveType.php, User.php |
| `guardian_name` | `Keep` | Low | 9 | Referenced in 9 files: ProjectController.php, DifferentlyAbledApplication.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `guardian_relation` | `Keep` | Low | 8 | Referenced in 8 files: ProjectController.php, DifferentlyAbledApplication.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `male_members` | `Keep` | Low | 3 | Referenced in 3 files: DifferentlyAbledApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `female_members` | `Keep` | Low | 3 | Referenced in 3 files: DifferentlyAbledApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `total_members` | `Keep` | Low | 3 | Referenced in 3 files: DifferentlyAbledApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `people_with_disabilities` | `Keep` | Low | 3 | Referenced in 3 files: DifferentlyAbledApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `monthly_income` | `Keep` | Low | 18 | Referenced in 18 files: ProjectController.php, DifferentlyAbledApplication.php, DrinkingWaterIndividualApplication.php, FamilyAidApplication.php, GeneralApplication.php |
| `monthly_cost` | `Keep` | Low | 3 | Referenced in 3 files: DifferentlyAbledApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `income_source` | `Keep` | Low | 7 | Referenced in 7 files: DifferentlyAbledApplication.php, FamilyAidApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php, family_aid.blade.php |
| `studying_institution` | `Keep` | Low | 3 | Referenced in 3 files: DifferentlyAbledApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `not_studying_reason` | `Keep` | Low | 7 | Referenced in 7 files: ProjectController.php, DifferentlyAbledApplication.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `health_status` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, HouseApplication.php, OrphanCareApplication.php |
| `disability_type` | `Keep` | Low | 4 | Referenced in 4 files: DifferentlyAbledApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `disability_percentage` | `Keep` | Low | 3 | Referenced in 3 files: DifferentlyAbledApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `disability_date` | `Keep` | Low | 3 | Referenced in 3 files: DifferentlyAbledApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `disability_level` | `Keep` | Low | 3 | Referenced in 3 files: DifferentlyAbledApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `other_help` | `Keep` | Low | 3 | Referenced in 3 files: DifferentlyAbledApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `description` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledApplication.php, FamilyAidApplication.php, LeaveType.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `accommodation` | `Keep` | Low | 7 | Referenced in 7 files: DifferentlyAbledApplication.php, HouseApplication.php, house.blade.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `cluster_id` | `Keep` | Low | 15 | Referenced in 15 files: ApplicationController.php, Cluster.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `agency_number` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `recommendation_name` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization_other` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_phone` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_position` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `rejected_reason` | `Keep` | Low | 20 | Referenced in 20 files: ApplicationController.php, HasCategoryMeta.php, base_table.blade.php, cultural_center.blade.php, differently_abled.blade.php |
| `agency_name` | `Keep` | Low | 24 | Referenced in 24 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, projects_list.blade.php, social_aid_project_detals.blade.php |
| `application_date` | `Keep` | Low | 10 | Referenced in 10 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, differently_abled.blade.php, drinking_water_group.blade.php |
| `whatsapp_number` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, social_aid_project_detals.blade.php, address_form_fields.blade.php, orphan_care.blade.php, social_aid_project_pdf.blade.php |
| `current_beneficiaries` | `Keep` | Low | 4 | Referenced in 4 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `sponsor_status` | `Keep` | Low | 16 | Referenced in 16 files: ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `student_photo` | `Keep` | Low | 9 | Referenced in 9 files: AdminController.php, ApplicationController.php, ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php |
| `contact_email` | `Keep` | Low | 5 | Referenced in 5 files: AdminController.php, ApplicationController.php, ProjectController.php, base_table.blade.php, ApplicationAddressTest.php |
| `additional_note` | `Keep` | Low | 11 | Referenced in 11 files: ApplicationController.php, ProjectController.php, HasCategoryMeta.php, social_aid_project_detals.blade.php, cultural_center.blade.php |
| `recommender_name` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org_other` | `Keep` | Low | 19 | Referenced in 19 files: HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `recommender_phone` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_position` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `locality_pin_code` | `Keep` | Low | 26 | Referenced in 26 files: ApplicationController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_place` | `Keep` | Low | 23 | Referenced in 23 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_village` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_post` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayath` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayat` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_district` | `Keep` | Low | 44 | Referenced in 44 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_state` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `submitted_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `received_support_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `financial_support_purpose` | `Keep` | Low | 6 | Referenced in 6 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php |
| `permitted_type` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, hospital_clinics.blade.php |
| `house_name` | `Keep` | Low | 27 | Referenced in 27 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `pin_code` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php, CulturalCenterApplication.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `post_office` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, FamilyAidApplication.php, HasCategoryMeta.php |
| `panchayat` | `Keep` | Low | 48 | Referenced in 48 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `district` | `Keep` | Low | 69 | Referenced in 69 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `state` | `Keep` | Low | 70 | Referenced in 70 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `contact_number_1` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `contact_number_2` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |

### Table: `rcfi.differently_abled_funds`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `agency_project_no` | `Keep` | Low | 38 | Referenced in 38 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php, DifferentlyAbledProject.php |
| `date` | `Keep` | Low | 126 | Referenced in 126 files: EntityChanged.php, LeaveBalanceUpdated.php, ProjectUpdated.php, AdminController.php, LeaveApiController.php |
| `amount` | `Keep` | Low | 55 | Referenced in 55 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php |
| `account_name` | `Keep` | Low | 3 | Referenced in 3 files: ProjectController.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `account_number` | `Keep` | Low | 8 | Referenced in 8 files: ProfileController.php, ProjectController.php, UserController.php, profile.blade.php, social_aid_project_detals.blade.php |
| `ifsc_number` | `Keep` | Low | 2 | Referenced in 2 files: ProjectController.php, social_aid_project_detals.blade.php |
| `donor` | `Keep` | Low | 64 | Referenced in 64 files: AdminController.php, ApplicationController.php, DonorController.php, ProjectController.php, CulturalCenterProject.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.differently_abled_programmes`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `differently_abled_project_id` | `Keep` | Low | 2 | Referenced in 2 files: DifferentlyAbledProgramme.php, DifferentlyAbledProject.php |
| `programme_name` | `Keep` | Low | 6 | Referenced in 6 files: ProjectController.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php, differently_abled.blade.php, family_aid.blade.php |
| `date` | `Keep` | Low | 126 | Referenced in 126 files: EntityChanged.php, LeaveBalanceUpdated.php, ProjectUpdated.php, AdminController.php, LeaveApiController.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `present_ticked` | `Keep` | Low | 9 | Referenced in 9 files: ProjectController.php, DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php |
| `photo_ticked` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `marklist_ticked` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `thanks_letter_ticked` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `report_form_ticked` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `medical_certificate_ticked` | `Keep` | Low | 5 | Referenced in 5 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_pdf.blade.php, differently_abled.blade.php |
| `other_document_ticked` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.differently_abled_projects`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_name` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `sponsor` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `agency_project_no` | `Keep` | Low | 38 | Referenced in 38 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php, DifferentlyAbledProject.php |
| `application_id` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `type_of_project` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `stage` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php, DifferentlyAbledProject.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `location` | `Keep` | Low | 58 | Referenced in 58 files: AdminController.php, ApplicationController.php, ProjectController.php, AllocateAnnualLeaveJob.php, CulturalCenterApplication.php |

### Table: `rcfi.donors`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `short_name` | `Keep` | Low | 5 | Referenced in 5 files: AdminController.php, DonorController.php, donors.blade.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `website` | `Keep` | Low | 2 | Referenced in 2 files: DonorController.php, donors.blade.php |
| `type_of_partner` | `Keep` | Low | 4 | Referenced in 4 files: DonorController.php, donors.blade.php, ApplicationApprovalPermissionsTest.php, Stage4ApprovalTest.php |
| `type_of_fund` | `Keep` | Low | 4 | Referenced in 4 files: DonorController.php, donors.blade.php, ApplicationApprovalPermissionsTest.php, Stage4ApprovalTest.php |
| `contact_person` | `Keep` | Low | 2 | Referenced in 2 files: DonorController.php, donors.blade.php |
| `support_initiated_at` | `Keep` | Low | 2 | Referenced in 2 files: DonorController.php, donors.blade.php |
| `email` | `Keep` | Low | 36 | Referenced in 36 files: AdminController.php, ApplicationController.php, AuthController.php, ContractorController.php, DonorController.php |
| `phone` | `Keep` | Low | 52 | Referenced in 52 files: AdminController.php, ContractorController.php, DonorController.php, ProjectController.php, Contractor.php |
| `image_path` | `Keep` | Low | 2 | Referenced in 2 files: DonorController.php, donors.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.drinking_water_group_applications`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `applicant_name` | `Keep` | Low | 66 | Referenced in 66 files: ApplicationCreated.php, AdminController.php, ApplicationController.php, ProjectController.php, ListensForEntityChanges.php |
| `gender` | `Keep` | Low | 29 | Referenced in 29 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `male_adults` | `Keep` | Low | 4 | Referenced in 4 files: DrinkingWaterGroupApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `male_children` | `Keep` | Low | 7 | Referenced in 7 files: DrinkingWaterGroupApplication.php, HouseApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, house.blade.php |
| `female_adults` | `Keep` | Low | 4 | Referenced in 4 files: DrinkingWaterGroupApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `female_children` | `Keep` | Low | 7 | Referenced in 7 files: DrinkingWaterGroupApplication.php, HouseApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, house.blade.php |
| `num_benefited_people` | `Keep` | Low | 6 | Referenced in 6 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `land_owner_name` | `Keep` | Low | 6 | Referenced in 6 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `land_owner_address` | `Keep` | Low | 4 | Referenced in 4 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `land_owner_place` | `Keep` | Low | 7 | Referenced in 7 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, admin.blade.php |
| `land_owner_post` | `Keep` | Low | 7 | Referenced in 7 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, admin.blade.php |
| `land_owner_panchayath` | `Keep` | Low | 7 | Referenced in 7 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, admin.blade.php |
| `land_owner_district` | `Keep` | Low | 7 | Referenced in 7 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, admin.blade.php |
| `land_owner_mobile` | `Keep` | Low | 6 | Referenced in 6 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `well_type` | `Keep` | Low | 8 | Referenced in 8 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `well_depth` | `Keep` | Low | 6 | Referenced in 6 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `legal_permissions` | `Keep` | Low | 4 | Referenced in 4 files: DrinkingWaterGroupApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `amount_requested` | `Keep` | Low | 43 | Referenced in 43 files: ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `recommendation_name` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization_other` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_phone` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_position` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `rejected_reason` | `Keep` | Low | 20 | Referenced in 20 files: ApplicationController.php, HasCategoryMeta.php, base_table.blade.php, cultural_center.blade.php, differently_abled.blade.php |
| `cluster_id` | `Keep` | Low | 15 | Referenced in 15 files: ApplicationController.php, Cluster.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `agency_number` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `agency_name` | `Keep` | Low | 24 | Referenced in 24 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, projects_list.blade.php, social_aid_project_detals.blade.php |
| `application_date` | `Keep` | Low | 10 | Referenced in 10 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, differently_abled.blade.php, drinking_water_group.blade.php |
| `whatsapp_number` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, social_aid_project_detals.blade.php, address_form_fields.blade.php, orphan_care.blade.php, social_aid_project_pdf.blade.php |
| `current_beneficiaries` | `Keep` | Low | 4 | Referenced in 4 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `contact_email` | `Keep` | Low | 5 | Referenced in 5 files: AdminController.php, ApplicationController.php, ProjectController.php, base_table.blade.php, ApplicationAddressTest.php |
| `additional_note` | `Keep` | Low | 11 | Referenced in 11 files: ApplicationController.php, ProjectController.php, HasCategoryMeta.php, social_aid_project_detals.blade.php, cultural_center.blade.php |
| `beneficiaries` | `Keep` | Low | 12 | Referenced in 12 files: ProjectController.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, OrphanCareApplication.php, drinking_water_group.blade.php |
| `recommender_name` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org_other` | `Keep` | Low | 19 | Referenced in 19 files: HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `recommender_phone` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_position` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `locality_pin_code` | `Keep` | Low | 26 | Referenced in 26 files: ApplicationController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_place` | `Keep` | Low | 23 | Referenced in 23 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_village` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_post` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayath` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayat` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_district` | `Keep` | Low | 44 | Referenced in 44 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_state` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `land_owner_pin` | `Keep` | Low | 1 | Referenced in 1 files: admin.blade.php |
| `land_owner_village` | `Keep` | Low | 1 | Referenced in 1 files: admin.blade.php |
| `land_owner_state` | `Keep` | Low | 1 | Referenced in 1 files: admin.blade.php |
| `submitted_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `received_support_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `financial_support_purpose` | `Keep` | Low | 6 | Referenced in 6 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php |
| `permitted_type` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, hospital_clinics.blade.php |
| `pin_code` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php, CulturalCenterApplication.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `village` | `Keep` | Low | 53 | Referenced in 53 files: AdminController.php, ApplicationController.php, ClusterController.php, ProjectController.php, Cluster.php |
| `post_office` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, FamilyAidApplication.php, HasCategoryMeta.php |
| `panchayat` | `Keep` | Low | 48 | Referenced in 48 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `district` | `Keep` | Low | 69 | Referenced in 69 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `state` | `Keep` | Low | 70 | Referenced in 70 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `contact_number_1` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `contact_number_2` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |

### Table: `rcfi.drinking_water_group_projects`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_name` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `sponsor` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `project_spec` | `Keep` | Low | 26 | Referenced in 26 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `agency_project_no` | `Keep` | Low | 38 | Referenced in 38 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php, DifferentlyAbledProject.php |
| `donor_id` | `Keep` | Low | 25 | Referenced in 25 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `project_manager_id` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `engineer_id` | `Keep` | Low | 29 | Referenced in 29 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `application_id` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `available_budget` | `Keep` | Low | 27 | Referenced in 27 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `type_of_project` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `theme` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, ThemeController.php, Subtheme.php, Theme.php |
| `subtheme` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, ProjectController.php, ThemeController.php, Subtheme.php, Theme.php |
| `activity` | `Keep` | Low | 24 | Referenced in 24 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `stage` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php, DifferentlyAbledProject.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `completion_details` | `Keep` | Low | 26 | Referenced in 26 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `community_contributions` | `Keep` | Low | 24 | Referenced in 24 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `materials` | `Keep` | Low | 13 | Referenced in 13 files: ProjectController.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `expenses` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.drinking_water_individual_applications`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `applicant_name` | `Keep` | Low | 66 | Referenced in 66 files: ApplicationCreated.php, AdminController.php, ApplicationController.php, ProjectController.php, ListensForEntityChanges.php |
| `job` | `Keep` | Low | 3 | Referenced in 3 files: DrinkingWaterIndividualApplication.php, drinking_water_individual.blade.php, LeaveManagementTest.php |
| `monthly_income` | `Keep` | Low | 18 | Referenced in 18 files: ProjectController.php, DifferentlyAbledApplication.php, DrinkingWaterIndividualApplication.php, FamilyAidApplication.php, GeneralApplication.php |
| `num_male_benefited` | `Keep` | Low | 2 | Referenced in 2 files: DrinkingWaterIndividualApplication.php, drinking_water_individual.blade.php |
| `num_female_benefited` | `Keep` | Low | 2 | Referenced in 2 files: DrinkingWaterIndividualApplication.php, drinking_water_individual.blade.php |
| `num_benefited_people` | `Keep` | Low | 6 | Referenced in 6 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `land_owner_name` | `Keep` | Low | 6 | Referenced in 6 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `land_owner_address` | `Keep` | Low | 4 | Referenced in 4 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `land_owner_place` | `Keep` | Low | 7 | Referenced in 7 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, admin.blade.php |
| `land_owner_post` | `Keep` | Low | 7 | Referenced in 7 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, admin.blade.php |
| `land_owner_panchayath` | `Keep` | Low | 7 | Referenced in 7 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, admin.blade.php |
| `land_owner_district` | `Keep` | Low | 7 | Referenced in 7 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, admin.blade.php |
| `land_owner_mobile` | `Keep` | Low | 6 | Referenced in 6 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `well_type` | `Keep` | Low | 8 | Referenced in 8 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `well_depth` | `Keep` | Low | 6 | Referenced in 6 files: DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `well_diameter` | `Keep` | Low | 2 | Referenced in 2 files: DrinkingWaterIndividualApplication.php, drinking_water_individual.blade.php |
| `amount_requested` | `Keep` | Low | 43 | Referenced in 43 files: ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `land_nature` | `Keep` | Low | 2 | Referenced in 2 files: DrinkingWaterIndividualApplication.php, drinking_water_individual.blade.php |
| `current_water_source` | `Keep` | Low | 2 | Referenced in 2 files: DrinkingWaterIndividualApplication.php, drinking_water_individual.blade.php |
| `need_pump` | `Keep` | Low | 2 | Referenced in 2 files: DrinkingWaterIndividualApplication.php, drinking_water_individual.blade.php |
| `well_for_agriculture` | `Keep` | Low | 2 | Referenced in 2 files: DrinkingWaterIndividualApplication.php, drinking_water_individual.blade.php |
| `recommendation_name` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization_other` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_phone` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_position` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `rejected_reason` | `Keep` | Low | 20 | Referenced in 20 files: ApplicationController.php, HasCategoryMeta.php, base_table.blade.php, cultural_center.blade.php, differently_abled.blade.php |
| `cluster_id` | `Keep` | Low | 15 | Referenced in 15 files: ApplicationController.php, Cluster.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `agency_number` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `agency_name` | `Keep` | Low | 24 | Referenced in 24 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, projects_list.blade.php, social_aid_project_detals.blade.php |
| `application_date` | `Keep` | Low | 10 | Referenced in 10 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, differently_abled.blade.php, drinking_water_group.blade.php |
| `whatsapp_number` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, social_aid_project_detals.blade.php, address_form_fields.blade.php, orphan_care.blade.php, social_aid_project_pdf.blade.php |
| `current_beneficiaries` | `Keep` | Low | 4 | Referenced in 4 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `contact_email` | `Keep` | Low | 5 | Referenced in 5 files: AdminController.php, ApplicationController.php, ProjectController.php, base_table.blade.php, ApplicationAddressTest.php |
| `additional_note` | `Keep` | Low | 11 | Referenced in 11 files: ApplicationController.php, ProjectController.php, HasCategoryMeta.php, social_aid_project_detals.blade.php, cultural_center.blade.php |
| `gender` | `Keep` | Low | 29 | Referenced in 29 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `beneficiaries` | `Keep` | Low | 12 | Referenced in 12 files: ProjectController.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, OrphanCareApplication.php, drinking_water_group.blade.php |
| `legal_permissions` | `Keep` | Low | 4 | Referenced in 4 files: DrinkingWaterGroupApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `recommender_name` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org_other` | `Keep` | Low | 19 | Referenced in 19 files: HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `recommender_phone` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_position` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `locality_pin_code` | `Keep` | Low | 26 | Referenced in 26 files: ApplicationController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_place` | `Keep` | Low | 23 | Referenced in 23 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_village` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_post` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayath` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayat` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_district` | `Keep` | Low | 44 | Referenced in 44 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_state` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `land_owner_pin` | `Keep` | Low | 1 | Referenced in 1 files: admin.blade.php |
| `land_owner_village` | `Keep` | Low | 1 | Referenced in 1 files: admin.blade.php |
| `land_owner_state` | `Keep` | Low | 1 | Referenced in 1 files: admin.blade.php |
| `submitted_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `received_support_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `financial_support_purpose` | `Keep` | Low | 6 | Referenced in 6 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php |
| `permitted_type` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, hospital_clinics.blade.php |
| `pin_code` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php, CulturalCenterApplication.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `village` | `Keep` | Low | 53 | Referenced in 53 files: AdminController.php, ApplicationController.php, ClusterController.php, ProjectController.php, Cluster.php |
| `post_office` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, FamilyAidApplication.php, HasCategoryMeta.php |
| `panchayat` | `Keep` | Low | 48 | Referenced in 48 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `district` | `Keep` | Low | 69 | Referenced in 69 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `state` | `Keep` | Low | 70 | Referenced in 70 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `contact_number_1` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `contact_number_2` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |

### Table: `rcfi.drinking_water_individual_projects`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_name` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `sponsor` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `project_spec` | `Keep` | Low | 26 | Referenced in 26 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `agency_project_no` | `Keep` | Low | 38 | Referenced in 38 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php, DifferentlyAbledProject.php |
| `donor_id` | `Keep` | Low | 25 | Referenced in 25 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `project_manager_id` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `engineer_id` | `Keep` | Low | 29 | Referenced in 29 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `application_id` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `available_budget` | `Keep` | Low | 27 | Referenced in 27 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `type_of_project` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `theme` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, ThemeController.php, Subtheme.php, Theme.php |
| `subtheme` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, ProjectController.php, ThemeController.php, Subtheme.php, Theme.php |
| `activity` | `Keep` | Low | 24 | Referenced in 24 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `stage` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php, DifferentlyAbledProject.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `completion_details` | `Keep` | Low | 26 | Referenced in 26 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `community_contributions` | `Keep` | Low | 24 | Referenced in 24 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `materials` | `Keep` | Low | 13 | Referenced in 13 files: ProjectController.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `expenses` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.education_center_applications`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `applicant_name` | `Keep` | Low | 66 | Referenced in 66 files: ApplicationCreated.php, AdminController.php, ApplicationController.php, ProjectController.php, ListensForEntityChanges.php |
| `committee_name` | `Keep` | Low | 29 | Referenced in 29 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `reg_number` | `Keep` | Low | 28 | Referenced in 28 files: AdminController.php, ApplicationController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php |
| `year` | `Keep` | Low | 65 | Referenced in 65 files: AdminController.php, LeaveApiController.php, LeaveRequestController.php, AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php |
| `submitted_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `received_support_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `financial_support_purpose` | `Keep` | Low | 6 | Referenced in 6 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php |
| `mahallu_name` | `Keep` | Low | 22 | Referenced in 22 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_pin_code` | `Keep` | Low | 26 | Referenced in 26 files: ApplicationController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_village` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_post` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayath` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_district` | `Keep` | Low | 44 | Referenced in 44 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_state` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `families_in_mahallu` | `Keep` | Low | 21 | Referenced in 21 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `site_has_building` | `Keep` | Low | 19 | Referenced in 19 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `status_of_current_building` | `Keep` | Low | 19 | Referenced in 19 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `status_of_current_building_other` | `Keep` | Low | 12 | Referenced in 12 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `students_boys` | `Keep` | Low | 12 | Referenced in 12 files: EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php, hospital_clinics.blade.php |
| `students_girls` | `Keep` | Low | 12 | Referenced in 12 files: EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php, hospital_clinics.blade.php |
| `total_students` | `Keep` | Low | 4 | Referenced in 4 files: EducationCenterApplication.php, education_center.blade.php, ApplicationAddressTest.php |
| `education_center_nearby` | `Keep` | Low | 12 | Referenced in 12 files: EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php, hospital_clinics.blade.php |
| `syllabus` | `Keep` | Low | 10 | Referenced in 10 files: EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php, hospital_clinics.blade.php |
| `distance_education_center` | `Keep` | Low | 6 | Referenced in 6 files: EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php |
| `project_type` | `Keep` | Low | 39 | Referenced in 39 files: AdminController.php, ProjectController.php, CulturalCenterApplication.php, EducationCenterApplication.php, ProjectCommunityContribution.php |
| `requirement` | `Keep` | Low | 17 | Referenced in 17 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, cultural_center.blade.php, education_center.blade.php |
| `building_area_sq` | `Keep` | Low | 23 | Referenced in 23 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, HouseApplication.php, ShopOtherApplication.php |
| `land_area_sq` | `Keep` | Low | 14 | Referenced in 14 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `num_classrooms` | `Keep` | Low | 11 | Referenced in 11 files: EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php, hospital_clinics.blade.php |
| `num_students` | `Keep` | Low | 11 | Referenced in 11 files: EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php, hospital_clinics.blade.php |
| `amount_requested` | `Keep` | Low | 43 | Referenced in 43 files: ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `legal_approvals_status` | `Keep` | Low | 24 | Referenced in 24 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, HouseApplication.php, ShopOtherApplication.php |
| `permitted_type` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, hospital_clinics.blade.php |
| `area` | `Keep` | Low | 54 | Referenced in 54 files: CulturalCenterApplication.php, EducationCenterApplication.php, FamilyAidApplication.php, HospitalClinicApplication.php, HouseApplication.php |
| `recommendation_name` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization_other` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_phone` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_position` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `rejected_reason` | `Keep` | Low | 20 | Referenced in 20 files: ApplicationController.php, HasCategoryMeta.php, base_table.blade.php, cultural_center.blade.php, differently_abled.blade.php |
| `agency_number` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `agency_name` | `Keep` | Low | 24 | Referenced in 24 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, projects_list.blade.php, social_aid_project_detals.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `locality_place` | `Keep` | Low | 23 | Referenced in 23 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `contact_email` | `Keep` | Low | 5 | Referenced in 5 files: AdminController.php, ApplicationController.php, ProjectController.php, base_table.blade.php, ApplicationAddressTest.php |
| `additional_note` | `Keep` | Low | 11 | Referenced in 11 files: ApplicationController.php, ProjectController.php, HasCategoryMeta.php, social_aid_project_detals.blade.php, cultural_center.blade.php |
| `recommender_name` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org_other` | `Keep` | Low | 19 | Referenced in 19 files: HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `recommender_phone` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_position` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `pin_code` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php, CulturalCenterApplication.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `village` | `Keep` | Low | 53 | Referenced in 53 files: AdminController.php, ApplicationController.php, ClusterController.php, ProjectController.php, Cluster.php |
| `post_office` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, FamilyAidApplication.php, HasCategoryMeta.php |
| `panchayat` | `Keep` | Low | 48 | Referenced in 48 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `district` | `Keep` | Low | 69 | Referenced in 69 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `state` | `Keep` | Low | 70 | Referenced in 70 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `contact_number_1` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `contact_number_2` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |

### Table: `rcfi.education_center_projects`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_name` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `sponsor` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `project_spec` | `Keep` | Low | 26 | Referenced in 26 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `agency_project_no` | `Keep` | Low | 38 | Referenced in 38 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php, DifferentlyAbledProject.php |
| `donor_id` | `Keep` | Low | 25 | Referenced in 25 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `project_manager_id` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `engineer_id` | `Keep` | Low | 29 | Referenced in 29 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `application_id` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `available_budget` | `Keep` | Low | 27 | Referenced in 27 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `type_of_project` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `theme` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, ThemeController.php, Subtheme.php, Theme.php |
| `subtheme` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, ProjectController.php, ThemeController.php, Subtheme.php, Theme.php |
| `activity` | `Keep` | Low | 24 | Referenced in 24 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `stage` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php, DifferentlyAbledProject.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `completion_details` | `Keep` | Low | 26 | Referenced in 26 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `community_contributions` | `Keep` | Low | 24 | Referenced in 24 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `materials` | `Keep` | Low | 13 | Referenced in 13 files: ProjectController.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `expenses` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.failed_jobs`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `uuid` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `connection` | `Keep` | Low | 3 | Referenced in 3 files: ProjectDocument.php, drinking_water_group.blade.php, admin.blade.php |
| `queue` | `Keep` | Low | 2 | Referenced in 2 files: leave-admin-dashboard.blade.php, role-dashboard.blade.php |
| `payload` | `Keep` | Low | 16 | Referenced in 16 files: EntityChanged.php, ProjectUpdated.php, ListensForEntityChanges.php, RoleDashboard.php, BroadcastsChanges.php |
| `exception` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `failed_at` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `rcfi.family_aid_applications`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `amount_requested` | `Keep` | Low | 43 | Referenced in 43 files: ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `applicant_name` | `Keep` | Low | 66 | Referenced in 66 files: ApplicationCreated.php, AdminController.php, ApplicationController.php, ProjectController.php, ListensForEntityChanges.php |
| `father_name` | `Keep` | Low | 30 | Referenced in 30 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `mother_name` | `Keep` | Low | 29 | Referenced in 29 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `fathers_father` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, FamilyAidApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `dob` | `Keep` | Low | 16 | Referenced in 16 files: ProjectController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `age` | `Keep` | Low | 141 | Referenced in 141 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php |
| `aadhar_number` | `Keep` | Low | 16 | Referenced in 16 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `children_total` | `Keep` | Low | 6 | Referenced in 6 files: FamilyAidApplication.php, house.blade.php, social_aid_project_detals.blade.php, family_aid.blade.php |
| `children_male` | `Keep` | Low | 5 | Referenced in 5 files: FamilyAidApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php, house.blade.php |
| `children_female` | `Keep` | Low | 5 | Referenced in 5 files: FamilyAidApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php, house.blade.php |
| `nri_status` | `Keep` | Low | 3 | Referenced in 3 files: FamilyAidApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php |
| `occupation` | `Keep` | Low | 7 | Referenced in 7 files: FamilyAidApplication.php, HouseApplication.php, house.blade.php, social_aid_project_detals.blade.php, family_aid.blade.php |
| `monthly_income` | `Keep` | Low | 18 | Referenced in 18 files: ProjectController.php, DifferentlyAbledApplication.php, DrinkingWaterIndividualApplication.php, FamilyAidApplication.php, GeneralApplication.php |
| `other_income_sources` | `Keep` | Low | 5 | Referenced in 5 files: FamilyAidApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php, house.blade.php |
| `health_status` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, HouseApplication.php, OrphanCareApplication.php |
| `disability_status` | `Keep` | Low | 3 | Referenced in 3 files: FamilyAidApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php |
| `routine_treatment_explanation` | `Keep` | Low | 5 | Referenced in 5 files: FamilyAidApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php, house.blade.php |
| `chronic_patients_description` | `Keep` | Low | 3 | Referenced in 3 files: FamilyAidApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php |
| `residence_info` | `Keep` | Low | 5 | Referenced in 5 files: FamilyAidApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php, house.blade.php |
| `own_house_condition` | `Keep` | Low | 3 | Referenced in 3 files: FamilyAidApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php |
| `own_place_status` | `Keep` | Low | 5 | Referenced in 5 files: FamilyAidApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php, house.blade.php |
| `own_place_size` | `Keep` | Low | 5 | Referenced in 5 files: FamilyAidApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php, house.blade.php |
| `sequel_status` | `Keep` | Low | 3 | Referenced in 3 files: FamilyAidApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php |
| `welfare_assistance_areas` | `Keep` | Low | 3 | Referenced in 3 files: FamilyAidApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php |
| `cluster_id` | `Keep` | Low | 15 | Referenced in 15 files: ApplicationController.php, Cluster.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `agency_number` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `recommendation_name` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization_other` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_phone` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_position` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `rejected_reason` | `Keep` | Low | 20 | Referenced in 20 files: ApplicationController.php, HasCategoryMeta.php, base_table.blade.php, cultural_center.blade.php, differently_abled.blade.php |
| `agency_name` | `Keep` | Low | 24 | Referenced in 24 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, projects_list.blade.php, social_aid_project_detals.blade.php |
| `application_date` | `Keep` | Low | 10 | Referenced in 10 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, differently_abled.blade.php, drinking_water_group.blade.php |
| `whatsapp_number` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, social_aid_project_detals.blade.php, address_form_fields.blade.php, orphan_care.blade.php, social_aid_project_pdf.blade.php |
| `current_beneficiaries` | `Keep` | Low | 4 | Referenced in 4 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `sponsor_status` | `Keep` | Low | 16 | Referenced in 16 files: ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `student_photo` | `Keep` | Low | 9 | Referenced in 9 files: AdminController.php, ApplicationController.php, ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php |
| `contact_email` | `Keep` | Low | 5 | Referenced in 5 files: AdminController.php, ApplicationController.php, ProjectController.php, base_table.blade.php, ApplicationAddressTest.php |
| `additional_note` | `Keep` | Low | 11 | Referenced in 11 files: ApplicationController.php, ProjectController.php, HasCategoryMeta.php, social_aid_project_detals.blade.php, cultural_center.blade.php |
| `gender` | `Keep` | Low | 29 | Referenced in 29 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `recommender_name` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org_other` | `Keep` | Low | 19 | Referenced in 19 files: HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `recommender_phone` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_position` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `locality_pin_code` | `Keep` | Low | 26 | Referenced in 26 files: ApplicationController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_place` | `Keep` | Low | 23 | Referenced in 23 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_village` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_post` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayath` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayat` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_district` | `Keep` | Low | 44 | Referenced in 44 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_state` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `submitted_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `received_support_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `financial_support_purpose` | `Keep` | Low | 6 | Referenced in 6 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php |
| `permitted_type` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, hospital_clinics.blade.php |
| `house_name` | `Keep` | Low | 27 | Referenced in 27 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `pin_code` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php, CulturalCenterApplication.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `post_office` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, FamilyAidApplication.php, HasCategoryMeta.php |
| `panchayat` | `Keep` | Low | 48 | Referenced in 48 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `district` | `Keep` | Low | 69 | Referenced in 69 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `state` | `Keep` | Low | 70 | Referenced in 70 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `contact_number_1` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `contact_number_2` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |

### Table: `rcfi.family_aid_funds`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `agency_project_no` | `Keep` | Low | 38 | Referenced in 38 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php, DifferentlyAbledProject.php |
| `date` | `Keep` | Low | 126 | Referenced in 126 files: EntityChanged.php, LeaveBalanceUpdated.php, ProjectUpdated.php, AdminController.php, LeaveApiController.php |
| `amount` | `Keep` | Low | 55 | Referenced in 55 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php |
| `account_name` | `Keep` | Low | 3 | Referenced in 3 files: ProjectController.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `account_number` | `Keep` | Low | 8 | Referenced in 8 files: ProfileController.php, ProjectController.php, UserController.php, profile.blade.php, social_aid_project_detals.blade.php |
| `ifsc_number` | `Keep` | Low | 2 | Referenced in 2 files: ProjectController.php, social_aid_project_detals.blade.php |
| `donor` | `Keep` | Low | 64 | Referenced in 64 files: AdminController.php, ApplicationController.php, DonorController.php, ProjectController.php, CulturalCenterProject.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.family_aid_programmes`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `family_aid_project_id` | `Keep` | Low | 2 | Referenced in 2 files: FamilyAidProgramme.php, FamilyAidProject.php |
| `programme_name` | `Keep` | Low | 6 | Referenced in 6 files: ProjectController.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php, differently_abled.blade.php, family_aid.blade.php |
| `date` | `Keep` | Low | 126 | Referenced in 126 files: EntityChanged.php, LeaveBalanceUpdated.php, ProjectUpdated.php, AdminController.php, LeaveApiController.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `present_ticked` | `Keep` | Low | 9 | Referenced in 9 files: ProjectController.php, DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php |
| `photo_ticked` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `marklist_ticked` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `thanks_letter_ticked` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `report_form_ticked` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `medical_certificate_ticked` | `Keep` | Low | 5 | Referenced in 5 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_pdf.blade.php, differently_abled.blade.php |
| `other_document_ticked` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.family_aid_projects`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_name` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `sponsor` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `agency_project_no` | `Keep` | Low | 38 | Referenced in 38 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php, DifferentlyAbledProject.php |
| `application_id` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `type_of_project` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `stage` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php, DifferentlyAbledProject.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `location` | `Keep` | Low | 58 | Referenced in 58 files: AdminController.php, ApplicationController.php, ProjectController.php, AllocateAnnualLeaveJob.php, CulturalCenterApplication.php |

### Table: `rcfi.general_applications`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `application_type` | `Keep` | Low | 5 | Referenced in 5 files: GeneralApplication.php, general.blade.php, project_detail.blade.php |
| `organization_name` | `Keep` | Low | 2 | Referenced in 2 files: GeneralApplication.php, general.blade.php |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `applicant_name` | `Keep` | Low | 66 | Referenced in 66 files: ApplicationCreated.php, AdminController.php, ApplicationController.php, ProjectController.php, ListensForEntityChanges.php |
| `age` | `Keep` | Low | 141 | Referenced in 141 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php |
| `sex` | `Keep` | Low | 5 | Referenced in 5 files: GeneralApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, house.blade.php, general.blade.php |
| `status_of_applicant` | `Keep` | Low | 2 | Referenced in 2 files: GeneralApplication.php, general.blade.php |
| `education` | `Keep` | Low | 26 | Referenced in 26 files: AdminController.php, ApplicationController.php, ProjectController.php, EducationCenterApplication.php, EducationCenterProject.php |
| `num_earning_members` | `Keep` | Low | 2 | Referenced in 2 files: GeneralApplication.php, general.blade.php |
| `applying_for` | `Keep` | Low | 4 | Referenced in 4 files: GeneralApplication.php, general.blade.php, project_detail.blade.php |
| `monthly_income_detail` | `Keep` | Low | 2 | Referenced in 2 files: GeneralApplication.php, general.blade.php |
| `recommended_by` | `Keep` | Low | 2 | Referenced in 2 files: GeneralApplication.php, general.blade.php |
| `recommended_phone` | `Keep` | Low | 2 | Referenced in 2 files: GeneralApplication.php, general.blade.php |
| `office_application_type` | `Keep` | Low | 5 | Referenced in 5 files: GeneralApplication.php, general.blade.php, project_detail.blade.php |
| `amount_requested` | `Keep` | Low | 43 | Referenced in 43 files: ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `recommendation_name` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization_other` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_phone` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_position` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `rejected_reason` | `Keep` | Low | 20 | Referenced in 20 files: ApplicationController.php, HasCategoryMeta.php, base_table.blade.php, cultural_center.blade.php, differently_abled.blade.php |
| `cluster_id` | `Keep` | Low | 15 | Referenced in 15 files: ApplicationController.php, Cluster.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `agency_number` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `agency_name` | `Keep` | Low | 24 | Referenced in 24 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, projects_list.blade.php, social_aid_project_detals.blade.php |
| `application_date` | `Keep` | Low | 10 | Referenced in 10 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, differently_abled.blade.php, drinking_water_group.blade.php |
| `whatsapp_number` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, social_aid_project_detals.blade.php, address_form_fields.blade.php, orphan_care.blade.php, social_aid_project_pdf.blade.php |
| `current_beneficiaries` | `Keep` | Low | 4 | Referenced in 4 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `contact_email` | `Keep` | Low | 5 | Referenced in 5 files: AdminController.php, ApplicationController.php, ProjectController.php, base_table.blade.php, ApplicationAddressTest.php |
| `additional_note` | `Keep` | Low | 11 | Referenced in 11 files: ApplicationController.php, ProjectController.php, HasCategoryMeta.php, social_aid_project_detals.blade.php, cultural_center.blade.php |
| `gender` | `Keep` | Low | 29 | Referenced in 29 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `father_name` | `Keep` | Low | 30 | Referenced in 30 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `mother_name` | `Keep` | Low | 29 | Referenced in 29 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `married` | `Keep` | Low | 9 | Referenced in 9 files: ProjectController.php, HouseApplication.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, house.blade.php |
| `male_family_members` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `female_family_members` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `total_family_members` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `occupation` | `Keep` | Low | 7 | Referenced in 7 files: FamilyAidApplication.php, HouseApplication.php, house.blade.php, social_aid_project_detals.blade.php, family_aid.blade.php |
| `monthly_income` | `Keep` | Low | 18 | Referenced in 18 files: ProjectController.php, DifferentlyAbledApplication.php, DrinkingWaterIndividualApplication.php, FamilyAidApplication.php, GeneralApplication.php |
| `other_income` | `Keep` | Low | 6 | Referenced in 6 files: FamilyAidApplication.php, HouseApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php, house.blade.php |
| `health_status` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, HouseApplication.php, OrphanCareApplication.php |
| `accommodation_details` | `Keep` | Low | 4 | Referenced in 4 files: HouseApplication.php, house.blade.php |
| `general_app_status` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `office_app_type` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `block` | `Keep` | Low | 59 | Referenced in 59 files: HasProjectColumns.php, applications.blade.php, donors.blade.php, leave_requests.blade.php, profile.blade.php |
| `ward` | `Keep` | Low | 23 | Referenced in 23 files: LeaveRequestController.php, AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php, RoleDashboard.php, LeaveBalance.php |
| `panchayat_municipality_corporation` | `Keep` | Low | 1 | Referenced in 1 files: general.blade.php |
| `recommender_name` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org_other` | `Keep` | Low | 19 | Referenced in 19 files: HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `recommender_phone` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_position` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `locality_pin_code` | `Keep` | Low | 26 | Referenced in 26 files: ApplicationController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_place` | `Keep` | Low | 23 | Referenced in 23 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_village` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_post` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayath` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayat` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_district` | `Keep` | Low | 44 | Referenced in 44 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_state` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `submitted_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `received_support_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `financial_support_purpose` | `Keep` | Low | 6 | Referenced in 6 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php |
| `permitted_type` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, hospital_clinics.blade.php |
| `house_name` | `Keep` | Low | 27 | Referenced in 27 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `pin_code` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php, CulturalCenterApplication.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `village` | `Keep` | Low | 53 | Referenced in 53 files: AdminController.php, ApplicationController.php, ClusterController.php, ProjectController.php, Cluster.php |
| `post_office` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, FamilyAidApplication.php, HasCategoryMeta.php |
| `panchayat` | `Keep` | Low | 48 | Referenced in 48 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `district` | `Keep` | Low | 69 | Referenced in 69 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `state` | `Keep` | Low | 70 | Referenced in 70 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `contact_number_1` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `contact_number_2` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |

### Table: `rcfi.general_projects`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_name` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `sponsor` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `project_spec` | `Keep` | Low | 26 | Referenced in 26 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `agency_project_no` | `Keep` | Low | 38 | Referenced in 38 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php, DifferentlyAbledProject.php |
| `donor_id` | `Keep` | Low | 25 | Referenced in 25 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `project_manager_id` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `engineer_id` | `Keep` | Low | 29 | Referenced in 29 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `application_id` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `available_budget` | `Keep` | Low | 27 | Referenced in 27 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `type_of_project` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `theme` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, ThemeController.php, Subtheme.php, Theme.php |
| `subtheme` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, ProjectController.php, ThemeController.php, Subtheme.php, Theme.php |
| `activity` | `Keep` | Low | 24 | Referenced in 24 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `stage` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php, DifferentlyAbledProject.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `completion_details` | `Keep` | Low | 26 | Referenced in 26 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `community_contributions` | `Keep` | Low | 24 | Referenced in 24 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `materials` | `Keep` | Low | 13 | Referenced in 13 files: ProjectController.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `expenses` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.hospital_clinic_applications`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `applicant_name` | `Keep` | Low | 66 | Referenced in 66 files: ApplicationCreated.php, AdminController.php, ApplicationController.php, ProjectController.php, ListensForEntityChanges.php |
| `committee_name` | `Keep` | Low | 29 | Referenced in 29 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `reg_number` | `Keep` | Low | 28 | Referenced in 28 files: AdminController.php, ApplicationController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php |
| `year` | `Keep` | Low | 65 | Referenced in 65 files: AdminController.php, LeaveApiController.php, LeaveRequestController.php, AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php |
| `mahallu_name` | `Keep` | Low | 22 | Referenced in 22 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_pin_code` | `Keep` | Low | 26 | Referenced in 26 files: ApplicationController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_village` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_post` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayath` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_district` | `Keep` | Low | 44 | Referenced in 44 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_state` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `families_in_mahallu` | `Keep` | Low | 21 | Referenced in 21 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `site_has_building` | `Keep` | Low | 19 | Referenced in 19 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `status_of_current_building` | `Keep` | Low | 19 | Referenced in 19 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `status_of_current_building_other` | `Keep` | Low | 12 | Referenced in 12 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `requirement` | `Keep` | Low | 17 | Referenced in 17 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, cultural_center.blade.php, education_center.blade.php |
| `building_area_sq` | `Keep` | Low | 23 | Referenced in 23 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, HouseApplication.php, ShopOtherApplication.php |
| `area` | `Keep` | Low | 54 | Referenced in 54 files: CulturalCenterApplication.php, EducationCenterApplication.php, FamilyAidApplication.php, HospitalClinicApplication.php, HouseApplication.php |
| `amount_requested` | `Keep` | Low | 43 | Referenced in 43 files: ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `is_pharmacy` | `Keep` | Low | 4 | Referenced in 4 files: HospitalClinicApplication.php, hospital_clinics.blade.php |
| `legal_approvals_status` | `Keep` | Low | 24 | Referenced in 24 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, HouseApplication.php, ShopOtherApplication.php |
| `permitted_type` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, hospital_clinics.blade.php |
| `project_area` | `Keep` | Low | 10 | Referenced in 10 files: HospitalClinicApplication.php, ShopOtherApplication.php, general.blade.php, hospital_clinics.blade.php, shops_others.blade.php |
| `num_beds` | `Keep` | Low | 4 | Referenced in 4 files: HospitalClinicApplication.php, hospital_clinics.blade.php |
| `recommendation_name` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization_other` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_phone` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_position` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `rejected_reason` | `Keep` | Low | 20 | Referenced in 20 files: ApplicationController.php, HasCategoryMeta.php, base_table.blade.php, cultural_center.blade.php, differently_abled.blade.php |
| `cluster_id` | `Keep` | Low | 15 | Referenced in 15 files: ApplicationController.php, Cluster.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `agency_number` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `agency_name` | `Keep` | Low | 24 | Referenced in 24 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, projects_list.blade.php, social_aid_project_detals.blade.php |
| `application_date` | `Keep` | Low | 10 | Referenced in 10 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, differently_abled.blade.php, drinking_water_group.blade.php |
| `whatsapp_number` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, social_aid_project_detals.blade.php, address_form_fields.blade.php, orphan_care.blade.php, social_aid_project_pdf.blade.php |
| `current_beneficiaries` | `Keep` | Low | 4 | Referenced in 4 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `contact_email` | `Keep` | Low | 5 | Referenced in 5 files: AdminController.php, ApplicationController.php, ProjectController.php, base_table.blade.php, ApplicationAddressTest.php |
| `additional_note` | `Keep` | Low | 11 | Referenced in 11 files: ApplicationController.php, ProjectController.php, HasCategoryMeta.php, social_aid_project_detals.blade.php, cultural_center.blade.php |
| `locality_place` | `Keep` | Low | 23 | Referenced in 23 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `rooms` | `Keep` | Low | 14 | Referenced in 14 files: EducationCenterApplication.php, ShopOtherApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `recommender_name` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org_other` | `Keep` | Low | 19 | Referenced in 19 files: HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `recommender_phone` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_position` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `locality_panchayat` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `submitted_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `received_support_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `financial_support_purpose` | `Keep` | Low | 6 | Referenced in 6 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php |
| `pin_code` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php, CulturalCenterApplication.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `village` | `Keep` | Low | 53 | Referenced in 53 files: AdminController.php, ApplicationController.php, ClusterController.php, ProjectController.php, Cluster.php |
| `post_office` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, FamilyAidApplication.php, HasCategoryMeta.php |
| `panchayat` | `Keep` | Low | 48 | Referenced in 48 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `district` | `Keep` | Low | 69 | Referenced in 69 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `state` | `Keep` | Low | 70 | Referenced in 70 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `contact_number_1` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `contact_number_2` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |

### Table: `rcfi.hospital_clinic_projects`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_name` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `sponsor` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `project_spec` | `Keep` | Low | 26 | Referenced in 26 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `agency_project_no` | `Keep` | Low | 38 | Referenced in 38 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php, DifferentlyAbledProject.php |
| `donor_id` | `Keep` | Low | 25 | Referenced in 25 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `project_manager_id` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `engineer_id` | `Keep` | Low | 29 | Referenced in 29 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `application_id` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `available_budget` | `Keep` | Low | 27 | Referenced in 27 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `type_of_project` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `theme` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, ThemeController.php, Subtheme.php, Theme.php |
| `subtheme` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, ProjectController.php, ThemeController.php, Subtheme.php, Theme.php |
| `activity` | `Keep` | Low | 24 | Referenced in 24 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `stage` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php, DifferentlyAbledProject.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `completion_details` | `Keep` | Low | 26 | Referenced in 26 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `community_contributions` | `Keep` | Low | 24 | Referenced in 24 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `materials` | `Keep` | Low | 13 | Referenced in 13 files: ProjectController.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `expenses` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.house_applications`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `applicant_name` | `Keep` | Low | 66 | Referenced in 66 files: ApplicationCreated.php, AdminController.php, ApplicationController.php, ProjectController.php, ListensForEntityChanges.php |
| `age` | `Keep` | Low | 141 | Referenced in 141 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php |
| `father_name` | `Keep` | Low | 30 | Referenced in 30 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `mother_name` | `Keep` | Low | 29 | Referenced in 29 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `gender` | `Keep` | Low | 29 | Referenced in 29 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `married` | `Keep` | Low | 9 | Referenced in 9 files: ProjectController.php, HouseApplication.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, house.blade.php |
| `num_children` | `Keep` | Low | 4 | Referenced in 4 files: HouseApplication.php, house.blade.php |
| `num_male_children` | `Keep` | Low | 3 | Referenced in 3 files: HouseApplication.php, house.blade.php |
| `num_female_children` | `Keep` | Low | 3 | Referenced in 3 files: HouseApplication.php, house.blade.php |
| `has_occupation` | `Keep` | Low | 3 | Referenced in 3 files: HouseApplication.php, house.blade.php |
| `monthly_income` | `Keep` | Low | 18 | Referenced in 18 files: ProjectController.php, DifferentlyAbledApplication.php, DrinkingWaterIndividualApplication.php, FamilyAidApplication.php, GeneralApplication.php |
| `other_income` | `Keep` | Low | 6 | Referenced in 6 files: FamilyAidApplication.php, HouseApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php, house.blade.php |
| `health_status` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, HouseApplication.php, OrphanCareApplication.php |
| `daily_treatment_explanation` | `Keep` | Low | 3 | Referenced in 3 files: HouseApplication.php, house.blade.php |
| `accommodation_details` | `Keep` | Low | 4 | Referenced in 4 files: HouseApplication.php, house.blade.php |
| `own_place` | `Keep` | Low | 7 | Referenced in 7 files: FamilyAidApplication.php, HouseApplication.php, house.blade.php, social_aid_project_detals.blade.php, family_aid.blade.php |
| `own_place_details` | `Keep` | Low | 4 | Referenced in 4 files: HouseApplication.php, house.blade.php |
| `land_type` | `Keep` | Low | 4 | Referenced in 4 files: HouseApplication.php, house.blade.php |
| `desired_model` | `Keep` | Low | 4 | Referenced in 4 files: HouseApplication.php, house.blade.php |
| `building_area_sq` | `Keep` | Low | 23 | Referenced in 23 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, HouseApplication.php, ShopOtherApplication.php |
| `amount_requested` | `Keep` | Low | 43 | Referenced in 43 files: ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `legal_approvals_status` | `Keep` | Low | 24 | Referenced in 24 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, HouseApplication.php, ShopOtherApplication.php |
| `permitted_type` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, hospital_clinics.blade.php |
| `intended_house_form` | `Keep` | Low | 3 | Referenced in 3 files: HouseApplication.php, house.blade.php |
| `office_build_house` | `Keep` | Low | 3 | Referenced in 3 files: HouseApplication.php, house.blade.php |
| `recommendation_name` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization_other` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_phone` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_position` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `rejected_reason` | `Keep` | Low | 20 | Referenced in 20 files: ApplicationController.php, HasCategoryMeta.php, base_table.blade.php, cultural_center.blade.php, differently_abled.blade.php |
| `cluster_id` | `Keep` | Low | 15 | Referenced in 15 files: ApplicationController.php, Cluster.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `agency_number` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `agency_name` | `Keep` | Low | 24 | Referenced in 24 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, projects_list.blade.php, social_aid_project_detals.blade.php |
| `application_date` | `Keep` | Low | 10 | Referenced in 10 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, differently_abled.blade.php, drinking_water_group.blade.php |
| `whatsapp_number` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, social_aid_project_detals.blade.php, address_form_fields.blade.php, orphan_care.blade.php, social_aid_project_pdf.blade.php |
| `current_beneficiaries` | `Keep` | Low | 4 | Referenced in 4 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `contact_email` | `Keep` | Low | 5 | Referenced in 5 files: AdminController.php, ApplicationController.php, ProjectController.php, base_table.blade.php, ApplicationAddressTest.php |
| `additional_note` | `Keep` | Low | 11 | Referenced in 11 files: ApplicationController.php, ProjectController.php, HasCategoryMeta.php, social_aid_project_detals.blade.php, cultural_center.blade.php |
| `children_total` | `Keep` | Low | 6 | Referenced in 6 files: FamilyAidApplication.php, house.blade.php, social_aid_project_detals.blade.php, family_aid.blade.php |
| `children_male` | `Keep` | Low | 5 | Referenced in 5 files: FamilyAidApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php, house.blade.php |
| `children_female` | `Keep` | Low | 5 | Referenced in 5 files: FamilyAidApplication.php, social_aid_project_detals.blade.php, family_aid.blade.php, house.blade.php |
| `occupation` | `Keep` | Low | 7 | Referenced in 7 files: FamilyAidApplication.php, HouseApplication.php, house.blade.php, social_aid_project_detals.blade.php, family_aid.blade.php |
| `house_type` | `Keep` | Low | 7 | Referenced in 7 files: ProjectController.php, OrphanCareApplication.php, house.blade.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `permission` | `Keep` | Low | 6 | Referenced in 6 files: DrinkingWaterGroupApplication.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, hospital_clinics.blade.php, shops_others.blade.php |
| `recommender_name` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org_other` | `Keep` | Low | 19 | Referenced in 19 files: HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `recommender_phone` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_position` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `locality_pin_code` | `Keep` | Low | 26 | Referenced in 26 files: ApplicationController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_place` | `Keep` | Low | 23 | Referenced in 23 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_village` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_post` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayath` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayat` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_district` | `Keep` | Low | 44 | Referenced in 44 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_state` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `submitted_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `received_support_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `financial_support_purpose` | `Keep` | Low | 6 | Referenced in 6 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php |
| `house_name` | `Keep` | Low | 27 | Referenced in 27 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `pin_code` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php, CulturalCenterApplication.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `village` | `Keep` | Low | 53 | Referenced in 53 files: AdminController.php, ApplicationController.php, ClusterController.php, ProjectController.php, Cluster.php |
| `post_office` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, FamilyAidApplication.php, HasCategoryMeta.php |
| `panchayat` | `Keep` | Low | 48 | Referenced in 48 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `district` | `Keep` | Low | 69 | Referenced in 69 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `state` | `Keep` | Low | 70 | Referenced in 70 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `contact_number_1` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `contact_number_2` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |

### Table: `rcfi.house_projects`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_name` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `sponsor` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `project_spec` | `Keep` | Low | 26 | Referenced in 26 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `agency_project_no` | `Keep` | Low | 38 | Referenced in 38 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php, DifferentlyAbledProject.php |
| `donor_id` | `Keep` | Low | 25 | Referenced in 25 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `project_manager_id` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `engineer_id` | `Keep` | Low | 29 | Referenced in 29 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `application_id` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `available_budget` | `Keep` | Low | 27 | Referenced in 27 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `type_of_project` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `theme` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, ThemeController.php, Subtheme.php, Theme.php |
| `subtheme` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, ProjectController.php, ThemeController.php, Subtheme.php, Theme.php |
| `activity` | `Keep` | Low | 24 | Referenced in 24 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `stage` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php, DifferentlyAbledProject.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `completion_details` | `Keep` | Low | 26 | Referenced in 26 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `community_contributions` | `Keep` | Low | 24 | Referenced in 24 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `materials` | `Keep` | Low | 13 | Referenced in 13 files: ProjectController.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `expenses` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.jobs`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `queue` | `Keep` | Low | 2 | Referenced in 2 files: leave-admin-dashboard.blade.php, role-dashboard.blade.php |
| `payload` | `Keep` | Low | 16 | Referenced in 16 files: EntityChanged.php, ProjectUpdated.php, ListensForEntityChanges.php, RoleDashboard.php, BroadcastsChanges.php |
| `attempts` | `Keep` | Low | 1 | Referenced in 1 files: ApplicationApprovalPermissionsTest.php |
| `reserved_at` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `available_at` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.job_batches`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `total_jobs` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `pending_jobs` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `failed_jobs` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `failed_job_ids` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `options` | `Keep` | Low | 25 | Referenced in 25 files: ApplicationController.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `cancelled_at` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `finished_at` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `rcfi.leave_accrual_log`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `user_id` | `Keep` | Low | 36 | Referenced in 36 files: LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php, LeaveRequestRejected.php, LeaveRequestSubmitted.php |
| `leave_type_id` | `Keep` | Low | 19 | Referenced in 19 files: LeaveApiController.php, LeaveRequestController.php, StoreLeaveRequestRequest.php, AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php |
| `accrual_month` | `Keep` | Low | 3 | Referenced in 3 files: AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php, LeaveAccrualLog.php |
| `accrual_year` | `Keep` | Low | 3 | Referenced in 3 files: AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php, LeaveAccrualLog.php |
| `days_accrued` | `Keep` | Low | 3 | Referenced in 3 files: AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php, LeaveAccrualLog.php |
| `accrued_on` | `Keep` | Low | 3 | Referenced in 3 files: AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php, LeaveAccrualLog.php |

### Table: `rcfi.leave_balances`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `user_id` | `Keep` | Low | 36 | Referenced in 36 files: LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php, LeaveRequestRejected.php, LeaveRequestSubmitted.php |
| `leave_type_id` | `Keep` | Low | 19 | Referenced in 19 files: LeaveApiController.php, LeaveRequestController.php, StoreLeaveRequestRequest.php, AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php |
| `year` | `Keep` | Low | 65 | Referenced in 65 files: AdminController.php, LeaveApiController.php, LeaveRequestController.php, AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php |
| `allocated_days` | `Keep` | Low | 10 | Referenced in 10 files: LeaveRequestController.php, AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php, RoleDashboard.php, LeaveBalance.php |
| `used_days` | `Keep` | Low | 11 | Referenced in 11 files: LeaveRequestController.php, AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php, RoleDashboard.php, LeaveBalance.php |
| `carried_forward_days` | `Keep` | Low | 9 | Referenced in 9 files: LeaveRequestController.php, AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php, RoleDashboard.php, LeaveBalance.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.leave_requests`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `user_id` | `Keep` | Low | 36 | Referenced in 36 files: LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php, LeaveRequestRejected.php, LeaveRequestSubmitted.php |
| `leave_type_id` | `Keep` | Low | 19 | Referenced in 19 files: LeaveApiController.php, LeaveRequestController.php, StoreLeaveRequestRequest.php, AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php |
| `start_date` | `Keep` | Low | 15 | Referenced in 15 files: LeaveApiController.php, LeaveRequestController.php, UserController.php, StoreLeaveRequestRequest.php, LeaveAdminDashboard.php |
| `end_date` | `Keep` | Low | 15 | Referenced in 15 files: LeaveApiController.php, LeaveRequestController.php, UserController.php, StoreLeaveRequestRequest.php, LeaveAdminDashboard.php |
| `total_days` | `Keep` | Low | 8 | Referenced in 8 files: LeaveRequestController.php, LeaveAdminDashboard.php, LeaveRequest.php, LeaveService.php, leave_requests.blade.php |
| `reason` | `Keep` | Low | 47 | Referenced in 47 files: LeaveApiController.php, ApplicationController.php, LeaveRequestController.php, ProjectController.php, UserController.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `applied_on` | `Keep` | Low | 5 | Referenced in 5 files: LeaveRequestController.php, LeaveAdminDashboard.php, LeaveRequest.php, LeaveService.php, leave-admin-dashboard.blade.php |
| `approved_by` | `Keep` | Low | 2 | Referenced in 2 files: LeaveRequest.php, LeaveService.php |
| `approved_on` | `Keep` | Low | 2 | Referenced in 2 files: LeaveRequest.php, LeaveService.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.leave_types`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `leave_code` | `Keep` | Low | 13 | Referenced in 13 files: LeaveRequestController.php, AccrueCasualLeaveJob.php, LeaveAdminDashboard.php, LeaveType.php, User.php |
| `leave_name` | `Keep` | Low | 11 | Referenced in 11 files: LeaveRequestController.php, LeaveAdminDashboard.php, LeaveType.php, User.php, leave_requests.blade.php |
| `description` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledApplication.php, FamilyAidApplication.php, LeaveType.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `accrual_type` | `Keep` | Low | 12 | Referenced in 12 files: LeaveRequestController.php, AllocateAnnualLeaveJob.php, LeaveDashboard.php, RoleDashboard.php, LeaveBalance.php |
| `max_days_per_year` | `Keep` | Low | 8 | Referenced in 8 files: LeaveRequestController.php, AllocateAnnualLeaveJob.php, LeaveAdminDashboard.php, RoleDashboard.php, LeaveType.php |
| `max_days_lifetime` | `Keep` | Low | 7 | Referenced in 7 files: LeaveRequestController.php, LeaveAdminDashboard.php, RoleDashboard.php, LeaveType.php, LeaveService.php |
| `carry_forward` | `Keep` | Low | 2 | Referenced in 2 files: LeaveType.php, LeaveTypeSeeder.php |
| `applicable_gender` | `Keep` | Low | 4 | Referenced in 4 files: LeaveType.php, User.php, leave-admin-dashboard.blade.php, LeaveTypeSeeder.php |
| `requires_marital_status` | `Keep` | Low | 4 | Referenced in 4 files: LeaveType.php, User.php, leave-admin-dashboard.blade.php, LeaveTypeSeeder.php |
| `min_service_years` | `Keep` | Low | 4 | Referenced in 4 files: LeaveAdminDashboard.php, LeaveType.php, User.php, LeaveTypeSeeder.php |
| `is_lifetime_only` | `Keep` | Low | 3 | Referenced in 3 files: LeaveType.php, User.php, LeaveTypeSeeder.php |
| `is_active` | `Keep` | Low | 14 | Referenced in 14 files: LeaveApiController.php, LeaveRequestController.php, AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php, LeaveAdminDashboard.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.migrations`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `migration` | `Keep` | Low | 1 | Referenced in 1 files: HasProjectColumns.php |
| `batch` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `rcfi.notifications`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `title` | `Keep` | Low | 90 | Referenced in 90 files: ApplicationController.php, LeaveRequestController.php, ProjectController.php, UserController.php, LeaveType.php |
| `message` | `Keep` | Low | 26 | Referenced in 26 files: LeaveApiController.php, ApplicationController.php, ProjectController.php, Notification.php, cultural_center.blade.php |
| `url` | `Keep` | Low | 63 | Referenced in 63 files: AdminController.php, ApplicationController.php, DonorController.php, ProjectController.php, UserController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.notification_recipients`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `notification_id` | `Keep` | Low | 3 | Referenced in 3 files: Notification.php, NotificationRecipient.php, NotificationTest.php |
| `user_id` | `Keep` | Low | 36 | Referenced in 36 files: LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php, LeaveRequestRejected.php, LeaveRequestSubmitted.php |
| `is_read` | `Keep` | Low | 5 | Referenced in 5 files: Notification.php, NotificationRecipient.php, header.blade.php, web.php, NotificationTest.php |
| `read_at` | `Keep` | Low | 3 | Referenced in 3 files: NotificationRecipient.php, web.php, NotificationTest.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.orphan_care_applications`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `amount_requested` | `Keep` | Low | 43 | Referenced in 43 files: ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `applicant_name` | `Keep` | Low | 66 | Referenced in 66 files: ApplicationCreated.php, AdminController.php, ApplicationController.php, ProjectController.php, ListensForEntityChanges.php |
| `gender` | `Keep` | Low | 29 | Referenced in 29 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `father_name` | `Keep` | Low | 30 | Referenced in 30 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `grandfather_name` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php, social_aid_project_pdf.blade.php |
| `student_photo` | `Keep` | Low | 9 | Referenced in 9 files: AdminController.php, ApplicationController.php, ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php |
| `mother_name` | `Keep` | Low | 29 | Referenced in 29 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `mothers_father_name` | `Keep` | Low | 4 | Referenced in 4 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `dob` | `Keep` | Low | 16 | Referenced in 16 files: ProjectController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `age` | `Keep` | Low | 141 | Referenced in 141 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php |
| `aadhar_number` | `Keep` | Low | 16 | Referenced in 16 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `guardian_name` | `Keep` | Low | 9 | Referenced in 9 files: ProjectController.php, DifferentlyAbledApplication.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `guardian_relation` | `Keep` | Low | 8 | Referenced in 8 files: ProjectController.php, DifferentlyAbledApplication.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `father_death_date` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php, ApplicationAddressTest.php |
| `father_death_cause` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php, ApplicationAddressTest.php |
| `mother_alive_status` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php, ApplicationAddressTest.php |
| `mother_remarried_status` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php, ApplicationAddressTest.php |
| `mother_death_date` | `Keep` | Low | 4 | Referenced in 4 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `mother_death_cause` | `Keep` | Low | 4 | Referenced in 4 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `siblings_male` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php, ApplicationAddressTest.php |
| `siblings_female` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php, ApplicationAddressTest.php |
| `siblings_total` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php, ApplicationAddressTest.php |
| `current_beneficiaries` | `Keep` | Low | 4 | Referenced in 4 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `monthly_income` | `Keep` | Low | 18 | Referenced in 18 files: ProjectController.php, DifferentlyAbledApplication.php, DrinkingWaterIndividualApplication.php, FamilyAidApplication.php, GeneralApplication.php |
| `monthly_expense` | `Keep` | Low | 6 | Referenced in 6 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php, social_aid_project_pdf.blade.php |
| `sponsorship_details` | `Keep` | Low | 4 | Referenced in 4 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `house_type` | `Keep` | Low | 7 | Referenced in 7 files: ProjectController.php, OrphanCareApplication.php, house.blade.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `school_name` | `Keep` | Low | 6 | Referenced in 6 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php, social_aid_project_pdf.blade.php |
| `school_class` | `Keep` | Low | 6 | Referenced in 6 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php, social_aid_project_pdf.blade.php |
| `madrassa_name` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php, social_aid_project_pdf.blade.php |
| `madrassa_class` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php, social_aid_project_pdf.blade.php |
| `not_studying_reason` | `Keep` | Low | 7 | Referenced in 7 files: ProjectController.php, DifferentlyAbledApplication.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, differently_abled.blade.php |
| `health_status` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, HouseApplication.php, OrphanCareApplication.php |
| `recommender_name` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org_other` | `Keep` | Low | 19 | Referenced in 19 files: HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `recommender_phone` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_position` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `cluster_id` | `Keep` | Low | 15 | Referenced in 15 files: ApplicationController.php, Cluster.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `agency_number` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `agency_name` | `Keep` | Low | 24 | Referenced in 24 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, projects_list.blade.php, social_aid_project_detals.blade.php |
| `application_date` | `Keep` | Low | 10 | Referenced in 10 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, differently_abled.blade.php, drinking_water_group.blade.php |
| `recommendation_name` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization_other` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_phone` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_position` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `rejected_reason` | `Keep` | Low | 20 | Referenced in 20 files: ApplicationController.php, HasCategoryMeta.php, base_table.blade.php, cultural_center.blade.php, differently_abled.blade.php |
| `whatsapp_number` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, social_aid_project_detals.blade.php, address_form_fields.blade.php, orphan_care.blade.php, social_aid_project_pdf.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `sponsor_status` | `Keep` | Low | 16 | Referenced in 16 files: ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `contact_email` | `Keep` | Low | 5 | Referenced in 5 files: AdminController.php, ApplicationController.php, ProjectController.php, base_table.blade.php, ApplicationAddressTest.php |
| `additional_note` | `Keep` | Low | 11 | Referenced in 11 files: ApplicationController.php, ProjectController.php, HasCategoryMeta.php, social_aid_project_detals.blade.php, cultural_center.blade.php |
| `town` | `Keep` | Low | 27 | Referenced in 27 files: ApplicationController.php, ProjectController.php, HasCategoryMeta.php, social_aid_project_detals.blade.php, cultural_center.blade.php |
| `locality_pin_code` | `Keep` | Low | 26 | Referenced in 26 files: ApplicationController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_place` | `Keep` | Low | 23 | Referenced in 23 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_village` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_post` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayath` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayat` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_district` | `Keep` | Low | 44 | Referenced in 44 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_state` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `submitted_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `received_support_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `financial_support_purpose` | `Keep` | Low | 6 | Referenced in 6 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php |
| `permitted_type` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, hospital_clinics.blade.php |
| `house_name` | `Keep` | Low | 27 | Referenced in 27 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `pin_code` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php, CulturalCenterApplication.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `village` | `Keep` | Low | 53 | Referenced in 53 files: AdminController.php, ApplicationController.php, ClusterController.php, ProjectController.php, Cluster.php |
| `post_office` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, FamilyAidApplication.php, HasCategoryMeta.php |
| `panchayat` | `Keep` | Low | 48 | Referenced in 48 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `district` | `Keep` | Low | 69 | Referenced in 69 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `state` | `Keep` | Low | 70 | Referenced in 70 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `contact_number_1` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `contact_number_2` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |

### Table: `rcfi.orphan_care_funds`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `agency_project_no` | `Keep` | Low | 38 | Referenced in 38 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php, DifferentlyAbledProject.php |
| `date` | `Keep` | Low | 126 | Referenced in 126 files: EntityChanged.php, LeaveBalanceUpdated.php, ProjectUpdated.php, AdminController.php, LeaveApiController.php |
| `amount` | `Keep` | Low | 55 | Referenced in 55 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php |
| `account_name` | `Keep` | Low | 3 | Referenced in 3 files: ProjectController.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `account_number` | `Keep` | Low | 8 | Referenced in 8 files: ProfileController.php, ProjectController.php, UserController.php, profile.blade.php, social_aid_project_detals.blade.php |
| `ifsc_number` | `Keep` | Low | 2 | Referenced in 2 files: ProjectController.php, social_aid_project_detals.blade.php |
| `donor` | `Keep` | Low | 64 | Referenced in 64 files: AdminController.php, ApplicationController.php, DonorController.php, ProjectController.php, CulturalCenterProject.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.orphan_care_programmes`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `orphan_care_project_id` | `Keep` | Low | 2 | Referenced in 2 files: OrphanCareProgramme.php, OrphanCareProject.php |
| `programme_name` | `Keep` | Low | 6 | Referenced in 6 files: ProjectController.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php, differently_abled.blade.php, family_aid.blade.php |
| `date` | `Keep` | Low | 126 | Referenced in 126 files: EntityChanged.php, LeaveBalanceUpdated.php, ProjectUpdated.php, AdminController.php, LeaveApiController.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `present_ticked` | `Keep` | Low | 9 | Referenced in 9 files: ProjectController.php, DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php |
| `photo_ticked` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `marklist_ticked` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `thanks_letter_ticked` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `report_form_ticked` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `medical_certificate_ticked` | `Keep` | Low | 5 | Referenced in 5 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_pdf.blade.php, differently_abled.blade.php |
| `other_document_ticked` | `Keep` | Low | 8 | Referenced in 8 files: DifferentlyAbledProgramme.php, FamilyAidProgramme.php, OrphanCareProgramme.php, social_aid_project_detals.blade.php, social_aid_project_pdf.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.orphan_care_projects`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_name` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `sponsor` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `agency_project_no` | `Keep` | Low | 38 | Referenced in 38 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php, DifferentlyAbledProject.php |
| `application_id` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `type_of_project` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `stage` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php, DifferentlyAbledProject.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `location` | `Keep` | Low | 58 | Referenced in 58 files: AdminController.php, ApplicationController.php, ProjectController.php, AllocateAnnualLeaveJob.php, CulturalCenterApplication.php |

### Table: `rcfi.password_reset_tokens`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `email` | `Keep` | Low | 36 | Referenced in 36 files: AdminController.php, ApplicationController.php, AuthController.php, ContractorController.php, DonorController.php |
| `token` | `Keep` | Low | 34 | Referenced in 34 files: AuthController.php, User.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.pincode_master`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `circle_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `region_name` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `division_name` | `Keep` | Low | 1 | Referenced in 1 files: web.php |
| `office_name` | `Keep` | Low | 1 | Referenced in 1 files: web.php |
| `pincode` | `Keep` | Low | 16 | Referenced in 16 files: HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `office_type` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `delivery_status` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `district` | `Keep` | Low | 69 | Referenced in 69 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `state_name` | `Keep` | Low | 1 | Referenced in 1 files: web.php |
| `latitude` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `longitude` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `rcfi.profiles`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `user_id` | `Keep` | Low | 36 | Referenced in 36 files: LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php, LeaveRequestRejected.php, LeaveRequestSubmitted.php |
| `photo` | `Keep` | Low | 40 | Referenced in 40 files: AdminController.php, ApplicationController.php, LeaveRequestController.php, ProfileController.php, ProjectController.php |
| `address` | `Keep` | Low | 40 | Referenced in 40 files: AdminController.php, ApplicationController.php, ContractorController.php, ProfileController.php, ProjectController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.project_community_contributions`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_type` | `Keep` | Low | 39 | Referenced in 39 files: AdminController.php, ProjectController.php, CulturalCenterApplication.php, EducationCenterApplication.php, ProjectCommunityContribution.php |
| `item` | `Keep` | Low | 82 | Referenced in 82 files: AdminController.php, ApplicationController.php, ProjectController.php, ProjectCommunityContribution.php, HasProjectColumns.php |
| `amount` | `Keep` | Low | 55 | Referenced in 55 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.project_completion_details`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_type` | `Keep` | Low | 39 | Referenced in 39 files: AdminController.php, ProjectController.php, CulturalCenterApplication.php, EducationCenterApplication.php, ProjectCommunityContribution.php |
| `total_project_cost` | `Keep` | Low | 15 | Referenced in 15 files: AdminController.php, ProjectController.php, ProjectCompletionDetail.php, HasProjectColumns.php, cultural_center.blade.php |
| `total_amount` | `Keep` | Low | 16 | Referenced in 16 files: AdminController.php, ProjectController.php, ProjectCompletionDetail.php, HasProjectColumns.php, cultural_center.blade.php |
| `amount_paid_by_donor` | `Keep` | Low | 15 | Referenced in 15 files: AdminController.php, ProjectController.php, ProjectCompletionDetail.php, HasProjectColumns.php, cultural_center.blade.php |
| `community_contribution` | `Keep` | Low | 28 | Referenced in 28 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `any_other` | `Keep` | Low | 15 | Referenced in 15 files: AdminController.php, ProjectController.php, ProjectCompletionDetail.php, HasProjectColumns.php, cultural_center.blade.php |
| `deductions` | `Keep` | Low | 16 | Referenced in 16 files: AdminController.php, ProjectController.php, ProjectCompletionDetail.php, HasProjectColumns.php, cultural_center.blade.php |
| `handover_date` | `Keep` | Low | 2 | Referenced in 2 files: ProjectCompletionDetail.php, HasProjectColumns.php |
| `handover_remarks` | `Keep` | Low | 2 | Referenced in 2 files: ProjectCompletionDetail.php, HasProjectColumns.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.project_contractors`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_type` | `Keep` | Low | 39 | Referenced in 39 files: AdminController.php, ProjectController.php, CulturalCenterApplication.php, EducationCenterApplication.php, ProjectCommunityContribution.php |
| `contractor_id` | `Keep` | Low | 15 | Referenced in 15 files: AdminController.php, ProjectController.php, Contractor.php, ProjectContractor.php, HasProjectColumns.php |
| `contractor_name` | `Keep` | Low | 12 | Referenced in 12 files: ProjectContractor.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `phone` | `Keep` | Low | 52 | Referenced in 52 files: AdminController.php, ContractorController.php, DonorController.php, ProjectController.php, Contractor.php |
| `company_name` | `Keep` | Low | 16 | Referenced in 16 files: ContractorController.php, Contractor.php, ProjectContractor.php, HasProjectColumns.php, contractors.blade.php |
| `address` | `Keep` | Low | 40 | Referenced in 40 files: AdminController.php, ApplicationController.php, ContractorController.php, ProfileController.php, ProjectController.php |
| `type_of_contract` | `Keep` | Low | 13 | Referenced in 13 files: ProjectController.php, ProjectContractor.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `purpose_of_contract` | `Keep` | Low | 13 | Referenced in 13 files: ProjectController.php, ProjectContractor.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.project_documents`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_type` | `Keep` | Low | 39 | Referenced in 39 files: AdminController.php, ProjectController.php, CulturalCenterApplication.php, EducationCenterApplication.php, ProjectCommunityContribution.php |
| `land_document` | `Keep` | Low | 3 | Referenced in 3 files: AdminController.php, ProjectDocument.php, ApplicationApprovalPermissionsTest.php |
| `land_document_ticked_at` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `possession_certificate` | `Keep` | Low | 2 | Referenced in 2 files: AdminController.php, ProjectDocument.php |
| `possession_certificate_ticked_at` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `tax_receipt` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `tax_receipt_ticked_at` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `recommendation_letter` | `Keep` | Low | 2 | Referenced in 2 files: AdminController.php, ProjectDocument.php |
| `recommendation_letter_ticked_at` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `committee_minutes` | `Keep` | Low | 2 | Referenced in 2 files: AdminController.php, ProjectDocument.php |
| `committee_minutes_ticked_at` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `permit_copy` | `Keep` | Low | 2 | Referenced in 2 files: AdminController.php, ProjectDocument.php |
| `permit_copy_ticked_at` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `plan` | `Keep` | Low | 10 | Referenced in 10 files: AdminController.php, FamilyAidApplication.php, HouseApplication.php, ProjectDocument.php, profile.blade.php |
| `plan_ticked_at` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `tender_schedule_sheet` | `Keep` | Low | 2 | Referenced in 2 files: AdminController.php, ProjectDocument.php |
| `tender_schedule_sheet_ticked_at` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `site_study` | `Keep` | Low | 12 | Referenced in 12 files: AdminController.php, ProjectController.php, ProjectDocument.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `site_study_ticked_at` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `quotations` | `Keep` | Low | 2 | Referenced in 2 files: AdminController.php, ProjectDocument.php |
| `quotations_ticked_at` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `quotations_approval_form` | `Keep` | Low | 2 | Referenced in 2 files: AdminController.php, ProjectDocument.php |
| `quotations_approval_form_ticked_at` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `work_order_letter` | `Keep` | Low | 2 | Referenced in 2 files: AdminController.php, ProjectDocument.php |
| `work_order_letter_ticked_at` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `meeting_minutes_copy` | `Keep` | Low | 2 | Referenced in 2 files: AdminController.php, ProjectDocument.php |
| `meeting_minutes_copy_ticked_at` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `agreement_with_contractor` | `Keep` | Low | 2 | Referenced in 2 files: AdminController.php, ProjectDocument.php |
| `agreement_with_contractor_ticked_at` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `agreement_with_committee` | `Keep` | Low | 2 | Referenced in 2 files: AdminController.php, ProjectDocument.php |
| `agreement_with_committee_ticked_at` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `project_summary_form` | `Keep` | Low | 2 | Referenced in 2 files: AdminController.php, ProjectDocument.php |
| `project_summary_form_ticked_at` | `Keep` | Low | 1 | Referenced in 1 files: ProjectDocument.php |
| `completion_certificate` | `Keep` | Low | 13 | Referenced in 13 files: ProjectController.php, ProjectDocument.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `completion_certificate_ticked_at` | `Keep` | Low | 11 | Referenced in 11 files: ProjectDocument.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `measurement_book` | `Keep` | Low | 13 | Referenced in 13 files: ProjectController.php, ProjectDocument.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `measurement_book_ticked_at` | `Keep` | Low | 11 | Referenced in 11 files: ProjectDocument.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `location_map_link` | `Keep` | Low | 13 | Referenced in 13 files: ProjectController.php, ProjectDocument.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |

### Table: `rcfi.project_expenses`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_type` | `Keep` | Low | 39 | Referenced in 39 files: AdminController.php, ProjectController.php, CulturalCenterApplication.php, EducationCenterApplication.php, ProjectCommunityContribution.php |
| `material_index` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, ProjectExpense.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `comm_index` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, ProjectExpense.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `expense_name` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, ProjectExpense.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `quantity` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, ProjectExpense.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `amount` | `Keep` | Low | 55 | Referenced in 55 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php |
| `type` | `Keep` | Low | 127 | Referenced in 127 files: EntityChanged.php, TableRowChanged.php, AdminController.php, LeaveApiController.php, ApplicationController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.project_inspections`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_type` | `Keep` | Low | 39 | Referenced in 39 files: AdminController.php, ProjectController.php, CulturalCenterApplication.php, EducationCenterApplication.php, ProjectCommunityContribution.php |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `designation` | `Keep` | Low | 46 | Referenced in 46 files: ApplicationController.php, ClusterController.php, ContractorController.php, LeaveRequestController.php, ProfileController.php |
| `date` | `Keep` | Low | 126 | Referenced in 126 files: EntityChanged.php, LeaveBalanceUpdated.php, ProjectUpdated.php, AdminController.php, LeaveApiController.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.project_photos`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_type` | `Keep` | Low | 39 | Referenced in 39 files: AdminController.php, ProjectController.php, CulturalCenterApplication.php, EducationCenterApplication.php, ProjectCommunityContribution.php |
| `photos` | `Keep` | Low | 15 | Referenced in 15 files: ApplicationController.php, ProjectController.php, ProjectPhoto.php, HasProjectColumns.php, cultural_center.blade.php |
| `photos_before` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, ProjectPhoto.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `photos_starting` | `Keep` | Low | 12 | Referenced in 12 files: ProjectPhoto.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `photos_inbetween` | `Keep` | Low | 13 | Referenced in 13 files: ProjectController.php, ProjectPhoto.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `photos_after` | `Keep` | Low | 13 | Referenced in 13 files: ProjectController.php, ProjectPhoto.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `photos_banner` | `Keep` | Low | 12 | Referenced in 12 files: ProjectPhoto.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `photos_stone` | `Keep` | Low | 12 | Referenced in 12 files: ProjectPhoto.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `photos_inauguration` | `Keep` | Low | 13 | Referenced in 13 files: ProjectController.php, ProjectPhoto.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.project_site_studies`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_type` | `Keep` | Low | 39 | Referenced in 39 files: AdminController.php, ProjectController.php, CulturalCenterApplication.php, EducationCenterApplication.php, ProjectCommunityContribution.php |
| `report` | `Keep` | Low | 34 | Referenced in 34 files: AdminController.php, LeaveApiController.php, ProjectController.php, LeaveAdminDashboard.php, DifferentlyAbledProgramme.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `file_path` | `Keep` | Low | 10 | Referenced in 10 files: ProjectController.php, ProjectSiteStudy.php, cultural_center.blade.php, drinking_water_group.blade.php, education_center.blade.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `ticked_at` | `Keep` | Low | 16 | Referenced in 16 files: ProjectController.php, ProjectDocument.php, ProjectSiteStudy.php, HasProjectColumns.php, cultural_center.blade.php |
| `created_by` | `Keep` | Low | 2 | Referenced in 2 files: ProjectController.php, ProjectSiteStudy.php |
| `updated_by` | `Keep` | Low | 2 | Referenced in 2 files: ProjectController.php, ProjectSiteStudy.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.project_statuses`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_type` | `Keep` | Low | 39 | Referenced in 39 files: AdminController.php, ProjectController.php, CulturalCenterApplication.php, EducationCenterApplication.php, ProjectCommunityContribution.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `status_custom` | `Keep` | Low | 4 | Referenced in 4 files: ProjectController.php, ProjectStatus.php, HasProjectColumns.php, ProjectStatusDefaultTest.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `coo_approved_at` | `Keep` | Low | 12 | Referenced in 12 files: ProjectController.php, ProjectStatus.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `coo_approver_id` | `Keep` | Low | 3 | Referenced in 3 files: ProjectController.php, ProjectStatus.php, Stage4ApprovalTest.php |
| `coo_remarks` | `Keep` | Low | 12 | Referenced in 12 files: ProjectController.php, ProjectStatus.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |

### Table: `rcfi.sessions`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `user_id` | `Keep` | Low | 36 | Referenced in 36 files: LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php, LeaveRequestRejected.php, LeaveRequestSubmitted.php |
| `ip_address` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `user_agent` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |
| `payload` | `Keep` | Low | 16 | Referenced in 16 files: EntityChanged.php, ProjectUpdated.php, ListensForEntityChanges.php, RoleDashboard.php, BroadcastsChanges.php |
| `last_activity` | `Unused` | Low | 0 | No occurrences found anywhere in app/, views/, routes/, database/, tests/ |

### Table: `rcfi.shop_other_applications`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `applicant_name` | `Keep` | Low | 66 | Referenced in 66 files: ApplicationCreated.php, AdminController.php, ApplicationController.php, ProjectController.php, ListensForEntityChanges.php |
| `committee_name` | `Keep` | Low | 29 | Referenced in 29 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `reg_number` | `Keep` | Low | 28 | Referenced in 28 files: AdminController.php, ApplicationController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php |
| `year` | `Keep` | Low | 65 | Referenced in 65 files: AdminController.php, LeaveApiController.php, LeaveRequestController.php, AccrueCasualLeaveJob.php, AllocateAnnualLeaveJob.php |
| `mahallu_name` | `Keep` | Low | 22 | Referenced in 22 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_pin_code` | `Keep` | Low | 26 | Referenced in 26 files: ApplicationController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_village` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_post` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_panchayath` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `locality_district` | `Keep` | Low | 44 | Referenced in 44 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `locality_state` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `site_has_building` | `Keep` | Low | 19 | Referenced in 19 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `status_of_current_building` | `Keep` | Low | 19 | Referenced in 19 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `status_of_current_building_other` | `Keep` | Low | 12 | Referenced in 12 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `building_area_sq` | `Keep` | Low | 23 | Referenced in 23 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, HouseApplication.php, ShopOtherApplication.php |
| `area` | `Keep` | Low | 54 | Referenced in 54 files: CulturalCenterApplication.php, EducationCenterApplication.php, FamilyAidApplication.php, HospitalClinicApplication.php, HouseApplication.php |
| `amount_requested` | `Keep` | Low | 43 | Referenced in 43 files: ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `families_in_mahallu` | `Keep` | Low | 21 | Referenced in 21 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `legal_approvals_status` | `Keep` | Low | 24 | Referenced in 24 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, HouseApplication.php, ShopOtherApplication.php |
| `permitted_type` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, hospital_clinics.blade.php |
| `project_area` | `Keep` | Low | 10 | Referenced in 10 files: HospitalClinicApplication.php, ShopOtherApplication.php, general.blade.php, hospital_clinics.blade.php, shops_others.blade.php |
| `num_rooms` | `Keep` | Low | 4 | Referenced in 4 files: ShopOtherApplication.php, shops_others.blade.php |
| `office_shop` | `Keep` | Low | 4 | Referenced in 4 files: ShopOtherApplication.php, shops_others.blade.php |
| `recommendation_name` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_organization_other` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommendation_phone` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `rejected_reason` | `Keep` | Low | 20 | Referenced in 20 files: ApplicationController.php, HasCategoryMeta.php, base_table.blade.php, cultural_center.blade.php, differently_abled.blade.php |
| `cluster_id` | `Keep` | Low | 15 | Referenced in 15 files: ApplicationController.php, Cluster.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `agency_number` | `Keep` | Low | 21 | Referenced in 21 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `agency_name` | `Keep` | Low | 24 | Referenced in 24 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, projects_list.blade.php, social_aid_project_detals.blade.php |
| `application_date` | `Keep` | Low | 10 | Referenced in 10 files: ApplicationController.php, ProjectController.php, OrphanCareApplication.php, differently_abled.blade.php, drinking_water_group.blade.php |
| `whatsapp_number` | `Keep` | Low | 5 | Referenced in 5 files: ProjectController.php, social_aid_project_detals.blade.php, address_form_fields.blade.php, orphan_care.blade.php, social_aid_project_pdf.blade.php |
| `current_beneficiaries` | `Keep` | Low | 4 | Referenced in 4 files: ProjectController.php, OrphanCareApplication.php, social_aid_project_detals.blade.php, orphan_care.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `contact_email` | `Keep` | Low | 5 | Referenced in 5 files: AdminController.php, ApplicationController.php, ProjectController.php, base_table.blade.php, ApplicationAddressTest.php |
| `additional_note` | `Keep` | Low | 11 | Referenced in 11 files: ApplicationController.php, ProjectController.php, HasCategoryMeta.php, social_aid_project_detals.blade.php, cultural_center.blade.php |
| `locality_place` | `Keep` | Low | 23 | Referenced in 23 files: AdminController.php, CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php |
| `rooms` | `Keep` | Low | 14 | Referenced in 14 files: EducationCenterApplication.php, ShopOtherApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `recommendation_position` | `Keep` | Low | 36 | Referenced in 36 files: CulturalCenterApplication.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php, DrinkingWaterIndividualApplication.php, EducationCenterApplication.php |
| `recommender_name` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_org_other` | `Keep` | Low | 19 | Referenced in 19 files: HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php, education_center.blade.php |
| `recommender_phone` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `recommender_position` | `Keep` | Low | 22 | Referenced in 22 files: ProjectController.php, OrphanCareApplication.php, HasCategoryMeta.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `locality_panchayat` | `Keep` | Low | 20 | Referenced in 20 files: CulturalCenterApplication.php, EducationCenterApplication.php, HospitalClinicApplication.php, ShopOtherApplication.php, cultural_center.blade.php |
| `submitted_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `received_support_before` | `Keep` | Low | 13 | Referenced in 13 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php, general.blade.php |
| `financial_support_purpose` | `Keep` | Low | 6 | Referenced in 6 files: CulturalCenterApplication.php, EducationCenterApplication.php, cultural_center.blade.php, education_center.blade.php |
| `pin_code` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php, CulturalCenterApplication.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `village` | `Keep` | Low | 53 | Referenced in 53 files: AdminController.php, ApplicationController.php, ClusterController.php, ProjectController.php, Cluster.php |
| `post_office` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, FamilyAidApplication.php, HasCategoryMeta.php |
| `panchayat` | `Keep` | Low | 48 | Referenced in 48 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `district` | `Keep` | Low | 69 | Referenced in 69 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `state` | `Keep` | Low | 70 | Referenced in 70 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `contact_number_1` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |
| `contact_number_2` | `Keep` | Low | 42 | Referenced in 42 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterApplication.php, DrinkingWaterGroupApplication.php |

### Table: `rcfi.shop_other_projects`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `project_id` | `Keep` | Low | 73 | Referenced in 73 files: AdminController.php, ProjectController.php, UserController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `project_name` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php, OrphanCareApplication.php |
| `sponsor` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `project_spec` | `Keep` | Low | 26 | Referenced in 26 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `agency_project_no` | `Keep` | Low | 38 | Referenced in 38 files: AdminController.php, ProjectController.php, DifferentlyAbledApplication.php, DifferentlyAbledFund.php, DifferentlyAbledProject.php |
| `donor_id` | `Keep` | Low | 25 | Referenced in 25 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `project_manager_id` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `engineer_id` | `Keep` | Low | 29 | Referenced in 29 files: AdminController.php, ApplicationController.php, ProjectController.php, UserController.php, RoleDashboard.php |
| `unit` | `Keep` | Low | 37 | Referenced in 37 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php |
| `application_id` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, ApplicationController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php |
| `available_budget` | `Keep` | Low | 27 | Referenced in 27 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `type_of_project` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, FamilyAidApplication.php |
| `theme` | `Keep` | Low | 32 | Referenced in 32 files: AdminController.php, ProjectController.php, ThemeController.php, Subtheme.php, Theme.php |
| `subtheme` | `Keep` | Low | 30 | Referenced in 30 files: AdminController.php, ProjectController.php, ThemeController.php, Subtheme.php, Theme.php |
| `activity` | `Keep` | Low | 24 | Referenced in 24 files: AdminController.php, ProjectController.php, projects_list.blade.php, cultural_center.blade.php, drinking_water_group.blade.php |
| `remarks` | `Keep` | Low | 45 | Referenced in 45 files: AdminController.php, LeaveApiController.php, ApplicationController.php, ClusterController.php, LeaveRequestController.php |
| `stage` | `Keep` | Low | 33 | Referenced in 33 files: AdminController.php, ProjectController.php, CulturalCenterProject.php, DifferentlyAbledApplication.php, DifferentlyAbledProject.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `completion_details` | `Keep` | Low | 26 | Referenced in 26 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `community_contributions` | `Keep` | Low | 24 | Referenced in 24 files: ProjectController.php, CulturalCenterProject.php, DifferentlyAbledProject.php, DrinkingWaterGroupProject.php, DrinkingWaterIndividualProject.php |
| `materials` | `Keep` | Low | 13 | Referenced in 13 files: ProjectController.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `expenses` | `Keep` | Low | 14 | Referenced in 14 files: ProjectController.php, HasProjectColumns.php, cultural_center.blade.php, drinking_water_group.blade.php, drinking_water_individual.blade.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.subthemes`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `theme_id` | `Keep` | Low | 15 | Referenced in 15 files: ThemeController.php, Subtheme.php, Theme.php, themes.blade.php, cultural_center.blade.php |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.themes`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `status` | `Keep` | Low | 100 | Referenced in 100 files: ApplicationCreated.php, AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

### Table: `rcfi.users`
| Column | Status | Risk Level | Code References | Audit Notes |
|---|---|---|---|---|
| `id` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `name` | `Keep` | Low | 192 | Referenced in 192 files: ApplicationCreated.php, EntityChanged.php, LeaveBalanceUpdated.php, LeaveRequestApproved.php, LeaveRequestCancelled.php |
| `email` | `Keep` | Low | 36 | Referenced in 36 files: AdminController.php, ApplicationController.php, AuthController.php, ContractorController.php, DonorController.php |
| `mobile` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php, DrinkingWaterGroupApplication.php |
| `father_name` | `Keep` | Low | 30 | Referenced in 30 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `mother_name` | `Keep` | Low | 29 | Referenced in 29 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `date_of_birth` | `Keep` | Low | 6 | Referenced in 6 files: ProfileController.php, UserController.php, profile.blade.php, users.blade.php, social_aid_project_pdf.blade.php |
| `date_of_joining` | `Keep` | Low | 8 | Referenced in 8 files: ProfileController.php, UserController.php, LeaveType.php, User.php, profile.blade.php |
| `gender` | `Keep` | Low | 29 | Referenced in 29 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `marital_status` | `Keep` | Low | 13 | Referenced in 13 files: ProfileController.php, UserController.php, DifferentlyAbledApplication.php, LeaveType.php, User.php |
| `house_name` | `Keep` | Low | 27 | Referenced in 27 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `place` | `Keep` | Low | 88 | Referenced in 88 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `po` | `Keep` | Low | 136 | Referenced in 136 files: AdminController.php, LeaveApiController.php, ApplicationController.php, AuthController.php, ClusterController.php |
| `district` | `Keep` | Low | 69 | Referenced in 69 files: AdminController.php, ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php |
| `state` | `Keep` | Low | 70 | Referenced in 70 files: AdminController.php, ApplicationController.php, ClusterController.php, ProfileController.php, ProjectController.php |
| `pin_code` | `Keep` | Low | 44 | Referenced in 44 files: ApplicationController.php, ProfileController.php, ProjectController.php, UserController.php, CulturalCenterApplication.php |
| `aadhar_number` | `Keep` | Low | 16 | Referenced in 16 files: ProfileController.php, ProjectController.php, UserController.php, DifferentlyAbledApplication.php, DrinkingWaterGroupApplication.php |
| `pan_card_number` | `Keep` | Low | 5 | Referenced in 5 files: ProfileController.php, UserController.php, profile.blade.php, users.blade.php, UserManagementTest.php |
| `account_number` | `Keep` | Low | 8 | Referenced in 8 files: ProfileController.php, ProjectController.php, UserController.php, profile.blade.php, social_aid_project_detals.blade.php |
| `bank_name` | `Keep` | Low | 5 | Referenced in 5 files: ProfileController.php, UserController.php, profile.blade.php, users.blade.php, UserManagementTest.php |
| `bank_branch` | `Keep` | Low | 5 | Referenced in 5 files: ProfileController.php, UserController.php, profile.blade.php, users.blade.php, UserManagementTest.php |
| `ifsc_code` | `Keep` | Low | 6 | Referenced in 6 files: ProfileController.php, UserController.php, profile.blade.php, users.blade.php, social_aid_project_pdf.blade.php |
| `role` | `Keep` | Low | 53 | Referenced in 53 files: ApplicationCreated.php, ApplicationController.php, AuthController.php, ClusterController.php, LeaveAdminController.php |
| `is_suspended` | `Keep` | Low | 11 | Referenced in 11 files: AuthController.php, UserController.php, CheckSuspendedUser.php, StoreLeaveRequestRequest.php, AccrueCasualLeaveJob.php |
| `email_verified_at` | `Keep` | Low | 5 | Referenced in 5 files: ProfileController.php, User.php, profile.blade.php, UserFactory.php, ProfileManagementTest.php |
| `password` | `Keep` | Low | 19 | Referenced in 19 files: AuthController.php, ProfileController.php, UserController.php, User.php, profile.blade.php |
| `designation` | `Keep` | Low | 46 | Referenced in 46 files: ApplicationController.php, ClusterController.php, ContractorController.php, LeaveRequestController.php, ProfileController.php |
| `remember_token` | `Keep` | Low | 2 | Referenced in 2 files: User.php, UserFactory.php |
| `created_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |
| `updated_at` | `Keep` | Low | Primary / Timestamp | Standard Eloquent primary key or timestamp column |

