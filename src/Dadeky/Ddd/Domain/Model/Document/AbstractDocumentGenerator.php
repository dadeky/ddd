<?php
namespace Dadeky\Ddd\Domain\Model\Document;

abstract class AbstractDocumentGenerator
{
    const OUTPUT_TYPE_FILE = 'F';
    const OUTPUT_TYPE_STREAM = 'I';
    
    const OUTPUT_FILE_EXTENSION_PDF = 'pdf';
    const OUTPUT_FILE_EXTENSION_XLS = 'xls';
    
    public static function getOutputTypeFile()
    {
        return self::OUTPUT_TYPE_FILE;
    }
    
    public static function getOutputTypeStream()
    {
        return self::OUTPUT_TYPE_STREAM;
    }
    
    /**
     * Method that extending classes have to implement
     * @param DocumentDtoInterface $data
     * @param string $path
     * @param string $filename
     * @param string $extension
     * @param string $outputTo
     */
    public abstract function generateDocument(DocumentDtoInterface $data, $path, $filename, $extension, $outputTo);
}

