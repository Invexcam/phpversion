<?php 
ob_start();
?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Créer un Utilisateur</h1>
        <p class="text-gray-600">Ajouter un nouvel utilisateur au système</p>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Informations Utilisateur</h3>
        </div>
        <div class="p-6">
            <form x-data="createUserForm()" @submit.prevent="submitForm">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                            <input type="text" x-model="formData.first_name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input type="text" x-model="formData.last_name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" x-model="formData.email" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                        <input type="password" x-model="formData.password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Minimum 6 caractères</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                        <select x-model="formData.role" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="user">Utilisateur</option>
                            <option value="admin">Administrateur</option>
                        </select>
                    </div>
                </div>

                <div x-show="error" x-cloak class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <span x-text="error"></span>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <a href="/admin/users" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                        Annuler
                    </a>
                    <button type="submit" :disabled="loading"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 transition">
                        <span x-show="!loading">Créer l'utilisateur</span>
                        <span x-show="loading">Création...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function createUserForm() {
    return {
        formData: {
            email: '',
            password: '',
            first_name: '',
            last_name: '',
            role: 'user'
        },
        loading: false,
        error: '',

        async submitForm() {
            this.loading = true;
            this.error = '';

            try {
                const formData = new FormData();
                Object.keys(this.formData).forEach(key => {
                    formData.append(key, this.formData[key]);
                });
                formData.append('csrf_token', window.csrfToken);

                const response = await fetch('/admin/users/create', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (response.ok) {
                    showToast('Utilisateur créé avec succès', 'success');
                    window.location.href = '/admin/users';
                } else {
                    this.error = result.message || 'Erreur lors de la création';
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
$title = 'Créer un Utilisateur - Administration';
include __DIR__ . '/../layouts/base.php';
?>