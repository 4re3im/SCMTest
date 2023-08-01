# CupContentFormat / formats
CREATE TRIGGER formats_after_insert 
AFTER INSERT ON `tngdb`.`CupContentFormat`
FOR EACH ROW 
  INSERT INTO `cap`.`formats` (id, name, pretty_url, description, is_digital, created_at, updated_at)
  VALUES (NEW.id, NEW.name, NEW.prettyUrl, NEW.longDescription, NEW.isDigital, NEW.createdAt, NEW.modifiedAt);
  
CREATE TRIGGER formats_after_update
AFTER UPDATE ON `tngdb`.`CupContentFormat`
FOR EACH ROW 
  UPDATE `cap`.`formats` 
  SET
  id=NEW.id,
  name=NEW.name, 
  pretty_url=NEW.prettyUrl,
  description=NEW.longDescription,
  is_digital=NEW.isDigital,
  updated_at=NEW.modifiedAt
  WHERE id = OLD.id;
    
CREATE TRIGGER formats_after_delete
AFTER DELETE ON `tngdb`.`CupContentFormat`
FOR EACH ROW 
  DELETE FROM `cap`.`formats` WHERE id = OLD.id;
  
# CupContentAuthor / authors
CREATE TRIGGER authors_after_insert  
AFTER INSERT ON `tngdb`.`CupContentAuthor`
FOR EACH ROW 
  INSERT INTO `cap`.`authors` (id, name, pretty_url, biography, created_at, updated_at)
  VALUES (NEW.id, NEW.name, NEW.prettyUrl, NEW.biography, NEW.createdAt, NEW.modifiedAt);  
  
CREATE TRIGGER authors_after_update
AFTER UPDATE ON `tngdb`.`CupContentAuthor`
FOR EACH ROW 
  UPDATE `cap`.`authors` 
  SET
  id=NEW.id,
  name=NEW.name, 
  pretty_url=NEW.prettyUrl,
  biography=NEW.biography,
  updated_at=NEW.modifiedAt
  WHERE id = OLD.id;
  
CREATE TRIGGER authors_after_delete
AFTER DELETE ON `tngdb`.`CupContentAuthor`
FOR EACH ROW 
  DELETE FROM `cap`.`authors` WHERE id = OLD.id;
    
# CupContentSubject / subjects
DELIMITER |
CREATE TRIGGER subjects_after_insert 
AFTER INSERT ON `tngdb`.`CupContentSubject`
FOR EACH ROW 
BEGIN
DECLARE var_country_id INT(11);
  SELECT id INTO var_country_id FROM `cap`.`countries` WHERE iso_code = NEW.region COLLATE utf8_unicode_ci;
  INSERT INTO `cap`.`subjects` (id, country_id, name, pretty_url, is_primary, is_secondary, description, created_at, updated_at)
  VALUES (NEW.id, var_country_id, NEW.name, NEW.prettyUrl, NEW.isPrimary, NEW.isSecondary, NEW.description, NEW.createdAt, NEW.modifiedAt);
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER subjects_after_update
AFTER UPDATE ON `tngdb`.`CupContentSubject`
FOR EACH ROW 
BEGIN
  DECLARE var_country_id INT(11);
  SELECT id INTO var_country_id FROM `cap`.`countries` WHERE iso_code = NEW.region COLLATE utf8_unicode_ci;
  UPDATE `cap`.`subjects` 
  SET
    id=NEW.id,
    country_id=var_country_id, 
    name=NEW.name, 
    pretty_url=NEW.prettyUrl,
    is_primary=NEW.isPrimary,
    is_secondary=NEW.isSecondary,
    description=NEW.description, 
    created_at=NEW.createdAt,
    updated_at=NEW.modifiedAt
  WHERE id = OLD.id;
END
|
DELIMITER ;

CREATE TRIGGER subjects_after_delete
AFTER DELETE ON `tngdb`.`CupContentSubject`
FOR EACH ROW 
  DELETE FROM `cap`.`subjects` WHERE id = OLD.id;
  
  
# CupContentSeries / series
DELIMITER |
CREATE TRIGGER series_after_insert 
AFTER INSERT ON `tngdb`.`CupContentSeries`
FOR EACH ROW 
BEGIN
  INSERT INTO `cap`.`series` (id, series_number, name, pretty_url, description, tagline, is_enabled) VALUES (NEW.id, NEW.SeriesID, NEW.name, NEW.prettyUrl, NEW.longDescription, NEW.tagline, NEW.isEnabled);
  INSERT INTO `cap`.`series_reviews` (series_id, detail) VALUES (NEW.id, NEW.reviews);
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER series_after_update
AFTER UPDATE ON `tngdb`.`CupContentSeries`
FOR EACH ROW 
BEGIN
  UPDATE `cap`.`series` 
  SET
    id=NEW.id,
    series_number=NEW.seriesID, 
    name=NEW.name,
    pretty_url=NEW.prettyUrl,
    description=NEW.longDescription,
    tagline=NEW.tagline,
    is_enabled=NEW.isEnabled,
    created_at=NEW.createdAt,
    updated_at=NEW.modifiedAt
  WHERE id = OLD.id;
  UPDATE `cap`.`series_reviews`
  SET
    detail=NEW.reviews
  WHERE series_id = OLD.id;
END
|
DELIMITER ;

CREATE TRIGGER series_after_delete
AFTER DELETE ON `tngdb`.`CupContentSeries`
FOR EACH ROW 
  DELETE FROM `cap`.`series` WHERE id = OLD.id;
  


# CupContentSeriesFormats / series_formats
DELIMITER |
CREATE TRIGGER series_formats_after_insert 
AFTER INSERT ON `tngdb`.`CupContentSeriesFormats`
FOR EACH ROW 
BEGIN
  DECLARE var_format_id INT(11);
  SELECT id INTO var_format_id FROM `cap`.`formats` WHERE name = NEW.format COLLATE utf8_unicode_ci;

  IF var_format_id IS NOT NULL THEN
   INSERT INTO `cap`.`series_formats` (series_id, format_id) VALUES (NEW.seriesID, var_format_id);
  END IF;
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER series_formats_after_delete
AFTER DELETE ON `tngdb`.`CupContentSeriesFormats`
FOR EACH ROW 
BEGIN
  DECLARE var_format_id INT(11);
  SELECT id INTO var_format_id FROM `cap`.`formats` WHERE name = OLD.format COLLATE utf8_unicode_ci;

  IF var_format_id IS NOT NULL THEN
   DELETE FROM `cap`.`series_formats` WHERE series_id = OLD.seriesID AND format_id = var_format_id;
  END IF;
END
|
DELIMITER ;

# CupContentSeriesSubjects / series_subjects
DELIMITER |
CREATE TRIGGER series_subjects_after_insert 
AFTER INSERT ON `tngdb`.`CupContentSeriesSubjects`
FOR EACH ROW 
BEGIN
  DECLARE var_subject_id INT(11);
  SELECT id INTO var_subject_id FROM `cap`.`subjects` WHERE name = NEW.subject COLLATE utf8_unicode_ci;

  IF var_subject_id IS NOT NULL THEN
   INSERT INTO `cap`.`series_subjects` (series_id, subject_id) VALUES (NEW.seriesID, var_subject_id);
  END IF;
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER series_subjects_after_delete
AFTER DELETE ON `tngdb`.`CupContentSeriesSubjects`
FOR EACH ROW 
BEGIN
  DECLARE var_subject_id INT(11);
  SELECT id INTO var_subject_id FROM `cap`.`subjects` WHERE name = OLD.subject COLLATE utf8_unicode_ci;

  IF var_subject_id IS NOT NULL THEN
   DELETE FROM `cap`.`series_subjects` WHERE series_id = OLD.seriesID AND subject_id = var_subject_id;
  END IF;
