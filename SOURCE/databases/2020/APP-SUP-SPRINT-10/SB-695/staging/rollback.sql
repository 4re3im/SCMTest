--- STEP 1: Rollback the inserted proofs (access codes)
DELETE FROM hub.permissions WHERE proof IN(
'SL06-RQ21-NUN9-AY94',
'YO08-GI16-BU20-BU08',
'ICE2-BE06-AR94-UO75'
);