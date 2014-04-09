<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nttuyen
 * Date: 4/9/14
 * Time: 9:28 PM
 * To change this template use File | Settings | File Templates.
 */

class JSONDocument extends JDocumentJSON {

    private $status = 400;
    private $message = 'Sorry, we can not process your request';
    private $headers = array();
    private $data = array();

    public function __construct() {}

    private static $instance = NULL;
    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new JSONDocument();
        }
        return self::$instance;
    }

    public function setStatus($status) {
        if(is_int($status)) {
            $this->status = (int)$status;
        }
    }
    public function setMessage($message) {
        $this->message = $message;
    }
    public function addHeader($name, $value) {
        if(empty($name)) {
            return;
        }
        $this->headers[$name] = $value;
    }
    public function setData($data) {
        $this->data = $data;
    }

    public function render($cache = false, $params = array()) {
        //. Override render method of JDocumentJSON
        $app = JFactory::getApplication();
        $app->allowCache(false);

        if ($mdate = $this->getModifiedDate()) {
            $app->modifiedDate = $mdate;
        }

        $app->mimeType = $this->_mime;
        $app->charSet  = $this->_charset;

        $app->setHeader('status', $this->status);
        $app->setHeader('message', $this->message);
        foreach($this->headers as $key => $value) {
            $app->setHeader($key, $value);
        }

        $body = $this->data ? json_encode($this->data) : '';
        $app->setBody($body);
        return $body;
    }
}