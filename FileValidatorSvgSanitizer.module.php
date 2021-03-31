<?php namespace ProcessWire;

/**
 * Validates and/or sanitizes SVG files in ProcessWire
 * 
 * Uses the svg-sanitizer library: 
 * https://github.com/darylldoyle/svg-sanitizer 
 * 
 * The FileValidatorModule interface and this module file are MIT licensed,
 * while the svg-sanitizer library in the /svgSanitize/ dir is GPL 2.0.
 * As a result, if your installation does not support GPL code, you should
 * inquire with the developer of the sanitizer lib before using this module
 * in your project: https://github.com/darylldoyle
 * 
 * Originally developed by Adrian and Ryan in 2015 and updated in 2020 
 * to change the SVG sanitizer library this module uses. 
 *
 * @property int|bool $removeRemoteReferences
 * @property int|bool $minify
 * @property string $customTags
 * @property string $customAttrs
 *
 */
class FileValidatorSvgSanitizer extends FileValidatorModule {

	public static function getModuleInfo() {
		return array(
			'title' => 'SVG File Sanitizer/Validator',
			'summary' => 'Validates and/or sanitizes uploaded SVG files.', 
			'version' => 4, 
			'author' => 'Adrian and Ryan',
			'autoload' => false, 
			'singular' => false, 
			'validates' => array('svg'),
			'requires' => 'ProcessWire>=3.0.148',
		);
	}

	/**
	 * @var \enshrined\svgSanitize\Sanitizer
	 *
	 */
	protected $svgSanitizer = null;

	/**
	 * Construct
	 * 
	 */
	public function __construct() {
		$this->set('removeRemoteReferences', 1);
		$this->set('minify', 0);
		$this->set('customTags', '');
		$this->set('customAttrs', '');
	}

	/**
	 * Module init
	 * 
	 */
	public function init() {
		$this->getSvgSanitizer();
	}

	/**
	 * Get the SVG Sanitizer instance
	 *
	 * @return \enshrined\svgSanitize\Sanitizer
	 *
	 */
	public function getSvgSanitizer() {
		if($this->svgSanitizer !== null) return $this->svgSanitizer;
		$ns = 'enshrined\svgSanitize';
		$classLoader = $this->wire()->classLoader;
		if(!$classLoader->hasNamespace($ns)) $classLoader->addNamespace($ns, __DIR__ . '/svgSanitize/');
		$className = $ns . '\Sanitizer';
		$this->svgSanitizer = new $className();
		$this->svgSanitizer->removeRemoteReferences((bool) $this->removeRemoteReferences);
		$this->svgSanitizer->minify((bool) $this->minify);
		list($tags, $attrs) = array($this->customTags, $this->customAttrs);
		if($tags || $attrs) {
			require_once(__DIR__ . '/FileValidatorSvgSanitizer.data.php'); 
			if($tags) {
				FileValidatorSvgSanitizerTags::add($tags);
				$this->svgSanitizer->setAllowedTags(new FileValidatorSvgSanitizerTags());
			}
			if($attrs) {
				FileValidatorSvgSanitizerAttributes::add($attrs);
				$this->svgSanitizer->setAllowedAttrs(new FileValidatorSvgSanitizerAttributes());
			}
		}
		return $this->svgSanitizer;
	}

	/**
	 * Is the given SVG file valid? 
	 *
	 * This is for implementation of PW's FileValidator interface. 
	 * 
	 * This method should return:
	 * - boolean TRUE if file is valid
	 * - boolean FALSE if file is not valid
	 * - integer 1 if file is valid as a result of sanitization performed by this method
	 * 	
	 * If method wants to explain why the file is not valid, it should call $this->error('reason why not valid'). 
	 * 
	 * @param string $filename Full path and filename to the file
	 * @return bool|int
	 * 
	 */
	protected function isValidFile($filename) {
		
		$svgSanitizer = $this->getSvgSanitizer();
		$svgDirty = file_get_contents($filename);
		$svgClean = $svgSanitizer->sanitize($svgDirty);
		$svgIssues = $svgSanitizer->getXmlIssues();

		if(!empty($svgIssues)) {
			// log found issues
			$issues = array();
			foreach($svgIssues as $issue) {
				$issue = "$issue[message] (line $issue[line])";
				$issues[$issue] = $issue;
			}
			if($svgClean === false) {
				foreach($issues as $issue) $this->error($issue);
			} else if(count($issues)) {
				$this->log("SvgSanitizer: " . basename($filename) . ": " . implode(', ', $issues));
			}
		}
		
		if($svgClean === false) {
			// sanitize failed
			return false;
			
		} else if($svgDirty === $svgClean) {
			// no changes after sanitization, file is ok. this is sort of unlikely
			// as SvgSanitizer seems to apply minor changes either way
			return true;
		}

		// write new sanitized svg file
		$files = $this->wire()->files;
		$files->unlink($filename);
		if($files->filePutContents($filename, $svgClean) === false) return false;
		
		return 1;
	}
	
	/**
	 * Return the data from the default whitelist
	 *
	 * This method doesn’t do anything for this module, it is just here if you want to
	 * know what are the whitelisted tags and attributes.
	 *
	 * @return array
	 *
	 */
	public function getDefaultWhitelist() {
		return array(
			'tags' => \enshrined\svgSanitize\data\AllowedTags::getTags(),
			'attributes' => \enshrined\svgSanitize\data\AllowedAttributes::getAttributes(),
		);
	}

	/**
	 * Return data from the customized whitelist
	 * 
	 * @return array
	 * 
	 */
	public function getWhitelist() {
		$this->getSvgSanitizer();
		return array(
			'tags' => FileValidatorSvgSanitizerTags::getTags(),
			'attributes' => FileValidatorSvgSanitizerAttributes::getAttributes(),
		);
	}

	/**
	 * Install 
	 * 
	 * @throws WireException
	 * 
	 */
	public function ___install() {
		$exts = get_loaded_extensions();
		if(!in_array('dom', $exts)) {
			throw new WireException('This module requires the PHP “dom” extension (ext-dom)');
		}
		if(!in_array('libxml', $exts)) {
			throw new WireException('This module requires the PHP “libxml” extension (ext-libxml)');
		}
	}

}

