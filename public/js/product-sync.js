/**
 * Product Synchronization JavaScript
 * 
 * Handles real-time product data synchronization across the frontend
 * Listens for product updates and automatically refreshes UI components
 */

class ProductSyncManager {
    constructor() {
        this.apiBaseUrl = '/api/products';
        this.wsConnection = null;
        this.eventListeners = new Map();
        this.syncQueue = [];
        this.isProcessing = false;
        this.retryAttempts = 3;
        this.retryDelay = 1000;
        
        this.init();
    }

    /**
     * Initialize the sync manager
     */
    init() {
        this.setupWebSocketConnection();
        this.setupEventListeners();
        this.setupPeriodicSync();
        this.bindUIEvents();
    }

    /**
     * Setup WebSocket connection for real-time updates
     */
    setupWebSocketConnection() {
        if (typeof Echo !== 'undefined') {
            // Listen for product data sync events
            Echo.channel('products.list')
                .listen('ProductDataSynced', (e) => {
                    this.handleProductUpdate(e.product, e.changes, e.sync_results);
                });

            Echo.channel('inventory.updates')
                .listen('ProductDataSynced', (e) => {
                    this.handleInventoryUpdate(e.product, e.changes);
                });

            Echo.channel('dashboard.stats')
                .listen('ProductDataSynced', (e) => {
                    this.handleDashboardUpdate(e.product, e.changes);
                });
        }
    }

    /**
     * Setup event listeners for product changes
     */
    setupEventListeners() {
        // Listen for form submissions
        document.addEventListener('submit', (e) => {
            if (e.target.matches('.product-form, .inventory-form, .price-form')) {
                this.handleFormSubmission(e);
            }
        });

        // Listen for input changes
        document.addEventListener('input', (e) => {
            if (e.target.matches('.product-field[data-sync="true"]')) {
                this.queueFieldUpdate(e.target);
            }
        });

        // Listen for AJAX complete events
        $(document).ajaxComplete((event, xhr, settings) => {
            if (settings.url && settings.url.includes('/products/')) {
                this.handleAjaxProductUpdate(xhr, settings);
            }
        });
    }

    /**
     * Setup periodic synchronization
     */
    setupPeriodicSync() {
        // Sync every 30 seconds for critical data
        setInterval(() => {
            this.syncVisibleProducts();
        }, 30000);

        // Full sync every 5 minutes
        setInterval(() => {
            this.performFullSync();
        }, 300000);
    }

    /**
     * Bind UI events for manual sync triggers
     */
    bindUIEvents() {
        // Sync button clicks
        $(document).on('click', '.sync-product-btn', (e) => {
            e.preventDefault();
            const productId = $(e.target).data('product-id');
            if (productId) {
                this.syncProduct(productId);
            }
        });

        // Sync all button
        $(document).on('click', '.sync-all-btn', (e) => {
            e.preventDefault();
            this.syncAllProducts();
        });

        // Refresh data button
        $(document).on('click', '.refresh-data-btn', (e) => {
            e.preventDefault();
            const productId = $(e.target).data('product-id');
            if (productId) {
                this.refreshProductData(productId);
            }
        });
    }

    /**
     * Handle form submission with automatic sync
     */
    handleFormSubmission(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const productId = formData.get('product_id') || form.dataset.productId;

        if (!productId) {
            console.warn('No product ID found for form submission');
            return;
        }

        this.showSyncIndicator(productId, 'Saving...');

        // Convert FormData to object
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }

