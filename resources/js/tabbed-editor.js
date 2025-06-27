
//  Tab Folder
document.addEventListener('DOMContentLoaded', function () {
    const style = document.createElement('style');
    style.textContent = `
            #tabbed-folder-editor .tab-btn {
                padding: 0.5rem 0.75rem;
                font-weight: 500;
                color: #4B5563;
                border-bottom: 2px solid transparent;
                border-radius: 0.375rem;
                background-color: transparent;
                transition: all 0.2s ease-in-out;
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
            }

            #tabbed-folder-editor .tab-btn .icon {
                transition: color 0.2s ease-in-out;
            }

            #tabbed-folder-editor .tab-btn.active-tab {
                color: #2563EB;
                border-color: #2563EB;
                background-color: #EFF6FF;
            }

            #tabbed-folder-editor .tab-btn.active-tab .icon {
                color: #2563EB;
            }

            .tab-content {
                transition: opacity 0.3s ease, transform 0.3s ease;
                opacity: 0;
                transform: translateY(10px);
                pointer-events: none;
                position: absolute;
                width: 100%;
            }

            .tab-content.active-content {
                opacity: 1;
                transform: translateY(0);
                pointer-events: auto;
                position: relative;
            }

            .tab-wrapper {
                position: relative;
                min-height: 220px;
            }
        `;
    document.head.appendChild(style);

    const tabs = document.querySelectorAll('#tabbed-folder-editor .tab-btn');
    const contents = document.querySelectorAll('#tabbed-folder-editor .tab-content');
    const hiddenInput = document.getElementById('active-tab-input');

    let activeTab = hiddenInput?.value || 'breadcrumb';

    const activateTab = (tabId) => {
        tabs.forEach(t => {
            t.classList.remove('active-tab');
            if (t.getAttribute('data-tab') === tabId) {
                t.classList.add('active-tab');
            }
        });

        contents.forEach(c => {
            c.classList.remove('active-content');
            c.classList.add('hidden');
        });

        const showEl = document.getElementById(`tab-${tabId}`);
        if (showEl) {
            showEl.classList.remove('hidden');
            // Trigger animation next frame
            requestAnimationFrame(() => {
                showEl.classList.add('active-content');
            });
        }

        if (hiddenInput) hiddenInput.value = tabId;
    };

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const selectedTab = this.getAttribute('data-tab');
            activateTab(selectedTab);
        });
    });

    activateTab(activeTab);
});

//  Tab File
