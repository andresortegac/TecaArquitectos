@extends('layouts.app')

@section('title','Arriendos')
@section('header','ALQUILER')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/ui.css') }}">

  {{-- ✅ ESTILOS SOLO PARA ESTA VISTA (ENCAPSULADO) --}}
  <style>
    .pro-ui{
      --surface: rgba(248,250,252,.96);
      --card: #fff;
      --text: #0f172a;
      --muted: #64748b;
      --line: #e5e7eb;
      --soft: #f8fafc;
      --primary: #2563eb;
      --success: #16a34a;
      --warning: #f59e0b;
      --danger: #dc2626;
      --shadow: 0 22px 55px rgba(15,23,42,.14);
      --shadow2: 0 14px 30px rgba(15,23,42,.09);
      --r: 16px;

      width: 100%;
      color: var(--text);
      font-family: "Inter", "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .pro-ui,
    .pro-ui *{
      transition-duration: .01s !important;
      transition-delay: 0s !important;
    }

    /* ✅ Contenedor: ocupa el ancho del content (no centra raro) */
    .pro-container{
      width: 100%;
      max-width: 100%;
      padding: 18px;
      border-radius: 24px;
      border: 1px solid rgba(203,213,225,.9);
      background:
        linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.96));
      backdrop-filter: blur(10px);
      box-shadow: var(--shadow);
      overflow: hidden;
    }

    /* Header superior */
    .pro-topbar{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:16px;
      flex-wrap:wrap;
      padding: 2px 0 16px;
      border-bottom: 1px solid rgba(226,232,240,.95);
      margin-bottom: 16px;
    }
    .pro-heading{
      min-width: 260px;
    }
    .pro-heading h2{
      margin:0;
      font-size:20px;
      font-weight:950;
      letter-spacing:0;
      color:#0f172a;
    }
    .pro-subtitle{
      margin: 6px 0 0;
      color: rgba(71,85,105,.95);
      font-size: 13px;
      line-height: 1.35;
      max-width: 760px;
    }
    .pro-actions{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      align-items:center;
      justify-content:flex-end;
    }

    /* Pulimos botones que ya existen */
    .pro-ui .btn-primary,
    .pro-ui .btn-ghost,
    .pro-ui .btn-sm{
      border-radius: 12px !important;
      font-weight: 850;
      transition: .01s linear;
      letter-spacing:0;
      text-decoration:none !important;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      min-height:38px;
    }
    .pro-ui .btn-primary{
      background:linear-gradient(180deg, #3b82f6, #1d4ed8) !important;
      border-color:#2563eb !important;
      box-shadow:0 12px 24px rgba(37,99,235,.24);
    }
    .pro-ui .btn-ghost{
      background:#fff !important;
      border:1px solid rgba(203,213,225,.95) !important;
      color:#0f172a !important;
      box-shadow:0 10px 20px rgba(15,23,42,.06);
    }
    .pro-ui .btn-primary:hover,
    .pro-ui .btn-ghost:hover,
    .pro-ui .btn-sm:hover{
      transform: translateY(-1px);
      box-shadow: var(--shadow2);
    }

    /* Cards */
    .pro-ui .card{
      border-radius: var(--r) !important;
      border: 1px solid rgba(203,213,225,.95) !important;
      background: rgba(255,255,255,.98) !important;
      box-shadow: var(--shadow2) !important;
    }
    .pro-ui .card-header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      padding-bottom: 10px;
      border-bottom: 1px solid rgba(226,232,240,.95);
    }
    .pro-ui .card-title{
      font-size: 14px !important;
      font-weight: 900 !important;
      letter-spacing: .2px;
    }

    /* KPI */
    .pro-ui .kpi-grid{ gap:12px !important; margin-bottom: 12px; }
    .pro-ui .card.kpi{
      min-height: 126px;
      border-radius: 16px !important;
      background: linear-gradient(180deg, #fff, #f8fafc) !important;
      position:relative;
      overflow:hidden;
      transition: transform .01s linear, border-color .01s linear, box-shadow .01s linear, background .01s linear;
    }
    .pro-ui .card.kpi::before{
      content:"";
      position:absolute;
      inset:0 auto 0 0;
      width:4px;
      background:linear-gradient(180deg, #2563eb, #16a34a);
      opacity:.9;
      transition: width .01s linear, background .01s linear, opacity .01s linear;
    }
    .pro-ui .card.kpi:hover{
      transform: translateY(-2px);
      box-shadow: 0 22px 42px rgba(15,23,42,.16) !important;
    }
    .pro-ui .card.kpi:hover::before{
      width:6px;
      opacity:1;
    }
    .pro-ui .kpi-total:hover,
    .pro-ui .kpi-recaudo-mes:hover{
      background:linear-gradient(135deg, #eff6ff 0%, #93c5fd 48%, #60a5fa 100%) !important;
      border-color:#2563eb !important;
    }
    .pro-ui .kpi-total:hover::before,
    .pro-ui .kpi-recaudo-mes:hover::before{
      background:linear-gradient(180deg, #1e40af, #2563eb);
    }
    .pro-ui .kpi-activos:hover,
    .pro-ui .kpi-recaudo-hoy:hover{
      background:linear-gradient(135deg, #ecfdf5 0%, #86efac 46%, #22c55e 100%) !important;
      border-color:#16a34a !important;
    }
    .pro-ui .kpi-activos:hover::before,
    .pro-ui .kpi-recaudo-hoy:hover::before{
      background:linear-gradient(180deg, #166534, #16a34a);
    }
    .pro-ui .kpi-devueltos:hover{
      background:linear-gradient(135deg, #f8fafc 0%, #cbd5e1 46%, #94a3b8 100%) !important;
      border-color:#64748b !important;
    }
    .pro-ui .kpi-devueltos:hover::before{
      background:linear-gradient(180deg, #334155, #64748b);
    }
    .pro-ui .card.kpi:hover .label,
    .pro-ui .card.kpi:hover .hint{
      color:rgba(15,23,42,.72) !important;
    }
    .pro-ui .card.kpi:hover .value{
      color:#0f172a !important;
    }
    .pro-ui .card.kpi .meta .label{
      font-size: 12px !important;
      color: rgba(100,116,139,.95) !important;
      text-transform: uppercase;
      letter-spacing: .45px;
      font-weight: 800 !important;
    }
    .pro-ui .card.kpi .meta .value{
      font-size: 24px !important;
      font-weight: 950 !important;
      letter-spacing:.2px;
    }
    .pro-ui .card.kpi .meta .hint{
      font-size: 12px !important;
      color: rgba(100,116,139,.95) !important;
    }

    /* Filtros */
    .pro-ui .filters-grid{ gap:12px !important; align-items:center; }
    .pro-ui .input{
      border-radius: 14px !important;
      height: 46px !important;
      border: 1px solid rgba(203,213,225,.95) !important;
      background:linear-gradient(180deg, #fff, #f8fafc) !important;
      padding: 0 16px !important;
      outline:none !important;
      transition: .01s linear;
      color:#0f172a !important;
    }
    .pro-ui .input:focus{
      border-color: rgba(59,130,246,.75) !important;
      box-shadow: 0 0 0 5px rgba(59,130,246,.12) !important;
    }

    /* ✅ Tabla pro (sin romper layout). En móvil hace scroll, en desktop no */
    .table-wrap-pro{
      width: 100%;
      overflow-x: auto;
      border-radius: 14px;
      border: 1px solid rgba(203,213,225,.95);
      background: #fff;
      margin-top: 12px;
      position: relative; /* ✅ necesario para layering del dropdown */
    }
    .pro-ui .table-pro{
      width: 100%;
      min-width: 0;
      table-layout: fixed;
      border-collapse: separate !important;
      border-spacing: 0 !important;
    }
    .pro-ui .table-pro col.col-client{ width:15%; }
    .pro-ui .table-pro col.col-products{ width:8%; }
    .pro-ui .table-pro col.col-date-start{ width:10%; }
    .pro-ui .table-pro col.col-date-end{ width:8%; }
    .pro-ui .table-pro col.col-money{ width:11%; }
    .pro-ui .table-pro col.col-money-due{ width:12%; }
    .pro-ui .table-pro col.col-mora{ width:5%; }
    .pro-ui .table-pro col.col-semaforo{ width:10%; }
    .pro-ui .table-pro col.col-estado{ width:12%; }
    .pro-ui .table-pro col.col-actions{ width:9%; }
    .pro-ui .table-pro thead th{
      position: sticky;
      top: 0;
      z-index: 2;
      background: linear-gradient(180deg, #f8fafc, #eef2f7) !important;
      color: #334155 !important;
      text-transform: uppercase;
      letter-spacing: .45px;
      font-size: 12px !important;
      font-weight: 900 !important;
      border-bottom: 1px solid rgba(203,213,225,.95) !important;
      padding: 12px 9px !important;
      white-space: normal;
      line-height:1.08;
      text-align:left;
    }
    .pro-ui .table-pro tbody td{
      padding: 11px 9px !important;
      border-bottom: 1px solid rgba(203,213,225,.9) !important;
      font-size: 12.5px !important;
      vertical-align: middle !important;
      color: rgba(15,23,42,.95);
      white-space: nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
      transition: background-color .01s linear, box-shadow .01s linear, border-color .01s linear;
    }
    .pro-ui .table-pro tbody tr{
      position: relative; /* ✅ para z-index cuando dropdown abre */
    }

    /* ✅ Hover sin cambiar colores de semáforo */
    .pro-ui .table-pro tbody tr:hover{
      filter: none !important;
      background: inherit !important;
    }

    .td-right{ text-align:right; }
    th.td-right{ text-align:right !important; }
    .col-products,
    .col-mora,
    .col-status,
    .col-actions{ text-align:center; }
    .table-pro td.col-status,
    .table-pro td.col-actions{
      overflow:visible !important;
    }
    .status-cell,
    .actions-cell{
      min-height:38px;
      display:flex;
      align-items:center;
      justify-content:center;
      width:100%;
    }
    .table-pro td.col-status .chip,
    .table-pro td.col-status .traffic-pill{
      min-width:88px;
      justify-content:center;
    }
    .cell-main{
      display:block;
      overflow:hidden;
      text-overflow:ellipsis;
      white-space:nowrap;
      font-weight:700;
    }
    .cell-sub{
      display:block;
      margin-top:4px;
      color:rgba(71,85,105,.86);
      font-size:11px;
      line-height:1.15;
      overflow:hidden;
      text-overflow:ellipsis;
      white-space:nowrap;
    }
    .money-cell{
      display:block;
      font-variant-numeric:tabular-nums;
      font-weight:800;
    }
    .small{
      display:block;
      margin-top: 6px;
      color: rgba(100,116,139,.95) !important;
      font-size: 12px !important;
      line-height: 1.2;
      white-space: normal;
    }

    /* Chips más serios (sin cambiar colores de tu ui.css) */
    .pro-ui .chip{
      border-radius: 999px !important;
      padding: 6px 10px !important;
      font-weight: 900 !important;
      letter-spacing: .2px;
      border: 1px solid rgba(226,232,240,.85);
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:6px;
      white-space: nowrap;
      max-width:none;
      overflow:visible;
      text-overflow:clip;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.8);
      font-size:11px !important;
      line-height:1;
      text-align:center;
    }
    .pro-ui .chip.blue{
      color:#fff !important;
      background:linear-gradient(180deg, #60a5fa, #2563eb) !important;
      border-color:#93c5fd !important;
      text-shadow:0 1px 1px rgba(15,23,42,.22);
    }
    .pro-ui .chip.gray{
      color:#0f172a !important;
      background:linear-gradient(180deg, #f8fafc, #cbd5e1) !important;
      border-color:#94a3b8 !important;
    }

    /* Dropdown */
    .pro-ui .actions{ display:flex; justify-content:flex-end; }
    .col-actions .actions{ justify-content:center; }
    .pro-ui .dropdown{ position: relative; z-index: 60; }
    .pro-ui [data-dd].open{ z-index: 60; }
    .pro-ui [data-dd].open .dropdown-menu{ display:none; }

    .pro-ui .btn-kebab{
      width: 38px !important;
      height: 38px !important;
      border-radius: 999px !important;
      border: 1px solid rgba(226,232,240,.95) !important;
      background: #fff !important;
      box-shadow: 0 10px 18px rgba(15,23,42,.08);
      transition: .01s linear;
    }
    .pro-ui .btn-kebab:hover{ transform: translateY(-1px); }
    .pro-ui .btn-kebab:active{ transform: translateY(0); }

    .pro-ui .dropdown-menu{
      display:none;
      position:absolute;
      right:0;
      top: calc(100% + 8px);
      min-width: 220px;
      background:#fff;
      border:1px solid rgba(226,232,240,.95);
      border-radius: 14px;
      box-shadow: 0 18px 40px rgba(15,23,42,.16);
      overflow:hidden;
      z-index: 9999 !important; /* ✅ arriba de todo */
      will-change: transform;   /* ✅ evita flicker */
      transform: translateZ(0); /* ✅ evita flicker */
    }
    .pro-ui .dropdown-menu.is-floating{
      position: fixed;
      top: var(--dd-top, 0px);
      left: var(--dd-left, 0px);
      right: auto;
      z-index: 100000 !important;
    }
    body > .dropdown-menu.is-floating{
      display:block;
      position: fixed;
      top: var(--dd-top, 0px);
      left: var(--dd-left, 0px);
      min-width:220px;
      background:#fff;
      border:1px solid rgba(226,232,240,.95);
      border-radius:14px;
      box-shadow:0 22px 46px rgba(15,23,42,.22);
      overflow:hidden;
      z-index:100000 !important;
    }
    body > .dropdown-menu.is-floating .menu-item{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:10px;
      width:100%;
      padding:11px 12px;
      border:0;
      background:#fff;
      text-decoration:none;
      color:rgba(15,23,42,.95);
      font-weight:800;
      font-size:13px;
      cursor:pointer;
      transition:.01s linear;
    }
    body > .dropdown-menu.is-floating .menu-item:hover{
      transform:translateX(2px);
      color:#0f172a;
    }
    body > .dropdown-menu.is-floating .item-return:hover,
    body > .dropdown-menu.is-floating .item-details:hover{
      background:linear-gradient(90deg, #dbeafe, #bfdbfe);
    }
    body > .dropdown-menu.is-floating .item-edit:hover{
      background:linear-gradient(90deg, #eef2ff, #c7d2fe);
    }
    body > .dropdown-menu.is-floating .item-close:hover{
      background:linear-gradient(90deg, #ffedd5, #fed7aa);
    }
    body > .dropdown-menu.is-floating .item-delete:hover{
      background:linear-gradient(90deg, #fee2e2, #fecaca);
      color:#991b1b;
    }
    body > .dropdown-menu.is-floating .menu-left{
      display:flex;
      align-items:center;
      gap:10px;
    }
    body > .dropdown-menu.is-floating .dot{
      width:8px;
      height:8px;
      border-radius:999px;
      background:rgba(148,163,184,.9);
      flex:0 0 auto;
    }
    body > .dropdown-menu.is-floating .item-delete .dot{ background:#ef4444; }
    body > .dropdown-menu.is-floating .item-close .dot{ background:#f59e0b; }
    body > .dropdown-menu.is-floating .item-return .dot{ background:#3b82f6; }
    body > .dropdown-menu.is-floating .item-edit .dot{ background:#94a3b8; }
    body > .dropdown-menu.is-floating .item-details .dot{ background:#10b981; }
    @media(max-width: 1180px){
      .pro-ui .table-pro{
        min-width: 1080px;
        table-layout: fixed;
      }
    }
    .pro-ui .menu-item{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:10px;
      width:100%;
      padding: 11px 12px;
      border:0;
      background:#fff;
      text-decoration:none;
      color: rgba(15,23,42,.95);
      font-weight: 700;
      cursor:pointer;
      transition:.01s linear;
    }
    .pro-ui .menu-item:hover{
      transform:translateX(2px);
      color:#0f172a;
    }
    .pro-ui .item-return:hover,
    .pro-ui .item-details:hover{
      background:linear-gradient(90deg, #dbeafe, #bfdbfe);
    }
    .pro-ui .item-edit:hover{
      background:linear-gradient(90deg, #eef2ff, #c7d2fe);
    }
    .pro-ui .item-close:hover{
      background:linear-gradient(90deg, #ffedd5, #fed7aa);
    }
    .pro-ui .item-delete:hover{
      background:linear-gradient(90deg, #fee2e2, #fecaca);
      color:#991b1b;
    }
    .pro-ui .menu-left{ display:flex; align-items:center; gap:10px; }
    .pro-ui .dot{
      width: 8px; height: 8px;
      border-radius: 999px;
      background: rgba(148,163,184,.9);
    }
    .pro-ui .item-delete .dot{ background: #ef4444; }
    .pro-ui .item-close .dot{ background: #f59e0b; }
    .pro-ui .item-return .dot{ background: #3b82f6; }
    .pro-ui .item-edit .dot{ background: #94a3b8; }
    .pro-ui .item-details .dot{ background: #10b981; }

    .report-toolbar{
      margin:12px 0 0;
      padding:10px;
      display:flex;
      gap:8px;
      flex-wrap:wrap;
      align-items:center;
      border:1px solid rgba(203,213,225,.9);
      border-radius:16px;
      background:rgba(255,255,255,.74);
    }
    .report-toolbar .btn-sm{
      background:#fff;
      border:1px solid rgba(203,213,225,.95);
      color:#0f172a;
      box-shadow:0 8px 18px rgba(15,23,42,.06);
    }
    .report-toolbar .btn-sm:hover{
      border-color:#93c5fd;
      color:#1d4ed8;
    }

    .semaforo-legend{
      display:flex;
      gap:8px;
      flex-wrap:wrap;
      align-items:center;
      color:#64748b;
      font-size:12px;
      font-weight:800;
    }
    .legend-dot{
      width:9px;
      height:9px;
      border-radius:999px;
      display:inline-block;
      margin-right:5px;
      box-shadow:0 0 0 3px rgba(15,23,42,.06);
    }
    .legend-dot.blue{ background:#2563eb; }
    .legend-dot.green{ background:#16a34a; }
    .legend-dot.amber{ background:#f59e0b; }
    .legend-dot.red{ background:#dc2626; }
    @media(max-width: 760px){
      .pro-container{ padding:12px; border-radius:18px; }
      .pro-topbar{ align-items:flex-start; }
      .pro-actions,
      .report-toolbar,
      .semaforo-legend{
        width:100%;
      }
      .pro-actions .btn-ghost,
      .pro-actions .btn-primary,
      .report-toolbar .btn-sm{
        flex:1 1 150px;
      }
      .pro-ui .card-header{
        align-items:flex-start;
        flex-direction:column;
      }
    }

    /* ✅ Fix parpadeo: cuando dropdown esté abierto, la fila sube de nivel */
    .pro-ui .table-pro tbody tr.row-open{ z-index: 60; }
    .pro-ui .table-pro tbody tr.row-open:hover{ filter: none !important; }

    /* Modal (solo estética; si tu ui.css ya lo maneja, no rompe) */
    .pro-ui .modal-backdrop{
      position: fixed !important;
      inset: 0 !important;
      background: rgba(2,6,23,.55) !important;
      backdrop-filter: blur(6px);
      z-index: 80 !important;
      padding: 18px;
    }
    .pro-ui .modal-dialog{
      max-width: 760px;
      margin: auto;
    }
    .pro-ui .modal-grid{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-top: 12px;
    }
    @media(max-width: 820px){
      .pro-ui .modal-grid{ grid-template-columns:1fr; }
    }
    .pro-ui .modal-actions{
      display:flex;
      justify-content:flex-end;
      gap:10px;
      margin-top: 14px;
    }
    .pro-ui .close-summary{
      margin-top: 12px;
      padding: 12px;
      border-radius: 14px;
      border: 1px solid rgba(59,130,246,.24);
      background: rgba(59,130,246,.06);
    }
    .pro-ui .close-summary-grid{
      display:grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
    }
    @media(max-width: 820px){
      .pro-ui .close-summary-grid{
        grid-template-columns: 1fr 1fr;
      }
    }
    .pro-ui .sum-box{
      background:#fff;
      border: 1px solid rgba(226,232,240,.95);
      border-radius: 12px;
      padding: 9px 10px;
    }
    .pro-ui .sum-k{
      display:block;
      font-size: 11px;
      color: rgba(100,116,139,.95);
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .2px;
    }
    .pro-ui .sum-v{
      display:block;
      margin-top: 4px;
      font-size: 15px;
      font-weight: 900;
      color: rgba(15,23,42,.95);
      font-variant-numeric: tabular-nums;
    }
    .pro-ui .sum-v-danger{ color:#b91c1c; }
    .pro-ui .sum-v-ok{ color:#166534; }

    /* ==============================
       SEMAFORIZACION POR FILA
       AZUL: activo
       VERDE: devuelto y al dia
       NARANJA: saldo reciente
       ROJO: saldo en mora
       ============================== */
    .tr-flag-blue{
      background: linear-gradient(90deg, #2563eb 0, #2563eb 10px, #93c5fd 10px, #bfdbfe 34%, #dbeafe 100%) !important;
    }
    .tr-flag-green{
      background: linear-gradient(90deg, #16a34a 0, #16a34a 10px, #86efac 10px, #bbf7d0 34%, #dcfce7 100%) !important;
    }
    .tr-flag-amber{
      background: linear-gradient(90deg, #f59e0b 0, #f59e0b 10px, #facc15 10px, #fde68a 34%, #fef3c7 100%) !important;
    }
    .tr-flag-red{
      background: linear-gradient(90deg, #dc2626 0, #dc2626 10px, #f87171 10px, #fecaca 34%, #fee2e2 100%) !important;
    }

    .tr-flag-blue td{
      background:#bfdbfe !important;
    }
    .tr-flag-green td{
      background:#bbf7d0 !important;
    }
    .tr-flag-amber td{
      background:#fde68a !important;
    }
    .tr-flag-red td{
      background:#fecaca !important;
    }
    .tr-flag-blue td:first-child{
      background:linear-gradient(90deg, #2563eb 0, #2563eb 12px, #93c5fd 12px, #bfdbfe 100%) !important;
    }
    .tr-flag-green td:first-child{
      background:linear-gradient(90deg, #16a34a 0, #16a34a 12px, #86efac 12px, #bbf7d0 100%) !important;
    }
    .tr-flag-amber td:first-child{
      background:linear-gradient(90deg, #f59e0b 0, #f59e0b 12px, #facc15 12px, #fde68a 100%) !important;
    }
    .tr-flag-red td:first-child{
      background:linear-gradient(90deg, #dc2626 0, #dc2626 12px, #f87171 12px, #fecaca 100%) !important;
    }

    .tr-flag-blue td{ border-top-color:#93c5fd !important; border-bottom-color:#93c5fd !important; }
    .tr-flag-green td{ border-top-color:#86efac !important; border-bottom-color:#86efac !important; }
    .tr-flag-amber td{ border-top-color:#facc15 !important; border-bottom-color:#facc15 !important; }
    .tr-flag-red td{ border-top-color:#f87171 !important; border-bottom-color:#f87171 !important; }

    .tr-flag-blue  td:first-child{ box-shadow: inset 12px 0 0 #1d4ed8; }
    .tr-flag-green td:first-child{ box-shadow: inset 12px 0 0 #15803d; }
    .tr-flag-amber td:first-child{ box-shadow: inset 12px 0 0 #d97706; }
    .tr-flag-red   td:first-child{ box-shadow: inset 12px 0 0 #b91c1c; }

    .pro-ui .traffic-pill{
      display:inline-flex;
      align-items:center;
      gap:7px;
      padding:6px 8px;
      border-radius:999px;
      font-size:10px;
      line-height:1;
      font-weight:950;
      letter-spacing:.35px;
      text-transform:uppercase;
      border:1px solid rgba(255,255,255,.86);
      box-shadow: 0 12px 24px rgba(15,23,42,.24), inset 0 1px 0 rgba(255,255,255,.58);
      max-width:100%;
      overflow:hidden;
      text-overflow:ellipsis;
    }
    .pro-ui .traffic-pill::before{
      content:"";
      width:8px;
      height:8px;
      border-radius:999px;
      background:currentColor;
    }
    .pro-ui .traffic-pill.blue{
      color:#fff;
      background:linear-gradient(180deg, #60a5fa 0%, #2563eb 48%, #1d4ed8 100%);
      text-shadow:0 1px 1px rgba(15,23,42,.22);
    }
    .pro-ui .traffic-pill.blue::before{ background:#bfdbfe; box-shadow:0 0 0 4px rgba(191,219,254,.22); }
    .pro-ui .traffic-pill.green{
      color:#fff;
      background:linear-gradient(180deg, #4ade80 0%, #16a34a 48%, #15803d 100%);
      text-shadow:0 1px 1px rgba(15,23,42,.18);
    }
    .pro-ui .traffic-pill.green::before{ background:#bbf7d0; box-shadow:0 0 0 4px rgba(187,247,208,.22); }
    .pro-ui .traffic-pill.amber{
      color:#451a03;
      background:linear-gradient(180deg, #fde047 0%, #f59e0b 52%, #ea580c 100%);
    }
    .pro-ui .traffic-pill.amber::before{ background:#fff7ed; box-shadow:0 0 0 4px rgba(255,247,237,.24); }
    .pro-ui .traffic-pill.red{
      color:#fff;
      background:linear-gradient(180deg, #fb7185 0%, #dc2626 52%, #991b1b 100%);
      text-shadow:0 1px 1px rgba(15,23,42,.22);
    }
    .pro-ui .traffic-pill.red::before{ background:#fecaca; box-shadow:0 0 0 4px rgba(254,202,202,.24); }

    .tr-flag-blue:hover,
    .tr-flag-green:hover,
    .tr-flag-amber:hover,
    .tr-flag-red:hover{
      filter:saturate(1.24) contrast(1.07) !important;
    }
    .tr-flag-blue:hover td{
      background:#60a5fa !important;
      border-top-color:#2563eb !important;
      border-bottom-color:#2563eb !important;
      box-shadow:inset 0 1px 0 rgba(255,255,255,.28), inset 0 -1px 0 rgba(37,99,235,.28);
    }
    .tr-flag-green:hover td{
      background:#34d869 !important;
      border-top-color:#16a34a !important;
      border-bottom-color:#16a34a !important;
      box-shadow:inset 0 1px 0 rgba(255,255,255,.26), inset 0 -1px 0 rgba(22,163,74,.30);
    }
    .tr-flag-amber:hover td{
      background:#fbbf24 !important;
      border-top-color:#d97706 !important;
      border-bottom-color:#d97706 !important;
      box-shadow:inset 0 1px 0 rgba(255,255,255,.30), inset 0 -1px 0 rgba(217,119,6,.30);
    }
    .tr-flag-red:hover td{
      background:#fb7185 !important;
      border-top-color:#dc2626 !important;
      border-bottom-color:#dc2626 !important;
      box-shadow:inset 0 1px 0 rgba(255,255,255,.24), inset 0 -1px 0 rgba(220,38,38,.34);
    }
    .tr-flag-blue:hover td:first-child{
      background:linear-gradient(90deg, #1e40af 0, #1e40af 14px, #2563eb 14px, #60a5fa 100%) !important;
      box-shadow:inset 14px 0 0 #1e40af, inset 0 1px 0 rgba(255,255,255,.28), inset 0 -1px 0 rgba(37,99,235,.28);
    }
    .tr-flag-green:hover td:first-child{
      background:linear-gradient(90deg, #166534 0, #166534 14px, #16a34a 14px, #34d869 100%) !important;
      box-shadow:inset 14px 0 0 #166534, inset 0 1px 0 rgba(255,255,255,.26), inset 0 -1px 0 rgba(22,163,74,.30);
    }
    .tr-flag-amber:hover td:first-child{
      background:linear-gradient(90deg, #b45309 0, #b45309 14px, #d97706 14px, #fbbf24 100%) !important;
      box-shadow:inset 14px 0 0 #b45309, inset 0 1px 0 rgba(255,255,255,.30), inset 0 -1px 0 rgba(217,119,6,.30);
    }
    .tr-flag-red:hover td:first-child{
      background:linear-gradient(90deg, #991b1b 0, #991b1b 14px, #dc2626 14px, #fb7185 100%) !important;
      box-shadow:inset 14px 0 0 #991b1b, inset 0 1px 0 rgba(255,255,255,.24), inset 0 -1px 0 rgba(220,38,38,.34);
    }
  </style>
@endpush

@section('content')

  <div class="principal-page">

    @if(session('success'))
      <div class="alert success">{{ session('success') }}</div>
    @endif

    @php
      // ✅ KPIs calculados con los datos visibles (paginación)
      $col = $arriendos->getCollection();

      $total = $col->count();
      $activos = $col->where('estado','activo')->count();
      $devueltos = $col->where('estado','devuelto')->count();

      $rojo = $col->where('semaforo_pago','ROJO')->count();
      $amarillo = $col->where('semaforo_pago','AMARILLO')->count();
      $verde = $total - $rojo - $amarillo;

      $saldoTotal = $col->sum(fn($x)=>(float)($x->saldo ?? 0));
      $moraTotal = $col->sum(fn($x)=>(int)($x->dias_mora ?? 0));

      $pctPagos = $total ? round(($verde / $total) * 100) : 0;
      $pctActivos = $total ? round(($activos / $total) * 100) : 0;
      $pctDev = $total ? round(($devueltos / $total) * 100) : 0;
      $pctMora = $total ? round((($rojo + $amarillo) / $total) * 100) : 0;

      $pctRecaudoMes = ((float)($recaudadoMes ?? 0)) > 0 ? 67 : 0;
      $pctRecaudoHoy = ((float)($recaudadoHoy ?? 0)) > 0 ? 85 : 15;
    @endphp

    <div class="pro-ui">
      <div class="pro-container">

        {{-- TOPBAR --}}
        <div class="pro-topbar">
          <div class="pro-heading">
            <h2>Control de arriendos</h2>
            <p class="pro-subtitle">
              Contratos padre, estados de pago y gestión por productos.
            </p>
          </div>

          <div class="pro-actions">
            <a class="btn-ghost" href="{{ route('arriendos.index') }}">Refrescar</a>
            <a class="btn-primary" href="{{ route('arriendos.create') }}">+ Nuevo arriendo</a>
          </div>
        </div>

        {{-- KPIs --}}
        <div class="kpi-grid">

          <div class="card kpi kpi-total">
            <div class="meta">
              <div class="label">Total</div>
              <div class="value">{{ $total }}</div>
              <div class="hint">En esta página</div>
            </div>
            <div class="ring"
                 style="--p: {{ $pctPagos }}%; --ring: var(--primary);"
                 data-t="{{ $pctPagos }}%">
            </div>
          </div>

          <div class="card kpi kpi-activos">
            <div class="meta">
              <div class="label">Activos</div>
              <div class="value">{{ $activos }}</div>
              <div class="hint">En curso</div>
            </div>
            <div class="ring"
                 style="--p: {{ $pctActivos }}%; --ring: var(--success);"
                 data-t="{{ $pctActivos }}%">
            </div>
          </div>

          <div class="card kpi kpi-devueltos">
            <div class="meta">
              <div class="label">Devueltos</div>
              <div class="value">{{ $devueltos }}</div>
              <div class="hint">Cerrados</div>
            </div>
            <div class="ring"
                 style="--p: {{ $pctDev }}%; --ring: rgba(100,116,139,.8);"
                 data-t="{{ $pctDev }}%">
            </div>
          </div>

          <div class="card kpi kpi-recaudo-mes" id="kpiRecaudoMes">
            <div class="meta">
              <div class="label">Recaudo del mes</div>
              <div class="value">${{ number_format((float)($recaudadoMes ?? 0), 0) }}</div>
              <div class="hint">{{ now()->format('m/Y') }} (confirmado)</div>

              @if(\Illuminate\Support\Facades\Route::has('metricas.reporte.anual') || \Illuminate\Support\Facades\Route::has('metricas.reporte.mensual'))
                <div style="margin-top:8px;">
                  @if(\Illuminate\Support\Facades\Route::has('metricas.reporte.mensual'))
                    <a class="btn-sm"
                       href="{{ route('metricas.reporte.mensual', ['year' => request('year', now()->year), 'month' => request('month', now()->month)]) }}">
                      Ver detalle del mes
                    </a>
                  @elseif(\Illuminate\Support\Facades\Route::has('metricas.reporte.anual'))
                    <a class="btn-sm"
                       href="{{ route('metricas.reporte.anual', ['year' => request('year', now()->year)]) }}">
                      Ver detalle anual
                    </a>
                  @endif
                </div>
              @endif
            </div>

            <div class="ring"
                 style="--p: {{ $pctRecaudoMes }}%; --ring: var(--primary);"
                 data-t="%">
            </div>
          </div>

          <div class="card kpi kpi-recaudo-hoy" id="kpiRecaudoHoy">
            <div class="meta">
              <div class="label">Recaudado hoy</div>
              <div class="value" id="recaudoHoyValue">
                ${{ number_format((float)($recaudadoHoy ?? 0), 0) }}
              </div>
              <div class="hint">{{ now()->format('d/m/Y') }} (confirmado)</div>

              @if(\Illuminate\Support\Facades\Route::has('metricas.detalle.dia'))
                <div style="margin-top:8px;">
                  <a class="btn-sm"
                     href="{{ route('metricas.detalle.dia', ['date' => now()->toDateString()]) }}">
                    Ver detalle de hoy
                  </a>
                </div>
              @endif
            </div>

            <div class="ring"
                 style="--p: {{ $pctRecaudoHoy }}%; --ring: var(--success);"
                 data-t="$">
            </div>
          </div>

        </div>

        {{-- MINI REPORTES --}}
        <div class="report-toolbar">
          @if(\Illuminate\Support\Facades\Route::has('metricas.reporte.anual'))
            <a class="btn-sm"
               href="{{ route('metricas.reporte.anual', ['year' => request('year', now()->year)]) }}">
              Reporte anual
            </a>
          @endif

          @if(\Illuminate\Support\Facades\Route::has('metricas.reporte.mensual'))
            <a class="btn-sm"
               href="{{ route('metricas.reporte.mensual', ['year' => request('year', now()->year), 'month' => request('month', now()->month)]) }}">
              Reporte mensual
            </a>
          @endif

          @if(\Illuminate\Support\Facades\Route::has('metricas.detalle.dia'))
            <a class="btn-sm"
               href="{{ route('metricas.detalle.dia', ['date' => now()->toDateString()]) }}">
              Detalle día (hoy)
            </a>
          @endif
        </div>

        {{-- FILTROS --}}
        <div class="card" style="margin-top:12px;">
          <div class="card-header">
            <h3 class="card-title">Filtros</h3>
            <a class="btn-sm" href="{{ route('arriendos.index') }}">Limpiar</a>
          </div>

          <form id="filtrosForm" method="GET" action="{{ route('arriendos.index') }}">
            <div class="filters-grid">

              <select name="cliente_id" class="input filtro-auto">
                <option value="">Cliente (todos)</option>
                @isset($clientes)
                  @foreach($clientes as $c)
                    <option value="{{ $c->id }}" {{ request('cliente_id') == $c->id ? 'selected' : '' }}>
                      {{ $c->nombre }}
                    </option>
                  @endforeach
                @endisset
              </select>

              <select name="producto_id" class="input filtro-auto">
                <option value="">Producto (todos)</option>
                @isset($productos)
                  @foreach($productos as $p)
                    <option value="{{ $p->id }}" {{ request('producto_id') == $p->id ? 'selected' : '' }}>
                      {{ $p->nombre }}
                    </option>
                  @endforeach
                @endisset
              </select>

              <select name="obra_id" class="input filtro-auto">
                <option value="">Obra (todas)</option>
                @isset($obras)
                  @foreach($obras as $obraId)
                    <option value="{{ $obraId }}" {{ (string)request('obra_id') === (string)$obraId ? 'selected' : '' }}>
                      {{ $obraId }}
                    </option>
                  @endforeach
                @endisset
              </select>

            </div>
          </form>
        </div>

        {{-- TABLA --}}
        <div class="card" style="margin-top:12px;">
          <div class="card-header">
            <h3 class="card-title">Lista de arriendos</h3>
            <div class="semaforo-legend" aria-label="Convenciones de semaforizacion">
              <span><i class="legend-dot blue"></i>Activo</span>
              <span><i class="legend-dot green"></i>Al dia</span>
              <span><i class="legend-dot amber"></i>Pendiente</span>
              <span><i class="legend-dot red"></i>Mora</span>
            </div>
          </div>

          <div class="table-wrap-pro">
            <table class="table-pro">
              <colgroup>
                <col class="col-client">
                <col class="col-products">
                <col class="col-date-start">
                <col class="col-date-end">
                <col class="col-money">
                <col class="col-money-due">
                <col class="col-mora">
                <col class="col-semaforo">
                <col class="col-estado">
                <col class="col-actions">
              </colgroup>
              <thead>
                <tr>
                  <th class="col-client">Cliente</th>
                  <th class="col-products">Productos</th>
                  <th class="col-date-start">Inicio</th>
                  <th class="col-date-end">Fin</th>
                  <th class="td-right col-money">Total</th>
                  <th class="td-right col-money-due">Saldo pendiente</th>
                  <th class="col-mora">Mora</th>
                  <th class="col-status">Semáforo</th>
                  <th class="col-status">Estado</th>
                  <th class="col-actions">Acciones</th>
                </tr>
              </thead>

              <tbody>
                @forelse($arriendos as $a)

                  @php
                    $itemsCount = $a->items_count ?? (isset($a->items) ? $a->items->count() : null);
                    $unidades = isset($a->items) ? (int)$a->items->sum('cantidad_actual') : null;

                    // ==============================
                    // ✅ LOGICA SEMAFORIZACION POR FILA (NO MUESTRA TEXTO)
                    // AZUL: activo
                    // VERDE: cerrado/devuelto y saldo=0
                    // NARANJA: cerrado/devuelto y saldo>0 y dias<=7
                    // ROJO: cerrado/devuelto y saldo>0 y dias>=8 (hasta que pague)
                    // ==============================
                    $saldo = (float)($a->saldo ?? 0);

                    $estaActivo  = (strtolower($a->estado ?? '') === 'activo') && ((int)($a->cerrado ?? 0) === 0);
                    $estaCerrado = ((int)($a->cerrado ?? 0) === 1) || (strtolower($a->estado ?? '') === 'devuelto');

                    // Fecha desde que cerró (usa el mejor dato disponible; no rompe si falta)
                    $fechaCierreRaw = $a->fecha_devolucion_real ?? ($a->fecha_fin ?? null) ?? ($a->updated_at ?? null);
                    $fechaCierre = $fechaCierreRaw ? \Carbon\Carbon::parse($fechaCierreRaw)->startOfDay() : null;
                    $diasDesdeCierre = $fechaCierre ? $fechaCierre->diffInDays(\Carbon\Carbon::today()) : 0;

                    if ($estaActivo) {
                      $rowClass = 'tr-flag-blue';
                      $trafficClass = 'blue';
                      $trafficLabel = 'Activo';
                    } elseif ($estaCerrado && $saldo <= 0) {
                      $rowClass = 'tr-flag-green';
                      $trafficClass = 'green';
                      $trafficLabel = 'Al dia';
                    } elseif ($estaCerrado && $saldo > 0 && $diasDesdeCierre <= 7) {
                      $rowClass = 'tr-flag-amber';
                      $trafficClass = 'amber';
                      $trafficLabel = 'Pendiente';
                    } elseif ($estaCerrado && $saldo > 0) {
                      $rowClass = 'tr-flag-red';
                      $trafficClass = 'red';
                      $trafficLabel = 'Mora';
                    } else {
                      $rowClass = '';
                      $trafficClass = 'blue';
                      $trafficLabel = 'Revision';
                    }

                    $devAlquiler = (float)($a->dev_total_alquiler ?? 0);
                    $devMerma = (float)($a->dev_total_merma ?? 0);
                    $devPagado = (float)($a->dev_total_pagado ?? 0);
                    $devTransporte = (float)($a->dev_total_transporte ?? 0);

                    $itemsAlquiler = (float)(isset($a->items) ? $a->items->sum('total_alquiler') : 0);
                    $itemsMerma = (float)(isset($a->items) ? $a->items->sum('total_merma') : 0);
                    $itemsPagado = (float)(isset($a->items) ? $a->items->sum('total_pagado') : 0);

                    $baseAlquiler = $devAlquiler > 0 ? $devAlquiler : ($itemsAlquiler > 0 ? $itemsAlquiler : (float)($a->total_alquiler ?? 0));
                    $baseMerma = $devMerma > 0 ? $devMerma : ($itemsMerma > 0 ? $itemsMerma : (float)($a->total_merma ?? 0));
                    $basePagado = $devPagado > 0 ? $devPagado : ($itemsPagado > 0 ? $itemsPagado : (float)($a->total_pagado ?? 0));

                    $baseTransportePadre = (float)(isset($a->transportes) ? $a->transportes->sum('valor') : 0);
                    $baseTransporte = $baseTransportePadre + $devTransporte;
                    $baseIvaRate    = (float)($a->iva_rate ?? 0.19);
                    $baseSubtotal   = $baseAlquiler + $baseMerma + $baseTransporte;
                    $baseIvaValor   = (int)($a->iva_aplica ?? 0) === 1 ? ($baseSubtotal * $baseIvaRate) : 0;
                    $baseTotalFinal = $baseSubtotal + $baseIvaValor;
                    $baseSaldoFinal = max(0, $baseTotalFinal - $basePagado);

                    $fechaFinVistaRaw = $a->fecha_devolucion_real ?? $a->fecha_fin ?? null;
                    $fechaFinVista = $fechaFinVistaRaw
                      ? \Carbon\Carbon::parse($fechaFinVistaRaw)->format('d/m/Y')
                      : '—';
                    $fechaInicioVista = $a->fecha_inicio
                      ? $a->fecha_inicio->format('d/m/Y')
                      : '—';
                    $horaInicioVista = $a->fecha_inicio
                      ? $a->fecha_inicio->format('H:i')
                      : null;
                  @endphp

                  <tr class="{{ $rowClass }}">
                    <td class="col-client">
                      <span class="cell-main">{{ $a->cliente->nombre ?? '—' }}</span>

                      @if(!empty($a->obra_id ?? null))
                        <span class="cell-sub">Obra: {{ $a->obra_id }}</span>
                      @endif
                    </td>

                    <td class="col-products">
                      <span class="cell-main">{{ $itemsCount !== null ? $itemsCount : '—' }} prod.</span>
                      <span class="cell-sub">{{ $unidades !== null ? $unidades : '—' }} unds.</span>
                    </td>

                    <td class="col-date-start">
                      <span class="cell-main">{{ $fechaInicioVista }}</span>
                      @if($horaInicioVista)
                        <span class="cell-sub">{{ $horaInicioVista }}</span>
                      @endif
                    </td>
                    <td class="col-date-end">
                      <span class="cell-main">{{ $fechaFinVista }}</span>
                    </td>

                    <td class="td-right col-money"><span class="money-cell">${{ number_format((float)($a->precio_total ?? 0), 0) }}</span></td>
                    <td class="td-right col-money-due"><span class="money-cell">${{ number_format((float)($a->saldo ?? 0), 0) }}</span></td>

                    <td class="col-mora"><span class="cell-main">{{ (int)($a->dias_mora ?? 0) }}</span></td>

                    <td class="col-status">
                      <div class="status-cell">
                        <span class="traffic-pill {{ $trafficClass }}">{{ $trafficLabel }}</span>
                      </div>
                    </td>

                    <td class="col-status">
                      <div class="status-cell">
                        @if($a->estado === 'devuelto')
                          <span class="chip gray">Devuelto</span>
                        @else
                          <span class="chip blue">{{ ucfirst($a->estado) }}</span>
                        @endif
                      </div>
                    </td>

                    <td class="col-actions">
                      <div class="actions actions-cell">
                        <div class="dropdown" data-dd>
                          <button type="button" class="btn-kebab" aria-label="Acciones">⋯</button>

                          <div class="dropdown-menu">

                            <a class="menu-item item-return" href="{{ route('arriendos.ver', $a) }}">
                              <span class="menu-left"><span class="dot"></span>Ver / Gestionar</span>
                              <span>›</span>
                            </a>

                            <a class="menu-item item-edit" href="{{ route('arriendos.edit',$a) }}">
                              <span class="menu-left"><span class="dot"></span>Editar</span>
                              <span>›</span>
                            </a>

                            @if((int)($a->cerrado ?? 0) === 1 || $a->estado === 'devuelto')
                              <a class="menu-item item-details" href="{{ route('arriendos.detalles', $a) }}">
                                <span class="menu-left"><span class="dot"></span>Detalles</span>
                                <span>›</span>
                              </a>
                            @endif

                            @if((int)($a->cerrado ?? 0) === 0)
                              <button type="button"
                                      class="menu-item item-close"
                                      onclick="document.getElementById('modalCerrar{{ $a->id }}').style.display='flex'">
                                <span class="menu-left"><span class="dot"></span>Cerrar</span>
                                <span>›</span>
                              </button>
                            @endif

                            <form action="{{ route('arriendos.destroy',$a) }}" method="POST">
                              @csrf
                              @method('DELETE')

                              <button class="menu-item item-delete" onclick="return confirm('¿Eliminar arriendo?')">
                                <span class="menu-left"><span class="dot"></span>Borrar</span>
                                <span>›</span>
                              </button>
                            </form>

                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>

                  {{-- MODAL CERRAR --}}
                  @if((int)($a->cerrado ?? 0) === 0)
                    <div id="modalCerrar{{ $a->id }}" class="modal-backdrop" style="display:none;">
                      <div class="card modal-dialog">
                        <div class="card-header modal-header">
                          <h3 class="card-title">Cerrar arriendo #{{ $a->id }}</h3>
                          <button type="button"
                                  class="btn-ghost"
                                  onclick="document.getElementById('modalCerrar{{ $a->id }}').style.display='none'">
                            Cerrar
                          </button>
                        </div>

                        <form method="POST"
                              action="{{ route('arriendos.cerrar', $a) }}"
                              class="js-cerrar-form"
                              data-arriendo-id="{{ $a->id }}"
                              data-base-alquiler="{{ $baseAlquiler }}"
                              data-base-merma="{{ $baseMerma }}"
                              data-base-pagado="{{ $basePagado }}"
                              data-base-transporte="{{ $baseTransporte }}"
                              data-iva-rate="{{ $baseIvaRate }}">
                          @csrf

                          <div class="modal-grid">
                            <div class="modal-field">
                              <label class="small modal-label">Fecha devolución real</label>
                              <input class="input"
                                     type="date"
                                     name="fecha_devolucion_real"
                                     required
                                     value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="modal-field">
                              <label class="small modal-label">Pago recibido (opcional)</label>
                              <input class="input"
                                     type="number"
                                     min="0"
                                     step="0.01"
                                     name="pago"
                                     value="0">
                              <div style="margin-top:8px;">
                                <button type="button" class="btn-sm js-pagar-todo">Pagar saldo completo</button>
                              </div>
                            </div>
                          </div>

                          <div class="modal-grid">
                            <div class="modal-field">
                              <label class="small modal-label">Días de lluvia (se descuentan)</label>
                              <input class="input"
                                     type="number"
                                     min="0"
                                     name="dias_lluvia"
                                     value="0">
                            </div>

                            <div class="modal-field">
                              <label class="small modal-label">Costo daño/merma</label>
                              <input class="input"
                                     type="number"
                                     min="0"
                                     step="0.01"
                                     name="costo_merma"
                                     value="0">
                            </div>
                          </div>

                          <div class="modal-grid">
                            <div class="modal-field">
                              <label class="small modal-label">Factura con IVA</label>
                              <select class="input" name="iva_aplica">
                                <option value="0" {{ (int)($a->iva_aplica ?? 0) === 0 ? 'selected' : '' }}>Sin IVA</option>
                                <option value="1" {{ (int)($a->iva_aplica ?? 0) === 1 ? 'selected' : '' }}>Con IVA (19%)</option>
                              </select>
                              <div class="small modal-help" style="margin-top:6px;">
                                El IVA se calcula sobre (alquiler + merma + transportes).
                              </div>
                            </div>
                            <div class="modal-field"></div>
                          </div>

                          <div class="close-summary">
                            <div class="close-summary-grid">
                              <div class="sum-box">
                                <span class="sum-k">Alquiler generado</span>
                                <span class="sum-v js-sum-alquiler">${{ number_format($baseAlquiler, 2) }}</span>
                              </div>
                              <div class="sum-box">
                                <span class="sum-k">Merma total</span>
                                <span class="sum-v js-sum-merma">${{ number_format($baseMerma, 2) }}</span>
                              </div>
                              <div class="sum-box">
                                <span class="sum-k">Transportes</span>
                                <span class="sum-v js-sum-transporte">${{ number_format($baseTransporte, 2) }}</span>
                              </div>
                              <div class="sum-box">
                                <span class="sum-k">IVA</span>
                                <span class="sum-v js-sum-iva">${{ number_format($baseIvaValor, 2) }}</span>
                              </div>
                              <div class="sum-box">
                                <span class="sum-k">Total generado</span>
                                <span class="sum-v js-sum-total">${{ number_format($baseTotalFinal, 2) }}</span>
                              </div>
                              <div class="sum-box">
                                <span class="sum-k">Total pagado</span>
                                <span class="sum-v js-sum-pagado">${{ number_format($basePagado, 2) }}</span>
                              </div>
                              <div class="sum-box">
                                <span class="sum-k">Saldo final</span>
                                <span class="sum-v js-sum-saldo {{ $baseSaldoFinal > 0 ? 'sum-v-danger' : 'sum-v-ok' }}">${{ number_format($baseSaldoFinal, 2) }}</span>
                              </div>
                              <div class="sum-box">
                                <span class="sum-k">Estado de cierre</span>
                                <span class="sum-v js-sum-estado {{ $baseSaldoFinal > 0 ? 'sum-v-danger' : 'sum-v-ok' }}">
                                  {{ $baseSaldoFinal > 0 ? 'Queda saldo pendiente' : 'Cierra sin deuda' }}
                                </span>
                              </div>
                            </div>
                          </div>

                          <div class="modal-field">
                            <label class="small modal-label">Descripción (opcional)</label>
                            <input class="input"
                                   type="text"
                                   name="descripcion_incidencia"
                                   placeholder="Ej: lluvia fuerte / mango roto">
                          </div>

                          <div class="small modal-help">
                            Domingos se descuentan automáticamente. Si queda saldo pendiente al cerrar, se activa semáforo (AMARILLO 0–9 / ROJO 10+).
                          </div>

                          <div class="modal-actions">
                            <button type="button"
                                    class="btn-ghost"
                                    onclick="document.getElementById('modalCerrar{{ $a->id }}').style.display='none'">
                              Cancelar
                            </button>

                            <button type="submit" class="btn-primary" style="padding:8px 12px;">
                              Cerrar y calcular
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>
                  @endif

                @empty
                  <tr>
                    <td colspan="10">No hay arriendos todavía.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div style="margin-top:12px;">
            {{ $arriendos->links() }}
          </div>
        </div>

        {{-- JS: filtros + dropdown (FIX parpadeo incluido) --}}
        <script>
          (function () {
            const form = document.getElementById('filtrosForm');

            function submitFormLimpio() {
              Array.from(form.elements).forEach(el => {
                if (!el.name) return;
                el.disabled = (el.value === '' || el.value === null);
              });
              form.submit();
            }

            document.querySelectorAll('.filtro-auto').forEach(el => {
              el.addEventListener('change', submitFormLimpio);
            });

            function closeAll() {
              document.querySelectorAll('body > .dropdown-menu.is-floating').forEach(menu => menu.remove());
              document.querySelectorAll('[data-dd].open').forEach(dd => {
                dd.classList.remove('open');
                const menu = dd.querySelector('.dropdown-menu');
                if (menu) {
                  menu.style.removeProperty('display');
                  menu.style.removeProperty('visibility');
                }
                const tr = dd.closest('tr');
                if (tr) tr.classList.remove('row-open');
              });
            }

            function positionDropdown(dd, btn) {
              const sourceMenu = dd.querySelector('.dropdown-menu');
              if (!sourceMenu) return;

              const menu = sourceMenu.cloneNode(true);
              menu.classList.add('is-floating');
              menu.style.visibility = 'hidden';
              menu.style.display = 'block';
              document.body.appendChild(menu);

              const btnRect = btn.getBoundingClientRect();
              const menuRect = menu.getBoundingClientRect();
              const gap = 8;
              const padding = 10;

              let left = btnRect.right - menuRect.width;
              let top = btnRect.bottom + gap;

              left = Math.max(padding, Math.min(left, window.innerWidth - menuRect.width - padding));

              if (top + menuRect.height > window.innerHeight - padding) {
                top = btnRect.top - menuRect.height - gap;
              }
              top = Math.max(padding, top);

              menu.style.setProperty('--dd-left', `${left}px`);
              menu.style.setProperty('--dd-top', `${top}px`);
              menu.style.visibility = '';
            }

            document.addEventListener('click', function (e) {
              const btn = e.target.closest('.btn-kebab');
              const dd  = e.target.closest('[data-dd]');

              if (btn && dd) {
                e.preventDefault();
                const wasOpen = dd.classList.contains('open');
                closeAll();
                if (!wasOpen) {
                  dd.classList.add('open');
                  positionDropdown(dd, btn);
                  const tr = dd.closest('tr');
                  if (tr) tr.classList.add('row-open');
                }
                return;
              }

              if (e.target.closest('.dropdown-menu')) return;
              closeAll();
            });

            document.addEventListener('keydown', function (e) {
              if (e.key === 'Escape') closeAll();
            });
            window.addEventListener('resize', closeAll);
            window.addEventListener('scroll', closeAll, true);

            function parseNum(v) {
              const n = Number(v);
              return Number.isFinite(n) ? n : 0;
            }

            function money(v) {
              return '$' + parseNum(v).toLocaleString('es-CO', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              });
            }

            function recalcCerrarForm(form) {
              const baseAlquiler = parseNum(form.dataset.baseAlquiler);
              const baseMerma = parseNum(form.dataset.baseMerma);
              const basePagado = parseNum(form.dataset.basePagado);
              const baseTransporte = parseNum(form.dataset.baseTransporte);
              const ivaRate = parseNum(form.dataset.ivaRate || 0.19);

              const pagoInput = form.querySelector('[name="pago"]');
              const mermaInput = form.querySelector('[name="costo_merma"]');
              const ivaInput = form.querySelector('[name="iva_aplica"]');

              const extraMerma = Math.max(0, parseNum(mermaInput?.value));
              const pagoCierre = Math.max(0, parseNum(pagoInput?.value));
              const ivaAplica = String(ivaInput?.value || '0') === '1';

              const totalMerma = baseMerma + extraMerma;
              const subtotal = baseAlquiler + totalMerma + baseTransporte;
              const ivaValor = ivaAplica ? (subtotal * ivaRate) : 0;
              const totalGenerado = subtotal + ivaValor;
              const totalPagado = basePagado + pagoCierre;
              const saldo = Math.max(0, totalGenerado - totalPagado);

              const $alq = form.querySelector('.js-sum-alquiler');
              const $mer = form.querySelector('.js-sum-merma');
              const $trn = form.querySelector('.js-sum-transporte');
              const $iva = form.querySelector('.js-sum-iva');
              const $tot = form.querySelector('.js-sum-total');
              const $pag = form.querySelector('.js-sum-pagado');
              const $sal = form.querySelector('.js-sum-saldo');
              const $est = form.querySelector('.js-sum-estado');

              if ($alq) $alq.textContent = money(baseAlquiler);
              if ($mer) $mer.textContent = money(totalMerma);
              if ($trn) $trn.textContent = money(baseTransporte);
              if ($iva) $iva.textContent = money(ivaValor);
              if ($tot) $tot.textContent = money(totalGenerado);
              if ($pag) $pag.textContent = money(totalPagado);
              if ($sal) {
                $sal.textContent = money(saldo);
                $sal.classList.toggle('sum-v-danger', saldo > 0);
                $sal.classList.toggle('sum-v-ok', saldo <= 0);
              }
              if ($est) {
                $est.textContent = saldo > 0 ? 'Queda saldo pendiente' : 'Cierra sin deuda';
                $est.classList.toggle('sum-v-danger', saldo > 0);
                $est.classList.toggle('sum-v-ok', saldo <= 0);
              }
            }

            document.querySelectorAll('.js-cerrar-form').forEach(form => {
              const pagoInput = form.querySelector('[name="pago"]');
              const mermaInput = form.querySelector('[name="costo_merma"]');
              const ivaInput = form.querySelector('[name="iva_aplica"]');
              const btnPagarTodo = form.querySelector('.js-pagar-todo');

              [pagoInput, mermaInput, ivaInput].forEach(el => {
                if (!el) return;
                el.addEventListener('input', () => recalcCerrarForm(form));
                el.addEventListener('change', () => recalcCerrarForm(form));
              });

              if (btnPagarTodo) {
                btnPagarTodo.addEventListener('click', function () {
                  const baseAlquiler = parseNum(form.dataset.baseAlquiler);
                  const baseMerma = parseNum(form.dataset.baseMerma);
                  const basePagado = parseNum(form.dataset.basePagado);
                  const baseTransporte = parseNum(form.dataset.baseTransporte);
                  const ivaRate = parseNum(form.dataset.ivaRate || 0.19);

                  const extraMerma = Math.max(0, parseNum(mermaInput?.value));
                  const ivaAplica = String(ivaInput?.value || '0') === '1';
                  const subtotal = baseAlquiler + (baseMerma + extraMerma) + baseTransporte;
                  const ivaValor = ivaAplica ? (subtotal * ivaRate) : 0;
                  const totalGenerado = subtotal + ivaValor;
                  const saldoActual = Math.max(0, totalGenerado - basePagado);

                  if (pagoInput) pagoInput.value = saldoActual.toFixed(2);
                  recalcCerrarForm(form);
                });
              }

              recalcCerrarForm(form);
            });
          })();
        </script>

        {{-- Recaudado hoy en tiempo real --}}
        <script>
          (function () {
            const el = document.getElementById('recaudoHoyValue');
            if (!el) return;

            async function refresh() {
              try {
                const url = "{{ \Illuminate\Support\Facades\Route::has('api.recaudado_hoy') ? route('api.recaudado_hoy') : '' }}";
                if (!url) return;

                const res = await fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } });
                const data = await res.json();
                const valor = Number(data.total || 0);

                el.textContent = '$' + valor.toLocaleString('es-CO');
              } catch (e) {
                console.error('No se pudo actualizar recaudado hoy', e);
              }
            }

            setInterval(refresh, 10000);
          })();
        </script>

      </div>
    </div>

  </div>

@endsection
