-- This script upgrades table structure, data, and privileges
-- from/to the exact version numbers specified in the filename.

--
-- First execute a check whether this upgrade should apply. The check
-- below should fail if this upgrade has already been applied, but
-- keep everything unchanged if not.
--

-- @UPGRADE-CHECK@
ALTER TABLE `problem`
    ADD COLUMN`source` text;
ALTER TABLE `problem`
    DROP COLUMN `source`;

--
-- Create additional structures
--

ALTER TABLE `problem`
    ADD COLUMN `source` varchar(255) COMMENT 'Source of this problem' after `name`,
    ADD COLUMN `problemtext_html` longtext COMMENT 'HTML problem text';

ALTER TABLE `contest`
    ADD COLUMN `scoreboardhideproblems` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If 1, will hide problems in the scoreboard',
    ADD COLUMN `scoreboardhidetime` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If 1, will hide time in the scoreboard';

--
-- Transfer data from old to new structure
--



--
-- Add/remove sample/initial contents
--

--
-- Finally remove obsolete structures after moving data
--

