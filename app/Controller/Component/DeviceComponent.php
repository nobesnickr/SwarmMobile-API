<?php

App::uses('APIComponent', 'Controller/Component');

class NetworkComponent extends APIComponent {
    public function visitorEvent(){        
        if(!empty($_POST)){
            $file = __DIR__.'/../../../raw/'.date('Y_m_d_h_i_s_').uniqid();
            $content = var_export($_POST);
            file_put_contents($file, $content);
        }
    }
}