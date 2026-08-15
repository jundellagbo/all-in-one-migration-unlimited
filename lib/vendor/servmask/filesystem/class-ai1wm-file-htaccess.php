<?php
/**
 * Copyright (C) 2014-2018 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

class Ai1wm_File_Htaccess {

	/**
	 * Create .htaccess file (ServMask)
	 *
	 * @param  string  $path Path to file
	 * @return boolean
	 */
	public static function create( $path ) {
		// The previous version of this file only set a MIME type and turned off directory listings,
		// which left every backup downloadable by anyone who guessed its name - and a backup is the
		// entire database, password hashes included. Deny web access outright; the plugin reads
		// these files from disk and never needs them served over HTTP.
		return Ai1wm_File::create( $path, implode( PHP_EOL, array(
			'<IfModule mod_authz_core.c>',
			'Require all denied',
			'</IfModule>',
			'<IfModule !mod_authz_core.c>',
			'Order deny,allow',
			'Deny from all',
			'</IfModule>',
			'<IfModule mod_mime.c>',
			'AddType application/octet-stream .wpress',
			'</IfModule>',
			'<IfModule mod_dir.c>',
			'DirectoryIndex index.php',
			'</IfModule>',
			'<IfModule mod_autoindex.c>',
			'Options -Indexes',
			'</IfModule>',
			'<IfModule mod_php.c>',
			'php_flag engine off',
			'</IfModule>',
			'<IfModule mod_php7.c>',
			'php_flag engine off',
			'</IfModule>',
			'<IfModule mod_php8.c>',
			'php_flag engine off',
			'</IfModule>',
		) ) );
	}

	/**
	 * Create .htaccess file (LiteSpeed)
	 *
	 * @param  string  $path Path to file
	 * @return boolean
	 */
	public static function litespeed( $path ) {
		return Ai1wm_File::create_with_markers( $path, 'LiteSpeed', array(
			'<IfModule Litespeed>',
			'SetEnv noabort 1',
			'</IfModule>',
		) );
	}
}
