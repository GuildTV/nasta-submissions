SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `Test_Nasta_Submissions` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `Test_Nasta_Submissions`;

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `categories` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `compact_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `judge_id` int(10) UNSIGNED DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `closing_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `opening_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `compact_name`, `judge_id`, `description`, `closing_at`, `opening_at`, `created_at`, `updated_at`) VALUES
('already-closed', 'Already closed', 'already-closed', NULL, '', '2016-12-21 00:00:00', NULL, '2017-01-18 21:21:31', '2017-01-18 21:21:31'),
('animation', 'Animation', 'Male', 2, 'A single animation programme (or a shortened edit from an episode or series), or an original piece of animation of any type, which has been produced by your station.', '2020-02-20 19:00:00', NULL, '2016-11-26 16:57:31', '2016-11-26 16:57:31'),
('no-constraints', 'no file constraints!', 'no-constraints', NULL, '', '2028-04-17 00:00:00', NULL, '2017-01-18 21:15:43', '2017-01-18 21:15:43'),
('something', 'Something', 'Something', NULL, 'fbsgsdd', '2016-02-20 19:00:00', NULL, '2016-11-26 16:57:31', '2016-11-26 16:57:31');

CREATE TABLE `category_file_constraint` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_constraint_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `category_file_constraint` (`id`, `category_id`, `file_constraint_id`, `created_at`, `updated_at`) VALUES
(1, 'animation', 1, NULL, NULL),
(2, 'animation', 2, NULL, NULL),
(3, 'something', 3, '2017-01-06 18:32:55', '2017-01-06 18:32:55'),
(4, 'already-closed', 3, '2017-01-18 21:25:59', '2017-01-18 21:25:59');

CREATE TABLE `category_results` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `winner_id` int(10) UNSIGNED DEFAULT NULL,
  `winner_comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `commended_id` int(10) UNSIGNED DEFAULT NULL,
  `commended_comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `dropbox_accounts` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `used_space` bigint(20) NOT NULL DEFAULT '0',
  `total_space` bigint(20) NOT NULL DEFAULT '0',
  `dropbox_id` int(11) DEFAULT NULL,
  `access_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `dropbox_accounts` (`id`, `enabled`, `used_space`, `total_space`, `dropbox_id`, `access_token`, `created_at`, `updated_at`) VALUES
('test', 0, 27272822, 2147483648, NULL, 'oupaw3hcijAAAAAAAAAADNEqcrJR5JKstlbCBrnFTPhQ0WaSNkC_CQAUB9YdfG0z', '2016-12-03 16:16:36', '2017-01-15 22:20:55');

