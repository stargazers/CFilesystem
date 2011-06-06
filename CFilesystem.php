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

			sort( $files );
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

		// ************************************************** 
		//  getFilesFromPathByRegexp
		/*!
			@brief Get files with regexp name searching
			@param $path Path where we search
			@param $regexp Regular expression what needs
			  to be ended with /
			@return Array of filenames
		*/
		// ************************************************** 
		public function getFilesFromPathByRegexp( $path, $regexp )
		{
			$all = $this->getAllFilesFromPath( $path );
			$files = array();
			
			foreach( $all as $filename )
			{
				if( preg_match( $regexp, basename( $filename ) ) )
					$files[] = $filename;
			}

			return $files;
		}

		// ************************************************** 
		//  getAllFilesFromPathWithExtension
		/*!
			@brief Get all files by extension from given path
			@param $path Path where we search
			@param $ext Extension. If multiple extensions
			  are given, then this must be array. If only one
			  extension is given, then it must be string.
			@return Array of filenames
		*/
		// ************************************************** 
		public function getAllFilesFromPathWithExtension( $path, $ext )
		{
			$files = array();
			$all = $this->getAllFilesFromPath( $path );

			foreach( $all as $file )
			{
				$e = $this->getFileExtension( $file );

				if(! is_array( $ext ) && $e == $ext )
					$files[] = $file;
				else if( is_array( $ext ) && in_array( $e, $ext ) )
					$files[] = $file;
			}

			return $files;
		}

		// ************************************************** 
		//  getFileExtension
		/*!
			@brief Gets a file extension
			@param $filename File
			@return String
		*/
		// ************************************************** 
		public function getFileExtension( $filename )
		{
			$filename = explode( '.', basename( $filename ) );
			$max = count( $filename ) -1;

			if( $max > 0 )
				return $filename[$max];

			return '';
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

		// ************************************************** 
		//  createFolderIfNotExists
		/*!
			@brief Create a folder if it does not exists.
			  Note that this will not try to create a folder
			  if there is already a file with that name.
			@param $path Path to create
		*/
		// ************************************************** 
		public function createFolderIfNotExists( $path )
		{
			if( file_exists( $path ) )
				return;

			$tmp = explode( '/', $path );
			$tmp_path = '';

			foreach( $tmp as $folder )
			{
				if(! empty( $folder ) )
					$tmp_path .= $folder . '/';

				if( $folder == '..' || $folder == '.' || empty( $folder ) )
					continue;

				if(! @mkdir( $tmp_path ) )
					throw new Exception( 'Cannot create folder ' 
						. $tmp_path );
			}
		}

		// ************************************************** 
		//  getFilenameWithoutExtension
		/*!
			@brief Returns a filename without its extension
			@param $filename Filename
			@return String
		*/
		// ************************************************** 
		public function getFilenameWithoutExtension( $filename )
		{
			$filename = basename( $filename );
			$pos = strrpos( $filename, '.' );

			if(! $pos )
				return $filename;

			return substr( $filename, 0, $pos );
		}

		// ************************************************** 
		//  getFileContentsInArray
		/*!
			@brief Read file contents and store its content
			  in the array.
			@param $filename Filename
			@param $explode_string Char/string what is used
			  when we do exploding from string to array.
			@return Array
		*/
		// ************************************************** 
		public function getFileContentsInArray( $filename, 
			$explode_string )
		{
			if(! file_exists( $filename ) )
				return array();

			$data = file_get_contents( $filename );
			return explode( $explode_string, $data );
		}

		// ************************************************** 
		//  createEmptyFile
		/*!
			@brief Creates an empty file
			@param $filename Filename
		*/
		// ************************************************** 
		public function createEmptyFile( $filename )
		{
			if(! file_exists( $filename ) )
				touch( $filename );
		}

		// ************************************************** 
		//  createFileWithData
		/*!
			@brief Creates a new file with given data.
			@param $filename Filename
			@param $mode Mode of write, 'w' for write, 'a' for append.
			@param $data Data to write in file.
		*/
		// ************************************************** 
		public function createFileWithData( $filename, $mode, $data )
		{
			if( $mode != 'w' && $mode != 'a' )
			{
				throw new Exception( 'File mode must be w or a!' );
			}

			$fh = fopen( $filename, $mode );
			fwrite( $fh, $data );
			fclose( $fh );
		}

		// ************************************************** 
		//  getFilenamesNotInArray
		/*!
			@brief Get all filenames which does not exists in
			  given array of filenames.
			@param $path Path where we search all files
			@param $filenames Array of filenames what we do
			  not want to be listed in return array
			@return Array of filenames in $path which was not
			  listed in $filenames array.
		*/
		// ************************************************** 
		public function getFilenamesNotInArray( $path, $filenames )
		{
			$all_files = $this->getAllFilesFromPath( $path );
			$files_not_in_path = array();
			
			foreach( $all_files as $existing_file )
			{
				if(! in_array( basename( $existing_file ), $filenames ) )
					$files_not_in_path[] = $existing_file;
			}

			return $files_not_in_path;
		}
	}

?>
