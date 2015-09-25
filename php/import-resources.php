<?php

class Resources_Section {
    function res_import($post, $xmlFileName) {
        if(isset($_POST['ressubmit'])) {
            include_once("lib/config.php");

            $xml = processXML($xmlFileName);

            //get posts
            $postsData = array();
            foreach ($xml->channel->item as $post) {
                //if its not a post, break
                $type = $post->children('wp',true)->post_type;

                if($type == "post"){
                    $wp = $post->children('wp',true);
                    $id = $post->children('wp',true)->post_id;
                    $parent = $post->children('wp',true)->post_parent;
                    $pt =       $mysqli->real_escape_string(parseContent((string)$post->title));
                    $it =       $mysqli->real_escape_string(parseContent((string)$post->children('excerpt',true)->encoded));
                    $content =  $mysqli->real_escape_string(parseContent((string)$post->children('content',true)->encoded));
                    $pub_date =  strtotime((string)$post->pubDate);
                        if (empty($pub_date)) {
                            $pub_date = strtotime((string)$wp->post_date);
                        }
                    $insert = "INSERT INTO `modx_site_content` (`id`,`pagetitle`,`introtext`,`content`,`parent`,`publishedon`,`published`) VALUES ('".$id."','".$pt."','".$it."','".$content."','".$parent."','".$pub_date."','1')";
                    $result = $mysqli->query($insert);
                    if ( $result ) { $postsData[] = $pt;}
                }
            }

            $pageData = array();
            /*get pages

            foreach ($xml->channel->item as $post) {
                //if its not a page, break
                $type = $post->children('wp',true)->post_type;

                if($type == "page"){
                    $p = parseContent((string)$post->title);
                    //echo $p."<br>";
                    if ( $p ) { $pageData[] = $p;}
                }
            }*/


            return '<p><strong>Posts Imported:</strong> '.count($postsData).' | <strong>Pages Imported:</strong> '.count($pageData).'</p>';

        }
    }
}
