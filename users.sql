-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(15) COLLATE utf8_unicode_ci NOT NULL,
  `gender` char(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '男',
  `createtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

