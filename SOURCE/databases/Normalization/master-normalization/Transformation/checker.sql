	-- Lookup tables - check
		SELECT * FROM cap.year_levels;
		select * from cap.divisions;
		select * from cap.states;
		select * from cap.countries;
        select * from cap.editions;

	-- CupContentAuthor - check
        SELECT * FROM tngdb.CupContentAuthor WHERE id NOT IN (SELECT id FROM cap.authors); -- expect 0 row

	-- CupContentSubject - check
		SELECT * FROM tngdb.CupContentSubject WHERE id NOT IN (SELECT id FROM cap.subjects); -- expect 0 row

	-- CupContentFormat - check
		SELECT * FROM tngdb.CupContentFormat WHERE id NOT IN (SELECT id FROM cap.formats); -- expect 0 row

	-- CupContentSeries - check
		SELECT * FROM tngdb.CupContentSeries WHERE id NOT IN (SELECT id FROM tngdb_tempo.CupContentSeries); -- expect 0 row
		SELECT * FROM tngdb.CupContentSeries WHERE id NOT IN (SELECT id FROM cap.series); -- expect 0 row

		-- SELECT * FROM cap.series_divisions; -- if count not matched, check CupContentSeries invalid data
		-- check cap count for division
        SELECT sd.series_id, GROUP_CONCAT(d.name SEPARATOR ', ') FROM cap.series_divisions sd JOIN cap.divisions d ON sd.division_id = d.id GROUP BY sd.series_id;
        -- check tng count for division
        SELECT id, divisions FROM tngdb.CupContentSeries;

        -- SELECT * FROM cap.series_year_levels; -- if count not matched, check CupContentSeries invalid data
        -- check cap count for year levels
        SELECT sy.series_id, GROUP_CONCAT(y.level SEPARATOR ', ') FROM cap.series_year_levels sy JOIN cap.year_levels y ON sy.year_level_id = y.id GROUP BY sy.series_id;
        -- check tng count for division
        SELECT id, yearLevels FROM tngdb.CupContentSeries;

        -- SELECT * FROM cap.series_states; -- if count not matched, check CupContentSeries invalid data
        -- check cap count for states
        SELECT st.series_id, GROUP_CONCAT(s.name SEPARATOR ', ') FROM cap.series_states st JOIN cap.states s ON st.state_id = s.id GROUP BY st.series_id;
        -- check tng count for division
        SELECT id, regions FROM tngdb.CupContentSeries;

		-- SELECT * FROM cap.series_reviews; -- should be 0 row
        SELECT id FROM tngdb.CupContentSeries WHERE id NOT IN (SELECT id FROM cap.series);



	-- CupContentSeriesFormats (double check this one)
        -- SELECT * FROM tngdb.CupContentSeriesFormats;
        -- SELECT * FROM cap.series_formats;
        -- check cap count for series formats
        SELECT sf.series_id, GROUP_CONCAT(f.name SEPARATOR ', ') AS cap_formats FROM cap.series_formats sf JOIN cap.formats f ON sf.format_id = f.id GROUP BY sf.series_id;
        -- counter check in tngdb
        SELECT ccsf.seriesID, GROUP_CONCAT(ccsf.format SEPARATOR ', ') AS tngdb_formats FROM tngdb.CupContentSeriesFormats ccsf GROUP BY ccsf.seriesID;

	-- CupContentSeriesSubjects (double check this one)
        -- SELECT * FROM tngdb.CupContentSeriesSubjects;
        -- SELECT * FROM cap.series_subjects;
        -- check cap count for series subjects
        SELECT ss.series_id, GROUP_CONCAT(s.name SEPARATOR ' & ') AS cap_subjects FROM cap.series_subjects ss JOIN cap.subjects s ON ss.subject_id = s.id GROUP BY ss.series_id;
        -- counter check in tngdb
        SELECT ccsf.seriesID, GROUP_CONCAT(ccsf.subject SEPARATOR ' & ') FROM tngdb.CupContentSeriesSubjects ccsf GROUP BY ccsf.seriesID;



	-- CupContentTitle (double check this one, all join tables)
		SELECT * FROM tngdb.CupContentTitle WHERE id NOT IN (SELECT id FROM cap.titles);

        -- Check title year levels
        -- check in cap
        SELECT t.id, t.name, GROUP_CONCAT(yl.level SEPARATOR ', ') FROM cap.title_year_levels tyl JOIN cap.titles t ON tyl.title_id = t.id JOIN cap.year_levels yl ON tyl.year_level_id = yl.id GROUP BY t.id;
        -- counter check in tngdb
        SELECT cct.id, cct.name, cct.yearLevels FROM tngdb.CupContentTitle cct;

        -- Check title states
        -- check in cap
		SELECT t.id, t.name, GROUP_CONCAT(s.name SEPARATOR ', ') AS cap_title_states FROM cap.title_states ts JOIN cap.titles t ON t.id = ts.title_id JOIN cap.states s ON ts.state_id = s.id GROUP BY t.id;
        -- counter check in tngdb
        SELECT cct.id, cct.name, cct.regions FROM tngdb.CupContentTitle cct;

        -- Check title divisions
        -- check in cap
        SELECT t.id, t.name, GROUP_CONCAT(d.name, ', ') AS cap_title_divisions FROM cap.title_divisions td JOIN cap.titles t ON td.title_id = t.id JOIN cap.divisions d ON td.division_id GROUP BY t.id;
        -- counter check in tngdb
        SELECT cct.id, cct.name, cct.divisions FROM tngdb.CupContentTitle cct;

	-- CupContentTitleAuthors
		SELECT * FROM tngdb.CupContentTitleAuthors WHERE titleID NOT IN (SELECT title_id FROM cap.title_authors); -- expect 0 rows
        -- counter check in tngdb and cap
        -- Check in cap
        SELECT t.id, t.name, GROUP_CONCAT(a.name SEPARATOR ', ') AS title_authors FROM cap.title_authors ta JOIN cap.authors a ON ta.author_id = a.id JOIN cap.titles t ON ta.title_id = t.id GROUP BY t.id;
        -- Check in tngdb
        SELECT cct.id, cct.name, GROUP_CONCAT(ccta.author SEPARATOR ', ') As title_authors FROM tngdb.CupContentTitleAuthors ccta JOIN tngdb.CupContentTitle cct ON ccta.titleID = cct.id GROUP BY cct.id;



	-- CupContentTitleSamplePages
		SELECT * FROM tngdb.CupContentTitleSamplePages WHERE id NOT IN (SELECT id FROM cap.title_sample_pages); -- expect 0 rows
        -- counter check in tngdb and cap - done no relatable title to title sample page
        SELECT * FROM cap.title_sample_pages;
        SELECT * FROM tngdb.CupContentTitleSamplePages;



	-- CupContentTitleSubjects
		SELECT * FROM tngdb.CupContentTitleSubjects WHERE titleID NOT IN (SELECT title_id FROM cap.title_subjects); -- expect 0 rows
        -- counter check in tngdb and cap
        -- check in cap
        SELECT t.id, t.name, GROUP_CONCAT(s.name SEPARATOR ' | ') AS cap_title_subjects FROM cap.title_subjects ts JOIN cap.titles t ON ts.title_id = t.id JOIN cap.subjects s ON ts.subject_id = s.id GROUP BY t.id;
        -- check in tngdb
        SELECT cct.id, cct.name, GROUP_CONCAT(ccts.subject SEPARATOR ' | ') AS tngdb_title_subjects FROM tngdb.CupContentTitleSubjects ccts JOIN tngdb.CupContentTitle cct ON ccts.titleID = cct.id GROUP BY cct.id;



	-- CupContentTitleRelatedTitle
        SELECT * FROM tngdb.CupContentTitleRelatedTitle WHERE titleID NOT IN (SELECT title_id FROM cap.title_related_titles);
        -- counter check in tngdb and cap
        -- check in cap
        SELECT t1.id, t1.name, t2.id AS related_title_id, t2.name AS related_title_name FROM cap.title_related_titles trt JOIN cap.titles t1 ON trt.title_id = t1.id JOIN cap.titles t2 ON trt.related_title_id = t2.id;
        -- NOTE: expect null value in related_titleID as this should be deleted in the first place
        -- check in tngdb
        SELECT cct1.id, cct1.name, cct2.id AS related_title_id, cct2.name AS related_title_name FROM tngdb.CupContentTitleRelatedTitle cctrt JOIN tngdb.CupContentTitle cct1 ON cctrt.titleID = cct1.id JOIN tngdb.CupContentTitle cct2 ON cctrt.related_titleID = cct2.id;

    -- CupGoBrandCodeTitles
        SELECT * FROM tngdb.CupGoBrandCodeTitles WHERE (titleID, brandCode) NOT IN (SELECT title_id, bc.name FROM cap.title_brand_codes tbc JOIN cap.brand_codes bc ON tbc.brand_code_id = bc.id); -- expect 0 row

	-- CupContentTitleSupportingTitle - TODO
		SELECT * FROM tngdb.CupContentTitleSupportingTitle WHERE (titleID, supporting_titleID) NOT IN (SELECT title_id, supporting_title_id FROM cap.title_supporting_titles);
		SELECT * FROM `tngdb_tempo`.`CupContentTitleSupportingTitle`;
        SELECT * FROM cap.title_supporting_titles;

        SELECT * FROM tngdb.CupContentTitleSupportingTitle WHERE supporting_titleID NOT IN (SELECT ID FROM tngdb.CupContentTitle);
        -- NOTE: Did not include rows that don't have existing supporting_title_id in the `title` table

    -- CupGoContentFolders
		SELECT * FROM tngdb.CupGoContentFolders WHERE ID NOT IN (SELECT id FROM cap.content_folders); -- expect 0 rows
        -- counter check in tngdb and cap
        -- check in cap
        SELECT t.id, t.name, GROUP_CONCAT(cf.name SEPARATOR ', ') AS cap_content_folders FROM cap.content_folders cf JOIN cap.titles t ON cf.title_id = t.id GROUP BY t.id;
        SELECT cct.id, cct.name, GROUP_CONCAT(cgcf.FolderName SEPARATOR ', ') AS tngdb_content_folders FROM tngdb.CupGoContentFolders cgcf JOIN tngdb.CupContentTitle cct ON cgcf.TitleID = cct.id GROUP BY cct.id;


	-- CupGoContent
		SELECT * FROM tngdb.CupGoContent WHERE ID NOT IN (SELECT id FROM cap.contents); 
        -- counter check in tngdb and cap
        SELECT * FROM tngdb.CupGoContent;
		SELECT * FROM cap.contents;
        -- NOTE: expect data didn't transform as some contents doesn't have folder
        
        SELECT * FROM tngdb.CupGoFolderContent WHERE ContentID IN (SELECT ID FROM tngdb_tempo.CupGoContent WHERE ID NOT IN (SELECT id FROM cap.contents));
        SELECT * FROM tngdb_tempo.CupGoContent WHERE ID NOT IN (SELECT id FROM cap.contents);

        SELECT * FROM tngdb.CupGoFolderContent;
        SELECT * FROM `tngdb`.`CupGoContent`;

        SELECT * FROM `tngdb`.`CupGoContent` WHERE ID not in (SELECT ContentID FROM tngdb.CupGoFolderContent);
        SELECT * FROM tngdb.CupGoFolderContent WHERE FolderID not in (SELECT ID FROM `tngdb`.`CupGoContentFolders`);
        -- NOTE: Did not include rows that don't have existing folder_id in the CupGoContentFolders table


	-- CupGoContentDetail
		SELECT * FROM tngdb.CupGoContentDetail WHERE ID NOT IN (SELECT id FROM cap.content_details); -- expect null values which contentID is not nullable in our cap
        -- counter check in tngdb and cap
        SELECT * FROM tngdb.CupGoContentDetail;
        SELECT * FROM cap.content_details;
        SELECT * FROM cap.content_detail_types;
        -- NOTE: didn't cover content details that have content_id null since ive tried to delete details and its content heading = the content_detail was also deleted in the database
        -- didn't cover content_detail_types that have type_id = NULL since this is required and a dropdown in the UI

	-- CupGoTabs
		SELECT * FROM tngdb.CupGoTabs WHERE ID NOT IN (SELECT id FROM cap.tabs);
        -- counter check in tngdb and cap
        SELECT * FROM tngdb.CupGoTabs;
		SELECT * FROM cap.tabs;
        SELECT * FROM cap.tab_details;

	-- CupGoTabContent, difference of 1 because tab_id is null
		SELECT * FROM tngdb.CupGoTabContent WHERE id NOT IN (SELECT id FROM cap.tab_contents);
        -- counter check in tngdb and cap
        SELECT * FROM tngdb.CupGoTabContent;
		SELECT * FROM cap.tab_contents;
        -- NOTE: didnt allow row that have null TabID