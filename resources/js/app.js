import $ from 'jquery';
window.$ = window.jQuery = $;

// Global helper for toast notifications
window.showToast = function(message, type = 'success') {
    const toastId = 'toast-' + Date.now();
    const bgColor = type === 'success' ? 'bg-teal-600' : (type === 'error' ? 'bg-rose-600' : 'bg-indigo-600');
    
    const toastHtml = `
        <div id="${toastId}" class="flex items-center w-full max-w-xs p-4 mb-4 text-white ${bgColor} rounded-xl shadow-lg transition-all duration-300 transform translate-y-2 opacity-0" role="alert">
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg bg-white/20 text-white">
                ${type === 'success' ? '✓' : '!'}
            </div>
            <div class="ms-3 text-sm font-medium leading-tight">${message}</div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 p-1.5 inline-flex items-center justify-center h-8 w-8 text-white/80 hover:text-white rounded-lg hover:bg-white/10" onclick="$('#${toastId}').remove()">
                <span class="sr-only">Close</span>
                ✕
            </button>
        </div>
    `;

    $('#toast-container').append(toastHtml);
    setTimeout(() => {
        $(`#${toastId}`).removeClass('translate-y-2 opacity-0').addClass('translate-y-0 opacity-100');
    }, 10);

    setTimeout(() => {
        $(`#${toastId}`).fadeOut(300, function() { $(this).remove(); });
    }, 4000);
};

// Document ready initializations
$(document).ready(function() {
    // Mobile sidebar toggle
    $('[data-sidebar-toggle]').on('click', function() {
        $('#sidebar-drawer').toggleClass('-translate-x-full 0');
        $('#sidebar-overlay').toggleClass('hidden block');
    });

    $('#sidebar-overlay').on('click', function() {
        $('#sidebar-drawer').addClass('-translate-x-full');
        $('#sidebar-overlay').addClass('hidden').removeClass('block');
    });

    // Permission Matrix Select All Handlers
    $('#select-all-permissions').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.permission-checkbox').prop('checked', isChecked);
        $('.module-select-all').prop('checked', isChecked);
    });

    $('.module-select-all').on('change', function() {
        const module = $(this).data('module');
        const isChecked = $(this).is(':checked');
        $(`.permission-checkbox[data-module="${module}"]`).prop('checked', isChecked);
    });
});
