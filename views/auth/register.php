<?php 
ob_start();
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Créer un compte
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                <a href="/" class="font-medium text-gray-500 hover:text-gray-700 mr-4">
                    ← Retour à l'accueil
                </a>
                |
                <a href="/login" class="font-medium text-blue-600 hover:text-blue-500 ml-4">
                    connectez-vous à votre compte existant
                </a>
            </p>
        </div>
        
        <form id="registerForm" class="mt-8 space-y-6" x-data="registerForm()" @submit.prevent="submitForm">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom</label>
                        <input id="first_name" name="first_name" type="text" 
                               x-model="formData.first_name"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="Prénom">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Nom</label>
                        <input id="last_name" name="last_name" type="text" 
                               x-model="formData.last_name"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="Nom">
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Adresse email</label>
                    <input id="email" name="email" type="email" required 
                           x-model="formData.email"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Adresse email">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                    <input id="password" name="password" type="password" required 
                           x-model="formData.password"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Mot de passe (min. 6 caractères)">
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                    <input id="confirm_password" name="confirm_password" type="password" required 
                           x-model="formData.confirm_password"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Confirmer le mot de passe">
                </div>
            </div>

            <div class="flex items-center">
                <input id="accept-terms" name="accept-terms" type="checkbox" required
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="accept-terms" class="ml-2 block text-sm text-gray-900">
                    J'accepte les <a href="#" class="text-blue-600 hover:text-blue-500">conditions d'utilisation</a>
                </label>
            </div>

            <div>
                <button type="submit" 
                        :disabled="loading || !isFormValid"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                    <span x-show="!loading">Créer le compte</span>
                    <span x-show="loading" class="flex items-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Création...
                    </span>
                </button>
            </div>

            <div x-show="error" x-cloak class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <span x-text="error"></span>
            </div>
        </form>
    </div>
</div>

<script>
function registerForm() {
    return {
        formData: {
            email: '',
            password: '',
            confirm_password: '',
            first_name: '',
            last_name: ''
        },
        loading: false,
        error: '',
        
        get isFormValid() {
            return this.formData.email && 
                   this.formData.password && 
                   this.formData.password.length >= 6 &&
                   this.formData.password === this.formData.confirm_password;
        },
        
        async submitForm() {
            if (!this.isFormValid) {
                this.error = 'Veuillez vérifier tous les champs';
                return;
            }
            
            this.loading = true;
            this.error = '';
            
            try {
                const formData = new FormData();
                formData.append('email', this.formData.email);
                formData.append('password', this.formData.password);
                formData.append('first_name', this.formData.first_name);
                formData.append('last_name', this.formData.last_name);
                formData.append('csrf_token', window.csrfToken);
                
                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    showToast('Compte créé avec succès !', 'success');
                    window.location.href = '/dashboard';
                } else {
                    this.error = result.message || 'Erreur lors de la création du compte';
                }
            } catch (error) {
                this.error = 'Erreur de connexion. Veuillez réessayer.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<?php
$content = ob_get_clean();
$title = 'Inscription - QR Generator';
include __DIR__ . '/../layouts/base.php';
?>