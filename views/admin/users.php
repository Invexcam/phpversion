<?php 
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="usersManager()">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestion des Utilisateurs</h1>
            <p class="text-gray-600">Total: <?= $totalUsers ?> utilisateurs</p>
        </div>
        <a href="/admin/users/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>
            Nouvel Utilisateur
        </a>
    </div>

    <!-- Admin Navigation -->
    <div class="mb-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4">
            <nav class="flex space-x-8">
                <a href="/admin" class="text-gray-500 hover:text-gray-700 pb-2">Dashboard</a>
                <a href="/admin/users" class="text-blue-600 border-b-2 border-blue-600 pb-2 font-medium">Utilisateurs</a>
                <a href="/admin/qr-codes" class="text-gray-500 hover:text-gray-700 pb-2">QR Codes</a>
                <a href="/admin/scans" class="text-gray-500 hover:text-gray-700 pb-2">Scans</a>
                <a href="/admin/system" class="text-gray-500 hover:text-gray-700 pb-2">Système</a>
                <a href="/admin/settings" class="text-gray-500 hover:text-gray-700 pb-2">Paramètres</a>
            </nav>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Liste des Utilisateurs</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Utilisateur
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rôle
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Abonnement
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Inscrit le
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            ID: <?= htmlspecialchars($user['id']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= htmlspecialchars($user['email']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= 
                                    $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' 
                                ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= 
                                    $user['subscription_status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' 
                                ?>">
                                    <?= ucfirst($user['subscription_status'] ?? 'free') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="/admin/users/<?= $user['id'] ?>/edit" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button @click="deleteUser('<?= $user['id'] ?>', '<?= htmlspecialchars($user['email']) ?>')"
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="mt-6 flex justify-center">
            <nav class="flex items-center space-x-2">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>" 
                       class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                        Précédent
                    </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" 
                       class="px-3 py-2 border rounded-md text-sm <?= 
                           $i === $currentPage 
                               ? 'bg-blue-600 border-blue-600 text-white' 
                               : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'
                       ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>" 
                       class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                        Suivant
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    <?php endif; ?>
</div>

<script>
function usersManager() {
    return {
        async deleteUser(userId, userEmail) {
            if (!confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur ${userEmail} ?`)) {
                return;
            }

            try {
                const response = await fetch(`/admin/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ csrf_token: window.csrfToken })
                });

                const result = await response.json();

                if (response.ok) {
                    showToast('Utilisateur supprimé', 'success');
                    window.location.reload();
                } else {
                    showToast(result.message || 'Erreur', 'error');
                }
            } catch (error) {
                showToast('Erreur réseau', 'error');
            }
        }
    }
}
</script>

<?php
$content = ob_get_clean();
$title = 'Gestion des Utilisateurs - Administration';
include __DIR__ . '/../layouts/base.php';
?>