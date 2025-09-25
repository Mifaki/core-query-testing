<?php
class HomeController extends CoreController
{
    public function index()
    {
        $service = new QBService();
        $result  = $service->getProducts();

        $posts     = $result['products'];
        $profiling = $result['profiling'];

        $this->ui->useCoreLib('core-ui');
        $this->ui->usePlugin('bootstrap');
        $this->ui->useStyle('css/bootstrap.css');

        $this->ui->view('home.php', [
            'posts'     => $posts,
            'profiling' => $profiling,
        ]);
    }
}
