--- STEP 1: INSERT access codes
--- NOTE: Copy the warning result generated, if there are ANY duplicates
INSERT IGNORE INTO hub.permissions (`entitlement_id`, `batch_id`, `proof`, `limit`, `released_at`)
VALUES (4180,7235,'SL06-RQ21-NUN9-AY94',1, NOW()), (4180,7235,'YO08-GI16-BU20-BU08',1, NOW()), (4180,7235,'ICE2-BE06-AR94-UO75',1, NOW());