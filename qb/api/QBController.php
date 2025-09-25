<?php

class QBApiController extends CoreApi
{
    public function createProducts(): void
    {
        $title = $this->postv('title');
        $desc  = $this->postv('desc');

        $service = new QBService();
        try {
            $result = $service->createProducts($title, $desc);
            CoreResult::instance($result)->json();
        } catch (Exception $e) {
            CoreError::instance($e->getMessage())->show();
        }
    }
}
