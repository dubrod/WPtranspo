<?php

class Resources_Section {
    function res_import($post, $xmlFileName) {
        if(isset($_POST['ressubmit'])) {
            include_once("lib/config.php");

            $xml = processXML($xmlFileName);

            $pageData = array();
            $postsData = array();

            //get posts
            foreach ($xml->channel->item as $post) {
                //if its not a post, break
                $type = $post->children('wp',true)->post_type;

                $wp = $post->children('wp',true);
                $id = $post->children('wp',true)->post_id;
                $pt =       $mysqli->real_escape_string(parseContent((string)$post->title));
                $it =       $mysqli->real_escape_string(parseContent((string)$post->children('excerpt',true)->encoded));
                $content =  $mysqli->real_escape_string(parseContent((string)$post->children('content',true)->encoded));

                $template = $_POST["tplDefault"];

                $pub_date =  strtotime((string)$post->pubDate);
                    if (empty($pub_date)) {$pub_date = strtotime((string)$wp->post_date);}


                if($type == "post"){

                    if($_POST["tplParent"]){
                        $parent = $_POST["tplParent"];
                    } else {
                        $parent = $post->children('wp',true)->post_parent;
                    }

                    //Insert to DB
                    $insert = "INSERT INTO `modx_site_content` (`id`,`pagetitle`,`introtext`,`content`,`parent`,`template`,`publishedon`,`published`) VALUES ('".$id."','".$pt."','".$it."','".$content."','".$parent."','".$template."','".$pub_date."','1')";
                    $result = $mysqli->query($insert);
                    if ( $result ) { $postsData[] = $pt;}
                }



                //get pages
                //if its not a page, break

                if($type == "page"){

                    if($_POST["tplParent"] && !$_POST["tplPageParent"]){
                        $parent = $_POST["tplParent"];
                    } else {
                        $parent = $post->children('wp',true)->post_parent;
                    }

                    //Insert to DB
                    $insert = "INSERT INTO `modx_site_content` (`id`,`pagetitle`,`introtext`,`content`,`parent`,`template`,`publishedon`,`published`) VALUES ('".$id."','".$pt."','".$it."','".$content."','".$parent."','".$template."','".$pub_date."','1')";
                    $result = $mysqli->query($insert);
                    if( $result ) {
                        $pageData[] = $pt;
                    } else {
                        //try again with no ID, its might have been taken
                        $reinsert = "INSERT INTO `modx_site_content` (`pagetitle`,`introtext`,`content`,`parent`,`template`,`publishedon`,`published`) VALUES ('".$pt."','".$it."','".$content."','".$parent."','".$template."','".$pub_date."','1')";
                        $result_two = $mysqli->query($reinsert);
                        $pageData[] = $pt;
                    }
                }
            }


            return '<p><strong>Posts Imported:</strong> '.count($postsData).' | <strong>Pages Imported:</strong> '.count($pageData).'</p>';

        }
    }
}
