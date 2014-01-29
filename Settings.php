<?php
/**
 * @file Settings.php
 * @ingroup PhpTags
 * @author Pavel Astakhov <pastakhov@yandex.ru>
 */

// Default settings
PhpTags\Runtime::$permittedTime = 1;

PhpTags\Runtime::$functions = array_merge(
		include __DIR__ . '/functions/strings.php', // String Functions @see http://php.net/manual/en/ref.strings.php
		include __DIR__ . '/functions/array.php', // Array Functions @see http://www.php.net/manual/en/ref.array.php
		include __DIR__ . '/functions/math.php', // Math Functions @see http://www.php.net/manual/en/ref.math.php
		include __DIR__ . '/functions/var.php', // Variable handling Functions @see http://www.php.net/manual/en/ref.var.php
		include __DIR__ . '/functions/pcre.php', // PCRE Functions @see http://www.php.net/manual/en/ref.pcre.php
		include __DIR__ . '/functions/datetime.php', // Date/Time Functions @see http://www.php.net/manual/en/ref.datetime.php
		PhpTags\Runtime::$functions
);

PhpTags\Runtime::$constants = array_merge(
		include __DIR__ . '/constants.php',
		PhpTags\Runtime::$constants
);
