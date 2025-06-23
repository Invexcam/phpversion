<?php 
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Administration</h1>
        <p class="text-gray-600">Tableau de bord administrateur</p>
    </div>

    <!-- Admin Navigation -->
    <div class="mb-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4">
            <nav class="flex space-x-8">
                <a href="/admin" class="text-blue-600 border-b-2 border-blue-600 pb-2 font-medium">Dashboard</a>
                <a href="/admin/users" class="text-gray-500 hover:text-gray-700 pb-2">Utilisateurs</a>
                <a href="/admin/qr-codes" class="text-gray-500 hover:text-gray-700 pb-2">QR Codes</a>
                <a href="/admin/scans" class="text-gray-500 hover:text-gray-700 pb-2">Scans</a>
                <a href="/admin/system" class="text-gray-500 hover:text-gray-700 pb-2">Système</a>
                <a href="/admin/settings" class="text-gray-500 hover:text-gray-700 pb-2">Paramètres</a>
            </nav>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Utilisateurs</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $stats['total_users'] ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-qrcode text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total QR Codes</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $stats['total_qr_codes'] ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Scans</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $stats['total_scans'] ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-calendar-day text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Activité Aujourd'hui</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        <?= array_sum(array_column($stats['recent_scans'], 'scanned_at')) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Utilisateurs Récents</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php foreach ($stats['recent_users'] as $user): ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                    </p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= 
                                    $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' 
                                ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                                <p class="text-xs text-gray-500 mt-1">
                                    <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4 pt-4 border-t">
                    <a href="/admin/users" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        Voir tous les utilisateurs →
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent QR Codes -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">QR Codes Récents</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php foreach ($stats['recent_qr_codes'] as $qr): ?>
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($qr['name']) ?></p>
                                <p class="text-xs text-gray-500">
                                    Par <?= htmlspecialchars($qr['user_email']) ?> • 
                                    <?= ucfirst($qr['content_type']) ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">
                                    <?= date('d/m/Y H:i', strtotime($qr['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4 pt-4 border-t">
                    <a href="/admin/qr-codes" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        Voir tous les QR codes →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Activité Récente</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <?php foreach ($stats['recent_scans'] as $scan): ?>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-eye text-gray-400"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-gray-900">
                                Scan de <strong><?= htmlspecialchars($scan['qr_name']) ?></strong>
                                par <?= htmlspecialchars($scan['user_email']) ?>
                            </p>
                            <p class="text-xs text-gray-500">
                                <?= date('d/m/Y H:i', strtotime($scan['scanned_at'])) ?>
                                <?php if ($scan['country'] !== 'Unknown'): ?>
                                    • <?= htmlspecialchars($scan['country']) ?>
                                <?php endif; ?>
                                • <?= htmlspecialchars($scan['device_type']) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-4 pt-4 border-t">
                <a href="/admin/scans" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    Voir toute l'activité →
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Actions Rapides</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="/admin/users/create" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-user-plus text-blue-600 mr-3"></i>
                    <span class="font-medium">Nouvel Utilisateur</span>
                </a>
                <a href="/admin/users" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-users text-green-600 mr-3"></i>
                    <span class="font-medium">Gérer Utilisateurs</span>
                </a>
                <a href="/admin/system" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-server text-purple-600 mr-3"></i>
                    <span class="font-medium">Info Système</span>
                </a>
                <a href="/admin/settings" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-cog text-gray-600 mr-3"></i>
                    <span class="font-medium">Paramètres</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Administration - QR Generator';
include __DIR__ . '/../layouts/base.php';
?>