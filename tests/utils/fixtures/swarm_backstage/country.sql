DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(2) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `country` VALUES (1,'US','United States');
INSERT INTO `country` VALUES (2,'CA','Canada');
INSERT INTO `country` VALUES (3,'AF','Afghanistan');
INSERT INTO `country` VALUES (4,'AL','Albania');
INSERT INTO `country` VALUES (5,'DZ','Algeria');
INSERT INTO `country` VALUES (6,'DS','American Samoa');
INSERT INTO `country` VALUES (7,'AD','Andorra');
INSERT INTO `country` VALUES (8,'AO','Angola');
INSERT INTO `country` VALUES (9,'AI','Anguilla');
INSERT INTO `country` VALUES (10,'AQ','Antarctica');
INSERT INTO `country` VALUES (11,'AG','Antigua and/or Barbuda');
INSERT INTO `country` VALUES (12,'AR','Argentina');
INSERT INTO `country` VALUES (13,'AM','Armenia');
INSERT INTO `country` VALUES (14,'AW','Aruba');
INSERT INTO `country` VALUES (15,'AU','Australia');
INSERT INTO `country` VALUES (16,'AT','Austria');
INSERT INTO `country` VALUES (17,'AZ','Azerbaijan');
INSERT INTO `country` VALUES (18,'BS','Bahamas');
INSERT INTO `country` VALUES (19,'BH','Bahrain');
INSERT INTO `country` VALUES (20,'BD','Bangladesh');
INSERT INTO `country` VALUES (21,'BB','Barbados');
INSERT INTO `country` VALUES (22,'BY','Belarus');
INSERT INTO `country` VALUES (23,'BE','Belgium');
INSERT INTO `country` VALUES (24,'BZ','Belize');
INSERT INTO `country` VALUES (25,'BJ','Benin');
INSERT INTO `country` VALUES (26,'BM','Bermuda');
INSERT INTO `country` VALUES (27,'BT','Bhutan');
INSERT INTO `country` VALUES (28,'BO','Bolivia');
INSERT INTO `country` VALUES (29,'BA','Bosnia and Herzegovina');
INSERT INTO `country` VALUES (30,'BW','Botswana');
INSERT INTO `country` VALUES (31,'BV','Bouvet Island');
INSERT INTO `country` VALUES (32,'BR','Brazil');
INSERT INTO `country` VALUES (33,'IO','British lndian Ocean Territory');
INSERT INTO `country` VALUES (34,'BN','Brunei Darussalam');
INSERT INTO `country` VALUES (35,'BG','Bulgaria');
INSERT INTO `country` VALUES (36,'BF','Burkina Faso');
INSERT INTO `country` VALUES (37,'BI','Burundi');
INSERT INTO `country` VALUES (38,'KH','Cambodia');
INSERT INTO `country` VALUES (39,'CM','Cameroon');
INSERT INTO `country` VALUES (40,'CV','Cape Verde');
INSERT INTO `country` VALUES (41,'KY','Cayman Islands');
INSERT INTO `country` VALUES (42,'CF','Central African Republic');
INSERT INTO `country` VALUES (43,'TD','Chad');
INSERT INTO `country` VALUES (44,'CL','Chile');
INSERT INTO `country` VALUES (45,'CN','China');
INSERT INTO `country` VALUES (46,'CX','Christmas Island');
INSERT INTO `country` VALUES (47,'CC','Cocos (Keeling) Islands');
INSERT INTO `country` VALUES (48,'CO','Colombia');
INSERT INTO `country` VALUES (49,'KM','Comoros');
INSERT INTO `country` VALUES (50,'CG','Congo');
INSERT INTO `country` VALUES (51,'CK','Cook Islands');
INSERT INTO `country` VALUES (52,'CR','Costa Rica');
INSERT INTO `country` VALUES (53,'HR','Croatia (Hrvatska)');
INSERT INTO `country` VALUES (54,'CU','Cuba');
INSERT INTO `country` VALUES (55,'CY','Cyprus');
INSERT INTO `country` VALUES (56,'CZ','Czech Republic');
INSERT INTO `country` VALUES (57,'DK','Denmark');
INSERT INTO `country` VALUES (58,'DJ','Djibouti');
INSERT INTO `country` VALUES (59,'DM','Dominica');
INSERT INTO `country` VALUES (60,'DO','Dominican Republic');
INSERT INTO `country` VALUES (61,'TP','East Timor');
INSERT INTO `country` VALUES (62,'EC','Ecuador');
INSERT INTO `country` VALUES (63,'EG','Egypt');
INSERT INTO `country` VALUES (64,'SV','El Salvador');
INSERT INTO `country` VALUES (65,'GQ','Equatorial Guinea');
INSERT INTO `country` VALUES (66,'ER','Eritrea');
INSERT INTO `country` VALUES (67,'EE','Estonia');
INSERT INTO `country` VALUES (68,'ET','Ethiopia');
INSERT INTO `country` VALUES (69,'FK','Falkland Islands (Malvinas)');
INSERT INTO `country` VALUES (70,'FO','Faroe Islands');
INSERT INTO `country` VALUES (71,'FJ','Fiji');
INSERT INTO `country` VALUES (72,'FI','Finland');
INSERT INTO `country` VALUES (73,'FR','France');
INSERT INTO `country` VALUES (74,'FX','France, Metropolitan');
INSERT INTO `country` VALUES (75,'GF','French Guiana');
INSERT INTO `country` VALUES (76,'PF','French Polynesia');
INSERT INTO `country` VALUES (77,'TF','French Southern Territories');
INSERT INTO `country` VALUES (78,'GA','Gabon');
INSERT INTO `country` VALUES (79,'GM','Gambia');
INSERT INTO `country` VALUES (80,'GE','Georgia');
INSERT INTO `country` VALUES (81,'DE','Germany');
INSERT INTO `country` VALUES (82,'GH','Ghana');
INSERT INTO `country` VALUES (83,'GI','Gibraltar');
INSERT INTO `country` VALUES (84,'GR','Greece');
INSERT INTO `country` VALUES (85,'GL','Greenland');
INSERT INTO `country` VALUES (86,'GD','Grenada');
INSERT INTO `country` VALUES (87,'GP','Guadeloupe');
INSERT INTO `country` VALUES (88,'GU','Guam');
INSERT INTO `country` VALUES (89,'GT','Guatemala');
INSERT INTO `country` VALUES (90,'GN','Guinea');
INSERT INTO `country` VALUES (91,'GW','Guinea-Bissau');
INSERT INTO `country` VALUES (92,'GY','Guyana');
INSERT INTO `country` VALUES (93,'HT','Haiti');
INSERT INTO `country` VALUES (94,'HM','Heard and Mc Donald Islands');
INSERT INTO `country` VALUES (95,'HN','Honduras');
INSERT INTO `country` VALUES (96,'HK','Hong Kong');
INSERT INTO `country` VALUES (97,'HU','Hungary');
INSERT INTO `country` VALUES (98,'IS','Iceland');
INSERT INTO `country` VALUES (99,'IN','India');
INSERT INTO `country` VALUES (100,'ID','Indonesia');
INSERT INTO `country` VALUES (101,'IR','Iran (Islamic Republic of)');
INSERT INTO `country` VALUES (102,'IQ','Iraq');
INSERT INTO `country` VALUES (103,'IE','Ireland');
INSERT INTO `country` VALUES (104,'IL','Israel');
INSERT INTO `country` VALUES (105,'IT','Italy');
INSERT INTO `country` VALUES (106,'CI','Ivory Coast');
INSERT INTO `country` VALUES (107,'JM','Jamaica');
INSERT INTO `country` VALUES (108,'JP','Japan');
INSERT INTO `country` VALUES (109,'JO','Jordan');
INSERT INTO `country` VALUES (110,'KZ','Kazakhstan');
INSERT INTO `country` VALUES (111,'KE','Kenya');
INSERT INTO `country` VALUES (112,'KI','Kiribati');
INSERT INTO `country` VALUES (113,'KP','Korea, Democratic People\'s Republic of');
INSERT INTO `country` VALUES (114,'KR','Korea, Republic of');
INSERT INTO `country` VALUES (115,'XK','Kosovo');
INSERT INTO `country` VALUES (116,'KW','Kuwait');
INSERT INTO `country` VALUES (117,'KG','Kyrgyzstan');
INSERT INTO `country` VALUES (118,'LA','Lao People\'s Democratic Republic');
INSERT INTO `country` VALUES (119,'LV','Latvia');
INSERT INTO `country` VALUES (120,'LB','Lebanon');
INSERT INTO `country` VALUES (121,'LS','Lesotho');
INSERT INTO `country` VALUES (122,'LR','Liberia');
INSERT INTO `country` VALUES (123,'LY','Libyan Arab Jamahiriya');
INSERT INTO `country` VALUES (124,'LI','Liechtenstein');
INSERT INTO `country` VALUES (125,'LT','Lithuania');
INSERT INTO `country` VALUES (126,'LU','Luxembourg');
INSERT INTO `country` VALUES (127,'MO','Macau');
INSERT INTO `country` VALUES (128,'MK','Macedonia');
INSERT INTO `country` VALUES (129,'MG','Madagascar');
INSERT INTO `country` VALUES (130,'MW','Malawi');
INSERT INTO `country` VALUES (131,'MY','Malaysia');
INSERT INTO `country` VALUES (132,'MV','Maldives');
INSERT INTO `country` VALUES (133,'ML','Mali');
INSERT INTO `country` VALUES (134,'MT','Malta');
INSERT INTO `country` VALUES (135,'MH','Marshall Islands');
INSERT INTO `country` VALUES (136,'MQ','Martinique');
INSERT INTO `country` VALUES (137,'MR','Mauritania');
INSERT INTO `country` VALUES (138,'MU','Mauritius');
INSERT INTO `country` VALUES (139,'TY','Mayotte');
INSERT INTO `country` VALUES (140,'MX','Mexico');
INSERT INTO `country` VALUES (141,'FM','Micronesia, Federated States of');
INSERT INTO `country` VALUES (142,'MD','Moldova, Republic of');
INSERT INTO `country` VALUES (143,'MC','Monaco');
INSERT INTO `country` VALUES (144,'MN','Mongolia');
INSERT INTO `country` VALUES (145,'ME','Montenegro');
INSERT INTO `country` VALUES (146,'MS','Montserrat');
INSERT INTO `country` VALUES (147,'MA','Morocco');
INSERT INTO `country` VALUES (148,'MZ','Mozambique');
INSERT INTO `country` VALUES (149,'MM','Myanmar');
INSERT INTO `country` VALUES (150,'NA','Namibia');
INSERT INTO `country` VALUES (151,'NR','Nauru');
INSERT INTO `country` VALUES (152,'NP','Nepal');
INSERT INTO `country` VALUES (153,'NL','Netherlands');
INSERT INTO `country` VALUES (154,'AN','Netherlands Antilles');
INSERT INTO `country` VALUES (155,'NC','New Caledonia');
INSERT INTO `country` VALUES (156,'NZ','New Zealand');
INSERT INTO `country` VALUES (157,'NI','Nicaragua');
INSERT INTO `country` VALUES (158,'NE','Niger');
INSERT INTO `country` VALUES (159,'NG','Nigeria');
INSERT INTO `country` VALUES (160,'NU','Niue');
INSERT INTO `country` VALUES (161,'NF','Norfork Island');
INSERT INTO `country` VALUES (162,'MP','Northern Mariana Islands');
INSERT INTO `country` VALUES (163,'NO','Norway');
INSERT INTO `country` VALUES (164,'OM','Oman');
INSERT INTO `country` VALUES (165,'PK','Pakistan');
INSERT INTO `country` VALUES (166,'PW','Palau');
INSERT INTO `country` VALUES (167,'PA','Panama');
INSERT INTO `country` VALUES (168,'PG','Papua New Guinea');
INSERT INTO `country` VALUES (169,'PY','Paraguay');
INSERT INTO `country` VALUES (170,'PE','Peru');
INSERT INTO `country` VALUES (171,'PH','Philippines');
INSERT INTO `country` VALUES (172,'PN','Pitcairn');
INSERT INTO `country` VALUES (173,'PL','Poland');
INSERT INTO `country` VALUES (174,'PT','Portugal');
INSERT INTO `country` VALUES (175,'PR','Puerto Rico');
INSERT INTO `country` VALUES (176,'QA','Qatar');
INSERT INTO `country` VALUES (177,'RE','Reunion');
INSERT INTO `country` VALUES (178,'RO','Romania');
INSERT INTO `country` VALUES (179,'RU','Russian Federation');
INSERT INTO `country` VALUES (180,'RW','Rwanda');
INSERT INTO `country` VALUES (181,'KN','Saint Kitts and Nevis');
INSERT INTO `country` VALUES (182,'LC','Saint Lucia');
INSERT INTO `country` VALUES (183,'VC','Saint Vincent and the Grenadines');
INSERT INTO `country` VALUES (184,'WS','Samoa');
INSERT INTO `country` VALUES (185,'SM','San Marino');
INSERT INTO `country` VALUES (186,'ST','Sao Tome and Principe');
INSERT INTO `country` VALUES (187,'SA','Saudi Arabia');
INSERT INTO `country` VALUES (188,'SN','Senegal');
INSERT INTO `country` VALUES (189,'RS','Serbia');
INSERT INTO `country` VALUES (190,'SC','Seychelles');
INSERT INTO `country` VALUES (191,'SL','Sierra Leone');
INSERT INTO `country` VALUES (192,'SG','Singapore');
INSERT INTO `country` VALUES (193,'SK','Slovakia');
INSERT INTO `country` VALUES (194,'SI','Slovenia');
INSERT INTO `country` VALUES (195,'SB','Solomon Islands');
INSERT INTO `country` VALUES (196,'SO','Somalia');
INSERT INTO `country` VALUES (197,'ZA','South Africa');
INSERT INTO `country` VALUES (198,'GS','South Georgia South Sandwich Islands');
INSERT INTO `country` VALUES (199,'ES','Spain');
INSERT INTO `country` VALUES (200,'LK','Sri Lanka');
INSERT INTO `country` VALUES (201,'SH','St. Helena');
INSERT INTO `country` VALUES (202,'PM','St. Pierre and Miquelon');
INSERT INTO `country` VALUES (203,'SD','Sudan');
INSERT INTO `country` VALUES (204,'SR','Suriname');
INSERT INTO `country` VALUES (205,'SJ','Svalbarn and Jan Mayen Islands');
INSERT INTO `country` VALUES (206,'SZ','Swaziland');
INSERT INTO `country` VALUES (207,'SE','Sweden');
INSERT INTO `country` VALUES (208,'CH','Switzerland');
INSERT INTO `country` VALUES (209,'SY','Syrian Arab Republic');
INSERT INTO `country` VALUES (210,'TW','Taiwan');
INSERT INTO `country` VALUES (211,'TJ','Tajikistan');
INSERT INTO `country` VALUES (212,'TZ','Tanzania, United Republic of');
INSERT INTO `country` VALUES (213,'TH','Thailand');
INSERT INTO `country` VALUES (214,'TG','Togo');
INSERT INTO `country` VALUES (215,'TK','Tokelau');
INSERT INTO `country` VALUES (216,'TO','Tonga');
INSERT INTO `country` VALUES (217,'TT','Trinidad and Tobago');
INSERT INTO `country` VALUES (218,'TN','Tunisia');
INSERT INTO `country` VALUES (219,'TR','Turkey');
INSERT INTO `country` VALUES (220,'TM','Turkmenistan');
INSERT INTO `country` VALUES (221,'TC','Turks and Caicos Islands');
INSERT INTO `country` VALUES (222,'TV','Tuvalu');
INSERT INTO `country` VALUES (223,'UG','Uganda');
INSERT INTO `country` VALUES (224,'UA','Ukraine');
INSERT INTO `country` VALUES (225,'AE','United Arab Emirates');
INSERT INTO `country` VALUES (226,'GB','United Kingdom');
INSERT INTO `country` VALUES (227,'UM','United States minor outlying islands');
INSERT INTO `country` VALUES (228,'UY','Uruguay');
INSERT INTO `country` VALUES (229,'UZ','Uzbekistan');
INSERT INTO `country` VALUES (230,'VU','Vanuatu');
INSERT INTO `country` VALUES (231,'VA','Vatican City State');
INSERT INTO `country` VALUES (232,'VE','Venezuela');
INSERT INTO `country` VALUES (233,'VN','Vietnam');
INSERT INTO `country` VALUES (234,'VG','Virigan Islands (British)');
INSERT INTO `country` VALUES (235,'VI','Virgin Islands (U.S.)');
INSERT INTO `country` VALUES (236,'WF','Wallis and Futuna Islands');
INSERT INTO `country` VALUES (237,'EH','Western Sahara');
INSERT INTO `country` VALUES (238,'YE','Yemen');
INSERT INTO `country` VALUES (239,'YU','Yugoslavia');
INSERT INTO `country` VALUES (240,'ZR','Zaire');
INSERT INTO `country` VALUES (241,'ZM','Zambia');
INSERT INTO `country` VALUES (242,'ZW','Zimbabwe');
