import './bootstrap';

// Alpine is intentionally not started here — Livewire 3 bundles and starts
// its own Alpine instance via @livewireScripts, which every layout in this
// app includes so Alpine directives (x-data etc.) work consistently on
// every page, Livewire-powered or not.

// ApexCharts lives in its own entry (resources/js/admin-charts.js), loaded
// only by the admin analytics page — see that file for why.
