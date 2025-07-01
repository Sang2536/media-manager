<div class="relative z-10" id="showFolderModal" aria-labelledby="dialog-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <form action="{{ route('media-folders.update', $folder) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <header class="flex items-center justify-between bg-gray-50 px-4 py-3 sm:px-6 border-b border-gray-200 h-14">
                        <div class="text-xl font-semibold truncate max-w-[80%]">
                            {{ $folder->name }} Info
                        </div>

                        <x-button type="button" name-btn="x"
                                class="text-xl font-bold text-gray-400 hover:text-red-500 focus:outline-none"
                                onclick="closeModal()" />
                    </header>

                    <div class="max-h-[80vh] overflow-y-auto p-4">
                        <div class="space-y-6">
                            <!-- Header -->
                            <div class="flex items-center gap-3">
                                <img src="/icons/folder-blue.png" class="w-12 h-12" alt="Folder Icon">
                                <div class="text-xl font-semibold">{{ $folder->name }}</div>
                                <div class="ml-auto text-sm text-gray-500">{{ $count['size'] }}</div>
                            </div>

                            <!-- General Info -->
                            <div>
                                <h3 class="font-medium text-gray-700 mb-1">General:</h3>
                                <div class="text-sm space-y-1 text-gray-800">
                                    <div><strong>Kind:</strong> {{ $folder->kind }}</div>
                                    <div><strong>Size:</strong> {{ $count['size'] }} for {{ $count['folders'] + $count['files'] }} items ({{ $count['folders'] }} folders & {{ $count['files'] }} files) </div>
                                    <div><strong>Where:</strong>
                                        @if ($breadcrumbs)
                                            <x-breadcrumb
                                                :breadcrumbs="$breadcrumbs"
                                                :view-mode="$view"
                                                separate="▸"
                                                :route-action="[
                                                    'index' => route('media-folders.index', ['view' => $view])
                                                ]"
                                            />
                                        @else
                                            <span class="text-gray-500">Root Folder</span>
                                        @endif
                                    </div>
                                    <div><strong>Created:</strong> {{ $folder->created_at }}</div>
                                    <div><strong>Modified:</strong> {{ $folder->updated_at ?? 'No' }}</div>

                                    <div class="space-x-4 mt-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="is_shared" class="form-checkbox" {{ $folder->is_shared ? 'checked' :  ''}}>
                                            <span class="ml-2">Shared folder</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="is_locked" class="form-checkbox" {{ $folder->is_locked ? 'checked' :  ''}}>
                                            <span class="ml-2">Locked</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- More Info -->
                            <div>
                                <h3 class="font-medium text-gray-700 mb-1">More Info:</h3>
                                <div class="text-sm text-gray-800">Last opened: {{ $folder->last_opened_at ?? $folder->updated_at }}</div>
                            </div>

                            <!-- Name & Extension -->
                            <div>
                                <h3 class="font-medium text-gray-700 mb-1">Name & Extension:</h3>
                                <input type="text" name="folder_name" class="w-full border rounded px-3 py-1 text-sm" value="{{ $folder->name }}" />
                            </div>

                            <!-- Comments -->
                            <div>
                                <h3 class="font-medium text-gray-700 mb-1">Comments:</h3>
                                <textarea name="comments" class="w-full border rounded px-3 py-2 text-sm" rows="2">{{ $folder->comments }}</textarea>
                            </div>

                            <!-- Sharing & Permissions -->
                            <div>
                                <h3 class="font-medium text-gray-700 mb-1">Sharing & Permissions:</h3>
                                <div class="text-sm text-gray-800 mb-2">You can read and write</div>

                                <div class="border border-gray-300 rounded">
                                    <div class="grid grid-cols-2 bg-gray-100 font-medium px-3 py-2 border-b">
                                        <div>Name</div>
                                        <div>Privilege</div>
                                    </div>
                                    <div class="grid grid-cols-2 px-3 py-2 border-b">
                                        <div>🙍‍♂️ {{ $folder->user->name }}</div>
                                        <div>Read & Write</div>
                                    </div>
                                    <div class="grid grid-cols-2 px-3 py-2 border-b">
                                        <div>👥 staff</div>
                                        <div>Read only</div>
                                    </div>
                                    <div class="grid grid-cols-2 px-3 py-2">
                                        <div>👥 everyone</div>
                                        <div>Read only</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <footer class="flex items-center justify-end bg-gray-50 px-4 py-3 sm:px-6 border-t border-gray-200 h-14 space-x-3">
                        <x-button type="submit" name-btn="💾 Update" class="inline-flex justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-blue-500" />

                        <x-button type="button" name-btn="Close"
                            class="inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50"
                            onclick="closeModal()" />
                    </footer>
                </div>
            </form>
        </div>
    </div>
</div>
