<?php
namespace Dadeky\Ddd\Domain\Model\Document;

abstract class AbstractDocumentGenerator
{
    const OUTPUT_TYPE_FILE      = 'F';
    const OUTPUT_TYPE_STREAM    = 'I';
    
    const OUTPUT_FILE_EXTENSION_PDF = 'pdf';
    const OUTPUT_FILE_EXTENSION_XLS = 'xls';
    const OUTPUT_FILE_EXTENSION_XML = 'xml';
    
    public static $_documentWidth         = 80;
    public static $_documentHeight        = 60;
    public static $_documentOrientation   = 'L';
    
    public static function getOutputTypeFile()
    {
        return self::OUTPUT_TYPE_FILE;
    }
    
    public static function getOutputTypeStream()
    {
        return self::OUTPUT_TYPE_STREAM;
    }
    
    
    public static function setDocumentWidth( $width ) {
        self::$_documentWidth = $width;
    }
    
    public static function setDocumentHeight( $height ) {
        self::$_documentHeight = $height;
    }
    
    public static function setDocumentOrientation( $orientation ) {
        self::$_documentOrientation = $orientation;
    }
    
    
    
    /**
     * Method that extending classes have to implement
     * @param DocumentDtoInterface $data
     * @param string $path
     * @param string $filename
     * @param string $extension
     * @param string $outputTo
     * @param string $documentRenderData
     */
    public abstract function generateDocument(DocumentDtoInterface $data, $path, $filename, $extension, $outputTo, $documentRenderData);
    
    
    /**
     * This function should be called in beginning, if you want to define custom
     * width, height & orientation
     */
    public abstract function initializeDocument();
    
}

