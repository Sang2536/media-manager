@extends('layouts.app')

@section('title', 'Guide')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
            🗓️ Hướng dẫn hệ thống
        </h1>
        <p class="text-gray-500">Hướng dẫn nhanh về thư mục, media, thẻ và metadata</p>
    </div>

    <div class="grid gap-4 mb-8">
        <div id="tabbed-folder-editor">
            {{-- Tabs Button --}}
            <div class="border-b border-gray-200 mb-4">
                <nav class="flex space-x-2">
                    <button type="button" data-tab="guide-general" class="tab-btn">
                        <span class="icon">🌐</span> General
                    </button>
                    <button type="button" data-tab="guide-active-area" class="tab-btn">
                        <span class="icon">️🖱</span> Drag & Drop
                    </button>
                    <button type="button" data-tab="guide-folder" class="tab-btn">
                        <span class="icon">📂</span> Folder
                    </button>
                    <button type="button" data-tab="guide-file" class="tab-btn">
                        <span class="icon">🖼️</span> File
                    </button>
                </nav>
            </div>

            {{-- Hidden input để lưu tab đang chọn --}}
            <input type="hidden" name="active_tab" id="active-tab-input" value="{{ old('active_tab', 'guide-general') }}">

            {{-- Tab Content --}}
            <div class="tab-wrapper relative">
                {{-- General Tab --}}
                <div class="tab-content transition-tab" id="tab-guide-general">
                    <div class="py-2 space-y-2 grid grid-cols-3 gap-2">
                        <div>
                            <h3>User & Role</h3>
                            <div><span class="icon">️👤</span> User</div>
                            <div><span class="icon">👥</span> User group</div>
                            <div><span class="icon">👑</span> Admin</div>
                            <div><span class="icon">️🔒</span> Lock</div>
                            <div><span class="icon">🔑</span> Password</div>
                            <div><span class="icon">🔐</span> Auth</div>
                            <div><span class="icon">🛡️</span> Protected</div>
                            <div><span class="icon">🏛️</span> Role</div>
                            <div><span class="icon">☁️</span> Cloud</div>
                        </div>
                        <div>
                            <h3>Dashboard</h3>
                            <div><span class="icon">🎨</span> Dashboard</div>
                            <div><span class="icon">️📊</span> Overview / Export</div>
                            <div><span class="icon">️📁</span> Folder</div>
                            <div><span class="icon">️🖼️</span> File</div>
                            <div><span class="icon">️🏷️</span> Tag</div>
                            <div><span class="icon">️📄</span> Metadata</div>
                            <div><span class="icon">️🕒</span> Recently / Time</div>
                            <div><span class="icon">️📜</span> Logs</div>
                            <div><span class="icon">️⚙️</span> Setting</div>
                            <div><span class="icon">️🧰</span> Tools</div>
                        </div>
                        <div>
                            <h3>Action</h3>
                            <div><span class="icon">️➕</span> Add / Create / New</div>
                            <div><span class="icon">️✏️</span> Edit / Update</div>
                            <div><span class="icon">️👁️</span> Watch / Show</div>
                            <div><span class="icon">️🗑️</span> Delete / Drop / Destroy</div>
                            <div><span class="icon">💾</span> Save</div>
                            <div><span class="icon">️🔗</span> Link / Share</div>
                            <div><span class="icon">📋</span> Copy</div>
                            <div><span class="icon">🔄</span> Refresh</div>
                            <div><span class="icon">️📤</span> Upload</div>
                            <div><span class="icon">📥</span> Download</div>
                            <div><span class="icon">🖨️</span> Print</div>
                            <div><span class="icon">🔍</span> Search</div>
                            <div><span class="icon">️🔎</span> Zoom</div>
                            <div><span class="icon">️🖱</span> Drag & Drop</div>
                            <div><span class="icon">️📌</span> Mark</div>
                            <div><span class="icon">️🚀</span> Starting</div>
                            <div><span class="icon">️🔙</span> Back</div>
                            <div><span class="icon">️🧹</span> Filter</div>
                        </div>
                        <div>
                            <h3>Status</h3>
                            <div><span class="icon">️🌐</span> General / Public</div>
                            <div><span class="icon">️❓</span> Help</div>
                            <div><span class="icon">️ℹ️</span> Info</div>
                            <div><span class="icon">️⚠️</span> Warning</div>
                            <div><span class="icon">️❌</span> Error / Close</div>
                            <div><span class="icon">️✅</span> Correct / Checked</div>
                            <div><span class="icon">️⏳</span> Processing</div>
                            <div><span class="icon">🚫</span> Locked / Ban</div>
                            <div><span class="icon">🟢</span> Online</div>
                            <div><span class="icon">📢</span> Notification</div>
                            <div><span class="icon">💬</span> Comment</div>
                            <div><span class="icon">❤️</span> Favorite</div>
                            <div><span class="icon">📋</span> List</div>
                            <div><span class="icon">🟦</span> Grid</div>
                        </div>
                        <div>
                            <h3>Input</h3>
                            <div><span class="icon">📝</span> Input</div>
                            <div><span class="icon">🔽</span> Dropdown</div>
                            <div><span class="icon">️📅</span> Calender</div>
                            <div><span class="icon">📧</span> Email</div>
                            <div><span class="icon">✉️</span> Message</div>
                        </div>
                    </div>
                </div>

                {{-- Drag & Drop Tab --}}
                <div class="tab-content transition-tab hidden" id="tab-guide-active-area">
                    <div class="py-2 space-y-2">
                        Active Area
                    </div>
                </div>

                {{-- Folder Tab --}}
                <div class="tab-content transition-tab hidden" id="tab-guide-folder">
                    <div class="py-2 space-y-2">
                        Folder
                    </div>
                </div>

                {{-- File Tab --}}
                <div class="tab-content transition-tab hidden" id="tab-guide-file">
                    <div class="py-2 space-y-2">
                        File
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
