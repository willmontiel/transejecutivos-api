<?php

/**
 * Class to handle writing error file
 * This class will have CRUD methods for database tables
 *
 * @author Will Montiel
 */
class LoggerHandler {
	public $file = '../v1/error.log';
	public function writeArray($array) {
	    // Abre el fichero para obtener el contenido existente
	    $actual = file_get_contents($this->file);
	    // Añade una nueva persona al fichero
	    $actual .= print_r($array, true);
	    // Escribe el contenido al fichero
	    file_put_contents($this->file, $actual);
	}

	public function writeString($string) {
	    // Abre el fichero para obtener el contenido existente
	    $actual = file_get_contents($this->file);
	    // Añade una nueva persona al fichero
	    $actual .= $string . "\n";
	    // Escribe el contenido al fichero
	    file_put_contents($this->file, $actual);
	}
}