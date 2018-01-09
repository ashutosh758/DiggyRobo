<?php 
    if( !class_exists('botClass') ) :
        class botClass {
            private $x, $y, $running ,$walk, $count_walk, $direction, $status;
            public $command, $debug = false;

            public function __construct(){
             
            }

            public function init(){
                
                $this->reset();
                $this->validate();
                $this->process();
                $this->response();
                $this->loop();
            }

            private function reset(){

                $this->command = strtoupper($this->command);
                $this->walk = 0;
                $this->x = 0;
                $this->y = 0;    
                $this->direction = "North";
                $this->running = 'Stop';
                $this->count_walk = 0;
                $this->status = 1;
                $this->exit = false;
                $this->validate = false;
            }

            private function validate(){

                if( $this->command != '' ) :
                    if( !( preg_match("/^[LRW0-9]+$/", $this->command) == 1 ) ) :
                        if( $this->debug ) :
                            echo "Invalid character.\n"; 
                        endif;
                    else:
                        if( preg_match_all('/W+/', $this->command, $matches_walk) ) :
                            if( preg_match_all('/\d+/', $this->command, $matches_count_walk) ) :
                                $this->count_walk = $matches_count_walk[0];
                                if( count($matches_walk[0]) == count($this->count_walk)  ) :
                                    $this->validate = true;
                                else :
                                    if( $this->debug ) :
                                        echo "Error type walk but specify number step.\n";
                                    endif;
                                    
                                endif;
                            else :
                                if( $this->debug ) :
                                    echo "Error type walk but specify number step.\n";
                                endif;
                            endif;
                        elseif( preg_match_all('/\d+/', $this->command) ):
                            if( $this->debug ) :
                                echo "Error type number step but specify walk.\n";
                            endif;
                        else :
                            $this->validate = true;
                        endif;
                    endif; 
                else :
                    $this->validate = true;
                endif;
            }

            private function process(){

                if( $this->validate ) :
                    if( $this->debug ) :
                        echo "From Command : ".$this->command."\n";
                       
                    endif;

                    foreach ( str_split($this->command) as $state_value) :
                        if ( in_array($state_value, ['R', 'L']) ) :
                            $this->running = $state_value;
                            $this->direction($state_value);

                        elseif( $state_value == "W" ):
                            $this->walk();
                        endif;
                    endforeach;
                endif;
            }

            private function direction(){
                if( $this->debug ) :
                    echo $this->status.".) Turne ".$this->running." Move Direction From ".$this->direction;
                endif;

                if( $this->running === "R" ) :
                    switch ($this->direction) :
                        case 'North':
                            $this->direction = 'East';
                            break;
                        case 'East':
                            $this->direction = 'South';
                            break;
                        case 'South':
                            $this->direction = 'West';
                            break;
                        case 'West':
                            $this->direction = 'North';
                            break;
                        default:
                            break;
                    endswitch;
                else :
                    switch ($this->direction) :
                        case 'North':
                            $this->direction = 'West';
                            break;
                        case 'East':
                            $this->direction = 'North';
                            break;
                        case 'South':
                            $this->direction = 'East';
                            break;
                        case 'West':
                            $this->direction = 'South';
                            break;
                        default:
                            break;
                    endswitch;
                endif;
                if( $this->debug ) :
                    echo " to ".$this->direction."\n";
                endif;
                $this->status += 1;
            }

            private function walk(){
                switch ($this->direction) :
                    case 'North':
                        $this->y += $this->count_walk[$this->walk];
                        break;
                    case 'East':
                        $this->x += $this->count_walk[$this->walk];
                        break;
                    case 'South':
                        $this->y -= $this->count_walk[$this->walk];
                        break;
                    case 'West':
                        $this->x -= $this->count_walk[$this->walk];
                        break;
                    default:
                        break;
                endswitch;
                if( $this->debug ) :
                    echo $this->status.".) Walk ".$this->count_walk[$this->walk]." Step \n";
                endif;
                $this->walk += 1;
                $this->status += 1;
            }

            private function response(){
                if( $this->validate ) :
                    echo "\nX : ".$this->x.' Y : '.$this->y." \n";
                    echo 'Direction : '.$this->direction."\n";
                else :
                    echo "This command is syntax error.\n";
                endif;
            }

            private function loop(){
                do {
                    print "\n exit : ";
                    print "\nCommand : ";
                    $command = trim(fgets(STDIN));

                    if( $command == "exit" ) :
                        
                        $this->exit = true;
                        exit(0);
                    endif;
                    $this->command = strtoupper($command);
                    $this->init();
                } while(!$this->exit);
            }
        }
    endif; 
    
    $objBot = new botClass();
    $objBot->command = isset($argv[1]) ? $argv[1] : '';
    $objBot->debug = (!getopt("d", [], null)) ? true : false;
    $objBot->init();

?>

    
