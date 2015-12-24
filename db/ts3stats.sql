-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Creato il: Dic 24, 2015 alle 13:54
-- Versione del server: 10.1.9-MariaDB-log
-- Versione PHP: 5.6.16

SET time_zone = "+00:00";

--
-- Database: `ts3stats`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `channel_events`
--

CREATE TABLE `channel_events` (
  `id` int(11) NOT NULL,
  `date` timestamp NOT NULL,
  `type` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `client_connected_events`
--

CREATE TABLE `client_connected_events` (
  `id` int(11) NOT NULL,
  `date` timestamp NOT NULL,
  `ip` varchar(15) NOT NULL,
  `user_id` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `client_disconnected_events`
--

CREATE TABLE `client_disconnected_events` (
  `id` int(11) NOT NULL,
  `date` timestamp NOT NULL,
  `reason` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `daily_results`
--

CREATE TABLE `daily_results` (
  `cell_id` int(11) NOT NULL,
  `average` float NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `file_manager_events`
--

CREATE TABLE `file_manager_events` (
  `id` int(11) NOT NULL,
  `date` timestamp NULL,
  `type` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Struttura della tabella `misc_results`
--

CREATE TABLE `misc_results` (
  `key` varchar(50) NOT NULL,
  `value` varchar(100) NOT NULL
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
  `username` varchar(50) NOT NULL
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
  `username` varchar(100) NOT NULL,
  `client_id` int(11) NOT NULL
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
ADD KEY `date` (`date`),
ADD KEY `user_id` (`user_id`);

--
-- Indici per le tabelle `client_connected_events`
--
ALTER TABLE `client_connected_events`
ADD PRIMARY KEY (`id`),
ADD KEY `user_id` (`user_id`),
ADD KEY `date` (`date`);

--
-- Indici per le tabelle `client_disconnected_events`
--
ALTER TABLE `client_disconnected_events`
ADD PRIMARY KEY (`id`),
ADD KEY `date` (`date`),
ADD KEY `user_id` (`user_id`);

--
-- Indici per le tabelle `daily_results`
--
ALTER TABLE `daily_results`
ADD PRIMARY KEY (`cell_id`);

--
-- Indici per le tabelle `file_manager_events`
--
ALTER TABLE `file_manager_events`
ADD PRIMARY KEY (`id`),
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
