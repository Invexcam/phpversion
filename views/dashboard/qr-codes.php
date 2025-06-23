<?php 
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="qrCodesManager()">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mes QR Codes</h1>
            <p class="text-gray-600">Gérez et créez vos QR codes personnalisés</p>
        </div>
        <button @click="showCreateModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>
            Nouveau QR Code
        </button>
    </div>

    <!-- QR Codes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" x-show="!loading">
        <template x-for="qr in qrCodes" :key="qr.id">
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold text-gray-900" x-text="qr.name"></h3>
                        <div class="flex space-x-2">
                            <button @click="editQR(qr)" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button @click="deleteQR(qr.id)" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <img :src="`/api/qr-codes/${qr.id}/image`" :alt="qr.name" class="w-32 h-32 mx-auto border">
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Type:</span>
                            <span class="capitalize" x-text="qr.content_type"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Créé:</span>
                            <span x-text="formatDate(qr.created_at)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Code court:</span>
                            <span class="font-mono text-blue-600" x-text="qr.short_code"></span>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between space-x-2">
                            <button @click="copyShortUrl(qr.short_code)" class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded text-sm hover:bg-gray-200 transition">
                                <i class="fas fa-copy mr-1"></i> Copier URL
                            </button>
                            <button @click="downloadQR(qr)" class="flex-1 bg-blue-100 text-blue-700 px-3 py-2 rounded text-sm hover:bg-blue-200 transition">
                                <i class="fas fa-download mr-1"></i> Télécharger
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="!loading && qrCodes.length === 0" class="text-center py-12">
        <i class="fas fa-qrcode text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun QR code</h3>
        <p class="text-gray-500 mb-4">Créez votre premier QR code pour commencer</p>
        <button @click="showCreateModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Créer un QR Code
        </button>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-12">
        <i class="fas fa-spinner fa-spin text-gray-400 text-4xl mb-4"></i>
        <p class="text-gray-500">Chargement...</p>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4" x-text="editingQR ? 'Modifier QR Code' : 'Nouveau QR Code'"></h3>
                
                <form @submit.prevent="saveQR">
                    <div class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input type="text" x-model="formData.name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Content Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type de contenu</label>
                            <select x-model="formData.content_type" @change="resetContent()" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Sélectionner un type</option>
                                <option value="url">URL / Site Web</option>
                                <option value="text">Texte</option>
                                <option value="email">Email</option>
                                <option value="phone">Téléphone</option>
                                <option value="sms">SMS</option>
                                <option value="wifi">WiFi</option>
                                <option value="vcard">Carte de visite</option>
                            </select>
                        </div>

                        <!-- Dynamic Content Fields -->
                        <div x-show="formData.content_type === 'url'">
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                            <input type="url" x-model="formData.content.url" placeholder="https://example.com"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div x-show="formData.content_type === 'text'">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Texte</label>
                            <textarea x-model="formData.content.text" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div x-show="formData.content_type === 'email'">
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" x-model="formData.content.email"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Sujet (optionnel)</label>
                                    <input type="text" x-model="formData.content.subject"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Corps (optionnel)</label>
                                    <textarea x-model="formData.content.body" rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                            </div>
                        </div>

                        <div x-show="formData.content_type === 'phone'">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de téléphone</label>
                            <input type="tel" x-model="formData.content.phone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div x-show="formData.content_type === 'sms'">
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de téléphone</label>
                                    <input type="tel" x-model="formData.content.phone"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Message (optionnel)</label>
                                    <textarea x-model="formData.content.message" rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                            </div>
                        </div>

                        <div x-show="formData.content_type === 'wifi'">
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du réseau (SSID)</label>
                                    <input type="text" x-model="formData.content.ssid"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                                    <input type="text" x-model="formData.content.password"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Sécurité</label>
                                    <select x-model="formData.content.security"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="WPA">WPA/WPA2</option>
                                        <option value="WEP">WEP</option>
                                        <option value="nopass">Aucune</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div x-show="formData.content_type === 'vcard'">
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                                        <input type="text" x-model="formData.content.firstName"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                                        <input type="text" x-model="formData.content.lastName"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" x-model="formData.content.email"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                                    <input type="tel" x-model="formData.content.phone"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Organisation</label>
                                    <input type="text" x-model="formData.content.organization"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="closeModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                            Annuler
                        </button>
                        <button type="submit" :disabled="saving"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 transition">
                            <span x-show="!saving" x-text="editingQR ? 'Modifier' : 'Créer'"></span>
                            <span x-show="saving">Sauvegarde...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function qrCodesManager() {
    return {
        qrCodes: [],
        loading: true,
        showCreateModal: false,
        editingQR: null,
        saving: false,
        formData: {
            name: '',
            content_type: '',
            content: {},
            style: {}
        },

        async init() {
            await this.loadQRCodes();
        },

        async loadQRCodes() {
            this.loading = true;
            try {
                const response = await fetch('/api/qr-codes');
                if (response.ok) {
                    this.qrCodes = await response.json();
                }
            } catch (error) {
                showToast('Erreur lors du chargement', 'error');
            } finally {
                this.loading = false;
            }
        },

        editQR(qr) {
            this.editingQR = qr;
            this.formData = {
                name: qr.name,
                content_type: qr.content_type,
                content: { ...qr.content },
                style: { ...qr.style }
            };
            this.showCreateModal = true;
        },

        async deleteQR(id) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce QR code ?')) return;

            try {
                const response = await fetch(`/api/qr-codes/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ csrf_token: window.csrfToken })
                });

                if (response.ok) {
                    await this.loadQRCodes();
                    showToast('QR code supprimé', 'success');
                } else {
                    const error = await response.json();
                    showToast(error.message || 'Erreur', 'error');
                }
            } catch (error) {
                showToast('Erreur réseau', 'error');
            }
        },

        async saveQR() {
            this.saving = true;
            try {
                const url = this.editingQR ? `/api/qr-codes/${this.editingQR.id}` : '/api/qr-codes';
                const method = this.editingQR ? 'PUT' : 'POST';

                const payload = {
                    ...this.formData,
                    csrf_token: window.csrfToken
                };

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    await this.loadQRCodes();
                    this.closeModal();
                    showToast(this.editingQR ? 'QR code modifié' : 'QR code créé', 'success');
                } else {
                    const error = await response.json();
                    showToast(error.message || 'Erreur', 'error');
                }
            } catch (error) {
                showToast('Erreur réseau', 'error');
            } finally {
                this.saving = false;
            }
        },

        closeModal() {
            this.showCreateModal = false;
            this.editingQR = null;
            this.formData = {
                name: '',
                content_type: '',
                content: {},
                style: {}
            };
        },

        resetContent() {
            this.formData.content = {};
        },

        copyShortUrl(shortCode) {
            const url = `${window.location.origin}/s/${shortCode}`;
            navigator.clipboard.writeText(url).then(() => {
                showToast('URL copiée', 'success');
            });
        },

        downloadQR(qr) {
            const link = document.createElement('a');
            link.href = `/api/qr-codes/${qr.id}/image`;
            link.download = `${qr.name}.png`;
            link.click();
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR');
        }
    }
}
</script>

<?php
$content = ob_get_clean();
$title = 'Mes QR Codes - QR Generator';
include __DIR__ . '/../layouts/base.php';
?>