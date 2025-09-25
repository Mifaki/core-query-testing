<?php

class HomeController extends CoreController
{
    public function index()
    {

        $service = new QBService();
        $post    = $service->getProducts();

        $this->ui->useCoreLib('core-ui');
        $this->ui->usePlugin('bootstrap');
        $this->ui->useStyle('css/bootstrap.css');
        $this->ui->view('home.php', ['posts' => $post]);
    }
}
