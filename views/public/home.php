<?php 
ob_start();
?>

<div class="bg-gradient-to-br from-blue-600 to-purple-700 min-h-screen">
    <!-- Hero Section -->
    <div class="container mx-auto px-4 py-16">
        <div class="text-center text-white">
            <h1 class="text-5xl font-bold mb-6">
                Générateur de QR Code
                <span class="text-yellow-300">Professionnel</span>
            </h1>
            <p class="text-xl mb-8 max-w-2xl mx-auto">
                Créez, personnalisez et suivez vos QR codes facilement. 
                Interface moderne avec analytics avancés.
            </p>
            <div class="space-x-4">
                <a href="/register" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                    Commencer Gratuitement
                </a>
                <a href="/login" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                    Se Connecter
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="bg-white py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Fonctionnalités</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-6">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-qrcode text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">QR Codes Personnalisés</h3>
                    <p class="text-gray-600">Créez des QR codes pour URLs, texte, email, téléphone, WiFi et cartes de visite.</p>
                </div>
                <div class="text-center p-6">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-bar text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Analytics Avancés</h3>
                    <p class="text-gray-600">Suivez les scans, géolocalisation, types d'appareils et tendances.</p>
                </div>
                <div class="text-center p-6">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-palette text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Personnalisation</h3>
                    <p class="text-gray-600">Personnalisez couleurs, tailles et styles de vos QR codes.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-gray-100 py-16" x-data="publicStats()">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Statistiques en Temps Réel</h2>
            <div class="grid md:grid-cols-4 gap-8 text-center">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-3xl font-bold text-blue-600" x-text="stats.total_qr_codes || '0'"></div>
                    <div class="text-gray-600">QR Codes Créés</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-3xl font-bold text-green-600" x-text="stats.total_scans || '0'"></div>
                    <div class="text-gray-600">Scans Totaux</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-3xl font-bold text-purple-600" x-text="stats.total_users || '0'"></div>
                    <div class="text-gray-600">Utilisateurs</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-3xl font-bold text-orange-600" x-text="stats.scans_today || '0'"></div>
                    <div class="text-gray-600">Scans Aujourd'hui</div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Types Section -->
    <div class="bg-white py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Types de QR Codes Supportés</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="border rounded-lg p-6 hover:shadow-lg transition">
                    <i class="fas fa-link text-blue-600 text-2xl mb-4"></i>
                    <h3 class="font-semibold mb-2">URL / Site Web</h3>
                    <p class="text-gray-600">Redirigez vers n'importe quel site web ou page.</p>
                </div>
                <div class="border rounded-lg p-6 hover:shadow-lg transition">
                    <i class="fas fa-envelope text-green-600 text-2xl mb-4"></i>
                    <h3 class="font-semibold mb-2">Email</h3>
                    <p class="text-gray-600">Créez un email pré-rempli avec sujet et corps.</p>
                </div>
                <div class="border rounded-lg p-6 hover:shadow-lg transition">
                    <i class="fas fa-phone text-purple-600 text-2xl mb-4"></i>
                    <h3 class="font-semibold mb-2">Téléphone</h3>
                    <p class="text-gray-600">Démarrez un appel automatiquement.</p>
                </div>
                <div class="border rounded-lg p-6 hover:shadow-lg transition">
                    <i class="fas fa-sms text-yellow-600 text-2xl mb-4"></i>
                    <h3 class="font-semibold mb-2">SMS</h3>
                    <p class="text-gray-600">Envoyez un SMS pré-écrit.</p>
                </div>
                <div class="border rounded-lg p-6 hover:shadow-lg transition">
                    <i class="fas fa-wifi text-indigo-600 text-2xl mb-4"></i>
                    <h3 class="font-semibold mb-2">WiFi</h3>
                    <p class="text-gray-600">Partagez vos informations de connexion WiFi.</p>
                </div>
                <div class="border rounded-lg p-6 hover:shadow-lg transition">
                    <i class="fas fa-user text-red-600 text-2xl mb-4"></i>
                    <h3 class="font-semibold mb-2">Carte de Visite</h3>
                    <p class="text-gray-600">Partagez vos informations de contact (vCard).</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-blue-600 py-16">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">
                Prêt à commencer ?
            </h2>
            <p class="text-xl text-blue-100 mb-8">
                Créez votre premier QR code en quelques secondes
            </p>
            <a href="/register" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                Inscription Gratuite
            </a>
        </div>
    </div>
</div>

<script>
function publicStats() {
    return {
        stats: {
            total_qr_codes: 0,
            total_scans: 0,
            total_users: 0,
            scans_today: 0
        },
        
        async init() {
            await this.loadStats();
            // Refresh stats every 30 seconds
            setInterval(() => this.loadStats(), 30000);
        },
        
        async loadStats() {
            try {
                const response = await fetch('/api/public/stats');
                if (response.ok) {
                    this.stats = await response.json();
                }
            } catch (error) {
                console.error('Erreur lors du chargement des statistiques:', error);
            }
        }
    }
}
</script>

<?php
$content = ob_get_clean();
$title = 'QR Generator - Générateur de QR Code Professionnel';
include __DIR__ . '/../layouts/base.php';
?>