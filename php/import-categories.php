<?php

class Category_Section {
    function cate_import($post, $xmlFileName) {
        if(isset($_POST['catesubmit'])) {
            include_once("lib/config.php");

            $xml = processXML($xmlFileName);

            //post variables
            if(empty($_POST["tplTagger"])){ $tplTagger = false;} else { $tplTagger = $_POST["tplTagger"]; }


            //get categories
            $cateData = array();
            foreach ($xml->channel as $post) {

                foreach ($post->children('wp',true)->category as $c){
                    $cn = parseContent((string)$c->cat_name);
                    $al = $c->category_nicename;

                    if ( $cn ) {
                        $insert = "INSERT INTO `modx_categories` (`category`) VALUES ('".$cn."')";
                        $result = $mysqli->query($insert);
                        $cateData[] = $cn;

                        if($tplTagger){
                            $cateInsert = "INSERT INTO `modx_tagger_groups` (`id`,`name`,`alias`,`field_type`,`allow_blank`,`show_for_templates`,`place`) VALUES ('1','Category','category','tagger-combo-tag','1','1,2,3,4,5','in-tab')";
                            $tagInsert = "INSERT INTO `modx_tagger_tags` (`tag`,`alias`,`group`) VALUES ('".$cn."','".$al."','1')";
                            $cresult = $mysqli->query($cateInsert);
                            $tresult = $mysqli->query($tagInsert);
                        }
                    }
                }

            }

            return '<p>Categories Imported - '.count($cateData).'</p>';

        }
    }
}
