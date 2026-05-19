@extends('layouts.app')

@section('breadcrumb', 'Messages')

@push('styles')
<style>
    #content { overflow: hidden !important; }
    .page { height: 100%; }
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
                
                <textarea class="chat-input" placeholder="Type a message… (Shift+Enter for new line)" id="chat-input-field" onkeydown="handleKey(event)" oninput="autoExpand(this)" rows="1" style="resize: none; max-height: 150px; overflow-y: auto; padding-top: 10px;"></textarea>
                
                <button class="btn btn-primary btn-sm" onclick="handleSend()"><i class="uil uil-message me-1"></i> Send</button>
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
    let allChatData = null;
    let currentFilter = 'groups';
    let searchTimeout = null;
    let selectedFiles = [];

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const autoType = urlParams.get('type');
        const autoId = urlParams.get('id');
        
        loadChats(autoType, autoId);

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
            const smileBtn = document.querySelector('.uil-smile').parentElement;
            if (picker && picker.style.display === 'block' && !picker.contains(e.target) && e.target !== smileBtn && !smileBtn.contains(e.target)) {
                picker.style.display = 'none';
            }
        });
    });

    function autoExpand(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    function toggleEmojiPicker() {
        const picker = document.getElementById('emoji-picker');
        picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
    }

    function insertEmoji(emoji) {
        const input = document.getElementById('chat-input-field');
        input.value += emoji;
        input.focus();
        // Keep picker open for multiple emojis? No, usually it closes.
        // toggleEmojiPicker(); 
        autoExpand(input);
    }

    function handleAttachmentSelect(e) {
        const files = Array.from(e.target.files);
        selectedFiles = [...selectedFiles, ...files];
        renderAttachmentPreview();
    }

    function removeAttachment(index) {
        selectedFiles.splice(index, 1);
        renderAttachmentPreview();
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
                allChatData.groups.filter(g => g.name.toLowerCase().includes(searchTerm)).forEach(group => {
                    const initials = group.name.substring(0, 2).toUpperCase();
                    const item = createChatItem('group', group.id, group.name, 'Group Chat', initials);
                    chatList.appendChild(item);
                    if (autoType === 'group' && autoId == group.id) {
                        targetItem = { type: 'group', id: group.id, name: group.name, initials: initials, element: item };
                    }
                });
            } else if (currentFilter === 'students') {
                allChatData.students.filter(u => u.name.toLowerCase().includes(searchTerm) || u.email.toLowerCase().includes(searchTerm)).forEach(user => {
                    const initials = user.initials || user.name.substring(0, 2).toUpperCase();
                    const item = createChatItem('private', user.id, user.name, 'Student', initials);
                    chatList.appendChild(item);
                    if (autoType === 'private' && autoId == user.id) {
                        targetItem = { type: 'private', id: user.id, name: user.name, initials: initials, element: item };
                    }
                });
            } else if (currentFilter === 'supervisors') {
                allChatData.supervisors.filter(u => u.name.toLowerCase().includes(searchTerm) || u.email.toLowerCase().includes(searchTerm)).forEach(user => {
                    const initials = user.initials || user.name.substring(0, 2).toUpperCase();
                    const item = createChatItem('private', user.id, user.name, 'Supervisor', initials);
                    chatList.appendChild(item);
                    if (autoType === 'private' && autoId == user.id) {
                        targetItem = { type: 'private', id: user.id, name: user.name, initials: initials, element: item };
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

            // Add Groups
            filteredGroups.forEach(group => {
                const initials = group.name.substring(0, 2).toUpperCase();
                const item = createChatItem('group', group.id, group.name, 'Group Chat', initials);
                chatList.appendChild(item);
                
                if (autoType === 'group' && autoId == group.id) {
                    targetItem = { type: 'group', id: group.id, name: group.name, initials: initials, element: item };
                }
            });

            // Add Private Chats
            filteredUsers.forEach(user => {
                const initials = user.initials || user.name.substring(0, 2).toUpperCase();
                const groupBadge = user.group_name ? `<span class="badge badge-blue" style="font-size: 9px; margin-left: 5px; padding: 2px 5px;">${user.group_name}</span>` : '';
                const item = createChatItem('private', user.id, user.name, 'Private Message', initials, groupBadge);
                chatList.appendChild(item);

                if (autoType === 'private' && autoId == user.id) {
                    targetItem = { type: 'private', id: user.id, name: user.name, initials: initials, element: item };
                }
            });
        }

        // Auto-select if requested
        if (targetItem) {
            selectChat(targetItem.type, targetItem.id, targetItem.name, targetItem.initials, targetItem.element);
        }
    }

    window.filterChats = function(filter, btn) {
        currentFilter = filter;
        document.querySelectorAll('.chat-filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderChatList();
    }

    function createChatItem(type, id, name, preview, initials, badge = '') {
        const div = document.createElement('div');
        div.className = 'chat-item';
        if (activeChat && activeChat.type === type && activeChat.id === id) {
            div.classList.add('active');
        }
        
        div.onclick = (e) => selectChat(type, id, name, initials, div);
        
        div.innerHTML = `
            <div class="chat-av" style="background:linear-gradient(135deg,var(--primary),var(--secondary))">${initials}</div>
            <div>
                <div class="chat-name">${name}${badge}</div>
                <div class="chat-preview">${preview}</div>
            </div>
        `;
        return div;
    }

    function selectChat(type, id, name, initials, element = null) {
        activeChat = { type, id, name, initials };
        
        // UI Updates
        document.querySelectorAll('.chat-item').forEach(el => el.classList.remove('active'));
        if (element) {
            element.classList.add('active');
        } else {
            // Find element if not provided (e.g. from auto-select)
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
        
        let statusHtml = type === 'group' ? '<i class="uil uil-users-alt me-1"></i> Group Chat' : '<i class="uil uil-circle me-1" style="font-size:8px; color: #10B981"></i> Online';
        
        // Add group info to status if available (for supervisors)
        if (type === 'private' && element) {
            const badge = element.querySelector('.badge');
            if (badge) {
                statusHtml += ` <span class="badge badge-blue" style="font-size: 10px; padding: 2px 6px;">${badge.innerText}</span>`;
            }
        }
        
        document.getElementById('active-chat-status').innerHTML = statusHtml;
        
        loadMessages();
        
        // Setup refresh interval
        if (refreshInterval) clearInterval(refreshInterval);
        refreshInterval = setInterval(loadMessages, 3000); // Refresh every 3 seconds
    }

    function loadMessages() {
        if (!activeChat) return;
        
        fetch(`/messages/${activeChat.type}/${activeChat.id}`)
            .then(response => response.json())
            .then(messages => {
                const container = document.getElementById('chat-messages');
                const wasAtBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 100;
                
                container.innerHTML = '';
                messages.forEach(msg => {
                    const isMe = msg.sender_id == {{ auth()->id() }};
                    const div = document.createElement('div');
                    div.className = `msg ${isMe ? 'me' : 'them'}`;
                    
                    const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
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

                    div.innerHTML = `
                        <div class="msg-bubble">
                            ${msg.content}
                            ${attachmentHtml}
                        </div>
                        <div class="msg-time">${senderName} · ${time}</div>
                    `;
                    container.appendChild(div);
                });
                
                if (wasAtBottom) {
                    container.scrollTop = container.scrollHeight;
                }
            });
    }

    function handleKey(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSend();
        }
    }

    function handleSend() {
        const input = document.getElementById('chat-input-field');
        const content = input.value.trim();
        
        if (!content && selectedFiles.length === 0 || !activeChat) return;
        
        const formData = new FormData();
        formData.append('content', content);
        formData.append('type', activeChat.type);
        formData.append('target_id', activeChat.id);
        
        selectedFiles.forEach((file, index) => {
            formData.append(`attachments[${index}]`, file);
        });

        // Reset input immediately for better UX
        input.value = '';
        input.style.height = 'auto';
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
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(message => {
            loadMessages();
        })
        .catch(error => {
            console.error('Error sending message:', error);
            const msg = error.error || error.message || 'Failed to send message';
            toast(msg, '<i class="uil uil-exclamation-circle"></i>');
        });
    }
</script>
@endpush
