<?php

class UrlService extends CoreService
{
    public function getLinks($limit = 100)
    {
        $db = self::instance('url');

        $qb = QB::instance('links')
            ->select()
            ->limit($limit);

        return ($db->query($qb->get()));
    }

    public function createLink($displayUrl, $redirectUrl)
    {
        $insert['display_url']  = QB::esc($displayUrl);
        $insert['redirect_url'] = QB::esc($redirectUrl);

        $db = self::instance('url');

        $qb = QB::instance('links')
            ->insert($insert);

        $result = $db->query($qb->get());

        return $result;
    }
}
