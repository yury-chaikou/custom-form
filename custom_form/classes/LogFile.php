<?php

class Log_file {
	protected $path_to_file;
	private $handle;

	public function __construct($path_to_file) {
		$this->path_to_file = $path_to_file;
		$this->handle       = fopen( $this->path_to_file, 'a' );
	}
	public function write_to_file( $string ) {
		fwrite( $this->handle,  $string );
	}
	public function __destruct() {
		fclose( $this->handle );
	}
}
