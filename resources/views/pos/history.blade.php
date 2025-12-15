<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - Resto POS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>

<!-- TOP NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">
            <i class="fas fa-utensils me-2"></i>Resto POS
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">
                        <i class="fas fa-cash-register me-1"></i> Order
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ url('/pos/history') }}">
                        <i class="fas fa-history me-1"></i> History
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">
    <!-- PAGE: HISTORY -->
    <div id="page-history">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold"><i class="fas fa-clock me-2"></i>Transaction History</h4>
            <button class="btn btn-outline-primary" onclick="fetchHistory()"><i class="fas fa-sync-alt"></i> Refresh</button>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div id="history-list" class="row g-3">
                    <!-- Injected via JS -->
                    <div class="text-center py-5 text-muted">Loading history...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    async function fetchHistory() {
        try {
            const res = await fetch('{{ url("/api/orders") }}');
            let orders = await res.json();
            orders = orders.reverse(); // Newest first
            renderHistory(orders);
        } catch (e) {
            console.error(e);
            document.getElementById('history-list').innerHTML = '<p class="text-center text-danger">Failed to load history.</p>';
        }
    }

    async function requestPrint(orderId) {
       try {
           const res = await fetch(`{{ url("/api/orders") }}/${orderId}/print`, { method: 'POST' });
           if(res.ok) showToast('Print command sent!', 'success');
           else showToast('Print failed (Check server logs)', 'danger');
       } catch (e) {
           showToast('Print connection error', 'danger');
       }
    }

    function renderHistory(orders) {
        const html = orders.map(order => `
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Order #${order.id}</span>
                        <span class="badge bg-success">PAID</span>
                    </div>
                    <div class="card-body">
                         <p class="small text-muted mb-2"><i class="far fa-calendar-alt me-1"></i> ${new Date(order.created_at).toLocaleString()}</p>
                        <div class="bg-light p-2 rounded mb-2" style="max-height: 150px; overflow-y: auto;">
                            ${order.order_items.map(i => `
                                <div class="d-flex justify-content-between small">
                                    <span>${i.quantity}x ${i.menu_name}</span>
                                    <span>Rp ${Number(i.subtotal).toLocaleString()}</span>
                                </div>
                            `).join('')}
                        </div>
                        <div class="d-flex justify-content-between align-items-center fw-bold">
                             <span>TOTAL</span>
                             <span>Rp ${Number(order.total_amount).toLocaleString()}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 text-end">
                        <button class="btn btn-sm btn-dark" onclick="requestPrint(${order.id})">
                             <i class="fas fa-print me-1"></i> Reprint Receipt
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        document.getElementById('history-list').innerHTML = html || '<div class="col-12 text-center text-muted">No history found.</div>';
    }

    // Init
    fetchHistory();
</script>
<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container"></div>

<script>
    function showToast(message, type = 'primary') {
        const container = document.getElementById('toast-container');
        const id = 'toast-' + Date.now();
        
        let icon = 'info-circle';
        if(type === 'success') icon = 'check-circle';
        if(type === 'danger') icon = 'exclamation-triangle';
        if(type === 'warning') icon = 'exclamation-circle';

        const html = `
            <div id="${id}" class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${icon} me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        const temp = document.createElement('div');
        temp.innerHTML = html;
        const toastEl = temp.firstElementChild;
        container.appendChild(toastEl);
        
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
        
        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.remove();
        });
    }
     // Override generic alert just in case
    window.alert = function(msg) { showToast(msg, 'warning'); }
</script>
</body>
</html>