END
|
DELIMITER ;


# CupContentTitle / titles
DELIMITER | 
CREATE TRIGGER titles_after_insert 
AFTER INSERT ON `tngdb`.`CupContentTitle`
FOR EACH ROW 
BEGIN
  DECLARE var_series_id INT(11);
  DECLARE var_edition_id INT(11);
  DECLARE var_count_series INT(11);
  DECLARE var_created_at DATETIME;
  DECLARE var_modified_at DATETIME;
  SELECT COUNT(name) INTO var_count_series FROM `cap`.`series` WHERE name = NEW.series COLLATE utf8_unicode_ci;

  IF var_count_series > 1 THEN 
    SELECT id INTO var_series_id FROM `cap`.`series` WHERE is_enabled = 1 AND name = NEW.series COLLATE utf8_unicode_ci;
  ELSE
    SELECT id INTO var_series_id FROM `cap`.`series` WHERE name = NEW.series COLLATE utf8_unicode_ci;
  END IF;

  SELECT id INTO var_edition_id FROM `cap`.`editions` WHERE name = NEW.edition COLLATE utf8_unicode_ci;

  IF var_edition_id IS NULL AND NEW.Edition IS NOT NULL THEN
    INSERT INTO `cap`.`editions` (name) VALUES (NEW.edition);
    SELECT id INTO var_edition_id FROM `cap`.`editions` ORDER BY id DESC LIMIT 1;
  END IF;

  IF NEW.createdAt IS NULL THEN SET var_created_at = CURRENT_TIMESTAMP; ELSE SET var_created_at = NEW.createdAt; END IF;
  IF NEW.modifiedAt IS NULL THEN SET var_modified_at = CURRENT_TIMESTAMP; ELSE SET var_modified_at = NEW.modifiedAt; END IF;

  INSERT INTO `cap`.`titles` 
    (id, 
    series_id, 
    edition_id,
    isbn_13,
    isbn_10,
    name,
    pretty_url,
    description,
    subtitle,
    content,
    feature,
    availability,
    tagline,
    is_enabled,
    is_buy_now_shown,
    published_at,
    created_at,
    updated_at)
    VALUES (NEW.id,
    var_series_id,
    var_edition_id,
    NEW.isbn13,
    NEW.isbn10,
    NEW.name,
    NEW.prettyUrl,
    NEW.longDescription,
    NEW.subtitle,
    NEW.content,
    NEW.feature,
    NEW.availability,
    NEW.tagline,
    NEW.isEnabled,
    NEW.showBuyNow,
    NEW.publishDate,
    var_created_at,
    var_modified_at);
    INSERT INTO `cap`.`title_reviews` (title_id, detail) VALUES (NEW.id, NEW.reviews);
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER titles_after_update
AFTER UPDATE ON `tngdb`.`CupContentTitle`
FOR EACH ROW
BEGIN
  DECLARE var_series_id INT(11);
  DECLARE var_edition_id INT(11);
  DECLARE var_created_at DATETIME;
  DECLARE var_modified_at DATETIME;
  SELECT id INTO var_series_id FROM `cap`.`series` WHERE name = NEW.series COLLATE utf8_unicode_ci;
  SELECT id INTO var_edition_id FROM `cap`.`editions` WHERE name = NEW.edition COLLATE utf8_unicode_ci;
  IF var_edition_id IS NULL AND NEW.Edition IS NOT NULL THEN
    INSERT INTO `cap`.`editions` (name) VALUES (NEW.edition);
    SELECT id INTO var_edition_id FROM `cap`.`editions` ORDER BY id DESC LIMIT 1;
  END IF;

  IF NEW.createdAt IS NULL THEN SET var_created_at = CURRENT_TIMESTAMP; ELSE SET var_created_at = NEW.createdAt; END IF;
  IF NEW.modifiedAt IS NULL THEN SET var_modified_at = CURRENT_TIMESTAMP; ELSE SET var_modified_at = NEW.modifiedAt; END IF;

  UPDATE `cap`.`titles` 
  SET
    id=NEW.id,
    series_id=var_series_id,
    edition_id=var_edition_id,
    isbn_13=NEW.isbn13,
    isbn_10=NEW.isbn10,
    name=NEW.name,
    pretty_url=NEW.prettyUrl,
    description=NEW.longDescription,
        subtitle=NEW.subtitle,
        content=NEW.content,
        feature=NEW.feature,
        availability=NEW.availability,
        tagline=NEW.tagline,
        is_enabled=NEW.isEnabled,
        is_buy_now_shown=NEW.showBuyNow,
        published_at=NEW.publishDate,
        created_at=var_created_at,
        updated_at=var_modified_at
  WHERE id = OLD.id;
    UPDATE `cap`.`title_reviews`
  SET
    detail=NEW.reviews
  WHERE title_id = OLD.id;
END;
|
DELIMITER ;

CREATE TRIGGER titles_after_delete
AFTER DELETE ON `tngdb`.`CupContentTitle`
FOR EACH ROW
  DELETE FROM `cap`.`titles` WHERE id = OLD.id;

# CupContentTitle HmTitles / CupGoBrandCodeTitles / brand_codes
DELIMITER |
CREATE TRIGGER title_brand_codes_after_insert
AFTER INSERT ON `tngdb`.`CupGoBrandCodeTitles`
FOR EACH ROW
BEGIN
  DECLARE var_brand_code_id INT(11);
  SELECT id INTO var_brand_code_id FROM `cap`.`brand_codes` WHERE LOWER(`name`) = LOWER(NEW.brandCode);

  IF var_brand_code_id IS NULL THEN
    INSERT INTO `cap`.`brand_codes` (name) VALUES (NEW.brandCode);
    SELECT LAST_INSERT_ID() INTO var_brand_code_id;
  END IF;

  INSERT INTO `cap`.`title_brand_codes` (title_id, brand_code_id) VALUES (NEW.titleID, var_brand_code_id);
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER title_brand_codes_after_update
AFTER UPDATE ON `tngdb`.`CupGoBrandCodeTitles`
FOR EACH ROW
BEGIN
  DECLARE var_brand_code_id INT(11);
  DECLARE var_is_title_brand_code_exist INT(11);
  SELECT id INTO var_brand_code_id FROM `cap`.`brand_codes` WHERE LOWER(`name`) = LOWER(NEW.brandCode);
  SELECT brand_code_id INTO var_is_title_brand_code_exist FROM `cap`.`title_brand_codes` WHERE brand_code_id = var_brand_code_id;

  IF var_is_title_brand_code_exist IS NULL THEN
    INSERT INTO `cap`.`title_brand_codes` (title_id, brand_code_id) VALUES (NEW.titleID, var_brand_code_id);
  ELSE
    UPDATE `cap`.`title_brand_codes`
    SET
      title_id=NEW.titleID,
      brand_code_id=var_brand_code_id
    WHERE brand_code_id = var_brand_code_id;
  END IF;
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER title_brand_codes_after_delete
AFTER DELETE ON `tngdb`.`CupGoBrandCodeTitles`
FOR EACH ROW
BEGIN
  DECLARE var_brand_code_id INT(11);
  SELECT id INTO var_brand_code_id FROM `cap`.`brand_codes` WHERE LOWER(`name`) = LOWER(OLD.brandCode);

  DELETE FROM `cap`.`title_brand_codes` 
  WHERE brand_code_id = var_brand_code_id
  AND title_id = OLD.titleID;
END
|
DELIMITER ;


