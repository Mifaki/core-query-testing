<?php


class HomeController extends CoreController {
  public function index() {

    $service = new UrlService();
    $links = $service->getLinks();

    $this->ui->useCoreLib('core-ui');
    $this->ui->usePlugin('bootstrap');
    $this->ui->useStyle('css/bootstrap.css'); 
    $this->ui->view('home.php', ['links' => $links]);
  }
}