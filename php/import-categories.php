<?php

class Category_Section {
    function cate_import($post, $xmlFileName) {
        if(isset($_POST['catesubmit'])) {
            include_once("lib/config.php");

            $xml = processXML($xmlFileName);

            //get categories
            $cateData = array();
            foreach ($xml->channel as $post) {

                foreach ($post->children('wp',true)->category as $c){
                    $cn = parseContent((string)$c->cat_name);
                    if ( $cn ) {
                        $insert = "INSERT INTO `modx_categories` (`category`) VALUES ('".$cn."')";
                        $result = $mysqli->query($insert);
                        $cateData[] = $cn;
                    }
                }

            }

            return '<p>Categories Imported - '.count($cateData).'</p>';

        }
    }
}
