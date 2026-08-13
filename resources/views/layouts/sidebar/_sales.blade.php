{{-- ================= SALES & CRM ================= --}}
<li class="sidebar-section-title">SALES & CRM</li>

@include('layouts.sidebar.components.item', [
    'route' => 'crm.dashboard.sales',
    'url'   => route('crm.dashboard.sales'),
    'icon'  => 'fa-chart-line',
    'label' => 'Dashboard Sales'
])

@include('layouts.sidebar.components.item', [
    'route' => 'crm.leads.*',
    'url'   => route('crm.leads.index'),
    'icon'  => 'fa-user-plus',
    'label' => 'Leads'
])

@include('layouts.sidebar.components.item', [
    'route' => 'crm.followup.*',
    'url'   => route('crm.followup.index'),
    'icon'  => 'fa-phone',
    'label' => 'Follow Up'
])

@include('layouts.sidebar.components.item', [
    'route' => 'crm.pipeline.*',
    'url'   => route('crm.pipeline.index'),
    'icon'  => 'fa-chart-pie',
    'label' => 'Pipeline'
])

@include('layouts.sidebar.components.item', [
    'route' => 'crm.closing.*',
    'url'   => route('crm.closing.index'),
    'icon'  => 'fa-check-circle',
    'label' => 'Closing'
])
