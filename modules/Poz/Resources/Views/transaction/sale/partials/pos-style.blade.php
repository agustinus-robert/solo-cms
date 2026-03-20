<style>
    .pos-left-section .row.g-2 > div {
        transition: all 0.3s ease-in-out;
        opacity: 1;
        transform: scale(1);
    }

    .product-hidden {
        opacity: 0 !important;
        transform: scale(0.9) !important;
        pointer-events: none;
        display: none !important;
    }
    .pos-main-layout {
        display: flex;
        gap: 15px;
        align-items: flex-start;
        padding: 10px;
    }

    .pos-left-section {
        width: 65%;
    }
    .pos-card-wrapper-manual {
        height: 700px;
    }
    .scroll-y-products-manual {
        height: 600px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .pos-right-sidebar {
        width: 35%;
        min-width: 380px;
    }
    .card-keranjang-manual {
        height: 450px;
    }
    .cart-body-scroll-manual {
        height: 380px;
        overflow-y: auto;
    }

    .table-fixed-cart {
        table-layout: fixed;
        width: 100%;
    }
    .col-produk { width: 50%; }
    .col-qty    { width: 20%; }
    .col-total  { width: 30%; }

    .scroll-y-products-manual::-webkit-scrollbar,
    .cart-body-scroll-manual::-webkit-scrollbar {
        width: 6px;
    }
    .scroll-y-products-manual::-webkit-scrollbar-thumb,
    .cart-body-scroll-manual::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
</style>
