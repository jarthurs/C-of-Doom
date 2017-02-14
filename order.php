<?php
@session_start();
  if(!isset($_SESSION['cameraArray'])) {
    $_SESSION['cameraArray'] = array();
  }
  if(isset($_POST['refresh'])){
    require("userInterface.php");
    exit("");
  }
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<style type="text/css">
  /*Style cart Items*/

  /* Main Cart Item Container */
  div.cartItem{

  }
  .button{

  }
  form.extraActions{

  }
  input.extraButton{

  }
</style>

<?php
    $zoom = 0;
    $focus = 0;
    //make sure post was succesful
    if(isset($_POST['type'])){
      //If the cart is empty add the new lens
      if(count($_SESSION['cameraArray']) === 0)
      {//create an array of the posted data
        $postedCamera = array($_POST['cameraName'], $_POST['cameraModel'], $_POST['type'], $focus, $zoom);
        //add one to the focus band quantity of the posted lens if it is a focus band
        if(preg_match('/F/', $postedCamera[2])){
          $postedCamera[3] = 1;
        }
        //add one to the zoom band quantity of the posted lens if it is a zoom band
        elseif(preg_match('/Z/', $postedCamera[2])){
          $postedCamera[4] = 1;
        }
        //add the new array
        $_SESSION['cameraArray'][] = $postedCamera;
      }
      //If the cart has items in it check eazch one to see if the post is a new camera or a duplicate
      else
      {
        //Create and array of the posted camera data
        $postedCamera = array($_POST['cameraName'], $_POST['cameraModel'], $_POST['type'], $focus, $zoom);
        //If the band type is focus
        if(preg_match('/F/', $_POST['type'])){
          $duplicate = false;
          //loop through each camera
          for($ii = 0; $ii < count($_SESSION['cameraArray']); $ii++){
            $aCamera = $_SESSION['cameraArray'][$ii];
            $name = ($aCamera[0]);
            $model = ($aCamera[1]);
            $type = ($aCamera[2]);
            //compare make and model

            if($postedCamera[0] === $name){

              if($postedCamera[1] === $model){
                //Compare type

                if(preg_match('/F/', $type)){
                  //Add one to camera's focus number if it is duplicate focus
                  $_SESSION['cameraArray'][$ii][3] = $_SESSION['cameraArray'][$ii][3] + 1;
                  $duplicate = true;
                }
              }
            }
          }
          //add the new camera if it's not a duplicate
          if(!$duplicate){
            if(preg_match('/F/', $postedCamera[2])){
              //add one to the focus band quantity of the posted lens if it is a focus band
              $postedCamera[3] = 1;
            }
            //add one to the zoom band quantity of the posted lens if it is a zoom band
            elseif(preg_match('/Z/', $postedCamera[2])){
              $postedCamera[4] = 1;
            }
            $_SESSION['cameraArray'][] = $postedCamera;
          }
        } elseif(preg_match('/Z/',$_POST['type'])){
          //same as with focus but zoom bands instead
          $duplicate = false;
          //loop through each camera
          for($ii = 0; $ii < count($_SESSION['cameraArray']); $ii++){
            $aCamera = $_SESSION['cameraArray'][$ii];
            $name = ($aCamera[0]);
            $model = ($aCamera[1]);
            $type = ($aCamera[2]);
            //compare make and model

            if($postedCamera[0] === $name){

              if($postedCamera[1] === $model){
                //Compare type

                if(preg_match('/Z/', $type)){
                  $_SESSION['cameraArray'][$ii][4] = $_SESSION['cameraArray'][$ii][4] + 1;
                  $duplicate = true;
                }
              }
            }
          }
          if(!$duplicate){
            if(preg_match('/F/', $postedCamera[2])){
              $postedCamera[3] = 1;
            }
            //add one to the zoom band quantity of the posted lens if it is a zoom band
            elseif(preg_match('/Z/', $postedCamera[2])){
              $postedCamera[4] = 1;
            }
            $_SESSION['cameraArray'][] = $postedCamera;
          }
        }
      }

    }

    //Create Individual Buttons For Each Camera in the Array for the user to control Individual quantity
    $x = 1;
    foreach(@$_SESSION['cameraArray'] as $camera){
      //Don't display any buttons for items with zero quantity value
      if($camera[3] <= 0 && $camera[4] <= 0){
        continue;
      }
      //format data in order to be able to make dynamic class names and event listeners
      $temp = $camera[1];
      $temp = preg_replace('/\s+/', '', $temp);
      $temp = preg_replace('/\-/', '', $temp);
      $temp = preg_replace('/\//', '', $temp);
      $buttonValue = $camera[0].$temp.$camera[2];
      $buttonValue = preg_replace('/\s+/', '', $buttonValue);
      $buttonValue = preg_replace('/-/', '', $buttonValue);
      $buttonValue = preg_replace('/\//', '', $buttonValue);
      //Parent div for each cart item
      echo "<div class='cartItem'>";
      //Unique div for each cart item
      echo '<div class="'.$x.'">';
      //Generate Div text based off it's a zoom or focus band
      if(preg_match('/F/', $camera[2])){
        echo '<text class="'.$x.'">Make: '.$camera[0].'<br />Model: '.$camera[1].'<br />Type: Focus<br />Quantity: '.$camera[3].'</text><br />';

      } elseif(preg_match('/Z/', $camera[2])){
        echo '<text class="'.$x.'">Make: '.$camera[0].'<br />Model: '.$camera[1].'<br />Type: Zoom<br />Quantity: '.$camera[4].'</text><br />';

      }
      //Create the buttons
      echo '<button value="'.$buttonValue.'" class="'.$x.'minus">-</button>';
      echo '<button value="'.$buttonValue.'" class="'.$x.'plus">+</button>';
      echo '</div>';
      echo "</div>";
      //Create the listeners for each button
      echo '
        <script>
          $("button.'.$x.'minus").click(function() {
            var name = $(this).val();
            var operation = "-";

            //Grab the data for text for each cart item
            $.post("cartUpdate.php",
             { name: name,
               operation: operation
             },
              function(data){
                //update cart html
              $("text.'.$x.'").html(data);
            });
          });

          $("button.'.$x.'plus").click(function() {
            var name = $(this).val();
            var operation = "+";

            //Grab the data for text for each cart item
            $.post("cartUpdate.php",
             { name: name,
               operation: operation
             },
              function(data){
                //update cart html
              $("text.'.$x.'").html(data);
            });
          });
        </script>
      ';
      $x++;
    }

?>
