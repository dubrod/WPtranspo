<?php
//ini_set("display_errors", "1");
//error_reporting(E_ALL);

function parseContent($string) {
    $string = (string)$string;
    $string = html_entity_decode((string)$string,ENT_COMPAT);
    $string = str_replace(array(
        '?',
        '?',
        '?',
        '[[',
        ']]',
        '&#147;', //Wordpress made ? this for some reason
    ),array(
        '&#147;',
        '&#148;',
        '&#189;',
        '&#91;&#91;',
        '&#93;&#93;',
        '?',
    ),$string);
    return $string;
}

function processXML($filename){
    //set the file
    //$xml = file_get_contents($_FILES['xml-file']['tmp_name']);
    $xml = file_get_contents("xml/".$filename);

    /** Remove meta tags: */
    $contents = '<?xml version="1.0" encoding="UTF-8" ?>'.PHP_EOL.substr($xml, mb_strpos($xml, '<rss version="2.0"'));
    /** Get rid of WP comments */
    $contents = str_replace('<!--more-->','',$contents);
    /** Fix improper <pre> placements */
    $contents = str_replace('</pre>]]>','</pre> ]]>',$contents);

    /* get rid of all the WP-specific special characters */
    $contents = str_replace(array(
            "\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6",
            '',
        ),array(
            "'", "'", '"', '"', '-', '--', '&#189;',
            '',
        ), $contents);
    $xml = simplexml_load_string($contents);
    return $xml;
}

?>
