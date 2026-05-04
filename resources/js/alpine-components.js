export function registerAlpineComponents(Alpine) {
    // Drives the mobile nav toggle used in the shared layout.
    Alpine.data('navigationMenu', () => ({
        open: false,
        toggle() {
            this.open = ! this.open;
        },
        close() {
            this.open = false;
        },
    }));

    // Shared open/close state for dropdowns such as the profile menu.
    Alpine.data('dropdownMenu', () => ({
        open: false,
        toggle() {
            this.open = ! this.open;
        },
        close() {
            this.open = false;
        },
    }));

    // Reusable modal state with optional focus trapping for accessibility.
    Alpine.data('modalDialog', ({ name, show = false, focusable = false } = {}) => ({
        name,
        show,
        focusable,
        init() {
            this.$watch('show', (value) => {
                if (value) {
                    document.body.classList.add('overflow-y-hidden');

                    if (this.focusable) {
                        window.setTimeout(() => {
                            this.firstFocusable()?.focus();
                        }, 100);
                    }

                    return;
                }

                document.body.classList.remove('overflow-y-hidden');
            });
        },
        focusables() {
            const selector = 'a, button, input:not([type="hidden"]), textarea, select, details, [tabindex]:not([tabindex="-1"])';

            return [...this.$el.querySelectorAll(selector)].filter((element) => ! element.hasAttribute('disabled'));
        },
        firstFocusable() {
            return this.focusables()[0];
        },
        lastFocusable() {
            return this.focusables().slice(-1)[0];
        },
        nextFocusable() {
            return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable();
        },
        prevFocusable() {
            return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable();
        },
        nextFocusableIndex() {
            return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1);
        },
        prevFocusableIndex() {
            return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1;
        },
        handleOpen(event) {
            if (event.detail === this.name) {
                this.show = true;
            }
        },
        handleClose(event) {
            if (event.detail === this.name) {
                this.show = false;
            }
        },
        close() {
            this.show = false;
        },
        focusNext() {
            this.nextFocusable()?.focus();
        },
        focusPrevious() {
            this.prevFocusable()?.focus();
        },
    }));

    // Small helper for buttons and links that dispatch modal open/close events.
    Alpine.data('modalTrigger', (name = null) => ({
        open() {
            if (name) {
                this.$dispatch('open-modal', name);
            }
        },
        close() {
            this.$dispatch('close');
        },
    }));

    // Keeps the owner order form's service list in sync with the selected shop.
    Alpine.data('ownerOrderForm', ({
        selectedShopId = '',
        selectedShopServiceId = '',
        serviceMode = 'walk_in',
        shopServiceOptions = [],
    } = {}) => ({
        selectedShopId,
        selectedShopServiceId,
        serviceMode,
        shopServiceOptions,
        filteredShopServices() {
            return this.shopServiceOptions.filter((option) => this.selectedShopId === '' || String(option.shop_id) === String(this.selectedShopId));
        },
        needsDelivery() {
            return this.serviceMode === 'delivery_only';
        },
        syncSelectedShopService() {
            const hasSelectedService = this.filteredShopServices().some((option) => String(option.id) === String(this.selectedShopServiceId));

            if (! hasSelectedService) {
                this.selectedShopServiceId = '';
            }
        },
    }));

    // Updates price and address field visibility in the customer order form.
    Alpine.data('customerOrderForm', ({ selectedPrice = '0.00', serviceMode = 'both' } = {}) => ({
        selectedPrice,
        serviceMode,
        needsPickup() {
            return ['pickup_only', 'both'].includes(this.serviceMode);
        },
        needsDelivery() {
            return ['delivery_only', 'both'].includes(this.serviceMode);
        },
        updateSelectedPrice(event) {
            this.selectedPrice = event.target.selectedOptions[0]?.dataset.price ?? '0.00';
        },
    }));

    // Auto-hides short-lived success and status messages after page load.
    Alpine.data('flashMessage', () => ({
        show: true,
        init() {
            window.setTimeout(() => {
                this.show = false;
            }, 2000);
        },
    }));
}

export function registerDomHandlers() {
    // Prevent duplicate listeners when Vite reloads or the module is imported again.
    if (window.__bubblelinkConfirmSubmitRegistered) {
        return;
    }

    // Supports declarative confirm dialogs through data-confirm-submit on forms.
    document.addEventListener('submit', (event) => {
        const form = event.target instanceof HTMLFormElement ? event.target : null;

        if (! form) {
            return;
        }

        const message = form.dataset.confirmSubmit;

        if (message && ! window.confirm(message)) {
            event.preventDefault();
        }
    });

    window.__bubblelinkConfirmSubmitRegistered = true;
}