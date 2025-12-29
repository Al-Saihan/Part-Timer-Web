<x-layouts.app :title="__('Inbox')">
    <div class="flex h-[calc(100vh-64px)] bg-gradient-to-br from-blue-100 via-blue-50 to-blue-200 overflow-hidden" 
        x-data="{ 
            activeChat: {{ count($chatRooms) > 0 ? ($chatRooms[0]['id'] ?? 0) : 0 }},
            currentUserId: {{ $user->id }},
            chats: {{ json_encode($chatRooms) }},
            messages: [],
            messagesLoading: false,
            newMessage: '',
            loading: false,
            async loadMessages(roomId) {
                if (!roomId) return;
                this.messagesLoading = true;
                try {
                    const res = await fetch('/inbox/' + roomId + '/messages?per_page=100');
                    const data = await res.json();
                    this.messages = (data.messages || []).reverse();
                    setTimeout(() => this.scrollToBottom(), 100);
                } catch(e) {
                    console.error('Failed to load messages', e);
                    this.messages = [];
                } finally {
                    this.messagesLoading = false;
                }
            },
            async sendMessage(roomId) {
                if (!this.newMessage.trim() || this.loading) return;
                this.loading = true;
                const messageContent = this.newMessage;
                
                const optimisticMsg = {
                    id: 'temp-' + Date.now(),
                    sender_id: this.currentUserId,
                    content: messageContent,
                    created_at: new Date().toISOString()
                };
                this.messages.push(optimisticMsg);
                this.newMessage = '';
                setTimeout(() => this.scrollToBottom(), 50);
                
                try {
                    const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
                    const res = await fetch('/inbox/' + roomId + '/message', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
                        body: JSON.stringify({ content: messageContent })
                    });
                    const data = await res.json();
                    if (data.success) {
                        const idx = this.messages.findIndex(m => m.id === optimisticMsg.id);
                        if (idx !== -1) this.messages[idx] = data.message;
                    } else {
                        this.messages = this.messages.filter(m => m.id !== optimisticMsg.id);
                        alert('Failed to send message');
                    }
                } catch(e) {
                    this.messages = this.messages.filter(m => m.id !== optimisticMsg.id);
                } finally {
                    this.loading = false;
                }
            },
            scrollToBottom() {
                const msgContainer = document.querySelector('[data-messages-container]');
                if (msgContainer) msgContainer.scrollTop = msgContainer.scrollHeight;
            },
            async deleteMessage(roomId, messageId) {
                if (!confirm('Are you sure you want to delete this message?')) return;
                
                try {
                    const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
                    const res = await fetch('/inbox/' + roomId + '/message/' + messageId, {
                        method: 'DELETE',
                        headers: {'X-CSRF-TOKEN': csrfToken}
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.messages = this.messages.filter(m => m.id !== messageId);
                    } else {
                        alert('Failed to delete message');
                    }
                } catch(e) {
                    console.error('Failed to delete message', e);
                    alert('Error deleting message');
                }
            }
        }" x-init="loadMessages(activeChat)">
        
        <div class="w-80 md:w-96 border-r border-blue-200/50 flex flex-col bg-white/30 backdrop-blur-md">
            <div class="p-6 border-b border-blue-200/30">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-black text-blue-950 tracking-tight">Inbox</h2>
                    <button onclick="window.location.reload()" class="p-2 hover:bg-blue-100/50 rounded-lg transition-colors" title="Refresh">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
                <div class="mt-4">
                    <input type="text" placeholder="Search messages..." 
                        class="w-full bg-white/50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all placeholder-blue-400 text-blue-900 shadow-sm">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar">
                @forelse($chatRooms as $chat)
                    @php
                        $otherUser = $chat['other_participant'] ?? null;
                        $initials = strtoupper(substr($otherUser['name'] ?? 'U', 0, 1));
                        $userPic = $otherUser['profile_pic'] ?? null;
                        if ($userPic && ! str_contains($userPic, '.')) { $userPic = $userPic . '.png'; }
                    @endphp
                    <button @click="activeChat = {{ $chat['id'] }}; loadMessages({{ $chat['id'] }})" 
                        :class="activeChat === {{ $chat['id'] }} ? 'bg-white/60 shadow-sm ring-1 ring-blue-200/50' : 'hover:bg-white/20'"
                        class="w-full text-left px-6 py-5 transition-all flex items-center gap-4 border-b border-blue-100/20 relative">
                        
                        <div x-show="activeChat === {{ $chat['id'] }}" class="absolute left-0 top-0 bottom-0 w-1.5 bg-blue-600"></div>

                        <div class="flex-shrink-0">
                            @if($userPic && file_exists(public_path('avatars/' . $userPic)))
                                <img src="{{ asset('avatars/' . $userPic) }}" alt="{{ $otherUser['name'] ?? 'User' }}" class="h-12 w-12 rounded-full object-cover shadow-md">
                            @elseif($otherUser['profile_pic'] ?? null)
                                <img src="{{ $otherUser['profile_pic'] }}" alt="{{ $otherUser['name'] ?? 'User' }}" class="h-12 w-12 rounded-full object-cover shadow-md">
                            @else
                                <div class="h-12 w-12 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white text-lg shadow-md">
                                    {{ $initials }}
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline mb-0.5">
                                <h4 class="text-sm font-bold text-blue-950 truncate">{{ $otherUser['name'] ?? 'User' }}</h4>
                                <span class="text-[10px] font-black text-blue-500/70 uppercase tracking-wider">
                                    {{ $chat['latest_message'] ? \Carbon\Carbon::parse($chat['latest_message']['created_at'])->diffForHumans(null, true) : '' }}
                                </span>
                            </div>
                            <p class="text-xs text-blue-900/60 truncate leading-relaxed">{{ $chat['latest_message']['content'] ?? 'No messages yet' }}</p>
                        </div>
                    </button>
                @empty
                    <div class="p-10 text-center text-blue-500/50 font-medium italic">No conversations found.</div>
                @endforelse
            </div>
        </div>

        <div class="flex-1 flex flex-col bg-white/10 backdrop-blur-[2px]">
            @if(count($chatRooms) > 0)
                <template x-if="activeChat !== 0">
                    <div class="w-full flex flex-col h-full">
                        <div class="px-8 py-5 border-b border-blue-200/30 flex items-center bg-blue-100 backdrop-blur-md flex-shrink-0">
                            <div class="flex items-center gap-4" x-data="{ currentChat: null }" x-effect="currentChat = chats.find(c => c.id === activeChat)">
                                <template x-if="currentChat?.other_participant.profile_pic">
                                    <img :src="currentChat.other_participant.profile_pic.includes('.') ? '/avatars/' + currentChat.other_participant.profile_pic : '/avatars/' + currentChat.other_participant.profile_pic + '.png'" 
                                        :alt="currentChat?.other_participant.name" 
                                        class="h-10 w-10 rounded-full object-cover shadow-sm">
                                </template>
                                <template x-if="!currentChat?.other_participant.profile_pic">
                                    <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white text-sm shadow-sm" x-text="currentChat?.other_participant.name.charAt(0)"></div>
                                </template>
                                <div>
                                    <h3 class="text-sm font-bold text-blue-950" x-text="currentChat?.other_participant.name"></h3>
                                    <template x-if="currentChat?.applied_jobs && currentChat.applied_jobs.length > 0">
                                        <div class="flex items-center gap-1 mt-1">
                                            <svg class="w-3 h-3 text-blue-600/70" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                                <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/>
                                            </svg>
                                            <p class="text-[10px] text-blue-700/70 font-medium" x-text="currentChat.applied_jobs.join(', ')"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto p-8 space-y-6 custom-scrollbar" data-messages-container>
                            <template x-if="messagesLoading">
                                <div class="flex flex-col items-center justify-center h-full space-y-3">
                                    <div class="w-8 h-8 border-4 border-blue-600/20 border-t-blue-600 rounded-full animate-spin"></div>
                                    <p class="text-blue-600/60 text-xs font-black uppercase tracking-tighter">Loading Chat</p>
                                </div>
                            </template>

                            <template x-if="!messagesLoading">
                                <template x-for="msg in messages" :key="msg.id">
                                    <div :class="msg.sender_id === currentUserId ? 'flex items-start gap-3 max-w-xl ml-auto flex-row-reverse' : 'flex items-start gap-3 max-w-xl'" class="group">
                                        <div :class="msg.sender_id === currentUserId 
                                            ? 'bg-blue-600 text-white shadow-blue-200/50 rounded-tr-none' 
                                            : 'bg-white/80 text-blue-950 border border-blue-100 rounded-tl-none'" 
                                            class="p-4 rounded-2xl shadow-xl backdrop-blur-sm relative">
                                            <template x-if="msg.sender_id === currentUserId">
                                                <button @click="deleteMessage(activeChat, msg.id)" 
                                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center shadow-lg"
                                                    title="Delete message">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                </button>
                                            </template>
                                            <p class="text-sm leading-relaxed font-medium" x-text="msg.content"></p>
                                            <span :class="msg.sender_id === currentUserId ? 'text-blue-100/70' : 'text-blue-400'" 
                                                class="text-[9px] font-bold mt-2 block uppercase tracking-tighter" 
                                                x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </div>

                        <div class="px-8 py-6 bg-white/20 backdrop-blur-lg border-t border-blue-200/30 flex-shrink-0">
                            <form @submit.prevent="sendMessage(activeChat)" class="flex items-center gap-4 max-w-5xl mx-auto">
                                <div class="flex-1 relative">
                                    <input type="text" placeholder="Write a message..." x-model="newMessage" :disabled="loading"
                                        class="w-full bg-white/80 border-none rounded-2xl px-6 py-4 text-sm text-blue-950 placeholder-blue-300 focus:ring-2 focus:ring-blue-600 transition-all shadow-lg disabled:opacity-50">
                                </div>
                                <button type="submit" :disabled="loading || !newMessage.trim()" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white w-14 h-14 rounded-2xl shadow-lg shadow-blue-400/30 flex items-center justify-center transition-all active:scale-95 disabled:opacity-50">
                                    <svg class="w-6 h-6 transform rotate-90" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </template>
            @else
                <div class="flex flex-col items-center justify-center h-full">
                    <div class="w-20 h-20 bg-blue-600/10 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-blue-600/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-blue-950 font-bold text-lg">Your Inbox is quiet</h3>
                    <p class="text-blue-600/60 text-sm">When you message recruiters, they will appear here.</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(59, 130, 246, 0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(59, 130, 246, 0.4); }
    </style>
</x-layouts.app>