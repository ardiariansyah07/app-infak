<style>

:root{

    --sidebar-width:260px;
    --sidebar-collapse-width:80px;

    --primary:#1e3a8a;
    --primary-hover:#172554;
    --primary-deep:#312e81;
    --primary-deep-hover:#1e1b4b;

    --sidebar-bg-1:#0f172a;
    --sidebar-bg-2:#1e293b;

    --body-bg:#f1f5f9;

}

/* ===================================
   GLOBAL
=================================== */

body{

    background:var(--body-bg);

    overflow-x:hidden;

    font-family:
        "Segoe UI",
        Tahoma,
        Geneva,
        Verdana,
        sans-serif;
}

/* ===================================
   SIDEBAR
=================================== */

.sidebar{

    position:fixed;

    top:0;
    left:0;

    width:var(--sidebar-width);

    height:100vh;

    overflow-y:auto;

    overflow-x:hidden;

    padding-bottom:18px;

    background:linear-gradient(
        180deg,
        var(--sidebar-bg-1),
        var(--sidebar-bg-2)
    );

    transition:.3s;

    z-index:999;
}

.sidebar::-webkit-scrollbar{

    width:7px;
}

.sidebar::-webkit-scrollbar-thumb{

    background:rgba(148,163,184,.35);

    border-radius:999px;
}

.sidebar.collapsed{

    width:var(--sidebar-collapse-width);
}

/* HEADER */

.sidebar-header{

    padding:18px 20px;

    text-align:center;

    color:white;

    border-bottom:
        1px solid rgba(255,255,255,.08);
}

.sidebar-logo{

    font-size:23px;

    font-weight:700;

    display:flex;

    align-items:center;

    justify-content:center;

    gap:10px;
}

.sidebar-logo-img{

    width:38px;

    height:38px;

    object-fit:contain;

    border-radius:10px;

    background:#fff;
}

.sidebar-subtitle{

    font-size:13px;

    color:#94a3b8;

    margin-top:5px;
}

/* GROUP */

.menu-group{

    padding:
        13px
        20px
        6px;

    font-size:11px;

    font-weight:700;

    letter-spacing:.5px;

    color:#64748b;
}

/* MENU */

.sidebar-menu{

    padding:0 12px;
}

.sidebar-menu a{

    display:flex;

    align-items:center;

    gap:10px;

    text-decoration:none;

    color:#cbd5e1;

    padding:9px 13px;

    border-radius:10px;

    margin-bottom:3px;

    transition:.25s;
}

.sidebar-menu a:hover{

    background:#334155;

    color:white;
}

/* ACTIVE */

.sidebar-menu a.active{

    background:
        linear-gradient(
            135deg,
            #1e3a8a,
            #312e81
        );

    color:white;

    box-shadow:
        0 10px 20px
        rgba(37,99,235,.25);
}

.sidebar-menu a.active::before{

    content:'';

    width:4px;

    height:20px;

    border-radius:10px;

    background:white;
}

/* COLLAPSE */

.sidebar.collapsed .menu-text{

    display:none;
}

.sidebar.collapsed .menu-group{

    display:none;
}

.sidebar.collapsed .sidebar-subtitle{

    display:none;
}

.sidebar.collapsed .sidebar-menu a{

    justify-content:center;
}

/* ===================================
   CONTENT
=================================== */

.content{

    margin-left:var(--sidebar-width);

    transition:.3s;

    min-height:100vh;

    display:flex;

    flex-direction:column;
}

.content.expanded{

    margin-left:var(--sidebar-collapse-width);
}

/* ===================================
   TOPBAR
=================================== */

.topbar{

    height:70px;

    background:white;

    border-bottom:
        1px solid #e5e7eb;

    display:flex;

    align-items:center;

    justify-content:space-between;

    padding:0 25px;
}

.btn-toggle{

    border:none;

    background:none;

    font-size:24px;
}

/* ===================================
   PAGE
=================================== */

.page{

    padding:25px;

    flex:1 0 auto;
}