#CupContentTitleAuthors / title_authors
DELIMITER |
CREATE TRIGGER title_authors_after_insert
AFTER INSERT ON `tngdb`.`CupContentTitleAuthors`
FOR EACH ROW
BEGIN
  DECLARE var_author_id INT(11);
  SELECT id INTO var_author_id FROM `cap`.`authors` WHERE name = NEW.author COLLATE utf8_unicode_ci;

  IF var_author_id IS NULL THEN
	  INSERT INTO `cap`.`authors` (name, pretty_url, biography) VALUES (NEW.author, 'Unverified-Pretty-Url','Unknown Biography');
	  SELECT LAST_INSERT_ID() INTO var_author_id;
  END IF;

  INSERT INTO `cap`.`title_authors` (title_id, author_id) VALUES (NEW.titleID, var_author_id);
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER title_authors_after_delete
AFTER DELETE ON `tngdb`.`CupContentTitleAuthors`
FOR EACH ROW
BEGIN
  DECLARE var_author_id INT(11);
  SELECT id INTO var_author_id FROM `cap`.`authors` WHERE name = OLD.author COLLATE utf8_unicode_ci;

  DELETE FROM `cap`.`title_authors` WHERE title_id = OLD.titleID AND author_id = var_author_id;
END
|
DELIMITER ;

#CupContentTitleFormats / title_formats
DELIMITER |
CREATE TRIGGER title_formats_after_insert
AFTER INSERT ON `tngdb`.`CupContentTitleFormats`
FOR EACH ROW
BEGIN
  DECLARE var_format_id INT(11);
  SELECT id INTO var_format_id FROM `cap`.`formats` WHERE name = NEW.format COLLATE utf8_unicode_ci;

  IF var_format_id IS NOT NULL THEN
   INSERT INTO `cap`.`title_formats` (title_id, format_id) VALUES (NEW.titleID, var_format_id);
  END IF;
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER title_formats_after_delete
AFTER DELETE ON `tngdb`.`CupContentTitleFormats`
FOR EACH ROW
BEGIN
  DECLARE var_format_id INT(11);
  SELECT id INTO var_format_id FROM `cap`.`formats` WHERE name = OLD.format COLLATE utf8_unicode_ci;

  IF var_format_id IS NOT NULL THEN
   DELETE FROM `cap`.`title_formats` WHERE title_id = OLD.titleID AND format_id = var_format_id;
  END IF;
END
|
DELIMITER ;



#CupContentTitleRelatedTitle / title_related_titles
DELIMITER |
CREATE TRIGGER title_related_titles_after_insert
AFTER INSERT ON `tngdb`.`CupContentTitleRelatedTitle`
FOR EACH ROW
BEGIN
  INSERT INTO `cap`.`title_related_titles` (title_id, related_title_id) VALUES (NEW.titleID, NEW.related_titleID);
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER title_related_titles_after_delete
AFTER DELETE ON `tngdb`.`CupContentTitleRelatedTitle`
FOR EACH ROW
BEGIN
  DELETE FROM `cap`.`title_related_titles` WHERE title_id = OLD.titleID AND related_title_id = OLD.related_titleID;
END
|
DELIMITER ;


#CupContentTitleSamplePages / title_sample_pages
DELIMITER |
CREATE TRIGGER title_sample_pagesafter_insert
AFTER INSERT ON `tngdb`.`CupContentTitleSamplePages`
FOR EACH ROW
BEGIN
  INSERT INTO `cap`.`title_sample_pages` (id, title_id, name, meta, size, description, is_page_proof, created_at, updated_at) VALUES (NEW.id, NEW.titleID, NEW.filename, NEW.filemeta, NEW.filesize, NEW.description, NEW.is_page_proof, NEW.createdAt, NEW.modifiedAt);
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER title_sample_pages_after_update
AFTER UPDATE ON `tngdb`.`CupContentTitleSamplePages`
FOR EACH ROW
BEGIN
UPDATE `cap`.`title_sample_pages`
SET
  id=NEW.id,
  title_id=NEW.titleID,
  name=NEW.filename,
  meta=NEW.filemeta,
  size=NEW.filesize,
  description=NEW.description,
    is_page_proof=NEW.is_page_proof,
  created_at=NEW.createdAt,
  updated_at=NEW.modifiedAt
WHERE id = OLD.id;
END
|
DELIMITER ;

CREATE TRIGGER title_sample_pages_after_delete
AFTER DELETE ON `tngdb`.`CupContentTitleSamplePages`
FOR EACH ROW
  DELETE FROM `cap`.`title_sample_pages` WHERE id = OLD.id;


#CupContentTitleSubjects / title_subjects
DELIMITER |
CREATE TRIGGER title_subjects_after_insert
AFTER INSERT ON `tngdb`.`CupContentTitleSubjects`
FOR EACH ROW
BEGIN
  DECLARE var_subject_id INT(11);
  SELECT id INTO var_subject_id FROM `cap`.`subjects` WHERE name = NEW.subject COLLATE utf8_unicode_ci;

  IF var_subject_id IS NOT NULL THEN
   INSERT INTO `cap`.`title_subjects` (title_id, subject_id) VALUES (NEW.titleID, var_subject_id);
  END IF;
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER title_subjects_after_delete
AFTER DELETE ON `tngdb`.`CupContentTitleSubjects`
FOR EACH ROW
BEGIN
  DECLARE var_subject_id INT(11);
  SELECT id INTO var_subject_id FROM `cap`.`subjects` WHERE name = OLD.subject COLLATE utf8_unicode_ci;

  IF var_subject_id IS NOT NULL THEN
   DELETE FROM `cap`.`title_subjects` WHERE title_id = OLD.titleID AND subject_id = var_subject_id;
  END IF;
END
|
DELIMITER ;

#CupContentTitleSupportingTitle / title_supporting_titles
DELIMITER |
CREATE TRIGGER title_supporting_titles_after_insert
AFTER INSERT ON `tngdb`.`CupContentTitleSupportingTitle`
FOR EACH ROW
BEGIN
  DECLARE var_find_title_id INT(11);

  SELECT ID INTO var_find_title_id FROM `cap`.`titles` WHERE ID = NEW.supporting_titleID;

  IF var_find_title_id IS NOT NULL THEN
   INSERT INTO `cap`.`title_supporting_titles` (title_id, supporting_title_id) VALUES (NEW.titleID, NEW.supporting_titleID);
  END IF;
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER title_supporting_titles_after_delete
AFTER DELETE ON `tngdb`.`CupContentTitleSupportingTitle`
FOR EACH ROW
BEGIN
  DELETE FROM `cap`.`title_supporting_titles` WHERE title_id = OLD.titleID AND supporting_title_id = OLD.supporting_titleID;
END
|
DELIMITER ;

#CupGoContentFolders / content_folders
DELIMITER |
CREATE TRIGGER content_folders_after_insert
AFTER INSERT ON `tngdb`.`CupGoContentFolders`
FOR EACH ROW
BEGIN
  INSERT INTO `cap`.`content_folders` (id, title_id, name) VALUES (NEW.ID, NEW.TitleID, NEW.FolderName);
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER content_folders_after_update
AFTER UPDATE ON `tngdb`.`CupGoContentFolders`
FOR EACH ROW
BEGIN
  UPDATE `cap`.`content_folders`
  SET
    id=NEW.id,
    name=NEW.FolderName
  WHERE id = OLD.id;
END
|
DELIMITER ;

CREATE TRIGGER content_folders_after_delete
AFTER DELETE ON `tngdb`.`CupGoContentFolders`
FOR EACH ROW
  DELETE FROM `cap`.`content_folders` WHERE id = OLD.id;



