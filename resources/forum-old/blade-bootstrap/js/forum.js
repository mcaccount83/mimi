const { createApp, ref, reactive, watch } = window.Vue;

document.addEventListener('DOMContentLoaded', function () {
    // Initialize Vue app
    createApp({
        setup() {
            const isCollapsed = ref(true);
            const isUserDropdownCollapsed = ref(true);

            window.addEventListener('click', event => {
                const ignore = ['navbar-toggler', 'navbar-toggler-icon', 'dropdown-toggle'];
                if (ignore.some(className => event.target.classList.contains(className))) return;
                if (!isCollapsed.value) isCollapsed.value = true;
                if (!isUserDropdownCollapsed.value) isUserDropdownCollapsed.value = true;
            });

            return {
                isCollapsed,
                isUserDropdownCollapsed,
            };
        }
    }).mount('.v-navbar');

    // Modal handling
    const mask = document.querySelector('.mask');

    function findModal(key) {
        const modal = document.querySelector(`[data-modal="${key}"]`);
        if (!modal) {
            console.error(`Attempted to open modal '${key}' but no such modal found.`);
            return null;
        }
        return modal;
    }

    function openModal(modal) {
        if (!modal) return;

        modal.style.display = 'block';
        mask.style.display = 'block';

        // Force a reflow before adding the show class
        modal.offsetHeight;

        modal.classList.add('show');
        mask.classList.add('show');
    }

    function closeModal(modal) {
        modal.classList.remove('show');
        mask.classList.remove('show');

        setTimeout(function() {
            modal.style.display = 'none';
            mask.style.display = 'none';
        }, 200);
    }

    // Modal open buttons
    document.querySelectorAll('[data-open-modal]').forEach(item => {
        item.addEventListener('click', event => {
            event.preventDefault();
            const modalKey = event.currentTarget.dataset.openModal;
            const modal = findModal(modalKey);
            openModal(modal);
        });
    });

    // Modal close buttons
    document.querySelectorAll('[data-modal]').forEach(modal => {
        modal.addEventListener('click', event => {
            if (!event.target.hasAttribute('data-close-modal')) return;
            closeModal(modal);
        });
    });

    // Dismiss buttons
    document.querySelectorAll('[data-dismiss]').forEach(item => {
        item.addEventListener('click', event => {
            event.currentTarget.parentElement.style.display = 'none';
        });
    });

    // Handle modal opening from URL hash
    const hash = window.location.hash.substr(1);
    if (hash.startsWith('modal=')) {
        const modalKey = hash.replace('modal=', '');
        const modal = findModal(modalKey);
        if (modal) openModal(modal);
    }

    // Initialize Feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Initialize color picker
    const input = document.querySelector('input[name=color_light_mode]');
    if (input && typeof Pickr !== 'undefined') {
        const pickr = Pickr.create({
            el: '.pickr',
            theme: 'classic',
            default: input.value || null,
            swatches: [
                window.defaultCategoryColor,
                '#f44336',
                '#e91e63',
                '#9c27b0',
                '#673ab7',
                '#3f51b5',
                '#2196f3',
                '#03a9f4',
                '#00bcd4',
                '#009688',
                '#4caf50',
                '#8bc34a',
                '#cddc39',
                '#ffeb3b',
                '#ffc107'
            ],
            components: {
                preview: true,
                hue: true,
                interaction: {
                    input: true,
                    save: true
                }
            },
            strings: {
                save: 'Apply'
            }
        });

        pickr
            .on('save', instance => pickr.hide())
            .on('clear', instance => {
                input.value = '';
                input.dispatchEvent(new Event('change'));
            })
            .on('cancel', instance => {
                const selectedColor = instance
                    .getSelectedColor()
                    .toHEXA()
                    .toString();
                input.value = selectedColor;
                input.dispatchEvent(new Event('change'));
            })
            .on('change', (color, instance) => {
                const selectedColor = color
                    .toHEXA()
                    .toString();
                input.value = selectedColor;
                input.dispatchEvent(new Event('change'));
            });
    }
});
