-- phpMyAdmin SQL Dump
-- version 3.1.2deb1ubuntu0.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Sam 26 Septembre 2009 à 14:14
-- Version du serveur: 5.0.75
-- Version de PHP: 5.2.6-3ubuntu4.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `oxycast`
--

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `id` bigint(20) NOT NULL auto_increment,
  `login` varchar(20) NOT NULL,
  `password` varchar(128) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `adresse` varchar(200) NOT NULL,
  `cp` varchar(5) NOT NULL,
  `ville` varchar(30) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `dateInscription` datetime NOT NULL,
  `dateUpdate` datetime NOT NULL,
  `admin` binary(1) NOT NULL default '0',
  `cle_conf` varchar(255) NOT NULL,
  `recover` varchar(255) NOT NULL,
  `blog` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Structure de la table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `offers`
--

CREATE TABLE IF NOT EXISTS `offers` (
  `id` bigint(20) NOT NULL auto_increment,
  `bitrate` smallint(6) NOT NULL,
  `slots` smallint(6) NOT NULL,
  `duree` tinyint(4) NOT NULL,
  `prixEuros` decimal(10,2) NOT NULL,
  `hidden` binary(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `partenaires`
--

CREATE TABLE IF NOT EXISTS `partenaires` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `logo` varchar(128) NOT NULL,
  `hide` binary(1) NOT NULL default '0',
  `type` enum('CLUB','DJ','AUTRE','RADIO') NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `pass_temp`
--

CREATE TABLE IF NOT EXISTS `pass_temp` (
  `id` int(11) NOT NULL auto_increment,
  `id_client` int(11) NOT NULL,
  `md5` varchar(32) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `streams`
--

CREATE TABLE IF NOT EXISTS `streams` (
  `id` bigint(20) NOT NULL auto_increment,
  `offerId` smallint(6) NOT NULL,
  `clientId` bigint(20) NOT NULL,
  `status` enum('programme','en cours','termine','suspendu','renew','change_password','commande') NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `genre` varchar(255) NOT NULL,
  `mountpoint` varchar(30) NOT NULL,
  `url` varchar(50) NOT NULL,
  `password` varchar(128) NOT NULL,
  `port` smallint(6) NOT NULL,
  `dateDebut` datetime NOT NULL,
  `dateFin` datetime NOT NULL,
  `ip_serveur` varchar(50) NOT NULL,
  `format_live` varchar(3) NOT NULL,
  `format_output` varchar(3) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE IF NOT EXISTS `commandes` (
  `transacId` int(11) NOT NULL,
  `clientId` int(11) NOT NULL,
  `payment_type` ENUM('paypal', 'cheque', 'none') NOT NULL,
  `status` enum('waiting','done','cancelled') NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `offerId` tinyint(4) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`transacId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