#CupGoTabs / tabs
DELIMITER |
CREATE TRIGGER tabs_after_insert
AFTER INSERT ON `tngdb`.`CupGoTabs`
FOR EACH ROW
BEGIN
  DECLARE var_content_access_id INT(11);
    DECLARE var_is_public VARCHAR(255);
    DECLARE var_is_active INT(11);
    DECLARE var_is_default INT(11);
  DECLARE var_is_content_visibility_open INT(11);
    DECLARE var_is_public_text_used INT(11);
    DECLARE var_is_my_resources_link INT(11);
    DECLARE var_is_elevate_product INT(11);
    DECLARE var_is_hm_product INT(11);
    DECLARE var_sort_order INT(100);
    DECLARE var_tab_name VARCHAR(255);

    SELECT id INTO var_content_access_id FROM `cap`.`content_access` WHERE name = NEW.ContentAccess;

    IF NEW.visibility = 'Public' THEN SET var_is_public = 1; ELSE SET var_is_public = 0; END IF;
    IF NEW.Active = 'Y' THEN SET var_is_active = 1; ELSE SET var_is_active = 0; END IF;
    IF NEW.DefaultTab = 'Y' THEN SET var_is_default = 1; ELSE SET var_is_default = 0; END IF;
    IF NEW.AlwaysUsePublicText = 'Y' THEN SET var_is_public_text_used = 1; ELSE SET var_is_public_text_used = 0; END IF;
    IF NEW.MyResourcesLink = 'Y' THEN SET var_is_my_resources_link = 1; ELSE SET var_is_my_resources_link = 0; END IF;
    IF NEW.ElevateProduct = 'Y' THEN SET var_is_elevate_product = 1; ELSE SET var_is_elevate_product = 0; END IF;
    IF NEW.HMProduct = 'Y' THEN SET var_is_hm_product = 1; ELSE SET var_is_hm_product = 0; END IF;
    IF NEW.ContentVisibility = 'open' THEN SET var_is_content_visibility_open = 1; ELSE SET var_is_content_visibility_open = 0; END IF;
    IF NEW.SortOrder IS NULL THEN SET var_sort_order = 1; ELSE SET var_sort_order = NEW.SortOrder; END IF;
    IF NEW.TabName IS NULL THEN SET var_tab_name = ''; ELSE SET var_tab_name = NEW.TabName; END IF;

  INSERT INTO `cap`.`tabs`(
    id,
    title_id,
    content_access_id,
    name,
    public_text,
    private_text,
    custom_access_message,
    is_public,
    is_active,
    is_default,
    column_number,
    user_type_restriction,
    resource_url,
    icon,
    is_public_text_used,
    is_my_resources_link,
    is_elevate_product,
    is_hm_product,
    sample_hm_id,
    sample_hm_prod_url,
    created_at)
    VALUES (
    NEW.ID,
    NEW.TitleID,
    var_content_access_id,
    var_tab_name,
    NEW.Public_TabText,
    NEW.Private_TabText,
    NEW.CustomAccessMessage,
    var_is_public,
    var_is_active,
    var_is_default,
    NEW.Columns,
    NEW.UserTypeIDRestriction,
    NEW.ResourceURL,
    NEW.TabIcon,
    var_is_public_text_used,
    var_is_my_resources_link,
    var_is_elevate_product,
    var_is_hm_product,
    NEW.HmID,
    NEW.hm_prod_url,
    NEW.CreationDate);
    INSERT INTO `cap`.`tab_details` (tab_id, sort_order, level, type, catalogue_field, is_content_visibility_open, content_type)
    VALUES(NEW.ID, var_sort_order, NEW.TabLevel, NEW.TabType, NEW.CatalogueField, var_is_content_visibility_open, NEW.ContentType);
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER tabs_after_update
AFTER UPDATE ON `tngdb`.`CupGoTabs`
FOR EACH ROW
BEGIN
  DECLARE var_content_access_id INT(11);
  DECLARE var_is_public VARCHAR(255);
    DECLARE var_is_active INT(11);
    DECLARE var_is_default INT(11);
  DECLARE var_is_content_visibility_open INT(11);
    DECLARE var_is_public_text_used INT(11);
    DECLARE var_is_my_resources_link INT(11);
    DECLARE var_is_elevate_product INT(11);
    DECLARE var_is_hm_product INT(11);
    DECLARE var_sort_order INT(100);
    DECLARE var_tab_name VARCHAR(255);

    SELECT id INTO var_content_access_id FROM `cap`.`content_access` WHERE name = NEW.ContentAccess;

    IF NEW.visibility = 'Public' THEN SET var_is_public = 1; ELSE SET var_is_public = 0; END IF;
    IF NEW.Active = 'Y' THEN SET var_is_active = 1; ELSE SET var_is_active = 0; END IF;
  IF NEW.DefaultTab = 'Y' THEN SET var_is_default = 1; ELSE SET var_is_default = 0; END IF;
  IF NEW.AlwaysUsePublicText = 'Y' THEN SET var_is_public_text_used = 1; ELSE SET var_is_public_text_used = 0; END IF;
  IF NEW.MyResourcesLink = 'Y' THEN SET var_is_my_resources_link = 1; ELSE SET var_is_my_resources_link = 0; END IF;
    IF NEW.ElevateProduct = 'Y' THEN SET var_is_elevate_product = 1; ELSE SET var_is_elevate_product = 0; END IF;
  IF NEW.HMProduct = 'Y' THEN SET var_is_hm_product = 1; ELSE SET var_is_hm_product = 0; END IF;
  IF NEW.ContentVisibility = 'open' THEN SET var_is_content_visibility_open = 1; ELSE SET var_is_content_visibility_open = 0; END IF;
  IF NEW.SortOrder IS NULL THEN SET var_sort_order = 1; ELSE SET var_sort_order = NEW.SortOrder; END IF;
  IF NEW.TabName IS NULL THEN SET var_tab_name = ''; ELSE SET var_tab_name = NEW.TabName; END IF;

  UPDATE `cap`.`tabs`
  SET
    id=NEW.id,
    title_id=NEW.TitleID,
    content_access_id=var_content_access_id,
    name=var_tab_name,
    public_text=NEW.Public_TabText,
    private_text=NEW.Private_TabText,
    custom_access_message=NEW.CustomAccessMessage,
        is_public=var_is_public,
        is_active=var_is_active,
        is_default=var_is_default,
        column_number=NEW.Columns,
        user_type_restriction=NEW.UserTypeIDRestriction,
        resource_url=NEW.ResourceURL,
        icon=NEW.TabIcon,
        is_public_text_used=var_is_public_text_used,
        is_my_resources_link=var_is_my_resources_link,
        is_elevate_product=var_is_elevate_product,
        is_hm_product=var_is_hm_product,
        sample_hm_id=NEW.HmID,
        sample_hm_prod_url=NEW.hm_prod_url
  WHERE id = OLD.ID;
    UPDATE `cap`.`tab_details`
    SET
    sort_order=var_sort_order,
    level=NEW.TabLevel,
    type=NEW.TabType,
    catalogue_field=NEW.CatalogueField,
    is_content_visibility_open=var_is_content_visibility_open,
        content_type=NEW.ContentType
  WHERE tab_id = OLD.ID;
END
|
DELIMITER ;


CREATE TRIGGER tabs_after_delete
AFTER DELETE ON `tngdb`.`CupGoTabs`
FOR EACH ROW
  DELETE FROM `cap`.`tabs` WHERE id = OLD.ID;


