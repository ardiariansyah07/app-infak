<style>

:root{

    --sidebar-width:260px;
    --sidebar-collapse-width:80px;

    --primary:#1e3a8a;
    --primary-hover:#172554;

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

    padding-bottom:28px;

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

    padding:24px;

    text-align:center;

    color:white;

    border-bottom:
        1px solid rgba(255,255,255,.08);
}

.sidebar-logo{

    font-size:28px;

    font-weight:700;
}

.sidebar-subtitle{

    font-size:13px;

    color:#94a3b8;

    margin-top:5px;
}

/* GROUP */

.menu-group{

    padding:
        20px
        20px
        10px;

    font-size:11px;

    font-weight:700;

    letter-spacing:.5px;

    color:#64748b;
}

/* MENU */

.sidebar-menu{

    padding:0 15px;
}

.sidebar-menu a{

    display:flex;

    align-items:center;

    gap:12px;

    text-decoration:none;

    color:#cbd5e1;

    padding:12px 15px;

    border-radius:12px;

    margin-bottom:5px;

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

    height:24px;

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

    background:linear-gradient(135deg,#1e3a8a,#312e81);
}

.panel-soft{

    border:0;

    border-radius:18px;

    box-shadow:0 14px 35px rgba(15,23,42,.07);
}

.btn-primary{

    background:linear-gradient(135deg,#1e3a8a,#312e81);

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
            #1e3a8a,
            #312e81
        );
}

.btn-primary:hover{

    background:
        linear-gradient(
            135deg,
            #172554,
            #1e1b4b
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

    margin-left:20px;

    font-size:14px;
}

#master-menu,
#transaksi-menu{

    overflow:hidden;

    transition:.3s;
}

.btn i{

    margin-right:4px;
}

/* BREADCRUMB */

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
