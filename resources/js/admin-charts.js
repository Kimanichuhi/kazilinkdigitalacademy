import ApexCharts from 'apexcharts';

// Split out of app.js (863KB minified) since only the admin analytics page
// actually uses charts — every other page (public site + rest of admin)
// was paying for this bundle on every load for nothing.
window.ApexCharts = ApexCharts;
