<?php
/**
 * list-dir.php
 *
 * Simple PHP4-compatible script to list files and folders in a directory
 * provided via GET param `dir`.
 *
 * Usage: list-dir.php?dir=path/to/dir
 * Notes:
 *  - This is a minimal demo for local/dev usage only (do not expose to public with
 *    sensitive paths or without proper access controls).
 *  - Compatible with PHP 4: avoids PHP5-only functions and uses $HTTP_GET_VARS fallback.
 */

// Read GET parameter in a PHP4-compatible way
$dirParam = '';
if (isset($_GET) && array_key_exists('dir', $_GET)) {
	$dirParam = $_GET['dir'];
} elseif (isset($HTTP_GET_VARS) && array_key_exists('dir', $HTTP_GET_VARS)) {
	$dirParam = $HTTP_GET_VARS['dir'];
}

$dirParam = trim((string)$dirParam);

// Default to current directory if nothing provided
if ($dirParam == '') {
	$dirParam = '.';
}

// Normalize slashes (helpful on Windows)
$dirParam = str_replace('\\', '/', $dirParam);

// Simple safety: resolve path and ensure the dir exists and is a directory
$real = @realpath($dirParam);
if ($real === false || $real == '') {
	echo "<h2>Directory not found: " . htmlspecialchars($dirParam) . "</h2>\n";
	exit;
}

if (!is_dir($real)) {
	echo "<h2>Not a directory: " . htmlspecialchars($dirParam) . "</h2>\n";
	exit;
}

// Read directory contents using PHP4-safe functions
$dh = @opendir($real);
if ($dh === false) {
	echo "<h2>Unable to open directory: " . htmlspecialchars($dirParam) . "</h2>\n";
	exit;
}

// Collect entries, skip '.' and optionally hidden files
$entries = array();
while (false !== ($entry = readdir($dh))) {
	if ($entry === '.' || $entry === '..') {
		continue;
	}
	$entries[] = $entry;
}
closedir($dh);

// Separate folders and files for nicer display
$folders = array();
$files = array();
for ($i = 0; $i < sizeof($entries); $i++) {
	$e = $entries[$i];
	$path = $real . DIRECTORY_SEPARATOR . $e;
	if (@is_dir($path)) {
		$folders[] = $e;
	} else {
		$files[] = $e;
	}
}

// Sort alphabetically (PHP4: use sort)
sort($folders);
sort($files);

// Helper: encode each path segment but keep slashes so browsers can resolve
function url_encode_path($p)
{
	// PHP4: explode/implode exist
	$parts = explode('/', $p);
	for ($j = 0; $j < sizeof($parts); $j++) {
		// preserve empty segments (leading/trailing slashes)
		if ($parts[$j] !== '') {
			$parts[$j] = rawurlencode($parts[$j]);
		}
	}
	return implode('/', $parts);
}

// HTML header
echo "<html><head><meta charset=\"utf-8\" /><title>Directory listing: " . htmlspecialchars($dirParam) . "</title></head><body>\n";
echo "<h1>Listing for: " . htmlspecialchars($dirParam) . "</h1>\n";

// Parent directory link
$parent = dirname($dirParam);
if ($parent == '') { $parent = '.'; }
if ($parent !== $dirParam) {
	echo "<div><a href=\"?dir=" . rawurlencode($parent) . "\">.. (parent)</a></div>\n";
}

// Show folders
echo "<h2>Folders</h2>\n";
if (sizeof($folders) == 0) {
	echo "<div><em>(no folders)</em></div>\n";
} else {
	echo "<ul>\n";
	for ($i = 0; $i < sizeof($folders); $i++) {
		$name = $folders[$i];
		$link = htmlspecialchars($dirParam . '/' . $name);
		echo "<li><a href=\"?dir=" . rawurlencode($dirParam . '/' . $name) . "\">" . htmlspecialchars($name) . "</a></li>\n";
	}
	echo "</ul>\n";
}

// Show files
echo "<h2>Files</h2>\n";
if (sizeof($files) == 0) {
	echo "<div><em>(no files)</em></div>\n";
} else {
	echo "<ul>\n";
	for ($i = 0; $i < sizeof($files); $i++) {
		$name = $files[$i];
		// Build a browser-friendly URL to the file relative to the current script
		$relativePath = $dirParam . '/' . $name;
		$href = url_encode_path($relativePath);
		// If the path looks like a filesystem absolute (starts with / or drive letter), browsers
		// may not have direct access. We still render a link; server config will determine accessibility.
		echo "<li><a href=\"" . htmlspecialchars($href) . "\" target=\"_blank\" rel=\"noopener noreferrer\">" . htmlspecialchars($name) . "</a></li>\n";
	}
	echo "</ul>\n";
}

echo "</body></html>\n";

?>

