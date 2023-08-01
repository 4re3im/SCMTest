-- environment: staging
-- master db: tngdb
-- tempo db: tngdb_tempo

-- Step 1. Drop the temporary database that were used to transform data
DROP DATABASE `tngdb_tempo`;


-- Step 2. Truncate all tables in cap database except the cap_schema (used by adonis for db versioning)
-- SELECT Concat('TRUNCATE TABLE cap.', TABLE_NAME, ';') FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = 'cap' AND TABLE_NAME <> 'cap_schema';
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE cap.content_detail_types;
TRUNCATE TABLE cap.content_details;
TRUNCATE TABLE cap.content_folders;
TRUNCATE TABLE cap.content_types;
TRUNCATE TABLE cap.contents;
TRUNCATE TABLE cap.series;
TRUNCATE TABLE cap.series_divisions;
TRUNCATE TABLE cap.series_formats;
TRUNCATE TABLE cap.series_reviews;
TRUNCATE TABLE cap.series_states;
TRUNCATE TABLE cap.series_subjects;
TRUNCATE TABLE cap.series_year_levels;
TRUNCATE TABLE cap.tab_contents;
TRUNCATE TABLE cap.tab_details;
TRUNCATE TABLE cap.tab_hm_ids;
TRUNCATE TABLE cap.tabs;
TRUNCATE TABLE cap.title_authors;
TRUNCATE TABLE cap.title_divisions;
TRUNCATE TABLE cap.title_formats;
TRUNCATE TABLE cap.title_related_titles;
TRUNCATE TABLE cap.title_reviews;
TRUNCATE TABLE cap.title_sample_pages;
TRUNCATE TABLE cap.title_states;
TRUNCATE TABLE cap.title_subjects;
TRUNCATE TABLE cap.title_supporting_titles;
TRUNCATE TABLE cap.title_year_levels;
TRUNCATE TABLE cap.title_brand_codes;
TRUNCATE TABLE cap.titles;
TRUNCATE TABLE cap.tokens;
TRUNCATE TABLE cap.users;
SET FOREIGN_KEY_CHECKS = 1;

-- Step 3. Run initial transformation script found in: 
-- http://svn.cup.cam.ac.uk/repos/OnlineContent/anz/tng/go/branches/main-engineering/SOURCE/databases/Normalization/master-normalization/Normalization/initial_tranformation_[env].sql