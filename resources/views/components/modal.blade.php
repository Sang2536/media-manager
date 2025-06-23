<!-- Modal wrapper -->
<div id="{{ $idModalWrapper }}" class="hidden relative z-10" aria-labelledby="dialog-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen">
        <!-- Modal content -->
        <div  id="{{ $idModalContent }}" class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            {{ $slot }}
        </div>
    </div>
</div>
