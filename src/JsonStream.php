<?php
namespace wrossmann\json_stream;

class JsonStream {
    
    protected $output_handle;
    protected $json_flags;
    
    public function __construct($output_handle=NULL, $json_flags = 0) {
        $this->output_handle = $output_handle ?? fopen('php://stdout', 'wb');
        $this->json_flags = $json_flags;
    }
    
    protected function write($output) {
        fwrite($this->output_handle, $output);
    }
    
    protected function is_json_array($obj) {
        // arrays must be sequentially indexed from 0
        $keys = array_keys($obj);
        for( $i=0,$c=count($keys); $i<$c; ++$i ) {
            if( $keys[$i] != $i ) {
                return false;
            }
        }
        return true;
    }
    
    protected function encode_json_array($obj) {
        $this->write('[');
        $c = count($obj) - 1;
        $i = 0;
        foreach( $obj as $value ) {
            $this->manual_encode($value);
            if( $c != $i++ ) {
                $this->write(',');
            }
        }
        $this->write(']');
    }
    
    protected function encode_json_object($obj) {
        // convert objects to array
        if( $obj instanceof \JsonSerializable ) {
            $obj = $obj->jsonSerialize($obj);
        } else if( ! is_array($obj) ) {
            $obj = (array)$obj;
        }
        $this->write('{');
        $c = count($obj) - 1;
        $i = 0;
        foreach( $obj as $key => $value ) {
            // object keys forced to string
            printf('%s:', json_encode((string)$key, $this->json_flags));
            $this->manual_encode($value);
            if( $c != $i++ ) {
                $this->write(',');
            }
        }
        $this->write('}');
    }
    
    public function manual_encode($obj) {
        switch( gettype($obj) ) {
            // pass simple types off to json_encode()
            case 'string':
            case 'double':
            case 'integer':
            case 'boolean':
            case 'NULL':
                $this->write(json_encode($obj, $this->json_flags));
                break;
            // complex types get recursively processed
            case 'array':
                if( $this->is_json_array($obj) ) {
                    $this->encode_json_array($obj);
                } else {
                    $this->encode_json_object($obj);
                }
                break;
            case 'object':
                $this->encode_json_object($obj);
                break;
            default:
                throw new \Exception( "Unhandled objcet type: ".gettype($obj) );
        }
    }
}

