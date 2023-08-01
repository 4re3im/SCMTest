--- Rollback: Run
UPDATE tngdb.Hotmaths_API
SET access_token = '0355986a-ad1a-4369-96dc-4ea75a3c9043', refresh_token = '8602e0b1-817a-4c30-b951-3b37e746b427'
WHERE ID = 1 AND env = 'testing';