.app-footer{

    margin:0 25px 24px;

    padding:16px 0;

    flex-shrink:0;

    color:#64748b;

    font-size:14px;

    text-align:center;
}

.app-footer a{

    color:var(--primary);

    font-weight:700;

    text-decoration:none;
}

.app-footer a:hover{

    color:var(--primary-hover);

    text-decoration:underline;
}

.stat-card{

    border:0;

    border-radius:18px;

    box-shadow:0 14px 35px rgba(15,23,42,.08);

    overflow:hidden;
}

.stat-icon{

    width:48px;

    height:48px;

    display:inline-flex;

    align-items:center;

    justify-content:center;

    border-radius:14px;

    color:#fff;

    background:linear-gradient(135deg,var(--primary),var(--primary-deep));
}

.panel-soft{

    border:0;

    border-radius:18px;

    box-shadow:0 14px 35px rgba(15,23,42,.07);
}

.btn-primary{

    background:linear-gradient(135deg,var(--primary),var(--primary-deep));

    border:0;
}

.btn-primary:hover{

    filter:brightness(.95);
}

.page-title{

    font-size:32px;

    font-weight:700;

    color:#111827;
}

.page-subtitle{

    color:#64748b;
}

/* ===================================
   CARD
=================================== */

.card-modern{

    border:none;

    border-radius:18px;

    box-shadow:
        0 4px 20px rgba(0,0,0,.05);
}

.card{

    border-radius:16px;
}

.card.border-0,
.card.shadow-sm,
.card-modern{

    box-shadow:0 14px 35px rgba(15,23,42,.07) !important;
}

/* ===================================
   TABLE
=================================== */

.table{

    margin-bottom:0;
}

.table thead{

    background:#f8fafc;
}

.table thead th{

    border:none;

    font-weight:600;
}

.table tbody td{

    vertical-align:middle;
}

.table-number-column{

    width:64px;

    text-align:center;

    white-space:nowrap;
}

.table-enhancer{

    margin-top:4px;
}

.table-search{

    max-width:360px;
}

.table-pagination{

    margin-top:12px;

    justify-content:flex-end;
}

.table-pagination .btn{

    min-width:36px;
}

.table-bordered{

    border:0;

    background:#fff;

    border-radius:16px;

    overflow:hidden;

    box-shadow:0 14px 35px rgba(15,23,42,.07);
}

/* ===================================
   BUTTON
=================================== */

.btn-primary{

    border:none;

    border-radius:12px;

    padding:
        10px
        20px;

    background:
        linear-gradient(
            135deg,
            var(--primary),
            var(--primary-deep)
        );
}

.btn-primary:hover{

    background:
        linear-gradient(
            135deg,
            var(--primary-hover),
            var(--primary-deep-hover)
        );
}

.btn-success{

    border:none;

    border-radius:12px;
}

.btn-danger{

    border:none;

    border-radius:12px;
}

.btn-warning{

    border:none;

    border-radius:12px;
}

.import-actions,
.import-actions form,
.import-action-btn{

    white-space:nowrap;
}

.import-action-btn{

    display:inline-flex;

    align-items:center;

    justify-content:center;

    gap:8px;

    min-height:44px;
}

.import-file{

    width:240px;

    min-height:44px;
}

@media (max-width: 768px){

    .import-actions{

        flex-wrap:wrap !important;

        justify-content:flex-start !important;
    }

    .import-file{

        width:min(100%, 240px);
    }
}

/* ===================================
   BADGE
=================================== */

.badge{

    padding:
        8px
        12px;

    border-radius:10px;
}

/* ===================================
   FORM
=================================== */

.form-control{

    border-radius:12px;
}

.form-select{

    border-radius:12px;
}

.searchable-select{

    position:relative;
}

.searchable-select-native{

    display:none !important;
}

.searchable-select-toggle{

    align-items:center;

    background:#fff;

    border:1px solid #dee2e6;

    border-radius:12px;

    color:#1f2937;

    display:flex;

    gap:12px;

    justify-content:space-between;

    min-height:42px;

    padding:8px 12px;

    text-align:left;

    width:100%;
}

