INSERT INTO `articles` (`id`, `title`, `contents`, `category`, `status`, `ready`, `last_updated`) VALUES
(1, 'Test', 'Dit is een test.', 'dit is de context', 1, 'open', 0, '2022-06-21 15:20:43'),
(2, 'Een nieuw stukje.', 'Dit is een nieuw stukje met een aanpassing.', '', 1, 'open', 0, '2022-06-21 15:36:27');

INSERT INTO `article_updates` (`id`, `article_id`, `update_type`, `changed_status`, `changed_title`, `changed_contents`, `changed_category`, `changed_ready`, `user`, `timestamp`) VALUES
(1, 1, 2, 'open', 'Test', 'Dit is een test.', 1, 0, 1, '2022-06-21 15:20:43'),
(2, 2, 2, 'open', 'Een nieuw stukje.', 'Dit is een nieuw stukje.', 1, 0, 1, '2022-06-21 15:36:27'),
(3, 2, 4, NULL, NULL, NULL, NULL, NULL, 2, '2022-06-21 15:52:14'),
(4, 2, 3, NULL, NULL, 'Dit is een nieuw stukje met een aanpassing.', NULL, NULL, 1, '2022-06-22 17:54:44'),
(5, 2, 4, NULL, NULL, NULL, NULL, NULL, 2, '2022-06-22 18:01:07');
