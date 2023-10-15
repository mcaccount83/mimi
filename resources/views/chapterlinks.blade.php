<!DOCTYPE html>
<html lang="en">
<head>
<title>MOMS Club: Chapter Links</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width; initial-scale=1.0">
<link rel="stylesheet" href="{{ asset('css2/style.css') }}" type="text/css" media="screen">
</head>
<body id="page2">
<!--======================== header ===========================-->
<header>
   <div class="main "> 
    <!--======================== logo ============================-->

      <!--======================== menu ============================-->
 
<!--======================== content ===========================-->
<section id="content">
    <div class="container_24">
      <div class="wrapper p10">
        <article class="grid_24">
          
          <p></p>
          <div class="border-horiz2"></div>
          <h5>International Chapters</h5>
          <div class="border-horiz2"></div>
          <div class="wrapper p10">
            <article class="grid_24">
              <div class="wrapper">
            
            <?php
            
                $current_country="";
                $toggle=1;
                
                $row_count=count($link_array_intl);
                for ($row = 0; $row < $row_count; $row++){
                    if($current_country <> $link_array_intl[$row]['country']){  
                        
                        if($current_country<>"")
                            echo "</article>";
                            
                        // set the correct header for a left or right heading, these alternate
                        if($toggle==1){     
                            echo "<article class=\"grid_11 alpha\">\n";
                            $toggle=2;
                        }
                        else{
                            echo "<article class=\"grid_11 omega prefix_1\">\n";
                            $toggle=1;
                        }
                        
                        echo "<h5>";
                        echo $link_array_intl[$row]['country'];
                        echo "</h5>";
                        echo "<p></p>\n";
                        
                        $current_country = $link_array_intl[$row]['country'];
                    }
                    
                    //if the chapter is "OK" and their link has been approved, get their link, otherwise, link to the inquiries form
                    if($link_array_intl[$row]['status']==1 && $link_array_intl[$row]['link_status']==1){
                        $link = $link_array_intl[$row]['url'];
                    }
                    else{
                        $link = "https://momsclub.org/contact-a-chapter/";
                        $link = "https://momsclub.org/contact-a-chapter?ChapterName=MOMS Club of " . $link_array_intl[$row]['name'] . "&WhereChapter=Outside USA" ."&Country=" . $link_array_intl[$row]['country'];

                    }

                    if($link_array_intl[$row]['status']<>4)
                        echo "<p><a href=\"" . $link . "\" class=\"link-4\" target=\"_blank\">MOMS Club of " . $link_array_intl[$row]['name'] . "</a></p>";                     
                }
                
                echo "</article>";

            ?>
            
              </div>
            </article>
          </div>
          <div class="border-horiz2"></div>
          <h5>USA Chapters</h5>
          <div class="border-horiz2"></div>
          <a href="#AL" class="link-1">Alabama</a> | <a href="#AK" class="link-1">Alaska</a> | <a href="#AZ" class="link-1">Arizona</a> | <a href="#AR" class="link-1">Arkansas</a> | <a href="#CA" class="link-1">California</a> | <a href="#CO" class="link-1">Colorado</a> | <a href="#CT" class="link-1">Connecticut</a> | <a href="#DE" class="link-1">Delaware</a> | <a href="#DC" class="link-1">District of Columbia </a> | <a href="#FL" class="link-1">Florida</a> | <a href="#GA" class="link-1">Georgia</a> | <a href="#HI" class="link-1">Hawaii</a> | <a href="#ID" class="link-1">Idaho</a> | <a href="#IL" class="link-1">Illinois</a> | <a href="#IN" class="link-1">Indiana</a> | <a href="#IA" class="link-1">Iowa</a> | <a href="#KS" class="link-1">Kansas</a> | <a href="#KY" class="link-1">Kentucky</a> | <a href="#LA" class="link-1">Louisiana</a> | <a href="#ME" class="link-1">Maine</a> | <a href="#MD" class="link-1">Maryland</a> | <a href="#MA" class="link-1">Massachusetts</a> | <a href="#MI" class="link-1">Michigan</a> | <a href="#MN" class="link-1">Minnesota</a> | <a href="#MS" class="link-1">Mississippi</a> | <a href="#MO" class="link-1">Missouri</a> | <a href="#MT" class="link-1">Montana</a> | <a href="#NE" class="link-1">Nebraska</a> | <a href="#NV" class="link-1">Nevada</a> | <a href="#NH" class="link-1">New Hampshire</a> | <a href="#NJ" class="link-1">New Jersey</a> | <a href="#NM" class="link-1">New Mexico</a> | <a href="#NY" class="link-1">New York</a> | <a href="#NC" class="link-1">North Carolina</a> | <a href="#ND" class="link-1">North Dakota</a> | <a href="#OH" class="link-1">Ohio</a> | <a href="#OK" class="link-1">Oklahoma</a> | <a href="#OR" class="link-1">Oregon</a> | <a href="#PA" class="link-1">Pennsylvania</a> | <a href="#RI" class="link-1">Rhode Island</a> | <a href="#SC" class="link-1">South Carolina</a> | <a href="#SD" class="link-1">South Dakota</a> | <a href="#TN" class="link-1">Tennessee</a> | <a href="#TX" class="link-1">Texas</a> | <a href="#UT" class="link-1">Utah</a> | <a href="#VT" class="link-1">Vermont</a> | <a href="#VA" class="link-1">Virginia</a> | <a href="#WA" class="link-1">Washington</a> | <a href="#WV" class="link-1">West Virginia</a> | <a href="#WI" class="link-1">Wisconsin</a> | <a href="#WY" class="link-1">Wyoming</a>
          <div class="border-horiz2"></div>
          <div class="wrapper p10">
            
            <?php
            
                $current_state=0;
                $toggle=1;
                
                $row_count=count($link_array_usa);
                for ($row = 0; $row < $row_count; $row++){
                    if($current_state <> $link_array_usa[$row]['state']){   
                        
                        if($current_state<>0)
                            echo "</article>\n";
                            
                        // set the correct header for a left or right heading, these alternate
                        if($toggle==1){     
                            if($current_state<>0){
                                echo "</div>\n";
                                echo "<div class=\"border-horiz2\"></div>";                             
                            }
                            
                            echo "<div class=\"wrapper p17\">";
                            echo "<article class=\"grid_11 alpha\"> <a name=\"" . $link_array_usa[$row]['state_abr'] . "\" class=\"link-5\"></a>\n";
                            $toggle=2;
                        }
                        else{
                            echo "<article class=\"grid_11 omega prefix_1\"> <a name=\"" . $link_array_usa[$row]['state_abr'] . "\" class=\"link-5\"></a>\n";
                            $toggle=1;
                        }

                        echo "<h5>";
                        echo $link_array_usa[$row]['state_name'];
                        echo "</h5>\n";
                        echo "<p></p>\n";
                        
                        $current_state = $link_array_usa[$row]['state'];
                    }
                    
                    //if the chapter is "OK" and their link has been approved, get their link, otherwise, link to the inquiries form
                    if($link_array_usa[$row]['status']==1 && $link_array_usa[$row]['link_status']==1){
                        $link = $link_array_usa[$row]['url'];
                    }
                    else{
                        $link = "https://momsclub.org/contact-a-chapter?ChapterName=MOMS Club of " . $link_array_usa[$row]['name'] . "&WhereChapter=" . $link_array_usa[$row]['state_name'];
                    }
                    if($link_array_usa[$row]['status']<>4)
                        echo "<p><a href=\"" . $link . "\" class=\"link-4\" target=\"_blank\">MOMS Club of " . $link_array_usa[$row]['name'] . "</a></p>\n";                    
                }
                
                echo "</article>";

            ?>
            
            
          </div>
        </article>
      </div>
  </div>
</section>
<!--======================== footer ===========================-->
</body>
</html>