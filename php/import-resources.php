<?php

class Resources_Section {
    function res_import($post, $xmlFileName) {
        if(isset($_POST['ressubmit'])) {
            include_once("lib/config.php");

            $xml = processXML($xmlFileName);

            $pageData = array();
            $pageDataErrors = array();
            $postsData = array();
            $postsDataErrors = array();

            //post variables
            if(empty($_POST["tplMatch"])){ $tplMatch = false;} else { $tplMatch = $_POST["tplMatch"]; }
            if(empty($_POST["tplPublish"])){$tplPublish = false; } else { $tplPublish = $_POST["tplPublish"]; }
            if(empty($_POST["tplParent"])){$tplParent = false; } else { $tplParent = $_POST["tplParent"]; }
            if(empty($_POST["tplPageParent"])){$tplPageParent = false; } else { $tplPageParent = $_POST["tplPageParent"]; }
            if(empty($_POST["taggerCate"])){$taggerCate = false; } else { $taggerCate = $_POST["taggerCate"]; }


            //get posts
            foreach ($xml->channel->item as $post) {

                //if its not a post, break
                $type = $post->children('wp',true)->post_type;

                $wp = $post->children('wp',true);
                $id = $post->children('wp',true)->post_id;
                $pt =       $mysqli->real_escape_string(parseContent((string)$post->title));
                $it =       $mysqli->real_escape_string(parseContent((string)$post->children('excerpt',true)->encoded));
                $raw_content =  $mysqli->real_escape_string(parseContent((string)$post->children('content',true)->encoded));
                //print_r($raw_content); //debug your conent
                $content = '<p>' . str_replace('\n\n', "</p><p>", $raw_content) . "</p>"; // make paragraphs, including one at the end;
                //print_r($content);//debug your conent
                //exit();//debug your conent
                $alias = $mysqli->real_escape_string(parseContent((string)$post->children('wp',true)->post_name));
                $menuIndex = $post->children('wp',true)->menu_order;
                $template = "";
                $publish = "0";
                $pub_date =  strtotime((string)$post->pubDate); if (empty($pub_date)) {$pub_date = strtotime((string)$wp->post_date);}

                //set tagger categories
                $tagId = "";
                if($taggerCate){
                    //loop through <category domain="category" nicename="functional-spirits"><![CDATA[Functional Spirits]]></category>
                    foreach( $post->category as $c){
                        if($c["domain"] == "category"){$categoryTemp = $c["nicename"];}
                    }
                    //check for tag
                    $tag_query = "SELECT * FROM modx_tagger_tags";
                    if ($tag_result = $mysqli->query($tag_query)) {
                        while ($row = $tag_result->fetch_array()) {
                            //if name matches set it
                            if($row["alias"] == $categoryTemp){
                                $tagId = $row["id"];
                            }
                        }
                    }
                }

                //if Respect Publish setting
                if($tplPublish){
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
                    if($tplMatch){
                        //if its a template Meta Item
                        //<wp:meta_key>_wp_page_template</wp:meta_key>
                        if ($wpm->meta_key == "_wp_page_template") {
                            //<wp:meta_value><![CDATA[default]]></wp:meta_value>
                            $wp_temp = parseContent((string)$wpm->meta_value);

                            //check for automatic match
                            $tpl_query = "SELECT * FROM modx_site_templates";
                            if ($tpl_result = $mysqli->query($tpl_query)) {
                                while ($row = $tpl_result->fetch_array()) {
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
                if(empty($template)){$template = $_POST["tplDefault"];}


                //Start Post Type Custom options

                if($type == "post"){

                    //if Global Parent Set
                    if(empty($tplParent)){
                        $parent = $post->children('wp',true)->post_parent;
                    } else {
                        $parent = $tplParent;
                    }

                    //Insert to DB
                    $insert = "INSERT INTO `modx_site_content` (`id`,`pagetitle`,`introtext`,`content`,`parent`,`template`,`alias`,`menuindex`,`publishedon`,`published`) VALUES ('".$id."','".$pt."','".$it."','".$content."','".$parent."','".$template."','".$alias."','".$menuIndex."','".$pub_date."','".$publish."')";
                    $result = $mysqli->query($insert);
                    if ( $result ) {
                        $postsData[] = $pt;
                        //we are inside matching ID success so we can add Tag now
                        $tagInsert = "INSERT INTO `modx_tagger_tag_resources` (`tag`,`resource`) VALUES ('".$tagId."','".$id."')";
                        $tresult = $mysqli->query($tagInsert);

                    } else {
                        //try again with no ID, it might have been taken by preset
                        $reinsert = "INSERT INTO `modx_site_content` (`pagetitle`,`introtext`,`content`,`parent`,`template`,`alias`,`menuindex,`publishedon`,`published`) VALUES ('".$pt."','".$it."','".$content."','".$parent."','".$template."','".$alias."','".$menuIndex."','".$pub_date."','".$publish."')";
                        $result_two = $mysqli->query($reinsert);
                        $postsDataErrors[] = $pt;
                    }
                }


                //get pages
                //if its not a page, break

                if($type == "page"){

                    //if Global Parent && Separate Page is NOT checked
                    if($tplParent && empty($tplPageParent)){
                        $parent = $tplParent;
                    } else {
                        $parent = $post->children('wp',true)->post_parent;
                    }

                    //Insert to DB
                    $insert = " INSERT INTO `modx_site_content` (`id`,`pagetitle`,`introtext`,`content`,`parent`,`template`,`alias`,`menuindex`,`publishedon`,`published`) VALUES ('".$id."','".$pt."','".$it."','".$content."','".$parent."','".$template."','".$alias."','".$menuIndex."','".$pub_date."','".$publish."')";
                    $result = $mysqli->query($insert);
                    if( $result ) {
                        $pageData[] = $pt;
                    } else {
                        //try again with no ID, it might have been taken by preset
                        $reinsert = "INSERT INTO `modx_site_content` (`pagetitle`,`introtext`,`content`,`parent`,`template`,`alias`,`menuindex`,`publishedon`,`published`) VALUES ('".$pt."','".$it."','".$content."','".$parent."','".$template."','".$alias."','".$menuIndex."','".$pub_date."','".$publish."')";
                        $result_two = $mysqli->query($reinsert);
                        $pageDataErrors[] = $pt;
                        if(!$result_two){printf("%s\n", $mysqli->error);}
                    }
                }

            } //for each item


            return '<p><strong>Imported With No Errors:</strong> Posts: '.count($postsData).' | Pages: '.count($pageData).'</p><p><strong>Errors:</strong> Posts: '.count($postsDataErrors).' | Pages: '.count($pageDataErrors).'</p>';

        }
    }
}
