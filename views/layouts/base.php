<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'QR Generator' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <?php if (auth_user()): ?>
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="/dashboard" class="text-xl font-bold text-blue-600">QR Generator</a>
                        <div class="hidden md:ml-6 md:flex md:space-x-8">
                            <a href="/dashboard" class="text-gray-900 hover:text-blue-600 px-3 py-2 text-sm font-medium">Dashboard</a>
                            <a href="/dashboard/qr-codes" class="text-gray-900 hover:text-blue-600 px-3 py-2 text-sm font-medium">Mes QR Codes</a>
                            <a href="/dashboard/analytics" class="text-gray-900 hover:text-blue-600 px-3 py-2 text-sm font-medium">Analytics</a>
                            <?php if (auth_user()['role'] === 'admin'): ?>
                                <a href="/admin" class="text-red-600 hover:text-red-700 px-3 py-2 text-sm font-medium">Administration</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">Bonjour, <?= htmlspecialchars(auth_user()['first_name'] ?? auth_user()['email']) ?></span>
                        <form method="POST" action="/api/auth/logout" class="inline">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <button type="submit" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="<?= auth_user() ? 'py-8' : '' ?>">
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                © <?= date('Y') ?> QR Generator. Tous droits réservés.
            </p>
        </div>
    </footer>

    <script>
        // CSRF token for AJAX requests
        window.csrfToken = '<?= csrf_token() ?>';
        
        // Helper function for API requests
        async function apiRequest(url, options = {}) {
            const headers = {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...options.headers
            };
            
            if (options.body && typeof options.body === 'object') {
                options.body = JSON.stringify(options.body);
            }
            
            const response = await fetch(url, {
                ...options,
                headers
            });
            
            if (!response.ok) {
                const error = await response.json().catch(() => ({ message: 'Erreur réseau' }));
                throw new Error(error.message || 'Erreur réseau');
            }
            
            return response.json();
        }
        
        // Show toast notifications
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' :
                type === 'warning' ? 'bg-yellow-500' :
                'bg-blue-500'
            } text-white`;
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 5000);
        }
    </script>
</body>
</html>