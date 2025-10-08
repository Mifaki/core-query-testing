<?php
class HomeController extends CoreController
{
    public function index()
    {
        $result = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['scenario'])) {
            $scenario = $_POST['scenario'];

            try {
                $service = new QBService();

                switch ($scenario) {
                    case 'scenario1':
                        $result = $service->testScenario1();
                        break;
                    case 'scenario2':
                        $result = $service->testScenario2();
                        break;
                    case 'scenario3':
                        $result = $service->testScenario3();
                        break;
                    case 'scenario4':
                        $result = $service->testScenario4();
                        break;
                    case 'scenario5':
                        $result = $service->testScenario5();
                        break;
                    default:
                        $result = [
                            'success'     => false,
                            'scenario'    => 'Unknown Scenario',
                            'description' => 'Scenario tidak dikenal',
                            'error'       => 'Invalid scenario parameter',
                        ];
                }
            } catch (Exception $e) {
                $result = [
                    'success'     => false,
                    'scenario'    => 'Error',
                    'description' => 'Test gagal dijalankan',
                    'error'       => $e->getMessage(),
                ];
            }
        }

        $this->ui->useCoreLib('core-ui');
        $this->ui->usePlugin('bootstrap');
        $this->ui->useStyle('css/bootstrap.css');
        $this->ui->useStyle('css/main.css');
        $this->ui->useScript('js/main.js');

        $this->ui->view('home.php', [
            'result' => $result,
        ]);

    }
}
