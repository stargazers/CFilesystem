<?php

/* 
CFilesystem - Class for general filesystem operations.
Copyright (C) 2011 Aleksi Räsänen <aleksi.rasanen@runosydan.net>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

	// *************************************************
	//	CFilesystem
	/*!
		@brief Filesystem handling class.

		@author Aleksi Räsänen <aleksi.rasanen@runosydan.net>
	*/
	// *************************************************
	class CFilesystem
	{
		// *************************************************
		//	getAllFilesAndDirectoriesFromPath
		/*!
			@brief Get all files and directories in array 
			  from given path

			@param $path Path where we search

			@return Array of files.
		*/
		// *************************************************
		public function getAllFilesAndDirectoriesFromPath( $path )
		{
			$files = array();
			$handle = opendir( $path );

			if( $handle )
			{
				while( false !== ( $file = readdir( $handle ) ) )
					$files[] = $file;
			}

			return $files;
		}

		// *************************************************
		//	getAllFilesFromPath
		/*!
			@brief Get all normal files from given path

			@param $path Path where we search

			@return Array of files
		*/
		// *************************************************
		public function getAllFilesFromPath( $path )
		{
			$all = $this->getAllFilesAndDirectoriesFromPath( $path );
			$path = $this->addEndingSlash( $path );
			$files = array();

			foreach( $all as $file )
			{
				$file = $path . $file;

				if( is_file( $file ) )
					$files[] = $file;
			}

			return $files;
		}

		// *************************************************
		//	getAllDirectoriesFromPath
		/*!
			@brief Get all directories from given path

			@param $path Path where we search

			@return Array of directory names
		*/
		// *************************************************
		public function getAllDirectoriesFromPath( $path )
		{
			$all = $this->getAllFilesAndDirectoriesFromPath( $path );
			$path = $this->addEndingSlash( $path );
			$dirs = array();

			foreach( $all as $file )
			{
				$file = $path . $file;

				if( is_dir( $file ) )
					$dirs[] = $file;
			}

			return $dirs;
		}

		// *************************************************
		//	addEndingSlash
		/*!
			@brief Add / in the end of string if there is
			  no / char already in the end

			@param $path String where we add ending slash

			@return String where is char / in the end
		*/
		// *************************************************
		public function addEndingSlash( $path )
		{
			if( substr( $path, strlen( $path ) -1, 1 ) != '/' )
				return $path . '/';

			return $path;
		}
	}

?>
