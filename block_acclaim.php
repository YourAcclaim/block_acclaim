<?php
class block_acclaim extends block_base{
    public function init(){
        $this->title = get_string('acclaim', 'block_acclaim');
    }

    public function get_content(){
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content         =  new stdClass;
        $this->content->text   = 'The content of our Acclaim block!';
        $this->content->footer = 'Footer here...';
                 
        return $this->content;
    }
            
}
