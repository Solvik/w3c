-- phpMyAdmin SQL Dump
-- version 3.1.2deb1ubuntu0.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Ven 02 Octobre 2009 à 15:51
-- Version du serveur: 5.0.75
-- Version de PHP: 5.2.6-3ubuntu4.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Structure de la table `musique`
--

CREATE TABLE IF NOT EXISTS `musique` (
  `id` int(11) NOT NULL auto_increment,
  `titre` varchar(255) character set latin1 collate latin1_general_ci NOT NULL default '',
  `artiste` varchar(255) character set latin1 collate latin1_general_ci NOT NULL default '',
  `filename` varchar(255) character set latin1 collate latin1_general_ci NOT NULL default '',
  `path` varchar(255) character set latin1 collate latin1_general_ci NOT NULL default '',
  `dernier_passage` int(11) default '0',
  `passage` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=62 ;

-- --------------------------------------------------------

--
-- Structure de la table `musique_playlist`
--

CREATE TABLE IF NOT EXISTS `musique_playlist` (
  `id_musique` int(11) NOT NULL,
  `id_playlist` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `planification`
--

CREATE TABLE IF NOT EXISTS `planification` (
  `id` int(11) NOT NULL auto_increment,
  `type` enum('playlist','podcast') NOT NULL,
  `jour` varchar(255) character set latin1 collate latin1_general_ci NOT NULL default '',
  `heure_debut` int(11) NOT NULL,
  `heure_fin` int(11) NOT NULL,
  `minute_debut` int(11) NOT NULL,
  `minute_fin` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Structure de la table `playlist`
--

CREATE TABLE IF NOT EXISTS `playlist` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(255) character set latin1 collate latin1_general_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

