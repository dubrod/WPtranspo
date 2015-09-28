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
                $menuIndex = $post->children('wp',true)->menu_order;
                $template = "";

                //if Respect Publish setting
                if($_POST["tplPublish"]){
                    //if its set to publish
                    if ($post->children('wp',true)->status == "publish") {
                        $publish = "1";
                    } else {
                        $publish = "0";
                    }
                }

                //loop through Item postmeta values
                foreach ($post->children('wp',true)->postmeta as $wpm) {

                    //if template matching set
                    if($_POST["tplMatch"]){
                        //if its a template Meta Item
                        if ($wpm->meta_key == "_wp_page_template") {
                            //<wp:meta_value><![CDATA[default]]></wp:meta_value>
                            $wp_temp = parseContent((string)$wpm->meta_value);

                            //check for automatic match
                            $tpl_query = "SELECT * FROM modx_site_templates";
                            if ($result = $mysqli->query($tpl_query)) {
                                while ($row = $result->fetch_array()) {
                                    //if name matches set it
                                    if($row["templatename"] == $wp_temp){
                                        $template = $row["id"];
                                    }
                                }
                            }
                        }
                    }


                } //eof PostMeta Loop


                //if template was not set above
                if(!$template){$template = $_POST["tplDefault"];}

                $pub_date =  strtotime((string)$post->pubDate);
                    if (empty($pub_date)) {$pub_date = strtotime((string)$wp->post_date);}


                //Start Post Type Custom options

                if($type == "post"){

                    //if Global Parent Set
                    if($_POST["tplParent"]){
                        $parent = $_POST["tplParent"];
                    } else {
                        $parent = $post->children('wp',true)->post_parent;
                    }

                    //Insert to DB
                    $insert = "INSERT INTO `modx_site_content` (`id`,`pagetitle`,`introtext`,`content`,`parent`,`template`,`menuindex`,`publishedon`,`published`) VALUES ('".$id."','".$pt."','".$it."','".$content."','".$parent."','".$template."','".$menuIndex."','".$pub_date."','".$publish."')";
                    $result = $mysqli->query($insert);
                    if ( $result ) {
                        $postsData[] = $pt;
                    } else {
                        //try again with no ID, it might have been taken by preset
                        $reinsert = "INSERT INTO `modx_site_content` (`pagetitle`,`introtext`,`content`,`parent`,`template`,`menuindex,`publishedon`,`published`) VALUES ('".$pt."','".$it."','".$content."','".$parent."','".$template."','".$menuIndex."','".$pub_date."','".$publish."')";
                        $result_two = $mysqli->query($reinsert);
                        $postsData[] = $pt;
                    }
                }



                //get pages
                //if its not a page, break

                if($type == "page"){

                    //if Global Parent && Separate Page is NOT checked
                    if($_POST["tplParent"] && !$_POST["tplPageParent"]){
                        $parent = $_POST["tplParent"];
                    } else {
                        $parent = $post->children('wp',true)->post_parent;
                    }

                    //Insert to DB
                    $insert = "INSERT INTO `modx_site_content` (`id`,`pagetitle`,`introtext`,`content`,`parent`,`template`,`menuindex,`publishedon`,`published`) VALUES ('".$id."','".$pt."','".$it."','".$content."','".$parent."','".$template."','".$menuIndex."','".$pub_date."','".$publish."')";
                    $result = $mysqli->query($insert);
                    if( $result ) {
                        $pageData[] = $pt;
                    } else {
                        //try again with no ID, it might have been taken by preset
                        $reinsert = "INSERT INTO `modx_site_content` (`pagetitle`,`introtext`,`content`,`parent`,`template`,`menuindex,`publishedon`,`published`) VALUES ('".$pt."','".$it."','".$content."','".$parent."','".$template."','".$menuIndex."','".$pub_date."','".$publish."')";
                        $result_two = $mysqli->query($reinsert);
                        $pageData[] = $pt;
                    }
                }
            }


            return '<p><strong>Posts Imported:</strong> '.count($postsData).' | <strong>Pages Imported:</strong> '.count($pageData).'</p>';

        }
    }
}
