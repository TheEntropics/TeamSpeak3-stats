-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Creato il: Feb 04, 2016 alle 17:59
-- Versione del server: 10.1.11-MariaDB-log
-- Versione PHP: 7.0.2

SET time_zone = "+00:00";

--
-- Database: `ts3stats_debug`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `channel_events`
--

CREATE TABLE `channel_events` (
  `id` int(11) NOT NULL,
  `date` timestamp(6) NOT NULL,
  `type` int(11) NOT NULL,
  `name` varchar(50) NOT NULL COLLATE utf8_bin,
  `user_id` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `client_connected_events`
--

CREATE TABLE `client_connected_events` (
  `id` int(11) NOT NULL,
  `date` timestamp(6) NOT NULL,
  `ip` varchar(15) NOT NULL COLLATE utf8_bin,
  `user_id` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `client_disconnected_events`
--

CREATE TABLE `client_disconnected_events` (
  `id` int(11) NOT NULL,
  `date` timestamp(6) NOT NULL,
  `reason` varchar(200) NOT NULL COLLATE utf8_bin,
  `user_id` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `daily_results`
--

CREATE TABLE `daily_results` (
  `timestamp` int(11) NOT NULL,
  `average` float NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `daily_user_result`
--

CREATE TABLE `daily_user_result` (
  `client_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `file_manager_events`
--

CREATE TABLE `file_manager_events` (
  `id` int(11) NOT NULL,
  `date` timestamp(6) NULL DEFAULT NULL,
  `type` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `misc_results`
--

CREATE TABLE `misc_results` (
  `key` varchar(50) NOT NULL COLLATE utf8_bin,
  `value` varchar(100) NOT NULL COLLATE utf8_bin
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `online_results`
--

CREATE TABLE `online_results` (
  `num_users` int(11) NOT NULL,
  `seconds` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `probable_username`
--

CREATE TABLE `probable_username` (
  `client_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL COLLATE utf8_bin
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `ranges`
--

CREATE TABLE `ranges` (
  `connected_id` int(11) NOT NULL,
  `disconnected_id` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `streak_results`
--

CREATE TABLE `streak_results` (
  `client_id` int(11) NOT NULL,
  `longest` int(11) NOT NULL,
  `startLongest` date NOT NULL,
  `current` int(11) NOT NULL,
  `startCurrent` date NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `uptime_results`
--

CREATE TABLE `uptime_results` (
  `client_id` int(11) NOT NULL,
  `uptime` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL COLLATE utf8_bin,
  `client_id` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `users_uptime`
--

CREATE TABLE `users_uptime` (
  `user_id` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `user_collapser_results`
--

CREATE TABLE `user_collapser_results` (
  `client_id1` int(11) NOT NULL,
  `client_id2` int(11) NOT NULL
) ;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `channel_events`
--
ALTER TABLE `channel_events`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `date_2` (`date`,`type`,`name`,`user_id`),
ADD KEY `date` (`date`),
ADD KEY `user_id` (`user_id`);

--
-- Indici per le tabelle `client_connected_events`
--
ALTER TABLE `client_connected_events`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `date_2` (`date`,`ip`,`user_id`),
ADD KEY `user_id` (`user_id`),
ADD KEY `date` (`date`);

--
-- Indici per le tabelle `client_disconnected_events`
--
ALTER TABLE `client_disconnected_events`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `date_2` (`date`,`reason`,`user_id`),
ADD KEY `date` (`date`),
ADD KEY `user_id` (`user_id`);

--
-- Indici per le tabelle `daily_results`
--
ALTER TABLE `daily_results`
ADD PRIMARY KEY (`timestamp`);

--
-- Indici per le tabelle `daily_user_result`
--
ALTER TABLE `daily_user_result`
ADD PRIMARY KEY (`client_id`,`date`);

--
-- Indici per le tabelle `file_manager_events`
--
ALTER TABLE `file_manager_events`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `date_2` (`date`,`type`,`user_id`),
ADD KEY `user_id` (`user_id`),
ADD KEY `date` (`date`);

--
-- Indici per le tabelle `misc_results`
--
ALTER TABLE `misc_results`
ADD PRIMARY KEY (`key`);

--
-- Indici per le tabelle `online_results`
--
ALTER TABLE `online_results`
ADD PRIMARY KEY (`num_users`);

--
-- Indici per le tabelle `probable_username`
--
ALTER TABLE `probable_username`
ADD PRIMARY KEY (`client_id`);

--
-- Indici per le tabelle `ranges`
--
ALTER TABLE `ranges`
ADD PRIMARY KEY (`connected_id`,`disconnected_id`);

--
-- Indici per le tabelle `streak_results`
--
ALTER TABLE `streak_results`
ADD PRIMARY KEY (`client_id`);

--
-- Indici per le tabelle `uptime_results`
--
ALTER TABLE `uptime_results`
ADD PRIMARY KEY (`client_id`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `username_2` (`username`,`client_id`),
ADD KEY `client_id` (`client_id`),
ADD KEY `username` (`username`);

--
-- Indici per le tabelle `users_uptime`
--
ALTER TABLE `users_uptime`
ADD PRIMARY KEY (`user_id`);

--
-- Indici per le tabelle `user_collapser_results`
--
ALTER TABLE `user_collapser_results`
ADD PRIMARY KEY (`client_id1`,`client_id2`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `channel_events`
--
ALTER TABLE `channel_events`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `client_connected_events`
--
ALTER TABLE `client_connected_events`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `client_disconnected_events`
--
ALTER TABLE `client_disconnected_events`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `file_manager_events`
--
ALTER TABLE `file_manager_events`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
