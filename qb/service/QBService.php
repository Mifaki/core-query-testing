<?php

class QBService extends CoreService
{
    public function getProducts($limit = 100)
    {
        $db = self::instance('fake-e-commerce');

        $qb = QB::instance('products')
            ->select()
            ->limit($limit);

        return ($db->query($qb->get()));
    }

    public function createProducts($title, $desc, )
    {
        try {
            $db = self::instance('fake-e-commerce');

            $insert['title'] = QB::esc($title);
            $insert['desc']  = QB::esc($desc);

            $qb = QB::instance('post')->insert($insert);
            $db->query($qb->get());
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
