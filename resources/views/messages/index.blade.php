@extends('layouts.app')

@section('breadcrumb', 'Messages')

@section('content')
<div class="page active" id="page-messages">
    <div class="section-header">
        <div><div class="section-title">Messages</div><div class="section-sub">Real-time team communication</div></div>
        <button class="btn btn-primary btn-sm" onclick="toast('New chat created!','<i class=\'uil uil-comment-add\'></i>')"><i class="uil uil-comment-add me-1"></i> New Chat</button>
    </div>
    <div class="chat-layout card" style="padding:0">
        <div class="chat-sidebar">
            <div class="chat-search"><input class="form-control" style="font-size:12.5px;padding:8px 12px" placeholder="🔍 Search chats…"/></div>
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
            <div class="chat-input-area">
                <button class="btn btn-ghost" style="font-size:18px;padding:0"><i class="uil uil-smile"></i></button>
                <input class="chat-input" placeholder="Type a message… (Press Enter to send)" id="chat-input-field" onkeydown="handleKey(event)"/>
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

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const autoType = urlParams.get('type');
        const autoId = urlParams.get('id');
        
        loadChats(autoType, autoId);
    });

    function loadChats(autoType = null, autoId = null) {
        fetch('/messages/chats')
            .then(response => response.json())
            .then(data => {
                const chatList = document.getElementById('chat-list');
                chatList.innerHTML = '';

                if (data.groups.length === 0 && data.users.length === 0) {
                    chatList.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 13px;">No active chats found</div>';
                    return;
                }

                let targetItem = null;

                // Add Groups
                data.groups.forEach(group => {
                    const initials = group.name.substring(0, 2).toUpperCase();
                    const item = createChatItem('group', group.id, group.name, 'Group Chat', initials);
                    chatList.appendChild(item);
                    
                    if (autoType === 'group' && autoId == group.id) {
                        targetItem = { type: 'group', id: group.id, name: group.name, initials: initials, element: item };
                    }
                });

                // Add Private Chats
                data.users.forEach(user => {
                    const initials = user.initials || user.name.substring(0, 2).toUpperCase();
                    const groupBadge = user.group_name ? `<span class="badge badge-blue" style="font-size: 9px; margin-left: 5px; padding: 2px 5px;">${user.group_name}</span>` : '';
                    const item = createChatItem('private', user.id, user.name, 'Private Message', initials, groupBadge);
                    chatList.appendChild(item);

                    if (autoType === 'private' && autoId == user.id) {
                        targetItem = { type: 'private', id: user.id, name: user.name, initials: initials, element: item };
                    }
                });

                // Auto-select if requested
                if (targetItem) {
                    selectChat(targetItem.type, targetItem.id, targetItem.name, targetItem.initials, targetItem.element);
                }
            });
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
                    
                    div.innerHTML = `
                        <div class="msg-bubble">${msg.content}</div>
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
        if (e.key === 'Enter') handleSend();
    }

    function handleSend() {
        const input = document.getElementById('chat-input-field');
        const content = input.value.trim();
        
        if (!content || !activeChat) return;
        
        input.value = '';
        
        fetch('/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                content: content,
                type: activeChat.type,
                target_id: activeChat.id
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(message => {
            loadMessages(); // Reload messages to show the new one
        })
        .catch(error => {
            console.error('Error sending message:', error);
            const msg = error.error || error.message || 'Failed to send message';
            toast(msg, '<i class="uil uil-exclamation-circle"></i>');
        });
    }
</script>
@endpush
