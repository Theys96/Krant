-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Aug 15, 2022 at 05:57 PM
-- Server version: 5.7.39
-- PHP Version: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `krant`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `contents` text NOT NULL,
  `context` text NOT NULL,
  `category` int(11) DEFAULT NULL,
  `status` set('draft','open','placed','bin') NOT NULL DEFAULT 'draft',
  `ready` tinyint(1) NOT NULL DEFAULT '0',
  `picture` tinyint(1) NOT NULL DEFAULT '0',
  `wjd` tinyint(1) NOT NULL DEFAULT '0',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `article_updates`
--

CREATE TABLE `article_updates` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `update_type` int(11) NOT NULL,
  `changed_status` enum('draft','open','placed','bin') NOT NULL,
  `changed_title` text NOT NULL,
  `changed_contents` text NOT NULL,
  `changed_context` text NOT NULL,
  `changed_category` int(11) DEFAULT NULL,
  `changed_ready` tinyint(1) NOT NULL,
  `changed_picture` tinyint(1) NOT NULL,
  `changed_wjd` tinyint(1) NOT NULL,
  `user` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `article_update_types`
--

CREATE TABLE `article_update_types` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL,
  `author` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `article_update_types`
--

INSERT INTO `article_update_types` (`id`, `description`, `author`) VALUES
(1, '%s is begonnen aan een wijziging zonder deze op te slaan.', 0),
(2, '%s is aan het stukje begonnen.', 1),
(3, '%s heeft het stukje aangepast.', 1),
(4, '%s heeft het stukje nagekeken.', 0),
(5, '%s heeft het stukje naar de prullenbak verplaatst.', 0),
(6, '%s heeft het stukje als geplaatst gemarkeerd.', 0),
(7, '%s heeft de categorie van het stukje verwijderd.', 0),
(8, '%s heeft het stukje teruggeplaatst.', 0),
(9, '%s heeft het stukje overgezet.', 0);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `article_number` int(11) NOT NULL DEFAULT '5',
  `picture_number` int(11) NOT NULL DEFAULT '2',
  `wjd_number` int(11) NOT NULL DEFAULT '7',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `edition` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `active`, `edition`) VALUES
(1, 'Algemeen', 'Algemeen', 5, 2, 7, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `editions`
--

CREATE TABLE `editions` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `editions`
--

INSERT INTO `editions` (`id`, `name`, `description`, `active`) VALUES
(1, 'Eerste editie', 'Eerste editie', 1);

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `type` enum('info','warning','error','feedback') NOT NULL,
  `user` int(11) DEFAULT NULL,
  `role` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `address` text NOT NULL,
  `request` text NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `perm_level` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `alt_css` tinyint(2) NOT NULL DEFAULT '0',
  `highscore` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `perm_level`, `active`, `alt_css`, `highscore`) VALUES
(1, 'Thijs', 3, 1, 0, 0),
(2, 'Renske', 3, 1, 0, 0);

--
-- Table structure for table `single_variables`
--

CREATE TABLE `configuration` (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `single_variables`
--

INSERT INTO `configuration` (`id`, `name`, `value`) VALUES
(1, 'schrijfregels', 'Dit zijn de schrijfregels'),
(2, 'min_checks', '3'),
(3, 'mail_address', null),
(4, 'passwords', ',,,printer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `articles_categories` (`category`);

--
-- Indexes for table `article_updates`
--
ALTER TABLE `article_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_updates_articles` (`article_id`),
  ADD KEY `article_updates_categories` (`changed_category`),
  ADD KEY `article_updates_users` (`user`),
  ADD KEY `article_updates_article_update_types` (`update_type`);

--
-- Indexes for table `article_update_types`
--
ALTER TABLE `article_update_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_editions` (`edition`);

--
-- Indexes for table `editions`
--
ALTER TABLE `editions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `log_users` (`user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `single_variables`
--
ALTER TABLE `configuration`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `article_updates`
--
ALTER TABLE `article_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `editions`
--
ALTER TABLE `editions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_categories` FOREIGN KEY (`category`) REFERENCES `categories` (`id`);

--
-- Constraints for table `article_updates`
--
ALTER TABLE `article_updates`
  ADD CONSTRAINT `article_updates_article_update_types` FOREIGN KEY (`update_type`) REFERENCES `article_update_types` (`id`),
  ADD CONSTRAINT `article_updates_articles` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`),
  ADD CONSTRAINT `article_updates_categories` FOREIGN KEY (`changed_category`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `article_updates_users` FOREIGN KEY (`user`) REFERENCES `users` (`id`);

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_editions` FOREIGN KEY (`edition`) REFERENCES `editions` (`id`);

--
-- Constraints for table `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `log_users` FOREIGN KEY (`user`) REFERENCES `users` (`id`);


CREATE TABLE `article_reactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reaction` VARCHAR(8) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `article_reactions`
  ADD CONSTRAINT `article_reactions_unique_user_article` UNIQUE (`article_id`, `user_id`),
  ADD CONSTRAINT `article_reactions_articles` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`),
  ADD CONSTRAINT `article_reactions_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