#CupGoTabContent / tab_contents
DELIMITER |
CREATE TRIGGER tab_contents_after_insert
AFTER INSERT ON `tngdb`.`CupGoTabContent`
FOR EACH ROW
BEGIN
  DECLARE var_is_active INT(11);
    DECLARE var_is_demo_only INT(11);
    DECLARE var_is_visible INT(11);
    DECLARE var_sort_order INT(100);

  IF NEW.Active = 'Y' THEN SET var_is_active = 1; ELSE SET var_is_active = 0; END IF;
  IF NEW.DemoOnly = 'Y' THEN SET var_is_demo_only = 1; ELSE SET var_is_demo_only = 0; END IF;
    IF NEW.Visibility = 'Public' THEN SET var_is_visible = 1; ELSE SET var_is_visible = 0; END IF;
    IF NEW.SortOrder IS NULL THEN SET var_sort_order = 1; ELSE SET var_sort_order = NEW.SortOrder; END IF;

  IF NEW.TabID IS NOT NULL THEN
   INSERT INTO `cap`.`tab_contents` (id, tab_id, content_id, sort_order, is_active, is_demo_only, is_visible, column_number, created_at, updated_at)
    VALUES (NEW.ID, NEW.TabID, NEW.ContentID, var_sort_order, var_is_active, var_is_demo_only, var_is_visible, NEW.ColumnNumber, NEW.CreationDate, NEW.CreationDate);
  END IF;
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER tab_contents_after_update
AFTER UPDATE ON `tngdb`.`CupGoTabContent`
FOR EACH ROW
BEGIN
  DECLARE var_is_active INT(11);
    DECLARE var_is_demo_only INT(11);
    DECLARE var_is_visible INT(11);
    DECLARE var_sort_order INT(100);

  IF NEW.Active = 'Y' THEN SET var_is_active = 1; ELSE SET var_is_active = 0; END IF;
  IF NEW.DemoOnly = 'Y' THEN SET var_is_demo_only = 1; ELSE SET var_is_demo_only = 0; END IF;
    IF NEW.Visibility = 'Public' THEN SET var_is_visible = 1; ELSE SET var_is_visible = 0; END IF;
    IF NEW.SortOrder IS NULL THEN SET var_sort_order = 1; ELSE SET var_sort_order = NEW.SortOrder; END IF;

  IF NEW.TabID IS NOT NULL THEN
    UPDATE `cap`.`tab_contents`
    SET
      id=NEW.id,
      tab_id=NEW.TabID,
      content_id=NEW.ContentID,
      sort_order=var_sort_order,
      is_active=var_is_active,
      is_demo_only=var_is_demo_only,
      is_visible=var_is_visible,
      column_number=NEW.ColumnNumber,
      updated_at=NOW()
    WHERE id = OLD.id;
  END IF;
END
|
DELIMITER ;

CREATE TRIGGER tab_contents_after_delete
AFTER DELETE ON `tngdb`.`CupGoTabContent`
FOR EACH ROW
  DELETE FROM `cap`.`tab_contents` WHERE id = OLD.ID;


CREATE TRIGGER tab_hm_ids_after_insert 
AFTER INSERT ON `tngdb`.`CupGoTabHmIds`
FOR EACH ROW 
  INSERT INTO `cap`.`tab_hm_ids` (ID, tab_id, entitlement_id, hm_id)
  VALUES (NEW.ID, NEW.TabId, NEW.EntitlementId, NEW.HmId);

CREATE TRIGGER tab_hm_ids_after_update 
AFTER UPDATE ON `tngdb`.`CupGoTabHmIds`
FOR EACH ROW 
  UPDATE `cap`.`tab_hm_ids` 
  SET
  ID=NEW.ID,
  tab_id=NEW.TabId, 
  entitlement_id=NEW.EntitlementId,
  hm_id=NEW.HmId
  WHERE ID = OLD.id;


#CupGoContent / contents
DELIMITER |
CREATE TRIGGER contents_after_insert
AFTER INSERT ON `tngdb`.`CupGoContent`
FOR EACH ROW
BEGIN
  DECLARE var_is_global INT(11);
  DECLARE var_type_id INT(11);
  DECLARE var_name VARCHAR(255);
  DECLARE var_heading VARCHAR(255);

  SELECT id INTO var_type_id FROM `cap`.`types` WHERE id = NEW.ContentTypeID;
  IF NEW.Global = 'Y' THEN SET var_is_global = 1; ELSE SET var_is_global = 0; END IF;
  IF NEW.CMS_Name IS NULL THEN SET var_name = ''; ELSE SET var_name = NEW.CMS_Name; END IF;
  IF NEW.ContentHeading IS NULL THEN SET var_heading = ''; ELSE SET var_heading = NEW.ContentHeading; END IF;
    
  INSERT INTO `cap`.`contents` (id, name, heading, details, note, is_global, created_at) 
  VALUES (NEW.ID, var_name, var_heading, NEW.ContentData, NEW.CMS_Notes, var_is_global, NEW.CreationDate);
  IF var_type_id IS NOT NULL THEN
  INSERT INTO `cap`.`content_types` (content_id, type_id) VALUES (NEW.ID, var_type_id);
  END IF;
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER contents_after_update
AFTER UPDATE ON `tngdb`.`CupGoContent`
FOR EACH ROW
BEGIN
  DECLARE var_is_global INT(11);
    DECLARE var_folder_id INT(11);
    DECLARE var_type_id INT(11);
    DECLARE var_name VARCHAR(255);
    DECLARE var_heading VARCHAR(255);
    SELECT id INTO var_type_id FROM `cap`.`types` WHERE id = NEW.ContentTypeID;
    IF NEW.Global = 'Y' THEN SET var_is_global = 1; ELSE SET var_is_global = 0; END IF;
    IF NEW.CMS_Name IS NULL THEN SET var_name = ''; ELSE SET var_name = NEW.CMS_Name; END IF;
    IF NEW.ContentHeading IS NULL THEN SET var_heading = ''; ELSE SET var_heading = NEW.ContentHeading; END IF;
    
  UPDATE `cap`.`contents` 
  SET
    id=NEW.id,
    name=var_name, 
    heading=var_heading,
    details=NEW.ContentData,
    note=NEW.CMS_Notes,
    is_global=var_is_global
  WHERE id = OLD.id;
    IF var_type_id IS NOT NULL THEN
      UPDATE `cap`.`content_types`
      SET
      type_id = var_type_id;
    END IF;
END
|
DELIMITER ;

CREATE TRIGGER contents_after_delete
AFTER DELETE ON `tngdb`.`CupGoContent`
FOR EACH ROW
  DELETE FROM `cap`.`contents` WHERE id = OLD.ID;


#CupGoContent / contents
DELIMITER |
CREATE TRIGGER folder_contents_after_insert
AFTER INSERT ON `tngdb`.`CupGoFolderContent`
FOR EACH ROW
BEGIN
  UPDATE `cap`.`contents`
  SET folder_id=NEW.FolderID
  WHERE id = NEW.ContentID;
END
|
DELIMITER ;