.searchable-select-toggle:focus{

    border-color:#86b7fe;

    box-shadow:0 0 0 .25rem rgba(13,110,253,.25);

    outline:0;
}

.searchable-select-placeholder{

    color:#8a94a6;
}

.searchable-select-menu{

    background:#fff;

    border:1px solid #bfc7d1;

    border-radius:8px;

    box-shadow:0 14px 35px rgba(15,23,42,.14);

    display:none;

    left:0;

    margin-top:4px;

    overflow:hidden;

    position:absolute;

    right:0;

    top:100%;

    z-index:1060;
}

.searchable-select.open .searchable-select-menu{

    display:block;
}

.searchable-select-search{

    border:0;

    border-bottom:1px solid #d1d5db;

    border-radius:0;

    box-shadow:none !important;

    width:100%;
}

.searchable-select-options{

    max-height:260px;

    overflow:auto;
}

.searchable-select-option,
.searchable-select-empty{

    background:#fff;

    border:0;

    color:#1f2937;

    display:block;

    padding:8px 10px;

    text-align:left;

    width:100%;
}

.searchable-select-option:hover,
.searchable-select-option.active{

    background:#0d6efd;

    color:#fff;
}

.searchable-select-empty{

    color:#6b7280;
}

.password-checklist{

    display:grid;

    grid-template-columns:repeat(2, minmax(0, 1fr));

    gap:8px 12px;

    margin-top:10px;

    color:#64748b;

    font-size:13px;
}

.password-check-item{

    display:flex;

    align-items:center;

    gap:7px;
}

.password-check-item i{

    color:#94a3b8;
}

.password-check-item.valid{

    color:#166534;

    font-weight:600;
}

.password-check-item.valid i{

    color:#16a34a;
}

.chart-bars{

    display:grid;

    grid-template-columns:repeat(6, minmax(44px, 1fr));

    gap:14px;

    align-items:end;

    min-height:220px;
}

.chart-bar{

    display:flex;

    flex-direction:column;

    align-items:center;

    gap:8px;
}

.chart-bar-track{

    width:100%;

    height:150px;

    border-radius:12px;

    background:#e2e8f0;

    display:flex;

    align-items:end;

    overflow:hidden;
}

.chart-bar-fill{

    width:100%;

    min-height:8px;

    background:linear-gradient(180deg,var(--primary),var(--primary-deep));
}

.chart-label{

    color:#64748b;

    font-size:12px;
}

.report-summary{

    border-left:4px solid var(--primary);
}

@media print{

    .sidebar,
    .topbar,
    .app-footer,
    .btn,
    form.card{

        display:none !important;
    }

    .content{

        margin-left:0 !important;
    }

    .page{

        padding:0;
    }

    .card{

        box-shadow:none !important;

        border:1px solid #e5e7eb !important;
    }
}

/* ===================================
   DROPDOWN
=================================== */

.dropdown-menu{

    border:none;

    border-radius:14px;

    box-shadow:
        0 10px 25px rgba(0,0,0,.1);
}

/* ===================================
   MOBILE
=================================== */

@media(max-width:768px){

    .sidebar{

        left:-260px;
    }

    .sidebar.mobile-show{

        left:0;
    }

    .content{

        margin-left:0;
    }

    .content.expanded{

        margin-left:0;
    }

}

/* SUBMENU */

.submenu{

    margin-left:14px;

    font-size:13px;
}

#master-menu,
#transaksi-menu{

    display:none;

    overflow:hidden;

    transition:.3s;
}

.btn i{

    margin-right:4px;
}

/* BREADCRUMB */

.app-breadcrumb{

    margin-bottom:18px;
}

.breadcrumb{

    margin-bottom:8px;
}

.breadcrumb-item a{

    text-decoration:none;

    color:#64748b;
}

.breadcrumb-item a:hover{

    color:#1e3a8a;
}

.breadcrumb-item.active{

    color:#0f172a;

    font-weight:600;
}

</style>
