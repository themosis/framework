<?php
namespace Themosis\Core;

/**
 * Common "interface" for extending the WordPress
 * 'functions.php' file.
*/
abstract class Loader
{	
	/**
	 * Keep a copy of file names.
	*/
	protected $names = [];

	/**
	 * Scan the directory at the given path and include
	 * all files. Only 1 level iteration.
	 * 
	 * @param string $path The directory/file path.
	 * @return bool True. False if not appended.
	 */
	protected function append($path)
    {
		if (is_dir($path))
        {
			$dir = new \DirectoryIterator($path);

			foreach ($dir as $file)
            {
				if (!$file->isDot() || !$file->isDir())
                {
					$file_extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);

					if ($file_extension === 'php')
                    {
						$this->names[] = $file->getBasename('.php');
						include_once $file->getPath().DS.$file->getBasename();
					}
				}
			}

			return true;
		}
		
		return false;
	}
}