#CupGoContentDetail / content_details
DELIMITER |
CREATE TRIGGER content_details_after_insert
AFTER INSERT ON `tngdb`.`CupGoContentDetail`
FOR EACH ROW
BEGIN
  DECLARE var_is_active INT(11);
    DECLARE var_is_visible INT(11);
    DECLARE var_is_demo_only INT(11);
    DECLARE var_is_file_size_shown INT(11);
    DECLARE var_type_id INT(11);
    DECLARE var_sort_order INT(100);
    SELECT id INTO var_type_id FROM `cap`.`types` WHERE id = NEW.TypeID;

    IF NEW.Active = 'Y' THEN SET var_is_active = 1; ELSE SET var_is_active = 0; END IF;
    IF NEW.Visibility = 'Public' THEN SET var_is_visible = 1; ELSE SET var_is_visible = 0; END IF;
    IF NEW.DemoOnly = 'Y' THEN SET var_is_demo_only = 1; ELSE SET var_is_demo_only = 0; END IF;
    IF NEW.ShowFileSize = 'Y' THEN SET var_is_file_size_shown = 1; ELSE SET var_is_file_size_shown = 0; END IF;
    IF NEW.SortOrder IS NULL THEN SET var_sort_order = 1; ELSE SET var_sort_order = NEW.SortOrder; END IF;
    

  IF NEW.ContentID IS NOT NULL THEN 
   INSERT INTO `cap`.`content_details` (id, content_id, name, note, description, sort_order, url, window_behaviour, window_height, window_width, html_content, is_active, is_visible, is_demo_only, file_name, file_path, is_file_size_shown, file_size, file_uploaded_at) 
    VALUES (NEW.ID, NEW.ContentID, NEW.Public_Name, NEW.CMS_Notes, NEW.Public_Description, var_sort_order, NEW.URL, NEW.WindowBehaviour, NEW.WindowHeight, NEW.WindowWidth, NEW.HTML_Content, var_is_active, var_is_visible, var_is_demo_only, NEW.FileName, NEW.FilePath, var_is_file_size_shown, NEW.FileSize, NEW.FileUploadDate);

    IF var_type_id IS NOT NULL THEN
      INSERT INTO `cap`.`content_detail_types` (content_detail_id, type_id) VALUES(NEW.ID, var_type_id);
    END IF;
  END IF;
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER content_details_after_update
AFTER UPDATE ON `tngdb`.`CupGoContentDetail`
FOR EACH ROW
BEGIN
  DECLARE var_is_active INT(11);
    DECLARE var_is_visible INT(11);
    DECLARE var_is_demo_only INT(11);
    DECLARE var_is_file_size_shown INT(11);
    DECLARE var_type_id INT(11);
    DECLARE var_sort_order INT(100);
    SELECT id INTO var_type_id FROM `cap`.`types` WHERE id = NEW.TypeID;

  IF NEW.Active = 'Y' THEN SET var_is_active = 1; ELSE SET var_is_active = 0; END IF;
    IF NEW.Visibility = 'Public' THEN SET var_is_visible = 1; ELSE SET var_is_visible = 0; END IF;
    IF NEW.DemoOnly = 'Y' THEN SET var_is_demo_only = 1; ELSE SET var_is_demo_only = 0; END IF;
    IF NEW.ShowFileSize = 'Y' THEN SET var_is_file_size_shown = 1; ELSE SET var_is_file_size_shown = 0; END IF;
    IF NEW.SortOrder IS NULL THEN SET var_sort_order = 1; ELSE SET var_sort_order = NEW.SortOrder; END IF;

  UPDATE `cap`.`content_details`
  SET
    id=NEW.id,
    content_id=NEW.ContentID,
    name=NEW.Public_Name,
    note=NEW.CMS_Notes,
    description=NEW.Public_Description,
    sort_order=var_sort_order,
    url=NEW.URL,
        window_behaviour=NEW.WindowBehaviour,
        window_height=NEW.WindowHeight,
        window_width=NEW.WindowWidth,
        html_content=NEW.HTML_Content,
        is_active=var_is_active,
        is_visible=var_is_visible,
        is_demo_only=var_is_demo_only,
        file_name=NEW.FileName,
        file_path=NEW.FilePath,
        is_file_size_shown=var_is_file_size_shown,
        file_size=NEW.FileSize
  WHERE id = OLD.ID;
    UPDATE `cap`.`content_detail_types`
    SET
        type_id = var_type_id
    WHERE content_detail_id = OLD.ID;
END
|
DELIMITER ;

CREATE TRIGGER content_details_after_delete
AFTER DELETE ON `tngdb`.`CupGoContentDetail`
FOR EACH ROW
  DELETE FROM `cap`.`content_details` WHERE id = OLD.ID;


#title_states
DELIMITER |
CREATE TRIGGER save_title_states_after_create_title
AFTER INSERT ON `tngdb`.`CupContentTitle`
FOR EACH ROW
BEGIN
  DECLARE var_title_id INT(11);
  DECLARE var_init_regions VARCHAR(255);
  DECLARE var_regionsWithNoBrackets VARCHAR(255);
  DECLARE var_state VARCHAR(255);
  DECLARE var_stateID INT(11);
  DECLARE var_regions VARCHAR(255);

  SET var_title_id = NEW.id;
  SET var_init_regions = NEW.regions;
  SET var_regionsWithNoBrackets = (left (right (var_init_regions, length (var_init_regions)-1), length (var_init_regions)-2));
  SET var_regions = CONCAT(REPLACE(var_regionsWithNoBrackets, '][', ','), ',');

  WHILE (LOCATE(',', var_regions ) > 0)
  DO
      SET var_state = SUBSTRING(var_regions, 1, LOCATE(',', var_regions) - 1);
      IF var_state IS NOT NULL AND LENGTH(var_state) > 0 THEN
        SET var_stateID = (SELECT id FROM `cap`.`states` WHERE LOWER(`name`) = LOWER(var_state));

        IF var_state = 'New Zealand' THEN
          INSERT INTO `cap`.`title_states` (title_id, state_id)
            SELECT var_title_id, s.id FROM `cap`.`states` s JOIN `cap`.`countries` c ON s.country_id = c.id WHERE c.name = var_state;
        END IF;

        IF var_stateID IS NOT NULL THEN
          INSERT INTO `cap`.`title_states` VALUES(var_title_id , var_stateID);
        END IF;
      END IF;
      SET var_regions = SUBSTRING(var_regions, LOCATE(',',var_regions) + 1);
  END WHILE;
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER update_title_states_after_update_title
AFTER UPDATE ON `tngdb`.`CupContentTitle`
FOR EACH ROW
BEGIN
  DECLARE var_title_id INT(11);
  DECLARE var_init_regions VARCHAR(255);
  DECLARE var_regionsWithNoBrackets VARCHAR(255);
  DECLARE var_state VARCHAR(255);
  DECLARE var_stateID INT(11);
  DECLARE var_regions VARCHAR(255);

  SET var_title_id = OLD.id;
  SET var_init_regions = OLD.regions;
  SET var_regionsWithNoBrackets = (left (right (var_init_regions, length (var_init_regions)-1), length (var_init_regions)-2));
  SET var_regions = CONCAT(REPLACE(var_regionsWithNoBrackets, '][', ','), ',');

  DELETE FROM `cap`.`title_states` WHERE title_id = var_title_id;

  WHILE (LOCATE(',', var_regions ) > 0)
  DO
    SET var_state = SUBSTRING(var_regions, 1, LOCATE(',', var_regions) - 1);
    IF var_state IS NOT NULL AND LENGTH(var_state) > 0 THEN
      SET var_stateID = (SELECT id FROM `cap`.`states` WHERE LOWER(`name`) = LOWER(var_state));

      IF var_state = 'New Zealand' THEN
        INSERT INTO `cap`.`title_states` (title_id, state_id)
            SELECT var_title_id, s.id FROM `cap`.`states` s JOIN `cap`.`countries` c ON s.country_id = c.id WHERE c.name = var_state;
      END IF;

      IF var_stateID IS NOT NULL THEN
        INSERT INTO `cap`.`title_states` VALUES(var_title_id , var_stateID );
      END IF;
    END IF;
    SET var_regions = SUBSTRING(var_regions, LOCATE(',',var_regions) + 1);
  END WHILE;
END
|
DELIMITER ;


#series_states
DELIMITER |
CREATE TRIGGER save_series_states_after_create_series
AFTER INSERT ON `tngdb`.`CupContentSeries`
FOR EACH ROW
BEGIN
  DECLARE var_series_id INT(11);
    DECLARE var_init_regions VARCHAR(255);
    DECLARE var_regionsWithNoBrackets VARCHAR(255);
    DECLARE var_state VARCHAR(255);
    DECLARE var_stateID INT(11);
    DECLARE var_regions VARCHAR(255);

  SET var_series_id = NEW.id;
    SET var_init_regions = NEW.regions;
    SET var_regionsWithNoBrackets = (left (right (var_init_regions, length (var_init_regions)-1), length (var_init_regions)-2));
    SET var_regions = CONCAT(REPLACE(var_regionsWithNoBrackets, '][', ','), ',');

