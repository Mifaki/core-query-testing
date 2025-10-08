<?php
defined('CORE') or (header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden") and die('403.14 - Access denied.'));

class QBService extends CoreService
{
    /**
     * 3.5.1 Pengukuran Latensi Dasar dan Overhead Inisiasi Koneksi
     */
    public function testScenario1()
    {
        if (! extension_loaded('xhprof')) {
            throw new Exception('XHProf extension is not available');
        }
        xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

        $db = self::instance('fake-e-commerce');
        $qb = QB::instance('users')
            ->select()
            ->where('id', 10)
            ->limit(1);
        $result = $db->query($qb->get());
        // === PROFILED CODE END ===

        $xhprof_data       = xhprof_disable();
        $profiling_results = ExtractXhprofStats::extractStats($xhprof_data);

        return [
            'success'      => true,
            'scenario'     => 'Skenario 1 - Latensi Dasar',
            'description'  => 'Simple SELECT query dengan WHERE clause',
            'database'     => 'fake-e-commerce',
            'result_count' => $result ? count($result) : 0,
            'profiling'    => $profiling_results,
        ];
    }

    /**
     * 3.5.2 Analisis Skalabilitas Alokasi Memori pada Operasi Tulis Massal
     */
    public function testScenario2()
    {
        if (! extension_loaded('xhprof')) {
            throw new Exception('XHProf extension is not available');
        }

        $db = self::instance('fake-e-commerce');

        $validCategoryIds = $this->getValidCategoryIds($db);
        $validUserIds     = $this->getValidUserIds($db);

        if (empty($validCategoryIds) || empty($validUserIds)) {
            throw new Exception('No valid categories or users found for foreign key constraints');
        }

        $batchData       = [];
        $productStatuses = ['draft', 'published', 'discontinued'];

        for ($i = 1; $i <= 1000; $i++) {
            $productName   = "Test Product {$i} - " . $this->generateProductName();
            $slug          = $this->generateSlug($productName, $i);
            $price         = rand(1000, 100000) / 100; // Random price between 10.00 - 1000.00
            $discountPrice = (rand(1, 100) <= 30) ? round($price * (rand(50, 90) / 100), 2) : null;

            $batchData[] = [
                'category_id' => $validCategoryIds[array_rand($validCategoryIds)],
                'user_id'     => $validUserIds[array_rand($validUserIds)],
                'name'        => $productName,
                'slug'        => $slug,
                'description' => "Detailed description for test product {$i}. " . $this->generateDescription(),
                'short_description' => "Short description for test product {$i}",
                'price'          => $price,
                'discount_price' => $discountPrice,
                'sku'            => "TEST-SKU-" . str_pad($i, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(md5(uniqid()), 0, 4)),
                'stock_quantity' => rand(0, 100),
                'weight'         => round(rand(10, 5000) / 100, 2), // 0.1 to 50.0
                'dimensions'     => rand(10, 100) . 'x' . rand(10, 100) . 'x' . rand(5, 50),
                'status'         => $productStatuses[array_rand($productStatuses)],
                'featured'       => (rand(1, 100) <= 20) ? 1 : 0, // 20% chance
                'view_count'     => rand(0, 1000),
                'rating_average' => round(rand(100, 500) / 100, 2), // 1.00 to 5.00
                'rating_count'   => rand(0, 50),
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ];
        }

        xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

        // === PROFILED CODE START ===
        $qb             = QB::instance('products')->inserts($batchData);
        $result         = $db->query($qb->get());
        $inserted_count = $result ? 1000 : 0;
        // === PROFILED CODE END ===

        $xhprof_data       = xhprof_disable();
        $profiling_results = ExtractXhprofStats::extractStats($xhprof_data);

        $this->cleanupTestProducts($batchData);

        return [
            'success'          => true,
            'scenario'         => 'Skenario 2 - Operasi Tulis Massal',
            'description'      => 'Batch INSERT 1000 products dengan data realistis',
            'database'         => 'fake-e-commerce',
            'result_count'     => $inserted_count,
            'valid_categories' => count($validCategoryIds),
            'valid_users'      => count($validUserIds),
            'profiling'        => $profiling_results,
        ];
    }

    /**
     * 3.5.3 Evaluasi Efisiensi Kompilasi Query Kompleks
     */
    public function testScenario3()
    {
        if (! extension_loaded('xhprof')) {
            throw new Exception('XHProf extension is not available');
        }

        xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

        // === PROFILED CODE START ===
        $db = self::instance('fake-e-commerce');

        $qb = QB::instance('products p')
            ->select(
                'p.name',
                'p.price',
                'c.name as category_name',
                'u.username as owner',
                QB::raw('AVG(r.rating) as avg_rating'),
                QB::raw('COUNT(r.id) as review_count'),
                QB::raw('SUM(oi.quantity) as total_sold')
            )
            ->join('categories c', 'p.category_id', 'c.id')
            ->join('users u', 'p.user_id', 'u.id')
            ->leftJoin('reviews r', [
                'p.id' => 'r.product_id',
            ])
            ->leftJoin('order_items oi', 'p.id', 'oi.product_id')
            ->where('p.status', 'published')
            ->where('r.status', 'approved')
            ->groupBy('p.id', 'p.name', 'p.price', 'c.name', 'u.username')
            ->having(QB::OG)
            ->having(QB::raw('AVG(r.rating)'), '>=', 4.0)
            ->having(QB::raw('AVG(r.rating)'), 'IS', QB::raw('NULL'), QB::OR)
            ->having(QB::EG)
            ->orderBy(QB::raw('total_sold'), QB::DESC)
            ->orderBy(QB::raw('avg_rating'), QB::DESC)
            ->limit(100);

        $result = $db->query($qb->get());
        // === PROFILED CODE END ===

        $xhprof_data       = xhprof_disable();
        $profiling_results = ExtractXhprofStats::extractStats($xhprof_data);

        return [
            'success'      => true,
            'scenario'     => 'Skenario 3 - Query Kompleks',
            'description'  => 'Complex query dengan JOIN, aggregation, GROUP BY, HAVING',
            'database'     => 'fake-e-commerce',
            'result_count' => $result ? count($result) : 0,
            'sql_query'    => $qb->get(), // Added for debugging
            'profiling'    => $profiling_results,
        ];

    }

    /**
     * 3.5.4 Analisis Penanganan Query Sub-Seleksi Berkorelasi
     */
    public function testScenario4()
    {
        if (! extension_loaded('xhprof')) {
            throw new Exception('XHProf extension is not available');
        }

        xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

        // === PROFILED CODE START ===
        $db = self::instance('fake-e-commerce');

        $qb = QB::instance('users u')
            ->select(
                'u.username',
                'u.email',
                QB::raw('(SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count'),
                QB::raw('(SELECT SUM(total_amount) FROM orders WHERE user_id = u.id) as total_spent')
            )
            ->where('u.status', 'active')
            ->orderBy(QB::raw('total_spent'), QB::DESC);

        $result = $db->query($qb->get());
        // === PROFILED CODE END ===

        $xhprof_data       = xhprof_disable();
        $profiling_results = ExtractXhprofStats::extractStats($xhprof_data);

        return [
            'success'      => true,
            'scenario'     => 'Skenario 4 - Sub-Query Berkorelasi',
            'description'  => 'Query dengan correlated subqueries',
            'database'     => 'fake-e-commerce',
            'result_count' => $result ? count($result) : 0,
            'profiling'    => $profiling_results,
        ];
    }

    /**
     * 3.5.5 Analisis Degradasi Kinerja pada Paginasi Dalam (Deep Pagination)
     */
    public function testScenario5()
    {
        if (! extension_loaded('xhprof')) {
            throw new Exception('XHProf extension is not available');
        }

        xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

        // === PROFILED CODE START ===
        $db = self::instance('fake-e-commerce');

        // Using proper limit with offset
        $qb = QB::instance('products')
            ->select()
            ->where('status', 'published')
            ->orderBy('created_at', QB::DESC)
            ->limit(1000, 10); // offset, limit

        $result = $db->query($qb->get());
        // === PROFILED CODE END ===

        $xhprof_data       = xhprof_disable();
        $profiling_results = ExtractXhprofStats::extractStats($xhprof_data);

        return [
            'success'      => true,
            'scenario'     => 'Skenario 5 - Paginasi Deep',
            'description'  => 'Deep pagination dengan OFFSET 1000, LIMIT 10',
            'database'     => 'fake-e-commerce',
            'result_count' => $result ? count($result) : 0,
            'profiling'    => $profiling_results,
        ];
    }

    /**
     * Get valid category IDs for foreign key constraints
     */
    private function getValidCategoryIds($db)
    {
        $qb = QB::instance('categories')
            ->select('id')
            ->where('is_active', 1);

        $result = $db->query($qb->get());
        return $result ? array_column($result, 'id') : [];
    }

    /**
     * Get valid user IDs for foreign key constraints
     */
    private function getValidUserIds($db)
    {
        $qb = QB::instance('users')
            ->select('id')
            ->where('status', 'active');

        $result = $db->query($qb->get());
        return $result ? array_column($result, 'id') : [];
    }

    /**
     * Generate a realistic product name
     */
    private function generateProductName()
    {
        $adjectives = ['Premium', 'Deluxe', 'Professional', 'Advanced', 'Ultimate', 'Essential', 'Classic', 'Modern'];
        $products   = ['Smartphone', 'Laptop', 'Headphones', 'Camera', 'Watch', 'Tablet', 'Speaker', 'Monitor'];

        return $adjectives[array_rand($adjectives)] . ' ' . $products[array_rand($products)];
    }

    /**
     * Generate a URL-friendly slug
     */
    private function generateSlug($productName, $id)
    {
        $slug = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '-', $productName));
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug . '-' . $id;
    }

    /**
     * Generate a longer product description
     */
    private function generateDescription()
    {
        $features = [
            'High-quality construction with premium materials.',
            'Advanced technology for superior performance.',
            'Ergonomic design for maximum comfort.',
            'Energy-efficient and environmentally friendly.',
            'Easy to use with intuitive interface.',
            'Durable and built to last.',
            'Compatible with multiple devices and platforms.',
        ];

        $selectedFeatures = array_rand($features, rand(2, 4));
        if (! is_array($selectedFeatures)) {
            $selectedFeatures = [$selectedFeatures];
        }

        $description = '';
        foreach ($selectedFeatures as $key) {
            $description .= $features[$key] . ' ';
        }

        return trim($description);
    }

    private function cleanupTestProducts($batchData = null)
    {
        try {
            $db = self::instance('fake-e-commerce');

            if ($batchData) {
                $skus = array_column($batchData, 'sku');
                if (! empty($skus)) {
                    $qb = QB::instance('products')
                        ->select('id')
                        ->whereIn('sku', $skus);

                    $productResult = $db->query($qb->get());
                    $productIds    = $productResult ? array_column($productResult, 'id') : [];

                    if (! empty($productIds)) {

                        $qb = QB::instance('product_tag_relations')
                            ->delete()
                            ->whereIn('product_id', $productIds);
                        $db->query($qb->get());

                        $qb = QB::instance('reviews')
                            ->delete()
                            ->whereIn('product_id', $productIds);
                        $db->query($qb->get());

                        $qb = QB::instance('order_items')
                            ->delete()
                            ->whereIn('product_id', $productIds);
                        $db->query($qb->get());

                        $qb = QB::instance('products')
                            ->delete()
                            ->whereIn('id', $productIds);
                        $db->query($qb->get());
                    }
                }
            } else {
                $qb = QB::instance('products')
                    ->select('id')
                    ->where('sku', QB::LIKE, 'TEST-SKU-%');

                $productResult = $db->query($qb->get());
                $productIds    = $productResult ? array_column($productResult, 'id') : [];

                if (! empty($productIds)) {
                    $qb = QB::instance('product_tag_relations')
                        ->delete()
                        ->whereIn('product_id', $productIds);
                    $db->query($qb->get());

                    $qb = QB::instance('reviews')
                        ->delete()
                        ->whereIn('product_id', $productIds);
                    $db->query($qb->get());

                    $qb = QB::instance('order_items')
                        ->delete()
                        ->whereIn('product_id', $productIds);
                    $db->query($qb->get());

                    $qb = QB::instance('products')
                        ->delete()
                        ->where('sku', QB::LIKE, 'TEST-SKU-%');
                    $db->query($qb->get());
                }
            }

        } catch (Exception $e) {
            error_log("Cleanup failed: " . $e->getMessage());
        }
    }
}
