<?php

/* 
CFilesystem - Class for general filesystem operations.
Copyright (C) 2011-2012 Aleksi Räsänen <aleksi.rasanen@runosydan.net>

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
		@brief Filesystem handling class
		@author Aleksi Räsänen 
		@email aleksi.rasanen@runosydan.net
		@copyright Aleksi Räsänen, 2011-2012
		@license GNU AGPL v3 or newer
	*/
	// *************************************************
	class CFilesystem extends CLogger
	{
		private $log;

		// ************************************************** 
		//  __construct
		/*!
			@brief Initialize class variables
			@return Nothing
		*/
		// ************************************************** 
		public function __construct()
		{
			$this->log = new CLog;
			$this->log->setFileWhatWeLog( 'CFilesystem.php' );
			$this->setLoggingClassInstance( $this->log );
			$this->logFunction( '__construct', get_defined_vars() );
		}

		// *************************************************
		//	getAllFilesAndDirectoriesFromPath
		/*!
			@brief Get all files and directories in array 
			  from given path
			@param $path Path where we search
			@return Array of files if all is fine, -1 if $path
			  not exists, -2 if $path is not a directory.
		*/
		// *************************************************
		public function getAllFilesAndDirectoriesFromPath( $path )
		{
			$this->logFunction( 'getAllFilesAndDirectoriesFromPath',
				get_defined_vars() );

			if(! file_exists( $path ) )
			{
				$this->logMsg( 'Error: File does not exists!' );
				$this->logMsg( 'Return: -1' );
				$this->logReturn();
				return -1;
			}

			if(! is_dir( $path ) )
			{
				$this->logMsg( 'Error: Path was not a directory!' );
				$this->logMsg( 'Return: -2' );
				$this->logReturn();
				return -2;
			}
			
			$files = array();
			$handle = opendir( $path );

			if( $handle )
			{
				while( false !== ( $file = readdir( $handle ) ) )
					$files[] = $file;
			}

			sort( $files );

			$this->logMsg( 'Return: ' . $files );
			$this->logReturn();
			return $files;
		}

		// *************************************************
		//	getAllFilesFromPath
		/*!
			@brief Get all normal files from given path
			@param $path Path where we search
			@return Array of files. -1 if $path does not exists,
			  -2 if path exists but is not a directory.
		*/
		// *************************************************
		public function getAllFilesFromPath( $path )
		{
			$this->logFunction( 'getAllFilesFromPath', get_defined_vars() );

			$all = $this->getAllFilesAndDirectoriesFromPath( $path );
			$path = $this->addEndingSlash( $path );
			$files = array();

			if(! is_array( $all ) )
			{
				$this->logMsg( 'Error: Did not got array' );
				$this->logMsg( 'Return: Return ' . $all );
				$this->logReturn();
				return $all;
			}

			foreach( $all as $file )
			{
				$file = $path . $file;

				if( is_file( $file ) )
					$files[] = $file;
			}

			$this->logMsg( 'Return: ' . $files );
			$this->logReturn();
			return $files;
		}

		// ************************************************** 
		//  getFilesFromPathByRegexp
		/*!
			@brief Get files with regexp name searching
			@param $path Path where we search
			@param $regexp Regular expression what needs
			  to be ended with /
			@return Array of filenames. -1 if $path not exists,
			  -2 if path exists but is not a directory.
		*/
		// ************************************************** 
		public function getFilesFromPathByRegexp( $path, $regexp )
		{
			$this->logFunction( 'getFilesFromPathByRegexp',
				get_defined_vars() );

			$all = $this->getAllFilesFromPath( $path );

			if(! is_array( $all ) )
			{
				$this->logMsg( 'Error: Did not got array!' );
				$this->logMsg( 'Return: ' . $all );
				$this->logReturn();
				return $all;
			}

			$files = array();
			
			foreach( $all as $filename )
			{
				if( preg_match( $regexp, basename( $filename ) ) )
					$files[] = $filename;
			}

			$this->logMsg( 'Return: ' . $files );
			$this->logReturn();
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
			$this->logFunction( 'getAllFilesFromPathWithExtension',
				get_defined_vars() );

			$this->logMsg( 'In method: getAllFilesFromPathWithExtension' );
			$this->logMsg( 'Param $path: ' . $path );
			$this->logMsg( 'Param $ext: ' . $ext );

			if( substr( $ext, 0, 1 ) == '.' )
				$ext = substr( $ext, 1 );

			$files = array();
			$all = $this->getAllFilesFromPath( $path );

			if(! is_array( $all ) )
			{
				$this->logMsg( 'Error: Did not got array' );
				$this->logMsg( 'Return: ' . $all );
				$this->logReturn();
				return $all;
			}

			foreach( $all as $file )
			{
				$e = $this->getFileExtension( $file );

				if(! is_array( $ext ) && $e == $ext )
					$files[] = $file;
				else if( is_array( $ext ) && in_array( $e, $ext ) )
					$files[] = $file;
			}

			$this->logMsg( 'Return: ' . $files );
			$this->logReturn();
			return $files;
		}

		// ************************************************** 
		//  getAllFilesFromPathWithMultipleExtensions
		/*!
			@brief Get all files from path by multiple extensions.
			@param $path Path where we search
			@param $ext_array Array of extensions of files what should
			  be searched from given path
			@return Array of filenames
		*/
		// ************************************************** 
		public function getAllFilesFromPathWithMultipleExtensions(
			$path, $ext_array )
		{
			$this->logFunction( 'getAllFilesFromPathWithMultipleExtensions',
				get_defined_vars() );

			$files = array();

			foreach( $ext_array as $ext )
			{
				$files[] = $this->getAllFilesFromPathWithExtension( 
					$path, $ext );
			}

			$this->logMsg( 'Return: ' . $files );
			$this->logReturn();
			return $files;
		}
		
		// ************************************************** 
		//  getAllFilesFromPathWithSameExtensions
		/*!
			@brief Get all files from path which have same filenames
			  but different extensions. For example this might be
			  used when we want to search all AVI-files from path
			  which does have also TXT-file with it in same name but
			  with different extension.
			@param $path Path where we search
			@param $ext_array Array of extensions which must be found.
			@return Array of filenames. NOTE! This will return only
			  the basename of filenames! For example if we search
			  files *.txt and *.jpg and there is mufasa.jpg and mufasa.txt
			  this return array include only 'mufasa', not any extension!
		*/
		// ************************************************** 
		public function getAllFilesFromPathWithSameExtensions(
			$path, $ext_array )
		{
			$this->logFunction( 'getAllFilesFromPathWithSameExtensions',
				get_defined_vars() );

			$files = $this->getAllFilesFromPathWithMultipleExtensions(
				$path, $ext_array );

			if(! isset( $files[0] ) )
			{
				$this->logMsg( 'Info: No files set in $files variable' );
				$this->logMsg( 'Return: ' . $files );
				$this->logReturn();
				return $files;
			}

			$final_files = array();
			
			foreach( $files[0] as $filename )
			{
				$file_to_search = $this->getFilenameWithoutExtension(
					$filename );

				for( $i=1;  $i<count( $files );$i++ )
				{
					foreach( $files[$i] as $tmp )
					{
						$tmp = $this->getFilenameWithoutExtension( $tmp );

						if( $tmp == $file_to_search )
						{
							$final_files[] = $file_to_search;
							break;
						}
					}
				}
			}

			$this->logMsg( 'Return: ' . $final_files );
			$this->logReturn();
			return $final_files;
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
			$this->logFunction( 'getFileExtension', get_defined_vars() );

			$filename = explode( '.', basename( $filename ) );
			$max = count( $filename ) -1;

			if( $max > 0 )
			{
				$this->logMsg( 'Return: ' . $filename[$max] );
				$this->logReturn();
				return $filename[$max];
			}

			$this->logMsg( 'Return: Empty string' );
			$this->logReturn();
			return '';
		}

		// *************************************************
		//	getAllDirectoriesFromPath
		/*!
			@brief Get all directories from given path
			@param $path Path where we search
			@return Array of directory names, -1 if $path not exists,
			  -2 if path exists but is not a directory.
		*/
		// *************************************************
		public function getAllDirectoriesFromPath( $path )
		{
			$this->logFunction( 'getAllDirectoriesFromPath', 
				get_defined_vars() );

			$all = $this->getAllFilesAndDirectoriesFromPath( $path );

			if(! is_array( $all ) )
			{
				$this->logMsg( 'Error: Not array!' );
				$this->logMsg( 'Return: ' . $all );
				$this->logReturn();
				return $all;
			}

			$path = $this->addEndingSlash( $path );
			$dirs = array();

			foreach( $all as $file )
			{
				$file = $path . $file;

				if( is_dir( $file ) )
					$dirs[] = $file;
			}

			$this->logMsg( 'Return: ' . $dirs );
			$this->logReturn();
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
			$this->logFunction( 'addEndingSlash', get_defined_vars() );

			$ret = $path;

			if( substr( $path, strlen( $path ) -1, 1 ) != '/' )
				$ret = $path . '/';

			$this->logMsg( 'Return: ' . $ret );
			$this->logReturn();
			return $ret;
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
			$this->logFunction( 'createFolderIfNotExists', get_defined_vars() );

			if( file_exists( $path ) )
			{
				$this->logMsg( 'Info: File already exists' );
				$this->logReturn();
				return;
			}

			$tmp = explode( '/', $path );
			$tmp_path = '';

			foreach( $tmp as $folder )
			{
				if(! empty( $folder ) )
					$tmp_path .= $folder . '/';

				if( $folder == '..' || $folder == '.' || empty( $folder ) )
					continue;

				if( file_exists( $tmp_path ) )
					continue;

				if(! @mkdir( $tmp_path ) )
				{
					$this->logMsg( 'Error: Cannot create folder ' 
						. $tmp_path );

					throw new Exception( 'Cannot create folder ' 
						. $tmp_path );
				}
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
			$this->logFunction( 'getFilenameWithoutExtension', 
				get_defined_vars() );

			$filename = basename( $filename );
			$pos = strrpos( $filename, '.' );

			if(! $pos )
			{
				$this->logMsg( 'Return: ' . $filename );
				$this->logReturn();
				return $filename;
			}

			$ret = substr( $filename, 0, $pos );

			$this->logMsg( 'Return: ' . $ret );
			$this->logReturn();
			return $ret;
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
		public function getFileContentsInArray( $filename, $explode_string )
		{
			$this->logFunction( 'getFileContentsInArray', 
				get_defined_vars() );

			if(! file_exists( $filename ) )
			{
				$this->logMsg( 'Error: File does not exists!' );
				$this->logMsg( 'Return: Empty array' );
				$this->logReturn();
				return array();
			}

			$data = file_get_contents( $filename );
			$ret = explode( $explode_string, $data );

			$this->logMsg( 'Return: ' . $ret );
			$this->logReturn();
			return $ret;
		}

		// ************************************************** 
		//  createEmptyFile
		/*!
			@brief Creates an empty file. We do not create an
			  empty file if file already exists!
			@param $filename Filename
		*/
		// ************************************************** 
		public function createEmptyFile( $filename )
		{
			$this->logFunction( 'createEmptyFile', get_defined_vars() );

			if( file_exists( $filename ) )
			{
				$this->logMsg( 'Error: File already exists' );
				$this->logReturn();
				return;
			}
			
			if(! @touch( $filename ) )
			{
				$error = error_get_last();
				$this->logMsg( 'Error: Cannot create file! '
					. 'Error was: "' . $error['message'] . '"' );
			}
			else
			{
				$this->logMsg( 'Info: File created' );
			}

			$this->logReturn();
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
			$this->logFunction( 'createFileWithData', get_defined_vars() );

			if( $mode != 'w' && $mode != 'a' )
			{
				$this->logMsg( 'Error: File mode must be w or a!' );
				throw new Exception( 'File mode must be w or a!' );
			}

			$fh = fopen( $filename, $mode );
			fwrite( $fh, $data );
			fclose( $fh );

			$this->logReturn();
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
			$this->logFunction( 'getFilenamesNotInArray', get_defined_vars() );

			$all_files = $this->getAllFilesFromPath( $path );
			$files_not_in_path = array();
			
			foreach( $all_files as $existing_file )
			{
				if(! in_array( basename( $existing_file ), $filenames ) )
					$files_not_in_path[] = $existing_file;
			}

			$this->logMsg( 'Return: ' . $files_not_in_path );
			$this->logReturn();
			return $files_not_in_path;
		}

		// ************************************************** 
		//  deleteFile
		/*!
			@brief Deletes a file if file is found
			@param $filename File to delete
			@return Nothing
		*/
		// ************************************************** 
		public function deleteFile( $filename )
		{
			$this->logFunction( 'deleteFile', get_defined_vars() );

			if(! file_exists( $filename ) )
			{
				$this->logMsg( 'Error: File not found' );
				$this->logReturn();
				return;
			}

			if(! unlink( $filename ) )
			{
				$this->logMsg( 'Error: Cannot delete file!' );
				throw new Exception( 'Cannot delete file ' 
					. $filename );
			}
			else
			{
				$this->logMsg( 'File removed.' );
			}

			$this->logReturn();
		}
		
		// ************************************************** 
		//  getTextBetweenStrings
		/*!
			@brief Get all texts between two strings which are
			  on different lines.
			@param $data Data where we search. This must be array!
			@param $begin String where we start
			@param $end String where we end
			@return String. If $data is not an array, return -1.
		*/
		// ************************************************** 
		public function getTextBetweenStrings( $data, $begin, $end )
		{
			$this->logFunction( 'getTextBetweenStrings', get_defined_vars() );

			if(! is_array( $data ) )
			{
				$this->logMsg( 'Error: Data is not an array!' );
				$this->logMsg( 'Return: -1' );
				$this->logReturn();
				return -1;
			}

			$readed = array();
			$started = false;

			foreach( $data as $line )
			{
				$line = trim( $line );

				if( $started && $line == $end )
					$started = false;

				if( $line == $begin )
				{
					$started = true;	
					continue;
				}

				if( $started )
					$readed[] = $line;
			}

			$this->logMsg( 'Return: ' . $readed );
			$this->logReturn();
			return $readed;
		}

		// ************************************************** 
		//  getFileInfo
		/*!
			@brief Get file informations like mtime etc.
			@param $filename Name of a file which informations we want
			@return Assoc array, same values than PHP stat
			  command will return. If files does not exists,
			  returns -1.
		*/
		// ************************************************** 
		public function getFileInfo( $filename )
		{
			$this->logFunction( 'getFileInfo', get_defined_vars() );

			if(! file_exists( $filename ) )
			{
				$this->logMsg( 'Error: File does not exists!' );
				$this->logMsg( 'Return: -1' );
				$this->logReturn();
				return -1;
			}

			$ret = stat( $filename );
			$this->logMsg( 'Return: ' . $ret );

			$this->logReturn();
			return $ret;
		}
	}

?>
