-- Safe on both existing installations and fresh schemas.
SET @schema_name = DATABASE();

SET @has_instagram = (
    SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = @schema_name AND table_name = 'profile' AND column_name = 'instagram'
);
SET @statement = IF(@has_instagram = 0,
    'ALTER TABLE profile ADD COLUMN instagram VARCHAR(255) NOT NULL DEFAULT '''' AFTER github',
    'SELECT 1'
);
PREPARE migration_statement FROM @statement;
EXECUTE migration_statement;
DEALLOCATE PREPARE migration_statement;

SET @has_tiktok = (
    SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = @schema_name AND table_name = 'profile' AND column_name = 'tiktok'
);
SET @statement = IF(@has_tiktok = 0,
    'ALTER TABLE profile ADD COLUMN tiktok VARCHAR(255) NOT NULL DEFAULT '''' AFTER instagram',
    'SELECT 1'
);
PREPARE migration_statement FROM @statement;
EXECUTE migration_statement;
DEALLOCATE PREPARE migration_statement;

SET @has_whatsapp = (
    SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = @schema_name AND table_name = 'profile' AND column_name = 'whatsapp'
);
SET @statement = IF(@has_whatsapp = 1, 'UPDATE profile SET whatsapp = ''''', 'SELECT 1');
PREPARE migration_statement FROM @statement;
EXECUTE migration_statement;
DEALLOCATE PREPARE migration_statement;

DELETE FROM links WHERE icon = 'whatsapp' OR LOWER(label) LIKE '%whatsapp%';

UPDATE profile SET 
    instagram = IF(instagram IS NULL OR instagram = '', 'https://instagram.com/zentokun90', instagram),
    tiktok = IF(tiktok IS NULL OR tiktok = '', 'https://tiktok.com/@zentokun90', tiktok)
WHERE id = 1;