WHILE (LOCATE(',', var_regions ) > 0)
DO
    SET var_state = SUBSTRING(var_regions, 1, LOCATE(',', var_regions) - 1);
    IF var_state IS NOT NULL AND LENGTH(var_state) > 0 THEN
      SET var_stateID = (SELECT id FROM `cap`.`states` WHERE LOWER(`name`) = LOWER(var_state));


      IF var_state = 'New Zealand' THEN
        INSERT INTO `cap`.`series_states` (series_id, state_id)
          SELECT var_series_id, s.id FROM `cap`.`states` s JOIN `cap`.`countries` c ON s.country_id = c.id WHERE c.name = var_state;
      END IF;

      IF var_stateID IS NOT NULL THEN
        INSERT INTO `cap`.`series_states` VALUES(var_series_id , var_stateID);
      END IF;
    END IF;
    SET var_regions = SUBSTRING(var_regions, LOCATE(',', var_regions) + 1);
END WHILE;
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER update_series_states_after_update_series
AFTER UPDATE ON `tngdb`.`CupContentSeries`
FOR EACH ROW
BEGIN
  DECLARE var_series_id INT(11);
    DECLARE var_init_regions VARCHAR(255);
    DECLARE var_regionsWithNoBrackets VARCHAR(255);
    DECLARE var_state VARCHAR(255);
    DECLARE var_stateID INT(11);
    DECLARE var_regions VARCHAR(255);

  SET var_series_id = NEW.id;
    SET var_init_regions = NEW.regions;
    SET var_regionsWithNoBrackets = (left (right (var_init_regions, length (var_init_regions)-1), length (var_init_regions)-2));
    SET var_regions = CONCAT(REPLACE(var_regionsWithNoBrackets, '][', ','), ',');

    DELETE FROM `cap`.`series_states` WHERE series_id = var_series_id;

WHILE (LOCATE(',', var_regions ) > 0)
DO
    SET var_state = SUBSTRING(var_regions, 1, LOCATE(',', var_regions) - 1);
    IF var_state IS NOT NULL AND LENGTH(var_state) > 0 THEN
      SET var_stateID = (SELECT id FROM `cap`.`states` WHERE LOWER(`name`) = LOWER(var_state));

      IF var_state = 'New Zealand' THEN
        INSERT INTO `cap`.`series_states` (series_id, state_id)
          SELECT var_series_id, s.id FROM `cap`.`states` s JOIN `cap`.`countries` c ON s.country_id = c.id WHERE c.name = var_state;
      END IF;

      IF var_stateID IS NOT NULL THEN
        INSERT INTO `cap`.`series_states` VALUES(var_series_id , var_stateID);
      END IF;
    END IF;
    SET var_regions = SUBSTRING(var_regions, LOCATE(',',var_regions) + 1);
END WHILE;
END
|
DELIMITER ;


#series_year_levels
DELIMITER |
CREATE TRIGGER save_series_year_levels_after_create_series
AFTER INSERT ON `tngdb`.`CupContentSeries`
FOR EACH ROW
BEGIN
  DECLARE var_series_id INT(11  );
    DECLARE var_init_levels VARCHAR(255);
    DECLARE var_levelsWithNoBrackets VARCHAR(255);
    DECLARE var_level VARCHAR(255);
    DECLARE var_levelID INT(11);
    DECLARE var_levels VARCHAR(255);

  SET var_series_id = NEW.id;
    SET var_init_levels = NEW.yearLevels;
    SET var_levelsWithNoBrackets = (left (right (var_init_levels, length (var_init_levels)-1), length (var_init_levels)-2));
    SET var_levels = CONCAT(REPLACE(var_levelsWithNoBrackets, '][', ','), ',');

WHILE (LOCATE(',', var_levels ) > 0)
DO
    SET var_level = SUBSTRING(var_levels, 1, LOCATE(',', var_levels) - 1);
    IF var_level IS NOT NULL AND length(var_level) > 0 THEN
      SET var_levelID = (SELECT id FROM `cap`.`year_levels` WHERE LOWER(`level`) = LOWER(var_level));

      IF var_levelID IS NOT NULL THEN
        INSERT INTO `cap`.`series_year_levels` VALUES (var_series_id , var_levelID);
      END IF;
    END IF;
    SET var_levels = SUBSTRING(var_levels, LOCATE(',',var_levels) + 1);
END WHILE;
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER update_series_year_levels_after_create_series
AFTER UPDATE ON `tngdb`.`CupContentSeries`
FOR EACH ROW
BEGIN
  DECLARE var_series_id INT(11);
    DECLARE var_init_levels VARCHAR(255);
    DECLARE var_levelsWithNoBrackets VARCHAR(255);
    DECLARE var_level VARCHAR(255);
    DECLARE var_levelID INT(11);
    DECLARE var_levels VARCHAR(255);

  SET var_series_id = NEW.id;
    SET var_init_levels = NEW.yearLevels;
    SET var_levelsWithNoBrackets = (left (right (var_init_levels, length (var_init_levels)-1), length (var_init_levels)-2));
    SET var_levels = CONCAT(REPLACE(var_levelsWithNoBrackets, '][', ','), ',');

    DELETE FROM `cap`.`series_year_levels` WHERE series_id = var_series_id;

WHILE (LOCATE(',', var_levels ) > 0)
DO
    SET var_level = SUBSTRING(var_levels, 1, LOCATE(',', var_levels) - 1);
    IF var_level IS NOT NULL AND length(var_level) > 0 THEN
      SET var_levelID = (SELECT id FROM `cap`.`year_levels` WHERE LOWER(`level`) = LOWER(var_level));

      IF var_levelID IS NOT NULL THEN
        INSERT INTO `cap`.`series_year_levels` VALUES (var_series_id , var_levelID);
      END IF;
    END IF;
    SET var_levels = SUBSTRING(var_levels, LOCATE(',',var_levels) + 1);
END WHILE;
END
|
DELIMITER ;


#title_year_levels
DELIMITER |
CREATE TRIGGER save_title_year_levels_after_create_title
AFTER INSERT ON `tngdb`.`CupContentTitle`
FOR EACH ROW
BEGIN
  DECLARE var_title_id INT(11 );
    DECLARE var_init_levels VARCHAR(255);
    DECLARE var_levelsWithNoBrackets VARCHAR(255);
    DECLARE var_level VARCHAR(255);
    DECLARE var_levelID INT(11);
    DECLARE var_levels VARCHAR(255);

  SET var_title_id = NEW.id;
    SET var_init_levels = NEW.yearLevels;
    SET var_levelsWithNoBrackets = (left (right (var_init_levels, length (var_init_levels)-1), length (var_init_levels)-2));
    SET var_levels = CONCAT(REPLACE(var_levelsWithNoBrackets, '][', ','), ',');

WHILE (LOCATE(',', var_levels ) > 0)
DO
    SET var_level = SUBSTRING(var_levels, 1, LOCATE(',', var_levels) - 1);
    IF var_level IS NOT NULL AND length(var_level) > 0 THEN
      SET var_levelID = (SELECT id FROM `cap`.`year_levels` WHERE LOWER(`level`) = LOWER(var_level));

      IF var_levelID IS NOT NULL THEN
        INSERT INTO `cap`.`title_year_levels` VALUES (var_title_id , var_levelID);
      END IF;
    END IF;
    SET var_levels = SUBSTRING(var_levels, LOCATE(',',var_levels) + 1);
