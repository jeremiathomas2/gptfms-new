@extends('layouts.app')

@section('breadcrumb', 'Messages')

@push('styles')
<style>
    #content { 
        display: flex; 
        flex-direction: column; 
        overflow: hidden !important; 
        height: calc(100vh - 60px); /* Adjust for navbar height */
    }
    .page { 
        display: none; 
        flex: 1; 
        flex-direction: column; 
        min-height: 0; 
        height: 100%; 
    }
    .page.active { display: flex; }
    .chat-item.has-unread .chat-name { font-weight: 900; }
    .unread-pill{display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;padding:0 6px;border-radius:999px;background:rgba(37,99,235,.14);border:1px solid rgba(37,99,235,.35);color:var(--primary);font-weight:900;font-size:11px;line-height:1}
</style>
@endpush

@section('content')
<div class="page active" id="page-messages">
    <div class="section-header">
        <div><div class="section-title">Messages</div><div class="section-sub">Real-time team communication</div></div>
        <button class="btn btn-primary btn-sm" onclick="toast('New chat created!','<i class=\'uil uil-comment-add\'></i>')"><i class="uil uil-comment-add me-1"></i> New Chat</button>
    </div>
    <div class="chat-layout card" style="padding:0">
        <div class="chat-sidebar">
            @role('admin')
            <div style="padding: 10px; border-bottom: 1px solid var(--border); display: flex; gap: 5px;">
                <button class="btn btn-ghost btn-sm active chat-filter-btn" onclick="filterChats('groups', this)" style="flex:1; font-size: 11px;">Groups</button>
                <button class="btn btn-ghost btn-sm chat-filter-btn" onclick="filterChats('students', this)" style="flex:1; font-size: 11px;">Students</button>
                <button class="btn btn-ghost btn-sm chat-filter-btn" onclick="filterChats('supervisors', this)" style="flex:1; font-size: 11px;">Supervisors</button>
            </div>
            @endrole
            <div class="chat-search"><input id="chat-search-input" class="form-control" style="font-size:12.5px;padding:8px 12px" placeholder="🔍 Search chats…"/></div>
            <div id="chat-list">
                <!-- Chats will be loaded here -->
                <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 13px;">
                    <i class="uil uil-spinner-alt uil-spin" style="font-size: 24px; display: block; margin-bottom: 10px;"></i>
                    Loading chats...
                </div>
            </div>
        </div>
        <div class="chat-main" id="chat-main" style="display: none;">
            <div class="chat-header">
                <div class="chat-av" style="width:34px;height:34px;font-size:13px" id="active-chat-av"></div>
                <div>
                    <div style="font-weight:700;font-size:13.5px" id="active-chat-name"></div>
                    <div style="font-size:11.5px;color:var(--secondary)" id="active-chat-status"></div>
                </div>
                <div style="margin-left:auto;display:flex;gap:6px">
                    <button class="btn btn-ghost btn-sm"><i class="uil uil-paperclip"></i></button>
                    <button class="btn btn-ghost btn-sm"><i class="uil uil-search"></i></button>
                </div>
            </div>
            <div class="chat-messages" id="chat-messages">
                <!-- Messages will be loaded here -->
            </div>
            
            <!-- Attachment Preview -->
            <div id="attachment-preview-container" style="display: none; padding: 10px 18px; background: var(--bg-soft); border-top: 1px solid var(--border); display: flex; gap: 10px; flex-wrap: wrap; flex-shrink: 0;"></div>

            <div class="chat-input-area" style="position: relative;">
                <div id="emoji-picker" class="card" style="display: none; position: absolute; bottom: 60px; left: 14px; width: 250px; padding: 10px; z-index: 1000; box-shadow: var(--shadow-hover);">
                    <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 5px; font-size: 20px;">
                        <span onclick="insertEmoji('😀')" style="cursor:pointer; text-align:center;">😀</span>
                        <span onclick="insertEmoji('😁')" style="cursor:pointer; text-align:center;">😁</span>
                        <span onclick="insertEmoji('😅')" style="cursor:pointer; text-align:center;">😅</span>
                        <span onclick="insertEmoji('😂')" style="cursor:pointer; text-align:center;">😂</span>
                        <span onclick="insertEmoji('🤣')" style="cursor:pointer; text-align:center;">🤣</span>
                        <span onclick="insertEmoji('😊')" style="cursor:pointer; text-align:center;">😊</span>
                        <span onclick="insertEmoji('😇')" style="cursor:pointer; text-align:center;">😇</span>
                        <span onclick="insertEmoji('😉')" style="cursor:pointer; text-align:center;">😉</span>
                        <span onclick="insertEmoji('😍')" style="cursor:pointer; text-align:center;">😍</span>
                        <span onclick="insertEmoji('🥰')" style="cursor:pointer; text-align:center;">🥰</span>
                        <span onclick="insertEmoji('😘')" style="cursor:pointer; text-align:center;">😘</span>
                        <span onclick="insertEmoji('😋')" style="cursor:pointer; text-align:center;">😋</span>
                        <span onclick="insertEmoji('😛')" style="cursor:pointer; text-align:center;">😛</span>
                        <span onclick="insertEmoji('🤑')" style="cursor:pointer; text-align:center;">🤑</span>
                        <span onclick="insertEmoji('🤔')" style="cursor:pointer; text-align:center;">🤔</span>
                        <span onclick="insertEmoji('🤫')" style="cursor:pointer; text-align:center;">🤫</span>
                        <span onclick="insertEmoji('🤨')" style="cursor:pointer; text-align:center;">🤨</span>
                        <span onclick="insertEmoji('😐')" style="cursor:pointer; text-align:center;">😐</span>
                        <span onclick="insertEmoji('🙄')" style="cursor:pointer; text-align:center;">🙄</span>
                        <span onclick="insertEmoji('😬')" style="cursor:pointer; text-align:center;">😬</span>
                        <span onclick="insertEmoji('😴')" style="cursor:pointer; text-align:center;">😴</span>
                        <span onclick="insertEmoji('😷')" style="cursor:pointer; text-align:center;">😷</span>
                        <span onclick="insertEmoji('😎')" style="cursor:pointer; text-align:center;">😎</span>
                        <span onclick="insertEmoji('😭')" style="cursor:pointer; text-align:center;">😭</span>
                        <span onclick="insertEmoji('😱')" style="cursor:pointer; text-align:center;">😱</span>
                        <span onclick="insertEmoji('😡')" style="cursor:pointer; text-align:center;">😡</span>
                        <span onclick="insertEmoji('👍')" style="cursor:pointer; text-align:center;">👍</span>
                        <span onclick="insertEmoji('👎')" style="cursor:pointer; text-align:center;">👎</span>
                        <span onclick="insertEmoji('👌')" style="cursor:pointer; text-align:center;">👌</span>
                        <span onclick="insertEmoji('✌️')" style="cursor:pointer; text-align:center;">✌️</span>
                        <span onclick="insertEmoji('🙌')" style="cursor:pointer; text-align:center;">🙌</span>
                        <span onclick="insertEmoji('🙏')" style="cursor:pointer; text-align:center;">🙏</span>
                        <span onclick="insertEmoji('🔥')" style="cursor:pointer; text-align:center;">🔥</span>
                        <span onclick="insertEmoji('✨')" style="cursor:pointer; text-align:center;">✨</span>
                        <span onclick="insertEmoji('🎉')" style="cursor:pointer; text-align:center;">🎉</span>
                        <span onclick="insertEmoji('❤️')" style="cursor:pointer; text-align:center;">❤️</span>
                    </div>
                </div>

                <button class="btn btn-ghost" onclick="toggleEmojiPicker()" style="font-size:18px;padding:0"><i class="uil uil-smile"></i></button>
                <button class="btn btn-ghost" onclick="document.getElementById('attachment-input').click()" style="font-size:18px;padding:0"><i class="uil uil-paperclip"></i></button>
                <input type="file" id="attachment-input" style="display: none;" multiple onchange="handleAttachmentSelect(event)">
                
                <textarea class="chat-input" placeholder="Type a message… (Shift+Enter for new line)" id="chat-input-field" onkeydown="handleChatKeydown(event)" oninput="autoExpand(this)" rows="1" style="resize: none; max-height: 150px; overflow-y: hidden; padding: 10px 16px; line-height: 1.45;"></textarea>
                
                <button class="btn btn-primary btn-sm" id="send-msg-btn" onclick="sendMessage()">
                    <span class="btn-text"><i class="uil uil-message me-1"></i> Send</span>
                    <span class="btn-loader" style="display: none;"><i class="uil uil-spinner-alt uil-spin"></i></span>
                </button>
            </div>
        </div>
        <div class="chat-main" id="no-chat-selected" style="display: flex; align-items: center; justify-content: center; background: var(--bg-soft);">
            <div style="text-align: center; color: var(--text-muted);">
                <i class="uil uil-comments" style="font-size: 48px; display: block; margin-bottom: 15px;"></i>
                <div style="font-weight: 600;">Select a chat to start messaging</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activeChat = null;
    let refreshInterval = null;
    let chatListInterval = null;
    let allChatData = null;
    let currentFilter = 'groups';
    let searchTimeout = null;
    let selectedFiles = [];
    let lastMessageId = 0;
    let isFetching = false;

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const autoType = urlParams.get('type');
        const autoId = urlParams.get('id');
        
        loadChats(autoType, autoId);
        if (chatListInterval) clearInterval(chatListInterval);
        chatListInterval = setInterval(() => loadChats(), 5000);

        const searchInput = document.getElementById('chat-search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    renderChatList();
                }, 300);
            });
        }

        // Close emoji picker when clicking outside
        document.addEventListener('click', function(e) {
            const picker = document.getElementById('emoji-picker');
            const smileBtn = document.querySelector('.uil-smile')?.parentElement;
            if (picker && picker.style.display === 'block' && !picker.contains(e.target) && e.target !== smileBtn && !smileBtn?.contains(e.target)) {
                picker.style.display = 'none';
            }
        });
    });

    window.autoExpand = function(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    window.toggleEmojiPicker = function() {
        const picker = document.getElementById('emoji-picker');
        picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
    }

    window.insertEmoji = function(emoji) {
        const input = document.getElementById('chat-input-field');
        input.value += emoji;
        input.focus();
        autoExpand(input);
    }

    window.handleAttachmentSelect = function(e) {
        const files = Array.from(e.target.files);
        selectedFiles = [...selectedFiles, ...files];
        renderAttachmentPreview();
    }

    window.removeAttachment = function(index) {
        selectedFiles.splice(index, 1);
        renderAttachmentPreview();
    }

    window.openNewChatModal = function() {
        toast('Search for users feature coming soon!', '<i class="uil uil-search"></i>');
    }

    function renderAttachmentPreview() {
        const container = document.getElementById('attachment-preview-container');
        if (selectedFiles.length === 0) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'flex';
        container.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const div = document.createElement('div');
            div.className = 'badge badge-gray';
            div.style.padding = '5px 10px';
            div.innerHTML = `
                <i class="uil uil-file me-1"></i> ${file.name} 
                <i class="uil uil-times ms-2" style="cursor:pointer" onclick="removeAttachment(${index})"></i>
            `;
            container.appendChild(div);
        });
    }

    function loadChats(autoType = null, autoId = null) {
        fetch('/messages/chats')
            .then(response => response.json())
            .then(data => {
                allChatData = data;
                renderChatList(autoType, autoId);
            });
    }

    function renderChatList(autoType = null, autoId = null) {
        const chatList = document.getElementById('chat-list');
        const searchInput = document.getElementById('chat-search-input');
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        
        chatList.innerHTML = '';

        if (!allChatData) return;

        let targetItem = null;

        // If it's admin data (has students/supervisors keys)
        if (allChatData.students || allChatData.supervisors) {
            if (currentFilter === 'groups') {
                allChatData.groups
                    .filter(g => g.name.toLowerCase().includes(searchTerm))
                    .sort((a, b) => (Number(b.last_message_sort || 0) - Number(a.last_message_sort || 0)))
                    .forEach(group => {
                    const initials = group.name.substring(0, 2).toUpperCase();
                    const preview = group.last_message_preview || 'Group Chat';
                    const unread = Number(group.unread_count || 0);
                    const item = createChatItem('group', group.id, group.name, preview, initials, '', false, unread);
                    chatList.appendChild(item);
                    if (autoType === 'group' && autoId == group.id) {
                        targetItem = { type: 'group', id: group.id, name: group.name, initials: initials, element: item, isOnline: false };
                    }
                });
            } else if (currentFilter === 'students') {
                allChatData.students
                    .filter(u => u.name.toLowerCase().includes(searchTerm) || u.email.toLowerCase().includes(searchTerm))
                    .sort((a, b) => (Number(b.last_message_sort || 0) - Number(a.last_message_sort || 0)))
                    .forEach(user => {
                    const initials = user.initials || user.name.substring(0, 2).toUpperCase();
                    const preview = user.last_message_preview || 'Private Message';
                    const unread = Number(user.unread_count || 0);
                    const item = createChatItem('private', user.id, user.name, preview, initials, '', user.is_online, unread);
                    chatList.appendChild(item);
                    if (autoType === 'private' && autoId == user.id) {
                        targetItem = { type: 'private', id: user.id, name: user.name, initials: initials, element: item, isOnline: user.is_online };
                    }
                });
            } else if (currentFilter === 'supervisors') {
                allChatData.supervisors
                    .filter(u => u.name.toLowerCase().includes(searchTerm) || u.email.toLowerCase().includes(searchTerm))
                    .sort((a, b) => (Number(b.last_message_sort || 0) - Number(a.last_message_sort || 0)))
                    .forEach(user => {
                    const initials = user.initials || user.name.substring(0, 2).toUpperCase();
                    const preview = user.last_message_preview || 'Private Message';
                    const unread = Number(user.unread_count || 0);
                    const item = createChatItem('private', user.id, user.name, preview, initials, '', user.is_online, unread);
                    chatList.appendChild(item);
                    if (autoType === 'private' && autoId == user.id) {
                        targetItem = { type: 'private', id: user.id, name: user.name, initials: initials, element: item, isOnline: user.is_online };
                    }
                });
            }
        } else {
            // Standard data for Student/Supervisor roles
            const filteredGroups = allChatData.groups.filter(g => g.name.toLowerCase().includes(searchTerm));
            const filteredUsers = allChatData.users.filter(u => u.name.toLowerCase().includes(searchTerm) || (u.group_name && u.group_name.toLowerCase().includes(searchTerm)));

            if (filteredGroups.length === 0 && filteredUsers.length === 0) {
                chatList.innerHTML = `<div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 13px;">${searchTerm ? 'No matches found' : 'No active chats found'}</div>`;
                return;
            }

            const combined = [];
            filteredGroups.forEach(group => {
                combined.push({
                    type: 'group',
                    id: group.id,
                    name: group.name,
                    initials: group.name.substring(0, 2).toUpperCase(),
                    isOnline: false,
                    badge: '',
                    preview: group.last_message_preview || 'Group Chat',
                    unread: Number(group.unread_count || 0),
                    sort: Number(group.last_message_sort || 0),
                });
            });

            filteredUsers.forEach(user => {
                const groupBadge = user.group_name ? `<span class="badge badge-blue" style="font-size: 9px; margin-left: 5px; padding: 2px 5px;">${user.group_name}</span>` : '';
                combined.push({
                    type: 'private',
                    id: user.id,
                    name: user.name,
                    initials: (user.initials || user.name.substring(0, 2).toUpperCase()),
                    isOnline: !!user.is_online,
                    badge: groupBadge,
                    preview: user.last_message_preview || 'Private Message',
                    unread: Number(user.unread_count || 0),
                    sort: Number(user.last_message_sort || 0),
                });
            });

            combined.sort((a, b) => b.sort - a.sort);
            combined.forEach(itemData => {
                const item = createChatItem(itemData.type, itemData.id, itemData.name, itemData.preview, itemData.initials, itemData.badge, itemData.isOnline, itemData.unread);
                chatList.appendChild(item);

                if (autoType === itemData.type && autoId == itemData.id) {
                    targetItem = { type: itemData.type, id: itemData.id, name: itemData.name, initials: itemData.initials, element: item, isOnline: itemData.isOnline };
                }
            });
        }

        // Auto-select if requested
        if (targetItem) {
            selectChat(targetItem.type, targetItem.id, targetItem.name, targetItem.initials, targetItem.element, targetItem.isOnline);
        }
    }

    window.filterChats = function(filter, btn) {
        currentFilter = filter;
        document.querySelectorAll('.chat-filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderChatList();
    }

    function createChatItem(type, id, name, preview, initials, badge = '', isOnline = false, unreadCount = 0) {
        const div = document.createElement('div');
        div.className = 'chat-item';
        if (activeChat && activeChat.type === type && activeChat.id === id) {
            div.classList.add('active');
        }
        if (Number(unreadCount || 0) > 0) {
            div.classList.add('has-unread');
        }
        
        div.onclick = (e) => selectChat(type, id, name, initials, div, isOnline);

        const unread = Number(unreadCount || 0);
        const unreadHtml = unread > 0 ? `<span class="unread-pill" title="Unread">${unread > 9 ? '9+' : unread}</span>` : '';
        
        div.innerHTML = `
            <div class="chat-av" style="background:linear-gradient(135deg,var(--primary),var(--secondary))">
                ${initials}
                <div class="online-dot ${isOnline ? 'active' : ''}"></div>
            </div>
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px">
                    <div class="chat-name">${name}${badge}</div>
                    ${unreadHtml}
                </div>
                <div class="chat-preview">${preview || '—'}</div>
            </div>
        `;
        return div;
    }

    function selectChat(type, id, name, initials, element = null, isOnline = false) {
        activeChat = { type, id, name, initials };
        lastMessageId = 0;
        
        // UI Updates
        document.querySelectorAll('.chat-item').forEach(el => el.classList.remove('active'));
        if (element) {
            element.classList.add('active');
            element.classList.remove('has-unread');
            const pill = element.querySelector('.unread-pill');
            if (pill) pill.remove();
        } else {
            const items = document.querySelectorAll('.chat-item');
            items.forEach(item => {
                if (item.querySelector('.chat-name').innerText.includes(name)) {
                    item.classList.add('active');
                }
            });
        }
        
        document.getElementById('no-chat-selected').style.display = 'none';
        document.getElementById('chat-main').style.display = 'flex';
        
        document.getElementById('active-chat-name').innerText = name;
        document.getElementById('active-chat-av').innerText = initials;
        
        let statusHtml = type === 'group' ? '<i class="uil uil-users-alt me-1"></i> Group Chat' : 
            (isOnline ? '<i class="uil uil-circle me-1" style="font-size:8px; color: #10B981"></i> Online' : '<i class="uil uil-circle me-1" style="font-size:8px; color: var(--text-muted)"></i> Offline');
        
        if (type === 'private' && element) {
            const badge = element.querySelector('.badge');
            if (badge) {
                statusHtml += ` <span class="badge badge-blue" style="font-size: 10px; padding: 2px 6px;">${badge.innerText}</span>`;
            }
        }
        
        document.getElementById('active-chat-status').innerHTML = statusHtml;
        document.getElementById('chat-messages').innerHTML = '<div style="display: flex; justify-content: center; padding: 40px;"><div class="spinner"></div></div>';
        
        loadMessages(true);
        setTimeout(() => loadChats(), 600);
        
        if (refreshInterval) clearInterval(refreshInterval);
        refreshInterval = setInterval(() => loadMessages(false), 2000); 
    }

    function loadMessages(isFirstLoad = false) {
        if (!activeChat || isFetching) return;
        
        isFetching = true;
        const url = `/messages/${activeChat.type}/${activeChat.id}?since_id=${lastMessageId}`;
        
        fetch(url)
            .then(response => response.json())
            .then(messages => {
                const container = document.getElementById('chat-messages');
                const wasAtBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 100;
                
                if (isFirstLoad) container.innerHTML = '';

                if (messages.length > 0) {
                    messages.forEach(msg => {
                        if (msg.id > lastMessageId) {
                            lastMessageId = msg.id;
                            
                            // If this is my message, check for and remove the optimistic version
                            if (msg.sender_id == {{ auth()->id() }}) {
                                const optimisticMessages = document.querySelectorAll('.msg.me[data-is-optimistic="true"]');
                                for (let opt of optimisticMessages) {
                                    // Match by content to identify the correct optimistic message
                                    const optContent = opt.querySelector('.msg-bubble').innerText.replace('Sending...', '').trim();
                                    if (optContent === msg.content.trim()) {
                                        opt.remove();
                                        break; // Found and removed, stop looking
                                    }
                                }
                            }
                            
                            appendMessageToUI(msg);
                        }
                    });

                    if (wasAtBottom || isFirstLoad) {
                        container.scrollTop = container.scrollHeight;
                    }
                }
                isFetching = false;
            })
            .catch(() => {
                isFetching = false;
            });
    }

    function appendMessageToUI(msg, isOptimistic = false) {
        const container = document.getElementById('chat-messages');
        const isMe = msg.sender_id == {{ auth()->id() }};
        const div = document.createElement('div');
        div.className = `msg ${isMe ? 'me' : 'them'}`;
        if (isOptimistic) {
            div.style.opacity = '0.7';
            div.setAttribute('data-is-optimistic', 'true');
            div.setAttribute('data-temp-timestamp', new Date().getTime());
        }
        
        const time = msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'Sending...';
        const senderName = isMe ? 'You' : (msg.sender ? msg.sender.name : 'Unknown');
        
        let attachmentHtml = '';
        if (msg.attachments && msg.attachments.length > 0) {
            attachmentHtml = '<div class="msg-attachments" style="margin-top: 8px; display: flex; flex-direction: column; gap: 5px;">';
            msg.attachments.forEach(att => {
                const fileName = att.name || att.path.split('/').pop();
                attachmentHtml += `
                    <a href="/storage/${att.path}" target="_blank" class="badge badge-gray" style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 5px;">
                        <i class="uil uil-file-download-alt"></i> ${fileName}
                    </a>
                `;
            });
            attachmentHtml += '</div>';
        }

        const contentHtml = msg.content ? `<span>${msg.content.trim()}</span>` : '';
        div.innerHTML = `<div class="msg-bubble">${contentHtml}${attachmentHtml}${isOptimistic ? ' <i class="uil uil-spinner-alt uil-spin" style="font-size: 10px; margin-left: 5px;"></i>' : ''}</div><div class="msg-time">${senderName} · ${time}</div>`;
        container.appendChild(div);
    }

    window.handleChatKeydown = function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    }

    window.sendMessage = function() {
        const input = document.getElementById('chat-input-field');
        const btn = document.getElementById('send-msg-btn');
        const content = input.value.trim();
        
        if ((!content && selectedFiles.length === 0) || !activeChat || btn.disabled) return;
        
        // Disable button and show loader
        btn.disabled = true;
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.btn-loader').style.display = 'inline-block';

        // Optimistic UI Update
        const tempMsg = {
            sender_id: {{ auth()->id() }},
            content: content,
            created_at: new Date().toISOString(),
            attachments: selectedFiles.map(f => ({ name: f.name, path: '#' }))
        };
        appendMessageToUI(tempMsg, true);
        const container = document.getElementById('chat-messages');
        container.scrollTop = container.scrollHeight;

        const formData = new FormData();
        formData.append('content', content);
        formData.append('type', activeChat.type);
        formData.append('target_id', activeChat.id);
        
        selectedFiles.forEach((file, index) => {
            formData.append(`attachments[${index}]`, file);
        });

        // Clear inputs immediately for better UX
        input.value = '';
        input.style.height = 'auto';
        const filesToClear = [...selectedFiles];
        selectedFiles = [];
        document.getElementById('attachment-input').value = '';
        renderAttachmentPreview();
        
        fetch('/messages', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) return response.json().then(err => { throw err; });
            return response.json();
        })
        .then(message => {
            loadMessages(); // Trigger update to replace optimistic msg
            loadChats(activeChat.type, activeChat.id);
        })
        .catch(error => {
            console.error('Error sending message:', error);
            // Revert UI on error
            input.value = content;
            selectedFiles = filesToClear;
            renderAttachmentPreview();
            
            // Remove optimistic message on error
            const opt = document.querySelector(`[data-temp-id="${tempMsg.created_at}"]`);
            if (opt) opt.innerHTML = '<div class="msg-bubble" style="background: var(--danger-light); color: var(--danger);">Failed to send message. Click to retry.</div>';
            
            toast('Failed to send message. Please try again.', '<i class="uil uil-exclamation-triangle"></i>');
        })
        .finally(() => {
            btn.disabled = false;
            btn.querySelector('.btn-text').style.display = 'inline-block';
            btn.querySelector('.btn-loader').style.display = 'none';
            input.focus();
        });
    }
</script>
@endpush
