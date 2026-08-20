<x-user-layout :title="'Notifications'">
    <!-- Header -->
    <div class="top">
        <div class="left-icons">
            <a href="{{ route('settings') }}" class="icon-btn"><i class='bx bx-arrow-back'></i></a>
        </div>
        <div class="title">Notifications</div>
        <button id="mark-all-read" class="text-sm text-green-400 font-semibold bg-transparent border-none cursor-pointer">
            Mark All Read
        </button>
    </div>

    <main class="w-full mx-auto px-4 pb-24 pt-2 md:pb-4 text-white">

        <!-- Notifications List -->
        <div class="space-y-3 mt-6" id="notifications-container">
            @forelse($notifications as $notification)
                <div class="notification-item bg-[#1b1b1d] rounded-xl p-4 border border-white/5 relative overflow-hidden" data-id="{{ $notification->id }}">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-3 flex-1 min-w-0">
                            <div class="mt-1 w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center {{ $notification->is_read ? 'bg-white/5' : 'bg-green-950/20' }}">
                                <i class="fas fa-bell {{ $notification->is_read ? 'text-muted' : 'text-green-400' }} text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h2 class="font-semibold text-sm text-white truncate">{{ $notification->title }}</h2>
                                <p class="text-xs text-muted mt-1 leading-relaxed">
                                    {{ $notification->message }}
                                </p>
                                <span class="text-[10px] text-muted mt-1.5 block">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 ml-4 flex-shrink-0">
                            @if(!$notification->is_read)
                                <button class="mark-read w-7 h-7 rounded-full bg-white/5 flex items-center justify-center text-muted hover:bg-green-950/30 hover:text-green-400 transition-all border-none cursor-pointer" data-id="{{ $notification->id }}">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                            @endif
                            <button class="delete-notification w-7 h-7 rounded-full bg-white/5 flex items-center justify-center text-muted hover:bg-red-950/30 hover:text-red-500 transition-all border-none cursor-pointer" data-id="{{ $notification->id }}">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-[#1b1b1d] rounded-xl border border-white/5">
                    <div class="w-16 h-16 bg-[#262628] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bell-slash text-muted text-2xl"></i>
                    </div>
                    <p class="text-sm text-muted">No notifications yet</p>
                </div>
            @endforelse

            <!-- Pagination -->
            <div class="mt-6 flex justify-center text-xs">
                {{ $notifications->links() }}
            </div>
        </div>
    </main>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mark single notification as read
        document.querySelectorAll('.mark-read').forEach(button => {
            button.addEventListener('click', function() {
                const notificationId = this.getAttribute('data-id');
                
                fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const notificationItem = this.closest('.notification-item');
                        const bellIcon = notificationItem.querySelector('.fa-bell');
                        const iconContainer = bellIcon.parentElement;
                        
                        bellIcon.classList.remove('text-green-400');
                        bellIcon.classList.add('text-muted');
                        iconContainer.classList.remove('bg-green-950/20');
                        iconContainer.classList.add('bg-white/5');
                        this.remove();
                        window.showToast('Notification marked as read');
                    }
                });
            });
        });

        // Delete notification
        document.querySelectorAll('.delete-notification').forEach(button => {
            button.addEventListener('click', function() {
                const notificationId = this.getAttribute('data-id');
                const notificationItem = this.closest('.notification-item');
                
                notificationItem.style.transition = 'all 0.3s ease';
                notificationItem.style.opacity = '0';
                notificationItem.style.transform = 'translateX(10px)';
                
                setTimeout(() => {
                    fetch(`/notifications/${notificationId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            notificationItem.remove();
                            window.showToast('Notification deleted');
                        }
                    });
                }, 300);
            });
        });

        // Mark all notifications as read
        document.getElementById('mark-all-read')?.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
            
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelectorAll('.notification-item').forEach(item => {
                        const bellIcon = item.querySelector('.fa-bell');
                        if (bellIcon) {
                            bellIcon.classList.remove('text-green-400');
                            bellIcon.classList.add('text-muted');
                            
                            const iconContainer = bellIcon.parentElement;
                            iconContainer.classList.remove('bg-green-950/20');
                            iconContainer.classList.add('bg-white/5');
                        }
                        
                        const markReadBtn = item.querySelector('.mark-read');
                        if (markReadBtn) {
                            markReadBtn.remove();
                        }
                    });
                    window.showToast('All notifications marked as read');
                }
            });
        });
    });
    </script>
    @endpush
</x-user-layout>