END WHILE;
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER update_title_year_levels_after_create_title
AFTER UPDATE ON `tngdb`.`CupContentTitle`
FOR EACH ROW
BEGIN
  DECLARE var_title_id INT(11);
    DECLARE var_init_levels VARCHAR(255);
    DECLARE var_levelsWithNoBrackets VARCHAR(255);
    DECLARE var_level VARCHAR(255);
    DECLARE var_levelID INT(11);
    DECLARE var_levels VARCHAR(255);

  SET var_title_id = NEW.id;
    SET var_init_levels = NEW.yearLevels;
    SET var_levelsWithNoBrackets = (left (right (var_init_levels, length (var_init_levels)-1), length (var_init_levels)-2));
    SET var_levels = CONCAT(REPLACE(var_levelsWithNoBrackets, '][', ','), ',');

    DELETE FROM `cap`.`title_year_levels` WHERE title_id = var_title_id;

WHILE (LOCATE(',', var_levels ) > 0)
DO
    SET var_level = SUBSTRING(var_levels, 1, LOCATE(',', var_levels) - 1);
    IF var_level IS NOT NULL AND length(var_level) > 0 THEN
      SET var_levelID = (SELECT id FROM `cap`.`year_levels` WHERE LOWER(`level`) = LOWER(var_level));

      IF var_levelID IS NOT NULL THEN
        INSERT INTO `cap`.`title_year_levels` VALUES (var_title_id , var_levelID);
      END IF;
    END IF;
    SET var_levels = SUBSTRING(var_levels, LOCATE(',',var_levels) + 1);
END WHILE;
END
|
DELIMITER ;


#series_divisions
DELIMITER |
CREATE TRIGGER save_series_divisions_after_create_series
AFTER INSERT ON `tngdb`.`CupContentSeries`
FOR EACH ROW
BEGIN
  DECLARE var_series_id INT(11);
    DECLARE var_init_divisions VARCHAR(255);
    DECLARE var_divisionsWithNoBrackets VARCHAR(255);
    DECLARE var_division VARCHAR(255);
    DECLARE var_divisionID INT(11);
    DECLARE var_divisions VARCHAR(255);

  SET var_series_id = NEW.id;
    SET var_init_divisions = NEW.divisions;
    SET var_divisionsWithNoBrackets = (left (right (var_init_divisions, length (var_init_divisions)-1), length (var_init_divisions)-2));
    SET var_divisions = CONCAT(REPLACE(var_divisionsWithNoBrackets, '][', ','), ',');

  WHILE (LOCATE(',', var_divisions ) > 0)
  DO
    SET var_division = SUBSTRING(var_divisions, 1, LOCATE(',', var_divisions) - 1);
    IF var_division IS NOT NULL AND LENGTH(var_division) > 0 THEN
      SET var_divisionID = (SELECT id FROM `cap`.`divisions` WHERE LOWER(`name`) = LOWER(var_division));

      IF var_divisionID IS NOT NULL THEN
        INSERT INTO `cap`.`series_divisions` VALUES(var_series_id , var_divisionID);
      END IF;
    END IF;
    SET var_divisions = SUBSTRING(var_divisions, LOCATE(',',var_divisions) + 1);
  END WHILE;
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER update_series_divisions_after_create_series
AFTER UPDATE ON `tngdb`.`CupContentSeries`
FOR EACH ROW
BEGIN
  DECLARE var_series_id INT(11);
    DECLARE var_init_divisions VARCHAR(255);
    DECLARE var_divisionsWithNoBrackets VARCHAR(255);
    DECLARE var_division VARCHAR(255);
    DECLARE var_divisionID INT(11);
    DECLARE var_divisions VARCHAR(255);

  SET var_series_id = NEW.id;
  SET var_init_divisions = NEW.divisions;
  SET var_divisionsWithNoBrackets = (left (right (var_init_divisions, length (var_init_divisions)-1), length (var_init_divisions)-2));
  SET var_divisions = CONCAT(REPLACE(var_divisionsWithNoBrackets, '][', ','), ',');

  DELETE FROM `cap`.`series_divisions` WHERE series_id = var_series_id;

  WHILE (LOCATE(',', var_divisions ) > 0)
  DO
    SET var_division = SUBSTRING(var_divisions, 1, LOCATE(',', var_divisions) - 1);
    IF var_division IS NOT NULL AND LENGTH(var_division) > 0 THEN
      SET var_divisionID = (SELECT id FROM `cap`.`divisions` WHERE LOWER(`name`) = LOWER(var_division));

      IF var_divisionID IS NOT NULL THEN
        INSERT INTO `cap`.`series_divisions` VALUES(var_series_id , var_divisionID);
      END IF;
    END IF;
    SET var_divisions = SUBSTRING(var_divisions, LOCATE(',',var_divisions) + 1);
  END WHILE;
END
|
DELIMITER ;

#title_divisions
DELIMITER |
CREATE TRIGGER save_title_divisions_after_create_title
AFTER INSERT ON `tngdb`.`CupContentTitle`
FOR EACH ROW
BEGIN
  DECLARE var_title_id INT(11);
  DECLARE var_init_divisions VARCHAR(255);
  DECLARE var_divisionsWithNoBrackets VARCHAR(255);
  DECLARE var_division VARCHAR(255);
  DECLARE var_divisionID INT(11);
  DECLARE var_divisions VARCHAR(255);

  SET var_title_id = NEW.id;
  SET var_init_divisions = NEW.divisions;
  SET var_divisionsWithNoBrackets = (left (right (var_init_divisions, length (var_init_divisions)-1), length (var_init_divisions)-2));
  SET var_divisions = CONCAT(REPLACE(var_divisionsWithNoBrackets, '][', ','), ',');

  WHILE (LOCATE(',', var_divisions ) > 0)
  DO
    SET var_division = SUBSTRING(var_divisions, 1, LOCATE(',', var_divisions) - 1);
    IF var_division IS NOT NULL AND LENGTH(var_division) > 0 THEN
      SET var_divisionID = (SELECT id FROM `cap`.`divisions` WHERE LOWER(`name`) = LOWER(var_division));

      IF var_divisionID IS NOT NULL THEN
        INSERT INTO `cap`.`title_divisions` VALUES(var_title_id , var_divisionID);
      END IF;
    END IF;
  SET var_divisions = SUBSTRING(var_divisions, LOCATE(',',var_divisions) + 1);
  END WHILE;
END
|
DELIMITER ;

DELIMITER |
CREATE TRIGGER update_title_divisions_after_create_title
AFTER UPDATE ON `tngdb`.`CupContentTitle`
FOR EACH ROW
BEGIN
  DECLARE var_title_id INT(11);
  DECLARE var_init_divisions VARCHAR(255);
  DECLARE var_divisionsWithNoBrackets VARCHAR(255);
  DECLARE var_division VARCHAR(255);
  DECLARE var_divisionID INT(11);
  DECLARE var_divisions VARCHAR(255);

  SET var_title_id = NEW.id;
  SET var_init_divisions = NEW.divisions;
  SET var_divisionsWithNoBrackets = (left (right (var_init_divisions, length (var_init_divisions)-1), length (var_init_divisions)-2));
  SET var_divisions = CONCAT(REPLACE(var_divisionsWithNoBrackets, '][', ','), ',');
  
  DELETE FROM `cap`.`title_divisions` WHERE title_id = var_title_id;
    
  WHILE (LOCATE(',', var_divisions ) > 0)
  DO
    SET var_division = SUBSTRING(var_divisions, 1, LOCATE(',', var_divisions) - 1);
    IF var_division IS NOT NULL AND LENGTH(var_division) > 0 THEN
      SET var_divisionID = (SELECT id FROM `cap`.`divisions` WHERE LOWER(`name`) = LOWER(var_division));
      
      IF var_divisionID IS NOT NULL THEN
        INSERT INTO `cap`.`title_divisions` VALUES(var_title_id , var_divisionID);
      END IF;
    END IF;
    SET var_divisions = SUBSTRING(var_divisions, LOCATE(',',var_divisions) + 1);
  END WHILE;
END
|
DELIMITER ;
