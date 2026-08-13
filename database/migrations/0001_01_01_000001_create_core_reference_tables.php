<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE TABLE `clusters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `institution_name` varchar(255) DEFAULT NULL,
  `head_of_institution` varchar(255) DEFAULT NULL,
  `head_contact_number` varchar(255) DEFAULT NULL,
  `place` varchar(255) DEFAULT NULL,
  `po` varchar(255) DEFAULT NULL,
  `village` varchar(255) DEFAULT NULL,
  `panjayath` varchar(255) DEFAULT NULL,
  `dist` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `contact_no` varchar(255) DEFAULT NULL,
  `cordinator_name` varchar(255) DEFAULT NULL,
  `cordinator_contact_number` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        DB::statement('CREATE TABLE `donors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `type_of_partner` varchar(255) NOT NULL,
  `type_of_fund` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `support_initiated_at` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `donors_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        DB::statement('CREATE TABLE `themes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT 1 COMMENT \'1=Active, 0=Inactive\',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci');
        DB::statement('CREATE TABLE `subthemes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `theme_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_theme_id` (`theme_id`),
  CONSTRAINT `fk_theme_id` FOREIGN KEY (`theme_id`) REFERENCES `themes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci');
        DB::statement('CREATE TABLE `pincode_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `circle_name` varchar(100) DEFAULT NULL,
  `region_name` varchar(100) DEFAULT NULL,
  `division_name` varchar(100) DEFAULT NULL,
  `office_name` varchar(150) DEFAULT NULL,
  `pincode` int(11) NOT NULL,
  `office_type` varchar(10) DEFAULT NULL,
  `delivery_status` varchar(20) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `state_name` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pincode` (`pincode`),
  KEY `idx_district` (`district`),
  KEY `idx_state` (`state_name`)
) ENGINE=InnoDB AUTO_INCREMENT=157127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        DB::statement('CREATE TABLE `profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profiles_user_id_foreign` (`user_id`),
  CONSTRAINT `profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('pincode_master');
        Schema::dropIfExists('subthemes');
        Schema::dropIfExists('themes');
        Schema::dropIfExists('donors');
        Schema::dropIfExists('clusters');
    }
};
