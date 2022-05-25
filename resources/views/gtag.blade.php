@if (config('app.gtag_id') != '')
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('app.gtag_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', '{{ config('app.gtag_id') }}');
    </script>
@endif
