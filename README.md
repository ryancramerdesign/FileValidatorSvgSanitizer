# ProcessWire SVG Sanitizer 

## A FileValidator module for ProcessWire 3.x

Installation of this module is recommended if using SVG file uploads
in ProcessWire, as SVG files can easily contain malicious code. On the other hand,
if you do not need SVG file uploads then you should simply prevent their use by
never adding "svg" as a file extension to your file/image fields. 

When installed, this module will automatically sanitize SVG files uploaded to file 
or image fields in ProcessWire (InputfieldFile and InputfieldImage). It will also
apply to anything else that calls the `$sanitizer->validateFile()` API method.

This module does not guarantee that SVG files are safe, but it does help to 
significantly reduce the risks associated with SVG files by sanitizing them from
known problematic tabs and attributes that can be present in SVG files. If you 
want to be 100% safe then do not allow SVG file uploads at all. 

The FileValidatorModule interface and this module file are MIT licensed,
while the svg-sanitizer library in the /svgSanitize/ dir is GPL 2.0.
As a result, if your usage does not support GPL code, you should inquire with 
the developer of the sanitizer lib before using this module in your project: 
<https://github.com/darylldoyle>

This module was developed by @adrianbj and @ryancramerdesign in 2015 and updated
in 2020 to replace the existing SVG library with a more up-to-date version located
at: <https://github.com/darylldoyle/svg-sanitizer>


## Requirements

- ProcessWire 3.0.148 or newer (3.0.164+ recommended). 
- PHP must have the "dom" and "libxml" extensions (most already do).


## Install

1. Copy all of the files for this module into a new directory named:
   `/site/modules/FileValidatorSvgSanitizer/`
2. In your ProcessWire admin, navigate to: “Modules > Refresh”
3. Click “Install” for module “SVG File Sanitizer/Validator”. 


## Upgrade

To upgrade the module, remove your old FileValidatorSvgSanitizer directory
(or hide it by placing a period in front of it) and then follow steps 1 
and 2 of the Install directions above. 

Because of the SVG library change, please note the following feature changes
in version 2+ relative to version 1: 

1. It does not use a `$config->FileValidatorSvgSanitizer` config array.
2. It does not have configurable whitelists of tags, attributes or fields. 
3. It only rejects SVG files that cannot be made valid through sanitization.
4. Sanitization seems to be quite a bit better than the previous. 
5. It optionally includes the ability to minify whitespace in an SVG file.
6. It performs more verbose logging of issues in the file-validator log. 


## General usage

This module is used automatically on any SVG files uploaded to file or image
fields in ProcessWire that use InputfieldFile or InputfieldImage. It is also
used on any SVG files passed to `$sanitizer->validateFile()`.

Activity for this module can be found in “Setup > Logs > File-validator”. 
It logs any issues it identifies and cleans out of SVG files.

## API usage

To use the module directly:
~~~~~
$validator = $modules->get('FileValidatorSvgSanitizer');
$result = $validator->isValid('/path/to/file.svg'); 

if($result === true) {
  // true: file is valid as-is
} else if($result) {
  // 1: file was made valid through sanitization
} else {
  // false: file is not valid svg
}
~~~~~
To use the module through the `$sanitizer` API var, you can validate
in the same way as any other file type: 
~~~~~
$result = $sanitizer->validateFile('/path/to/file.svg'); 

if($result === null) {
  // null: no FileValidator module available for given file type
} else if($result) {
  // true: file is valid or was made valid
} else {
  // false: file is not valid
}
~~~~~



