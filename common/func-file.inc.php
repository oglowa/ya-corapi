<?php

/**
 * @return void
 */
function prepareFilesystem(): void {
    logMe("TARGET_ROOT: '%s'\n", TARGET_ROOTDIR);
    logMe("TARGET_DIR : '%s'\n", TARGET_DIR);
    if (!file_exists(TARGET_ROOTDIR)) {
        mkdir(TARGET_ROOTDIR);
    }
    mkdir(TARGET_DIR, 0777, true);
    mkdir(TARGET_ORGDIR);
    mkdir(TARGET_MODDIR);
}

function prepareCsvLine($format, ...$param) {
    return sprintf($format . "\n", ...$param);
}

function readResultFile($fileName): array {
    $resultList = [];
    if (file_exists($fileName)) {
        $fHandle = fopen($fileName, 'r');

        while ($line = fgets($fHandle, 1000)) {
            $nl           = mb_convert_encoding($line, 'UTF-8');
            $resultList[] = explode(';', $nl);
        }
        fclose($fHandle);
    } else {
        logMe("+++ file '%s' does not exists! +++\n", $fileName);
    }

    return $resultList;
}

function storeResults($targetDir, $page): void {
    $output_file = sprintf("%s%srisks-prod.csv", $targetDir, DIRECTORY_SEPARATOR);
    $pageId      = $page['id'];
    $line        = sprintf("%s;%s%s;%s", $pageId, CONF_BASE_URL, $page['_links']['tinyui'], $page['title']);
    //logMe("%s\n", $line);

    file_put_contents($output_file, sprintf("%s\n", $line), FILE_APPEND);
}

function storeCsv($pathToFile, $fileName, $content): void {
    $output_file = sprintf("%s%s%s.csv", $pathToFile, DIRECTORY_SEPARATOR, $fileName);
    logMe("Writing to '%s'\n", $output_file);

    file_put_contents($output_file, $content, FILE_APPEND);
}

function storeText($pathToFile, $fileName, $content): void {
    $output_file = sprintf("%s%s%s", $pathToFile, DIRECTORY_SEPARATOR, $fileName);
    logMe("Writing to '%s'\n", $output_file);

    file_put_contents($output_file, $content, FILE_APPEND);
}

function storeData($targetDir, $pageId, $body): void {
    $output_file = sprintf("%s%s%s.xml", $targetDir, DIRECTORY_SEPARATOR, $pageId);
    logMe("Writing to '%s'\n", $output_file);

    file_put_contents($output_file, $body);
}