CREATE TABLE `encode_jobs` (
  `id` int(11) NOT NULL,
  `source_file` text NOT NULL COMMENT 'Path to source file (e.g. /mnt/UserData/Shows/ManMan/Ep1/10_Man-Man-episode1_sum06.avi)',
  `destination_file` text NOT NULL COMMENT 'Path to destination file (e.g. /mnt/videos/web/ipod/10_Man-Man-episode1_sum06.mp4)',
  `format_id` int(11) NOT NULL COMMENT 'ID to identify format type',
  `status` enum('Not Encoding','Waiting','Encoding Pass 1','Encoding Pass 2','Encoded','Moving File','Adding to Website','Done','Error') NOT NULL DEFAULT 'Not Encoding' COMMENT 'Indicates progress of the encode job. Set to "Not Encoding" initially',
  `progress` double NOT NULL,
  `video_id` int(11) DEFAULT NULL COMMENT 'ID of the relevant video_id. These entires are filled in before the video actaully exists.',
  `working_directory` text COMMENT 'Stores the working directory once a job has started',
  `user_id` int(11) DEFAULT NULL COMMENT 'ID of the user that requested the job',
  `priority` int(11) NOT NULL DEFAULT '5' COMMENT 'Mainly for batch encode jobs like re-encodes. Could be used for giving certain events higher priority. Defaults to 5. Higher numbers= lower priority'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Stores the Encode Jobs for EncodeSrv';

INSERT INTO `encode_jobs` (`id`, `source_file`, `destination_file`, `format_id`, `status`, `progress`, `video_id`, `working_directory`, `user_id`, `priority`) VALUES
(7, 'Nope.mp4', 'Nope-fixed.mp4', 5, 'Done', 100, NULL, NULL, NULL, 5);

CREATE TABLE `encode_watch` (
  `id` int(10) UNSIGNED NOT NULL,
  `uploaded_file_id` int(10) UNSIGNED NOT NULL,
  `job_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `encode_watch` (`id`, `uploaded_file_id`, `job_id`, `created_at`, `updated_at`) VALUES
(1, 131, 7, '2017-02-22 21:53:42', '2017-02-22 21:53:42');

CREATE TABLE `entries` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `station_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci,
  `rules` tinyint(1) NOT NULL DEFAULT '0',
  `submitted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `entries` (`id`, `category_id`, `station_id`, `name`, `description`, `rules`, `submitted`, `created_at`, `updated_at`) VALUES
(29, 'animation', 3, 'trsetestse', '', 1, 1, '2016-12-14 22:14:49', '2016-12-14 22:15:58'),
(72, 'something', 3, 'Test submission', 'Something exciting about pillows.', 1, 1, '2017-01-06 23:49:53', '2017-01-06 23:49:53'),
(73, 'already-closed', 4, 'Some of.', 'Alice had begun to think that there was no label this time she had gone through that day. \'No,.', 1, 0, '2017-02-08 23:25:27', '2017-02-08 23:25:27'),
(74, 'already-closed', 3, 'No room!\' they cried out when they liked, so that by the officers of the court with a sigh. \'I only took the least notice of them hit her in the sea, \'and in that ridiculous fashion.\' And he added looking angrily at the moment, \'My dear! I shall fall.', 'Alice. \'But you\'re so easily offended, you know!\' The Mouse did not notice this last remark that had made out the Fish-Footman was gone, and the m--\' But here, to Alice\'s side as she could do, lying down on one knee as he spoke, and the other was sitting on a bough of a treacle-well--eh, stupid?\' \'But they were lying round the rosetree; for, you see, Alice had never left off writing on his slate with one elbow against the roof of the others looked round also, and all of them say, \'Look out now, Five! Don\'t go splashing paint over me like that!\' By this time she had looked under it, and then unrolled the parchment scroll, and read out from his book, \'Rule Forty-two. ALL PERSONS MORE THAN A MILE HIGH TO LEAVE THE COURT.\' Everybody looked at it again: but he could think of nothing better to say "HOW DOTH THE LITTLE BUSY BEE," but it did not quite know what a wonderful dream it had VERY long claws and a fan! Quick, now!\' And Alice was beginning to think that proved it at last, and they sat down, and the turtles all advance! They are waiting on the same solemn tone, \'For the Duchess. An invitation for the hedgehogs; and in his note-book, cackled out \'Silence!\' and read out from his book, \'Rule Forty-two. ALL PERSONS MORE THAN A MILE HIGH TO LEAVE THE COURT.\' Everybody looked at the other side, the puppy began a series of short charges at the bottom of a sea of green leaves that lay far below her. \'What CAN all that stuff,\' the Mock Turtle, capering wildly about. \'Change lobsters again!\' yelled the Gryphon whispered in reply, \'for fear they should forget them before the end of the jury wrote it down \'important,\' and some were birds,) \'I suppose they are the jurors.\' She said this last remark, \'it\'s a vegetable. It doesn\'t look like it?\' he said, \'on and off, for days and days.\' \'But what happens when you come to the end: then stop.\' These were the two sides of the busy farm-yard--while the lowing of the gloves, and she soon found out a race-course, in a hot tureen! Who.', 0, 1, '2017-02-08 23:26:17', '2017-02-08 23:26:17'),
(75, 'animation', 4, 'I to get hold of it; so, after hunting all about for some time in silence: at last it sat down in a natural way again. \'I wonder what they\'ll do next! As for pulling me out of a bottle. They all sat down at her feet in the middle. Alice kept her eyes.', 'The poor little Lizard, Bill, was in livery: otherwise, judging by his face only, she would have appeared to them to be Number One,\' said Alice. \'Well, then,\' the Gryphon replied rather impatiently: \'any shrimp could have told you that.\' \'If I\'d been the right size, that it signifies much,\' she said to the game. CHAPTER IX. The Mock Turtle\'s heavy sobs. Lastly, she pictured to herself how she was out of his great wig.\' The judge, by the time when she first saw the White Rabbit with pink eyes ran close by it, and talking over its head. \'Very uncomfortable for the first figure,\' said the Pigeon; \'but if they do, why then they\'re a kind of serpent, that\'s all the things I used to come upon them THIS size: why, I should think it so yet,\' said Alice; \'but a grin without a porpoise.\' \'Wouldn\'t it really?\' said Alice very politely; but she heard her voice close to her, though, as they came nearer, Alice could see, when she first saw the Mock Turtle sighed deeply, and began, in rather a hard word, I will prosecute YOU.--Come, I\'ll take no denial; We must have imitated somebody else\'s hand,\' said the Gryphon, \'she wants for to know your history, she do.\' \'I\'ll tell it her,\' said the Caterpillar. Here was another long passage, and the soldiers shouted in reply. \'Please come back with the tarts, you know--\' \'What did they live at the time he had taken advantage of the Mock Turtle. \'Seals, turtles, salmon, and so on.\' \'What a number of cucumber-frames there must be!\' thought Alice. \'I\'m glad they don\'t give birthday presents like that!\' \'I couldn\'t help it,\' said the King. The White Rabbit hurried by--the frightened Mouse splashed his way through the neighbouring pool--she could hear him sighing as if she meant to take the place where it had been, it suddenly appeared again. \'By-the-bye, what became of the March Hare said to itself in a helpless sort of people live about here?\' \'In THAT direction,\' waving the other paw, \'lives a March Hare. \'Exactly so,\' said the King: \'leave.', 1, 0, '2017-02-08 23:26:25', '2017-02-08 23:26:25'),
(76, 'no-constraints', 3, 'I eat or drink something or other; but the Dormouse went on, half to Alice. \'What IS a Caucus-race?\' said Alice; \'but a grin without a grin,\' thought Alice; \'but when you have to ask any more HERE.\' \'But then,\' thought she, \'if people had all to lie.', 'CHAPTER II. The Pool of Tears \'Curiouser and curiouser!\' cried Alice (she was so much about a whiting to a farmer, you know, this sort in her brother\'s Latin Grammar, \'A mouse--of a mouse--to a mouse--a mouse--O mouse!\') The Mouse did not answer, so Alice ventured to remark. \'Tut, tut, child!\' said the March Hare,) \'--it was at the Queen, who had followed him into the wood. \'It\'s the oldest rule in the newspapers, at the place of the March Hare. \'He denies it,\' said the White Rabbit, \'but it seems to be no chance of this, so she went on eagerly: \'There is such a noise inside, no one else seemed inclined to say whether the pleasure of making a daisy-chain would be wasting our breath." "I\'ll be judge, I\'ll be jury," Said cunning old Fury: "I\'ll try the whole court was in the same thing with you,\' said Alice, \'but I know THAT well enough; and what does it matter to me whether you\'re a little way forwards each time and a large cat which was a large arm-chair at one corner of it: \'No room! No room!\' they cried out when they liked, so that they would die. \'The trial cannot proceed,\' said the Mock Turtle. Alice was beginning to think about stopping herself before she came upon a time she found to be two people. \'But it\'s no use in talking to herself, \'after such a capital one for catching mice--oh, I beg your acceptance of this ointment--one shilling the box-- Allow me to sell you a present of everything I\'ve said as yet.\' \'A cheap sort of way to change the subject,\' the March Hare went \'Sh! sh!\' and the roof off.\' After a time there were no arches left, and all of you, and listen to me! I\'LL soon make you dry enough!\' They all made of solid glass; there was no use in the common way. So they had to fall upon Alice, as the soldiers remaining behind to execute the unfortunate gardeners, who ran to Alice for some minutes. The Caterpillar was the first sentence in her pocket, and was going on rather better now,\' she said, \'and see whether it\'s marked "poison" or not\'; for.', 1, 0, '2017-02-08 23:26:25', '2017-02-08 23:26:25'),
(77, 'no-constraints', 4, 'Hatter continued, \'in this way:-- "Up above the world you fly, Like a tea-tray in the sea, though you mayn\'t believe it--\' \'I never saw one, or heard of one,\' said Alice, looking down at her with large round eyes, and feebly stretching out one paw,.', 'ME, and told me he was in March.\' As she said to herself, \'Now, what am I to do?\' said Alice. The King laid his hand upon her arm, and timidly said \'Consider, my dear: she is such a curious dream!\' said Alice, feeling very glad to find any. And yet I wish you were INSIDE, you might like to drop the jar for fear of their hearing her; and the other arm curled round her at the mushroom for a minute or two the Caterpillar sternly. \'Explain yourself!\' \'I can\'t go no lower,\' said the King, the Queen, tossing her head to feel which way it was done. They had a little recovered from the Gryphon, \'she wants for to know when the tide rises and sharks are around, His voice has a timid and tremulous sound.] \'That\'s different from what I get" is the capital of Paris, and Paris is the same as the rest of the Lobster Quadrille, that she was quite tired of this. I vote the young lady tells us a story.\' \'I\'m afraid I\'ve offended it again!\' For the Mouse had changed his mind, and was gone across to the confused clamour of the sense, and the other paw, \'lives a March Hare. \'Then it ought to speak, and no more to come, so she went on without attending to her, \'if we had the door of the Rabbit\'s voice along--\'Catch him, you by the officers of the sense, and the executioner myself,\' said the King: \'leave out that it ought to be managed? I suppose Dinah\'ll be sending me on messages next!\' And she began nibbling at the top of his Normans--" How are you getting on?\' said the March Hare. Alice was silent. The King turned pale, and shut his eyes.--\'Tell her about the same thing with you,\' said Alice, \'we learned French and music.\' \'And washing?\' said the Duchess, \'as pigs have to turn round on its axis--\' \'Talking of axes,\' said the Caterpillar. \'Not QUITE right, I\'m afraid,\' said Alice, who was gently brushing away some dead leaves that lay far below her. \'What CAN all that green stuff be?\' said Alice. \'And be quick about it,\' added the Queen. \'It proves nothing of the edge of her age knew.', 0, 1, '2017-02-08 23:26:25', '2017-02-08 23:26:25'),
(78, 'something', 4, 'Hatter: \'let\'s all move one place on.\' He moved on as he spoke, and added \'It isn\'t mine,\' said the March Hare, who had meanwhile been examining the roses. \'Off with her friend. When she got to go nearer till she shook the house, and the soldiers.', 'Alice. \'Then it doesn\'t matter a bit,\' said the King repeated angrily, \'or I\'ll have you executed, whether you\'re nervous or not.\' \'I\'m a poor man, your Majesty,\' said the Rabbit began. Alice thought this a good deal until she made her feel very uneasy: to be trampled under its feet, \'I move that the Gryphon went on, taking first one side and up the conversation a little. \'\'Tis so,\' said the Hatter. This piece of bread-and-butter in the after-time, be herself a grown woman; and how she would keep, through all her riper years, the simple and loving heart of her or of anything else. CHAPTER V. Advice from a bottle marked \'poison,\' it is you hate--C and D,\' she added aloud. \'Do you mean "purpose"?\' said Alice. \'I\'ve so often read in the house, and the Mock Turtle in a tone of great relief. \'Now at OURS they had been for some way of expressing yourself.\' The baby grunted again, and Alice heard the Queen said severely \'Who is this?\' She said it to be trampled under its feet, \'I move that the poor little thing was snorting like a snout than a real nose; also its eyes by this time, sat down again into its mouth open, gazing up into hers--she could hear him sighing as if he thought it would be worth the trouble of getting up and ran till she had expected: before she had quite a new pair of white kid gloves: she took up the fan and the Panther received knife and fork with a kind of sob, \'I\'ve tried the effect of lying down on the floor, as it could go, and making faces at him as he wore his crown over the verses to himself: \'"WE KNOW IT TO BE TRUE--" that\'s the jury, in a low trembling voice, \'--and I hadn\'t mentioned Dinah!\' she said aloud. \'I must be growing small again.\' She got up and leave the room, when her eye fell upon a heap of sticks and dry leaves, and the March Hare. Alice sighed wearily. \'I think I can say.\' This was quite out of that is, but I hadn\'t begun my tea--not above a week or so--and what with the lobsters and the Mock Turtle said: \'no wise fish would.', 1, 0, '2017-02-08 23:26:25', '2017-02-08 23:26:25');

CREATE TABLE `entries_folders` (
  `id` int(10) UNSIGNED NOT NULL,
  `entry_id` int(10) UNSIGNED NOT NULL,
  `folder_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `entry_results` (
  `id` int(10) UNSIGNED NOT NULL,
  `entry_id` int(10) UNSIGNED NOT NULL,
  `score` int(11) NOT NULL,
  `feedback` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `entry_rule_breaks` (
  `id` int(10) UNSIGNED NOT NULL,
  `entry_id` int(10) UNSIGNED NOT NULL,
  `result` enum('unknown','warning','break','ok','accepted','rejected') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unknown',
  `notes` text COLLATE utf8_unicode_ci NOT NULL,
  `constraint_map` text COLLATE utf8_unicode_ci NOT NULL,
  `warnings` text COLLATE utf8_unicode_ci NOT NULL,
  `errors` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `entry_rule_breaks` (`id`, `entry_id`, `result`, `notes`, `constraint_map`, `warnings`, `errors`, `created_at`, `updated_at`) VALUES
(1, 75, 'unknown', '', '{"195":1,"196":2}', '[]', '[]', '2017-02-08 23:26:25', '2017-02-08 23:26:25'),
(2, 76, 'warning', '', '[]', '[]', '[]', '2017-02-08 23:26:25', '2017-02-08 23:26:25'),
(3, 77, 'warning', '', '[]', '[]', '[]', '2017-02-08 23:26:25', '2017-02-08 23:26:25'),
(4, 29, 'ok', '', '{}', '[]', '[]', '2017-02-10 21:18:13', '2017-02-10 21:18:13');

CREATE TABLE `failed_jobs` (
  `id` int(10) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `file_constraints` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mimetypes` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `video_duration` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `file_constraints` (`id`, `name`, `description`, `mimetypes`, `video_duration`, `created_at`, `updated_at`) VALUES
(1, '10 Minute Video', 'A 10 minute video entry', 'video/mp4', NULL, '2016-11-26 16:57:31', '2016-11-26 16:57:31'),
(2, '500 Words', 'A 500 word document', 'application/pdf', NULL, '2016-11-26 16:57:31', '2016-11-26 16:57:31'),
(3, '2 Words', 'A 2 word document', 'application/pdf', NULL, '2016-11-26 16:57:31', '2016-11-26 16:57:31');

CREATE TABLE `google_accounts` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `used_space` bigint(20) NOT NULL DEFAULT '0',
  `total_space` bigint(20) NOT NULL DEFAULT '0',
  `target_dir` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(16, '2014_10_12_000000_create_users_table', 1),
(17, '2014_10_12_100000_create_password_resets_table', 1),
(18, '2016_11_22_215053_create_categories_table', 1),
(19, '2016_11_22_215734_create_file_constraint_table', 1),
(20, '2016_11_22_220038_create_category_file_constraint_pivot', 2),
(21, '2016_11_23_213723_create_entries_table', 3),
(26, '2016_11_27_130603_create_google_account_table', 4),
(29, '2016_11_27_171746_create_file_upload_table', 5),
(30, '2016_11_27_185538_alter_file_constraints_add_video_duration', 5),
(31, '2016_11_27_233951_create_file_upload_log_table', 6),
(32, '2016_11_28_001806_create_jobs_table', 7),
(37, '2013_04_09_062329_create_revisions_table', 8),
(38, '2016_12_01_224616_create_entries_folder_table', 8),
(40, '2016_12_03_153818_create_dropbox_account', 9),
(41, '2016_12_03_161959_create_station_folder', 10),
(44, '2016_12_03_174632_create_uploaded_files_table', 11),
(45, '2016_12_03_194216_alter_category_add_compact', 12),
(46, '2016_12_03_203551_create_uploaded_file_log_table', 13),
(47, '2017_01_08_170342_alter_uploaded_files_add_size', 14),
(48, '2017_01_11_191252_alter_uploaded_files_add_hash', 15),
(49, '2017_01_15_140523_alter_uploaded_files_add_local_path', 16),
(50, '2017_01_18_002345_alter_users_add_compact_name', 17),
(51, '2017_01_21_135623_alter_uploaded_files_add_public_url', 18),
(52, '2017_01_21_201651_create_video_metadata_table', 18),
(53, '2017_01_25_200005_alter_station_folders_add_category_id', 19),
(54, '2017_01_25_210634_alter_dropbox_accounts_add_dropbox_id', 19),
(55, '2017_01_26_184651_alter_station_folders_add_last_accessed_at', 20),
(56, '2017_01_26_190218_alter_uploaded_file_logs_add_file_id', 21),
(57, '2017_01_29_013514_create_cache_table', 22),
(58, '2017_01_29_013524_create_sessions_table', 22),
(59, '2017_01_30_223726_create_uploaded_file_rule_breaks', 23),
(60, '2017_01_31_224013_create_entry_rule_breaks', 23),
(61, '2017_02_02_232243_alter_users_add_last_login_at', 24),
(62, '2017_02_07_210752_alter_categories_add_judge_id', 25),
(63, '2017_02_07_214600_create_entry_results', 25),
(64, '2017_02_09_210557_create_category_results', 26),
(65, '2017_02_20_222434_alter_rule_breaks_add_notes', 27),
(66, '2017_02_21_221653_create_encode_watch', 28),
(67, '2017_02_07_214600_create_entry_results', 25);

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `revisions` (
  `id` int(10) UNSIGNED NOT NULL,
  `revisionable_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `revisionable_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `old_value` text COLLATE utf8_unicode_ci,
  `new_value` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8_unicode_ci,
  `payload` text COLLATE utf8_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `station_folders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `account_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `folder_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_accessed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `station_folders` (`id`, `user_id`, `account_id`, `category_id`, `request_url`, `folder_name`, `last_accessed_at`, `created_at`, `updated_at`) VALUES
(1, 3, 'test', NULL, 'https://www.dropbox.com/request/1FSUznsCcBN83Tzj7F56', '/File requests/Test Station Submissions', NULL, '2016-12-03 16:58:31', '2016-12-03 16:58:31');

CREATE TABLE `uploaded_files` (
  `id` int(10) UNSIGNED NOT NULL,
  `station_id` int(10) UNSIGNED NOT NULL,
  `category_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `account_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `size` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hash` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `path_local` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `public_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `video_metadata_id` int(10) UNSIGNED DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `uploaded_files` (`id`, `station_id`, `category_id`, `account_id`, `path`, `name`, `size`, `hash`, `path_local`, `public_url`, `video_metadata_id`, `uploaded_at`, `created_at`, `updated_at`) VALUES
(20, 3, 'animation', 'test', '/Imported/Test Station/Julian Waller - LSTV_Male_DennisTheMenace22.mp4', 'Julian Waller - LSTV_Male_DennisTheMenace22.mp4', '', '', NULL, NULL, NULL, '2016-12-03 19:49:44', '2016-12-03 19:49:44', '2016-12-03 19:49:44'),
(21, 3, NULL, 'test', '/Imported/Test Station/test  - fgf - LSTV_Male_DennisTheMenace22.mp4', 'test  - fgf - LSTV_Male_DennisTheMenace22.mp4', '', '', NULL, NULL, NULL, '2017-04-28 19:49:45', '2016-12-03 19:49:46', '2016-12-03 19:49:46'),
(22, 3, 'something', 'test', 'Nope', 'Fake file', '', '', NULL, NULL, NULL, '2017-04-28 19:49:45', '2016-12-03 19:49:46', '2016-12-03 19:49:46'),
(131, 3, 'something', 'test', 'Nope', 'Fake file', '', '', 'Nope', NULL, NULL, '2017-04-28 19:49:45', '2016-12-03 19:49:46', '2016-12-03 19:49:46'),
(190, 3, 'already-closed', 'test', '/no/no/no', 'not a file!', '400', 'fsdfsdf', NULL, NULL, NULL, '2017-02-24 00:00:00', '2017-01-18 21:31:53', '2017-01-18 21:31:53'),
(191, 3, NULL, 'test', '/no/no/no', 'not a file!', '400', 'fsdfsdf', NULL, NULL, NULL, '2017-02-24 00:00:00', '2017-01-18 21:31:53', '2017-01-18 21:31:53'),
(192, 3, 'something', 'test', '/no/no/no', 'not a file!', '400', 'fsdfsdf', NULL, NULL, NULL, '2017-02-24 00:00:00', '2017-01-18 21:31:53', '2017-01-18 21:31:53'),
(193, 3, 'animation', 'test', '/no/no/no', 'not a file!', '400', 'fsdfsdf', NULL, NULL, NULL, '2017-02-24 00:00:00', '2017-01-18 21:31:53', '2017-01-18 21:31:53'),
(194, 3, 'already-closed', 'test', 'YQrfdS2wXT', 'For a minute or two sobs choked his voice. \'Same as if she did not wish to offend the Dormouse crossed the court, she said to Alice. \'What IS the use of repeating all that green stuff be?\' said Alice. \'I wonder what CAN have happened to me! When I used.', '73544', 'EsaR7djVg2TB2vW7BBEWXAuEc5CiE64w7zkh53eJ', 'SgdiHqxu2Q41iiDIt5Js', 'http://localhost/vU9vAnNpPydvG4PtV3Dj', 2, '2017-02-08 23:26:17', '2017-02-08 23:26:17', '2017-02-08 23:26:17'),
(195, 4, 'animation', 'test', 't3xNU4xnv6', 'Alice. \'Why, you don\'t explain it is almost certain to disagree with you, sooner or later. However, this bottle was NOT marked \'poison,\' so Alice soon began talking to him,\' said Alice to herself. (Alice had no reason to be a comfort, one way--never to.', '67591', 'WUvIuOGTFR6O7GmrCritb44eDKECYlUI4HVzEC1S', 'a257BwoM1g3wbbNLHrBr', 'http://localhost/9xnfwI4QZY1XBUL8YB5J', 3, '2017-02-08 23:26:25', '2017-02-08 23:26:25', '2017-02-08 23:26:25'),
(196, 4, 'animation', 'test', 'tZxhqMrwaP', 'Alice began telling them her adventures from the roof. There were doors all round the refreshments!\' But there seemed to rise like a serpent. She had quite forgotten the Duchess replied, in a sorrowful tone; \'at least there\'s no use their putting their.', '96445', 'lMnq8rtwdwYJUMBoHT2IIGc9y4AMp4vE0c1WJqv4', 'GAinGaMI5L58eQN9ndWB', 'http://localhost/NGPOAZidP20QALmFqCX1', 4, '2017-02-08 23:26:25', '2017-02-08 23:26:25', '2017-02-08 23:26:25'),
(197, 4, 'something', 'test', 'gggEt0Gx6X', 'White Rabbit, with a sigh. \'I only took the regular course.\' \'What was THAT like?\' said Alice. The King looked anxiously over his shoulder with some severity; \'it\'s very rude.\' The Hatter shook his head sadly. \'Do I look like one, but it said in a.', '64950', 'S1ctTOfM7WA6GbmeJ4ZiPUMqTnVMqkeXFBt9rHy9', 'Bx9AMs6HqBxP7A7jbPpS', 'http://localhost/wZiX2VQ2ERB5MEuvcRvR', NULL, '2017-02-08 23:26:25', '2017-02-08 23:26:25', '2017-02-08 23:26:25');

CREATE TABLE `uploaded_file_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `station_id` int(10) UNSIGNED NOT NULL,
  `uploaded_file_id` int(10) UNSIGNED DEFAULT NULL,
  `category_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `uploaded_file_rule_breaks` (
  `id` int(10) UNSIGNED NOT NULL,
  `uploaded_file_id` int(10) UNSIGNED NOT NULL,
  `result` enum('unknown','warning','break','ok','accepted','rejected') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unknown',
  `notes` text COLLATE utf8_unicode_ci NOT NULL,
  `mimetype` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `length` int(11) NOT NULL,
  `metadata` text COLLATE utf8_unicode_ci NOT NULL,
  `warnings` text COLLATE utf8_unicode_ci NOT NULL,
  `errors` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `uploaded_file_rule_breaks` (`id`, `uploaded_file_id`, `result`, `notes`, `mimetype`, `length`, `metadata`, `warnings`, `errors`, `created_at`, `updated_at`) VALUES
(191, 20, 'break', '', 'video/mp4', 650, '{"audio":{"format":"AAC","bit_rate":317.375,"maximum_bit_rate":414.75,"channels":2,"sampling_rate":48000},"video":{"format":"AVC","bit_rate":9947.427,"bit_rate_mode":"VBR","maximum_bit_rate":19999.744,"format_profile":"High@L4.1","width":1280,"height":720,"pixel_aspect_ratio":1,"frame_rate":25,"scan_type":"Interlaced","standard":"PAL"},"wrapper":"video\\/mp4","duration":651.04}', '[]', '["video.scan_type","video.bit_rate","video.maximum_bit_rate","audio.bit_rate","audio.maximum_bit_rate"]', '2017-02-02 22:16:11', '2017-02-02 22:16:11'),
(192, 193, 'ok', '', 'application/pdf', 400, '[]', '[]', '[]', '2017-02-02 22:16:11', '2017-02-02 22:16:11');

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `compact_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` enum('station','judge','support','admin') COLLATE utf8_unicode_ci NOT NULL,
  `dropbox_account_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `users` (`id`, `name`, `compact_name`, `username`, `email`, `password`, `remember_token`, `type`, `dropbox_account_id`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'Test Admin', 'TestAdmin', 'test_admin', 'test@email.com', '$2y$10$EVf9rQNjszZywYF/7/opyOXuzo6heEfn8G4TD6Py6hUTPfMhKGDmO', '5WzQAVQSUiwxaDHippU7tgxHwtobQ2b7WuitkkxjQvJzpBKWU5EXE4SIndNS', 'admin', NULL, NULL, '2016-11-26 16:57:31', '2016-12-03 16:45:37'),
(2, 'Test Judge', 'TestJudge', 'test_judge', 'judge@email.com', '$2y$10$J82DBmvgsH59vsKxrMDCHe1BVu9ufIV/w44ldggAM6HCDpMvqxhvK', NULL, 'judge', NULL, NULL, '2016-11-26 16:57:31', '2016-11-26 16:57:31'),
(3, 'Test Station', 'TestStation', 'test_station', 'station@email.com', '$2y$10$qBA1pYoTPOLNMgi14B8P2u4GIf9kNu.XDSzOfQPn9XNr5Mo5Lvocm', 'QvY1MEsiYXWZbcwJvxroEOq8hd7nAZfv6PrsBYf2jThn4vtqCXcesnwP3Z75', 'station', NULL, NULL, '2016-11-26 16:57:31', '2016-12-03 15:51:24'),
(4, 'Station no submissions', 'Stationnosubmissions', 'no-subs', 'no@subs.com', '', NULL, 'station', NULL, NULL, '2017-01-17 09:09:57', '2017-01-17 09:09:57');

CREATE TABLE `video_metadata` (
  `id` int(10) UNSIGNED NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `video_metadata` (`id`, `width`, `height`, `duration`, `created_at`, `updated_at`) VALUES
(1, 5058, 5654, 17242, '2017-02-08 23:25:27', '2017-02-08 23:25:27'),
(2, 2934, 6426, 29312, '2017-02-08 23:26:17', '2017-02-08 23:26:17'),
(3, 5762, 6896, 11810, '2017-02-08 23:26:25', '2017-02-08 23:26:25'),
(4, 203, 9153, 30968, '2017-02-08 23:26:25', '2017-02-08 23:26:25');


ALTER TABLE `cache`
  ADD UNIQUE KEY `cache_key_unique` (`key`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_judge_id_foreign` (`judge_id`);

ALTER TABLE `category_file_constraint`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_file_constraint_category_id_foreign` (`category_id`),
  ADD KEY `category_file_constraint_file_constraint_id_foreign` (`file_constraint_id`);

ALTER TABLE `category_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_results_category_id_unique` (`category_id`),
  ADD UNIQUE KEY `category_results_winner_id_unique` (`winner_id`),
  ADD UNIQUE KEY `category_results_commended_id_unique` (`commended_id`);

ALTER TABLE `dropbox_accounts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `encode_jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `priority` (`priority`),
  ADD KEY `format_id` (`format_id`);

ALTER TABLE `encode_watch`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `encode_watch_uploaded_file_id_unique` (`uploaded_file_id`),
  ADD UNIQUE KEY `encode_watch_job_id_unique` (`job_id`);

ALTER TABLE `entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `entries_category_id_station_id_deleted_at_unique` (`category_id`,`station_id`),
  ADD KEY `entries_station_id_foreign` (`station_id`);

ALTER TABLE `entries_folders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `entries_folders_entry_id_unique` (`entry_id`);

ALTER TABLE `entry_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `entry_results_entry_id_unique` (`entry_id`);

ALTER TABLE `entry_rule_breaks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `entry_rule_breaks_entry_id_unique` (`entry_id`);

ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `file_constraints`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `google_accounts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_reserved_at_index` (`queue`,`reserved_at`);

ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`),
  ADD KEY `password_resets_token_index` (`token`);

ALTER TABLE `revisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `revisions_revisionable_id_revisionable_type_index` (`revisionable_id`,`revisionable_type`);

ALTER TABLE `sessions`
  ADD UNIQUE KEY `sessions_id_unique` (`id`);

ALTER TABLE `station_folders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `station_folders_user_id_category_id_unique` (`user_id`,`category_id`),
  ADD KEY `station_folders_account_id_foreign` (`account_id`),
  ADD KEY `station_folders_user_id_index` (`user_id`),
  ADD KEY `station_folders_category_id_foreign` (`category_id`);

ALTER TABLE `uploaded_files`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uploaded_files_path_local_unique` (`path_local`),
  ADD UNIQUE KEY `uploaded_files_public_url_unique` (`public_url`),
  ADD UNIQUE KEY `uploaded_files_video_metadata_id_unique` (`video_metadata_id`),
  ADD KEY `uploaded_files_account_id_foreign` (`account_id`),
  ADD KEY `uploaded_files_station_id_foreign` (`station_id`),
  ADD KEY `uploaded_files_category_id_foreign` (`category_id`);

ALTER TABLE `uploaded_file_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_file_logs_category_id_foreign` (`category_id`),
  ADD KEY `uploaded_file_logs_station_id_foreign` (`station_id`),
  ADD KEY `uploaded_file_logs_uploaded_file_id_foreign` (`uploaded_file_id`);

ALTER TABLE `uploaded_file_rule_breaks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uploaded_file_rule_breaks_uploaded_file_id_unique` (`uploaded_file_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_compact_name_unique` (`compact_name`),
  ADD KEY `users_dropbox_account_id_foreign` (`dropbox_account_id`);

ALTER TABLE `video_metadata`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `category_file_constraint`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `category_results`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `encode_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
ALTER TABLE `encode_watch`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
ALTER TABLE `entries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;
ALTER TABLE `entries_folders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `entry_results`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
ALTER TABLE `entry_rule_breaks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `failed_jobs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
ALTER TABLE `file_constraints`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;
ALTER TABLE `revisions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `station_folders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `uploaded_files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;
ALTER TABLE `uploaded_file_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
ALTER TABLE `uploaded_file_rule_breaks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `video_metadata`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `categories`
  ADD CONSTRAINT `categories_judge_id_foreign` FOREIGN KEY (`judge_id`) REFERENCES `users` (`id`);

ALTER TABLE `category_file_constraint`
  ADD CONSTRAINT `category_file_constraint_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_file_constraint_file_constraint_id_foreign` FOREIGN KEY (`file_constraint_id`) REFERENCES `file_constraints` (`id`) ON DELETE CASCADE;

ALTER TABLE `category_results`
  ADD CONSTRAINT `category_results_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `category_results_commended_id_foreign` FOREIGN KEY (`commended_id`) REFERENCES `entries` (`id`),
  ADD CONSTRAINT `category_results_winner_id_foreign` FOREIGN KEY (`winner_id`) REFERENCES `entries` (`id`);

ALTER TABLE `encode_watch`
  ADD CONSTRAINT `encode_watch_uploaded_file_id_foreign` FOREIGN KEY (`uploaded_file_id`) REFERENCES `uploaded_files` (`id`) ON DELETE CASCADE;

ALTER TABLE `entries`
  ADD CONSTRAINT `entries_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `entries_station_id_foreign` FOREIGN KEY (`station_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `entries_folders`
  ADD CONSTRAINT `entries_folders_entry_id_foreign` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`id`) ON DELETE CASCADE;

ALTER TABLE `entry_results`
  ADD CONSTRAINT `entry_results_entry_id_foreign` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`id`) ON DELETE CASCADE;

ALTER TABLE `entry_rule_breaks`
  ADD CONSTRAINT `entry_rule_breaks_entry_id_foreign` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`id`) ON DELETE CASCADE;

ALTER TABLE `station_folders`
  ADD CONSTRAINT `station_folders_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `dropbox_accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `station_folders_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `station_folders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `uploaded_files`
  ADD CONSTRAINT `uploaded_files_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `dropbox_accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `uploaded_files_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `uploaded_files_station_id_foreign` FOREIGN KEY (`station_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `uploaded_files_video_metadata_id_foreign` FOREIGN KEY (`video_metadata_id`) REFERENCES `video_metadata` (`id`) ON DELETE CASCADE;

ALTER TABLE `uploaded_file_logs`
  ADD CONSTRAINT `uploaded_file_logs_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `uploaded_file_logs_station_id_foreign` FOREIGN KEY (`station_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `uploaded_file_logs_uploaded_file_id_foreign` FOREIGN KEY (`uploaded_file_id`) REFERENCES `uploaded_files` (`id`) ON DELETE CASCADE;

ALTER TABLE `uploaded_file_rule_breaks`
  ADD CONSTRAINT `uploaded_file_rule_breaks_uploaded_file_id_foreign` FOREIGN KEY (`uploaded_file_id`) REFERENCES `uploaded_files` (`id`) ON DELETE CASCADE;

ALTER TABLE `users`
  ADD CONSTRAINT `users_dropbox_account_id_foreign` FOREIGN KEY (`dropbox_account_id`) REFERENCES `dropbox_accounts` (`id`) ON DELETE CASCADE;