        // Submit with sync
        this.updateProductWithSync(productId, data)
            .then(response => {
                this.showSyncIndicator(productId, 'Saved', 'success');
                this.updateUIComponents(response.data.product, response.data.sync_results);
                
                // Show success message
                this.showNotification('Product updated successfully', 'success');
            })
            .catch(error => {
                this.showSyncIndicator(productId, 'Error', 'error');
                this.showNotification('Failed to update product: ' + error.message, 'error');
            });
    }

    /**
     * Queue field update for batch processing
     */
    queueFieldUpdate(field) {
        const productId = field.dataset.productId;
        const fieldName = field.name;
        const value = field.value;

        if (!productId || !fieldName) return;

        // Add to queue
        this.syncQueue.push({
            productId: productId,
            field: fieldName,
            value: value,
            timestamp: Date.now()
        });

        // Process queue after delay
        clearTimeout(this.queueTimeout);
        this.queueTimeout = setTimeout(() => {
            this.processQueue();
        }, 1000);
    }

    /**
     * Process the sync queue
     */
    async processQueue() {
        if (this.isProcessing || this.syncQueue.length === 0) return;

        this.isProcessing = true;

        // Group by product ID
        const groupedUpdates = this.syncQueue.reduce((acc, item) => {
            if (!acc[item.productId]) {
                acc[item.productId] = {};
            }
            acc[item.productId][item.field] = item.value;
            return acc;
        }, {});

        // Clear queue
        this.syncQueue = [];

        // Process each product
        for (const [productId, data] of Object.entries(groupedUpdates)) {
            try {
                await this.updateProductWithSync(productId, data);
                this.showSyncIndicator(productId, 'Synced', 'success');
            } catch (error) {
                this.showSyncIndicator(productId, 'Error', 'error');
                console.error('Failed to sync product:', productId, error);
            }
        }

        this.isProcessing = false;
    }

    /**
     * Sync a specific product
     */
    async syncProduct(productId, changes = {}, original = {}) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/${productId}/sync`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ changes, original })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (data.success) {
                this.handleSyncSuccess(productId, data.data);
                return data;
            } else {
                throw new Error(data.message || 'Sync failed');
            }
        } catch (error) {
            this.handleSyncError(productId, error);
            throw error;
        }
    }

    /**
     * Update product with automatic sync
     */
    async updateProductWithSync(productId, data) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/${productId}/update-with-sync`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success) {
                return result;
            } else {
                throw new Error(result.message || 'Update failed');
            }
        } catch (error) {
            console.error('Update with sync failed:', error);
            throw error;
        }
    }

    /**
     * Sync all products
     */
    async syncAllProducts() {
        try {
            this.showGlobalSyncIndicator('Syncing all products...');
            
            const response = await fetch(`${this.apiBaseUrl}/sync-all`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (data.success) {
                this.showGlobalSyncIndicator('All products synced', 'success');
                this.showNotification(`Synced ${data.data.synchronized} of ${data.data.total_products} products`, 'success');
                
                // Refresh current page data
                this.refreshCurrentPageData();
            } else {
                throw new Error(data.message || 'Bulk sync failed');
            }
        } catch (error) {
            this.showGlobalSyncIndicator('Sync failed', 'error');
            this.showNotification('Failed to sync all products: ' + error.message, 'error');
        }
    }

    /**
     * Get real-time product data
     */
    async getRealtimeData(productId) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/${productId}/realtime`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            return data.success ? data.data : null;
        } catch (error) {
            console.error('Failed to get realtime data:', error);
            return null;
        }
    }

    /**
     * Handle product update from WebSocket
     */
    handleProductUpdate(product, changes, syncResults) {
        console.log('Product updated via WebSocket:', product.id, changes);
        
        // Update UI components
        this.updateUIComponents(product, syncResults);
        
        // Show notification for significant changes
        if (changes.price || changes.stock || changes.status) {
            this.showNotification(`Product "${product.name}" has been updated`, 'info');
        }
    }

    /**
     * Handle inventory update
     */
    handleInventoryUpdate(product, changes) {
        if (changes.stock !== undefined) {
            this.updateStockDisplays(product.id, product.stock);
        }
    }

    /**
     * Handle dashboard update
     */
    handleDashboardUpdate(product, changes) {
        // Refresh dashboard widgets that show product statistics
        this.refreshDashboardWidgets();
    }

    /**
     * Update UI components with new product data
     */
    updateUIComponents(product, syncResults = {}) {
        // Update product cards
        $(`.product-card[data-product-id="${product.id}"]`).each((index, element) => {
            this.updateProductCard(element, product);
        });

        // Update product rows in tables
        $(`.product-row[data-product-id="${product.id}"]`).each((index, element) => {
            this.updateProductRow(element, product);
        });

        // Update form fields
        $(`.product-form[data-product-id="${product.id}"] input, .product-form[data-product-id="${product.id}"] select`).each((index, element) => {
            this.updateFormField(element, product);
        });

        // Update stock displays
        this.updateStockDisplays(product.id, product.stock);

        // Update price displays
        this.updatePriceDisplays(product.id, product.price);
    }

    /**
     * Update product card
     */
    updateProductCard(cardElement, product) {
        const $card = $(cardElement);
        
        $card.find('.product-name').text(product.name);
        $card.find('.product-sku').text(product.sku);
        $card.find('.product-price').text(this.formatCurrency(product.price));
        $card.find('.product-stock').text(product.stock);
        
        // Update stock status
        const stockStatus = this.getStockStatus(product.stock, product.min_stock_level);
        $card.find('.stock-status')
            .removeClass('low-stock out-of-stock in-stock')
            .addClass(stockStatus.class)
            .text(stockStatus.text);
    }

    /**
     * Update product row in table
     */
    updateProductRow(rowElement, product) {
        const $row = $(rowElement);
        
        $row.find('.product-name-cell').text(product.name);
        $row.find('.product-sku-cell').text(product.sku);
        $row.find('.product-price-cell').text(this.formatCurrency(product.price));
        $row.find('.product-stock-cell').text(product.stock);
        
        if (product.expiration_date) {
            $row.find('.product-expiry-cell').text(this.formatDate(product.expiration_date));
        }
    }

    /**
     * Update form field
     */
    updateFormField(fieldElement, product) {
        const $field = $(fieldElement);
        const fieldName = $field.attr('name');
        
        if (fieldName && product.hasOwnProperty(fieldName)) {
            if ($field.val() !== product[fieldName]) {
                $field.val(product[fieldName]);
                $field.trigger('change');
            }
        }
    }

    /**
     * Update stock displays
     */
    updateStockDisplays(productId, stock) {
        $(`.stock-display[data-product-id="${productId}"]`).each((index, element) => {
            $(element).text(stock);
        });

        // Update stock badges
        $(`.stock-badge[data-product-id="${productId}"]`).each((index, element) => {
            const $badge = $(element);
            const minStock = parseInt($badge.data('min-stock')) || 0;
            const stockStatus = this.getStockStatus(stock, minStock);
            
            $badge.removeClass('badge-success badge-warning badge-danger')
                  .addClass(stockStatus.badgeClass)
                  .text(stock);
        });
    }

    /**
     * Update price displays
     */
    updatePriceDisplays(productId, price) {
        $(`.price-display[data-product-id="${productId}"]`).each((index, element) => {
            $(element).text(this.formatCurrency(price));
        });
    }

    /**
     * Show sync indicator
     */
    showSyncIndicator(productId, message, type = 'info') {
        const $indicator = $(`.sync-indicator[data-product-id="${productId}"]`);
        
        if ($indicator.length) {
            $indicator.removeClass('sync-info sync-success sync-error')
                     .addClass(`sync-${type}`)
                     .text(message)
                     .show();
            
            if (type === 'success' || type === 'error') {
                setTimeout(() => {
                    $indicator.fadeOut();
                }, 3000);
            }
        }
    }

    /**
     * Show global sync indicator
     */
    showGlobalSyncIndicator(message, type = 'info') {
        const $indicator = $('.global-sync-indicator');
        
        if ($indicator.length) {
            $indicator.removeClass('sync-info sync-success sync-error')
                     .addClass(`sync-${type}`)
                     .text(message)
                     .show();
            
            if (type === 'success' || type === 'error') {
                setTimeout(() => {
                    $indicator.fadeOut();
                }, 5000);
            }
        }
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        // Use your existing notification system
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                text: message,
                icon: type === 'error' ? 'error' : type === 'success' ? 'success' : 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }

    /**
     * Get stock status
     */
    getStockStatus(stock, minStock = 0) {
        if (stock <= 0) {
            return {
                class: 'out-of-stock',
                badgeClass: 'badge-danger',
                text: 'Out of Stock'
            };
        } else if (stock <= minStock) {
            return {
                class: 'low-stock',
                badgeClass: 'badge-warning',
                text: 'Low Stock'
            };
        } else {
            return {
                class: 'in-stock',
                badgeClass: 'badge-success',
                text: 'In Stock'
            };
        }
    }

    /**
     * Format currency
     */
    formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount || 0);
    }

    /**
     * Format date
     */
    formatDate(dateString) {
        if (!dateString) return '';
        return new Date(dateString).toLocaleDateString();
    }

    /**
     * Sync visible products on current page
     */
    async syncVisibleProducts() {
        const visibleProductIds = [];
        
        $('.product-card[data-product-id], .product-row[data-product-id]').each((index, element) => {
            const productId = $(element).data('product-id');
            if (productId && this.isElementVisible(element)) {
                visibleProductIds.push(productId);
            }
        });

        for (const productId of visibleProductIds) {
            try {
                const realtimeData = await this.getRealtimeData(productId);
                if (realtimeData) {
                    this.updateUIComponents(realtimeData.product);
                }
            } catch (error) {
                console.error('Failed to sync visible product:', productId, error);
            }
        }
    }

    /**
     * Check if element is visible
     */
    isElementVisible(element) {
        const rect = element.getBoundingClientRect();
        return rect.top >= 0 && rect.left >= 0 && 
               rect.bottom <= window.innerHeight && 
               rect.right <= window.innerWidth;
    }

    /**
     * Refresh current page data
     */
    refreshCurrentPageData() {
        // Trigger a page refresh or reload specific components
        if (typeof window.refreshPageData === 'function') {
            window.refreshPageData();
        } else {
            // Fallback: reload the page
            window.location.reload();
        }
    }

    /**
     * Refresh dashboard widgets
     */
    refreshDashboardWidgets() {
        $('.dashboard-widget[data-refresh-url]').each((index, element) => {
            const $widget = $(element);
            const refreshUrl = $widget.data('refresh-url');
            
            if (refreshUrl) {
                $.get(refreshUrl)
                    .done((data) => {
                        $widget.html(data);
                    })
                    .fail((error) => {
                        console.error('Failed to refresh widget:', error);
                    });
            }
        });
    }
}

// Initialize the sync manager when DOM is ready
$(document).ready(() => {
    window.productSyncManager = new ProductSyncManager();
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProductSyncManager;
}