--- Rollback: Run
UPDATE tngdb.Hotmaths_API
SET access_token = 'b5271794-c1a6-427c-b865-5c2c5a17e318', refresh_token = '619b0597-ee11-4d09-8666-55257f063aad'
WHERE ID = 2 AND env = 'production';