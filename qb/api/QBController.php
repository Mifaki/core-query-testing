<?php

class QBApiController extends CoreApi
{
    public function createPost(): void
    {
        $title = $this->postv('title');
        $desc  = $this->postv('desc');

        $service = new QBService();
        try {
            $result = $service->createPost($title, $desc);
            CoreResult::instance($result)->json();
        } catch (Exception $e) {
            CoreError::instance($e->getMessage())->show();
        }
    }
}
