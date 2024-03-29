<?php
if(isset($_POST) && $_POST) print_r($_POST);
define('MAPFONT', 'arialbd.ttf');
$background = imagecreatefromgif('samplemap.gif');
$red = imagecolorallocate($background, 255, 0, 0);
$white = imagecolorallocate($background, 255, 255, 255);
$black = imagecolorallocate($background, 0, 0, 0);

$data = file('mapdata.txt');
foreach($data as $line) {
	$t = explode("\t", $line);
	$coords[] = array('label_points'=>intval($t[4]), 'x'=>intval($t[1]), 'y'=>intval($t[2]), 'label_angle'=>intval($t[3]), 'label'=>$t[0]);
}
# echo '<pre>'; print_r($coords); echo '</pre>'; die;
foreach($coords as $n => $row) {
	printit($row['label'], $background, $row, $black);
	imagefilledellipse ($background, $row['x'], $row['y'], 8, 8, $red);
}
# create the image file to display
imagejpeg($background, 'samplemap.jpg', 70);

echo '<form method="POST" style="margin-top:0px" name="myform" action="samplemap.php">';
echo '<input type="image" name="map" src="samplemap.jpg" style="margin-top:0px; cursor:crosshair;"/>';
echo '</form>';

echo '<pre>';
echo htmlentities(file_get_contents('samplemap.php'));
echo '</pre>';

function calculateTextBox($fontSize,$fontAngle,$fontFile,$text) { 
    /************ 
    simple function that calculates the *exact* bounding box (single pixel precision). 
    The function returns an associative array with these keys: 
    left, top:  coordinates you will pass to imagettftext 
    width, height: dimension of the image you have to create 
    *************/ 
    $rect = imagettfbbox($fontSize,$fontAngle,$fontFile,$text); 
    $minX = min(array($rect[0],$rect[2],$rect[4],$rect[6])); 
    $maxX = max(array($rect[0],$rect[2],$rect[4],$rect[6])); 
    $minY = min(array($rect[1],$rect[3],$rect[5],$rect[7])); 
    $maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7])); 
    
    return array( 
     "left"   => abs($minX) - 1, 
     "top"    => abs($minY) - 1, 
     "width"  => $maxX - $minX, 
     "height" => $maxY - $minY, 
     "box"    => $rect 
    ); 
} 

function printit($label, $background, $row, $color, $justify='C') {
    # justify would be center, left, right
    $font = MAPFONT;
    putenv('GDFONTPATH=' . realpath('.')); # see http://www.php.net/manual/en/function.imagettftext.php
    if($label == '') $label = '??';
    $size = $row['label_points']; if($size==0) $size='20';
    $angle = $row['label_angle'];
    
    if($angle < 0){
        $angle = 360 - ($angle*-1); //convert clockwise rotation to anti-clockwise
    }
    $angle = $angle % 360;  //for text rotation revolution is a cycle
    $y = $row['y'];
    $x = $row['x'];
   
    # rotate cartesian coordinates calculator: http://keisan.casio.com/exec/system/1223522781
   
    if($angle<=90) {
        $bbox = calculateTextBox($size, $angle, $font, $label);
        $height = $bbox['height'];
        $width = $bbox['width'];
        
        $temp=imagettftext($background, $size, $angle, ($x-($width/2))+($angle/4),  $y+($height/2), $color, $font, $label);
        if($temp===false) imagestring($background, 5, 50, 50, 'error', $color);
    }
    elseif ($angle<=180) {
        $bbox = calculateTextBox($size, $angle, $font, $label);
        $height = $bbox['height'];
        $width = $bbox['width'];
        $temp=imagettftext($background, $size, $angle, ($x+($width/2)),  $y+($height/2)-5, $color, $font, $label);
 
        // $temp=imagettftext($background, $size, $angle, ($x-($height)),  $y+($width/2), $color, $font, $label);
        if($temp===false) imagestring($background, 5, 50, 50, 'error', $color);
    }
    elseif ($angle<=270) {
        $bbox = calculateTextBox($size, $angle, $font, $label);
        $height = $bbox['height'];
        $width = $bbox['width'];
        $temp=imagettftext($background, $size, $angle, ($x-($width/2))+ (270-$angle),  $y-($height/2), $color, $font, $label);
 
        // $temp=imagettftext($background, $size, $angle, ($x-($height)),  $y+($width/2), $color, $font, $label);
        if($temp===false) imagestring($background, 5, 50, 50, 'error', $color);
    }
    else {
        $bbox = calculateTextBox($size, $angle, $font, $label);
        $height = $bbox['height'];
        $width = $bbox['width'];
        $temp=imagettftext($background, $size, $angle, ($x-($width/2))+2,  $y-($height/2)+($angle/25), $color, $font, $label);
 
        // $temp=imagettftext($background, $size, $angle, ($x-($height)),  $y+($width/2), $color, $font, $label);
        if($temp===false) imagestring($background, 5, 50, 50, 'error', $color);
    }
    // if($temp===false) imagestring($background, 5, 50, 50, 'error', $color);
}
?>
