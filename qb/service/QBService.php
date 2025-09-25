<?php
class QBService extends CoreService
{
    public function getProducts($limit = 100)
    {
        xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

        $db = self::instance('fake-e-commerce');
        $qb = QB::instance('products')
            ->select()
            ->limit($limit);

        $products = $db->query($qb->get());

        $xhprof_data = xhprof_disable();

        $profiling_results = ExtractXhprofStats::extractStats($xhprof_data);
        $summary           = ExtractXhprofStats::getSummary($profiling_results);
        error_log("Profiling Summary: " . json_encode($summary));

        return [
            'products'  => $products,
            'profiling' => $profiling_results,
        ];
    }

    public function createProducts($title, $desc)
    {
        try {
            $db              = self::instance('fake-e-commerce');
            $insert['title'] = QB::esc($title);
            $insert['desc']  = QB::esc($desc);
            $qb              = QB::instance('post')->insert($insert);
            $db->query($qb->get());
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
