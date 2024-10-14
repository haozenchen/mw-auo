<?php
require_once(LIBS.DS.'component_object.php');
class ComBase64urlComponent extends ComponentObject {
    var $controller=true;

    function base64url_encode($data = null) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    function base64url_decode($data = null) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

}
?>
