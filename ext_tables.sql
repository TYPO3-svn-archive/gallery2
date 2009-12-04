--
-- Tabellenstruktur für Tabelle `g2_AccessMap`
--

CREATE TABLE `g2_AccessMap` (
  `g_accessListId` int(11) default '' NOT NULL,
  `g_userOrGroupId` int(11) default '' NOT NULL,
  `g_permission` int(11) default '' NOT NULL,
  PRIMARY KEY  (`g_accessListId`,`g_userOrGroupId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_AccessSubscriberMap`
--

CREATE TABLE `g2_AccessSubscriberMap` (
  `g_itemId` int(11) default '' NOT NULL,
  `g_accessListId` int(11) default '' NOT NULL,
  PRIMARY KEY  (`g_itemId`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_AlbumItem`
--

CREATE TABLE `g2_AlbumItem` (
  `g_id` int(11) default '' NOT NULL,
  `g_theme` varchar(32) default '' NOT NULL,
  `g_orderBy` varchar(128) default '' NOT NULL,
  `g_orderDirection` varchar(32) default '' NOT NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_AnimationItem`
--

CREATE TABLE `g2_AnimationItem` (
  `g_id` int(11) default '' NOT NULL,
  `g_width` int(11) default '' NOT NULL,
  `g_height` int(11) default '' NOT NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_CacheMap`
--

CREATE TABLE `g2_CacheMap` (
  `g_key` varchar(32) default '' NOT NULL,
  `g_value` longtext,
  `g_userId` int(11) default '' NOT NULL,
  `g_itemId` int(11) default '' NOT NULL,
  `g_type` varchar(32) default '' NOT NULL,
  `g_timestamp` int(11) default '' NOT NULL,
  `g_isEmpty` int(1) default '' NOT NULL,
  PRIMARY KEY  (`g_key`,`g_userId`,`g_itemId`,`g_type`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_ChildEntity`
--

CREATE TABLE `g2_ChildEntity` (
  `g_id` int(11) default '' NOT NULL,
  `g_parentId` int(11) default '' NOT NULL,
  PRIMARY KEY  (`g_id`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_DataItem`
--

CREATE TABLE `g2_DataItem` (
  `g_id` int(11) default '' NOT NULL,
  `g_mimeType` varchar(128) default '' NOT NULL,
  `g_size` int(11) default '' NOT NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_Derivative`
--

CREATE TABLE `g2_Derivative` (
  `g_id` int(11) default '' NOT NULL,
  `g_derivativeSourceId` int(11) default '' NOT NULL,
  `g_derivativeOperations` varchar(255) default '' NOT NULL,
  `g_derivativeOrder` int(11) default '' NOT NULL,
  `g_derivativeSize` int(11) default '' NOT NULL,
  `g_derivativeType` int(11) default '' NOT NULL,
  `g_mimeType` varchar(128) default '' NOT NULL,
  `g_postFilterOperations` varchar(255) default '' NOT NULL,
  `g_isBroken` int(1) default '' NOT NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_DerivativeImage`
--

CREATE TABLE `g2_DerivativeImage` (
  `g_id` int(11) default '' NOT NULL,
  `g_width` int(11) default '' NOT NULL,
  `g_height` int(11) default '' NOT NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_DerivativePrefsMap`
--

CREATE TABLE `g2_DerivativePrefsMap` (
  `g_itemId` int(11) default '' NOT NULL,
  `g_order` int(11) default '' NOT NULL,
  `g_derivativeType` int(11) default '' NOT NULL,
  `g_derivativeOperations` varchar(255) default '' NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_DescendentCountsMap`
--

CREATE TABLE `g2_DescendentCountsMap` (
  `g_userId` int(11) default '' NOT NULL,
  `g_itemId` int(11) default '' NOT NULL,
  `g_descendentCount` int(11) default '' NOT NULL,
  PRIMARY KEY  (`g_userId`,`g_itemId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_Entity`
--

CREATE TABLE `g2_Entity` (
  `g_id` int(11) default '' NOT NULL,
  `g_creationTimestamp` int(11) default '' NOT NULL,
  `g_isLinkable` int(1) default '' NOT NULL,
  `g_linkId` int(11) default '' NOT NULL,
  `g_modificationTimestamp` int(11) default '' NOT NULL,
  `g_serialNumber` int(11) default '' NOT NULL,
  `g_entityType` varchar(32) default '' NOT NULL,
  `g_onLoadHandlers` varchar(128) default '' NOT NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_EventLogMap`
--

CREATE TABLE `g2_EventLogMap` (
  `g_id` int(11) default '' NOT NULL,
  `g_userId` int(11) default '' NOT NULL,
  `g_type` varchar(32) default '' NOT NULL,
  `g_summary` varchar(255) default '' NOT NULL,
  `g_details` text,
  `g_location` varchar(255) default '' NOT NULL,
  `g_client` varchar(128) default '' NOT NULL,
  `g_timestamp` int(11) default '' NOT NULL,
  `g_referer` varchar(128) default '' NOT NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_ExternalIdMap`
--

CREATE TABLE `g2_ExternalIdMap` (
  `g_externalId` varchar(128) default '' NOT NULL,
  `g_entityType` varchar(32) default '' NOT NULL,
  `g_entityId` int(11) default '' NOT NULL,
  PRIMARY KEY  (`g_externalId`,`g_entityType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_FactoryMap`
--

CREATE TABLE `g2_FactoryMap` (
  `g_classType` varchar(128) default '' NOT NULL,
  `g_className` varchar(128) default '' NOT NULL,
  `g_implId` varchar(128) default '' NOT NULL,
  `g_implPath` varchar(128) default '' NOT NULL,
  `g_implModuleId` varchar(128) default '' NOT NULL,
  `g_hints` varchar(255) default '' NOT NULL,
  `g_orderWeight` varchar(255) default '' NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_FailedLoginsMap`
--

CREATE TABLE `g2_FailedLoginsMap` (
  `g_userName` varchar(32) default '' NOT NULL,
  `g_count` int(11) default '' NOT NULL,
  `g_lastAttempt` int(11) default '' NOT NULL,
  PRIMARY KEY  (`g_userName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_FileSystemEntity`
--

CREATE TABLE `g2_FileSystemEntity` (
  `g_id` int(11) default '' NOT NULL,
  `g_pathComponent` varchar(128) default '' NOT NULL,
  PRIMARY KEY  (`g_id`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_Group`
--

CREATE TABLE `g2_Group` (
  `g_id` int(11) default '' NOT NULL,
  `g_groupType` int(11) default '' NOT NULL,
  `g_groupName` varchar(128) default '' NOT NULL,
  PRIMARY KEY  (`g_id`),
  UNIQUE KEY `g_groupName` (`g_groupName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_ImageBlockCacheMap`
--

CREATE TABLE `g2_ImageBlockCacheMap` (
  `g_userId` int(11) default '' NOT NULL,
  `g_itemType` int(11) default '' NOT NULL,
  `g_itemTimestamp` int(11) default '' NOT NULL,
  `g_itemId` int(11) default '' NOT NULL,
  `g_random` int(11) default '' NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_ImageBlockDisabledMap`
--

CREATE TABLE `g2_ImageBlockDisabledMap` (
  `g_itemId` int(11) default '' NOT NULL,
  PRIMARY KEY  (`g_itemId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_Item`
--

CREATE TABLE `g2_Item` (
  `g_id` int(11) default '' NOT NULL,
  `g_canContainChildren` int(1) default '' NOT NULL,
  `g_description` text,
  `g_keywords` varchar(255) default '' NOT NULL,
  `g_ownerId` int(11) default '' NOT NULL,
  `g_renderer` varchar(128) default '' NOT NULL,
  `g_summary` varchar(255) default '' NOT NULL,
  `g_title` varchar(128) default '' NOT NULL,
  `g_viewedSinceTimestamp` int(11) default '' NOT NULL,
  `g_originationTimestamp` int(11) default '' NOT NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_ItemAttributesMap`
--

CREATE TABLE `g2_ItemAttributesMap` (
  `g_itemId` int(11) default '' NOT NULL,
  `g_viewCount` int(11) default '' NOT NULL,
  `g_orderWeight` int(11) default '' NOT NULL,
  `g_parentSequence` varchar(255) default '' NOT NULL,
  PRIMARY KEY  (`g_itemId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_Lock`
--

CREATE TABLE `g2_Lock` (
  `g_lockId` int(11) default '' NOT NULL,
  `g_readEntityId` int(11) default '' NOT NULL,
  `g_writeEntityId` int(11) default '' NOT NULL,
  `g_freshUntil` int(11) default '' NOT NULL,
  `g_request` int(11) default '' NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_MaintenanceMap`
--

CREATE TABLE `g2_MaintenanceMap` (
  `g_runId` int(11) default '' NOT NULL,
  `g_taskId` varchar(128) default '' NOT NULL,
  `g_timestamp` int(11) default '' NOT NULL,
  `g_success` int(1) default '' NOT NULL,
  `g_details` text,
  PRIMARY KEY  (`g_runId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_MimeTypeMap`
--

CREATE TABLE `g2_MimeTypeMap` (
  `g_extension` varchar(32) default '' NOT NULL,
  `g_mimeType` varchar(128) default '' NOT NULL,
  `g_viewable` int(1) default '' NOT NULL,
  PRIMARY KEY  (`g_extension`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_MovieItem`
--

CREATE TABLE `g2_MovieItem` (
  `g_id` int(11) default '' NOT NULL,
  `g_width` int(11) default '' NOT NULL,
  `g_height` int(11) default '' NOT NULL,
  `g_duration` int(11) default '' NOT NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_PermissionSetMap`
--

CREATE TABLE `g2_PermissionSetMap` (
  `g_module` varchar(128) default '' NOT NULL,
  `g_permission` varchar(128) default '' NOT NULL,
  `g_description` varchar(255) default '' NOT NULL,
  `g_bits` int(11) default '' NOT NULL,
  `g_flags` int(11) default '' NOT NULL,
  UNIQUE KEY `g_permission` (`g_permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_PhotoItem`
--

CREATE TABLE `g2_PhotoItem` (
  `g_id` int(11) default '' NOT NULL,
  `g_width` int(11) default '' NOT NULL,
  `g_height` int(11) default '' NOT NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_PluginMap`
--

CREATE TABLE `g2_PluginMap` (
  `g_pluginType` varchar(32) default '' NOT NULL,
  `g_pluginId` varchar(32) default '' NOT NULL,
  `g_active` int(1) default '' NOT NULL,
  PRIMARY KEY  (`g_pluginType`,`g_pluginId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_PluginPackageMap`
--

CREATE TABLE `g2_PluginPackageMap` (
  `g_pluginType` varchar(32) default '' NOT NULL,
  `g_pluginId` varchar(32) default '' NOT NULL,
  `g_packageName` varchar(32) default '' NOT NULL,
  `g_packageVersion` varchar(32) default '' NOT NULL,
  `g_packageBuild` varchar(32) default '' NOT NULL,
  `g_locked` int(1) default '' NOT NULL,
  KEY `g2_PluginPackageMap_80596` (`g_pluginType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_PluginParameterMap`
--

CREATE TABLE `g2_PluginParameterMap` (
  `g_pluginType` varchar(32) default '' NOT NULL,
  `g_pluginId` varchar(32) default '' NOT NULL,
  `g_itemId` int(11) default '' NOT NULL,
  `g_parameterName` varchar(128) default '' NOT NULL,
  `g_parameterValue` text,
  UNIQUE KEY `g_pluginType` (`g_pluginType`,`g_pluginId`,`g_itemId`,`g_parameterName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_RecoverPasswordMap`
--

CREATE TABLE `g2_RecoverPasswordMap` (
  `g_userName` varchar(32) default '' NOT NULL,
  `g_authString` varchar(32) default '' NOT NULL,
  `g_requestExpires` int(11) default '' NOT NULL,
  PRIMARY KEY  (`g_userName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_Schema`
--

CREATE TABLE `g2_Schema` (
  `g_name` varchar(128) default '' NOT NULL,
  `g_major` int(11) default '' NOT NULL,
  `g_minor` int(11) default '' NOT NULL,
  `g_createSql` text,
  `g_pluginId` varchar(32) default '' NOT NULL,
  `g_type` varchar(32) default '' NOT NULL,
  `g_info` text,
  PRIMARY KEY  (`g_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_SequenceEventLog`
--

CREATE TABLE `g2_SequenceEventLog` (
  `id` int(11) default '' NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_SequenceId`
--

CREATE TABLE `g2_SequenceId` (
  `id` int(11) default '' NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_SequenceLock`
--

CREATE TABLE `g2_SequenceLock` (
  `id` int(11) default '' NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_SessionMap`
--

CREATE TABLE `g2_SessionMap` (
  `g_id` varchar(32) default '' NOT NULL,
  `g_userId` int(11) default '' NOT NULL,
  `g_remoteIdentifier` varchar(128) default '' NOT NULL,
  `g_creationTimestamp` int(11) default '' NOT NULL,
  `g_modificationTimestamp` int(11) default '' NOT NULL,
  `g_data` longtext,
  PRIMARY KEY  (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_TkOperatnMap`
--

CREATE TABLE `g2_TkOperatnMap` (
  `g_name` varchar(128) default '' NOT NULL,
  `g_parametersCrc` varchar(32) default '' NOT NULL,
  `g_outputMimeType` varchar(128) default '' NOT NULL,
  `g_description` varchar(255) default '' NOT NULL,
  PRIMARY KEY  (`g_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_TkOperatnMimeTypeMap`
--

CREATE TABLE `g2_TkOperatnMimeTypeMap` (
  `g_operationName` varchar(128) default '' NOT NULL,
  `g_toolkitId` varchar(128) default '' NOT NULL,
  `g_mimeType` varchar(128) default '' NOT NULL,
  `g_priority` int(11) default '' NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_TkOperatnParameterMap`
--

CREATE TABLE `g2_TkOperatnParameterMap` (
  `g_operationName` varchar(128) default '' NOT NULL,
  `g_position` int(11) default '' NOT NULL,
  `g_type` varchar(128) default '' NOT NULL,
  `g_description` varchar(255) default '' NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_TkPropertyMap`
--

CREATE TABLE `g2_TkPropertyMap` (
  `g_name` varchar(128) default '' NOT NULL,
  `g_type` varchar(128) default '' NOT NULL,
  `g_description` varchar(128) default '' NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_TkPropertyMimeTypeMap`
--

CREATE TABLE `g2_TkPropertyMimeTypeMap` (
  `g_propertyName` varchar(128) default '' NOT NULL,
  `g_toolkitId` varchar(128) default '' NOT NULL,
  `g_mimeType` varchar(128) default '' NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_UnknownItem`
--

CREATE TABLE `g2_UnknownItem` (
  `g_id` int(11) default '' NOT NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_User`
--

CREATE TABLE `g2_User` (
  `g_id` int(11) default '' NOT NULL,
  `g_userName` varchar(32) default '' NOT NULL,
  `g_fullName` varchar(128) default '' NOT NULL,
  `g_hashedPassword` varchar(128) default '' NOT NULL,
  `g_email` varchar(255) default '' NOT NULL,
  `g_language` varchar(128) default '' NOT NULL,
  `g_locked` int(1) default '0' NOT NULL,
  PRIMARY KEY  (`g_id`),
  UNIQUE KEY `g_userName` (`g_userName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_UserGroupMap`
--

CREATE TABLE `g2_UserGroupMap` (
  `g_userId` int(11) default '' NOT NULL,
  `g_groupId` int(11) default '' NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `g2_WatermarkImage`
--

CREATE TABLE `g2_WatermarkImage` (
  `g_id` int(11) default '' NOT NULL,
  `g_applyToPreferred` int(1) default '' NOT NULL,
  `g_applyToResizes` int(1) default '' NOT NULL,
  `g_applyToThumbnail` int(1) default '' NOT NULL,
  `g_name` varchar(128) default '' NOT NULL,
  `g_fileName` varchar(128) default '' NOT NULL,
  `g_mimeType` varchar(128) default '' NOT NULL,
  `g_size` int(11) default '' NOT NULL,
  `g_width` int(11) default '' NOT NULL,
  `g_height` int(11) default '' NOT NULL,
  `g_ownerId` int(11) default '' NOT NULL,
  `g_xPercentage` varchar(32) default '' NOT NULL,
  `g_yPercentage` varchar(32) default '' NOT NULL,
  PRIMARY KEY  (`g_id`),
  UNIQUE KEY `g_fileName` (`g_fileName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;