ALTER TABLE profile ADD COLUMN instagram VARCHAR(255) NOT NULL DEFAULT '' AFTER github;
ALTER TABLE profile ADD COLUMN tiktok VARCHAR(255) NOT NULL DEFAULT '' AFTER instagram;
UPDATE profile SET whatsapp = '';
DELETE FROM links WHERE icon = 'whatsapp' OR LOWER(label) LIKE '%whatsapp%';
