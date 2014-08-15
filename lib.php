<?php

function block_acclaim_images() {
    
    return array(
	html_writer::tag('img', '', array('alt' => get_string('red', 'block_acclaim'), 'src' => "http://www.texasranger.org/artifacts/Circle%20KB/Antiqued_Brass_badge.jpg")),
        html_writer::tag('img', '', array('alt' => get_string('blue', 'block_acclaim'), 'src' => "http://www.texasranger.org/artifacts/Circle%20KB/Antiqued_Brass_badge.jpg")),
        html_writer::tag('img', '', array('alt' => get_string('green', 'block_acclaim'), 'src' => "http://www.texasranger.org/artifacts/Circle%20KB/Antiqued_Brass_badge.jpg"))
	);

}

?>

