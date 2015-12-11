<?php

	//Leave all this stuff as it is
	date_default_timezone_set('Europe/London');
	include 'GIFEncoder.class.php';
	include 'php52-fix.php';
	$time = $_GET['time'];
    
    // To understand DD/MM/YYYY+00:00:01 format
    str_replace('/', '-', $time);
    
	$future_date = new DateTime(date('r',strtotime($time)));
	$time_now = time();
	$now = new DateTime(date('r', $time_now));
	$frames = array();	
	$delays = array();


	// Your image link
	$image = imagecreatefrompng('images/preview.png');

	$delay = 100;// milliseconds

	$font = array(
		'size' => 33, // Font size, in pts usually.
		'angle' => 0, // Angle of the text
		'x-offset' => 68, // The larger the number the further the distance from the left hand side, 0 to align to the left.
		'y-offset' => 60, // The vertical alignment, trial and error between 20 and 60.
		'file' => __DIR__ . DIRECTORY_SEPARATOR . 'Futura.ttc', // Font path
		'color' => imagecolorallocate($image, 0, 0, 0), // RGB Colour of the text
	);
	for($i = 0; $i <= 60; $i++){
		
		$interval = date_diff($future_date, $now);
		
		if($future_date < $now){
			// Open the first source image and add the text.
			$image = imagecreatefrompng('images/preview.png');
			$text = $interval->format('00:00:00:00');
			imagettftextSp ($image , $font['size'] , $font['angle'] , $font['x-offset'] , $font['y-offset'] , $font['color'] , $font['file'], $text, 1 );
			ob_start();
			imagegif($image);
			$frames[]=ob_get_contents();
			$delays[]=$delay;
			$loops = 1;
			ob_end_clean();
			break;
		} else {
			// Open the first source image and add the text.
			$image = imagecreatefrompng('images/preview.png');
			$text = $interval->format('0%a:%H:%I:%S');
			imagettftextSp ($image , $font['size'] , $font['angle'] , $font['x-offset'] , $font['y-offset'] , $font['color'] , $font['file'], $text, 1 );
			ob_start();
			imagegif($image);
			$frames[]=ob_get_contents();
			$delays[]=$delay;
			$loops = 0;
			ob_end_clean();
		}

		$now->modify('+1 second');
	}

	//expire this image instantly
	header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
	header( 'Cache-Control: no-store, no-cache, must-revalidate' );
	header( 'Cache-Control: post-check=0, pre-check=0', false );
	header( 'Pragma: no-cache' );
	$gif = new AnimatedGif($frames,$delays,$loops);
	$gif->display();

    /**
     * imagettftext performing letter spacing
     */
    function imagettftextSp($image, $size, $angle, $x, $y, $color, $font, $text, $spacing = 0)
    {        
        if ($spacing == 0)
        {
            imagettftext($image, $size, $angle, $x, $y, $color, $font, $text);
        }
        else
        {
            $temp_x = $x;
            for ($i = 0; $i < strlen($text); $i++)
            {
                $bbox = imagettftext($image, $size, $angle, $temp_x, $y, $color, $font, $text[$i]);
                $temp_x += $spacing + ($bbox[2] - $bbox[0]);
            }
        }
    }
    