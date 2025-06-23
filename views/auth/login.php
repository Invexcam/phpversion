<?php 
ob_start();
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Connexion à votre compte
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                <a href="/" class="font-medium text-gray-500 hover:text-gray-700 mr-4">
                    ← Retour à l'accueil
                </a>
                |
                <a href="/register" class="font-medium text-blue-600 hover:text-blue-500 ml-4">
                    créez un nouveau compte
                </a>
            </p>
        </div>
        
        <form id="loginForm" class="mt-8 space-y-6" x-data="loginForm()" @submit.prevent="submitForm">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Adresse email</label>
                    <input id="email" name="email" type="email" required 
                           x-model="formData.email"
                           class="relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Adresse email">
                </div>
                <div>
                    <label for="password" class="sr-only">Mot de passe</label>
                    <input id="password" name="password" type="password" required 
                           x-model="formData.password"
                           class="relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Mot de passe">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                        Se souvenir de moi
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" @click="showResetForm = true" class="font-medium text-blue-600 hover:text-blue-500">
                        Mot de passe oublié ?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" 
                        :disabled="loading"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                    <span x-show="!loading">Se connecter</span>
                    <span x-show="loading" class="flex items-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Connexion...
                    </span>
                </button>
            </div>

            <div x-show="error" x-cloak class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <span x-text="error"></span>
            </div>
        </form>

        <!-- Reset Password Modal -->
        <div x-show="showResetForm" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Réinitialiser le mot de passe</h3>
                    <form @submit.prevent="resetPassword">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <div class="mb-4">
                            <label for="reset-email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="reset-email" x-model="resetEmail" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="showResetForm = false"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Annuler
                            </button>
                            <button type="submit" :disabled="loading"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
                                Envoyer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loginForm() {
    return {
        formData: {
            email: '',
            password: ''
        },
        loading: false,
        error: '',
        showResetForm: false,
        resetEmail: '',
        
        async submitForm() {
            this.loading = true;
            this.error = '';
            
            try {
                const formData = new FormData();
                formData.append('email', this.formData.email);
                formData.append('password', this.formData.password);
                formData.append('csrf_token', window.csrfToken);
                
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    window.location.href = '/dashboard';
                } else {
                    this.error = result.message || 'Erreur de connexion';
                }
            } catch (error) {
                this.error = 'Erreur de connexion. Veuillez réessayer.';
            } finally {
                this.loading = false;
            }
        },
        
        async resetPassword() {
            this.loading = true;
            
            try {
                const formData = new FormData();
                formData.append('email', this.resetEmail);
                formData.append('csrf_token', window.csrfToken);
                
                const response = await fetch('/api/auth/reset-password', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    showToast('Instructions de réinitialisation envoyées', 'success');
                    this.showResetForm = false;
                    this.resetEmail = '';
                } else {
                    showToast(result.message || 'Erreur', 'error');
                }
            } catch (error) {
                showToast('Erreur réseau', 'error');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<?php
$content = ob_get_clean();
$title = 'Connexion - QR Generator';
include __DIR__ . '/../layouts/base.php';
?>