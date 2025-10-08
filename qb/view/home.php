<?php $this->view('head.php', null, CoreView::CORE); ?>

<div class="container">
    <div class="header">
        <h1>📊 Analisis Performance Query Builder CORE</h1>
    </div>

    <form method="POST" action="">
        <button id="btn-scenario-1" type="submit" name="scenario" value="scenario1" class="test-button">
            🔍 Skenario 1 - Latensi Dasar(Simple SELECTQuery)
        </button>

        <button id="btn-scenario-2" type="submit" name="scenario" value="scenario2" class="test-button">
            📝 Skenario 2 - Operasi TulisMassal(Batch INSERT1000records);
        </button>

        <button id="btn-scenario-3" type="submit" name="scenario" value="scenario3" class="test-button">
            🔗 Skenario 3 - Query Kompleks(JOIN, GROUP BY, HAVING);
        </button>

        <button id="btn-scenario-4" type="submit" name="scenario" value="scenario4" class="test-button">
            🔄 Skenario 4 - Sub - Query Berkorelasi(Correlated Subqueries);
        </button>

        <button id="btn-scenario-5" type="submit" name="scenario" value="scenario5" class="test-button">
            📄 Skenario 5 - Paginasi Deep(Deep PaginationOFFSET1000);
        </button>
    </form>

    <?php if (isset($result)): ?>
    <div id="result" class="result-<?php echo $result['success'] ? 'success' : 'error'; ?>" style="display: block;">
        <div class="result-header">
            <h3><?php echo htmlspecialchars($result['scenario']); ?></h3>
            <p class="result-description"><?php echo htmlspecialchars($result['description']); ?></p>
        </div>

        <?php if ($result['success'] && isset($result['profiling'])): ?>
            <div class="result-info">
                <span class="info-item">
                    <strong>Database:</strong>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       <?php echo htmlspecialchars($result['database']); ?>
                </span>
                <span class="info-separator">|</span>
                <span class="info-item">
                    <strong>Records:</strong>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  <?php echo $result['result_count']; ?>
                </span>
            </div>

            <?php
                $stats = $result['profiling']['xhprof_stats'];
            ?>
            <div class="xhprof-section">
                <h4>Hasil Analisis XHProf Performance - CORE Framework</h4>

                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-label">Total Execution Time</div>
                        <div id="xhprof-total-execution-time" class="metric-value"><?php echo $stats['total_execution_time_ms']; ?> ms</div>
                        <div class="metric-secondary"><?php echo $stats['total_execution_time_microsec']; ?> μs</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Peak Memory Usage</div>
                        <div id="xhprof-total-memory-usage" class="metric-value"><?php echo $stats['peak_memory_mb']; ?> MB</div>
                        <div class="metric-secondary"><?php echo $stats['peak_memory_kb']; ?> KB</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Total Memory Used</div>
                        <div class="metric-value"><?php echo $stats['total_memory_used_mb']; ?> MB</div>
                        <div class="metric-secondary"><?php echo $stats['total_memory_used_kb']; ?> KB</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Function Calls Analysis</div>
                        <div class="metric-value"><?php echo $stats['total_function_calls']; ?></div>
                        <div class="metric-secondary">Total Function Calls</div>
                    </div>
                </div>

                <div class="efficiency-metrics">
                    <div class="efficiency-card">
                        <div class="efficiency-title">Database Functions</div>
                        <div class="efficiency-value"><?php echo $stats['database_related_functions'] ?? 0; ?></div>
                    </div>
                    <div class="efficiency-card">
                        <div class="efficiency-title">CORE Functions</div>
                        <div class="efficiency-value"><?php echo $stats['core_framework_functions'] ?? 0; ?></div>
                    </div>
                    <div class="efficiency-card">
                        <div class="efficiency-title">PHP Core Functions</div>
                        <div class="efficiency-value"><?php echo $stats['php_core_functions'] ?? 0; ?></div>
                    </div>
                    <div class="efficiency-card">
                        <div class="efficiency-title">Memory Efficiency</div>
                        <div class="efficiency-value"><?php echo $stats['memory_efficiency'] ?? 0; ?> B/op</div>
                    </div>
                    <div class="efficiency-card">
                        <div class="efficiency-title">Time Efficiency</div>
                        <div class="efficiency-value"><?php echo $stats['time_efficiency'] ?? 0; ?> μs/op</div>
                    </div>
                </div>

                <div class="analysis-section">
                    <div class="analysis-title">
                        Analisis Performance untuk Skripsi - CORE Framework
                    </div>
                    <div class="analysis-grid">
                        <div class="analysis-item">
                            <span class="highlight">Peak Memory:</span>
                            <span class="value"><?php echo $stats['peak_memory_mb']; ?> MB</span>
                            <small>Penggunaan memori tertinggi selama eksekusi</small>
                        </div>
                        <div class="analysis-item">
                            <span class="highlight">Execution Time:</span>
                            <span class="value"><?php echo $stats['total_execution_time_ms']; ?> ms</span>
                            <small>Total waktu eksekusi dari main() function</small>
                        </div>
                        <div class="analysis-item">
                            <span class="highlight">Database Operations:</span>
                            <span class="value"><?php echo $stats['database_related_functions'] ?? 0; ?> functions</span>
                            <small>Operasi yang berhubungan dengan database</small>
                        </div>
                        <div class="analysis-item">
                            <span class="highlight">Framework Overhead:</span>
                            <span class="value"><?php echo $stats['core_framework_functions'] ?? 0; ?> functions</span>
                            <small>Overhead dari CORE framework</small>
                        </div>
                        <div class="analysis-item">
                            <span class="highlight">Memory per DB Operation:</span>
                            <span class="value"><?php echo $stats['memory_efficiency'] ?? 0; ?> bytes/op</span>
                            <small>Efisiensi memori per operasi database</small>
                        </div>
                        <div class="analysis-item">
                            <span class="highlight">Time per DB Operation:</span>
                            <span class="value"><?php echo $stats['time_efficiency'] ?? 0; ?> μs/op</span>
                            <small>Efisiensi waktu per operasi database</small>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($result['success']): ?>
            <div class="no-profiling-data">
                <h4>Test Completed Successfully</h4>
                <p><strong>Database:</strong>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  <?php echo htmlspecialchars($result['database']); ?></p>
                <p><strong>Records:</strong>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             <?php echo $result['result_count']; ?></p>
                <div class="alert alert-warning">
                    Profiling data tidak tersedia. Pastikan XHProf extension terinstall dan berfungsi dengan baik.
                </div>
            </div>

        <?php else: ?>
            <div class="error-section">
                <h4>Test Failed</h4>
                <div class="error-message">
                    <?php echo htmlspecialchars($result['error'] ?? 'Unknown error occurred'); ?>
                </div>
                <div class="error-details">
                    <strong>Troubleshooting:</strong>
                    <ul>
                        <li>Pastikan XHProf extension sudah terinstall</li>
                        <li>Periksa koneksi database</li>
                        <li>Cek log error PHP untuk detail lebih lanjut</li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php $this->view('foot.php', null, CoreView::CORE); ?>