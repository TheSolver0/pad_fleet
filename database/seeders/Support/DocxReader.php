<?php

namespace Database\Seeders\Support;

use RuntimeException;
use ZipArchive;

class DocxReader
{
    public static function lines(string $path): array
    {
        if (! is_file($path)) {
            throw new RuntimeException("Fichier introuvable : {$path}");
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException("Impossible d'ouvrir le fichier DOCX : {$path}");
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            throw new RuntimeException("Contenu document.xml introuvable dans : {$path}");
        }

        $document = simplexml_load_string($xml);
        if ($document === false) {
            throw new RuntimeException("XML invalide dans : {$path}");
        }

        $document->registerXPathNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $lines = [];
        foreach ($document->xpath('//w:p') ?: [] as $paragraph) {
            $parts = [];
            foreach ($paragraph->xpath('.//w:t') ?: [] as $textNode) {
                $parts[] = (string) $textNode;
            }

            $line = trim(implode('', $parts));
            if ($line !== '') {
                $lines[] = $line;
            }
        }

        return $lines;
    }
}
