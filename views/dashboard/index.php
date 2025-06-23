<?php 
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Tableau de Bord</h1>
        <p class="text-gray-600">Bienvenue, <?= htmlspecialchars($user['first_name'] ?? $user['email']) ?></p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-qrcode text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total QR Codes</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $analytics['total_qr_codes'] ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-chart-line text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Scans</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $analytics['total_scans'] ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-calendar-day text-purple-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Scans Aujourd'hui</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $analytics['scans_today'] ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-bolt text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">QR Actifs</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $analytics['active_qr_codes'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top Performing QR Codes -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">QR Codes les Plus Performants</h3>
            </div>
            <div class="p-6">
                <?php if (!empty($topQRCodes)): ?>
                    <div class="space-y-4">
                        <?php foreach ($topQRCodes as $qr): ?>
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($qr['name']) ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($qr['content_type']) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900"><?= $qr['scan_count'] ?> scans</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-8">Aucun QR code créé</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Activité Récente</h3>
            </div>
            <div class="p-6">
                <?php if (!empty($recentActivity)): ?>
                    <div class="space-y-4">
                        <?php foreach ($recentActivity as $activity): ?>
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-eye text-gray-400"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm text-gray-900">
                                        Scan de <strong><?= htmlspecialchars($activity['qr_name']) ?></strong>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <?= date('d/m/Y H:i', strtotime($activity['scanned_at'])) ?>
                                        <?php if ($activity['country'] !== 'Unknown'): ?>
                                            • <?= htmlspecialchars($activity['country']) ?>
                                        <?php endif; ?>
                                        • <?= htmlspecialchars($activity['device_type']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-8">Aucune activité récente</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Scan Trends Chart -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Tendance des Scans (7 derniers jours)</h3>
        </div>
        <div class="p-6">
            <canvas id="scanTrendsChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Actions Rapides</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="/dashboard/qr-codes" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-plus text-blue-600 mr-3"></i>
                    <span class="font-medium">Créer un QR Code</span>
                </a>
                <a href="/dashboard/qr-codes" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-list text-green-600 mr-3"></i>
                    <span class="font-medium">Voir Mes QR Codes</span>
                </a>
                <a href="/dashboard/analytics" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-chart-bar text-purple-600 mr-3"></i>
                    <span class="font-medium">Analytics Détaillés</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Scan Trends Chart
const ctx = document.getElementById('scanTrendsChart').getContext('2d');
const scanTrends = <?= json_encode($scanTrends) ?>;

const labels = scanTrends.map(item => {
    const date = new Date(item.date);
    return date.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' });
});

const data = scanTrends.map(item => parseInt(item.scans));

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Scans',
            data: data,
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>

<?php
$content = ob_get_clean();
$title = 'Tableau de Bord - QR Generator';
include __DIR__ . '/../layouts/base.php';
?>