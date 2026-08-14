# All In One WP Migration Fork 
*(from Version 6.77)*

| | |
|---|---|
| Plugin base | All-in-One WP Migration 6.77 |
| Max import size | 32 GB |
| Restores backups from | All-in-One WP Migration 6.x and 7.x (verified against 7.109) |
| Verified on | WordPress 7.0.4, PHP 8.3 |

## Notice
This repository originates from [Servmask](https://servmask.com/)'s [All-In-One-WP-Migration Version 6.77](https://downloads.wordpress.org/plugin/all-in-one-wp-migration.6.77.zip), which has the GPLv2 license. I do not claim to be the original author, and I do not claim to have ever had any involvement with Servmask. The modifications that I have made are clearly stated below, and include only one minor change to `constants.php`.

> **This copy carries an additional local change** on top of the upstream fork: the archive reader has been taught the All-in-One WP Migration 7.x `.wpress` format, so backups exported by current versions of the plugin can be restored. See [Restoring 7.x backups](#restoring-7x-backups) below.


### Why?
This repository is a fork of the last version of the [All In One WP Migration](https://wordpress.org/plugins/all-in-one-wp-migration/) plugin that easily allows modification of the import file size, and it includes those modifications. By modifying this freely available older version, users can empower themselves to migrate larger sites than they otherwise would be able to. Use at your own risk, and delete the plugin post migration as this older version contains [unpatched security vulnerabilities](https://www.wordfence.com/threat-intel/vulnerabilities/wordpress-plugins/all-in-one-wp-migration). 


### How?
The file upload size limit has been modified to be `32GB`. To change this you may define the limit in byes on line 284 in `constants.php` (if 32 Gigs doesn't float your boat). 

```php
// =================
// = Max File Size =
// =================
define( 'AI1WM_MAX_FILE_SIZE', 34359738368 );
```

### Restoring 7.x backups
Stock 6.77 cannot read a `.wpress` file written by All-in-One WP Migration 7.x. It fails part way through the import with:

> Please make sure that your file was exported using **All-in-One WP Migration** plugin.

The archive is fine — the header layout changed. Every entry in a `.wpress` file is preceded by a 4377 byte header block, and 7.x reserves the **last 8 bytes of that block for an integrity value**, which leaves 4088 bytes for the path instead of 4096:

```
Offset  Length  Contents
     0     255  filename
   255      14  size of file contents
   269      12  last modification time
   281    4088  path                      <-- 4096 in 6.x
  4369       8  integrity value           <-- 7.x only
```

Reading the path as 4096 bytes swallows that value. Because it sits *after* the NUL padding, `trim()` cannot strip it and every path is decoded as `".\0\0…\0c891fc12"`. `package.json` then never matches, nothing is extracted, and the importer reaches the end of the archive empty handed — hence the message above.

7.x also stores the archive size and an integrity value in the terminating block, so 6.77's `is_valid()` rejected it for not being 4377 NUL bytes.

Both are fixed in `lib/vendor/servmask/archiver/`:

* **`class-ai1wm-archiver.php`** — added `$read_block_format` (`a255` / `a14` / `a12` / `a4088` / `a8`) used when reading, and `is_eof_block()`, which detects a terminator by its empty filename field rather than by requiring an all-NUL block. `is_valid()` uses it.
* **`class-ai1wm-extractor.php`** — `get_data_from_block()` uses the read layout, and the four end-of-archive comparisons call `is_eof_block()`.

The write path (`$block_format`) is deliberately untouched, so backups *created* by this plugin are still byte-identical 6.x archives. Splitting the trailing 8 bytes off is safe when reading a 6.x archive too: its path field is NUL padded and a relative path can never reach 4088 bytes.

Verified end to end against a 5.3 GB backup exported by **All-in-One WP Migration 7.109** from **WordPress 7.0.4** (40,044 entries), restored onto PHP 8.3. No edits to the `.wpress` file are required — if you previously zeroed the terminator block by hand to get past `is_valid()`, you can put those bytes back.

### More
If you'd like to review the changes that Servmask has made to this plugin, please refer to the SVN Repository to browse the revision history yourself [here](https://plugins.trac.wordpress.org/log/all-in-one-wp-migration).
