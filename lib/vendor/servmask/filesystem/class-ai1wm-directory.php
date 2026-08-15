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

class Ai1wm_Directory {

	/**
	 * Create directory (recursively)
	 *
	 * @param  string  $path Path to the directory
	 * @return boolean
	 */
	public static function create( $path ) {
		// 0777 made the backups and storage directories writable by every account on the host, which
		// on shared hosting means any neighbouring site could plant files in them. Honour WordPress'
		// configured mode where present and otherwise fall back to 0755.
		$mode = defined( 'FS_CHMOD_DIR' ) ? FS_CHMOD_DIR : 0755;

		return @mkdir( $path, $mode, true );
	}

	/**
	 * Delete directory (recursively)
	 *
	 * @param  string  $path Path to the directory
	 * @return boolean
	 */
	public static function delete( $path ) {
		// Iterate over directory
		$iterator = new Ai1wm_Recursive_Directory_Iterator( $path );

		// Recursively iterate over directory
		$iterator = new Ai1wm_Recursive_Iterator_Iterator( $iterator, RecursiveIteratorIterator::CHILD_FIRST, RecursiveIteratorIterator::CATCH_GET_CHILD );

		// Remove files and directories
		foreach ( $iterator as $item ) {
			if ( $item->isDir() ) {
				@rmdir( $item->getPathname() );
			} else {
				@unlink( $item->getPathname() );
			}
		}

		return @rmdir( $path );
	}
}
