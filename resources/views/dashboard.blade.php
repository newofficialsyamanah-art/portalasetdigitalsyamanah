@extends('layouts.app')

@section('content')
    <!-- Tab 1: Spreadsheet Master Ledger 1:1 -->
    @include('tabs.spreadsheet')

    <!-- Tab 2: Laptop & PC Workstation -->
    @include('tabs.inventory')

    <!-- Tab 3: Smartphone & HP Sales -->
    @include('tabs.phones')

    <!-- Tab 4: Generator & Hotspot MikroTik -->
    @include('tabs.mikrotik')

    <!-- Tab 5: Audit & Analitik Kredensial -->
    @include('tabs.audit')

    <!-- Tab 6: Form Input Laptop & PC Workstation -->
    @include('tabs.form-pc')

    <!-- Tab 7: Form Input HP & Akun Sales -->
    @include('tabs.form-akun')

    <!-- Tab 8: Pusat Kontrol Superadmin (Master CRUD) -->
    @include('tabs.superadmin')
@endsection

