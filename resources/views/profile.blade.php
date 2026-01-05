<x-app-layout>
    <!-- Toast Notification Container -->
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

    <div class="py-12" x-data="notificationHandler()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>

    <script>
        function notificationHandler() {
            return {
                init() {
                    // Listen for Livewire events
                    if (window.Livewire) {
                        Livewire.on('profile-updated', () => {
                            console.log('Profile updated event received');
                            showToast('Data berhasil disimpan', 'success');
                        });

                        Livewire.on('password-updated', () => {
                            console.log('Password updated event received');
                            showToast('Data berhasil disimpan', 'success');
                        });

                        Livewire.on('user-deleted', () => {
                            console.log('User deleted event received');
                            showToast('Akun berhasil dihapus', 'success');
                        });
                    }
                }
            }
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            const icon = type === 'success' ? '✓' : type === 'error' ? '✕' : 'ℹ';
            
            toast.className = `${bgColor} text-gray-900 px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 mb-3 animate-slide-in font-semibold`;
            toast.innerHTML = `
                <span class="text-xl">${icon}</span>
                <span>${message}</span>
            `;
            
            container.appendChild(toast);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                toast.classList.add('animate-fade-out');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (window.Livewire) {
                notificationHandler().init();
            }
        });
    </script>

    <style>
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }

        .animate-fade-out {
            animation: fadeOut 0.3s ease-out;
        }
    </style>
</x-app-layout>
