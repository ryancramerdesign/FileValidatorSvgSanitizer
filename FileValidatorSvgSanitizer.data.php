<?php namespace ProcessWire;

class FileValidatorSvgSanitizerData {
	protected static $data = array();
	public static function add($data) { 
		if(!is_array($data)) {
			$data = trim($data); 
			$data = str_replace(' ', "\n", $data); 
			$data = explode("\n", $data);
		}
		foreach($data as $line) {
			$line = trim($line); 
			if(strlen($line)) self::$data[] = $line;
		}
	}
	public static function process(array $data) {
		foreach(self::$data as $line) {
			if(strpos($line, '-') === 0) { // remove
				$key = array_search(trim($line, '- '), $data);
				if($key !== false) unset($data[$key]);
			} else { // add
				$data[] = ltrim($line, '+ ');
			}
		}
		return $data;
	}
}

class FileValidatorSvgSanitizerAttributes extends FileValidatorSvgSanitizerData implements \enshrined\svgSanitize\data\AttributeInterface {
	public static function getAttributes() {
		$data = \enshrined\svgSanitize\data\AllowedAttributes::getAttributes();
		return self::process($data); 
	}
}

class FileValidatorSvgSanitizerTags extends FileValidatorSvgSanitizerData implements \enshrined\svgSanitize\data\TagInterface {
	public static function getTags() {
		$data = \enshrined\svgSanitize\data\AllowedTags::getTags();
		return self::process($data); 
	}
}

