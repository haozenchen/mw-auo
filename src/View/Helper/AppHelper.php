<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

class AppHelper extends Helper {
    public $helpers = ['Html', 'Url'];

    public function safeScript($scriptName) {
        return $this->Html->script($this->Url->build('/js/' . $scriptName . '.js', ['fullBase' => true]));
    }


    public function safeCss($cssName) {
        return $this->Html->css($this->Url->build('/css/' . $cssName . '.css', ['fullBase' => true]));
    }
}

?>