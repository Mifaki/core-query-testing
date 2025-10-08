<?php

defined('CORE') or (header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden") and die('403.14 - Access denied.'));
defined('CORE') or die();

class UrlApiController extends CoreApi
{
    public function create()
    {
        $displayUrl  = $this->postv('display_url');
        $redirectUrl = $this->postv('redirect_url');

        try {
            $service = new UrlService();
            $result  = $service->createLink($displayUrl, $redirectUrl);
            CoreResult::instance($result)->show();
        } catch (Exception $e) {
            CoreError::instance($e->getMessage())->show();
        }
    }
}
