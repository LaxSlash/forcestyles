Version 0.3 ALPHA:
- Added capitalization for the warning about override user style.
- Changed "that option" to "that setting" in that same language string.
- Fixed the folder structure of the repository.
- Newline added to all applicable extension files where one was missing.
- Completely revised the query process for finding effected users
- Rewrote the ACP Logging Code
- Various language fixes
- ACP Log now properly uses plurals
- "Select Anonymous User" setting now works in the ACP Module
- Added a forced style notification
- Fixed a few PHP Notices in the ACP Module
- get_groups() now gets the group_type as well
- Permissions and auth checking is now working
- ext.php file added for the notifications

Version 0.2-PL1 ALPHA:
- Style changes weren't logging for when crieria-less changes were performed. Added a special logging entry, just for that.

Version 0.2 ALPHA:
 - Added a CHANGELOG.md file
 - Fixed description typo in composer.json
 - Fixed a typo for LAXSLASH_FORCESTYLES_WARN_ABOUT_OVERRIDE_STYLES_ON
 - Added authentication for the ACP Module
 - Added a language string for the ACP Module's Permission
 - Changed description to reflect that this is an extension, not software
 - Homepage added for the extension
 - The admin module mode was renamed from settings to force_styles
 - Fixed major versioning in the ACP Module
 - Fixed indentation for phpbb/phpbb under soft-require in the composer.json file.
 - Added ACP Logging for Forced Styles

Version 0.1 ALPHA 1:
 - Initial Release