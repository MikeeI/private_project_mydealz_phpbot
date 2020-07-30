@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../jeremykendall/php-domain-parser/bin/pdp-psl
php "%BIN_TARGET%" %*
