<?php $this->view('head.php', null, CoreView::CORE); ?>

<div class="container">
    <div class="row m-5">
        <div class="col">
            <h3 class="border-bottom pb-3">
                <i class="bi bi-speedometer mx-2 text-primary"></i> Home
            </h3>

            <?php if (isset($profiling) && $profiling): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-graph-up"></i> Performance Profiling
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php
                                $stats = $profiling['xhprof_stats'];
                            ?>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="text-primary"><?php echo $stats['total_wall_time_ms']; ?> ms</h5>
                                            <small class="text-muted">Total Execution Time</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="text-success"><?php echo $stats['peak_memory_kb']; ?> KB</h5>
                                            <small class="text-muted">Peak Memory Usage</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="text-warning"><?php echo $stats['unique_functions']; ?></h5>
                                            <small class="text-muted">Functions Called</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="text-danger"><?php echo $stats['total_function_calls']; ?></h5>
                                            <small class="text-muted">Total Function Calls</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detailed Stats Collapsible -->
                            <div class="mt-3">
                                <button class="btn btn-outline-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#detailedStats" aria-expanded="false">
                                    <i class="bi bi-chevron-down"></i> Show Detailed Stats
                                </button>
                                <div class="collapse mt-3" id="detailedStats">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-muted">Timing Details</h6>
                                            <ul class="list-unstyled">
                                                <li><strong>Max Wall Time:</strong>                                                                                                                                                                                                                                                                                                                                             <?php echo $stats['max_wall_time_ms']; ?> ms</li>
                                                <li><strong>Avg Wall Time per Function:</strong>                                                                                                                                                                                                                                                                                                                                                                                                 <?php echo $stats['avg_wall_time_ms']; ?> ms</li>
                                                <li><strong>Total Wall Time:</strong>                                                                                                                                                                                                                                                                                                                                                     <?php echo $stats['total_wall_time_microsec']; ?> μs</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-muted">Memory Details</h6>
                                            <ul class="list-unstyled">
                                                <li><strong>Total Memory:</strong>                                                                                                                                                                                                                                                                                                                                         <?php echo $stats['total_memory_kb']; ?> KB</li>
                                                <li><strong>Peak Memory:</strong>                                                                                                                                                                                                                                                                                                                                     <?php echo $stats['peak_memory_mb']; ?> MB</li>
                                                <li><strong>Avg Memory per Function:</strong>                                                                                                                                                                                                                                                                                                                                                                                     <?php echo $stats['avg_memory_kb']; ?> KB</li>
                                                <li><strong>Avg Calls per Function:</strong>                                                                                                                                                                                                                                                                                                                                                                                 <?php echo $stats['avg_calls_per_function']; ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Products Display -->
            <?php if ($posts): ?>
            <div class="row">
                <div class="col-12 mb-3">
                    <h4 class="text-muted">Products (<?php echo count($posts); ?> items)</h4>
                </div>
                <?php foreach ($posts as $post): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars(is_object($post) ? ($post->name ?? 'No Title') : ($post['name'] ?? 'No Title')) ?>
                            </h5>
                            <p class="card-text">
                                <?php echo htmlspecialchars(is_object($post) ? ($post->description ?? 'No Description') : ($post['description'] ?? 'No Description')) ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="alert alert-info">No posts found</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $this->view('foot.php', null, CoreView::CORE); ?>